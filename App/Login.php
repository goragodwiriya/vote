<?php
/**
 * @filesource App/Login.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace App;

use Somtum\Http\Request;

/**
 * คลาสสำหรับตรวจสอบการ Login.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Login extends \Somtum\Base
{
    /**
     * @var array
     */
    public static $login_params = array();
    /**
     * @var string
     */
    public static $login_message;
    /**
     * @var string
     */
    public static $login_input;
    /**
     * @var bool
     */
    private static $from_submit;

    /**
     * ตรวจสอบการ login เมื่อมีการเรียกใช้ class new Login
     * action=logout ออกจากระบบ
     * มาจากการ submit ตรวจสอบการ login
     * ถ้าไม่มีทั้งสองส่วนด้านบน จะตรวจสอบการ login จาก session.
     *
     * @param Request $request
     *
     * @return \static
     */
    public static function create(Request $request)
    {
        // create class
        $login = new static();
        // อ่านข้อมูลจากฟอร์ม login ฟิลด์ login_username
        self::$login_params['username'] = self::username($request->post('login_username'));
        if (empty(self::$login_params['username'])) {
            if (isset($_SESSION['login']) && isset($_SESSION['login']['username'])) {
                // session
                self::$login_params['username'] = self::username($_SESSION['login']['username']);
                if (isset($_SESSION['login']['token'])) {
                    self::$login_params['token'] = $_SESSION['login']['token'];
                }
            } else {
                self::$login_params['username'] = null;
            }
            self::$from_submit = false;
        } elseif ($request->post('login_password') !== null) {
            // มี password มา
            self::$login_params['password'] = $request->post('login_password');
            self::$from_submit = true;
        }
        $action = $request->get('action');
        // ตรวจสอบการ login
        if ($action === 'logout' && !self::$from_submit) {
            // logout ลบ session และ cookie
            unset($_SESSION['login']);
            self::$login_message = 'ออกจากระบบเรียบร้อย';
        } else {
            // ตรวจสอบค่าที่ส่งมา
            if (empty(self::$login_params['username'])) {
                if (self::$from_submit) {
                    self::$login_message = 'กรุณากรอกชื่อผู้ใช้';
                    self::$login_input = 'login_username';
                }
            } elseif (empty(self::$login_params['password']) && self::$from_submit) {
                self::$login_message = 'กรุณากรอกรหัสผ่าน';
                self::$login_input = 'login_password';
            } elseif (!self::$from_submit || (self::$from_submit && $request->isReferer())) {
                // ตรวจสอบการ login กับฐานข้อมูล
                $login_result = $login->checkLogin($request, self::$login_params);
                if (is_array($login_result)) {
                    // save login session
                    $_SESSION['login'] = $login_result;
                } else {
                    if (is_string($login_result)) {
                        // ข้อความผิดพลาด
                        self::$login_input = self::$login_input == 'password' ? 'login_password' : 'login_username';
                        self::$login_message = $login_result;
                    }
                    // logout ลบ session และ cookie
                    unset($_SESSION['login']);
                }
            }

            return $login;
        }
    }

    /**
     * ฟังก์ชั่นตรวจสอบการ login และบันทึกการเข้าระบบ
     * เข้าระบบสำเร็จคืนค่าแอเรย์ข้อมูลสมาชิก, ไม่สำเร็จ คืนค่าข้อความผิดพลาด.
     *
     * @param Request $request
     * @param array   $params  ข้อมูลการ login ที่ส่งมา $params = array('username' => '', 'password' => '');
     *
     * @return string|array
     */
    public function checkLogin(Request $request, $params)
    {
        // Database
        $db = new \App\Db();
        // ตรวจสอบ username
        $search = $db->first('user', array('username' => $params['username']));
        if ($search && isset($params['password']) && $search->password === sha1($params['password'].$search->salt)) {
            // ตรวจสอบรหัสผ่าน
            $login_result = $search;
        } elseif ($search && isset($params['token']) && $params['token'] === $search->token) {
            // ตรวจสอบ token
            $login_result = $search;
        }
        if (isset($login_result)) {
            // ip ที่ login
            $ip = $request->getClientIp();
            // current session
            $session_id = session_id();
            // token
            $login_result->token = sha1(uniqid());
            // ลบ password
            unset($login_result->password);
            // อัปเดทการเยี่ยมชม
            if ($session_id != $login_result->session_id) {
                ++$login_result->visited;
                $save = array(
                    'session_id' => $session_id,
                    'visited' => $login_result->visited,
                    'lastvisited' => time(),
                    'ip' => $ip,
                    'token' => $login_result->token,
                );
            } else {
                $save = array(
                    'token' => $login_result->token,
                );
            }
            // อัปเดทการเข้าระบบ
            $db->edit('user', $login_result->id, $save);
            // คืนค่าข้อมูลการเข้าระบบ

            return (array) $login_result;
        } else {
            // user หรือ password ไม่ถูกต้อง
            self::$login_input = $search ? 'password' : 'username';

            return $search ? 'รหัสผ่านไม่ถูกต้อง' : 'ไม่พบสมาชิกลงทะเบียนไว้';
        }
    }

    /**
     * คืนค่า username
     * ภาษาอังกฤษตัวพิมพ์เล็ก และ อีเมล์.
     *
     * @param string $username
     *
     * @return string
     */
    public static function username($username)
    {
        return preg_replace('/[^a-z0-9@\.\-\_]+/', '', $username);
    }

    /**
     * ฟังก์ชั่นตรวจสอบสถานะแอดมิน
     * คืนค่าข้อมูลสมาชิก (แอเรย์) ถ้าเป็นผู้ดูแลระบบและเข้าระบบแล้ว ไม่ใช่คืนค่า null.
     *
     * @return array|null
     */
    public static function isAdmin()
    {
        $login = self::isMember();

        return isset($login['status']) && $login['status'] == 1 ? $login : null;
    }

    /**
     * ฟังก์ชั่นตรวจสอบการเข้าระบบ
     * คืนค่าข้อมูลสมาชิก (แอเรย์) ถ้าเป็นสมาชิกและเข้าระบบแล้ว ไม่ใช่คืนค่า null.
     *
     * @return array|null
     */
    public static function isMember()
    {
        return empty($_SESSION['login']) ? null : $_SESSION['login'];
    }
}
