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

$planetData	= array(
	1	=> array('temp' => mt_rand(220, 260),	'fields' => mt_rand(96, 172),	'image' => array('trocken' => mt_rand(1, 10), 'wuesten' => mt_rand(1, 4))),
	2	=> array('temp' => mt_rand(170, 210),	'fields' => mt_rand(104, 176),	'image' => array('trocken' => mt_rand(1, 10), 'wuesten' => mt_rand(1, 4))),
	3	=> array('temp' => mt_rand(120, 160),	'fields' => mt_rand(112, 182),	'image' => array('trocken' => mt_rand(1, 10), 'wuesten' => mt_rand(1, 4))),
	4	=> array('temp' => mt_rand(70, 110),	'fields' => mt_rand(118, 208),	'image' => array('dschjungel' => mt_rand(1, 10))),
	5	=> array('temp' => mt_rand(60, 100),	'fields' => mt_rand(133, 232),	'image' => array('dschjungel' => mt_rand(1, 10))),
	6	=> array('temp' => mt_rand(50, 90),		'fields' => mt_rand(146, 242),	'image' => array('dschjungel' => mt_rand(1, 10))),
	7	=> array('temp' => mt_rand(40, 80),		'fields' => mt_rand(152, 248),	'image' => array('normaltemp' => mt_rand(1, 7))),
	8	=> array('temp' => mt_rand(30, 70),		'fields' => mt_rand(156, 252),	'image' => array('normaltemp' => mt_rand(1, 7))),
	9	=> array('temp' => mt_rand(20, 60),		'fields' => mt_rand(150, 246),	'image' => array('normaltemp' => mt_rand(1, 7), 'wasser' => mt_rand(1, 9))),
	10	=> array('temp' => mt_rand(10, 50),		'fields' => mt_rand(142, 232),	'image' => array('normaltemp' => mt_rand(1, 7), 'wasser' => mt_rand(1, 9))),
	11	=> array('temp' => mt_rand(0, 40),		'fields' => mt_rand(136, 210),	'image' => array('normaltemp' => mt_rand(1, 7), 'wasser' => mt_rand(1, 9))),
	12	=> array('temp' => mt_rand(-10, 30),	'fields' => mt_rand(125, 186),	'image' => array('normaltemp' => mt_rand(1, 7), 'wasser' => mt_rand(1, 9))),
	13	=> array('temp' => mt_rand(-50, -10),	'fields' => mt_rand(114, 172),	'image' => array('eis' => mt_rand(1, 10))),
	14	=> array('temp' => mt_rand(-90, -50),	'fields' => mt_rand(100, 168),	'image' => array('eis' => mt_rand(1, 10))),
	15	=> array('temp' => mt_rand(-130, -90),	'fields' => mt_rand(90, 164),	'image' => array('eis' => mt_rand(1, 10)))
);