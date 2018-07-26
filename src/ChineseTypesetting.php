<?php
/**
 * Created by PhpStorm.
 * User: jxlwqq
 * Date: 2018/7/26
 * Time: 09:33
 */

namespace Jxlwqq\ChineseTypesetting;

class ChineseTypesetting
{
    /**
     * 在中文与英文字母/用于数学、科学和工程的希腊字母/数字之间添加空格
     * insert a space between Chinese character and English/Greek/Number character
     * @link https://github.com/mzlogin/chinese-copywriting-guidelines#空格
     * @param $text
     * @return null|string|string[]
     *
     */
    public function insertSpace($text)
    {
        $cjk = '' .
            '\x{2e80}-\x{2eff}' .
            '\x{2f00}-\x{2fdf}' .
            '\x{3040}-\x{309f}' .
            '\x{30a0}-\x{30ff}' .
            '\x{3100}-\x{312f}' .
            '\x{3200}-\x{32ff}' .
            '\x{3400}-\x{4dbf}' .
            '\x{4e00}-\x{9fff}' .
            '\x{f900}-\x{faff}';
        $patterns = array(
            'cjk_quote' => array(
                '([' . $cjk . '])(["\'])',
                '$1 $2'
            ),
            'quote_cjk' => array(
                '(["\'])([' . $cjk . '])',
                '$1 $2'
            ),
            'fix_quote' => array(
                '(["\']+)(\s*)(.+?)(\s*)(["\']+)',
                '$1$3$5'
            ),
            'cjk_hash' => array(
                '([' . $cjk . '])(#(\S+))',
                '$1 $2'
            ),
            'hash_cjk' => array(
                '((\S+)#)([' . $cjk . '])',
                '$1 $3'
            ),
            'cjk_operator_ans' => array(
                '([' . $cjk . '])([A-Za-zΑ-Ωα-ω0-9])([\+\-\*\/=&\\|<>])',
                '$1 $2 $3'
            ),
            'ans_operator_cjk' => array(
                '([\+\-\*\/=&\\|<>])([A-Za-zΑ-Ωα-ω0-9])([' . $cjk . '])',
                '$1 $2 $3'
            ),
            'bracket' => array(
                array(
                    '([' . $cjk . '])([<\[\{\(]+(.*?)[>\]\}\)]+)([' . $cjk . '])',
                    '$1 $2 $4'
                ),
                array(
                    'cjk_bracket' => array(
                        '([' . $cjk . '])([<>\[\]\{\}\(\)])',
                        '$1 $2'
                    ),
                    'bracket_cjk' => array(
                        '([<>\[\]\{\}\(\)])([' . $cjk . '])',
                        '$1 $2'
                    )
                )
            ),
            'fix_bracket' => array(
                '([<\[\{\(]+)(\s*)(.+?)(\s*)([>\]\}\)]+)',
                '$1$3$5'
            ),
            'cjk_ans' => array(
                '([' . $cjk . '])([A-Za-zΑ-Ωα-ω0-9`@&%\=\$\^\*\-\+\\/|\\\])',
                '$1 $2'
            ),
            'ans_cjk' => array(
                '([A-Za-zΑ-Ωα-ω0-9`~!%&=;\|\,\.\:\?\$\^\*\-\+\/\\\])([' . $cjk . '])',
                '$1 $2'
            )
        );
        foreach ($patterns as $key => $value) {
            if ($key === 'bracket') {
                $old = $text;
                $new = preg_replace('/' . $value[0][0] . '/iu', $value[0][1], $text);
                $text = $new;
                if ($old === $new) {
                    foreach ($value[1] as $value) {
                        $text = preg_replace('/' . $value[0] . '/iu', $value[1], $text);
                    }
                }
                continue;
            }
            $text = preg_replace('/' . $value[0] . '/iu', $value[1], $text);
        }
        return $text;
    }

    /**
     * 有限度的全角转半角（英文、数字、百分号、空格等使用半角字符）
     * Limited Fullwidth to halfwidth Transformer
     * @link https://github.com/mzlogin/chinese-copywriting-guidelines#全角和半角
     * @param $text
     * @return null|string|string[]
     */
    public function full2Half($text)
    {
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
            '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
            'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
            'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
            'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
            'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
            'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
            'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
            'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
            'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
            'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
            'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ' => 'y', 'ｚ' => 'z',
            '％' => '%', '　' => ' ', '．' => '.');
        return strtr($text, $arr);
    }

    /**
     * 清除 Class 属性
     * Remove Specific Class From HTML Tag
     * @param $text
     * @return null|string|string[]
     */
    public function removeClass($text)
    {
        return preg_replace('#\s(class)="[^"]+"#', '', $text);
    }

    /**
     * 清除 ID 属性
     * Remove Specific Id From HTML Tag
     * @param $text
     * @return null|string|string[]
     */
    public function removeId($text)
    {
        return preg_replace('#\s(id)="[^"]+"#', '', $text);
    }

    /**
     * 清除 Style 属性
     * Remove Specific Style From HTML Tag
     * @param $text
     * @return null|string|string[]
     */
    public function removeStyle($text)
    {
        return preg_replace('#\s(style)="[^"]+"#', '', $text);
    }

}