<?php

/*
 * Battle engines
 *
 * Use SteemNova_Julia if programming runtime Julia is available
 * Use SteemNova if php-ds is installed, otherwise use SteamNova_Array
 * SteemNova's Battle Engine based on Arrays; Very slow, not recommended
 */

/*
 * DON'T MODIFY ↓
 */
if (isJuliaRunning()) {
    include("SteemNova_Julia.php");
} else if (extension_loaded('ds')) {
    include("SteemNova.php");
} else {
    include("SteemNova_Array.php");
}
