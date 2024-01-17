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

class FlyingFleetsTable
{
    protected $Mode = null;
    protected $userId = null;
    protected $planetId = null;
    protected $IsPhalanx = false;
    protected $missions = false;

    public function __construct()
    {
    }

    public function setUser($userId)
    {
        $this->userId = $userId;
    }

    public function setPlanet($planetId)
    {
        $this->planetId = $planetId;
    }

    public function setPhalanxMode()
    {
        $this->IsPhalanx = true;
    }

    public function setMissions($missions)
    {
        $this->missions = implode(',', array_filter(explode(',', $missions), 'is_numeric'));
    }

    private function getFleets($acsID = false)
    {
        if ($this->IsPhalanx && empty($acsID)) {
            $where = '(fleet_start_id = :planetId AND fleet_start_type = 1 AND fleet_mission != :stay) OR'
                . ' (fleet_end_id = :planetId AND fleet_end_type = 1 AND fleet_mission != :rec AND fleet_mess IN (0, 2))';

            $param = [
                ':planetId' => $this->planetId,
                ':stay'     => MISSION_STATION,
                ':rec'      => MISSION_RECYCLING,
            ];
        } elseif (!empty($acsID)) {
            $where  = 'fleet_group = :acsId';
            $param = [':acsId'    => $acsID];
        } elseif ($this->missions) {
            $where = '(fleet_owner = :userId OR (fleet_target_owner = :userId AND fleet_mission != :rec)) AND'
                . ' fleet_mission IN (' . $this->missions . ')';
            $param = [
                ':userId'   => $this->userId,
                ':rec'      => MISSION_RECYCLING,
            ];
        } else {
            $where  = 'fleet_owner = :userId OR (fleet_target_owner = :userId AND fleet_mission != :rec AND fleet_mission != :trade)';
            $param = [
                ':userId'   => $this->userId,
                ':rec'      => MISSION_RECYCLING,
                ':trade'    => MISSION_TRADE,
            ];
        }

        $sql = 'SELECT DISTINCT fleet.*, ownuser.username as own_username, targetuser.username as target_username,
		ownplanet.name as own_planetname, targetplanet.name as target_planetname
		FROM %%FLEETS%% fleet
		LEFT JOIN %%USERS%% ownuser ON (ownuser.id = fleet.fleet_owner)
		LEFT JOIN %%USERS%% targetuser ON (targetuser.id = fleet.fleet_target_owner)
		LEFT JOIN %%PLANETS%% ownplanet ON (ownplanet.id = fleet.fleet_start_id)
		LEFT JOIN %%PLANETS%% targetplanet ON (targetplanet.id = fleet.fleet_end_id)
		WHERE ' . $where . ';';

        return Database::get()->select($sql, $param);
    }

    public function renderTable()
    {
        $fleetResult    = $this->getFleets();
        $ACSDone        = [];
        $FleetData      = [];

        foreach ($fleetResult as $fleetRow) {
            if (
                $fleetRow['fleet_mess'] == 0 && $fleetRow['fleet_start_time'] > TIMESTAMP
                && ($fleetRow['fleet_group'] == 0 || !isset($ACSDone[$fleetRow['fleet_group']]))
            ) {
                $ACSDone[$fleetRow['fleet_group']]      = true;
                $FleetData[$fleetRow['fleet_start_time'] . $fleetRow['fleet_id']] = $this->buildFleetEventTable(
                    $fleetRow,
                    0
                );
            }

            if (
                $fleetRow['fleet_mission'] == MISSION_MISSILE
                || (($fleetRow['fleet_mission'] == MISSION_TRANSFER
                || ($fleetRow['fleet_mission'] == MISSION_STATION) && $fleetRow['fleet_mess'] == 0))
            ) {
                continue;
            }

            if (
                $fleetRow['fleet_end_stay'] != $fleetRow['fleet_start_time']
                && $fleetRow['fleet_end_stay'] > TIMESTAMP
                && ($this->IsPhalanx && $fleetRow['fleet_end_id'] == $this->planetId)
            ) {
                $FleetData[$fleetRow['fleet_end_stay'] . $fleetRow['fleet_id']] = $this->buildFleetEventTable(
                    $fleetRow,
                    2
                );
            }

            $MissionsOK = 5;
            if ($fleetRow['fleet_end_stay'] > TIMESTAMP && $fleetRow['fleet_mission'] == $MissionsOK) {
                $FleetData[$fleetRow['fleet_end_stay'] . $fleetRow['fleet_id']] = $this->buildFleetEventTable(
                    $fleetRow,
                    2
                );
            }

            if ($fleetRow['fleet_owner'] != $this->userId) {
                continue;
            }

            if ($fleetRow['fleet_end_time'] > TIMESTAMP) {
                $FleetData[$fleetRow['fleet_end_time'] . $fleetRow['fleet_id']] = $this->buildFleetEventTable(
                    $fleetRow,
                    1
                );
            }
        }

        ksort($FleetData);
        return $FleetData;
    }

