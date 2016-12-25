<?php

namespace app\Helpers;

use phpMorphy;

class Text
{
    public static function getWordsWithNum2Text($text, $wordMinLen = 2)
    {
        $text = preg_replace_callback('/\d+/', 'self::num2text', $text);
        $words = self::getWordsWithMinLen($text, $wordMinLen);
        if (null !== $words) {
            return $words;
        }

        return [];
    }

    public static function getWords($text):array
    {
        return preg_split('/\s+/u', preg_replace('/[^а-яА-ЯёЁ0-9\s]/u', '', mb_strtolower($text)));
    }

    public static function getWordsWithMinLen($text, $wordMinLen = 2)
    {
        if (preg_match_all('/(\w{' . $wordMinLen . ',})/iu', $text, $words)) {
            return $words[1];
        }
        return null;
    }

    /**
     * @param phpMorphy $morphy
     * @param array $words
     * @return array
     */
    public static function normalizeWords(phpMorphy $morphy, array $words): array
    {
        return array_map('mb_strtolower',
            array_map(function ($word) use ($morphy) {
                $p = $morphy->lemmatize($word, phpMorphy::IGNORE_PREDICT);
                if (!$p && $morphy->getLastPredictionType() == phpMorphy::PREDICT_BY_NONE) {
                    return $word;
                } else {
                    $parts = $morphy->getPartOfSpeech($word);
                    if (in_array('С', $parts) && !in_array('ПРЕДЛ', $parts)) {
                        $base = $morphy->castFormByGramInfo($word, null, ['ЕД', 'ИМ'], true);
                    } else if (in_array('П', $parts)) {
                        $base = $morphy->castFormByGramInfo($word, 'П', ['ЕД', 'ИМ'], true);
                    } else {
                        $base = $p;
                    }
                    return $base[0];
                }
            }, array_map('mb_strtoupper', $words))
        );
    }

    public static function num2text($num)
    {
        if (is_array($num)) {
            $num = $num[0];
        }
        $num = (int) $num;
        $m = [
            ['ноль'],
            ['-', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
            ['десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать'],
            ['-', '-', 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто'],
            ['-', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'],
            ['-', 'один', 'две']
        ];

        $r = [
            ['...ллион', '', 'а', 'ов'],
            ['тысяч', 'а', 'и', ''],
            ['миллион', '', 'а', 'ов'],
            ['миллиард', '', 'а', 'ов'],
            ['триллион', '', 'а', 'ов'],
            ['квадриллион', '', 'а', 'ов'],
            ['квинтиллион', '', 'а', 'ов']
        ];

        if ($num == 0) {
            return $m[0][0];
        }

        $o = [];
        foreach (array_reverse(str_split(str_pad($num, ceil(strlen($num) / 3) * 3, '0', STR_PAD_LEFT), 3)) as $k => $p) {
            $o[$k] = [];

            foreach ($n = str_split($p) as $kk => $pp) {
                if (!$pp) {
                    continue;
                }

                switch ($kk) {
                    case 0:
                        $o[$k][] = $m[4][$pp];
                        break;
                    case 1:
                        if ($pp == 1) {
                            $o[$k][] = $m[2][$n[2]];
                            break;
                        } else {
                            $o[$k][] = $m[3][$pp];
                        }
                        break;
                    case 2:
                        if (($k == 1) && ($pp <= 2)) {
                            $o[$k][] = $m[5][$pp];
                        } else {
                            $o[$k][] = $m[1][$pp];
                        }
                        break;
                }
            }

            $p *= 1;
            if (!$r[$k]) {
                $r[$k] = reset($r);
            }

            if ($p && $k)
                switch (true) {
                    case preg_match("/^[1]$|^\\d*[0,2-9][1]$/", $p):
                        $o[$k][] = $r[$k][0] . $r[$k][1];
                        break;
                    case preg_match("/^[2-4]$|\\d*[0,2-9][2-4]$/", $p):
                        $o[$k][] = $r[$k][0] . $r[$k][2];
                        break;
                    default:
                        $o[$k][] = $r[$k][0] . $r[$k][3];
                        break;
                }
            $o[$k] = implode(' ', $o[$k]);
        }

        return implode(' ', array_reverse($o));
    }
}