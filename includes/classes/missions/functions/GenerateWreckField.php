<?php

function GenerateWreckField($planetID, $combatResult) {
    if (!isModuleAvailable(MODULE_REPAIR_DOCK) || !isset($combatResult['wreckfield']) || empty($combatResult['wreckfield'])) {
        return;
    }

    $db = Database::get();

    $sql = "SELECT * FROM %%PLANET_WRECKFIELD%% WHERE `planetID` = :planetID;";
    $wreckfield = $db->selectSingle($sql, [':planetID' => $planetID]);

    if (!empty($wreckfield)) {
        $fleetArray = FleetFunctions::unserialize($wreckfield['ships']);
        foreach ($fleetArray as $elementID => $amount) {
            if (!isset($combatResult['wreckfield'][$elementID])) {
                $combatResult['wreckfield'][$elementID] = $amount;
            } else {
                $combatResult['wreckfield'][$elementID] += $amount;
            }
        }
        $shipArray = '';
        foreach ($combatResult['wreckfield'] as $elementID => $amount) {
            $shipArray .= $elementID . ',' . floatToString($amount) . ';';
        }
        $sql = "UPDATE %%PLANET_WRECKFIELD%%
            SET `created` = :created, `ships` = :shipArray
            WHERE `planetID` = :planetID;";
        $db->update($sql, [
            ':planetID' => $planetID,
            ':created' => TIMESTAMP,
            ':shipArray' => substr($shipArray, 0, -1),
        ]);
    }
    else {
        $shipArray = '';
        foreach ($combatResult['wreckfield'] as $elementID => $amount) {
            $shipArray .= $elementID . ',' . floatToString($amount) . ';';
        }
        $sql = "INSERT INTO %%PLANET_WRECKFIELD%%
            SET `planetID` = :planetID, `created` = :created, `ships` = :shipArray;";
        $db->insert($sql, [
            ':planetID' => $planetID,
            ':created' => TIMESTAMP,
            ':shipArray' => substr($shipArray, 0, -1),
        ]);
    }


}
