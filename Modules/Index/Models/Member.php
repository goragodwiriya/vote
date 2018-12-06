<?php
/**
 * @filesource Modules/Index/Models/Member.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Member;

use App\Login;
use Somtum\Http\Request;

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
        $this->sql = 'FROM '.$this->getTableName('user');
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
        $sql = 'SELECT `id`,`username`,`name`,`phone`,`status`,`create_date`,`lastvisited`,`visited` '.$this->sql;
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

    /**
     * รับค่าจาก action ของตาราง.
     *
     * @param Request $request
     */
    public function action(Request $request)
    {
        // session, referer
        if ($request->initSession() && $request->isReferer() && Login::isAdmin()) {
            $ret = array();
            if (preg_match_all('/,?([0-9]+),?/', $request->post('id'), $match)) {
                $action = $request->post('action');
                if ($action === 'delete') {
                    // ลบสมาชิก ไม่ลบ ID 1
                    $ids = array();
                    foreach ($match[1] as $i) {
                        if ($i != 1) {
                            $ids[] = $i;
                        }
                    }
                    if (!empty($ids)) {
                        $this->db->delete('user', array('id' => $ids), 0);
                    }
                    // reload
                    $ret['location'] = 'reload';
                }
            }
            if (empty($ret)) {
                $ret['alert'] = Language::get('Unable to complete the transaction');
            }
            // คืนค่า JSON
            echo json_encode($ret);
        }
    }
}
