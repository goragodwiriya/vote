<?php
/**
 * @filesource Modules/Index/Controllers/Api.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Api;

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
     * หน้าหลักเว็บไซต์ (index.html)
     * ให้ผลลัพท์เป็น HTML.
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        // ตรวจสอบว่าเรียกมาจากไซต์ตัวเองหรือไม่
        if ($request->isReferer() && $request->isAjax()) {
            // ค่าที่ส่งมาจาก Router
            $module = $request->get('module');
            $method = $request->get('method');
            // ตรวจสอบความถูกต้องของ $module และ $method
            if (preg_match('/^[a-z]+$/', $module) && preg_match('/^[a-z]+$/', $method)) {
                $className = 'Index\\'.ucfirst($module).'\\Model';
                if (class_exists($className) && method_exists($className, $method)) {
                    createClass($className)->$method($request);
                }
            }
        }
    }
}
