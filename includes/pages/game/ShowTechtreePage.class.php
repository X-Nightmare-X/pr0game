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


class ShowTechtreePage extends AbstractGamePage
{
    public static $requireModule = MODULE_TECHTREE;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        $resource =& Singleton()->resource;
        $requeriments =& Singleton()->requeriments;
        $reslist =& Singleton()->reslist;
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        $elementIDs		= array_merge(
            [0],
            $reslist['build'],
            [100],
            $reslist['tech'],
            [200],
            $reslist['fleet'],
            [400],
            $reslist['defense'],
            [500],
            $reslist['missile'],
        );

        $techTreeList = [];
        $Messages		= $USER['messages'];
        foreach ($elementIDs as $elementId) {
            if (!BuildFunctions::isEnabled($elementId)) {
                continue;
            } elseif (!isset($resource[$elementId])) {
                $techTreeList[$elementId]	= $elementId;
            } else {
                $requirementsList	= [];
                if (isset($requeriments[$elementId])) {
                    foreach ($requeriments[$elementId] as $requireID => $RedCount) {
                        $requirementsList[$requireID]	= [
                            'count' => $RedCount,
                            'own'   => isset($PLANET[$resource[$requireID]]) ? $PLANET[$resource[$requireID]] : $USER[$resource[$requireID]]
                        ];
                    }
                }

                $techTreeList[$elementId]	= $requirementsList;
            }
        }

        $this->assign([
            'TechTreeList'		=> $techTreeList,
            'messages'			=> ($Messages > 0) ? (($Messages == 1) ? $LNG['ov_have_new_message'] : sprintf($LNG['ov_have_new_messages'], pretty_number($Messages))) : false,
        ]);

        $this->display('page.techTree.default.tpl');
    }
}
