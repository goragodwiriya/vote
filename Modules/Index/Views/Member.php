<?php
/**
 * @filesource Modules/Index/Views/Member.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Member;

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
        // สถานะสมาชิก
        $member_status = array(-1 => 'ทั้งหมด');
        foreach (self::$cfg->member_status as $key => $value) {
            $member_status[$key] = $value;
        }
        // URL สำหรับส่งให้ตาราง
        $uri = Uri::createFromGlobals(WEB_URL.'admin.php');
        // ตาราง
        $table = new DataTable(array(
            /* Uri */
            'uri' => $uri,
            /* Model */
            'model' => 'Index\Member\Model',
            /* รายการต่อหน้า */
            'perPage' => (int) $request->cookie('member_perPage', 30),
            /* เรียงลำดับ */
            'sort' => $request->cookie('member_sort', 'id desc'),
            /* คอลัมน์ที่สามารถเรียงลำดับได้ */
            'sortColumns' => array('id', 'name', 'lastvisited'),
            /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
            'onRow' => array($this, 'onRow'),
            /* คอลัมน์ที่สามารถค้นหาได้ */
            'searchColumns' => array('name', 'username', 'phone'),
            /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
            'action' => 'admin.php/Index/Model/Member/action',
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
                    'href' => $uri->withParams(array('module' => 'register')),
                    'text' => 'สมัครสมาชิก',
                ),
            ),
            /* ตัวเลือกด้านบนของตาราง ใช้จำกัดผลลัพท์การ query */
            'filters' => array(
                'status' => array(
                    'name' => 'status',
                    'default' => -1,
                    'text' => 'สถานะ',
                    'options' => $member_status,
                    'value' => (int) $request->request('status', -1),
                ),
            ),
            /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
            'headers' => array(
                'name' => array(
                    'text' => 'ชื่อ',
                    'sort' => 'name',
                ),
                'phone' => array(
                    'text' => 'โทรศัพท์',
                    'class' => 'center',
                ),
                'status' => array(
                    'text' => 'สถานะ',
                    'class' => 'center',
                ),
                'create_date' => array(
                    'text' => 'เพิ่มเมื่อ',
                    'class' => 'center',
                ),
                'lastvisited' => array(
                    'text' => 'เข้าระบบครั้งสุดท้าย (ครั้ง)',
                    'class' => 'center',
                    'sort' => 'lastvisited',
                ),
            ),
            /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
            'cols' => array(
                'phone' => array(
                    'class' => 'center',
                ),
                'status' => array(
                    'class' => 'center',
                ),
                'create_date' => array(
                    'class' => 'center',
                ),
                'lastvisited' => array(
                    'class' => 'center',
                ),
            ),
            /* ปุ่มแสดงในแต่ละแถว */
            'buttons' => array(
                array(
                    'class' => 'icon-edit button green',
                    'href' => $uri->withParams(array('module' => 'editprofile', 'id' => ':id')),
                    'text' => 'แก้ไข',
                ),
            ),
        ));
        // save cookie
        setcookie('member_perPage', $table->perPage, time() + 2592000, '/', HOST, null, true);
        setcookie('member_sort', $table->sort, time() + 2592000, '/', HOST, null, true);
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
        $item['create_date'] = date('d M Y', strtotime($item['create_date']));
        $item['lastvisited'] = empty($item['lastvisited']) ? '-' : date('d M Y H:i', $item['lastvisited']).' ('.number_format($item['visited']).')';
        $item['status'] = isset(self::$cfg->member_status[$item['status']]) ? '<span class="term'.$item['status'].'">'.self::$cfg->member_status[$item['status']].'</span>' : 'Unknow';

        return $item;
    }
}
