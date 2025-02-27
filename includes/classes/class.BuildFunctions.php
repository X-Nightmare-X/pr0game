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

class BuildFunctions
{
    public static $bonusList = [
        'Attack',
        'Defensive',
        'Shield',
        'BuildTime',
        'ResearchTime',
        'ShipTime',
        'DefensiveTime',
        'Resource',
        'Energy',
        'ResourceStorage',
        'ShipStorage',
        'FlyTime',
        'FleetSlots',
        'Planets',
        'SpyPower',
        'Expedition',
        'GateCoolTime',
        'MoreFound',
    ];

    public static function getBonusList()
    {
        return self::$bonusList;
    }

    public static function getRestPrice($USER, $PLANET, $Element, $elementPrice = null)
    {
        $resource =& Singleton()->resource;

        if (!isset($elementPrice)) {
            $elementPrice = self::getElementPrice($USER, $PLANET, $Element);
        }

        $overflow = [];

        foreach ($elementPrice as $resType => $resPrice) {
            $available = isset($PLANET[$resource[$resType]])
                ? $PLANET[$resource[$resType]] : $USER[$resource[$resType]];
            $overflow[$resType] = max($resPrice - floor($available), 0);
        }

        return $overflow;
    }

    public static function getElementPrice($USER, $PLANET, $Element, $forDestroy = false, $forLevel = null)
    {
        $pricelist =& Singleton()->pricelist;
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        if (
            in_array($Element, $reslist['fleet']) || in_array($Element, $reslist['defense'])
            || in_array($Element, $reslist['missile'])
        ) {
            $elementLevel = $forLevel;
        } elseif (isset($forLevel)) {
            $elementLevel = $forLevel;
        } elseif (isset($PLANET[$resource[$Element]])) {
            $elementLevel = $PLANET[$resource[$Element]];
        } elseif (isset($USER[$resource[$Element]])) {
            $elementLevel = $USER[$resource[$Element]];
        } else {
            return [];
        }

        $price = [];
        foreach ($reslist['ressources'] as $resType) {
            if (!isset($pricelist[$Element]['cost'][$resType])) {
                continue;
            }
            $ressourceAmount = $pricelist[$Element]['cost'][$resType];

            if ($ressourceAmount == 0) {
                continue;
            }

            $price[$resType] = $ressourceAmount;

            if (
                isset($pricelist[$Element]['factor']) && $pricelist[$Element]['factor'] != 0
                && $pricelist[$Element]['factor'] != 1
            ) {
                $price[$resType] *= pow($pricelist[$Element]['factor'], $elementLevel - 1);
            }

            if (
                $forLevel && (in_array($Element, $reslist['fleet']) || in_array($Element, $reslist['defense'])
                    || in_array($Element, $reslist['missile']))
            ) {
                $price[$resType] *= $elementLevel;
            }

            if ($forDestroy === true) {
                $price[$resType]    /= 2;
            }
        }

        return $price;
    }

