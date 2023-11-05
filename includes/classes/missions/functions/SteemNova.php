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

function fight(&$attackers, &$defenders, $sim, &$advancedStats)
{
    $attack = new Ds\Map(['attack' => 0, 'shield' => 0]);
    $defense = new Ds\Map(['attack' => 0, 'shield' => 0]);

    // attackers shoot
    foreach ($attackers as $fleetID => $attacker) {
        foreach ($attacker['units'] as $element => $unit) {
            shoot($attacker['player']['id'], $unit, $defenders, $attack, $sim, $advancedStats);
        }
    }
    // defenders shoot
    foreach ($defenders as $fleetID => $defender) {
        foreach ($defender['units'] as $element => $unit) {
            shoot($defender['player']['id'], $unit, $attackers, $defense, $sim, $advancedStats);
        }
    }

    return new Ds\Map([
        'attack' => $attack['attack'],
        'defense' => $defense['attack'],
        'attackShield' => $defense['shield'],
        'defShield' => $attack['shield'],
    ]);
}

function destroy(&$attackers)
{
    foreach ($attackers as $fleetID => &$attacker) {
        // foreach ($attacker['units'] as $element => $unit)
        for ($i = 0; $i < count($attacker['units']); $i++) {
            $unit = $attacker['units'][$i];
            if ($unit['armor'] <= 0 || $unit['explode']) {
                // destroy unit
                $attacker['unit'][$unit['unit']] -= 1;
                $attacker['units']->remove($i);
                $i--;
            }
        }
    }
}

function shoot($attacker, $unit, &$defenders, &$ad, $sim, &$advancedStats)
{
    // SHOOT
    $CombatCaps =& Singleton()->CombatCaps;
    $pricelist =& Singleton()->pricelist;
    $count = 0;
    foreach ($defenders as $fID => &$defender) {
        $count += count($defender['units']);
    }
    $ran = rand(0, $count - 1);
    $count = 0;
    $victim = 0;
    $victimShip = 0;
    $initialArmor = 0;
    foreach ($defenders as $fID => &$defender) {
        $count += count($defender['units']);
        if ($ran < $count) {
            $victim = $defender['player']['id'];
            $victimShipId = rand(0, count($defender['units']) - 1);
            $victimShip = &$defender['units'][$victimShipId];
            $armorTech = (1 + (0.1 * $defender['player']['defence_tech']));
            $initialArmor = ($pricelist[$victimShip['unit']]['cost'][901]
                + $pricelist[$victimShip['unit']]['cost'][902]) / 10 * $armorTech;
            break;
        }
    }
    $ad['attack'] += $unit['att'];
    if ($unit['att'] * 100 > $victimShip['shield']) {
        $penetration = $unit['att'] - $victimShip['shield'];
        if ($penetration >= 0) {
            //+penetration
            $ad['shield'] += $victimShip['shield'];
            $victimShip['shield'] = 0;
            $victimShip['armor'] -= $penetration; // shoot at armor
        } else {
            //-penetration
            $ad['shield'] += $unit['att'];
            $victimShip['shield'] -= $unit['att']; // shoot at shield
        }

        //check destruction
        if (floor($victimShip['unit'] / 100) == 2 && !$victimShip['explode']) {
            if ($victimShip['armor'] > 0 && $victimShip['armor'] < 0.7 * $initialArmor) {
                $ran = rand(0, (int) $initialArmor);
                if ($ran > $victimShip['armor']) {
                    $victimShip['explode'] = true;
                    if (!isset($advancedStats[$attacker][$victim][$victimShip['unit']])) {
                        $advancedStats[$attacker][$victim][$victimShip['unit']] = 0;
                    }
                    $advancedStats[$attacker][$victim][$victimShip['unit']] += 1;
                }
            }
        }
        if ($victimShip['armor'] <= 0 && !$victimShip['explode']) {
            $victimShip['explode'] = true;
            if (!isset($advancedStats[$attacker][$victim][$victimShip['unit']])) {
                $advancedStats[$attacker][$victim][$victimShip['unit']] = 0;
            }
            $advancedStats[$attacker][$victim][$victimShip['unit']] += 1;
        }
    }
    // else bounced hit (Weaponry of the shooting unit is less than 1% of the Shielding of the target unit)

    // Rapid fire
    if (isset($CombatCaps[$unit['unit']]['sd'])) {
        foreach ($CombatCaps[$unit['unit']]['sd'] as $sdId => $count) {
            if ($victimShip['unit'] == $sdId) {
                $ran = rand(0, $count);
                if ($ran < $count) {
                    shoot($attacker, $unit, $defenders, $ad, $sim, $advancedStats);
                }
            }
        }
    }
}

