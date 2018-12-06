<?php
/**
 * @filesource Modules/Index/Models/Settings.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Settings;

use App\Login;
use Somtum\Config;
use Somtum\Http\Request;
use Somtum\Text;

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
     * @var Index\People\Model
     */
    private $people;

    public function __construct()
    {
        // อ่านข้อมูล people.json
        $this->people = new \Index\People\Model();
    }

    /**
     * คืนค่าจำนวน record ทั้งหมด.
     *
     * @return int
     */
    public function count($coditions)
    {
        return sizeof($this->people->getDatas());
    }

    /**
     * Query ข้อมูล.
     *
     * @param $perPage
     * @param $start
     */
    public function execute($coditions, $perPage, $start, $sort)
    {
        $datas = array();
        foreach ($this->people->getDatas() as $i => $values) {
            $values['id'] = $i;
            $datas[] = $values;
        }

        return $datas;
    }

    /**
     * รับค่าจากฟอร์ม settings.html.
     *
     * @param Request $request
     *
     * @return JSON
     */
    public function submit(Request $request)
    {
        // session, referer, แอดมิน
        if ($request->initSession() && $request->isReferer() && Login::isAdmin()) {
            $ret = array();
            // บันทึก JSON
            $this->people->setConfig('showResult', Text::filter('a-z_', $request->post('showResult')));
            if ($this->people->save() === false) {
                $ret['alert'] = 'ไม่สามารถบันทึกได้ ไฟล์ datas/people.json อาจเป็นแบบอ่านอย่างเดียว';
            } else {
                // โหลด config
                $cfg = Config::load(ROOT_PATH.'settings/config.php');
                // ค่าที่ส่งมา
                $cfg->start_vote_date = Text::filter('0-9\-', $request->post('start_vote_date'));
                $cfg->end_vote_date = Text::filter('0-9\-', $request->post('end_vote_date'));
                $cfg->start_vote_time = Text::filter('0-9:', $request->post('start_vote_time'));
                $cfg->end_vote_time = Text::filter('0-9:', $request->post('end_vote_time'));
                // บันทึก
                if (Config::save($cfg, ROOT_PATH.'settings/config.php')) {
                    $ret['alert'] = 'บันทึกเรียบร้อย';
                    $ret['location'] = 'reload';
                } else {
                    $ret['alert'] = 'ไม่สามารถบันทึกได้ ไฟล์ settings/config.php อาจเป็นแบบอ่านอย่างเดียว';
                }
            }
            echo json_encode($ret);
        }
    }

    /**
     * รับค่าจาก action ของตาราง.
     *
     * @param Request $request
     */
    public function action(Request $request)
    {
        // session, referer, admin
        if ($request->initSession() && $request->isReferer() && Login::isAdmin()) {
            $ret = array();
            if (preg_match_all('/,?([0-9]+),?/', $request->post('id'), $match)) {
                $action = $request->post('action');
                if ($action === 'delete') {
                    // โหลด people.json
                    $people = new \Index\People\Model();
                    // ลบ
                    foreach ($match[1] as $i) {
                        $people->delete($i);
                    }
                    $people->save();
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
