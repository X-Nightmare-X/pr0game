<?php

/**
 * pr0game powered by steemnova_julia
 * 
 * (c) 2024 reflexrecon/Hyman
 */

function calculateAttack(&$attackers, &$defenders, $FleetTF, $DefTF, $sim = false, $simTries=1) 
{
    $json = createJuliaJson($attackers, $defenders, $simTries=1);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    if ($simTries > 1) {
        curl_setopt($curl, CURLOPT_URL, "http://127.0.0.1:8101/battlesim");
    } else {
        curl_setopt($curl, CURLOPT_URL, "http://127.0.0.1:8100/battlesim");
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);

    return parseJuliaJson($result, $attackers, $defenders, $FleetTF, $DefTF, $sim);
}

function createJuliaJson(&$attackers, &$defenders, $simTries=1) : string
{
    $json='{ "attackers": [';
    foreach ($attackers as $fleetID => $attacker) {
        $json = $json.'{';
        $json = $json. '"fleetid": '.$fleetID.',';
        foreach ($attacker['unit'] as $unit => $amount) {
            $json = $json. '"'.$unit.'": '.$amount.',';
        }
        $json = $json. '"wtech": '.$attacker['player']['military_tech'].',';
        $json = $json. '"shieldtech": '.$attacker['player']['shield_tech'].',';
        $json = $json. '"armortech": '.$attacker['player']['defence_tech'].'';
        $json = $json. '},';
    }
    $json=substr($json, 0, -1);

    $json = $json. '], "defenders": [';

    foreach ($defenders as $fleetID => $defender) {
        $json = $json. '{';
        $json = $json. '"fleetid": '.$fleetID.',';
        foreach ($defender['unit'] as $unit => $amount) {
            $json = $json. '"'.$unit.'": '.$amount.',';
        }
        $json = $json. '"wtech": '.$defender['player']['military_tech'].',';
        $json = $json. '"shieldtech": '.$defender['player']['shield_tech'].',';
        $json = $json. '"armortech": '.$defender['player']['defence_tech'].'';
        $json = $json. '},';
    }
    $json=substr($json, 0, -1);
    $json = $json. '], "rounds": '.$simTries.'}';

    return $json;
}

