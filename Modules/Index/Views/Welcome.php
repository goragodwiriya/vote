<?php
/**
 * @filesource Modules/Index/Views/Welcome.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Welcome;

use App\Login;
use Somtum\Http\Request;
use Somtum\Template;

/**
 * Login, Forgot.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Somtum\View
{
    /**
     * ฟอร์มเข้าระบบ.
     *
     * @param Request $request
     *
     * @return object
     */
    public static function login(Request $request)
    {
        // template
        $template = Template::create('', '', 'login');
        $template->add(array(
            '/{EMAIL}/' => Login::$login_params['username'],
            '/{PASSWORD}/' => isset(Login::$login_params['password']) ? Login::$login_params['password'] : '',
            '/{MESSAGE}/' => Login::$login_message,
            '/{CLASS}/' => empty(Login::$login_message) ? 'hidden' : (empty(Login::$login_input) ? 'message' : 'error'),
        ));

        return (object) array(
            'detail' => $template->render(),
            'title' => 'เข้าระบบ '.self::$cfg->web_title,
        );
    }
}
