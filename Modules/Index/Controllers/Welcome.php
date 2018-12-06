<?php
/**
 * @filesource Modules/Index/Controllers/Welcome.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Welcome;

use Somtum\Http\Request;

/**
 * Controller สำหรับแสดงหน้าเว็บ.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Somtum\Controller
{
    /**
     * หน้าเข้าระบบ
     * ให้ผลลัพท์เป็น HTML.
     *
     * @param Request $request
     */
    public function execute(Request $request)
    {
        // login
        $page = \Index\Welcome\View::login($request);
        // ไตเติลจากและเนื้อหาจาก View
        $this->title = $page->title;
        $this->detail = $page->detail;

        return $this;
    }
}
