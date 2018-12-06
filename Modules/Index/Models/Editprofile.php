<?php
/**
 * @filesource Modules/Index/Models/Editprofile.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Editprofile;

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
class Model extends \Somtum\Model
{
    /**
     * @return mixed
     */
    public function __construct()
    {
        if ($this->db === null) {
            $this->db = new \App\Db();
        }

        return $this->db;
    }

    /**
     * อ่านข้อมูลสมาชิกที่ $id
     * ถ้า $id=0 หมายถึงรายการใหม่.
     *
     * @param  $id
     *
     * @return mixed
     */
    public static function get($id)
    {
        if ($id > 0) {
            $model = new static();

            return $model->db->first('user', array('id' => $id));
        }

        return false;
    }

    /**
     * รับค่าจากฟอร์ม editprofile.html.
     *
     * @param Request $request
     *
     * @return JSON
     */
    public function submit(Request $request)
    {
        // session, referer, สมาชิก
        if ($request->initSession() && $request->isReferer() && $login = Login::isMember()) {
            $ret = array();
            // ค่าที่ส่งมา
            $save = array(
                'username' => Text::username($request->post('register_username')),
                'name' => Text::topic($request->post('register_name')),
                'phone' => Text::filter('0-9', $request->post('register_phone')),
                'status' => (int) $request->post('register_status'),
            );
            // ตรวจสอบรายการที่เลือก
            $user = self::get((int) $request->post('register_id'));
            if ($user && ($user->id == $login['id'] || $login['status'] == 1)) {
                // ตรวจสอบค่าที่ส่งมา
                if (empty($save['username'])) {
                    $ret['ret_register_username'] = 'กรุณากรอก ชื่อผู้ใช้';
                } else {
                    // ตรวจสอบ username ซ้ำ
                    $search = $this->db->first('user', array('username' => $save['username']));
                    if ($search && $search->id != $user->id) {
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
                if ($password != '' || $repassword != '') {
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
                } elseif ($user->username != $save['username']) {
                    // มีการเปลี่ยนแปลง username ต้องกรอกรหัสผ่าน
                    $ret['ret_register_password'] = 'กรุณากรอกรหัสผ่าน';
                }
                if (empty($ret)) {
                    if ($login['status'] != 1 || $user->id == 1) {
                        unset($save['status']);
                    }
                    // save
                    $this->db->edit('user', $user->id, $save);
                    // คืนค่า
                    $ret['alert'] = 'บันทึกเรียบร้อย';
                    $ret['location'] = 'back';
                }
            } else {
                $ret['alert'] = 'ไม่พบสมาชิกที่เลือก';
            }
            echo json_encode($ret);
        }
    }
}
