<?php

/**
 * pr0game powered by steemnova
 * achievements
 * (c) 2022 reflexrecon
 */

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
            $fleets[$fleetID]['units'] = []; // array();
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
                        'explode' => false,
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

    return ['attackAmount' => $attackAmount, 'attArray' => $attArray];
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

function calculateAttack(&$attackers, &$defenders, $FleetTF, $DefTF, $sim = false, $simTries=1) 
{

    $json = createJuliaJson($attackers, $defenders, $simTries=1);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    if($simTries > 1) {
        curl_setopt($curl, CURLOPT_URL, "http://127.0.0.1:8081/battlesimmulti");
    } else {
        curl_setopt($curl, CURLOPT_URL, "http://127.0.0.1:8081/battlesim");
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);
    error_log(var_export($result, true));
    parseJuliaJson($result, $attackers, $defenders, $FleetTF, $DefTF);
    die();
    return $result;
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

function createJuliaJson(&$attackers, &$defenders, $simTries=1) : string
{
    $json='{ "attackers": [';
    foreach ($attackers as $attacker) {
        $json = $json.'{';
        $json = $json. '"name": "'.$attacker['player']['username'].'",';
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

    foreach ($defenders as $defender) {
        $json = $json. '{';
        $json = $json. '"name": "'.$defender['player']['username'].'",';
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

function parseJuliaJson($json, &$attackers, &$defenders, $FleetTF, $DefTF)
{
    $decoded = json_decode($json, true);
    error_log(var_export($decoded, true));

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
    $test='{ 
        "outcome": 1,
        "losses": [[{
                    "(-2, 1)": 16720,
                    "(-902, 1)": 36000,
                    "(-903, 1)": 0,
                    "(-901, 1)": 36000,
                    "(-3, 1)": 16720,
                    "(-1, 1)": 818,
                    "(-1, 2)": 10,
                    "(-2, 2)": 55,
                    "(-3, 2)": 55,
                    "(-902, 2)": 0,
                    "(-903, 2)": 0,
                    "(-901, 2)": 0
                }, {
                    "(-2, 1)": 1815,
                    "(-903, 1)": 0,
                    "(401, 1)": 16,
                    "(-901, 1)": 0,
                    "(203, 1)": 6,
                    "(-1, 1)": 684,
                    "(-902, 1)": 0,
                    "(-3, 1)": 1815
                }
            ], [{
                    "(-2, 1)": 3520,
                    "(-1, 2)": 0,
                    "(-902, 1)": 60000,
                    "(-2, 2)": 0,
                    "(-903, 1)": 0,
                    "(-901, 1)": 60000,
                    "(-902, 2)": 0,
                    "(-903, 2)": 0,
                    "(-901, 2)": 0,
                    "(-3, 1)": 20240,
                    "(-1, 1)": 282,
                    "(-3, 2)": 55
                }, {
                    "(-2, 1)": 374,
                    "(-903, 1)": 0,
                    "(401, 1)": 4,
                    "(-901, 1)": 0,
                    "(203, 1)": 4,
                    "(-1, 1)": 216,
                    "(-902, 1)": 0,
                    "(-3, 1)": 2189
                }
            ]]
    }
    ';
}
//attacker 1 30xer, attacker 2 1LJ, defender 1 10 GT 20 raks
//-1 = schildabsorb
//-2 = damage
//-3 = totaldamage
//outcome = 1 attacker wins(a), -1 defender wins(r), 0 = draw(w)