function initCombatValues(&$fleets, $firstInit = false)
{
    // INIT COMBAT VALUES
    $CombatCaps =& Singleton()->CombatCaps;
    $pricelist =& Singleton()->pricelist;
    $attackAmount = ['total' => 0];
    $attArray = [];
    foreach ($fleets as $fleetID => $attacker) {
        $attackAmount[$fleetID] = 0;

        // init techs
        $attTech = (1 + (0.1 * $attacker['player']['military_tech']));
        $shieldTech = (1 + (0.1 * $attacker['player']['shield_tech']));
        $armorTech = (1 + (0.1 * $attacker['player']['defence_tech']));

        if ($firstInit) {
            $fleets[$fleetID]['techs'] = [$attTech, $shieldTech, $armorTech];
            $fleets[$fleetID]['units'] = new Ds\Vector(); // array();
        }

        $iter = 0;
        // init single ships
        foreach ($attacker['unit'] as $element => $amount) {
            // dont randomize +/-20% of attack power. The random factor is high enough
            $thisAtt = ($CombatCaps[$element]['attack']) * $attTech; // * (rand(80, 120) / 100);
            $thisShield = ($CombatCaps[$element]['shield']) * $shieldTech;
            $thisArmor = ($pricelist[$element]['cost'][901] + $pricelist[$element]['cost'][902]) / 10 * $armorTech;

            $attArray[$fleetID][$element]['def'] = 0;
            $attArray[$fleetID][$element]['shield'] = 0;
            $attArray[$fleetID][$element]['att'] = 0;
            for ($ship = 0; $ship < $amount; $ship++, $iter++) {
                if ($firstInit) {
                    // create new array for EACH ship
                    $fleets[$fleetID]['units'][] = [
                        'unit' => $element,
                        'shield' => $thisShield,
                        'armor' => $thisArmor,
                        'att' => $thisAtt,
                        'explode' => false
                    ];
                }
                $attArray[$fleetID][$element]['def'] += $fleets[$fleetID]['units'][$iter]['armor'];
                $attArray[$fleetID][$element]['shield'] += $thisShield;
                $attArray[$fleetID][$element]['att'] += $thisAtt;
            }

            $attackAmount[$fleetID] += $amount;
            $attackAmount['total'] += $amount;
        }
    }

    return [
        'attackAmount' => $attackAmount,
        'attArray' => $attArray
    ];
}

function restoreShields(&$fleets)
{
    $CombatCaps =& Singleton()->CombatCaps;
    foreach ($fleets as $fleetID => $attacker) {
        $shieldTech = (1 + (0.1 * $attacker['player']['shield_tech']));
        foreach ($attacker['units'] as $element => $unit) {
            $fleets[$fleetID]['units'][$element]['shield'] = ($CombatCaps[$unit['unit']]['shield']) * $shieldTech;
        }
    }
}

