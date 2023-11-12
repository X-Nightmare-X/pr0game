<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 * For the full copyright and license information, please view the LICENSE
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */


class ShowRepairdockPage extends AbstractGamePage
{
    public static $requireModule = MODULE_REPAIR_DOCK;

    public static $defaultController = 'show';

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        $resource =& Singleton()->resource;
        $db = Database::get();
        if ($PLANET[$resource[REPAIR_DOCK]] == 0) {
            $this->printMessage($LNG['bd_repairdock_required']);
        }
        $Messages = $USER['messages'];

        $sql = "SELECT * FROM %%PLANET_WRECKFIELD%% WHERE `planetID` = :planetID;";
        $wreckfield = $db->selectSingle($sql, [':planetID' => $PLANET['id']]);

        if (empty($wreckfield)) {
            $this->printMessage($LNG['bd_repairdock_empty']);
        }

        $ships = FleetFunctions::unserialize($wreckfield['ships']);
        $buildTodo = HTTP::_GP('fmenge', []);
        $action = HTTP::_GP('cmd', '');

        $ElementQueue = !empty($wreckfield['repair_order']) ? unserialize($wreckfield['repair_order']) : [];
        $buildList = [];
        if (!empty($action) && $action == 'deploy' && $wreckfield['repair_order_end'] <= TIMESTAMP) {
            $db->startTransaction();
            $sql = "SELECT * FROM %%PLANETS%%
                JOIN %%PLANET_WRECKFIELD%% ON `planetID` = `id`
                WHERE `id` = :planetID FOR UPDATE;";
            $db->selectSingle($sql, [':planetID' => $PLANET['id']]);

            $repaired = [];
            $params = [
                ':planetID' => $PLANET['id'],
            ];
            foreach ($ElementQueue as $elementID => $amount) {
                $repaired[] = '`' . $resource[$elementID] . '` = ' . $resource[$elementID] . ' + :' . $resource[$elementID];
                $params[':' . $resource[$elementID]] = $amount;
                $ships[$elementID] -= floor($amount * (1 - $this->getRepairRate($PLANET[$resource[REPAIR_DOCK]])));
                if ($ships[$elementID] <= 0) {
                    unset($ships[$elementID]);
                }
            }

            if (!empty($repaired)) {
                $sql = "UPDATE %%PLANETS%% SET " . implode(', ', $repaired) . " WHERE id = :planetID;";
                $db->update($sql, $params);

                $sql = "UPDATE %%PLANET_WRECKFIELD%% SET `ships` = :ships, `repair_order` = '', `repair_order_start` = 0, `repair_order_end` = 0
                    WHERE `planetID` = :planetID;";
                $db->update($sql, [
                    ':ships' => FleetFunctions::serialize($ships),
                    ':planetID' => $PLANET['id'],
                ]);
            }
            $db->commit();
            $this->printMessage($LNG['bd_repairdock_deploy'], [[
                'label' => $LNG['bd_continue'],
                'url'   => 'game.php?page=repairdock'
            ]]);
        }
        elseif (empty($ElementQueue) && !empty($buildTodo) && $USER['urlaubs_modus'] == 0) {
            $db->startTransaction();
            $sql = "SELECT * FROM %%PLANET_WRECKFIELD%%
                WHERE `planetID` = :planetID FOR UPDATE;";
            $db->selectSingle($sql, [':planetID' => $PLANET['id']]);

            $QueueTime = 0;
            foreach ($buildTodo as $element => $amount) {
                if (isset($ships[$element])) {
                    $ElementQueue[$element] = max(0, min($amount, $ships[$element] * $this->getRepairRate($PLANET[$resource[REPAIR_DOCK]])));
                    $ElementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $element);
                    $QueueTime += $ElementTime * $ElementQueue[$element];
                    $ships[$element] -= $ElementQueue[$element];
                    $ships[$element] -= floor($ElementQueue[$element] * (1 - $this->getRepairRate($PLANET[$resource[REPAIR_DOCK]])));
                    if ($ships[$element] <= 0) {
                        unset($ships[$element]);
                    }
                }
            }
            $QueueTime = max(1800, min($QueueTime, TIME_12_HOURS)); //Repair duration: min 30m, max 12h

            $sql = "UPDATE %%PLANET_WRECKFIELD%% SET `repair_order` = :repair_order, `repair_order_start` = :start_time, `repair_order_end` = :end_time
                WHERE `planetID` = :planetID;";
            $db->update($sql, [
                ':repair_order' => serialize($ElementQueue),
                ':start_time' => TIMESTAMP,
                ':end_time' => TIMESTAMP + $QueueTime,
                ':planetID' => $PLANET['id'],
            ]);

            $buildList = [
                'ships' => $ElementQueue,
                'start_time' => TIMESTAMP,
                'time' => TIMESTAMP,
                'end_time' => TIMESTAMP + $QueueTime,
                'pretty_end_time' => pretty_time($QueueTime),
            ];

            $db->commit();
        }
        elseif (!empty($ElementQueue)) {
            $buildList = [
                'ships' => $ElementQueue,
                'time' => TIMESTAMP - $wreckfield['repair_order_start'],
                'resttime'  => $USER['urlaubs_modus'] ? ($wreckfield['repair_order_end'] - $USER['urlaubs_start']) : ($wreckfield['repair_order_end'] - TIMESTAMP),
                'endtime'   => _date('U', $wreckfield['repair_order_end'], $USER['timezone']),
                'pretty_end_time' => _date($LNG['php_tdformat'], $wreckfield['repair_order_end'], $USER['timezone']),
            ];

            foreach ($ElementQueue as $element => $amount) {
                if (isset($ships[$element])) {
                    $ships[$element] -= floor($amount * (1 - $this->getRepairRate($PLANET[$resource[REPAIR_DOCK]])));
                    if ($ships[$element] <= 0) {
                        unset($ships[$element]);
                    }
                }
            }
        }

        $elementList = [];
        foreach ($ships as $Element => $amount) {
            if (!isset($ships[$Element])) {
                continue;
            }

            $elementTime = BuildFunctions::getBuildingTime($USER, $PLANET, $Element);
            $maxBuildable = floor($amount * $this->getRepairRate($PLANET[$resource[REPAIR_DOCK]]));

            $elementList[$Element] = [
                'id' => $Element,
                'available' => $PLANET[$resource[$Element]],
                'elementTime' => $elementTime,
                'maxBuildable' => floatToString($maxBuildable),
            ];
        }
        $SolarEnergy = round((($PLANET['temp_max'] + 160) / 6) * Config::get()->energySpeed, 1);

        $buisy = !empty($wreckfield['repair_order']);

        if ($buisy) {
            $this->tplObj->loadscript('repairdock.js');
        }

        $this->assign([
            'umode' => $USER['urlaubs_modus'],
            'buisy' => $buisy,
            'elementList' => $elementList,
            'List' => $buildList,
            'messages' => ($Messages > 0) ? (($Messages == 1) ? $LNG['ov_have_new_message']
                : sprintf($LNG['ov_have_new_messages'], pretty_number($Messages))) : false,
            'SolarEnergy' => $SolarEnergy,
        ]);

        $this->display('page.repairdock.default.tpl');
    }

    private function getRepairRate($repairDockLvl) : float {
        return (44 + pow($repairDockLvl, 0.85)) / 100;
    }
}
