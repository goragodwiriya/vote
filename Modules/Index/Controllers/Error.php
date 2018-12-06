<?php
/**
 * @filesource Modules/Index/Controllers/Error.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Error;

use Somtum\Http\Request;
use Somtum\Template;

/**
 * Error Controller ถ้าไม่สามารถทำรายการได้.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Somtum\Controller
{
    /**
     * init Class.
     */
    public function __construct()
    {
        // ค่าเริ่มต้นของ Controller
        $this->title = 'Sorry, cannot find a page called Please check the URL or try the call again.';
        $this->menu = 'home';
        $this->status = 404;
    }

    /**
     * แสดงข้อผิดพลาด (เช่น 404 page not found)
     * สำหรับการเรียกโดย GLoader.
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        $template = Template::create('', '', '404');
        $template->add(array(
            '/{TOPIC}/' => $this->title,
            '/{DETAIL}/' => $this->title,
        ));

        return $template->render();
    }

    /**
     * แสดงข้อผิดพลาด (เช่น 404 page not found).
     *
     * @param string $menu
     * @param int    $status
     * @param string $message ข้อความที่จะแสดง ถ้าไม่กำหนดจะใช้ข้อความของระบบ
     *
     * @return \Somtum\Controller
     */
    public static function execute(\Somtum\Controller $controller, $status = 404, $message = '')
    {
        $obj = new static();
        $template = Template::create($controller->menu, '', '404');
        if ($message == '') {
            $message = $obj->title;
        }
        $template->add(array(
            '/{TOPIC}/' => $message,
            '/{DETAIL}/' => $message,
        ));
        $controller->title = strip_tags($message);
        $controller->menu = $controller->menu;
        $controller->status = $status;

        return $template->render();
    }
}
