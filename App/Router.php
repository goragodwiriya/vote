<?php

/**
 * @filesource App/Router.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace App;

/**
 * Router class.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Router extends \Somtum\Router
{
    /**
     * กฏของ Router สำหรับการแยกหน้าเว็บไซต์.
     *
     * @var array
     */
    protected $rules = array(
        // vote/post, vote/get
        '/(vote)\/(post|get)/' => array('module', 'method'),
        // index.php/module/<model|controller|view>/folder/_dir/_method
        //'/^[a-z0-9]+\.php\/([a-z]+)\/(model)(\/([\/a-z0-9_]+)\/([a-z0-9_]+))?$/i' => array('module', '_mvc', '', '_dir', '_method'),
    );
}
