<?php
/**
 * Created by PhpStorm.
 * User: jxlwqq
 * Date: 2018/7/26
 * Time: 09:33.
 */

namespace Jxlwqq\ChineseTypesetting;

class ChineseTypesetting
{

    private $cjk = ''.
    '\x{2e80}-\x{2eff}'.
    '\x{2f00}-\x{2fdf}'.
    '\x{3040}-\x{309f}'.
    '\x{30a0}-\x{30ff}'.
    '\x{3100}-\x{312f}'.
    '\x{3200}-\x{32ff}'.
    '\x{3400}-\x{4dbf}'.
    '\x{4e00}-\x{9fff}'.
    '\x{f900}-\x{faff}';

    /**
     * 使用全部或指定的方法来纠正排版.
     * @param $text
     * @param array $methods
     * @return mixed
     * @throws \ReflectionException
     */
    public function correct($text, $methods = [])
    {
        if (empty($methods)) {
            $class = new \ReflectionClass($this);
            $methodsList = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methodsList as $methodObj) {
                $methods[] = $methodObj->name;
            }
        }
        foreach ($methods as $method) {
            if (__FUNCTION__ == $method || !method_exists($this, $method)) {
                continue;
            }
            $text = $this->$method($text);
        }

        return $text;
    }

    /**
     * 在中文与英文字母/用于数学、科学和工程的希腊字母/数字之间添加空格
     * insert a space between Chinese character and English/Greek/Number character.
     *
     * @link https://github.com/mzlogin/chinese-copywriting-guidelines#空格
     *
     * @param $text
     *
     * @return null|string|string[]
     */
    public function insertSpace($text)
    {
        $patterns = [
            'cjk_quote' => [
                '(['.$this->cjk.'])(["\'])',
                '$1 $2',
            ],
            'quote_cjk' => [
                '(["\'])(['.$this->cjk.'])',
                '$1 $2',
            ],
            'fix_quote' => [
                '(["\']+)(\s*)(.+?)(\s*)(["\']+)',
                '$1$3$5',
            ],
            'cjk_hash' => [
                '(['.$this->cjk.'])(#(\S+))',
                '$1 $2',
            ],
            'hash_cjk' => [
                '((\S+)#)(['.$this->cjk.'])',
                '$1 $3',
            ],
            'cjk_operator_ans' => [
                '(['.$this->cjk.'])([A-Za-zΑ-Ωα-ω0-9])([\+\-\*\/=&\\|<>])',
                '$1 $2 $3',
            ],
            'ans_operator_cjk' => [
                '([\+\-\*\/=&\\|<>])([A-Za-zΑ-Ωα-ω0-9])(['.$this->cjk.'])',
                '$1 $2 $3',
            ],
            'bracket' => [
                [
                    '(['.$this->cjk.'])([<\[\{\(]+(.*?)[>\]\}\)]+)(['.$this->cjk.'])',
                    '$1 $2 $4',
                ],
                [
                    'cjk_bracket' => [
                        '(['.$this->cjk.'])([<>\[\]\{\}\(\)])',
                        '$1 $2',
                    ],
                    'bracket_cjk' => [
                        '([<>\[\]\{\}\(\)])(['.$this->cjk.'])',
                        '$1 $2',
                    ],
                ],
            ],
            'fix_bracket' => [
                '([<\[\{\(]+)(\s*)(.+?)(\s*)([>\]\}\)]+)',
                '$1$3$5',
            ],
            'cjk_ans' => [
                '(['.$this->cjk.'])([A-Za-zΑ-Ωα-ω0-9`@&%\=\$\^\*\-\+\\/|\\\])',
                '$1 $2',
            ],
            'ans_cjk' => [
                '([A-Za-zΑ-Ωα-ω0-9`~!%&=;\|\,\.\:\?\$\^\*\-\+\/\\\])(['.$this->cjk.'])',
                '$1 $2',
            ],
        ];
        foreach ($patterns as $key => $value) {
            if ($key === 'bracket') {
                $old = $text;
                $new = preg_replace('/'.$value[0][0].'/iu', $value[0][1], $text);
                $text = $new;
                if ($old === $new) {
                    foreach ($value[1] as $value) {
                        $text = preg_replace('/'.$value[0].'/iu', $value[1], $text);
                    }
                }
                continue;
            }
            $text = preg_replace('/'.$value[0].'/iu', $value[1], $text);
        }

        return $text;
    }

    /**
     * 有限度的全角转半角（英文、数字、空格以及一些特殊字符等使用半角字符）
     * Limited Fullwidth to halfwidth Transformer.
     *
     * @link https://github.com/mzlogin/chinese-copywriting-guidelines#全角和半角
     *
     * @param $text
     *
     * @return null|string|string[]
     */
    public function full2Half($text)
    {
        $arr = ['０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
            '５'     => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
            'Ａ'     => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
            'Ｆ'     => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
            'Ｋ'     => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
            'Ｐ'     => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
            'Ｕ'     => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
            'Ｚ'     => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
            'ｅ'     => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
            'ｊ'     => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
            'ｏ'     => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
            'ｔ'     => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ'     => 'y', 'ｚ' => 'z',
            '－'     => '-', '　' => ' ', '／' => '/',
            '％'     => '%', '＃' => '#', '＠' => '@', '＆' => '&', '＜' => '<',
            '＞'     => '>', '［' => '[', '］' => ']', '｛' => '{', '｝' => '}',
            '＼'     => '\\', '｜' => '|', '＋' => '+', '＝' => '=', '＿' => '_',
            '＾'     => '^', '￣' => '~', '｀' => '`', ];

        return strtr($text, $arr);
    }

    /**
     * 清除 Class 属性
     * Remove Specific Class From HTML Tag.
     *
     * @param $text
     *
     * @return null|string|string[]
     */
    public function removeClass($text)
    {
        return preg_replace('#\s(class)="[^"]+"#', '', $text);
    }

    /**
     * 清除 ID 属性
     * Remove Specific Id From HTML Tag.
     *
     * @param $text
     *
     * @return null|string|string[]
     */
    public function removeId($text)
    {
        return preg_replace('#\s(id)="[^"]+"#', '', $text);
    }

    /**
     * 清除 Style 属性
     * Remove Specific Style From HTML Tag.
     *
     * @param $text
     *
     * @return null|string|string[]
     */
    public function removeStyle($text)
    {
        return preg_replace('#\s(style)="[^"]+"#', '', $text);
    }

    /**
     * 清除空段落标签.
     *
     * @param $text
     *
     * @return null|string|string[]
     */
    public function removeEmptyParagraph($text)
    {
        return preg_replace('/<p[^>]*>([\s|&nbsp;]?)<\\/p[^>]*>/', '', $text);
    }

    /**
     * 清除所有空标签.
     *
     * @param $text
     *
     * @return null|string|string[]
     */
    public function removeEmptyTag($text)
    {
        return preg_replace('/<[^\/>]*>([\s|&nbsp;]?)*<\/[^>]*>/', '', $text);
    }

    /**
     * 清除段首缩紧.
     *
     * @param $text
     *
     * @return null|string|string[]
     */
    public function removeIndent($text)
    {
        return preg_replace('/<p([^>]*)>(\s|&nbsp;)+/', '<p${1}>', $text);
    }
}
