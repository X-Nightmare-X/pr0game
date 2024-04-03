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

$planetData	= [
    1	=> ['temp' => mt_rand(220, 260), 'avgTemp' => 240,	'fields' => mt_rand(96, 172), 'avgFields' => 134,	'image' => ['trocken' => mt_rand(1, 10), 'wuesten' => mt_rand(1, 4)]],
    2	=> ['temp' => mt_rand(170, 210), 'avgTemp' => 190,	'fields' => mt_rand(104, 176), 'avgFields' => 140,	'image' => ['trocken' => mt_rand(1, 10), 'wuesten' => mt_rand(1, 4)]],
    3	=> ['temp' => mt_rand(120, 160), 'avgTemp' => 140,	'fields' => mt_rand(112, 182), 'avgFields' => 140,	'image' => ['trocken' => mt_rand(1, 10), 'wuesten' => mt_rand(1, 4)]],
    4	=> ['temp' => mt_rand(70, 110), 'avgTemp' => 90,	'fields' => mt_rand(118, 208), 'avgFields' => 163,	'image' => ['dschjungel' => mt_rand(1, 10)]],
    5	=> ['temp' => mt_rand(60, 100), 'avgTemp' => 80,	'fields' => mt_rand(133, 232), 'avgFields' => 182,	'image' => ['dschjungel' => mt_rand(1, 10)]],
    6	=> ['temp' => mt_rand(50, 90), 'avgTemp' => 70,	'fields' => mt_rand(146, 242), 'avgFields' => 194,	'image' => ['dschjungel' => mt_rand(1, 10)]],
    7	=> ['temp' => mt_rand(40, 80), 'avgTemp' => 60,	'fields' => mt_rand(152, 248), 'avgFields' => 200,	'image' => ['normaltemp' => mt_rand(1, 7)]],
    8	=> ['temp' => mt_rand(30, 70), 'avgTemp' => 50,	'fields' => mt_rand(156, 252), 'avgFields' => 204,	'image' => ['normaltemp' => mt_rand(1, 7)]],
    9	=> ['temp' => mt_rand(20, 60), 'avgTemp' => 40,	'fields' => mt_rand(150, 246), 'avgFields' => 198,	'image' => ['normaltemp' => mt_rand(1, 7), 'wasser' => mt_rand(1, 9)]],
    10	=> ['temp' => mt_rand(10, 50), 'avgTemp' => 30,	'fields' => mt_rand(142, 232), 'avgFields' => 187,	'image' => ['normaltemp' => mt_rand(1, 7), 'wasser' => mt_rand(1, 9)]],
    11	=> ['temp' => mt_rand(0, 40), 'avgTemp' => 20,		'fields' => mt_rand(136, 210), 'avgFields' => 173,	'image' => ['normaltemp' => mt_rand(1, 7), 'wasser' => mt_rand(1, 9)]],
    12	=> ['temp' => mt_rand(-10, 30), 'avgTemp' => 10,	'fields' => mt_rand(125, 186), 'avgFields' => 156,	'image' => ['normaltemp' => mt_rand(1, 7), 'wasser' => mt_rand(1, 9)]],
    13	=> ['temp' => mt_rand(-50, -10), 'avgTemp' => -30,	'fields' => mt_rand(114, 172), 'avgFields' => 143,	'image' => ['eis' => mt_rand(1, 10)]],
    14	=> ['temp' => mt_rand(-90, -50), 'avgTemp' => -70,	'fields' => mt_rand(100, 168), 'avgFields' => 134,	'image' => ['eis' => mt_rand(1, 10), 'gas' => mt_rand(1, 8)]],
    15	=> ['temp' => mt_rand(-130, -90), 'avgTemp' => -110,	'fields' => mt_rand(90, 164), 'avgFields' => 127,	'image' => ['eis' => mt_rand(1, 10), 'gas' => mt_rand(1, 8)]]
];

$increasedMinFieldsPlantetData = [
    1=> ['fields' => mt_rand(104, 172), 'avgFields' => 138, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 38, 'deuteriumBonusPercent' => 0],
    2=> ['fields' => mt_rand(112, 176), 'avgFields' => 144, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 26, 'deuteriumBonusPercent' => 0],
    3=> ['fields' => mt_rand(120, 182), 'avgFields' => 144, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 15, 'deuteriumBonusPercent' => 0],
    4=> ['fields' => mt_rand(126, 208), 'avgFields' => 167, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 8, 'deuteriumBonusPercent' => 0],
    5=> ['fields' => mt_rand(141, 232), 'avgFields' => 186, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 6, 'deuteriumBonusPercent' => 0],
    6=> ['fields' => mt_rand(154, 242), 'avgFields' => 198, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 4, 'deuteriumBonusPercent' => 0],
    7=> ['fields' => mt_rand(160, 248), 'avgFields' => 204, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 2, 'deuteriumBonusPercent' => 0],
    8=> ['fields' => mt_rand(164, 252), 'avgFields' => 208, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 0, 'deuteriumBonusPercent' => 0],
    9=> ['fields' => mt_rand(158, 246), 'avgFields' => 202, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 0, 'deuteriumBonusPercent' => 0],
    10=> ['fields' => mt_rand(150, 232), 'avgFields' => 191, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 0, 'deuteriumBonusPercent' => 0],
    11=> ['fields' => mt_rand(144, 210), 'avgFields' => 177, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 0, 'deuteriumBonusPercent' => 0],
    12=> ['fields' => mt_rand(133, 186), 'avgFields' => 160, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 0, 'deuteriumBonusPercent' => 0],
    13=> ['fields' => mt_rand(122, 172), 'avgFields' => 147, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 0, 'deuteriumBonusPercent' => 0],
    14=> ['fields' => mt_rand(108, 168), 'avgFields' => 138, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 0, 'deuteriumBonusPercent' => 0],
    15=> ['fields' => mt_rand(98, 164), 'avgFields' => 131, 'metalBonusPercent' => 0, 'crystalBonusPercent' => 0, 'deuteriumBonusPercent' => 0]
];

if (!function_exists('getAllPlanetPictures')) {
    function getAllPlanetPictures() : array {
        return ['trocken' => mt_rand(1, 10), 'wuesten' => mt_rand(1, 4), 'dschjungel' => mt_rand(1, 10), 'normaltemp' => mt_rand(1, 7), 'wasser' => mt_rand(1, 9), 'eis' => mt_rand(1, 10), 'gas' => mt_rand(1, 8)];
    }
}