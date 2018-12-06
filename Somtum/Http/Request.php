<?php
/**
 * @filesource Somtum/Http/Request.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Somtum\Http;

/**
 * คลาสสำหรับจัดการคำร้องขอที่ส่งมาจาก Browser
 * เช่น $_POST $_GET.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Request extends \Somtum\Base
{
    /**
     * $_POST.
     *
     * @var array
     */
    private $parsedBody;

    /**
     * $_GET.
     *
     * @var array
     */
    private $queryParams;

    /**
     * ฟังก์ชั่น อ่าน ip ของ client
     * คืนค่า IP ที่อ่านได้.
     *
     * @return string|null
     */
    public function getClientIp()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $IParray = array_filter(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));

            return $IParray[0];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return null;
    }

    /**
     * ฟังก์ชั่นเริ่มต้นใช้งาน session, cookie.
     *
     * @return bool
     */
    public function initSession()
    {
        session_start();
        if (!ob_get_status()) {
            if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
                // เปิดใช้งานการบีบอัดหน้าเว็บไซต์
                ob_start('ob_gzhandler');
            } else {
                ob_start();
            }
        }

        return true;
    }

    /**
     * ตรวจสอบว่าเรียกมาโดย Ajax หรือไม่
     * คืนค่า true ถ้าเรียกมาจาก Ajax (XMLHttpRequest).
     *
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * ฟังก์ชั่น ตรวจสอบ referer
     * คืนค่า true ถ้า referer มาจากเว็บไซต์นี้.
     *
     * @return bool
     */
    public function isReferer()
    {
        $host = empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if (preg_match("/$host/ui", $referer)) {
            return true;
        } elseif (preg_match('/^(http(s)?:\/\/)(.*)(\/.*){0,}$/U', WEB_URL, $match)) {
            return preg_match("/$match[3]/ui", $referer);
        } else {
            return false;
        }
    }

    /**
     * อ่านค่าจากตัวแปร $_GET
     * คืนค่ารายการแรกที่พบ ถ้าไม่พบเลยคืนค่า $default.
     *
     * @param string $key     ชื่อตัวแปร
     * @param mixed  $default ค่าเริ่มต้นหากไม่พบตัวแปร
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->queryParams === null) {
            $this->queryParams = $this->normalize($_GET);
        }

        return isset($this->queryParams[$key]) ? $this->queryParams[$key] : $default;
    }

    /**
     * อ่านค่าจากตัวแปร $_POST
     * คืนค่ารายการแรกที่พบ ถ้าไม่พบเลยคืนค่า $default.
     *
     * @param string $key     ชื่อตัวแปร
     * @param mixed  $default ค่าเริ่มต้นหากไม่พบตัวแปร
     *
     * @return mixed
     */
    public function post($key, $default = null)
    {
        if ($this->parsedBody === null) {
            $this->parsedBody = $this->normalize($_POST);
        }

        return isset($this->parsedBody[$key]) ? $this->parsedBody[$key] : $default;
    }

    /**
     * อ่านค่าจากตัวแปร $_COOKIE
     * คืนค่ารายการแรกที่พบ ถ้าไม่พบเลยคืนค่า $default.
     *
     * @param string $key     ชื่อตัวแปร
     * @param mixed  $default ค่าเริ่มต้นหากไม่พบตัวแปร
     *
     * @return mixed
     */
    public function cookie($key, $default = null)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }

    /**
     * อ่านค่าจากตัวแปร $_SESSION
     * คืนค่ารายการแรกที่พบ ถ้าไม่พบเลยคืนค่า $default.
     *
     * @param string $key     ชื่อตัวแปร
     * @param mixed  $default ค่าเริ่มต้นหากไม่พบตัวแปร
     *
     * @return mixed
     */
    public function session($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * อ่านค่าจากตัวแปร $_POST $_GET ตามลำดับ
     * คืนค่ารายการแรกที่พบ ถ้าไม่พบเลยคืนค่า $default.
     *
     * @param string $key     ชื่อตัวแปร
     * @param mixed  $default ค่าเริ่มต้นหากไม่พบตัวแปร
     *
     * @return mixed
     */
    public function request($key, $default = null)
    {
        if ($this->parsedBody === null) {
            $this->parsedBody = $this->normalize($_POST);
        }
        if (isset($this->parsedBody[$key])) {
            // คืนค่า $_POST
            return $this->parsedBody[$key];
        }
        if ($this->queryParams === null) {
            $this->queryParams = $this->normalize($_GET);
        }
        // คืนค่า $_GET

        return isset($this->queryParams[$key]) ? $this->queryParams[$key] : $default;
    }

    /**
     * อ่านค่าจากตัวแปร $_SERVER
     * ถ้าไม่พบเลยคืนค่า $default.
     *
     * @param string $name    ชื่อตัวแปร
     * @param mixed  $default ค่าเริ่มต้นหากไม่พบตัวแปร
     *
     * @return mixed
     */
    public function server($name, $default = null)
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }

    /**
     * คืนค่าจากตัวแปร $_POST.
     *
     * @return array
     */
    public function getParsedBody()
    {
        if ($this->parsedBody === null) {
            $this->parsedBody = $this->normalize($_POST);
        }

        return $this->parsedBody;
    }

    /**
     * คืนค่าจากตัวแปร $_GET ทั้งหมด.
     *
     * @return array
     */
    public function getQueryParams()
    {
        if ($this->queryParams === null) {
            $this->queryParams = $this->normalize($_GET);
        }

        return $this->queryParams;
    }

    /**
     * กำหนดค่า parsedBody.
     *
     * @param null|array|object $data
     */
    public function withParsedBody($data)
    {
        $clone = clone $this;
        $clone->parsedBody = $data;

        return $clone;
    }

    /**
     * กำหนดค่า queryParams.
     *
     * @param array $query
     *
     * @return \static
     */
    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    /**
     * รวมแอเรย์ $_GET $_POST เป็นข้อมูลเดียวกัน.
     *
     * @param array $result  ตัวแปรเก็บผลลัพท์ สำหรับนำไปใช้งานต่อ
     * @param array $array   ตัวแปรที่ต้องการรวม เช่น $_GET $_POST
     * @param array $exclude รายการคีย์ของแอเรย์ ที่ไม่ต้องการให้รวมอยู่ในผลลัพท์
     */
    public function map(&$result, $array, $exclude = array())
    {
        foreach ($array as $key => $value) {
            if ($value != '' && !in_array($key, $exclude)) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $result[$key.'['.$k.']'] = $v;
                    }
                } else {
                    $result[$key] = $value;
                }
            }
        }
    }

    /**
     * remove slashes (/).
     *
     * @param array $vars ตัวแปร Global เช่น POST GET
     */
    private function normalize($vars)
    {
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            return $this->stripSlashes($vars);
        }

        return $vars;
    }

    /**
     * ฟังก์ชั่น remove slashes (/).
     *
     * @param array $datas
     *
     * @return array
     */
    private function stripSlashes($datas)
    {
        if (is_array($datas)) {
            foreach ($datas as $key => $value) {
                $datas[$key] = $this->stripSlashes($value);
            }

            return $datas;
        }

        return stripslashes($datas);
    }
}
