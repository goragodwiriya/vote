<?php
/**
 * @filesource Modules/Index/Models/Register.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Register;

use App\Login;
use Somtum\Http\Request;
use Somtum\Text;

/**
 * Model สำหรับ Vote.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model
{
    /**
     * รับค่าจากฟอร์ม register.html.
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
            // ค่าที่ส่งมา
            $save = array(
                'username' => Text::username($request->post('register_username')),
                'name' => Text::topic($request->post('register_name')),
                'phone' => Text::filter('0-9', $request->post('register_phone')),
                'status' => (int) $request->post('register_status'),
            );
            // Database
            $db = new \App\Db();
            // ตรวจสอบค่าที่ส่งมา
            if (empty($save['username'])) {
                $ret['ret_register_username'] = 'กรุณากรอก ชื่อผู้ใช้';
            } else {
                // ตรวจสอบ username ซ้ำ
                $search = $db->first('user', array('username' => $save['username']));
                if ($search) {
                    $ret['ret_register_username'] = 'มีชื่อนี้อยู่ก่อนแล้ว';
                }
            }
            // name
            if (empty($save['name'])) {
                $ret['ret_register_name'] = 'กรุณากรอก ชื่อ นามสกุล';
            }
            // password
            $password = $request->post('register_password');
            $repassword = $request->post('register_repassword');
            if (mb_strlen($password) < 4) {
                // รหัสผ่านต้องไม่น้อยกว่า 4 ตัวอักษร
                $ret['ret_register_password'] = 'รหัสผ่านต้องไม่น้อยกว่า 4 ตัวอักษร';
            } elseif ($repassword != $password) {
                // กรอกรหัสผ่านสองช่องให้ตรงกัน
                $ret['ret_register_repassword'] = 'กรุณากรอกรหัสผ่านสองช่องให้ตรงกัน';
            } else {
                $save['salt'] = uniqid();
                $save['password'] = sha1($password.$save['salt']);
            }
            if (empty($ret)) {
                // เพิ่มข้อมูล
                $save['create_date'] = date('Y-m-d H:i:s');
                // save
                $db->add('user', $save);
                // คืนค่า
                $ret['alert'] = 'บันทึกเรียบร้อย';
                $ret['location'] = 'admin.php?module=member';
            }
            // คืนค่า JSON
            echo json_encode($ret);
        }
    }
}
