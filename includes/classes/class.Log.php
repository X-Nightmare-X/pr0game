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

class Log
{
    private $data = [];

    public function __construct($mode)
    {
        $this->data['mode']     = $mode;
        $this->data['admin']    = Session::load()->userId;
        $this->data['uni']      = Universe::getEmulated();
    }
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }
    public function __get($key)
    {
        return $this->__isset($key) ? $this->data[$key] : null;
    }
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    public function saveTr()
    {
        $db = Database::get();
        $uni = (empty($this->data['universe']) ? $this->data['uni'] : $this->data['universe']);

        $sql = "INSERT INTO %%LOG%% SET
            `mode` = :mode,
            `admin` = :admin,
            `target` = :target,
            `time` = :time,
            `data` = :data,
            `universe` = :universe;";

        $db->insert($sql, [
            ':mode'     => $this->data['mode'],
            ':admin'    => $this->data['admin'],
            ':target'   => $this->data['target'],
            ':time'     => TIMESTAMP,
            ':data'     => serialize($this->data['new']),
            ':universe' => $uni,
        ]);
    }

    public function save()
    {
        $db = Database::get();
        $uni = (empty($this->data['universe']) ? $this->data['uni'] : $this->data['universe']);

        $sql = "INSERT INTO %%LOG%% SET
            `mode` = :mode,
            `admin` = :admin,
            `target` = :target,
            `time` = :time,
            `data` = :data,
            `universe` = :universe;";

        $db->insert($sql, [
            ':mode'     => $this->data['mode'],
            ':admin'    => $this->data['admin'],
            ':target'   => $this->data['target'],
            ':time'     => TIMESTAMP,
            ':data'     => serialize([$this->data['old'], $this->data['new']]),
            ':universe' => $uni,
        ]);
    }
}
