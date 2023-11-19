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

function calculateSteal($attackFleets, $defenderPlanet, $simulate = false)
{
    // See: http://www.owiki.de/Beute
    $pricelist =& Singleton()->pricelist;
    $resource =& Singleton()->resource;
    $firstResource = 901;
    $secondResource = 902;
    $thirdResource = 903;

    $SortFleets = [];
    $capacity = 0;

    $stealResource = [
        $firstResource => 0,
        $secondResource => 0,
        $thirdResource => 0
    ];

    foreach ($attackFleets as $FleetID => $Attacker) {
        $SortFleets[$FleetID] = 0;

        foreach ($Attacker['unit'] as $Element => $amount) {
            if ($Element != 210) { //Exclude spy drone from raid capacity
                $SortFleets[$FleetID] += $pricelist[$Element]['capacity'] * $amount;
            }
        }

        $SortFleets[$FleetID] -= $Attacker['fleetDetail']['fleet_resource_metal'];
        $SortFleets[$FleetID] -= $Attacker['fleetDetail']['fleet_resource_crystal'];
        $SortFleets[$FleetID] -= $Attacker['fleetDetail']['fleet_resource_deuterium'];
        if ($SortFleets[$FleetID] < 0) {//if fleet is already full but spydrones are not factored in,
            $SortFleets[$FleetID] = 0; //capacity can be negative, resulting in negative steel numbers.
        }
        $capacity += $SortFleets[$FleetID];
    }

    $AllCapacity = $capacity;
    if ($AllCapacity <= 0) {
        return $stealResource;
    }

    $planetResource = [
        $firstResource => $defenderPlanet[$resource[$firstResource]],
        $secondResource => $defenderPlanet[$resource[$secondResource]],
        $thirdResource => $defenderPlanet[$resource[$thirdResource]]
    ];

    if (isModuleAvailable(MODULE_RESOURCE_STASH)) {
        // Up to 10% (1% per resource store level) of daily production is protected from stealing
        $daily = [
            $firstResource => $defenderPlanet['metal_perhour'] * TIME_24_HOURS,
            $secondResource => $defenderPlanet['crystal_perhour'] * TIME_24_HOURS,
            $thirdResource => $defenderPlanet['deuterium_perhour'] * TIME_24_HOURS
        ];
        $protected = [
            $firstResource => min(0.1, 0.01 * $defenderPlanet['metal_store']),
            $secondResource => min(0.1, 0.01 * $defenderPlanet['crystal_store']),
            $thirdResource => min(0.1, 0.01 * $defenderPlanet['deuterium_store'])
        ];
        $planetResource[$firstResource] = max(0, $planetResource[$firstResource] - $daily[$firstResource] * $protected[$firstResource]);
        $planetResource[$secondResource] = max(0, $planetResource[$secondResource] - $daily[$secondResource] * $protected[$secondResource]);
        $planetResource[$thirdResource] = max(0, $planetResource[$thirdResource] - $daily[$thirdResource] * $protected[$thirdResource]);
    }

    // Step 1
    $stealResource[$firstResource] = min($capacity / 3, $planetResource[$firstResource] / 2);
    $capacity -= $stealResource[$firstResource];

    // Step 2
    $stealResource[$secondResource] = min($capacity / 2, $planetResource[$secondResource] / 2);
    $capacity -= $stealResource[$secondResource];

    // Step 3
    $stealResource[$thirdResource] = min($capacity, $planetResource[$thirdResource] / 2);
    $capacity -= $stealResource[$thirdResource];

    // Step 4
    $oldMetalBooty = $stealResource[$firstResource];
    $stealResource[$firstResource] += min(
        $capacity / 2,
        $planetResource[$firstResource] / 2 - $stealResource[$firstResource]
    );
    $capacity -= $stealResource[$firstResource] - $oldMetalBooty;

    // Step 5
    $stealResource[$secondResource] += min(
        $capacity,
        $planetResource[$secondResource] / 2 - $stealResource[$secondResource]
    );

    $stealResource[$firstResource] = floor(max(0, $stealResource[$firstResource]));
    $stealResource[$secondResource] = floor(max(0, $stealResource[$secondResource]));
    $stealResource[$thirdResource] = floor(max(0, $stealResource[$thirdResource]));

    if ($simulate) {
        return $stealResource;
    }

    $db = Database::get();

    foreach ($SortFleets as $FleetID => $Capacity) {
        $slotFactor = $Capacity / $AllCapacity;

        $sql = "UPDATE %%FLEETS%% SET
		`fleet_resource_metal` = `fleet_resource_metal` + '"
            . ($stealResource[$firstResource] * $slotFactor) . "',
		`fleet_resource_crystal` = `fleet_resource_crystal` + '"
            . ($stealResource[$secondResource] * $slotFactor) . "',
		`fleet_resource_deuterium` = `fleet_resource_deuterium` + '"
            . ($stealResource[$thirdResource] * $slotFactor) . "'
		WHERE fleet_id = :fleetId;";

        $db->update($sql, [
            ':fleetId'  => $FleetID,
        ]);

        $sql = "UPDATE %%LOG_FLEETS%% SET
		`fleet_gained_metal` = `fleet_gained_metal` + '"
            . ($stealResource[$firstResource] * $slotFactor) . "',
		`fleet_gained_crystal` = `fleet_gained_crystal` + '"
            . ($stealResource[$secondResource] * $slotFactor) . "',
		`fleet_gained_deuterium` = `fleet_gained_deuterium` + '"
            . ($stealResource[$thirdResource] * $slotFactor) . "'
		WHERE fleet_id = :fleetId;";

        $db->update($sql, [
            ':fleetId'  => $FleetID,
        ]);
    }

    return $stealResource;
}
