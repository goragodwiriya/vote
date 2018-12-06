<?php
/**
 * @filesource Modules/Index/Controllers/Settings.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Settings;

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
        $this->title = 'ตั้งค่าโหวต';
        $this->menu = 'vote';
        // แอดมิน
        if (Login::isAdmin()) {
            $content = '<section>';
            // breadcrumbs
            $content .= '<div class="breadcrumbs"><ul>';
            $content .= '<li><span class="icon-like">โหวต</span></li>';
            $content .= '<li><span>ตั้งค่า</span></li>';
            $content .= '</ul></div>';
            $content .= '<header><h1 class="icon-settings">'.$this->title.'</h1></header>';
            // อ่านข้อมูล JSON
            $people = new \Index\People\Model();
            // ตัวเลือกการแสดงผลโหวต
            $array = array(
                'none' => 'ไม่แสดงผล',
                'always' => 'แสดงผลตลอดเวลา',
                'after_vote' => 'หลังจากกดโหวต',
            );
            $showResult = '';
            foreach ($array as $key => $value) {
                $sel = $key == $people->getConfig('showResult') ? ' selected' : '';
                $showResult .= '<option value="'.$key.'"'.$sel.'>'.$value.'</option>';
            }
            // page.html
            $template = Template::createFromFile(ROOT_PATH.'skin/'.self::$cfg->skin.'/settings.html');
            $template->add(array(
                '/{START_VOTE_DATE}/' => self::$cfg->start_vote_date,
                '/{START_VOTE_TIME}/' => self::$cfg->start_vote_time,
                '/{END_VOTE_DATE}/' => self::$cfg->end_vote_date,
                '/{END_VOTE_TIME}/' => self::$cfg->end_vote_time,
                '/{LIST}/' => createClass('Index\Settings\View')->render($request),
                '/{SHOWRESULT}/' => $showResult,
            ));
            $content .= $template->render();
            $content .= '</section>';

            return $content;
        }
        // 404

        return \Index\Error\Controller::execute($this);
    }
}
