<?php
/**
 * @filesource Modules/Index/Models/Menu.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Menu;

/**
 * Model สำหรับจัดการเมนู.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model
{
    /**
     * รายการเมนู.
     *
     * @param array $login
     *
     * @return array
     */
    public static function getMenus($login = null)
    {
        $menus = array(
            'home' => array(
                'text' => 'หน้าโหวต',
                'url' => WEB_URL,
            ),
            'vote' => array(
                'text' => 'โหวต',
                'submenus' => array(
                    'vote' => array(
                        'text' => 'ผลโหวต',
                        'url' => WEB_URL.'admin.php',
                    ),
                    'report' => array(
                        'text' => 'รายละเอียดการโหวต',
                        'url' => WEB_URL.'admin.php?module=report',
                    ),
                    'settings' => array(
                        'text' => 'ตั้งค่า',
                        'url' => WEB_URL.'admin.php?module=settings',
                    ),
                ),
            ),
            'member' => array(
                'text' => 'สมาชิก',
                'submenus' => array(
                    'welcome' => array(
                        'text' => 'รายชื่อ',
                        'url' => WEB_URL.'admin.php?module=member',
                    ),
                    'crud' => array(
                        'text' => 'ลงทะเบียน',
                        'url' => WEB_URL.'admin.php?module=register',
                    ),
                ),
            ),
            'logout' => array(
                'text' => 'ออกจากระบบ',
                'url' => WEB_URL.'admin.php?action=logout',
            ),
        );
        // ไม่ใช่แอดมิน
        if ($login['status'] != 1) {
            unset($menus['member']);
            unset($menus['vote']['submenus']['settings']);
        }

        return $menus;
    }
}
