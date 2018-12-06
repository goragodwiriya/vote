<?php
/**
 * @filesource Somtum/Base.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Somtum;

/**
 * Class แม่ของระบบ
 * Class ส่วนใหญ่จะสืบทอดมาจาก Class นี้
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Base
{
    /**
     * Config class.
     *
     * @var \Somtum\Config
     */
    protected static $cfg;

    /**
     * Server request class.
     *
     * @var \Somtum\Http\Request
     */
    protected static $request;
}
