<?php

/*
 * Battle engines
 *
 * 0 - Ogame probabilistic battle engine (Fast, stable, but buggy)
 * 1 - SteemNova; Slow, unstable on big battles, required custom PHP Extension)
 * 2 - Hybrid; Below 200000 ships SteemNova, Above OPBE
 * 999 - SteemNova's Battle Engine based on Arrays; Very slow, not recommended
 */
$battle_engine = 2;

/*
 * DON'T MODIFY ↓
 */
if ($battle_engine === 2) {
    if (extension_loaded('ds')) {
        include("SteemNova.php");
    } else {
        include("SteemNova_Array.php");
    }
} else if ($battle_engine === 1) {
    include("SteemNova.php");
} else if ($battle_engine === 999) {
    include("SteemNova_Array.php");
} else {
    include("OPBE.php");
}
