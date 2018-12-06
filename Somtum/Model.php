<?php
/**
 * @filesource Somtum/Model.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Somtum;

/**
 * Model base class.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Somtum\Base
{
    /**
     * Database Object.
     *
     * @var mixed
     */
    protected $db = null;
    /**
     * Sql command.
     *
     * @var string
     */
    protected $sql;

    /**
     * คืนค่าชื่อตาราง รวม prefix.
     *
     * @param  $table
     *
     * @return string
     */
    public function getTableName($table)
    {
        return empty(self::$cfg->prefix) ? $table : self::$cfg->prefix.'_'.$table;
    }

    /**
     * คืนค่า Database Object.
     *
     * @return mixed
     */
    public function db()
    {
        return $this->db;
    }
}
