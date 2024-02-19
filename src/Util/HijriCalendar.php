<?php

namespace HudhaifaS\Util;

define('GREGORIAN_EPOCH', 1721425.5);
define('HIJRI_EPOCH', 1948439.5);

/**
 * 
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Sep 22, 2017 - 10:33:09 AM
 */
class HijriCalendar {

    static function monthName($month) { // $month = 1..12
        if ($month < 1 || $month > 12) {
            return $month;
        }

        $months = [
            "MUHARRAM", "SAFAR", "RABI_UL_AWWAL", "RABI_UL_ALTHANI",
            "JUMADA_UL_OULA", "JUMADA_UL_AKHERA", "RAJAB", "SHABAN",
            "RAMADHAN", "SHAWWAL", "THUL_QEDA", "THUL_HEJJA"
        ];
        $key = $months[$month - 1];

        return _t("GreatDate.{$key}", $key);
    }

    static function gregorianToHijri($month, $day, $year) {
        return self::jdToHijri(cal_to_jd(CAL_GREGORIAN, $month, $day, $year));
    }

    static function hijriToGregorian($m, $d, $y) {
        return explode('/', jdtogregorian(self::hijriToJulian($m, $d, $y)));
    }

    /**
     * Hijri To Julian Day Count
     * 
     * @param type $month
     * @param type $day
     * @param type $year
     * @return type
     */
    static function hijriToJulian($month, $day, $year) {
        return ($day + ceil(29.5 * ($month - 1)) + ($year - 1) * 354 + floor((3 + (11 * $year)) / 30) + HIJRI_EPOCH) - 1;
    }

    /**
     * Julian Day Count To Hijri
     * 
     * @param type $jd
     * @return type
     */
    static function jdToHijri($jd) {
        $jd = floor($jd) + 0.5;
        $year = floor(((30 * ($jd - HIJRI_EPOCH)) + 10646) / 10631);
        $month = min(12, ceil(($jd - (29 + self::hijriToJulian(1, 1, $year))) / 29.5) + 1);
        $day = ($jd - self::hijriToJulian($month, 1, $year)) + 1;

        return [
            'Month' => $month,
            'Day' => $day,
            'Year' => $year
        ];
    }

}
