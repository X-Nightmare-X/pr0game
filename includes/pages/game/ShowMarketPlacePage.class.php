<?php

/**
 *  Steemnova
 *   by Adam "dotevo" Jordanek 2018
 *
 * For the full copyright and license information, please view the LICENSE
 *
 */

class ShowMarketPlacePage extends AbstractGamePage
{
    public static $requireModule = MODULE_MISSION_TRADE;

    public function __construct()
    {
        parent::__construct();
    }

    private function checkSlots($USER)
    {
        $LNG =& Singleton()->LNG;
        $ActualFleets = FleetFunctions::getCurrentFleets($USER['id']);
        if (FleetFunctions::getMaxFleetSlots($USER) <= $ActualFleets) {
            return ['result' => -1, 'message' => $LNG['fl_no_slots']];
        }
        return ['result' => 0];
    }

    private function checkTechs($SELLER)
    {
        $USER =& Singleton()->USER;
        $resource =& Singleton()->resource;
        $LNG =& Singleton()->LNG;
        $attack = $USER[$resource[109]] * 10;
        $shield = $USER[$resource[110]] * 10;
        $defensive = $USER[$resource[111]] * 10;

        $attack_targ = $SELLER[$resource[109]] * 10;
        $shield_targ = $SELLER[$resource[110]] * 10;
        $defensive_targ = $SELLER[$resource[111]] * 10;

        if ($attack > $attack_targ || $defensive > $defensive_targ || $shield > $shield_targ) {
            return [
                'buyable' => false,
                'reason' => $LNG['market_buyable_no_tech'],
            ];
        }

        return [
            'buyable' => true,
            'reason' => '',
        ];
    }
    private function checkVmode()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        if ($USER['urlaubs_modus'] == 1) {
            return [
                'buyable' => false,
                'reason' => $LNG['fa_vacation_mode_current'],
            ];
        }
        return [
            'buyable' => true,
            'reason' => '',
        ];
    }
    private function checkDiplo($visibility, $level, $seller_ally, $ally, $receiverID)
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        $blocked = PlayerUtil::get_block_status($USER['id'], $receiverID);
        $blockedInverse = PlayerUtil::get_block_status($receiverID, $USER['id']);
        if (($visibility == 2 && $level == 5) || $blocked['block_trade'] || $blockedInverse['block_trade']) {
            return [
                'buyable' => false,
                'reason' => $LNG['market_buyable_no_enemies'],
            ];
        }

        if ($visibility == 1 && $ally != $seller_ally && ($level == null || $level > 3)) {
            return [
                'buyable' => false,
                'reason' => $LNG['market_buyable_only_trade_partners'],
            ];
        }

        return [
            'buyable' => true,
            'reason' => '',
        ];
    }

    private function getResourceTradeHistory()
    {
        $db = Database::get();
        $sql = 'SELECT
			buy_time as time,
			ex_resource_type as res_type,
			ex_resource_amount as amount,
			seller.fleet_resource_metal as metal,
			seller.fleet_resource_crystal as crystal,
			seller.fleet_resource_deuterium as deuterium
			FROM %%TRADES%%
			JOIN %%LOG_FLEETS%% seller ON seller.fleet_id = seller_fleet_id
			JOIN %%LOG_FLEETS%% buyer ON buyer.fleet_id = buyer_fleet_id
			WHERE transaction_type = 0 AND seller.fleet_universe = :universe ORDER BY time DESC LIMIT 40;';
        $trades = $db->select($sql, [
            ':universe' => Universe::current(),
        ]);
        return $trades;
    }

    private function getFleetTradeHistory()
    {
        $LNG =& Singleton()->LNG;
        $db = Database::get();
        $sql = 'SELECT
			seller.fleet_array as fleet,
			buy_time as time,
			ex_resource_type as res_type,
			ex_resource_amount as amount
			FROM %%TRADES%%
			JOIN %%LOG_FLEETS%% seller ON seller.fleet_id = seller_fleet_id
			JOIN %%LOG_FLEETS%% buyer ON buyer.fleet_id = buyer_fleet_id
			WHERE transaction_type = 1 AND seller.fleet_universe = :universe ORDER BY time DESC LIMIT 40;';

        $trades = $db->select($sql, [
            ':universe' => Universe::current(),
        ]);
        for ($i = 0; $i < count($trades); $i++) {
            $fleet =  FleetFunctions::unserialize($trades[$i]['fleet']);
            $fleet_str = '';
            foreach ($fleet as $name => $amount) {
                $fleet_str .= $LNG['shortNames'][$name] . ' x' . $amount . "\n";
            }
            $trades[$i]['fleet_str'] = $fleet_str;
        }
        return $trades;
    }

    private function calculateFleetSize($fleetResult, $shipType)
    {
        $pricelist =& Singleton()->pricelist;
        $PLANET =& Singleton()->PLANET;
        $resource =& Singleton()->resource;
        // sorry world.
        $amount = $fleetResult['ex_resource_amount'];

        $F1capacity = 0;
        $F1type = 0;
        if ($shipType == 1) {
            // PRIO for LC
            $F1capacity = $pricelist[202]['capacity'];
            $F1type = 202;
        } else {
            // PRIO for HC
            $F1capacity = $pricelist[203]['capacity'];
            $F1type = 203;
        }

        $F1 = min($PLANET[$resource[$F1type]], ceil($amount / $F1capacity));

        //taken
        $amountTMP = $amount - $F1 * $F1capacity;
        // If still fleet needed
        $F2 = 0;
        $F2capacity = 0;
        $F2type = 0;
        if ($amountTMP > 0) {
            if ($shipType == 1) {
                // We need HC
                $F2capacity = $pricelist[203]['capacity'];
                $F2type = 203;
            } else {
                // We need LC
                $F2capacity = $pricelist[202]['capacity'];
                $F2type = 202;
            }
            $F2 = min($PLANET[$resource[$F2type]], ceil($amountTMP / $F2capacity));
            $amountTMP -= $F2 * $F2capacity;
        }

        if ($amountTMP > 0) {
            return false;
        }

        return [$F1type => $F1, $F2type => $F2];
    }

    private function doBuy()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $resource =& Singleton()->resource;
        $LNG =& Singleton()->LNG;
        $FleetID = HTTP::_GP('fleetID', 0);
        $shipType = HTTP::_GP('shipType', "");
        $db = Database::get();
        //Get trade fleet
        $sql = "SELECT * FROM %%FLEETS%% JOIN %%TRADES%% ON fleet_id = seller_fleet_id JOIN %%USERS%%"
            . " ON fleet_owner = id WHERE fleet_id = :fleet_id AND fleet_mess = 2;";
        $fleetResult = $db->selectSingle($sql, [':fleet_id' => $FleetID]);
        $isFleetTrade = ($fleetResult['transaction_type'] == 1);

        //Vmode check
        $vmode = $this->checkVmode();
        if (!$vmode['buyable']) {
            return $vmode['reason'];
        }

        //Slots checking
        $checkResult = $this->checkSlots($USER);
        if ($checkResult['result'] < 0) {
            return $checkResult['message'];
        }
        //Get trade fleet
        $sql = "SELECT * FROM %%FLEETS%% JOIN %%TRADES%% ON fleet_id = seller_fleet_id JOIN %%USERS%%"
            . " ON fleet_owner = id WHERE fleet_id = :fleet_id AND fleet_mess = 2;";
        $fleetResult = $db->selectSingle($sql, [':fleet_id' => $FleetID]);

        //Error: no results
        if ($db->rowCount() == 0) {
            return $LNG['market_p_msg_not_found'];
        }

        if ($fleetResult['filter_visibility'] != 0 && $USER['id'] != $fleetResult['id']) {
            //Check packts
            $sql = "SELECT * FROM %%DIPLO%% WHERE (owner_1 = :ow AND owner_2 = :ow2) OR"
                . " (owner_2 = :ow AND owner_1 = :ow2) AND accept = 1;";
            $res = $db->select($sql, [
                ':ow' => $USER['ally_id'],
                ':ow2' => $fleetResult['ally_id'],
            ]);
            $level = null;
            if ($db->rowCount() != 0) {
                $level = $res[0]['level'];
            }
            $buy = $this->checkDiplo(
                $fleetResult['filter_visibility'],
                $level,
                $fleetResult['ally_id'],
                $USER['ally_id'],
                $fleetResult['fleet_owner'],
            );
            if (!$buy['buyable']) {
                return $buy['reason'];
            }
        }

        $isPush = $this->checkPush($fleetResult, $USER['id'], $fleetResult['fleet_owner']);
        if ($isPush) {
            return $LNG['market_buyable_no_tech'];
        }

        //if not in range 1-3
        if ($fleetResult['resource_amount'] > $fleetResult['ex_resource_amount'] || $isFleetTrade) {
            $amount['ex_resource_amount'] = $fleetResult['resource_amount'];
        } else {
            $amount['ex_resource_amount'] = $fleetResult['ex_resource_amount'];
        }
        $fleetNeeded = $this->calculateFleetSize($amount, $shipType);
        if (
            $fleetResult['ex_resource_type'] >= 4 ||
            $fleetResult['ex_resource_type'] <= 0
        ) {
            return $LNG['market_p_msg_wrong_resource_type'];
        }

        // not enough ships
        if (!$fleetNeeded) {
            return $LNG['market_p_msg_more_ships_is_needed'];
        }

        $fleetArray = array_filter($fleetNeeded);
        $amount = $fleetResult['ex_resource_amount'];
        $marketplace = (Config::get($USER['universe'])->max_planets + 2);
        
        $SpeedFactor = FleetFunctions::getGameSpeedFactor();
        if ($isFleetTrade) {
            $Distance = FleetFunctions::getTargetDistance(
                [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
                [$fleetResult['fleet_end_galaxy'], $fleetResult['fleet_end_system'], $fleetResult['fleet_end_planet']]
            );
        } else {
            $Distance = FleetFunctions::getTargetDistance(
                [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
                [$PLANET['galaxy'], $PLANET['system'], $marketplace]
            );
        }
        
        $SpeedAllMin = FleetFunctions::getFleetMaxSpeed($fleetArray, $USER);
        $Duration = FleetFunctions::getMissionDuration(10, $SpeedAllMin, $Distance, $SpeedFactor, $USER);
        $consumption = FleetFunctions::getFleetConsumption($fleetArray, $Duration, $Distance, $USER, $SpeedFactor);

        if ($isFleetTrade) {
            $fleet = FleetFunctions::unserialize($fleetResult['fleet_array']);
            $fleetSpeedAllMin = FleetFunctions::getFleetMaxSpeed($fleet, $USER);
            $fleetDuration = FleetFunctions::getMissionDuration(10, $fleetSpeedAllMin, $Distance, $SpeedFactor, $USER);
            $bonusConsumption = FleetFunctions::getFleetConsumption($fleet, $fleetDuration, $Distance, $USER, $SpeedFactor);
            $consumption += $bonusConsumption;
        }

        $fleetStartTime = $Duration + TIMESTAMP;
        $fleetStayTime = $fleetStartTime;
        $fleetEndTime = $fleetStayTime + $Duration;

        $met = 0;
        $cry = 0;
        $deu = 0;
        if ($fleetResult['ex_resource_type'] == 1) {
            $met = $amount;
        } elseif ($fleetResult['ex_resource_type'] == 2) {
            $cry = $amount;
        } elseif ($fleetResult['ex_resource_type'] == 3) {
            $deu = $amount;
        }

        $fleetResource = [
            901 => $met,
            902 => $cry,
            903 => $deu,
        ];

        if (
            $PLANET[$resource[901]] - $fleetResource[901] < 0 ||
            $PLANET[$resource[902]] - $fleetResource[902] < 0 ||
            $PLANET[$resource[903]] - $fleetResource[903] - $consumption < 0
        ) {
            return $LNG['market_p_msg_resources_error'];
        }

        $PLANET[$resource[901]] -= $fleetResource[901];
        $PLANET[$resource[902]] -= $fleetResource[902];
        $PLANET[$resource[903]] -= $fleetResource[903] + $consumption;
        $buyerMission = $isFleetTrade ? MISSION_TRANSPORT : MISSION_TRADE;
        $targetGalaxy = $isFleetTrade ? $fleetResult['fleet_start_galaxy'] : $PLANET['galaxy'];
        $targetSystem = $isFleetTrade ? $fleetResult['fleet_start_system'] : $PLANET['system'];
        $targetPlanet = $isFleetTrade ? $fleetResult['fleet_start_planet'] : $marketplace;
        $targetType = $isFleetTrade ? $fleetResult['fleet_start_type'] : 1;
        $buyerfleet = FleetFunctions::sendFleet(
            $fleetArray,
            $buyerMission /*buyerfleet to automatically get rerouted*/,
            $USER['id'],
            $PLANET['id'],
            $PLANET['galaxy'],
            $PLANET['system'],
            $PLANET['planet'],
            $PLANET['planet_type'],
            $fleetResult['fleet_owner'],
            $fleetResult['fleet_start_id'],
            $targetGalaxy,
            $targetSystem,
            $targetPlanet,
            $targetType,
            $fleetResource,
            $fleetStartTime,
            $fleetStayTime,
            $fleetEndTime,
            0,
            0,
            1,
            $consumption
        );



        /////////////////////////////////////////////////////////////////////////////
        /// SEND/
        $sql = "SELECT * FROM %%USERS%% WHERE id = :userId;";
        $USER_2 = Database::get()->selectSingle($sql, [':userId' => $fleetResult['fleet_owner']]);
        $fleetArray = FleetFunctions::unserialize($fleetResult['fleet_array']);
        $SpeedFactor = FleetFunctions::getGameSpeedFactor();
        $Distance = FleetFunctions::getTargetDistance(
            [$PLANET['galaxy'], $PLANET['system'], $marketplace],
            [$fleetResult['fleet_end_galaxy'], $fleetResult['fleet_end_system'], $marketplace]
        );
        $SpeedAllMin = FleetFunctions::getFleetMaxSpeed($fleetArray, $USER_2);
        $Duration = FleetFunctions::getMissionDuration(10, $SpeedAllMin, $Distance, $SpeedFactor, $USER_2);
        if ($PLANET['galaxy'] == $fleetResult['fleet_end_galaxy'] && $PLANET['system'] == $fleetResult['fleet_end_system'] && !$isFleetTrade) {
            $Distance = FleetFunctions::getTargetDistance(
                [$fleetResult['fleet_start_galaxy'], $fleetResult['fleet_start_system'], $marketplace],
                [$fleetResult['fleet_end_galaxy'], $fleetResult['fleet_end_system'], $marketplace]
            );
            $Duration = FleetFunctions::getMissionDuration(10, $SpeedAllMin, $Distance, $SpeedFactor, $USER_2);
            $startTime = $fleetResult['start_time'];
            $fleetStartTime = $fleetResult['fleet_start_time'];
            $fleetStayTime = $fleetStartTime + TIME_6_HOURS;
            $fleetEndTime = $fleetStayTime + $Duration;
        } else if (!$isFleetTrade) {
            $startTime = TIMESTAMP;
            $fleetStartTime = $Duration + TIMESTAMP;
            $fleetStayTime = $fleetStartTime + TIME_6_HOURS;
            $fleetEndTime = $fleetStayTime + $Duration;
        } else {
            $startTime = $fleetResult['start_time'];
            $fleetStartTime = $Duration + TIMESTAMP;
            $fleetStayTime = $fleetStartTime;
            $fleetEndTime = $fleetStayTime + $Duration;
        }
        $params = [
            ':fleetID' => $FleetID,
            ':fleet_target_owner' => $USER['id'],
            ':fleet_end_id' => $isFleetTrade ? $PLANET['id'] : 0,
            ':fleet_end_planet' => $isFleetTrade ? $PLANET['planet'] : $marketplace,
            ':fleet_end_system' => $PLANET['system'],
            ':fleet_end_galaxy' => $PLANET['galaxy'],
            ':start_time' => $startTime,
            ':fleet_start_time' => $fleetStartTime,
            ':fleet_end_stay' => $fleetStayTime,
            ':fleet_end_time' => $fleetEndTime,
            ':fleet_mission' => $fleetResult['transaction_type'] == 0 ? MISSION_TRADE : MISSION_TRANSFER,
            ':fleet_no_m_return' => 1,
            ':fleet_mess' => 0,
        ];
        $logParams = array_merge($params, [
            ':fleet_start_time_formated' => Database::formatDate($fleetStartTime),
            ':fleet_end_stay_formated' => Database::formatDate($fleetStayTime),
            ':fleet_end_time_formated' => Database::formatDate($fleetEndTime),
        ]);
        $params = array_merge($params, [
            ':metal' => 0,
            ':crystal' => 0,
            ':deuterium' => 0,
        ]);
        $sql = "UPDATE %%FLEETS%% SET `fleet_no_m_return` = :fleet_no_m_return, `fleet_end_id` = :fleet_end_id,"
            . " `fleet_target_owner` = :fleet_target_owner, `fleet_mess` = :fleet_mess,"
            . " `fleet_mission` = :fleet_mission, `fleet_end_stay` = :fleet_end_stay,"
            . " `fleet_end_time` = :fleet_end_time,`fleet_start_time` = :fleet_start_time, start_time = :start_time,"
            . " `fleet_end_planet` = :fleet_end_planet, `fleet_end_system` = :fleet_end_system,"
            . " `fleet_end_galaxy` = :fleet_end_galaxy, `fleet_resource_metal` = :metal,"
            . " `fleet_resource_crystal` = :crystal, `fleet_resource_deuterium` = :deuterium WHERE fleet_id = :fleetID;";
        $db->update($sql, $params);
        $sql = "UPDATE %%LOG_FLEETS%% SET `fleet_no_m_return` = :fleet_no_m_return, `fleet_end_id` = :fleet_end_id,"
            . " `fleet_target_owner` = :fleet_target_owner, `fleet_mess` = :fleet_mess,"
            . " `fleet_mission` = :fleet_mission, `fleet_end_stay` = :fleet_end_stay,"
            . " `fleet_end_time` = :fleet_end_time ,`fleet_start_time` = :fleet_start_time, start_time = :start_time,"
            . " `fleet_start_time_formated` = :fleet_start_time_formated ,`fleet_end_stay_formated` = :fleet_end_stay_formated,"
            . " `fleet_end_time_formated` = :fleet_end_time_formated ,"
            . " `fleet_end_planet` = :fleet_end_planet, `fleet_end_system` = :fleet_end_system,"
            . " `fleet_end_galaxy` = :fleet_end_galaxy WHERE fleet_id = :fleetID;";
        $db->update($sql, $logParams);
        $sql = 'UPDATE %%FLEETS_EVENT%% SET  `time` = :endTime WHERE fleetID	= :fleetId;';
        $db->update($sql, [
            ':fleetId'  => $FleetID,
            ':endTime'  => $fleetStartTime,
        ]);

        $sql = 'UPDATE %%TRADES%% SET  `buyer_fleet_id` = :buyerFleetId,`buy_time` = NOW()'
            . ' WHERE seller_fleet_id = :fleetId;';
        $db->update($sql, [
            ':fleetId'  => $FleetID,
            ':buyerFleetId' => $buyerfleet,
        ]);

        $LC = 0;
        $HC = 0;
        if (array_key_exists(202, $fleetArray)) {
            $LC = $fleetArray[202];
        }
        if (array_key_exists(203, $fleetArray)) {
            $HC = $fleetArray[203];
        }
        $sql="SELECT `marketplace_galaxy`, `marketplace_system` FROM %%TRADES%% WHERE seller_fleet_id = :fleetId;";
        $target = $db->selectSingle($sql, [
            ':fleetId' => $FleetID,
        ]);
        // To customer
        $Message = sprintf(
            $LNG['market_msg_trade_bought'],
            sprintf(
                '%s:%s:%s',
                $isFleetTrade ? $fleetResult['fleet_start_galaxy'] : $target['marketplace_galaxy'],
                $isFleetTrade ? $fleetResult['fleet_start_system'] : $target['marketplace_system'],
                $isFleetTrade ? $fleetResult['fleet_start_planet'] : $marketplace
            ),
            $fleetResource[901],
            $LNG['tech'][901],
            $fleetResource[902],
            $LNG['tech'][902],
            $fleetResource[903],
            $LNG['tech'][903],
            $consumption,
            $LNG['tech'][903]
        );
        PlayerUtil::sendMessage(
            $USER['id'],
            0,
            $LNG['market_msg_trade_from'],
            4,
            $LNG['market_msg_trade_topic'],
            $Message,
            TIMESTAMP,
            null,
            1,
            $fleetResult['fleet_universe']
        );

        // To salesmen
        $Message = sprintf(
            $LNG['market_msg_trade_sold'],
            $PLANET['galaxy'] . ":" . $PLANET['system'] . ":" . $isFleetTrade ? $marketplace : $PLANET['planet'],
            $fleetResult['fleet_resource_metal'],
            $LNG['tech'][901],
            $fleetResult['fleet_resource_crystal'],
            $LNG['tech'][902],
            $fleetResult['fleet_resource_deuterium'],
            $LNG['tech'][903]
        );
        PlayerUtil::sendMessage(
            $fleetResult['fleet_owner'],
            0,
            $LNG['market_msg_trade_from'],
            4,
            $LNG['market_msg_trade_topic'],
            $Message,
            TIMESTAMP,
            null,
            1,
            $fleetResult['fleet_universe']
        );
        return sprintf($LNG['market_p_msg_sent'], $LC, $HC);
    }

    /** automatic push prevention **/
    private function checkPush($fleetsRow, $buyer, $seller)
    {
        $pMarket = new MarketManager();
        $offer = 0;
        $fleetMarket = $fleetsRow['transaction_type'] == 1;
        $ask = $pMarket->convertExpResTypeToDeut($fleetsRow['ex_resource_type'], $fleetsRow['ex_resource_amount']);

        if ($fleetMarket) {
            $fleet = FleetFunctions::unserialize($fleetsRow['fleet_array']);
            return $pMarket->isFleetPush($fleet, $ask, $seller, $buyer);
        } else {
            $offerArray = [
                'metal' => $fleetsRow['fleet_resource_metal'],
                'crystal' => $fleetsRow['fleet_resource_crystal'],
                'deuterium' => $fleetsRow['fleet_resource_deuterium'],
            ];

            $offer = $pMarket->convertToDeut($offerArray);
            return $pMarket->isPush($offer, $ask, $seller, $buyer);
        }
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        require_once 'includes/classes/class.MarketManager.php';

        $GetAction = HTTP::_GP('action', "");

        $message = "";
        $db = Database::get();

        if ($GetAction == "buy") {
            $message = $this->doBuy();
        }

        $sql = 'SELECT *
			FROM %%FLEETS%%
			JOIN %%USERS%% ON fleet_owner = id
			JOIN %%TRADES%% ON fleet_id = seller_fleet_id
			LEFT JOIN (
				SELECT owner_2 as al ,level, accept  FROM %%DIPLO%% WHERE owner_1 = :al
				UNION
				SELECT owner_1 as al,level, accept  FROM %%DIPLO%% WHERE owner_2 = :al) as packts
			ON al = ally_id
			WHERE fleet_mission = :trade
			AND fleet_universe = :universe
			AND fleet_mess = :hold 
            and buyer_fleet_id IS NULL 
            ORDER BY fleet_end_time ASC;';
        $fleetResult = $db->select($sql, [
            ':al'       => $USER['ally_id'],
            ':trade'    => MISSION_TRADE,
            ':buyerid' => $USER['id'],
            ':universe' => Universe::current(),
            ':hold'     => FLEET_HOLD,
        ]);

        $FlyingFleetList = [];

        foreach ($fleetResult as $fleetsRow) {
            $resourceN = " ";
            switch ($fleetsRow['ex_resource_type']) {
                case 1:
                    $resourceN = $LNG['tech'][901];
                    break;
                case 2:
                    $resourceN = $LNG['tech'][902];
                    break;
                case 3:
                    $resourceN = $LNG['tech'][903];
                    break;
                default:
                    break;
            }

            //Level of diplo
            if ($fleetsRow['accept'] == 0) {
                $fleetsRow['level'] = null;
            }
            $marketplace = (Config::get($USER['universe'])->max_planets + 2);
            $SpeedFactor = FleetFunctions::getGameSpeedFactor();
            //FROM
            $FROM_fleet =  FleetFunctions::unserialize($fleetsRow['fleet_array']);
            $FROM_Distance = FleetFunctions::getTargetDistance(
                [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
                [$fleetsRow['fleet_end_galaxy'], $fleetsRow['fleet_end_system'], $marketplace]
            );
            $FROM_SpeedAllMin = FleetFunctions::getFleetMaxSpeed($FROM_fleet, $fleetsRow);
            $FROM_Duration = FleetFunctions::getMissionDuration(
                10,
                $FROM_SpeedAllMin,
                $FROM_Distance,
                $SpeedFactor,
                $fleetsRow
            );
            $sql="SELECT `marketplace_galaxy`, `marketplace_system` FROM %%TRADES%% WHERE seller_fleet_id = :fleetId;";
            $target = $db->selectSingle($sql, [
                ':fleetId' => $fleetsRow['fleet_id'],
            ]);
            //TO
            $TO_Distance = FleetFunctions::getTargetDistance(
                [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
                [$target['marketplace_galaxy'], $target['marketplace_system'], $marketplace]
            );
            $TO_LC_SPEED = FleetFunctions::getFleetMaxSpeed([202 => 1], $USER);
            $TO_LC_DUR = FleetFunctions::getMissionDuration(10, $TO_LC_SPEED, $TO_Distance, $SpeedFactor, $USER);
            $TO_HC_SPEED = FleetFunctions::getFleetMaxSpeed([203 => 1], $USER);
            $TO_HC_DUR = FleetFunctions::getMissionDuration(10, $TO_HC_SPEED, $TO_Distance, $SpeedFactor, $USER);

            $fleet_str = '';
            foreach ($FROM_fleet as $name => $amount) {
                $fleet_str .= $LNG['shortNames'][$name] . ' x' . $amount . "\n";
            }

            $buyer = $USER['id'];
            $seller = $fleetsRow['fleet_owner'];

            if ($buyer == $seller) {
                $buy = [
                    'buyable' => false,
                    'reason' => 'N/A', // todo $LNG['text'] - creative input needed
                ];
            } elseif ($this->checkPush($fleetsRow, $buyer, $seller)) {
                $buy = [
                    'buyable' => false,
                    'reason' => $LNG['market_buyable_no_tech'],
                ];
            } else {
                //Level 5 - enemies
                //Level 0 - 3 alliance
                $buy = $this->checkDiplo(
                    $fleetsRow['filter_visibility'],
                    $fleetsRow['level'],
                    $fleetsRow['ally_id'],
                    $USER['ally_id'],
                    $seller,
                );
            }

            $pMarket = new MarketManager();
            $fleetValue = $pMarket->getFleetValue($FROM_fleet);
            $ask = $pMarket->convertExpResTypeToDeut($fleetsRow['ex_resource_type'], $fleetsRow['ex_resource_amount']);
            $fleetRatio = round(($fleetValue / $ask), 2);

            $FlyingFleetList[] = [
                'id'                            => $fleetsRow['fleet_id'],
                'type'                          => $fleetsRow['transaction_type'],
                'fleet_resource_metal'          => $fleetsRow['fleet_resource_metal'],
                'fleet_resource_crystal'        => $fleetsRow['fleet_resource_crystal'],
                'fleet_resource_deuterium'      => $fleetsRow['fleet_resource_deuterium'],
                'fleet'                         => $fleet_str,
                'diplo'                         => $fleetsRow['level'],
                'from_alliance'                 => $fleetsRow['ally_id'] == $USER['ally_id'],
                'possible_to_buy'               => $buy['buyable'],
                'reason'                        => $buy['reason'],
                'fleet_wanted_resource'         => $resourceN,
                'fleet_wanted_resource_id'      => $fleetsRow['ex_resource_type'],
                'fleet_wanted_resource_amount'  => $fleetsRow['ex_resource_amount'],
                'end'                           => $fleetsRow['fleet_end_stay'] - TIMESTAMP,
                'from_duration'                 => $FROM_Duration,
                'to_lc_duration'                => $TO_LC_DUR,
                'to_hc_duration'                => $TO_HC_DUR,
                'fleet_ratio'					=> $fleetRatio,
            ];
        }

        $pMarket = new MarketManager();
        $refrates = $pMarket->getReferenceRatios();

        $this->assign([
            'message' => $message,
            'FlyingFleetList'       => $FlyingFleetList,
            'resourceHistory' => $this->getResourceTradeHistory(),
            'fleetHistory' => $this->getFleetTradeHistory(),
            'ratio_metal'					=> $refrates['metal'],
            'ratio_crystal'					=> $refrates['crystal'],
            'ratio_deuterium'				=> $refrates['deuterium'],
        ]);
        $this->tplObj->loadscript('marketplace.js');
        $this->display('page.marketPlace.default.tpl');
    }
}
