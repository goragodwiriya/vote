<?php
/**
 * @filesource Modules/Index/Models/Write.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Write;

use App\Login;
use Somtum\Http\Request;
use Somtum\Text;

/**
 * Model สำหรับข้อมูล Vote.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Somtum\Model
{
    /**
     * รับค่าจากฟอร์ม write.html.
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
            // โหลด people.json
            $people = new \Index\People\Model();
            // รายการที่ต้องการ
            $save = $people->get((int) $request->post('write_id'));
            if ($save) {
                // ค่าที่ส่งมา
                $save['name'] = Text::topic($request->post('write_name'));
                $save['number'] = (int) $request->post('write_number');
                // ตรวจสอบค่าที่ส่งมา
                if ($save['number'] != $save['id'] && $people->exists($save['number'])) {
                    $ret['ret_write_number'] = 'มีรายการ ID นี้ อยู่ก่อนแล้ว';
                }
                if (empty($save['name'])) {
                    $ret['ret_write_name'] = 'กรุณากรอก ชื่อ';
                }
                if (empty($ret)) {
                    // รูปภาพอัปโหลด
                    $picture = $_FILES['write_picture'];
                    if ($picture['tmp_name'] != '') {
                        $exts = explode('.', strtolower($picture['name']));
                        $ext = end($exts);
                        if (!in_array($ext, array('jpg', 'jpeg', 'png'))) {
                            $ret['ret_write_picture'] = 'ชนิดของไฟล์ไม่ถูกต้อง';
                        } else {
                            // อัปดหลดรูปภาพ
                            $old_picture = $save['picture'];
                            $save['picture'] = $save['number'].'.'.$ext;
                            move_uploaded_file($picture['tmp_name'], ROOT_PATH.'datas/'.$save['picture']);
                            if ($old_picture != $save['picture'] && is_file(ROOT_PATH.'datas/'.$old_picture)) {
                                unlink(ROOT_PATH.'datas/'.$old_picture);
                            }
                        }
                    }
                }
                if (empty($ret)) {
                    // บันทึก
                    $people->setDatas($save['id'], $save);
                    $people->save();
                    // คืนค่า
                    $ret['alert'] = 'บันทึกเรียบร้อย';
                    $ret['location'] = WEB_URL.'admin.php?module=settings';
                }
            } else {
                $ret['alert'] = 'ไม่พบรายการที่เลือก';
            }
            echo json_encode($ret);
        }
    }
}
