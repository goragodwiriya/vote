<?php
/**
 * @filesource Modules/Index/Views/Settings.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Settings;

use Somtum\DataTable;
use Somtum\Http\Request;
use Somtum\Http\Uri;

/**
 * module=member.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Somtum\View
{
    /**
     * ตารางรายชื่อสมาชิก
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // URL สำหรับส่งให้ตาราง
        $uri = Uri::createFromGlobals(WEB_URL.'admin.php');
        // ตาราง
        $table = new DataTable(array(
            /* Uri */
            'uri' => $uri,
            /* Model */
            'model' => 'Index\Settings\Model',
            /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
            'onRow' => array($this, 'onRow'),
            /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
            'action' => 'admin.php/Index/Model/Settings/action',
            'actionCallback' => 'dataTableActionCallback',
            'actions' => array(
                array(
                    'id' => 'action',
                    'class' => 'ok',
                    'text' => 'ทำกับที่เลือก',
                    'options' => array(
                        'delete' => 'ลบ',
                    ),
                ),
                array(
                    'class' => 'button pink icon-plus',
                    'href' => $uri->withParams(array('module' => 'write')),
                    'text' => 'เพิ่ม',
                ),
            ),
            /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
            'headers' => array(
                'id' => array(
                    'text' => 'ลำดับ',
                ),
                'name' => array(
                    'text' => 'ชื่อ',
                ),
                'picture' => array(
                    'text' => 'รูปภาพ',
                    'class' => 'center',
                ),
            ),
            /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
            'cols' => array(
                'picture' => array(
                    'class' => 'center',
                ),
            ),
            /* ปุ่มแสดงในแต่ละแถว */
            'buttons' => array(
                array(
                    'class' => 'icon-edit button green',
                    'href' => $uri->withParams(array('module' => 'write', 'id' => ':id')),
                    'text' => 'แก้ไข',
                ),
            ),
        ));
        // save cookie
        setcookie('member_perPage', $table->perPage, time() + 2592000, '/', HOST, null, true);
        // คืนค่า HTML

        return $table->render();
    }

    /**
     * จัดรูปแบบการแสดงผลในแต่ละแถว.
     *
     * @param array  $item ข้อมูลแถว
     * @param int    $o    ID ของข้อมูล
     * @param object $prop กำหนด properties ของ TR
     *
     * @return array คืนค่า $item กลับไป
     */
    public function onRow($item, $o, $prop)
    {
        $item['picture'] = '<img src="'.WEB_URL.'datas/'.$item['picture'].'" style="max-height:50px;">';

        return $item;
    }
}
