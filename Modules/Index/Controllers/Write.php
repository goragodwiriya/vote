<?php
/**
 * @filesource Modules/Index/Controllers/Write.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Write;

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
        // รายการที่เลือก
        $people = createClass('Index\People\Model')->get((int) $request->get('id'));
        $title = $people['id'] == 0 ? 'เพิ่ม' : 'แก้ไข';
        // ไตเติล, เมนู
        $this->title = $title.' ข้อมูลผู้เข้ารับการโหวต';
        $this->menu = 'vote';
        // แอดมิน
        if (Login::isAdmin()) {
            if ($people) {
                $content = '<section>';
                // breadcrumbs
                $content .= '<div class="breadcrumbs"><ul>';
                $content .= '<li><span class="icon-like">โหวต</span></li>';
                $content .= '<li><a href="admin.php?module=settings">ตั้งค่า</a></li>';
                $content .= '<li><span>'.$title.'</span></li>';
                $content .= '</ul></div>';
                $content .= '<header><h1 class="icon-write">'.$this->title.'</h1></header>';
                if (is_file(ROOT_PATH.'datas/'.$people['picture'])) {
                    $picture = WEB_URL.'datas/'.$people['picture'];
                } else {
                    $picture = WEB_URL.'skin/'.self::$cfg->skin.'/noicon.jpg';
                }
                // write.html
                $template = Template::createFromFile(ROOT_PATH.'skin/'.self::$cfg->skin.'/write.html');
                $template->add(array(
                    '/{ID}/' => $people['id'],
                    '/{NUMBER}/' => $people['number'],
                    '/{NAME}/' => $people['name'],
                    '/{PICTURE}/' => $picture,
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
