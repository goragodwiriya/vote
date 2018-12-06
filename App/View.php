<?php
/**
 * @filesource App/View.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace App;

/**
 * View base class สำหรับ GCMS.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Somtum\View
{
    /**
     * ouput เป็น HTML.
     *
     * @param string|null $template HTML Template ถ้าไม่กำหนด (null) จะใช้ index.html
     *
     * @return string
     */
    public function renderHTML($template = null)
    {
        // เนื้อหา
        parent::setContents(array(
            '/\[code(=([a-z]{1,}))?\](.*?)\[\/code\]/is' => '<code><a class="copytoclipboard notext" title="สำเนาไปยังคลิปบอร์ด"><span class="icon-copy"></span></a><div class="content-code \\2">\\3</div></code>',
            '/([^:])(\/\/\s[^\r\n]+)/' => '\\1<span class=comment>\\2</span>',
            '/(\/\*(.*?)\*\/)/s' => '<span class=comment>\\1</span>',
            '/(&lt;!--(.*?)--&gt;)/uis' => '<span class=comment>\\1</span>',
            '/([^["]]|\r|\n|\s|\t|^)((ftp|https?):\/\/([a-z0-9\.\-_]+)\/([^\s<>\"\']{1,})([^\s<>\"\']{20,20}))/i' => '\\1<a href="\\2" target="_blank">\\3://\\4/...\\6</a>',
            '/([^["]]|\r|\n|\s|\t|^)((ftp|https?):\/\/([^\s<>\"\']+))/i' => '\\1<a href="\\2" target="_blank">\\2</a>',
            '/(<a[^>]+>)(https?:\/\/[^\%<]+)([\%][^\.\&<]+)([^<]{5,})(<\/a>)/i' => '\\1\\2...\\4\\5',
            '/\[youtube\]([a-z0-9-_]+)\[\/youtube\]/i' => '<div class="youtube"><iframe src="//www.youtube.com/embed/\\1?wmode=transparent"></iframe></div>',
        ));

        return parent::renderHTML($template);
    }
}