    private function buildFleetEventTable($fleetRow, $FleetState)
    {
        $Time   = 0;
        $Rest   = 0;

        if (
            $FleetState == 0 && $this->IsPhalanx && $fleetRow['fleet_group'] != 0
            && (
                strpos(
                    (
                        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http"
                    ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
                    'page=phalanx'
                ) !== false
            )
        ) {
            // Rebuilt the code above to eliminate possible errors with ACS without Phalanx.
            $acsResult      = $this->getFleets($fleetRow['fleet_group']);
            $EventString    = '';

            foreach ($acsResult as $acsRow) {
                if ($acsRow['fleet_group'] != 0) {
                    $Return         = $this->getEventData($acsRow, $FleetState);

                    $Rest           = $Return[0];
                    $EventString    .= $Return[1] . '<br><br>';
                    $Time           = $Return[2];
                }
            }

            $EventString    = substr($EventString, 0, -8);
        } elseif ($FleetState == 0 && $fleetRow['fleet_group'] != 0) {
            $acsResult      = $this->getFleets($fleetRow['fleet_group']);
            $EventString    = '';

            foreach ($acsResult as $acsRow) {
                $Return         = $this->getEventData($acsRow, $FleetState);

                $Rest           = $Return[0];
                $EventString    .= $Return[1] . '<br><br>';
                $Time           = $Return[2];
            }

            $EventString    = substr($EventString, 0, -8);
        } else {
            list($Rest, $EventString, $Time) = $this->getEventData($fleetRow, $FleetState);
        }

        return [
            'text'          => $EventString,
            'returntime'    => $Time,
            'resttime'      => $Rest,
        ];
    }

    public function getEventData($fleetRow, $Status)
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        $db = Database::get();
        $Owner          = $fleetRow['fleet_owner'] == $this->userId;
        $friendly = !$Owner && $fleetRow['fleet_target_owner'] != $this->userId;
        $FleetStyle  = [
            MISSION_ATTACK => 'attack',
            MISSION_ACS => 'federation',
            MISSION_TRANSPORT => 'transport',
            MISSION_STATION => 'deploy',
            MISSION_HOLD => 'hold',
            MISSION_SPY => 'espionage',
            MISSION_COLONISATION => 'colony',
            MISSION_RECYCLING => 'harvest',
            MISSION_DESTRUCTION => 'destroy',
            MISSION_MISSILE => 'missile',
            MISSION_EXPEDITION => 'transport',
            MISSION_TRADE => 'transport',
            MISSION_TRANSFER => 'transport',
        ];

        $GoodMissions = [MISSION_TRANSPORT, MISSION_HOLD];
        $MissionType = $fleetRow['fleet_mission'];
        if ($friendly && $fleetRow['fleet_group'] != 0) {
            $MissionType = MISSION_ACS;
        }

        $FleetPrefix = ($Owner == true) ? 'own' : '';
        if ($MissionType != MISSION_ATTACK && !$Owner && !$friendly && $fleetRow['fleet_group'] != 0) {
            $FleetPrefix = 'hostile';
        }
        $FleetType = $FleetPrefix . $FleetStyle[$MissionType];
        if (
            !$Owner
            && ($MissionType == MISSION_ATTACK || $MissionType == MISSION_ACS)
            && $Status == FLEET_OUTWARD && $fleetRow['fleet_target_owner'] != $this->userId
        ) {
            $FleetName = $LNG['cff_acs_fleet'];
        } else {
            $FleetName = $LNG['ov_fleet'];
        }
        $FleetContent = $this->createFleetPopupedFleetLink(
            $fleetRow,
            $FleetName,
            $FleetPrefix . $FleetStyle[$MissionType]
        );
        $FleetCapacity = $this->createFleetPopupedMissionLink(
            $fleetRow,
            $LNG['type_mission_' . $MissionType],
            $FleetPrefix . $FleetStyle[$MissionType]
        );
        $FleetClass = '';
        $StartType = $LNG['type_planet_' . $fleetRow['fleet_start_type']];
        $TargetType = $LNG['type_planet_' . $fleetRow['fleet_end_type']];

        if ($MissionType == MISSION_RECYCLING) {
            if ($Status == FLEET_OUTWARD) {
                $EventString = sprintf(
                    $LNG['cff_mission_own_recy_0'],
                    $FleetContent,
                    $StartType,
                    $fleetRow['own_planetname'],
                    GetStartAddressLink($fleetRow, $FleetType),
                    GetTargetAddressLink($fleetRow, $FleetType),
                    $FleetCapacity
                );
            } else {
                $EventString = sprintf(
                    $LNG['cff_mission_own_recy_1'],
                    $FleetContent,
                    GetTargetAddressLink($fleetRow, $FleetType),
                    $StartType,
                    $fleetRow['own_planetname'],
                    GetStartAddressLink($fleetRow, $FleetType),
                    $FleetCapacity
                );
            }
        } elseif ($MissionType == MISSION_MISSILE) {
            if ($Owner) {
                $EventString = sprintf(
                    $LNG['cff_mission_own_mip'],
                    $fleetRow['fleet_amount'],
                    $StartType,
                    $fleetRow['own_planetname'],
                    GetStartAddressLink($fleetRow, $FleetType),
                    $TargetType,
                    $fleetRow['target_planetname'],
                    GetTargetAddressLink($fleetRow, $FleetType)
                );
            } else {
                $EventString = sprintf(
                    $LNG['cff_mission_target_mip'],
                    $fleetRow['fleet_amount'],
                    $this->buildHostileFleetPlayerLink($fleetRow),
                    $StartType,
                    $fleetRow['own_planetname'],
                    GetStartAddressLink($fleetRow, $FleetType),
                    $TargetType,
                    $fleetRow['target_planetname'],
                    GetTargetAddressLink($fleetRow, $FleetType)
                );
            }
        } elseif ($MissionType == MISSION_EXPEDITION) {
            if ($Status == FLEET_OUTWARD) {
                $EventString = sprintf(
                    $LNG['cff_mission_own_expo_0'],
                    $FleetContent,
                    $StartType,
                    $fleetRow['own_planetname'],
                    GetStartAddressLink($fleetRow, $FleetType),
                    GetTargetAddressLink($fleetRow, $FleetType),
                    $FleetCapacity
                );
            } elseif ($Status == FLEET_HOLD) {
                $EventString = sprintf(
                    $LNG['cff_mission_own_expo_2'],
                    $FleetContent,
                    $StartType,
                    $fleetRow['own_planetname'],
                    GetStartAddressLink($fleetRow, $FleetType),
                    GetTargetAddressLink($fleetRow, $FleetType),
                    $FleetCapacity
                );
            } else {
                $EventString = sprintf(
                    $LNG['cff_mission_own_expo_1'],
                    $FleetContent,
                    GetTargetAddressLink($fleetRow, $FleetType),
                    $StartType,
                    $fleetRow['own_planetname'],
                    GetStartAddressLink($fleetRow, $FleetType),
                    $FleetCapacity
                );
            }
        } else {
            if ($Owner == true) {
                if ($Status == FLEET_OUTWARD) {
                    $EventString  = sprintf(
                        $LNG['cff_mission_own_0'],
                        $FleetContent,
                        $StartType,
                        $fleetRow['own_planetname'],
                        GetStartAddressLink($fleetRow, $FleetType),
                        $TargetType,
                        $fleetRow['target_planetname'],
                        GetTargetAddressLink($fleetRow, $FleetType),
                        $FleetCapacity
                    );
                } elseif ($Status == FLEET_RETURN) {
                    $EventString  = sprintf(
                        $LNG['cff_mission_own_1'],
                        $FleetContent,
                        $TargetType,
                        $fleetRow['target_planetname'],
                        GetTargetAddressLink($fleetRow, $FleetType),
                        $StartType,
                        $fleetRow['own_planetname'],
                        GetStartAddressLink($fleetRow, $FleetType),
                        $FleetCapacity
                    );
                } else {
                    $EventString  = sprintf(
                        $LNG['cff_mission_own_2'],
                        $FleetContent,
                        $StartType,
                        $fleetRow['own_planetname'],
                        GetStartAddressLink($fleetRow, $FleetType),
                        $TargetType,
                        $fleetRow['target_planetname'],
                        GetTargetAddressLink($fleetRow, $FleetType),
                        $FleetCapacity
                    );
                }
            } else {
                if (($MissionType == MISSION_ATTACK || $MissionType == MISSION_ACS) && $Status == FLEET_OUTWARD) {
                    $EventString = sprintf(
                        $LNG['cff_mission_acs'],
                        $FleetContent,
                        $StartType,
                        $fleetRow['own_planetname'],
                        GetStartAddressLink($fleetRow, $FleetType),
                        $TargetType,
                        $fleetRow['target_planetname'],
                        GetTargetAddressLink($fleetRow, $FleetType),
                        $FleetCapacity
                    );
                } else {
                    if ($Status == FLEET_HOLD) {
                        $Message    = $LNG['cff_mission_target_stay'];
                    } elseif (in_array($MissionType, $GoodMissions)) {
                        $Message    = $LNG['cff_mission_target_good'];
                    } else {
                        $Message    = $LNG['cff_mission_target_bad'];
                    }

                    $EventString = sprintf(
                        $Message,
                        $FleetContent,
                        $this->buildHostileFleetPlayerLink($fleetRow),
                        $StartType,
                        $fleetRow['own_planetname'],
                        GetStartAddressLink($fleetRow, $FleetType),
                        $TargetType,
                        $fleetRow['target_planetname'],
                        GetTargetAddressLink($fleetRow, $FleetType),
                        $FleetCapacity
                    );
                }
            }
        }if ($Status == FLEET_OUTWARD) {
            if ($MissionType == MISSION_ACS) {
                $sql = "SELECT count(uta.acsID) as count FROM %%USERS_TO_ACS%% AS uta 
                    INNER JOIN %%FLEETS%% AS f ON uta.acsID = f.fleet_group 
                    WHERE uta.userID = :userId AND f.fleet_id = :fleetId;";
                $friend = $db->selectSingle($sql, [
                    ':userId'   => $USER['id'],
                    ':fleetId'  => $fleetRow['fleet_id']
                ], 'count');
                if ($Owner == true) {
                    $FleetClass = 'colorMission2Own';
                } elseif ($friend > 0) {
                    $FleetClass = 'colorMission2friend';
                } else {
                    $FleetClass = 'colorMission2Foreign';
                }
            } elseif ($MissionType == MISSION_ATTACK) {
                $FleetClass = $Owner ? 'colorMission1Own' : 'colorMission1Foreign';
            } elseif ($MissionType == MISSION_TRANSPORT) {
                $FleetClass = $Owner ? 'colorMission3Own' : 'colorMission3Foreign';
            } elseif ($MissionType == MISSION_STATION) {
                $FleetClass = $Owner ? 'colorMission4Own' : 'colorMission4Foreign';
            } elseif ($MissionType == MISSION_HOLD) {
                $FleetClass = $Owner ? 'colorMission5Own' : 'colorMission5Foreign';
            } elseif ($MissionType == MISSION_SPY) {
                $FleetClass = $Owner ? 'colorMission6Own' : 'colorMission6Foreign';
            } elseif ($MissionType == MISSION_COLONISATION) {
                $FleetClass = $Owner ? 'colorMission7Own' : 'colorMission7Foreign';
            } elseif ($MissionType == MISSION_RECYCLING) {
                $FleetClass = $Owner ? 'colorMission8Own' : 'colorMission8Foreign';
            } elseif ($MissionType == MISSION_DESTRUCTION) {
                $FleetClass = $Owner ? 'colorMission9Own' : 'colorMission9Foreign';
            } elseif ($MissionType == MISSION_MISSILE) {
                $FleetClass = $Owner ? 'colorMission10Own' : 'colorMission10Foreign';
            } elseif ($MissionType == MISSION_EXPEDITION) {
                $FleetClass = $Owner ? 'colorMission15Own' : 'colorMission15Foreign';
            } elseif ($MissionType == MISSION_TRADE) {
                $FleetClass = $Owner ? 'colorMission16Own' : 'colorMission16Foreign';
            } elseif ($MissionType == MISSION_TRANSFER) {
                $FleetClass = $Owner ? 'colorMission17Own' : 'colorMission17Foreign';
            }
        } else {
            if ($MissionType == MISSION_COLONISATION) {
                $FleetClass = $Owner ? 'colorMission7OwnReturn' : 'colorMissionReturnForeign';
            } else {
                $FleetClass = $Owner ? 'colorMissionReturnOwn' : 'colorMissionReturnForeign';
            }
        }

        $EventString = '<span class="' . $FleetClass . '">' . $EventString . '</span>';

        if ($Status == FLEET_OUTWARD) {
            $Time = $fleetRow['fleet_start_time'];
        } elseif ($Status == FLEET_RETURN) {
            $Time = $fleetRow['fleet_end_time'];
        } elseif ($Status == FLEET_HOLD) {
            $Time = $fleetRow['fleet_end_stay'];
        } else {
            $Time = TIMESTAMP;
        }

        $Rest   = $Time - TIMESTAMP;
        return [$Rest, $EventString, $Time];
    }

