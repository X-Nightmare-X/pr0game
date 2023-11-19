<?php

function GenerateWreckField($planetID, $combatResult) {
    if (!isModuleAvailable(MODULE_REPAIR_DOCK) || !isset($combatResult['wreckfield']) || empty($combatResult['wreckfield'])) {
        return;
    }

    $db = Database::get();

    $sql = "SELECT * FROM %%PLANET_WRECKFIELD%% WHERE `planetID` = :planetID FOR UPDATE;";
    $wreckfield = $db->selectSingle($sql, [':planetID' => $planetID]);

    if (!empty($wreckfield)) {
        $fleetArray = FleetFunctions::unserialize($wreckfield['ships']);
        foreach ($combatResult['wreckfield'] as $elementID => $amount) {
            if ($amount > 0) {
                if (!isset($fleetArray[$elementID])) {
                    $fleetArray[$elementID] = $amount;
                } else {
                    $fleetArray[$elementID] += $amount;
                }
            }
        }
        $sql = "UPDATE %%PLANET_WRECKFIELD%%
            SET `created` = :created, `ships` = :shipArray
            WHERE `planetID` = :planetID;";
        $db->update($sql, [
            ':planetID' => $planetID,
            ':created' => TIMESTAMP,
            ':shipArray' => FleetFunctions::serialize($fleetArray),
        ]);
    }
    else {
        $sql = "INSERT INTO %%PLANET_WRECKFIELD%%
            SET `planetID` = :planetID, `created` = :created, `ships` = :shipArray;";
        $db->insert($sql, [
            ':planetID' => $planetID,
            ':created' => TIMESTAMP,
            ':shipArray' => FleetFunctions::serialize($combatResult['wreckfield']),
        ]);
    }


}
