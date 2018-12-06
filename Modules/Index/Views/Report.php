<?php
/**
 * @filesource Modules/Index/Views/Report.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Report;

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
     * @var \Index\People\Model
     */
    private $people;

    /**
     * ตารางรายชื่อสมาชิก
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        $this->people = new \Index\People\Model();
        // URL สำหรับส่งให้ตาราง
        $uri = Uri::createFromGlobals(WEB_URL.'admin.php');
        // ตาราง
        $table = new DataTable(array(
            /* Uri */
            'uri' => $uri,
            /* Model */
            'model' => 'Index\Report\Model',
            /* รายการต่อหน้า */
            'perPage' => (int) $request->cookie('report_perPage', 30),
            /* เรียงลำดับ */
            'sort' => $request->cookie('report_sort', 'create_date desc'),
            /* คอลัมน์ที่สามารถเรียงลำดับได้ */
            'sortColumns' => array('create_date', 'ip', 'user', 'vote_id'),
            /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
            'onRow' => array($this, 'onRow'),
            /* คอลัมน์ที่สามารถค้นหาได้ */
            'searchColumns' => array('ip', 'user'),
            /* ตัวเลือกด้านบนของตาราง ใช้จำกัดผลลัพท์การ query */
            'filters' => array(
                'vote_id' => array(
                    'name' => 'vote_id',
                    'default' => 0,
                    'text' => 'โหวต',
                    'options' => array(0 => 'ทั้งหมด') + $this->people->toSelect(),
                    'value' => (int) $request->request('vote_id'),
                ),
            ),
            /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
            'headers' => array(
                'create_date' => array(
                    'text' => 'เมื่อ',
                    'sort' => 'create_date',
                ),
                'ip' => array(
                    'text' => 'IP',
                    'sort' => 'ip',
                ),
                'user' => array(
                    'text' => 'โทรศัพท์',
                    'sort' => 'user',
                ),
                'vote_id' => array(
                    'text' => 'โหวต',
                    'class' => 'center',
                    'sort' => 'vote_id',
                ),
            ),
            /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
            'cols' => array(
                'vote_id' => array(
                    'class' => 'center',
                ),
            ),
        ));
        // save cookie
        setcookie('report_perPage', $table->perPage, time() + 2592000, '/', HOST, null, true);
        setcookie('report_sort', $table->sort, time() + 2592000, '/', HOST, null, true);
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
        $item['create_date'] = date('d M Y H:i:s', strtotime($item['create_date']));
        $item['ip'] = '<a href="admin.php?module=report&amp;search='.$item['ip'].'">'.$item['ip'].'</a>';
        $item['vote_id'] = $this->people->getName($item['vote_id']);

        return $item;
    }
}