    private function buildHostileFleetPlayerLink($fleetRow)
    {
        $LNG =& Singleton()->LNG;
        return $fleetRow['own_username']
            . ' <a href="#" onclick="return Dialog.PM(' . $fleetRow['fleet_owner'] . ')">' . $LNG['PM'] . '</a>';
    }

    private function createFleetPopupedMissionLink($fleetRow, $Texte, $FleetType)
    {
        $LNG =& Singleton()->LNG;
        $FleetTotalC  = $fleetRow['fleet_resource_metal'] + $fleetRow['fleet_resource_crystal']
            + $fleetRow['fleet_resource_deuterium'];
        if ($FleetTotalC != 0 && !$this->IsPhalanx) {
            $textForBlind = $LNG['tech'][900] . ': ';
            $textForBlind .= floatToString($fleetRow['fleet_resource_metal']) . ' ' . $LNG['tech'][901];
            $textForBlind .= '; ' . floatToString($fleetRow['fleet_resource_crystal']) . ' ' . $LNG['tech'][902];
            $textForBlind .= '; ' . floatToString($fleetRow['fleet_resource_deuterium']) . ' ' . $LNG['tech'][903];

            $FRessource   = '<table style=\'width:200px\'>';
            $FRessource  .= '<tr><td style=\'width:50%;color:white\'>' . $LNG['tech'][901]
                . '</td><td style=\'width:50%;color:white\'>' . pretty_number($fleetRow['fleet_resource_metal'])
                . '</td></tr>';
            $FRessource  .= '<tr><td style=\'width:50%;color:white\'>' . $LNG['tech'][902]
                . '</td><td style=\'width:50%;color:white\'>' . pretty_number($fleetRow['fleet_resource_crystal'])
                . '</td></tr>';
            $FRessource  .= '<tr><td style=\'width:50%;color:white\'>' . $LNG['tech'][903]
                . '</td><td style=\'width:50%;color:white\'>' . pretty_number($fleetRow['fleet_resource_deuterium'])
                . '</td></tr>';
            $FRessource  .= '</table>';

            $MissionPopup  = '<a data-tooltip-content="' . $FRessource . '" class="tooltip ' . $FleetType . '">'
                . $Texte . '</a><span class="textForBlind"> (' . $textForBlind . ')</span>';
        } else {
            $MissionPopup  = $Texte;
        }

        return $MissionPopup;
    }

