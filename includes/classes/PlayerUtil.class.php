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

class PlayerUtil
{
    public static function cryptPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);
    }

    public static function isPositionFree($universe, $galaxy, $system, $position, $type = 1)
    {
        $db = Database::get();
        $sql = 'SELECT COUNT(*) as record
            FROM %%PLANETS%%
            WHERE `universe` = :universe
            AND `galaxy` = :galaxy
            AND `system` = :system
            AND `planet` = :position
            AND `planet_type` = :type;';

        $count = $db->selectSingle($sql, [
            ':universe' => $universe,
            ':galaxy'   => $galaxy,
            ':system'   => $system,
            ':position' => $position,
            ':type'     => $type,
        ], 'record');

        return $count == 0 && $position > 0 && $system > 0 && $galaxy > 0;
    }

    public static function isNameValid($name)
    {
        if (UTF8_SUPPORT) {
            return preg_match('/^[\p{L}\p{N}_\-. ]*$/u', $name);
        } else {
            return preg_match('/^[A-z0-9_\-. ]*$/', $name);
        }
    }

    public static function isMailValid($address)
    {
        if (function_exists('filter_var')) {
            return filter_var($address, FILTER_VALIDATE_EMAIL) !== false;
        } else {
            return preg_match('^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$', $address);
        }
    }

    public static function checkPosition($universe, $galaxy, $system, $position)
    {
        $config = Config::get($universe);

        return !(1 > $galaxy
            || 1 > $system
            || 1 > $position
            || $config->max_galaxy < $galaxy
            || $config->max_system < $system
            || $config->max_planets < $position);
    }

    public static function randomHP($universe)
    {
        $config = Config::get($universe);
        $db = Database::get();
        //get avg planets per system per galaxy
        $sql = 'SELECT tab.`galaxy`, (sum(tab.anz)/:maxSys) as AvgPlanetsPerSys FROM (
            SELECT p.`galaxy`, p.`system`, COUNT(p.`planet`) as anz
            FROM %%PLANETS%% p
            JOIN %%USERS%% u on u.`id` = p.`id_owner`
            WHERE `planet_type` = 1 AND p.`galaxy` <= :maxGala AND u.`onlinetime` >= ( :ts - :inactive ) AND p.`universe` = :universe
            GROUP BY p.`galaxy`, p.`system`
        ) as tab GROUP BY `galaxy` ORDER BY tab.`galaxy` ASC;';
        $result = $db->select($sql, [
            ':universe' => $universe,
            ':maxGala' => $config->max_galaxy,
            ':maxSys' => $config->max_system,
            ':ts' => TIMESTAMP,
            ':inactive' => INACTIVE,
        ]);

        $avgPlanetsPerGala = [];
        if (empty($result)) {
            for ($i = 1; $i <= $config->max_galaxy; $i++) {
                $avgPlanetsPerGala[] = ['galaxy' => $i, 'AvgPlanetsPerSys' => 0];
            }
        } else {
            $i = 1;
            foreach ($result as $resultArray) {
                while ($i < $resultArray['galaxy']) {
                    $avgPlanetsPerGala[] = ['galaxy' => $i, 'AvgPlanetsPerSys' => 0];
                    $i ++;
                }
                $avgPlanetsPerGala[] = $resultArray;
                $i ++;
            }
            for (; $i <= $config->max_galaxy; $i++) {
                $avgPlanetsPerGala[] = ['galaxy' => $i, 'AvgPlanetsPerSys' => 0];
            }
        }

        // get gala with min avg systems
        $minAvg = $config->max_planets;
        $galaxy = 0;
        $galaArray = [];
        foreach ($avgPlanetsPerGala as $data) {
            if ($data['AvgPlanetsPerSys'] < $minAvg) {
                $minAvg = $data['AvgPlanetsPerSys'];
            }
        }
        foreach ($avgPlanetsPerGala as $data) {
            if ($data['AvgPlanetsPerSys'] == $minAvg) {
                array_push($galaArray, $data['galaxy']);
            }
        }
        $galaxy = $galaArray[rand(0, count($galaArray) - 1)];

        // get system with planet count for selected gala
        $sql = 'SELECT `system`, count(`planet`) as anz FROM %%PLANETS%%
            WHERE `planet_type` = 1 AND `galaxy` = :gala AND `universe` = :universe GROUP BY `system`;';
        $result = $db->select($sql, [
            ':gala' => $galaxy,
            ':universe' => $universe,
        ]);

        $systems = [];
        if (empty($result)) {
            for ($i = 1; $i <= $config->max_system; $i++) {
                $systems[] = ['system' => $i, 'anz' => 0];
            }
        } else {
            $i = 1;
            foreach ($result as $resultArray) {
                while ($i < $resultArray['system']) {
                    $systems[] = ['system' => $i, 'anz' => 0];
                    $i ++;
                }
                $systems[] = $resultArray;
                $i ++;
            }
            for (; $i <= $config->max_system; $i++) {
                $systems[] = ['system' => $i, 'anz' => 0];
            }
        }

        // get empty systems in selected gala
        for ($planetamount = 0; $planetamount <= $config->max_planets; $planetamount++) {
            $usableSystems = [];
            foreach ($systems as $sysArray) {
                if ($sysArray['anz'] == $planetamount) {
                    $usableSystems[] = $sysArray['system'];
                }
            }

            $position = 0;
            $system = 0;

            // find random system and random planet inside
            if (!empty($usableSystems)) {
                do {
                    $system = $usableSystems[array_rand($usableSystems)];
                    do {
                        $position = mt_rand(round($config->max_planets * 0.2), round($config->max_planets * 0.8));
                    } while (!PlayerUtil::allowPlanetPosition($position, null, $universe));
                } while (self::isPositionFree($universe, $galaxy, $system, $position) === false);
            }
            if (PlayerUtil::allowPlanetPosition($position, null, $universe) && self::isPositionFree($universe, $galaxy, $system, $position)) {
                break;
            }
        }
        $pos = [
            'galaxy'   => $galaxy,
            'system'   => $system,
            'position' => $position,
        ];
        return $pos;
    }

    public static function blockHP($universe)
    {
        $config = Config::get($universe);
        $db = Database::get();

        //if less than 50 planets, place randomly
        $sql = 'SELECT Count(`id`) AS planetcount FROM %%PLANETS%% WHERE `universe` = :universe;';
        $planetcount = $db->selectSingle($sql, [
            ':universe' => $universe,
        ], 'planetcount');
        if ($planetcount < 50) {
            $pos = self::randomHP($universe);
            return $pos;
        }

        $minPos = round($config->max_planets * 0.2);
        $maxPos = round($config->max_planets * 0.8);
        $maxPlanets = $maxPos - $minPos;

        //Search for systems with one planet, if none, search for 2 planets
        for ($planetamount = 1; $planetamount < 3; $planetamount++) {
            $sql = 'SELECT p.`galaxy`, p.`system` FROM
                %%PLANETS%% p
                JOIN (
                        SELECT `galaxy`, `system`
                        FROM %%PLANETS%%
                        WHERE `planet_type` = 1
                        AND `universe` = :universe
                        AND `galaxy` <= :maxGala
                        AND `planet` >= :minPos
                        AND `planet` <= :maxPos
                        GROUP BY `galaxy`, `system`
                        HAVING COUNT(`planet`) < :maxPlanets
                    ) as tab ON tab.`galaxy` = p.`galaxy` AND tab.`system` = p.`system`
                JOIN %%USERS%% u on u.`id` = p.`id_owner`
                WHERE p.`planet_type` = 1 AND u.`onlinetime` >= ( :ts - :inactive ) AND p.`universe` = :universe
                GROUP BY p.`galaxy`, p.`system`
                HAVING COUNT(p.`planet`) < :planetamount;';
            $systems = $db->select($sql, [
                ':universe' => $universe,
                ':maxGala' => $config->max_galaxy,
                ':minPos' => $minPos,
                ':maxPos' => $maxPos,
                ':maxPlanets' => $maxPlanets,
                ':planetamount' => $planetamount,
                ':ts' => TIMESTAMP,
                ':inactive' => INACTIVE,
            ]);

            if (!empty($systems)) {
                break;
            }
        }

        // if systems found, place planet
        if (!empty($systems)) {
            do {
                $galasys = $systems[array_rand($systems)];
                $galaxy = $galasys['galaxy'];
                $system = $galasys['system'];
                do {
                    $position = mt_rand($minPos, $maxPos);
                } while (!PlayerUtil::allowPlanetPosition($position, null, $universe));
            } while (self::isPositionFree($universe, $galaxy, $system, $position) === false);
            $pos = [
                'galaxy'   => $galaxy,
                'system'   => $system,
                'position'  => $position,
            ];
            return $pos;
        }

        // else place random planet
        $pos = self::randomHP($universe);
        return $pos;
    }

    public static function linearHP($universe, $config){
        $galaxy = $config->last_galaxy_pos;
            $system = $config->last_system_pos;
            $position = $config->last_planet_pos;

            if ($galaxy > $config->max_galaxy) {
                $galaxy = 1;
            }

            if ($system > $config->max_system) {
                $system = 1;
            }

            do {
                $position = mt_rand(round($config->max_planets * 0.2), round($config->max_planets * 0.8));
                if ($position < 3) {
                    $position += 1;
                } else {
                    if ($system >= $config->max_system) {
                        $system = 1;
                        if ($galaxy >= $config->max_galaxy) {
                            $galaxy = 1;
                        } else {
                            $galaxy += 1;
                        }
                    } else {
                        $system += 1;
                    }
                }
            } while (self::isPositionFree($universe, $galaxy, $system, $position) === false);

            // Update last coordinates to config table
            $config->last_galaxy_pos = $galaxy;
            $config->last_system_pos = $system;
            $config->last_planet_pos = $position;

            $pos = [
                'galaxy'   => $galaxy,
                'system'   => $system,
                'position' => $position,
            ];
            return $pos;
    }

    public static function createPlayer(
        $universe,
        $userName,
        $userPassword,
        $userMail,
        $userLanguage = null,
        $galaxy = null,
        $system = null,
        $position = null,
        $name = null,
        $authlevel = 0,
        $userIpAddress = null
    ) {
        $config = Config::get($universe);
        $db = Database::get();

        if (isset($universe, $galaxy, $system, $position)) {
            $pos = [
                'galaxy'   => $galaxy,
                'system'   => $system,
                'position' => $position,
            ];
        } elseif ($config->planet_creation == 1) {
            $pos = PlayerUtil::randomHP($universe);
        } elseif ($config->planet_creation == 2) {
            $rand_vs_block = rand(0, 1);
            switch ($rand_vs_block) {
                case 0:
                    $pos = PlayerUtil::randomHP($universe);
                    break;
                case 1:
                    $pos = PlayerUtil::blockHP($universe);
                    break;
            }
        } else {
            $pos = PlayerUtil::linearHP($universe, $config);
        }

        if (isset($universe, $galaxy, $system, $position)) {
            if (self::checkPosition($universe, $galaxy, $system, $position) === false) {
                throw new Exception(
                    sprintf("Try to create a planet at position: %s:%s:%s!", $galaxy, $system, $position)
                );
            }

            if (self::isPositionFree($universe, $galaxy, $system, $position) === false) {
                throw new Exception(
                    sprintf("Position is not empty: %s:%s:%s!", $galaxy, $system, $position)
                );
            }
        }

        $params = [
            ':username'          => $userName,
            ':email'             => $userMail,
            ':email2'            => $userMail,
            ':authlevel'         => $authlevel,
            ':universe'          => $universe,
            ':language'          => $userLanguage,
            ':registerAddress'   => !empty($userIpAddress) ? $userIpAddress : Session::getClientIp(),
            ':onlinetime'        => TIMESTAMP,
            ':registerTimestamp' => TIMESTAMP,
            ':password'          => $userPassword,
            ':dpath'             => DEFAULT_THEME,
            ':timezone'          => $config->timezone,
            ':nameLastChanged'   => 0,
        ];

        $sql = 'INSERT INTO %%USERS%% SET
            `username`		= :username,
            `email`			= :email,
            `email_2`		= :email2,
            `authlevel`		= :authlevel,
            `universe`		= :universe,
            `lang`			= :language,
            `ip_at_reg`		= :registerAddress,
            `onlinetime`	= :onlinetime,
            `register_time` = :registerTimestamp,
            `password`		= :password,
            `dpath`			= :dpath,
            `timezone`		= :timezone,
            `uctime`		= :nameLastChanged;';

        $db->insert($sql, $params);

        // $userId = $db->selectSingle('SELECT `id` FROM %%USERS%% WHERE `username` = :username;', [':username' => $userName], 'id');

        $userId = $db->lastInsertId();

        $planetId = self::createPlanet(
            $pos['galaxy'],
            $pos['system'],
            $pos['position'],
            $universe,
            $userId,
            $name,
            true,
            $authlevel
        );

        $currentUserAmount = $config->users_amount + 1;
        $config->users_amount = $currentUserAmount;

        $sql = 'UPDATE %%USERS%% SET
            `galaxy` = :galaxy,
            `system` = :system,
            `planet` = :position,
            `id_planet` = :planetId
            WHERE `id` = :userId;';

        $db->update($sql, [
            ':galaxy'   => $pos['galaxy'],
            ':system'   => $pos['system'],
            ':position' => $pos['position'],
            ':planetId' => $planetId,
            ':userId'   => $userId,
        ]);

        $sql = 'SELECT MAX(`total_rank`) as `rank` FROM %%STATPOINTS%% WHERE `universe` = :universe AND `stat_type` = :type;';
        $rank = $db->selectSingle($sql, [
            ':universe' => $universe,
            ':type'     => 1,
        ], 'rank');

        $sql = "INSERT INTO %%STATPOINTS%% SET
            `id_owner`   = :userId,
            `universe`   = :universe,
            `stat_type`  = :type,
            `tech_rank`  = :rank,
            `build_rank` = :rank,
            `defs_rank`	 = :rank,
            `fleet_rank` = :rank,
            `total_rank` = :rank;";

        $db->insert($sql, [
           ':universe'  => $universe,
           ':userId'    => $userId,
           ':type'      => 1,
           ':rank'      => $rank + 1,
        ]);

        $sql = 'INSERT INTO %%ADVANCED_STATS%% SET `userId` = :userId;';
        $db->insert($sql, [
            ':userId'    => $userId,
        ]);

        $config->save();

        return [
            $userId,
            $planetId
        ];
    }

    public static function createPlanet(
        $galaxy,
        $system,
        $position,
        $universe,
        $userId,
        $name = null,
        $isHome = false,
        $authlevel = 0
    ) {
        $LNG =& Singleton()->LNG;

        if (self::checkPosition($universe, $galaxy, $system, $position) === false) {
            throw new Exception(
                sprintf("Try to create a planet at position: %s:%s:%s!", $galaxy, $system, $position)
            );
        }

        if (self::isPositionFree($universe, $galaxy, $system, $position) === false) {
            throw new Exception(
                sprintf("Position is not empty: %s:%s:%s!", $galaxy, $system, $position)
            );
        }
        $db = Database::get();

        $sql = 'SELECT count(`id`) as anz FROM %%PLANETS%% WHERE `id_owner` = :userId AND `planet_type` = 1';
        $planetArray = $db->selectSingle($sql, [
            ':userId' => $userId,
        ]);

        $planetData = [];
        require 'includes/PlanetData.php';

        $config = Config::get($universe);
        $positionBasedRessourceBonus = $config->planet_ressource_bonus;

        $dataIndex = (int) ceil($position / ($config->max_planets / count($planetData)));
        if ($isHome) {
            $maxFields = $config->initial_fields;
            $maxTemperature = $config->initial_temp;
            $metal_bonus_percent = 5;
            $crystal_bonus_percent = 0;
            $deuterium_bonus_percent = 0;
        } elseif (!empty($planetArray) && $planetArray['anz'] == 1) {
            if ($positionBasedRessourceBonus){
                $maxFields = (int) floor( ($increasedMinFieldsPlantetData[$dataIndex]['avgFields'] ) * $config->planet_size_factor);
            } else {
                $maxFields = (int) floor($planetData[$dataIndex]['avgFields'] * $config->planet_size_factor);
            }
            $maxTemperature = $planetData[$dataIndex]['avgTemp'];
        } else {
            if ($positionBasedRessourceBonus){
                $maxFields = (int) floor($increasedMinFieldsPlantetData[$dataIndex]['fields'] * $config->planet_size_factor); 
            } else {
                $maxFields = (int) floor($planetData[$dataIndex]['fields'] * $config->planet_size_factor); 
            }
            $maxTemperature = $planetData[$dataIndex]['temp'];
        }

        require_once 'includes/classes/achievements/PlayerUtilAchievement.class.php';
        $sql = 'SELECT `galaxy` FROM %%USERS%% WHERE `id` = :userId;';
        $hpGala = $db->selectSingle($sql, [
            ':userId' => $userId,
        ], 'galaxy');
        PlayerUtilAchievement::checkPlayerUtilAchievements40($isHome, $hpGala, $userId, $galaxy, $config);
        $minTemperature = $maxTemperature - 40;

        $diameter = (int) floor(1000 * sqrt($maxFields));

        $sql = 'SELECT `image` FROM %%PLANETS%% WHERE `id_owner` = :userId; and `destruyed` = 0';
        $planetPicturesAchieved = $db->select($sql, [
            ':userId' => $userId,
        ]);

        $planetPictures = [];
        foreach ($planetPicturesAchieved as $image => $value) {
            array_push( $planetPictures, $value["image"]);
        }

        $enabledAllPlanetPictures = $config->all_planet_pictures;
        $maxImages = 0;
        do {
            if ($enabledAllPlanetPictures){
                $planetData[$dataIndex]['image'] = getAllPlanetPictures();
            }
            $imageNames = array_keys($planetData[$dataIndex]['image']);
            $imageNameType = $imageNames[array_rand($imageNames)];
            $imageName = $imageNameType;
            $imageName .= 'planet';
            $imageName .= $planetData[$dataIndex]['image'][$imageNameType] < 10 ? '0' : '';
            $imageName .= $planetData[$dataIndex]['image'][$imageNameType];
            $maxImages++;
        } while (in_array($imageName, $planetPictures) && ($maxImages < MAXPLANETPICTURECOUNT) && ($enabledAllPlanetPictures));

        if (empty($name)) {
            $name = $isHome ? $LNG['fcm_mainplanet'] : $LNG['fcp_colony'];
        }

        $params = [
            ':name'             => $name,
            ':universe'         => $universe,
            ':userId'           => $userId,
            ':galaxy'           => $galaxy,
            ':system'           => $system,
            ':position'         => $position,
            ':updateTimestamp'  => TIMESTAMP,
            ':type'             => 1,
            ':imageName'        => $imageName,
            ':diameter'         => $diameter,
            ':maxFields'        => $maxFields,
            ':minTemperature'   => $minTemperature,
            ':maxTemperature'   => $maxTemperature,
            ':metal_start'      => $config->metal_start,
            ':crystal_start'    => $config->crystal_start,
            ':deuterium_start'  => $config->deuterium_start,
            ':creation_time'    => TIMESTAMP,
        ];

        $sql = 'INSERT INTO %%PLANETS%% SET
            `name`		    = :name,
            `universe`      = :universe,
            `id_owner`      = :userId,
            `galaxy`		= :galaxy,
            `system`		= :system,
            `planet`		= :position,
            `last_update`   = :updateTimestamp,
            `planet_type`   = :type,
            `image`		    = :imageName,
            `diameter`      = :diameter,
            `field_max`     = :maxFields,
            `temp_min` 	    = :minTemperature,
            `temp_max` 	    = :maxTemperature,
            `metal`		    = :metal_start,
            `crystal`		= :crystal_start,
            `deuterium`     = :deuterium_start,
            `creation_time` = :creation_time;';

        $db->insert($sql, $params);

        // $sql = 'SELECT `id` FROM %%PLANETS%% WHERE
        // `universe` = :universe AND
        // `id_owner` = :userId AND
        // `galaxy` = :galaxy AND
        // `system` = :system AND
        // `planet` = :position AND
        // `planet_type` = 1;';

        // $planetId = $db->selectSingle($sql, [
        //     ':universe' => $universe,
        //     ':userId' => $userId,
        //     ':galaxy' => $galaxy,
        //     ':system' => $system,
        //     ':position' => $position,
        // ], 'id');

        $planetId = $db->lastInsertId();

        return $planetId;
    }

    public static function createMoon(
        $universe,
        $galaxy,
        $system,
        $position,
        $userId,
        $chance,
        $diameter = null,
        $moonName = null
    ) {
        $LNG =& Singleton()->LNG;

        $db = Database::get();

        $sql = 'SELECT `id_luna`, `planet_type`, `id`, `name`, `temp_max`, `temp_min`
            FROM %%PLANETS%%
            WHERE `universe` = :universe
            AND `galaxy` = :galaxy
            AND `system` = :system
            AND `planet` = :position
            AND `planet_type` = :type;';

        $parentPlanet = $db->selectSingle($sql, [
            ':universe' => $universe,
            ':galaxy'   => $galaxy,
            ':system'   => $system,
            ':position' => $position,
            ':type'     => 1,
        ]);

        if ($parentPlanet['id_luna'] != 0) {
            return false;
        }

        $config = Config::get($universe);
        $moon_size_factor = $config->moon_size_factor;

        if (is_null($diameter)) {
            $diameter = floor(pow(mt_rand(10, 20) + 3 * $chance, 0.5) * 1000 * $moon_size_factor);
        }

        $maxTemperature = $parentPlanet['temp_max'] - mt_rand(10, 45);
        $minTemperature = $parentPlanet['temp_min'] - mt_rand(10, 45);

        if (empty($moonName)) {
            $moonName = $LNG['type_planet_3'] ?? 'Mond';
        }

        $sql = 'INSERT INTO %%PLANETS%% SET
            `name`				= :name,
            `id_owner`			= :owner,
            `universe`			= :universe,
            `galaxy`			= :galaxy,
            `system`			= :system,
            `planet`			= :planet,
            `last_update`		= :updateTimestamp,
            `planet_type`		= :type,
            `image`				= :image,
            `diameter`			= :diameter,
            `field_max`			= :fields,
            `temp_min`			= :minTemperature,
            `temp_max`			= :maxTemperature,
            `metal`				= :metal,
            `metal_perhour`		= :metPerHour,
            `crystal`			= :crystal,
            `crystal_perhour`	= :cryPerHour,
            `deuterium`			= :deuterium,
            `deuterium_perhour` = :deuPerHour;';

        $db->insert($sql, [
            ':name'             => $moonName,
            ':owner'            => $userId,
            ':universe'         => $universe,
            ':galaxy'           => $galaxy,
            ':system'           => $system,
            ':planet'           => $position,
            ':updateTimestamp'  => TIMESTAMP,
            ':type'             => 3,
            ':image'            => 'mond0'.rand(1,9),
            ':diameter'         => $diameter,
            ':fields'           => 1,
            ':minTemperature'   => $minTemperature,
            ':maxTemperature'   => $maxTemperature,
            ':metal'            => 0,
            ':metPerHour'       => 0,
            ':crystal'          => 0,
            ':cryPerHour'       => 0,
            ':deuterium'        => 0,
            ':deuPerHour'       => 0,
        ]);

        // $sql = 'SELECT `id` FROM %%PLANETS%% WHERE
        // `universe` = :universe AND
        // `id_owner` = :userId AND
        // `galaxy` = :galaxy AND
        // `system` = :system AND
        // `planet` = :position AND
        // `planet_type` = 3;';

        // $moonId = $db->selectSingle($sql, [
        //     ':universe' => $universe,
        //     ':userId' => $userId,
        //     ':galaxy' => $galaxy,
        //     ':system' => $system,
        //     ':position' => $position,
        // ], 'id');

        $moonId = $db->lastInsertId();

        $sql = 'UPDATE %%PLANETS%% SET `id_luna` = :moonId WHERE `id` = :planetId;';

        $db->update($sql, [
            ':moonId'   => $moonId,
            ':planetId' => $parentPlanet['id'],
        ]);

        return $moonId;
    }
    public static function removeFromAlliance($userData)
    {
        $db = Database::get();
        if (!empty($userData['ally_id'])) {
            $sql = 'SELECT `ally_owner`, `ally_members` FROM %%ALLIANCE%% WHERE `id` = :allianceId;';
            $allyData = $db->selectSingle($sql, [
                ':allianceId'   => $userData['ally_id']
            ]);

            if ($allyData['ally_members'] > 1) {
                if ($allyData['ally_owner'] == $userData['id']) { // Removed player is ally leader
                    // Find new leader by respecting rank settings
                    $sql = 'SELECT u.`id` FROM %%ALLIANCE_RANK%% r
                        JOIN %%USERS%% u ON u.`ally_rank_id` = r.`rankID`
                        WHERE r.`allianceID` = :allianceId
                          AND u.`onlinetime` < :onlinetime
                          AND ( r.`transfer` = 1 OR r.`admin` = 1 OR r.`kick` = 1 OR r.`diplomatic` = 1 OR r.`manageusers` = 1 OR r.`ranks` = 1 )
                          ORDER BY r.`transfer` DESC, r.`admin` DESC, r.`kick` DESC, r.`diplomatic` DESC, r.`manageusers` DESC, r.`ranks` DESC, u.`onlinetime` DESC
                          LIMIT 1;';
                    $newLeaders = $db->select($sql, [
                        ':allianceId'   => $userData['ally_id'],
                        ':onlinetime'   => TIMESTAMP - TIME_72_HOURS,
                    ]);

                    $newLeader = 0;
                    if (!empty($newLeaders)) { // New leader by rank found?
                        $newLeader = $newLeaders[0]['id'];
                    } else {
                        // Find new leader by onlinetime
                        $sql = 'SELECT u.`id` FROM %%ALLIANCE_RANK%% r
                            JOIN %%USERS%% u ON u.`ally_rank_id` = r.`rankID`
                            WHERE r.`allianceID` = :allianceId
                            ORDER BY u.`onlinetime` DESC;';
                        $newLeader = $db->selectSingle($sql, [
                            ':allianceId'   => $userData['ally_id'],
                        ], 'id');
                    }

                    if (!empty($newLeader)) { // New leader found?
                        // Update rank of new leader
                        $sql = 'UPDATE %%USERS%% SET `ally_rank_id` = 0 WHERE `id` = :UserID;';
                        $db->update($sql, [
                            ':UserID' => $newLeader,
                        ]);

                        // Update ally_owner to new leader and reduce member amount
                        $sql = 'UPDATE %%ALLIANCE%% SET `ally_owner` = :UserID, `ally_members` = `ally_members` - 1 WHERE `id` = :allianceId;';
                        $db->update($sql, [
                            ':allianceId'   => $userData['ally_id'],
                            ':UserID' => $newLeader,
                        ]);
                    }
                    else {
                        // No new leader found, just reduce member amount
                        $sql = 'UPDATE %%ALLIANCE%% SET `ally_members` = `ally_members` - 1 WHERE `id` = :allianceId;';
                        $db->update($sql, [
                            ':allianceId'   => $userData['ally_id']
                        ]);
                    }
                } else {
                    // Removed player is not leader, just reduce member amount
                    $sql = 'UPDATE %%ALLIANCE%% SET `ally_members` = `ally_members` - 1 WHERE `id` = :allianceId;';
                    $db->update($sql, [
                        ':allianceId'   => $userData['ally_id']
                    ]);
                }
            } else {
                // Delete alliance, since there are no members
                $sql = 'DELETE FROM %%ALLIANCE%% WHERE `id` = :allianceId;';
                $db->delete($sql, [
                    ':allianceId'   => $userData['ally_id']
                ]);

                $sql = 'DELETE FROM %%ALLIANCE_RANK%% WHERE `allianceID` = :allianceId;';
                $db->delete($sql, [
                    ':allianceId'   => $userData['ally_id']
                ]);

                $sql = 'DELETE FROM %%STATPOINTS%% WHERE `stat_type` = :type AND `id_owner` = :allianceId;';
                $db->delete($sql, [
                    ':allianceId'   => $userData['ally_id'],
                    ':type'         => 2
                ]);

                $sql = 'UPDATE %%STATPOINTS%% SET `id_ally` = :resetId WHERE `id_ally` = :allianceId;';
                $db->update($sql, [
                    ':allianceId'   => $userData['ally_id'],
                    ':resetId'      => 0
                ]);
            }

            $sql = 'UPDATE %%USERS%% SET `ally_id` = 0, `ally_register_time` = 0, `ally_rank_id` = 0 WHERE `id` = :UserID;';
            $db->update($sql, [
                ':UserID' => $userData['id']
            ]);

            $sql = 'UPDATE %%STATPOINTS%% SET `id_ally` = :resetId WHERE `stat_type` = :type AND `id_owner` = :UserID;';
            $db->update($sql, [
                ':UserID'   => $userData['id'],
                ':resetId'  => 0,
                ':type'     => 1,
            ]);
        }

        $sql = 'DELETE FROM %%ALLIANCE_REQUEST%% WHERE `userID` = :userId;';
        $db->delete($sql, [
            ':userId'   => $userData['id']
        ]);
    }

    public static function removeFromBuddy($userId)
    {
        $db = Database::get();
        $sql = 'DELETE FROM %%BUDDY%% WHERE `owner` = :userId OR `sender` = :userId;';
        $db->delete($sql, [
            ':userId'   => $userId
        ]);
    }

    public static function deletePlayer($userId)
    {
        if (ROOT_USER == $userId) {
            // superuser can not be deleted.
            throw new Exception("Superuser #" . ROOT_USER . " can't be deleted!");
        }

        $db = Database::get();
        $sql = 'SELECT `id`, `universe`, `ally_id` FROM %%USERS%% WHERE `id` = :userId;';
        $userData = $db->selectSingle($sql, [
            ':userId'   => $userId
        ]);

        if (empty($userData)) {
            return false;
        }

        PlayerUtil::removeFromAlliance($userData);

        PlayerUtil::removeFromBuddy($userId);

        $sql = 'DELETE %%FLEETS%%, %%FLEETS_EVENT%%
            FROM %%FLEETS%% LEFT JOIN %%FLEETS_EVENT%% on `fleet_id` = fleetId
            WHERE `fleet_owner` = :userId;';
        $db->delete($sql, [
            ':userId'   => $userId
        ]);

        $sql = 'DELETE FROM %%MESSAGES%% WHERE `message_owner` = :userId;';
        $db->delete($sql, [
            ':userId'   => $userId
        ]);

        $sql = 'DELETE FROM %%NOTES%% WHERE `owner` = :userId;';
        $db->delete($sql, [
            ':userId'   => $userId
        ]);

        $sql = 'DELETE %%PLANETS%%, %%PLANET_WRECKFIELD%%
            FROM %%PLANETS%% LEFT JOIN %%PLANET_WRECKFIELD%% on `planetID` = id
            WHERE `id_owner` = :userId;';
        $db->delete($sql, [
            ':userId'   => $userId
        ]);

        $sql = 'DELETE FROM %%USERS%% WHERE `id` = :userId;';
        $db->delete($sql, [
            ':userId'   => $userId
        ]);

        $sql = 'DELETE FROM %%STATPOINTS%% WHERE `stat_type` = :type AND `id_owner` = :userId;';
        $db->delete($sql, [
            ':userId'   => $userId,
            ':type'     => 1
        ]);

        $sql = 'DELETE FROM %%ADVANCED_STATS%% WHERE `userId` = :userId;';
        $db->delete($sql, [
            ':userId'    => $userId,
        ]);

        $fleetIds = $db->select('SELECT `fleet_id` FROM %%FLEETS%% WHERE `fleet_target_owner` = :userId;', [
            ':userId'   => $userId
        ]);

        foreach ($fleetIds as $fleetId) {
            FleetFunctions::sendFleetBack(['id' => $userId], $fleetId['fleet_id']);
        }

        /*
        $sql = 'UPDATE %%UNIVERSE%% SET `userAmount` = `userAmount` - 1 WHERE `universe` = :universe;';
        $db->update($sql, array(
            ':universe' => $userData['universe']
        ));

        Cache::get()->flush('universe');
        */

        return true;
    }

    /**
	 * Delete all Planets of players and recreate them according to the algorithm, currently selected.
	 *
	 * @param int	 		$universe       The id of the Universe
	 */
    public static function reshuffleAllPlanets(
        $universe
    ){
        $USER =& Singleton()->USER;
        $db = Database::get();
        $config = Config::get($universe);
        $sql = 'SELECT MAX(count) AS max FROM(SELECT count(*) AS count, `id_owner` FROM %%PLANETS%% WHERE `universe` = :uni GROUP BY `id_owner`) AS plani;';
        $maxPlanetAmount = $db->selectSingle($sql, [
            ':uni' => $universe
        ], 'max');
        $uniStatus = $config->uni_status;

        if ($maxPlanetAmount > 1 || $uniStatus != STATUS_CLOSED || $USER['authlevel'] != AUTH_ADM) {
            $result = "";
            $LNG =& Singleton()->LNG;
            if ($maxPlanetAmount > 1) {
                $result = $LNG['rp_planet_error'];
            } elseif ($uniStatus != STATUS_CLOSED) {
                $result = $LNG['rp_universe_error'];
            } elseif ($USER['authlevel'] != AUTH_ADM) {
                $result = $LNG['rp_rights_error'];
            }
            return $result;
        }
        $sql = 'UPDATE %%USERS%% SET `onlinetime` = :onlinetime WHERE `universe` = :universe;';
        $db->update($sql, [
            ':onlinetime' => TIMESTAMP,
            ':universe' => $universe
        ]);
        $sql = 'SELECT `id` FROM %%USERS%% WHERE `authlevel` = :auth AND `universe` = :universe ORDER BY `id`;';
        $userIds = $db->select($sql, [
            ':auth'      => AUTH_USR,
            ':universe' => $universe
        ], 'id');
        foreach ($userIds as $userId) {
            $sql = 'DELETE FROM %%PLANETS%% WHERE `id_owner` = :userId;';
            $db->delete($sql, [
                ':userId'   => $userId['id'],
            ]);
        }
        $config->last_galaxy_pos = 1;
        $config->last_system_pos = 1;
        foreach ($userIds as $userId) {
            if ($config->planet_creation == 1) {
                $pos = PlayerUtil::randomHP($universe);
            } elseif ($config->planet_creation == 2) {
                $rand_vs_block = rand(0, 1);
                switch ($rand_vs_block) {
                    case 0:
                        $pos = PlayerUtil::randomHP($universe);
                        break;
                    case 1:
                        $pos = PlayerUtil::blockHP($universe);
                        break;
                }
            } else {
                $pos = PlayerUtil::linearHP($universe, $config);
            }
            if (isset($universe, $pos['galaxy'], $pos['system'], $pos['position'])) {
                if (self::checkPosition($universe, $pos['galaxy'], $pos['system'], $pos['position']) === false) {
                    throw new Exception(
                        sprintf("Try to create a planet at position: %s:%s:%s!", $pos['galaxy'], $pos['system'], $pos['position'])
                    );
                }

                if (self::isPositionFree($universe, $pos['galaxy'], $pos['system'], $pos['position']) === false) {
                    throw new Exception(
                        sprintf("Position is not empty: %s:%s:%s!", $pos['galaxy'], $pos['system'], $pos['position'])
                    );
                }
            }
            $sql = 'SELECT `lang` FROM %%USERS%% WHERE `id` = :userId;';
            $lang = $db->selectSingle($sql, [
                ':userId' => $userId['id'],
            ], 'lang');
            $LNG    = new Language($lang);
            Singleton()->LNG = $LNG;
            $LNG =& Singleton()->LNG;
            $LNG->includeData(['L18N', 'INGAME', 'TECH', 'CUSTOM']);
            $name = $LNG['fcm_mainplanet'];
            $planetId = self::createPlanet(
                $pos['galaxy'],
                $pos['system'],
                $pos['position'],
                $universe,
                $userId['id'],
                $name,
                true
            );
            $sql = 'UPDATE %%USERS%% SET
                `galaxy` = :galaxy,
                `system` = :system,
                `planet` = :position,
                `id_planet` = :planetId
                WHERE `id` = :userId;';

            $db->update($sql, [
                ':galaxy'   => $pos['galaxy'],
                ':system'   => $pos['system'],
                ':position' => $pos['position'],
                ':planetId' => $planetId,
                ':userId'   => $userId['id'],
            ]);
        }
        return true;
    }
    public static function deletePlanet($planetId)
    {
        $db = Database::get();
        $sql = 'SELECT `id_owner`, `planet_type`, `id_luna` FROM %%PLANETS%% 
                    WHERE `id` = :planetId AND `id` NOT IN (SELECT `id_planet` FROM %%USERS%%);';
        $planetData = $db->selectSingle($sql, [
            ':planetId' => $planetId
        ]);

        if (empty($planetData)) {
            throw new Exception("Can not found planet #" . $planetId . "!");
        }

        $sql = 'SELECT `fleet_id` FROM %%FLEETS%% WHERE `fleet_end_id` = :planetId 
                    OR (`fleet_end_type` = 3 AND `fleet_end_id` = :moondId);';
        $fleetIds = $db->select($sql, [
            ':planetId' => $planetId,
            ':moondId'  => $planetData['id_luna']
        ]);

        foreach ($fleetIds as $fleetId) {
            FleetFunctions::sendFleetBack(['id' => $planetData['id_owner']], $fleetId['fleet_id']);
        }

        if ($planetData['planet_type'] == 3) {
            $sql = 'DELETE FROM %%PLANETS%% WHERE id = :planetId;';
            $db->delete($sql, [
                ':planetId' => $planetId
            ]);

            $sql = 'UPDATE %%PLANETS%% SET `id_luna` = :resetId WHERE `id_luna` = :planetId;';
            $db->update($sql, [
                ':resetId'  => 0,
                ':planetId' => $planetId
            ]);
        } else {
            require_once 'includes/classes/achievements/PlayerUtilAchievement.class.php';
            PlayerUtilAchievement::checkPlayerUtilAchievementsDeletion($planetData, $planetId);
            $sql = 'UPDATE %%PLANETS%% SET `destruyed` = :time WHERE `id` = :planetID;';
            $db->update($sql, [
                ':time'   => TIMESTAMP + 86400,
                ':planetID' => $planetId,
            ]);
            $sql = 'DELETE FROM %%PLANETS%% WHERE `id` = :lunaID;';
            $db->delete($sql, [
                ':lunaID' => $planetData['id_luna']
            ]);
            $sql = 'DELETE FROM %%PLANET_WRECKFIELD%% WHERE `planetID` = :planetID;';
            $db->delete($sql, [
                ':planetID' => $planetId,
            ]);
        }

        return true;
    }

    private static function getAstroTech($USER)
    {
        $resource =& Singleton()->resource;

        $astroTech = $USER[$resource[124]];

        $CurrentQueue = !empty($USER['b_tech_queue']) ? unserialize($USER['b_tech_queue']) : [];
        if (!empty($CurrentQueue) && count($CurrentQueue) > 0) {
            foreach ($CurrentQueue as $ListIDArray) {
                if ($ListIDArray[0] == 124 && $ListIDArray[3] <= TIMESTAMP) {
                    $astroTech++;
                }
            }
        }

        return $astroTech;
    }

    public static function maxPlanetCount($USER)
    {
        $resource =& Singleton()->resource;
        $config = Config::get($USER['universe']);

        $planetPerTech = $config->max_additional_planets;

        if ($config->max_initial_planets == 0) {
            $planetPerTech = 999;
        }

        if ($config->max_initial_planets == 0) {
            $planetPerBonus = 999;
        }

        // http://owiki.de/index.php/Astrophysik#.C3.9Cbersicht
        return (int) ceil(
            $config->max_initial_planets + min(
                $planetPerTech,
                PlayerUtil::getAstroTech($USER) * $config->planets_per_tech
            )
        );
    }

    public static function allowPlanetPosition($position, $USER = null, $universe = ROOT_UNI)
    {
        // http://owiki.de/index.php/Astrophysik#.C3.9Cbersicht

        if (isset($USER)) {
            $config = Config::get($USER['universe']);
            $astroTech = PlayerUtil::getAstroTech($USER);
        } else {
            $config = Config::get($universe);
            $astroTech = 1;
        }

        switch ($position) {
            case 0:
                return false;
                break;
            case 1:
            case ($config->max_planets):
                return $astroTech >= 8;
                break;
            case 2:
            case ($config->max_planets - 1):
                return $astroTech >= 6;
                break;
            case 3:
            case ($config->max_planets - 2):
                return $astroTech >= 4;
                break;
            default:
                return $astroTech >= 1 && $config->max_planets >= $position;
                break;
        }
    }

    public static function sendMessage(
        $userId,
        $senderId,
        $senderName,
        $messageType,
        $subject,
        $text,
        $time,
        $parentID = null,
        $unread = 1,
        $universe = null
    ) {
        if (is_null($universe)) {
            $universe = Universe::current();
        }

        $db = Database::get();

        $sql = 'INSERT INTO %%MESSAGES%% SET
            `message_owner`     = :userId,
            `message_sender`    = :sender,
            `message_time`      = :time,
            `message_type`      = :type,
            `message_from`      = :from,
            `message_subject`   = :subject,
            `message_text`      = :text,
            `message_unread`    = :unread,
            `message_universe`  = :universe;';

        $db->insert($sql, [
            ':userId'   => $userId,
            ':sender'   => $senderId,
            ':time'     => $time,
            ':type'     => $messageType,
            ':from'     => $senderName,
            ':subject'  => $subject,
            ':text'     => $text,
            ':unread'   => $unread,
            ':universe' => $universe,
        ]);

        if ($messageType === 1) {
            $sql = 'SELECT `username`, `discord_id`, `discord_hook` FROM %%USERS%% 
                WHERE `universe` = :universe AND `id` = :userID AND `authlevel` > 0;';
            $admin = $db->selectSingle($sql, [
                ':universe' => $universe,
                ':userID' => $userId,
            ]);

            if (!empty($admin['discord_hook'])) {
                $title = '';
                if (!empty($admin['discord_id'])) {
                    $title = '<@' . $admin['discord_id'] . '> hat eine neue Ingame-Nachricht erhalten.';
                } else {
                    $title = '<@' . $admin['username'] . '> hat eine neue Ingame-Nachricht erhalten.';
                }

                $content = [
                    'Universum' => $universe,
                    'Absender' => $senderId . ' - ' . $senderName,
                    'Betreff' => $subject,
                ];
                require_once 'includes/classes/class.Discord.php';
                Discord::sendMessage($admin['discord_hook'], $title, $content);
            }
        }

        require_once 'includes/classes/achievements/PlayerUtilAchievement.class.php';
        PlayerUtilAchievement::checkPlayerUtilAchievements36($userId, $text);
    }

    public static function clearPlanets($USER)
    {
        $reslist =& Singleton()->reslist;
        $resource =& Singleton()->resource;

        $db = Database::get();

        $params = [];
        $params[] = $resource[RESOURCE_METAL] . ' = 0';
        $params[] = $resource[RESOURCE_CRYSTAL] . ' = 0';
        $params[] = $resource[RESOURCE_DEUT] . ' = 0';

        foreach ($reslist['fleet'] as $element) {
            $params[] = $resource[$element] . ' = 0';
        }

        foreach ($reslist['defense'] as $element) {
            $params[] = $resource[$element] . ' = 0';
        }

        $sql = 'UPDATE %%PLANETS%% SET `b_hangar` = 0, `b_hangar_id` = "", ' . implode(", ", $params) . ' WHERE `id_owner` = :owner;';
        $db->update($sql, [':owner' => $USER['id']]);
    }

    public static function disable_vmode(&$USER, &$PLANET = null)
    {
        $db = Database::get();

        $sql = 'SELECT `urlaubs_start` FROM %%USERS%% WHERE `id` = :userID;';
        $umode_start = $db->selectSingle($sql, [
            ':userID'   => $USER['id'],
        ], 'urlaubs_start');
        $umode_delta = TIMESTAMP - $umode_start;
        if ($USER['urlaubs_until'] <= TIMESTAMP) {
            if (!empty($USER['b_tech']) && !empty($USER['b_tech_queue'])) {
                $CurrentQueue       = unserialize($USER['b_tech_queue']);
                foreach ($CurrentQueue as &$TechArray) {
                    $TechArray[3] += $umode_delta;
                }
                $USER['b_tech_queue'] = serialize($CurrentQueue);
                unset($CurrentQueue);
                $sql = 'UPDATE %%USERS%% SET
                    `b_tech` = `b_tech` + :umode_delta,
                    `b_tech_queue` = :b_tech_queue
                    WHERE `id` = :userID;';
                $db->update($sql, [
                    ':umode_delta' => $umode_delta,
                    ':userID'   => $USER['id'],
                    ':b_tech_queue' => $USER['b_tech_queue'],
                ]);
                $USER['b_tech'] = $USER['b_tech'] + $umode_delta;
            }
            if (isset($PLANET) && !empty($PLANET['b_building']) && !empty($PLANET['b_building_id'])) {
                $PLANET['b_building'] = $PLANET['b_building'] + $umode_delta;
                $CurrentQueue       = unserialize($PLANET['b_building_id']);
                foreach ($CurrentQueue as &$BuildArray) {
                    $BuildArray[3] += $umode_delta;
                }
                $PLANET['b_building_id'] = serialize($CurrentQueue);
                unset($CurrentQueue);
            }

            $sql = 'SELECT `id`, `b_building`, `b_building_id` FROM %%PLANETS%% WHERE `id_owner` = :userID AND `destruyed` = 0;';
            $planets = $db->select($sql, [
                ':userID'   => $USER['id'],
            ]);

            foreach ($planets as $CPLANET) {
                if (!empty($CPLANET['b_building']) && !empty($CPLANET['b_building_id'])) {
                    $CPLANET['b_building'] = $CPLANET['b_building'] + $umode_delta;
                    $CurrentQueue = unserialize($CPLANET['b_building_id']);
                    foreach ($CurrentQueue as &$BuildArray) {
                        $BuildArray[3] += $umode_delta;
                    }
                    $CPLANET['b_building_id'] = serialize($CurrentQueue);
                    unset($CurrentQueue);
                }

                $sql = 'UPDATE %%PLANETS%% SET
                    `b_building` = :building,
                    `b_building_id` = :current_queue,
                    `last_update` = :timestamp,
                    `metal_mine_porcent` = "10",
                    `crystal_mine_porcent` = "10",
                    `deuterium_sintetizer_porcent` = "10",
                    `solar_plant_porcent` = "10",
                    `fusion_plant_porcent` = "10",
                    `solar_satelit_porcent` = "10"
                    WHERE `id` = :planetID;';
                $db->update($sql, [
                    ':planetID' => $CPLANET['id'],
                    ':building' => $CPLANET['b_building'],
                    ':current_queue' => $CPLANET['b_building_id'],
                    ':timestamp' => TIMESTAMP,
                ]);

                $sql = 'UPDATE %%PLANET_WRECKFIELD%% SET
                    `created` = `created` + :umode_delta,
                    `repair_order_start` =`repair_order_start` + :umode_delta,
                    `repair_order_end` = `repair_order_end` + :umode_delta
                    WHERE `planetID` = :planetID;';
                $db->update($sql, [
                    ':umode_delta' => $umode_delta,
                    ':planetID' => $CPLANET['id'],
                ]);

                unset($CPLANET);
            }

            $sql = 'UPDATE %%USERS%% SET
                `urlaubs_modus` = 0,
                `urlaubs_until` = 0,
                `urlaubs_start` = 0
                WHERE `id` = :userID;';
            $db->update($sql, [':userID'   => $USER['id']]);

            $USER['urlaubs_modus'] = 0;
            $USER['urlaubs_until'] = 0;
            $USER['urlaubs_start'] = 0;

            $sql = "SELECT * FROM %%PLANETS%% WHERE id_owner = :id;";
            $PlanetsRAW = $db->select($sql, [
                ':id' => $USER['id'],
            ]);

            $PlanetRess = new ResourceUpdate();
            if (isset($PLANET)) {
                $PLANET['last_update'] = TIMESTAMP;
                $PLANET['eco_hash'] = '';
                list($USER, $PLANET) = $PlanetRess->CalcResource($USER, $PLANET, true);
                require_once 'includes/classes/achievements/PlayerUtilAchievement.class.php';
                PlayerUtilAchievement::checkPlayerUtilAchievements33($USER['id']);
            }

            foreach ($PlanetsRAW as $CPLANET) {
                $CPLANET['eco_hash'] = '';
                list($USER, $CPLANET) = $PlanetRess->CalcResource($USER, $CPLANET, true);
            }
        }
    }

    public static function enable_vmode(&$USER, &$PLANET = null)
    {
        $db = Database::get();

        $sql = 'UPDATE %%USERS%% SET `urlaubs_modus` = 1, `urlaubs_until` = :time, `urlaubs_start` = :startTime
            WHERE `id` = :userID';
        $db->update($sql, [
            ':userID'       => $USER['id'],
            ':time'         => (TIMESTAMP + Config::get()->vmode_min_time),
            ':startTime'    => TIMESTAMP,
        ]);

        $sql = 'UPDATE %%PLANETS%% SET `energy_used` = 0, `energy` = 0, `metal_mine_porcent` = "0",
            `crystal_mine_porcent` = "0", `deuterium_sintetizer_porcent` = "0", `solar_plant_porcent` = "0",
            `fusion_plant_porcent` = "0", `solar_satelit_porcent` = "0", `metal_perhour` = 0,
            `crystal_perhour` = 0, `deuterium_perhour` = 0 WHERE `id_owner` = :userID;';
        $db->update($sql, [
            ':userID'   => $USER['id'],
        ]);

        $sql = 'SELECT `fleet_id`, `fleet_owner` FROM %%FLEETS%%
            WHERE `fleet_universe` = :universe
            AND `fleet_mission` = :fleet_mision
            AND `fleet_target_owner` = :id;';
        $FleetsRAW = $db->select($sql, [
            ':universe'     => Universe::current(),
            ':fleet_mision' => MISSION_HOLD,
            ':id'           => $USER['id'],
        ]);
        foreach ($FleetsRAW as $Fleet) {
            $attacker['id'] = $Fleet['fleet_owner'];
            FleetFunctions::SendFleetBack($attacker, $Fleet['fleet_id']);
        }
        if (isset($PLANET)) {
            $PLANET['energy_used'] = '0';
            $PLANET['energy'] = '0';
            $PLANET['metal_mine_porcent'] = '0';
            $PLANET['crystal_mine_porcent'] = '0';
            $PLANET['deuterium_sintetizer_porcent'] = '0';
            $PLANET['solar_plant_porcent'] = '0';
            $PLANET['fusion_plant_porcent'] = '0';
            $PLANET['solar_satelit_porcent'] = '0';
            $PLANET['metal_perhour'] = '0';
            $PLANET['crystal_perhour'] = '0';
            $PLANET['deuterium_perhour'] = '0';
            require_once 'includes/classes/achievements/PlayerUtilAchievement.class.php';
            PlayerUtilAchievement::checkPlayerUtilAchievements31($USER['id']);
        }
    }

    public static function player_colors($USER = null)
    {
        if (!isset($USER)) {
            return [
                'colorMission2friend' => '#ff00ff',

                'colorMission1Own' => '#66cc33',
                'colorMission2Own' => '#339966',
                'colorMission3Own' => '#5bf1c2',
                'colorMission4Own' => '#cf79de',
                'colorMission5Own' => '#80a0c0',
                'colorMission6Own' => '#ffcc66',
                'colorMission7Own' => '#c1c1c1',
                'colorMission7OwnReturn' => '#cf79de',
                'colorMission8Own' => '#ceff68',
                'colorMission9Own' => '#ffff99',
                'colorMission10Own' => '#ffcc66',
                'colorMission15Own' => '#5bf1c2',
                'colorMission16Own' => '#5bf1c2',
                'colorMission17Own' => '#5bf1c2',

                'colorMissionReturnOwn' => '#6e8eea',

                'colorMission1Foreign' => '#ff0000',
                'colorMission2Foreign' => '#aa0000',
                'colorMission3Foreign' => '#00ff00',
                'colorMission4Foreign' => '#ad57bc',
                'colorMission5Foreign' => '#3399cc',
                'colorMission6Foreign' => '#ff6600',
                'colorMission7Foreign' => '#00ff00',
                'colorMission8Foreign' => '#acdd46',
                'colorMission9Foreign' => '#dddd77',
                'colorMission10Foreign' => '#ff6600',
                'colorMission15Foreign' => '#39d0a0',
                'colorMission16Foreign' => '#39d0a0',
                'colorMission17Foreign' => '#39d0a0',

                'colorMissionReturnForeign' => '#6e8eea',

                'colorStaticTimer' => '#ffff00',
            ];
        }

        if (isset($USER['colors'])) {
            return $USER['colors'];
        }

        return [
            'colorMission2friend' => $USER['colorMission2friend'],

            'colorMission1Own' => $USER['colorMission1Own'],
            'colorMission2Own' => $USER['colorMission2Own'],
            'colorMission3Own' => $USER['colorMission3Own'],
            'colorMission4Own' => $USER['colorMission4Own'],
            'colorMission5Own' => $USER['colorMission5Own'],
            'colorMission6Own' => $USER['colorMission6Own'],
            'colorMission7Own' => $USER['colorMission7Own'],
            'colorMission7OwnReturn' => $USER['colorMission7OwnReturn'],
            'colorMission8Own' => $USER['colorMission8Own'],
            'colorMission9Own' => $USER['colorMission9Own'],
            'colorMission10Own' => $USER['colorMission10Own'],
            'colorMission15Own' => $USER['colorMission15Own'],
            'colorMission16Own' => $USER['colorMission16Own'],
            'colorMission17Own' => $USER['colorMission17Own'],

            'colorMissionReturnOwn' => $USER['colorMissionReturnOwn'],

            'colorMission1Foreign' => $USER['colorMission1Foreign'],
            'colorMission2Foreign' => $USER['colorMission2Foreign'],
            'colorMission3Foreign' => $USER['colorMission3Foreign'],
            'colorMission4Foreign' => $USER['colorMission4Foreign'],
            'colorMission5Foreign' => $USER['colorMission5Foreign'],
            'colorMission6Foreign' => $USER['colorMission6Foreign'],
            'colorMission7Foreign' => $USER['colorMission7Foreign'],
            'colorMission8Foreign' => $USER['colorMission8Foreign'],
            'colorMission9Foreign' => $USER['colorMission9Foreign'],
            'colorMission10Foreign' => $USER['colorMission10Foreign'],
            'colorMission15Foreign' => $USER['colorMission15Foreign'],
            'colorMission16Foreign' => $USER['colorMission16Foreign'],
            'colorMission17Foreign' => $USER['colorMission17Foreign'],

            'colorMissionReturnForeign' => $USER['colorMissionReturnForeign'],

            'colorStaticTimer' => $USER['colorStaticTimer'],
        ];
    }

    public static function player_stb_settings($USER = null)
    {
        if (!isset($USER)) {
            return [
                'stb_small_ress'    => 500,
                'stb_med_ress'      => 3000,
                'stb_big_ress'      => 7000,
                'stb_small_time'    => 1,
                'stb_med_time'      => 2,
                'stb_big_time'      => 3,
                'stb_enabled'       => 0,
            ];
        }

        if (isset($USER['stb_settings'])) {
            return $USER['stb_settings'];
        }

        return [
            'stb_small_ress'    => $USER['stb_small_ress'],
            'stb_med_ress'      => $USER['stb_med_ress'],
            'stb_big_ress'      => $USER['stb_big_ress'],
            'stb_small_time'    => $USER['stb_small_time'],
            'stb_med_time'      => $USER['stb_med_time'],
            'stb_big_time'      => $USER['stb_big_time'],
            'stb_enabled'       => $USER['stb_enabled'],
        ];
    }

    public static function player_signal_colors($USER = null)
    {
        if (!isset($USER)) {
            return [
                'colorPositive' => '#00ff00',
                'colorNegative' => '#ff0000',
                'colorNeutral' => '#ffd600',
            ];
        }

        if (isset($USER['signalColors'])) {
            return $USER['signalColors'];
        }

        return [
            'colorPositive' => $USER['colorPositive'],
            'colorNegative' => $USER['colorNegative'],
            'colorNeutral' => $USER['colorNeutral'],
        ];
    }

    public static function get_block_status($userId, $targetUserId)
    {
        $db = Database::get();

        $sql = 'SELECT `block_dm`, `block_trade` FROM %%USERS_BLOCKLIST%% WHERE `blocked_user` = :userID AND `blocking_user` = :targetID;';
        $blocked = $db->selectSingle($sql, [
            ':userID'       => $userId,
            ':targetID'     => $targetUserId,
        ]);
        if (empty($blocked)) {
            return [
                'block_dm' => 0,
                'block_trade' => 0,
            ];
        }
        return $blocked;
    }

    public static function get_authlevel($userId)
    {
        $db = Database::get();

        $sql = 'SELECT `authlevel` FROM %%USERS%% WHERE `id` = :userID;';
        $authlevel = $db->selectSingle($sql, [
            ':userID'       => $userId,
        ], 'authlevel');

        return $authlevel;
    }
}
/*
    Enable to debug
*/
// try {
//     define('MODE', 'INSTALL');
//     define('ROOT_PATH', 'G:/xampp/htdocs/pr0game/');
//     define('TIMESTAMP', time());
//     require 'includes/constants.php';
//     require 'includes/classes/Database.class.php';
//     require 'includes/classes/Cache.class.php';
//     require 'includes/vars.php';
//     require 'includes/classes/Config.class.php';

//     PlayerUtil::randomHP(1);
// } catch (Exception $e) {
// }
