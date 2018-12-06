<?php
/**
 * @filesource Modules/Index/Controllers/Main.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Main;

use Somtum\Http\Request;
use Somtum\Template;

/**
 * Controller หลัก สำหรับแสดงหน้าเว็บไซต์.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Somtum\Controller
{
    /**
     * ฟังก์ชั่นแปลงชื่อโมดูลที่ส่งมาเป็น Controller Class และโหลดคลาสไว้ เช่น
     * home = Index\Home\Controller
     * person-index = Person\Index\Controller.
     *
     * @param Request $request
     * @param string  $default ถ้าไม่ระบุจะคืนค่า Error Controller
     *
     * @return string|null คืนค่าชื่อคลาส ถ้าไม่พบจะคืนค่า null
     */
    public static function parseModule($request, $default = null)
    {
        $module = strtolower($request->get('module'));
        if (!empty($module) && $module != 'index' && preg_match('/^([a-z]+)([\/\-]([a-z]+))?$/', $module, $match)) {
            // ตัวแรกเป็นตัวพิมพ์ใหญ่
            $owner = ucfirst($match[1]);
            if (empty($match[3])) {
                if (is_file(APP_PATH.'Modules/'.$owner.'/Controllers/Index.php')) {
                    $module = 'Index';
                } else {
                    $module = $owner;
                    $owner = 'Index';
                }
            } else {
                $module = ucfirst($match[3]);
            }
        } elseif (!empty($default) && preg_match('/^([a-z]+)([\/\-]([a-z]+))?$/i', $default, $match)) {
            // ตัวแรกเป็นตัวพิมพ์ใหญ่
            $owner = ucfirst($match[1]);
            if (empty($match[3])) {
                if (is_file(APP_PATH.'Modules/'.$owner.'/Controllers/Index.php')) {
                    $module = 'Index';
                } else {
                    $module = $owner;
                    $owner = 'Index';
                }
            } else {
                $module = ucfirst($match[3]);
            }
        } else {
            // ไม่มีเมนู
            return null;
        }
        // ตรวจสอบหน้าที่เรียก
        if (is_file(APP_PATH.'Modules/'.$owner.'/Controllers/'.$module.'.php')) {
            // โหลดคลาส ถ้าพบโมดูลที่เรียก
            include APP_PATH.'Modules/'.$owner.'/Controllers/'.$module.'.php';

            return $owner.'\\'.$module.'\Controller';
        }

        return null;
    }

    /**
     * หน้าหลักเว็บไซต์.
     *
     * @param Request                $request
     * @param \Index\Menu\Controller $menus
     *
     * @return string
     */
    public function execute(Request $request, $menus)
    {
        // โมดูลจาก URL ถ้าไม่มีใช้เมนูรายการแรก
        $className = self::parseModule($request, $menus->home());
        if (!$className) {
            // 404
            $className = 'Index\Error\Controller';
        }
        // create Class
        $controller = new $className();
        // main.html
        $template = Template::create('', '', 'main');
        $template->add(array(
            '/{CONTENT}/' => $controller->render($request),
        ));
        // คืนค่า controller
        $controller->detail = $template->render();

        return $controller;
    }
}