    private function createFleetPopupedFleetLink($fleetRow, $Text, $FleetType)
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        $resource =& Singleton()->resource;
        $SpyTech        = $USER[$resource[106]];
        $Owner          = $fleetRow['fleet_owner'] == $this->userId;
        $FleetRec       = explode(';', $fleetRow['fleet_array']);
        $FleetPopup     = '<a href="#" data-tooltip-content="<table style=\'width:200px\'>';
        $textForBlind   = '';
        if ($this->IsPhalanx || $SpyTech >= 2 || $Owner) {
            if ($SpyTech < 8 && !$Owner) {
                $addColon = $SpyTech >= 4 ? ": " : "";
                $FleetPopup     .= '<tr><td style=\'width:100%;color:white\'>' . $LNG['cff_aproaching']
                    . $fleetRow['fleet_amount'] . $LNG['cff_ships'] . $addColon . '</td></tr>';
                $textForBlind   = $LNG['cff_aproaching'] . $fleetRow['fleet_amount'] . $LNG['cff_ships'] . $addColon;
            }
            $shipsData  = [];
            foreach ($FleetRec as $Item => $Group) {
                if (empty($Group)) {
                    continue;
                }

                $Ship    = explode(',', $Group);
                if ($Owner) {
                    $FleetPopup     .= '<tr><td style=\'width:50%;color:white\'>' . $LNG['tech'][$Ship[0]]
                        . ':</td><td style=\'width:50%;color:white\'>' . pretty_number($Ship[1]) . '</td></tr>';
                    $shipsData[]    = floatToString($Ship[1]) . ' ' . $LNG['tech'][$Ship[0]];
                } else {
                    if ($SpyTech >= 8) {
                        $FleetPopup     .= '<tr><td style=\'width:50%;color:white\'>' . $LNG['tech'][$Ship[0]]
                            . ':</td><td style=\'width:50%;color:white\'>' . pretty_number($Ship[1]) . '</td></tr>';
                        $shipsData[]    = floatToString($Ship[1]) . ' ' . $LNG['tech'][$Ship[0]];
                    } elseif ($SpyTech >= 4) {
                        $FleetPopup     .= '<tr><td style=\'width:100%;color:white\'>' . $LNG['tech'][$Ship[0]]
                            . '</td></tr>';
                        $shipsData[]    = $LNG['tech'][$Ship[0]];
                    }
                }
            }
            $textForBlind   .= implode('; ', $shipsData);
        } else {
            $FleetPopup     .= '<tr><td style=\'width:100%;color:white\'>' . $LNG['cff_no_fleet_data']
                . '</span></td></tr>';
            $textForBlind   = $LNG['cff_no_fleet_data'];
        }

        $FleetPopup  .= '</table>" class="tooltip ' . $FleetType . '">' . $Text . '</a><span class="textForBlind"> ('
            . $textForBlind . ')</span>';

        return $FleetPopup;
    }
}
