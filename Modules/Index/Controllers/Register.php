<?php
/**
 * @filesource Modules/Index/Controllers/Register.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Register;

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
     * สมัครสมาชิก
     * ให้ผลลัพท์เป็น HTML.
     *
     * @param Request $request
     */
    public function render(Request $request)
    {
        // ไตเติล, เมนู
        $this->title = 'ลงทะเบียนสมาชิกใหม่';
        $this->menu = 'member';
        // แอดมิน
        if (Login::isAdmin()) {
            $content = '<section>';
            // breadcrumbs
            $content .= '<div class="breadcrumbs"><ul>';
            $content .= '<li><span class="icon-user">สมาชิก</span></li>';
            $content .= '<li><span>ลงทะเบียน</span></li>';
            $content .= '</ul></div>';
            $content .= '<header><h1 class="icon-register">'.$this->title.'</h1></header>';
            // สถานะสมาชิก
            $member_status = '';
            foreach (self::$cfg->member_status as $k => $v) {
                $member_status .= '<option value="'.$k.'">'.$v.'</option>';
            }
            // page.html
            $template = Template::createFromFile(ROOT_PATH.'skin/'.self::$cfg->skin.'/register.html');
            $template->add(array(
                '/{STATUS}/' => $member_status,
            ));
            $content .= $template->render();
            $content .= '</section>';

            return $content;
        }
        // 404

        return \Index\Error\Controller::execute($this);
    }
}