    public static function isTechnologieAccessible($USER, $PLANET, $Element)
    {
        $requeriments =& Singleton()->requeriments;
        $resource =& Singleton()->resource;

        if (!isset($requeriments[$Element])) {
            return true;
        }

        foreach ($requeriments[$Element] as $ReqElement => $EleLevel) {
            if (
                (isset($USER[$resource[$ReqElement]]) && $USER[$resource[$ReqElement]] < $EleLevel) ||
                (isset($PLANET[$resource[$ReqElement]]) && $PLANET[$resource[$ReqElement]] < $EleLevel)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if an element is enabled in the current universe.
     * Buldings, ships etc are enabled for all universes via table vars.
     * To disable individual elements one can extend the switch cases and check e.g. for an active module.
     *
     * @param integer $elementID
     * @return boolean
     */
    public static function isEnabled(int $elementID) : bool {
        switch ($elementID) {
            case REPAIR_DOCK:
                return isModuleAvailable(MODULE_REPAIR_DOCK);
            default:
                return true;
        }
    }

    public static function getBuildingTime(
        $USER,
        $PLANET,
        $Element,
        $elementPrice = null,
        $forDestroy = false,
        $forLevel = null,
        $maxQueueIndex = 0
    ) {
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $requeriments =& Singleton()->requeriments;
        $config = Config::get($USER['universe']);

        $time = 0;

        if (!isset($elementPrice)) {
            $elementPrice = self::getElementPrice($USER, $PLANET, $Element, $forDestroy, $forLevel);
        }

        $elementCost = 0;

        if (isset($elementPrice[RESOURCE_METAL])) {
            $elementCost += $elementPrice[RESOURCE_METAL];
        }

        if (isset($elementPrice[RESOURCE_CRYSTAL])) {
            $elementCost += $elementPrice[RESOURCE_CRYSTAL];
        }

        if (in_array($Element, $reslist['build'])) {
            $maxQueueIndex = max(0, $maxQueueIndex);
            $robo = $PLANET[$resource[14]];
            $nani = $PLANET[$resource[15]];
            $CurrentQueue = !empty($PLANET['b_building_id']) ? unserialize($PLANET['b_building_id']) : [];
            if (!empty($CurrentQueue) && count($CurrentQueue) != 0) {
                foreach ($CurrentQueue as $index => $QueueSubArray) {
                    if ($index >= $maxQueueIndex) {
                        break;
                    }
                    if ($QueueSubArray[0] == ROBOT_FACTORY) {
                        if ($QueueSubArray[4] == 'build') {
                            $robo++;
                        } else {
                            $robo--;
                        }
                    } elseif ($QueueSubArray[0] == NANITE_FACTORY) {
                        if ($QueueSubArray[4] == 'build') {
                            $nani++;
                        } else {
                            $nani--;
                        }
                    }
                }
            }
            $time = $elementCost / ($config->building_speed * (1 + $robo))
                * pow(0.5, $nani);
        } elseif (in_array($Element, $reslist['fleet'])) {
            $time = $elementCost / ($config->shipyard_speed * (1 + $PLANET[$resource[SHIPYARD]]))
                * pow(0.5, $PLANET[$resource[NANITE_FACTORY]]);
        } elseif (in_array($Element, $reslist['defense'])) {
            $time = $elementCost / ($config->shipyard_speed * (1 + $PLANET[$resource[SHIPYARD]]))
                * pow(0.5, $PLANET[$resource[NANITE_FACTORY]]);
        } elseif (in_array($Element, $reslist['missile'])) {
            $time = $elementCost / ($config->shipyard_speed * (1 + $PLANET[$resource[SHIPYARD]]))
                * pow(0.5, $PLANET[$resource[NANITE_FACTORY]]);
        } elseif (in_array($Element, $reslist['tech'])) {
            if (is_numeric($PLANET[$resource[RESEARCH_LABORATORY] . '_inter'])) {
                $Level = $PLANET[$resource[RESEARCH_LABORATORY]];
            } else {
                $Level = 0;
                foreach ($PLANET[$resource[RESEARCH_LABORATORY] . '_inter'] as $Levels) {
                    if (!isset($requeriments[$Element][RESEARCH_LABORATORY]) || $Levels >= $requeriments[$Element][RESEARCH_LABORATORY]) {
                        $Level += $Levels;
                    }
                }
            }

            $time = $elementCost / (1000 * (1 + $Level)) / ($config->research_speed / 2500)
                * pow(1 - $config->factor_university / 100, $PLANET[$resource[6]]);
        }

        if ($forDestroy) {
            $time = floor($time * 1300);
        } else {
            $time = floor($time * 3600);
        }

        return max($time, $config->min_build_time);
    }

    public static function isElementBuyable(
        $USER,
        $PLANET,
        $Element,
        $elementPrice = null
    ) {
        $rest = self::getRestPrice($USER, $PLANET, $Element, $elementPrice);
        return count(array_filter($rest)) === 0;
    }

    public static function getMaxConstructibleElements($USER, $PLANET, $Element, $elementPrice = null)
    {
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        if (!isset($elementPrice)) {
            $elementPrice = self::getElementPrice($USER, $PLANET, $Element);
        }

        $maxElement = [];

        foreach ($elementPrice as $resourceID => $price) {
            if (isset($PLANET[$resource[$resourceID]])) {
                $maxElement[] = floor($PLANET[$resource[$resourceID]] / $price);
            } elseif (isset($USER[$resource[$resourceID]])) {
                $maxElement[] = floor($USER[$resource[$resourceID]] / $price);
            } else {
                throw new Exception("Unknown Ressource " . $resourceID . " at element " . $Element . ".");
            }
        }

        if (in_array($Element, $reslist['one'])) {
            $maxElement[] = 1;
        }

        return min($maxElement);
    }

    public static function getMaxConstructibleRockets($USER, $PLANET, $Missiles = null)
    {
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        if (!isset($Missiles)) {
            $Missiles = [];

            foreach ($reslist['missile'] as $elementID) {
                $Missiles[$elementID] = $PLANET[$resource[$elementID]];
            }
        }

        $BuildArray = !empty($PLANET['b_hangar_id']) ? unserialize($PLANET['b_hangar_id']) : [];
        $MaxMissiles = $PLANET[$resource[SILO]] * 10 * max(Config::get()->silo_factor, 1);

        foreach ($BuildArray as $ElementArray) {
            if (isset($Missiles[$ElementArray[0]])) {
                $Missiles[$ElementArray[0]] += $ElementArray[1];
            }
        }

        $ActuMissiles = $Missiles[INTERCEPTOR_MISSILE] + (2 * $Missiles[INTERPLANETARY_MISSILE]);
        $MissilesSpace = max(0, $MaxMissiles - $ActuMissiles);

        return [
            INTERCEPTOR_MISSILE => $MissilesSpace,
            INTERPLANETARY_MISSILE => floor($MissilesSpace / 2),
        ];
    }

    public static function getAvalibleBonus($Element)
    {
        $pricelist =& Singleton()->pricelist;

        $elementBonus = [];

        foreach (self::$bonusList as $bonus) {
            $temp = (float) $pricelist[$Element]['bonus'][$bonus][0];
            if (empty($temp)) {
                continue;
            }

            $elementBonus[$bonus] = $pricelist[$Element]['bonus'][$bonus];
        }

        return $elementBonus;
    }
}
