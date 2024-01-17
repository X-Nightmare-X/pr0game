<?php

/**
 *  2Moons / Steemnova
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package Steemnova
 * @author Adam Jordanek <dotevo@gmail.com>
 * @licence MIT
 */

class MissionCaseTrade extends MissionFunctions implements Mission
{
    public function __construct($fleet)
    {
        $this->_fleet	= $fleet;
    }

    public function TargetEvent()
    {
        if ($this->_fleet['fleet_end_id'] != 0) {
            /*
            * Buyer fleet flies to its own market place first
            * then gets rerouted to sellers marketplace
            */
            $db = Database::get();
            $sql = "SELECT `marketplace_system`, `marketplace_galaxy` FROM %%TRADES%% WHERE `seller_fleet_id` = :fleet_id OR `buyer_fleet_id` = :fleet_id;";
            $target = $db->selectSingle($sql, [
                ':fleet_id' => $this->_fleet['fleet_id']
            ]);
            //$this->UpdateFleet('fleet_mission', 3);
            $sql = "SELECT * FROM %%USERS%% WHERE id = :userId;";
            $fleet_owner = Database::get()->selectSingle($sql, [':userId' => $this->_fleet['fleet_owner']]);
            $fleetArray = FleetFunctions::unserialize($this->_fleet['fleet_array']);
            $SpeedFactor = FleetFunctions::getGameSpeedFactor();
            $Distance = FleetFunctions::getTargetDistance(
                [$this->_fleet['fleet_start_galaxy'], $this->_fleet['fleet_start_system'], (Config::get($fleet_owner['universe'])->max_planets + 2)],
                [$target['marketplace_galaxy'], $target['marketplace_system'], (Config::get($fleet_owner['universe'])->max_planets + 2)]
            );
            $SpeedAllMin = FleetFunctions::getFleetMaxSpeed($fleetArray, $fleet_owner);
            $Duration = FleetFunctions::getMissionDuration(10, $SpeedAllMin, $Distance, $SpeedFactor, $fleet_owner);
            //if trade is with another system, set start time to arrival time on own market place to remove the extra time back
            if ($target['marketplace_system'] != $this->_fleet['fleet_start_system'] || $target['marketplace_galaxy'] != $this->_fleet['fleet_start_galaxy']) {
                $this->UpdateFleet('start_time', $this->_fleet['fleet_start_time']);
            }
            $fleetStartTime = $Duration + $this->_fleet['fleet_start_time'];
            $fleetStayTime = $fleetStartTime + TIME_12_HOURS;
            $fleetEndTime = $fleetStayTime + $Duration;
            $this->UpdateFleet('fleet_start_time', $fleetStartTime);
            $this->UpdateFleet('fleet_end_stay', $fleetStayTime);
            $this->UpdateFleet('fleet_end_time', $fleetEndTime);
            $this->UpdateFleet('fleet_end_id', 0);
            $this->UpdateFleet('fleet_end_galaxy', $target['marketplace_galaxy']);
            $this->UpdateFleet('fleet_end_system', $target['marketplace_system']);
            $this->UpdateFleet('fleet_resource_metal', 0);
            $this->UpdateFleet('fleet_resource_crystal', 0);
            $this->UpdateFleet('fleet_resource_deuterium', 0);
        } else if ($this->_fleet['fleet_end_id'] == 0) {
            /*
            * either exchange ressources or hold at market place
            * else is for holding(normal flight to marketplace)
            */
            $db = Database::get();
            $sql = "SELECT `seller_fleet_id`, `ex_resource_type`, `ex_resource_amount`, `buy_time` FROM %%TRADES%% WHERE `seller_fleet_id` = :fleet_id;";
            $tradeSeller = $db->selectSingle($sql, [
                ':fleet_id' => $this->_fleet['fleet_id']
            ]);
            $sql = "SELECT `buyer_fleet_id`, `resource_type`, `resource_amount`, `buy_time` FROM %%TRADES%% WHERE `buyer_fleet_id` = :fleet_id;";
            $tradeBuyer = $db->selectSingle($sql, [
                ':fleet_id' => $this->_fleet['fleet_id']
            ]);
            if (!empty($tradeSeller)) {
                if (!empty($tradeSeller['buy_time'])) {
                    switch ($tradeSeller['ex_resource_type']) {
                        case 1:
                            $ressource = 'fleet_resource_metal';
                            break;
                        case 2:
                            $ressource = 'fleet_resource_crystal';
                            break;
                        case 3:
                            $ressource = 'fleet_resource_deuterium';
                            break;
                    }
                    $this->UpdateFleet($ressource, $tradeSeller['ex_resource_amount']);
                    $this->setState(FLEET_HOLD);
                    $this->UpdateFleet('fleet_no_m_return', 0);
                } else {
                    $this->setState(FLEET_HOLD);
                    $this->UpdateFleet('fleet_no_m_return', 1);
                }
            } else if (!empty($tradeBuyer)) {
                if (!empty($tradeBuyer['buy_time'])) {
                    switch ($tradeBuyer['resource_type']) {
                        case 1:
                            $ressource = 'fleet_resource_metal';
                            break;
                        case 2:
                            $ressource = 'fleet_resource_crystal';
                            break;
                        case 3:
                            $ressource = 'fleet_resource_deuterium';
                            break;
                    }
                    $this->UpdateFleet($ressource, $tradeBuyer['resource_amount']);
                    $this->setState(FLEET_HOLD);
                    $this->UpdateFleet('fleet_no_m_return', 0);
                } else {
                    $this->setState(FLEET_HOLD);
                    $this->UpdateFleet('fleet_no_m_return', 1);
                }
            }
        } else {
            $this->setState(FLEET_HOLD);
            $this->UpdateFleet('fleet_no_m_return', 1);
        }
        
        $this->SaveFleet();
    }

    public function EndStayEvent()
    {
        $this->setState(FLEET_RETURN);
        $this->SaveFleet();
    }

    public function ReturnEvent()
    {
        $LNG		= $this->getLanguage(null, $this->_fleet['fleet_owner']);
        $db = Database::get();
        $sql		= 'SELECT name FROM %%PLANETS%% WHERE id = :planetId;';
        $planetName	= $db->selectSingle($sql, [
            ':planetId'	=> $this->_fleet['fleet_start_id'],
        ], 'name');
        $sql = "SELECT `buyer_fleet_id` FROM %%TRADES%% WHERE `seller_fleet_id` = :fleet_id;";
        $sellerBuy	= $db->selectSingle($sql, [
            ':fleet_id'	=> $this->_fleet['fleet_id'],
        ], 'buyer_fleet_id');
        $sql = "SELECT `buyer_fleet_id` FROM %%TRADES%% WHERE `buyer_fleet_id` = :fleet_id;";
        $buyerBuy	= $db->selectSingle($sql, [
            ':fleet_id'	=> $this->_fleet['fleet_id'],
        ], 'buyer_fleet_id');

        if ($sellerBuy || $buyerBuy) {
            $Message	= sprintf($LNG['sys_trade_mess_back_success'], $this->_fleet['fleet_resource_metal'], $this->_fleet['fleet_resource_crystal'], 
            $this->_fleet['fleet_resource_deuterium'], $planetName, GetStartAddressLink($this->_fleet, ''));
        } else {
            $Message	= sprintf($LNG['sys_trade_mess_back'], $planetName, GetStartAddressLink($this->_fleet, ''));
        }

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_tower'],
            4,
            $LNG['sys_mess_fleetback'],
            $Message,
            $this->_fleet['fleet_end_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->RestoreFleet();
    }
}
