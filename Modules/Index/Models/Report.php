<?php
/**
 * @filesource Modules/Index/Models/Report.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Report;

/**
 * Model สำหรับตาราง user.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Somtum\Model
{
    public function __construct()
    {
        $this->db = new \App\Db();
        $this->sql = 'FROM '.$this->getTableName('vote');
    }

    /**
     * คืนค่าจำนวน record ทั้งหมด.
     *
     * @return int
     */
    public function count($coditions)
    {
        $sql = 'SELECT COUNT(*) AS `count` '.$this->sql;
        $datas = array();
        $sql .= $this->createWhere($coditions, $datas);
        $result = $this->db->customQuery($sql, false, $datas);

        return $result ? $result[0]->count : 0;
    }

    /**
     * Query ข้อมูล.
     *
     * @param $perPage
     * @param $start
     */
    public function execute($coditions, $perPage, $start, $sort)
    {
        $sql = 'SELECT `create_date`,`ip`,`user`,`vote_id` '.$this->sql;
        $datas = array();
        $sql .= $this->createWhere($coditions, $datas);
        if ($sort != '') {
            $sql .= ' ORDER BY '.$sort;
        }
        if ($start == 0) {
            $sql .= ' LIMIT '.$perPage;
        } else {
            $sql .= ' LIMIT '.$start.', '.$perPage;
        }

        return $this->db->customQuery($sql, true, $datas);
    }

    /**
     * @param $coditions
     * @param $datas
     */
    private function createWhere($coditions, &$datas)
    {
        if (!empty($coditions)) {
            $keys = array();
            foreach ($coditions as $field => $value) {
                if (strpos($field, ' :search ') !== false) {
                    $keys[] = $field;
                    $datas[':search'] = $value;
                } else {
                    if (is_array($value)) {
                        $keys[] = "`$field` IN :$field";
                        $datas[":$field"] = $value;
                    } else {
                        $keys[] = "`$field`=:$field";
                        $datas[":$field"] = $value;
                    }
                }
            }

            return ' WHERE '.implode(' AND ', $keys);
        }

        return '';
    }
}