function calculateAttack(&$attackers, &$defenders, $FleetTF, $DefTF, $sim = false)
{
    $pricelist =& Singleton()->pricelist;
    $CombatCaps =& Singleton()->CombatCaps;
    $resource =& Singleton()->resource;
    $TRES = ['attacker' => 0, 'defender' => 0];
    $ARES = $DRES = ['metal' => 0, 'crystal' => 0];
    $ROUND = [];
    $RF = [];

    $attackAmount = [];
    $defenseAmount = [];

    // $STARTDEF - snapshot of defense amount. Needed for 70% restore
    $STARTDEF = [];

    // calculate attackers fleet metal+crystal value
    foreach ($attackers as $fleetID => $attacker) {
        foreach ($attacker['unit'] as $element => $amount) {
            $ARES['metal'] += $pricelist[$element]['cost'][901] * $amount;
            $ARES['crystal'] += $pricelist[$element]['cost'][902] * $amount;
        }
    }
    $TRES['attacker'] = $ARES['metal'] + $ARES['crystal'];

    //calculate defenders fleet metal+crystal value
    foreach ($defenders as $fleetID => $defender) {
        foreach ($defender['unit'] as $element => $amount) {
            if ($element < 300) {
                // ships
                $DRES['metal'] += $pricelist[$element]['cost'][901] * $amount;
                $DRES['crystal'] += $pricelist[$element]['cost'][902] * $amount;
            }

            if ($fleetID == 0) {
                if (!isset($STARTDEF[$element])) {
                    $STARTDEF[$element] = 0;
                }

                $STARTDEF[$element] += $amount;
            }

            $TRES['defender'] += $pricelist[$element]['cost'][901] * $amount;
            $TRES['defender'] += $pricelist[$element]['cost'][902] * $amount;
        }
    }

    //will also be filled during simulation to have a comparable ram usage. If sim crashes, the real fight will not work.
    $advancedStats = [];

    for ($ROUNDC = 0; $ROUNDC <= MAX_ATTACK_ROUNDS; $ROUNDC++) {
        $attArray = [];
        $defArray = [];

        $att = initCombatValues($attackers, $ROUNDC == 0);
        $def = initCombatValues($defenders, $ROUNDC == 0);

        $ROUND[$ROUNDC] = [
            'attackers' => $attackers,
            'defenders' => $defenders,
            'attackA' => $att['attackAmount'],
            'defenseA' => $def['attackAmount'],
            'infoA' => $att['attArray'],
            'infoD' => $def['attArray'],
        ];

        if ($att['attackAmount']['total'] > 0 && $def['attackAmount']['total'] > 0 && $ROUNDC < MAX_ATTACK_ROUNDS) {
            // FIGHT
            $fightResults = fight($attackers, $defenders, $sim, $advancedStats);

            destroy($attackers);
            destroy($defenders);

            restoreShields($attackers);
            restoreShields($defenders);

            $ROUND[$ROUNDC]['attack'] = $fightResults['attack'];
            $ROUND[$ROUNDC]['defense'] = $fightResults['defense'];
            $ROUND[$ROUNDC]['attackShield'] = $fightResults['attackShield'];
            $ROUND[$ROUNDC]['defShield'] = $fightResults['defShield'];
        } else {
            break;
        }
    }

    if (!$sim) {
        require_once 'includes/classes/class.MissionFunctions.php';
        foreach ($advancedStats as $attacker => $victims) {
            foreach ($victims as $victim => $ships) {
                foreach ($ships as $element => $amount) {
                    MissionFunctions::updateDestroyedAdvancedStats($attacker, $victim, $element, $amount);
                }
            }
        }
    }

    if ($att['attackAmount']['total'] <= 0 && $def['attackAmount']['total'] > 0) {
        $won = "r"; // defender
    } elseif ($att['attackAmount']['total'] > 0 && $def['attackAmount']['total'] <= 0) {
        $won = "a"; // attacker
    } else {
        $won = "w"; // draw
    }

    // CDR
    foreach ($attackers as $fleetID => $attacker) {                    // flotte attaquant en CDR
        foreach ($attacker['unit'] as $element => $amount) {
            $TRES['attacker'] -= $pricelist[$element]['cost'][901] * $amount;
            $TRES['attacker'] -= $pricelist[$element]['cost'][902] * $amount;

            $ARES['metal'] -= $pricelist[$element]['cost'][901] * $amount;
            $ARES['crystal'] -= $pricelist[$element]['cost'][902] * $amount;
        }
    }

    $DRESDefs = ['metal' => 0, 'crystal' => 0];

    // restore defense (70% +/- 20%)
    $repairedDef = [];
    // wreckfield ships
    $wreckfield = [];
    // wreckfield requirements
    $defendingPlayer = [
        'total' => 0,
        'lost' => 0,
    ];
    foreach ($defenders as $fleetID => $defender) {
        foreach ($defender['unit'] as $element => $amount) {
            if ($element < 300) {                           // flotte defenseur en CDR
                $DRES['metal'] -= $pricelist[$element]['cost'][901] * $amount;
                $DRES['crystal'] -= $pricelist[$element]['cost'][902] * $amount;

                $TRES['defender'] -= $pricelist[$element]['cost'][901] * $amount;
                $TRES['defender'] -= $pricelist[$element]['cost'][902] * $amount;

                if ($fleetID == 0 && isModuleAvailable(MODULE_REPAIR_DOCK) && $STARTDEF[$element] > $amount) {
                    $wreckfield[$element] = floor(($STARTDEF[$element] - $amount) * (1-($FleetTF / 100)));
                    $defendingPlayer['total'] += $pricelist[$element]['cost'][901] * $STARTDEF[$element];
                    $defendingPlayer['total'] += $pricelist[$element]['cost'][902] * $STARTDEF[$element];
                    $defendingPlayer['lost'] += $pricelist[$element]['cost'][901] * $amount;
                    $defendingPlayer['lost'] += $pricelist[$element]['cost'][902] * $amount;
                }
            } else {                                    // defs defenseur en CDR + reconstruction
                $TRES['defender'] -= $pricelist[$element]['cost'][901] * $amount;
                $TRES['defender'] -= $pricelist[$element]['cost'][902] * $amount;

                $lost = $STARTDEF[$element] - $amount;
                $giveback = 0;
                for ($i = 0; $i < $lost; $i++) {
                    if (rand(1, 100) <= 70) {
                        $giveback += 1;
                    }
                }
                $defenders[$fleetID]['unit'][$element] += $giveback;
                if ($lost > 0) {
                    $repairedDef[$element]['units'] = $giveback;
                    $repairedDef[$element]['percent'] = $giveback / $lost * 100;
                    if (!$sim) {
                        MissionFunctions::updateRepairedDefAdvancedStats($defender['player']['id'], $element, $giveback);
                    }
                }
                $DRESDefs['metal'] += $pricelist[$element]['cost'][901] * ($lost - $giveback);
                $DRESDefs['crystal'] += $pricelist[$element]['cost'][902] * ($lost - $giveback);
            }
        }
    }

    $ARES['metal'] = max($ARES['metal'], 0);
    $ARES['crystal'] = max($ARES['crystal'], 0);
    $DRES['metal'] = max($DRES['metal'], 0);
    $DRES['crystal'] = max($DRES['crystal'], 0);
    $TRES['attacker'] = max($TRES['attacker'], 0);
    $TRES['defender'] = max($TRES['defender'], 0);

    $totalLost = ['attacker' => $TRES['attacker'], 'defender' => $TRES['defender']];
    $debAttMet = ($ARES['metal'] * ($FleetTF / 100));
    $debAttCry = ($ARES['crystal'] * ($FleetTF / 100));
    $debDefMet = ($DRES['metal'] * ($FleetTF / 100)) + ($DRESDefs['metal'] * ($DefTF / 100));
    $debDefCry = ($DRES['crystal'] * ($FleetTF / 100)) + ($DRESDefs['crystal'] * ($DefTF / 100));

    //Repairable wreckfield only with min 150.000 lost units and min 5% lost fleet
    if ($defendingPlayer['lost'] < 150000 || $defendingPlayer['lost']/$defendingPlayer['total'] < 0.05) {
        $wreckfield = [];
    }

    return [
        'won' => $won,
        'debris' => [
            'attacker' => [
                901 => $debAttMet,
                902 => $debAttCry,
            ],
            'defender' => [
                901 => $debDefMet,
                902 => $debDefCry,
            ]
        ],
        'rw' => $ROUND,
        'unitLost' => $totalLost,
        'repaired' => $repairedDef,
        'wreckfield' => $wreckfield,
    ];
}