function parseJuliaJson($json, &$attackers, &$defenders, $FleetTF, $DefTF, $sim)
{
    $pricelist =& Singleton()->pricelist;
    $TRES = ['attacker' => 0, 'defender' => 0];
    $ARES = $DRES = ['metal' => 0, 'crystal' => 0];

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

            $TRES['defender'] += $pricelist[$element]['cost'][901] * $amount;
            $TRES['defender'] += $pricelist[$element]['cost'][902] * $amount;
        }
    }

    $decoded = json_decode($json, true);

    switch ($decoded['outcome']) {
        case 1:
            $won = 'a';
            break;
        case -1:
            $won = 'r';
            break;
        case 0:
            $won = 'w';
            break;
    }

    $rounds = [];
    $att = initCombatValues($attackers);
    $def = initCombatValues($defenders);

    $rounds[0] = [
        'attackers' => $attackers,
        'defenders' => $defenders,
        'infoA' => $att,
        'infoD' => $def,
    ];

    foreach ($decoded['round_have'] as $roundCount => $roundDetails) {
        // Attacker
        foreach ($roundDetails[0] as $fleetID => $roundResult) {
            foreach ($attackers[$fleetID]['unit'] as $element => $amount) {
                if (!empty($roundResult[$element])) {
                    $details = $roundResult[$element];
                    $attackers[$fleetID]['unit'][$element] = $details['amount'];
                    $def[$fleetID][$element]['def'] = $details['hull'];
                    $def[$fleetID][$element]['shield'] = $details['shield'];
                    $def[$fleetID][$element]['att'] = $details['attack'];
                } else {
                    $attackers[$fleetID]['unit'][$element] = 0;
                }
            }
        }

        // Defender
        foreach ($roundDetails[1] as $fleetID => $roundResult) {
            foreach ($defenders[$fleetID]['unit'] as $element => $amount) {
                if (!empty($roundResult[$element])) {
                    $details = $roundResult[$element];
                    $defenders[$fleetID]['unit'][$element] = $details['amount'];
                    $def[$fleetID][$element]['def'] = $details['hull'];
                    $def[$fleetID][$element]['shield'] = $details['shield'];
                    $def[$fleetID][$element]['att'] = $details['attack'];
                } else {
                    $defenders[$fleetID]['unit'][$element] = 0;
                }
            }
        }

        $rounds[$roundCount]['attack'] = $decoded['round_lost'][$roundCount][0]['hulldamage'];
        $rounds[$roundCount]['defense'] = $decoded['round_lost'][$roundCount][1]['hulldamage'];
        $rounds[$roundCount]['attackShield'] = $decoded['round_lost'][$roundCount][1]['shielddamage'];
        $rounds[$roundCount]['defShield'] = $decoded['round_lost'][$roundCount][0]['shielddamage'];

        $rounds[$roundCount + 1] = [
            'attackers' => $attackers,
            'defenders' => $defenders,
            'infoA' => $att,
            'infoD' => $def,
        ];
    }

    if (!$sim) {
        require_once 'includes/classes/class.MissionFunctions.php';

        foreach ($decoded['destroyed_attacker'] as $fleetID => $destroyed) {
            foreach ($destroyed as $element => $amount) {
                if ($element > 0 && in_array($element, $pricelist) && $amount > 0) {
                    MissionFunctions::updateDestroyedAdvancedStats($attackers[$fleetID]['player']['id'], 0, $element, $amount);
                }
            }
        }
        foreach ($decoded['destroyed_defender'] as $fleetID => $destroyed) {
            foreach ($destroyed as $element => $amount) {
                if ($element > 0 && in_array($element, $pricelist) && $amount > 0) {
                    MissionFunctions::updateDestroyedAdvancedStats($attackers[$fleetID]['player']['id'], 0, $element, $amount);
                }
            }
        }
    }

    foreach ($rounds[0]['attackers'] as $fleetID => $attacker) {
        foreach ($attacker['unit'] as $element => $startAmmount) {
            $amount = $attackers[$fleetID]['unit'][$element];
            $lost = $startAmmount - $amount;
            if (!$sim && $lost > 0) {
                MissionFunctions::updateLostAdvancedStats($attacker['player']['id'], [$element => $lost]);
            }

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

    foreach ($rounds[0]['defenders'] as $fleetID => $defender) {
        foreach ($defender['unit'] as $element => $startAmmount) {
            $amount = $defenders[$fleetID]['unit'][$element];
            $lost = $startAmmount - $amount;
            if (!$sim && $lost > 0) {
                MissionFunctions::updateLostAdvancedStats($defender['player']['id'], [$element => $lost]);
            }

            if ($element < 300) {                           // flotte defenseur en CDR
                $DRES['metal'] -= $pricelist[$element]['cost'][901] * $amount;
                $DRES['crystal'] -= $pricelist[$element]['cost'][902] * $amount;

                $TRES['defender'] -= $pricelist[$element]['cost'][901] * $amount;
                $TRES['defender'] -= $pricelist[$element]['cost'][902] * $amount;

                if ($fleetID == 0 && isModuleAvailable(MODULE_REPAIR_DOCK) && $startAmmount > $amount) {
                    $wreckfield[$element] = floor($lost * (1-($FleetTF / 100)));
                    $defendingPlayer['total'] += $pricelist[$element]['cost'][901] * $startAmmount;
                    $defendingPlayer['total'] += $pricelist[$element]['cost'][902] * $startAmmount;
                    $defendingPlayer['lost'] += $pricelist[$element]['cost'][901] * $lost;
                    $defendingPlayer['lost'] += $pricelist[$element]['cost'][902] * $lost;
                }
            } else {                                    // defs defenseur en CDR + reconstruction
                $TRES['defender'] -= $pricelist[$element]['cost'][901] * $amount;
                $TRES['defender'] -= $pricelist[$element]['cost'][902] * $amount;

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
        'rw' => $rounds,
        'unitLost' => $totalLost,
        'repaired' => $repairedDef,
        'wreckfield' => $wreckfield,
    ];
}

function initCombatValues(&$fleets)
{
    // INIT COMBAT VALUES
    $CombatCaps =& Singleton()->CombatCaps;
    $pricelist =& Singleton()->pricelist;
    $attArray = [];
    foreach ($fleets as $fleetID => $attacker) {

        // init techs
        $attTech = (1 + (0.1 * $attacker['player']['military_tech']));
        $shieldTech = (1 + (0.1 * $attacker['player']['shield_tech']));
        $armorTech = (1 + (0.1 * $attacker['player']['defence_tech']));

        $fleets[$fleetID]['techs'] = [$attTech, $shieldTech, $armorTech];

        foreach ($attacker['unit'] as $element => $amount) {
            $attArray[$fleetID][$element]['def'] = $amount * ($pricelist[$element]['cost'][901] + $pricelist[$element]['cost'][902]) / 10 * $armorTech;
            $attArray[$fleetID][$element]['shield'] = $amount * ($CombatCaps[$element]['shield']) * $shieldTech;
            $attArray[$fleetID][$element]['att'] = $amount * ($CombatCaps[$element]['attack']) * $attTech;
        }
    }

    return $attArray;
}
