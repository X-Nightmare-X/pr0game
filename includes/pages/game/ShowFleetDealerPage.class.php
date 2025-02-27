<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */


class ShowFleetDealerPage extends AbstractGamePage
{
    public static $requireModule = MODULE_FLEET_TRADER;

    public function __construct()
    {
        parent::__construct();
    }

    public function send()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        $pricelist =& Singleton()->pricelist;
        $resource =& Singleton()->resource;
        $shipID = HTTP::_GP('shipID', 0);
        $Count = max(0, round(HTTP::_GP('count', 0.0)));
        $allowedShipIDs = explode(',', Config::get()->trade_allowed_ships);

        if (
            !empty($shipID)
            && !empty($Count)
            && in_array($shipID, $allowedShipIDs)
            && $PLANET[$resource[$shipID]] >= $Count
        ) {
            $tradeCharge = 1 - (Config::get()->trade_charge / 100);

            $resources = [
                RESOURCE_METAL => $Count * $pricelist[$shipID]['cost'][RESOURCE_METAL] * $tradeCharge,
                RESOURCE_CRYSTAL => $Count * $pricelist[$shipID]['cost'][RESOURCE_CRYSTAL] * $tradeCharge,
                RESOURCE_DEUT => $Count * $pricelist[$shipID]['cost'][RESOURCE_DEUT] * $tradeCharge,
            ];

            $this->ecoObj->addResources($PLANET['id'], $resources, $PLANET);

            $PLANET[$resource[$shipID]] -= $Count;

            $sql = 'UPDATE %%PLANETS%% SET ' . $resource[$shipID] . ' = ' . $resource[$shipID] . ' - :count'
                . ' WHERE id = :planetID;';
            Database::get()->update($sql, [
                ':count'    => $Count,
                ':planetID' => $PLANET['id']
            ]);

            $this->printMessage($LNG['tr_exchange_done'], [
                [
                    'label' => $LNG['sys_forward'],
                    'url'   => 'game.php?page=fleetDealer'
                ]
            ]);
        } else {
            $this->printMessage($LNG['tr_exchange_error'], [
                [
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetDealer'
                ]
            ]);
        }
    }

    public function show()
    {
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        $pricelist =& Singleton()->pricelist;
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $Cost = [];

        $allowedShipIDs = explode(',', Config::get()->trade_allowed_ships);

        foreach ($allowedShipIDs as $shipID) {
            if (in_array($shipID, $reslist['fleet']) || in_array($shipID, $reslist['defense'])) {
                $Cost[$shipID]  = [$PLANET[$resource[$shipID]], $LNG['tech'][$shipID], $pricelist[$shipID]['cost']];
            }
        }

        if (empty($Cost)) {
            $this->printMessage($LNG['ft_empty'], [
                [
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetDealer'
                ]
            ]);
        }

        $this->assign([
            'shipIDs'   => $allowedShipIDs,
            'CostInfos' => $Cost,
            'Charge'    => Config::get()->trade_charge,
        ]);

        $this->display('page.fleetDealer.default.tpl');
    }
}
