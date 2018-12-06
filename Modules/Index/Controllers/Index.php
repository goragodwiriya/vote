<?php
/**
 * @filesource Modules/Index/Controllers/Index.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Index;

use App\Login;
use Somtum\Http\Request;
use Somtum\Template;

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
        // session
        $request->initSession();
        // ตรวจสอบการ login
        Login::create($request);
        // กำหนด skin ให้กับ template
        Template::init('skin/'.self::$cfg->skin);
        if ($login = Login::isMember()) {
            // โหลดเมนู
            $menus = \Somtum\Menu::init(\Index\Menu\Model::getMenus($login));
            // Controller หลัก
            $page = createClass('Index\Main\Controller')->execute($request, $menus);
            $bodyclass = 'mainpage';
        } else {
            //  login
            $page = createClass('Index\Welcome\Controller')->execute($request);
            $bodyclass = 'loginpage';
        }
        // View
        $view = new \App\View();
        // Meta
        $view->setMetas(array(
            'description' => '<meta name=description content="'.$page->description().'" />',
            'keywords' => '<meta name=keywords content="'.$page->keywords().'" />',
        ));
        // เนื้อหา
        $view->setContents(array(
            // main template
            '/{MAIN}/' => $page->detail(),
            // title
            '/{TITLE}/' => $page->title(),
            // body class name
            '/{BODYCLASS}/' => $bodyclass,
        ));
        if ($login) {
            $view->setContents(array(
                // เมนู
                '/{MENUS}/' => isset($menus) ? $menus->render($page->menu()) : '',
                '/{LOGINNAME}/' => $login['name'] == '' ? $login['username'] : $login['name'],
            ));
        }
        // ส่งออก เป็น HTML
        echo $view->renderHTML();
    }
}
