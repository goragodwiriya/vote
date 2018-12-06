<?php
/**
 * @filesource Modules/Index/Models/Vote.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Vote;

use Somtum\Http\Request;

/**
 * Model สำหรับ Vote.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Somtum\Model
{
    /**
     * เมธอดสำหรับการโหวต
     * คืนค่าผลโหวต เป็น JSON.
     *
     * @param Request $request
     *
     * @return JSON
     */
    public function post(Request $request)
    {
        // ตรวจสอบความถูกต้องของข้อมูลที่ส่งมา
        if (preg_match('/^vote_([0-9]+)$/', $request->post('id'), $values) &&
            preg_match('/^([0-9]{9,})$/', $request->post('user'), $users)) {
            $ret = array();
            $mktime = time();
            // ตรวจสอบเปิดโหวต
            if (preg_match('/^[0-9]{2,2}:[0-9]{2,2}$/', self::$cfg->start_vote_time)) {
                if (preg_match('/^[0-9]{4,4}\-[0-9]{2,2}\-[0-9]{2,2}$/', self::$cfg->start_vote_date)) {
                    $start_vote = strtotime(self::$cfg->start_vote_date.' '.self::$cfg->start_vote_time.':00');
                } else {
                    $start_vote = strtotime(date('Y-m-d').' '.self::$cfg->start_vote_time.':00');
                }
            } elseif (preg_match('/^[0-9]{4,4}\-[0-9]{2,2}\-[0-9]{2,2}$/', self::$cfg->start_vote_date)) {
                $start_vote = strtotime(self::$cfg->start_vote_date.' 00:00:00');
            } else {
                $start_vote = 0;
            }
            if ($start_vote > 0 && $mktime < $start_vote) {
                $ret['alert'] = "ขออภัย! ยังไม่เปิดให้โหวต กรุณากลับมาใหม่ ในเวลา\n".date('d M Y H:i', $start_vote);
            }
            if (empty($ret)) {
                // ตรวจสอบปิดโหวต
                if (preg_match('/^[0-9]{2,2}:[0-9]{2,2}$/', self::$cfg->end_vote_time)) {
                    if (preg_match('/^[0-9]{4,4}\-[0-9]{2,2}\-[0-9]{2,2}$/', self::$cfg->end_vote_date)) {
                        $end_vote = strtotime(self::$cfg->end_vote_date.' '.self::$cfg->end_vote_time.':59');
                    } else {
                        $end_vote = strtotime(date('Y-m-d').' '.self::$cfg->end_vote_time.':59');
                    }
                } elseif (preg_match('/^[0-9]{4,4}\-[0-9]{2,2}\-[0-9]{2,2}$/', self::$cfg->end_vote_date)) {
                    $end_vote = strtotime(self::$cfg->end_vote_date.' 23:59:59');
                } else {
                    $end_vote = 0;
                }
                if ($end_vote > 0 && $mktime > $end_vote) {
                    $ret['alert'] = 'ขออภัย! ปิดโหวตแล้ว';
                }
            }
            if (empty($ret)) {
                // Database
                $db = new \App\Db();
                // ตรวจสอบว่าโหวตแล้วหรือยัง
                $search = $db->first('vote', array('user' => $users[1]));
                if ($search) {
                    // โหวตแล้ว
                    $ret['alert'] = 'ขออภัย! คุณสามารถโหวตได้แค่ครั้งเดียว';
                } else {
                    // บันทึกผลการโหวต
                    $db->add('vote', array(
                        'user' => $users[1],
                        'vote_id' => (int) $values[1],
                        'create_date' => date('Y-m-d H:i:s'),
                        'ip' => $request->getClientIp(),
                    ));
                    // โหลด people.json
                    $people = new \Index\People\Model();
                    $showResult = $people->getConfig('showResult');
                    if ($showResult === 'always' || $showResult === 'after_vote') {
                        // ผล Vote
                        $ret['result'] = $this->getResult($db, $people->getDatas());
                    }
                    // คืนค่า
                    $ret['success'] = 'true';
                    $ret['alert'] = 'ขอบคุณสำหรับผลโหวต';
                }
            }
            if (!empty($ret)) {
                // คืนค่า JSON
                echo json_encode($ret);
            }
        }
    }

    /**
     * คืนค่าผลโหวตเป็น JSON สำหรับการเรียกจาก API.
     *
     * @param Request $request
     *
     * @return JSON
     */
    public function get(Request $request)
    {
        // Database
        $db = new \App\Db();
        // โหลด people.json
        $people = new \Index\People\Model();
        // คืนค่า JSON
        echo json_encode($this->getResult($db, $people->getDatas()));
    }

    /**
     * คืนค่าผลโหวตจากฐานข้อมูล
     * คืนค่าเป็นแอเรย์.
     *
     * @param Database $db
     * @param array    $result ข้อมูลโหวต สำหรับส่งกลับ
     *
     * @return array
     */
    private function getResult($db, $result)
    {
        $sql = 'SELECT `vote_id`,COUNT(*) AS `vote` FROM `'.self::$cfg->prefix.'_vote` GROUP BY `vote_id` ORDER BY `vote` DESC';
        foreach ($db->customQuery($sql) as $item) {
            $result[$item->vote_id]['vote'] = $item->vote;
        }

        return $result;
    }
}
