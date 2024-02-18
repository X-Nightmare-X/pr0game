<?php

/**
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Reflexrecon
 * @copyright 2023 Reflexrecon
 * @licence MIT
 * @version 1.8.0
 */


class ShowAdvancedStatsPage extends AbstractGamePage
{
    public function __construct()
    {
        parent::__construct();
    }

    private function checkAvailable($key)
    {
        $reslist =& Singleton()->reslist;
        $result = false;
        $id = substr($key, strpos($key, "_") + 1);

        if ((in_array($id, $reslist['fleet']) || in_array($id, $reslist['defense']) || in_array($id, $reslist['missile']) || 
            $id == RESOURCE_METAL || $id == RESOURCE_CRYSTAL || $id == RESOURCE_DEUT) &&
            BuildFunctions::isEnabled($id)
        ) {
            $result = true;
        }
        return $result;
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $db = Database::get();

        $sql = "SELECT *
		FROM %%ADVANCED_STATS%%
		WHERE userId = :id;";

        $advancedStats = $db->selectSingle($sql, [':id' => $USER['id']]);
        $build = [];
        $lost = [];
        $repaired = [];
        $destroyed = [];
        $expo = [];
        $rest = [];
        foreach ($advancedStats as $key => $value) {
            if ($key == 'userId') {
                continue;
            } else if (str_contains($key, 'build_')) {
                if ($this->checkAvailable($key)) {
                    $build[$key] = $value;
                }
                continue;
            } else if (str_contains($key, 'lost_')) {
                if ($this->checkAvailable($key)) {
                    $lost[$key] = $value;
                }
                continue;
            } else if (str_contains($key, 'repaired_')) {
                if ($this->checkAvailable($key)) {
                    $repaired[$key] = $value;
                }
                continue;
            } else if (str_contains($key, 'destroyed_')) {
                if ($this->checkAvailable($key)) {
                    $destroyed[$key] = $value;
                }
                continue;
            } else if (str_contains($key, 'found_')) {
                if ($this->checkAvailable($key)) {
                    $expo[$key] = $value;
                }
                continue;
            } else if (str_contains($key, 'expo_')) {
                $expo[$key] = $value;
                continue;
            } else {
                $rest[$key] = $value;
                continue;
            }

            $advancedStats[$key] = number_format($value, 2, ',', '.');
        }

        $this->assign([
            'build'     => $build,
            'lost'      => $lost,
            'repaired'  => $repaired,
            'destroyed' => $destroyed,
            'expo'      => $expo,
            'rest'      => $rest,
            'userId'    => $USER['id'],
        ]);

        $this->display('page.advancedStats.default.tpl');
    }
}
