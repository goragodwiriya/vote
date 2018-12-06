<?php
/**
 * @filesource Modules/Index/Controllers/Editprofile.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Editprofile;

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
     * แก้ไขข้อมูลสมาชิก
     * ให้ผลลัพท์เป็น HTML.
     *
     * @param Request $request
     */
    public function render(Request $request)
    {
        // ไตเติล, เมนู
        $this->title = 'แก้ไขข้อมูลส่วนตัว';
        $this->menu = 'member';
        if ($login = Login::isMember()) {
            // สมาชิกที่เลือก
            $user = \Index\Editprofile\Model::get((int) $request->get('id', $login['id']));
            if ($user && $user->id > 0) {
                $content = '<section>';
                // breadcrumbs
                $content .= '<div class="breadcrumbs"><ul>';
                $content .= '<li><span class="icon-user">สมาชิก</span></li>';
                $content .= '<li><span>ข้อมูลส่วนตัว</span></li>';
                $content .= '</ul></div>';
                $content .= '<header><h1 class="icon-profile">'.$this->title.'</h1></header>';
                // สถานะสมาชิก
                $member_status = '';
                foreach (self::$cfg->member_status as $k => $v) {
                    $sel = $k == $user->status ? ' selected' : '';
                    $member_status .= '<option value="'.$k.'"'.$sel.'>'.$v.'</option>';
                }
                // page.html
                $template = Template::createFromFile(ROOT_PATH.'skin/'.self::$cfg->skin.'/editprofile.html');
                $template->add(array(
                    '/{EMAIL}/' => $user->username,
                    '/{NAME}/' => $user->name,
                    '/{PHONE}/' => $user->phone,
                    '/{STATUS}/' => $member_status,
                    '/{ID}/' => $user->id,
                    '/{ADMIN}/' => $login['status'] != 1 || $user->id == $login['id'] ? ' disabled' : '',
                ));
                $content .= $template->render();
                $content .= '</section>';

                return $content;
            }
        }
        // 404

        return \Index\Error\Controller::execute($this);
    }
}
