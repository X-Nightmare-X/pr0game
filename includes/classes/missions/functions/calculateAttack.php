<?php

/*
 * Battle engines
 *
 * Use SteemNova if php-ds is installed, otherwise use SteamNova_Array
 * SteemNova's Battle Engine based on Arrays; Very slow, not recommended
 */

/*
 * DON'T MODIFY ↓
 */
if (extension_loaded('ds')) {
    include("SteemNova.php");
} else {
    include("SteemNova_Array.php");
}
