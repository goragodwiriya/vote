<?php
/**
 * @filesource api.php.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */
/**
 * 0 (default) ปิดการแสดงผลข้อผิดพลาดของ PHP
 * 2 แสดงผลข้อผิดพลาดและคำเตือนออกทางหน้าจอ (ใช้เฉพาะตอนออกแบบเท่านั้น).
 */
define('DEBUG', 0);

// load Somtum
include 'Somtum/load.php';
// Initial Somtum Framework
$app = Somtum::createWebApplication();
$app->defaultController = 'Index\Api\Controller';
$app->defaultRouter = 'App\Router';
$app->run();
