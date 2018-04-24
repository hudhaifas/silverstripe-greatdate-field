<?php

namespace HudhaifaS\Fields;

use SilverStripe\Forms\FormField;
use SilverStripe\ORM\FieldType\DBComposite;
use SilverStripe\ORM\FieldType\DBDatetime;

define('NULL_MONTH', 13);
define('NULL_DAY', 32);

/**
 * 
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Sep 21, 2017 - 8:08:43 PM
 */
class DBGreatDate
        extends DBComposite {

    /**
     * @param array
     */
    private static $composite_db = [
        'Day' => 'Int',
        'Month' => 'Int',
        'Year' => 'Int'
    ];

    /**
     * @return boolean
     */
    public function exists() {
        return self::is_valid_year($this->getYear());
    }

    /**
     * Returns a CompositeField instance used as a default
     * for form scaffolding.
     *
     * Used by {@link SearchContext}, {@link ModelAdmin}, {@link DataObject::scaffoldFormFields()}
     *
     * @param string $title Optional. Localized title of the generated instance
     * @return FormField
     */
    public function scaffoldFormField($title = null, $params = null) {
        $field = new GreatDateField($this->getName(), $title);

        return $field;
    }

    /**
     * For backwards compatibility reasons
     * (mainly with ecommerce module),
     * this returns the amount value of the field,
     * rather than a {@link Nice()} formatting.
     */
    public function __toString() {
        return (string) "{$this->getYear()}/{$this->getMonth()}/{$this->getDay()}";
    }

    /**
     * @return int
     */
    public function getYear() {
        return $this->getField('Year');
    }

    /**
     * @param int $year
     */
    public function setYear($year, $markChanged = true) {
//        if (is_numeric($this->getYear())) {
//            $this->setField('Year', (int) $year, $markChanged);
//        } else {
//            $this->setField('Year', (int) $year, $markChanged);
//            $manipulation['fields'][$this->name . 'Year'] = DBField::create_field('Int', $this->getYear())->nullValue();
//        }

        $this->setField('Year', (int) $year, $markChanged);
        return $this;
    }

    /**
     * @return int
     */
    public function getMonth() {
        return $this->getField('Month');
    }

    /**
     * @param int $month
     */
    public function setMonth($month, $markChanged = true) {
        if (is_numeric($month) && $month >= 1 && $month <= 12) {
            $this->setField('Month', (int) $month, $markChanged);
        } else {
            $this->setField('Month', NULL_MONTH, $markChanged);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getDay() {
        return $this->getField('Day');
    }

    /**
     * @param int $day
     */
    public function setDay($day, $markChanged = true) {
        if (is_numeric($day) && $day >= 1 && $day <= 31) {
            $this->setField('Day', (int) $day, $markChanged);
        } else {
            $this->setField('Day', NULL_DAY, $markChanged);
        }

        return $this;
    }

    public function isEstimated() {
        return $this->getMonth() == NULL_MONTH || $this->getDay() == NULL_DAY;
    }

    public function isEstimatedMonth() {
        return $this->getMonth() == NULL_MONTH;
    }

    public function isEstimatedDay() {
        return $this->getDay() == NULL_DAY;
    }

    public function Nice() {
        $formattedYear = $this->getYear() > 0 ? sprintf('%04d', $this->getYear()) : sprintf('%05d', $this->getYear());

        if ($this->isEstimatedMonth()) {
            return $formattedYear;
        } else if ($this->isEstimatedDay()) {
            return sprintf("%s/%02d", $formattedYear, $this->getMonth());
        } else {
            return sprintf("%s/%02d/%02d", $formattedYear, $this->getMonth(), $this->getDay());
        }
    }

    /**
     * Returns the date in the format 24 December 2006
     */
    public function Long() {
        $bc = $this->getYear() >= 0 ? _t('Date.AD_DATE', ' A.D.') : _t('Date.BC_DATE', ' B.C.');

        if ($this->isEstimatedMonth()) {
            return _t('Date.Formal_YEAR', '{year}{bc}', [
                'year' => abs($this->getYear()),
                'bc' => $bc
            ]);
        } else if ($this->isEstimatedDay()) {
            return _t('Date.Formal_MONTH', '{month} {year}{bc}', [
                'year' => abs($this->getYear()),
                'month' => $this->monthName(),
                'bc' => $bc
            ]);
        } else {
            return _t('Date.Formal', '{month} {day}, {year}{bc}', [
                'year' => abs($this->getYear()),
                'month' => $this->monthName(),
                'day' => $this->getDay(),
                'bc' => $bc
            ]);
        }
    }

    public function Hijri() {
        $hijri = HijriCalendar::gregorianToHijri(
                        $this->isEstimatedMonth() ? 1 : $this->getMonth(), // 
                        $this->isEstimatedDay() ? 1 : $this->getDay(), // 
                        $this->getYear()
        );
        $bh = $hijri['Year'] >= 0 ? _t('Date.AH_DATE', ' A.H.') : _t('Date.BH_DATE', ' B.H.');

        if ($this->isEstimatedMonth()) {
            return _t('Date.Formal_YEAR', '{year}{bc}', [
                'year' => abs($hijri['Year']),
                'bc' => $bh
            ]);
        } else if ($this->isEstimatedDay()) {
            return _t('Date.Formal_MONTH', '{month} {year}{bc}', [
                'year' => abs($hijri['Year']),
                'month' => HijriCalendar::monthName($hijri['Month']),
                'bc' => $bh
            ]);
        } else {
            return _t('Date.Formal', '{month} {day}, {year}{bc}', [
                'year' => abs($hijri['Year']),
                'month' => HijriCalendar::monthName($hijri['Month']),
                'day' => $hijri['Day'],
                'bc' => $bh
            ]);
        }
    }

    public function Both() {
        return $this->Hijri() . _t('Date.ALMOWAFEQ', ', ') . $this->Long();
    }

    public function MonthDay($short = false) {
        if ($this->isEstimatedMonth()) {
            return '';
        } else if ($this->isEstimatedDay()) {
            return $this->monthName($short);
        } else {
            return _t('Date.Formal_MONTH_DAY', '{month} {day}', [
                'month' => $this->monthName($short),
                'day' => $this->getDay(),
            ]);
        }
    }

    function monthName($short = false) { // $month = 1..12
        if ($this->getMonth() < 1 || $this->getMonth() > 12) {
            return $this->getMonth();
        }


        static $months = [
            "Jan", "Feb", "Mar", "Apr",
            "May", "Jun", "Jul", "Aug",
            "Sep", "Oct", "Nov", "Dec"
        ];
        $key = $months[$this->getMonth() - 1];

        return $short ? _t("Date.SHORT_{$key}", $key) : _t("Date.{$key}", $key);
    }

    /**
     * Returns the number of seconds/minutes/hours/days or months since the timestamp.
     *
     * @param boolean $includeSeconds Show seconds, or just round to "less than a minute".
     * @param int $significance Minimum significant value of X for "X units ago" to display
     * @return  String
     */
    public function Ago($significance = 2, $another = null) {
        $ago = $this->DaysAgo($another);

        if ($ago >= 0) {
            return _t(
                    'Date.TIMEDIFFAGO', "{difference} ago", 'Natural language time difference, e.g. 2 hours ago', ['difference' => $this->TimeDiff($significance)]
            );
        } else {
            return _t(
                    'Date.TIMEDIFFIN', "in {difference}", 'Natural language time difference, e.g. in 2 hours', ['difference' => $this->TimeDiff($significance)]
            );
        }
    }

    /**
     * @param boolean $includeSeconds Show seconds, or just round to "less than a minute".
     * @param int $significance Minimum significant value of X for "X units ago" to display
     * @return string
     */
    public function TimeDiff($significance = 2, $another = null, $allowPast = false) {
        $ago = $this->DaysAgo($another);

        if ($ago <= 0) {
            return;
        }

        if ($ago < $significance * 30) {
            return $this->TimeDiffIn('days', $another);
        } elseif ($ago < $significance * 365) {
            return $this->TimeDiffIn('months', $another);
        } else {
            return $this->TimeDiffIn('years', $another);
        }
    }

    /**
     * Gets the time difference, but always returns it in a certain format
     *
     * @param string $format The format, could be one of these:
     * 'seconds', 'minutes', 'hours', 'days', 'months', 'years'.
     * @return string The resulting formatted period
     */
    public function TimeDiffIn($format, $another = null) {
        $ago = $this->DaysAgo($another);

        switch ($format) {
            case "days":
                $span = round($ago);
                return ($span != 1) ? "{$span} " . _t("Date.DAYS", "days") : "{$span} " . _t("Date.DAY", "day");

            case "months":
                $span = round($ago / 30);
                return ($span != 1) ? "{$span} " . _t("Date.MONTHS", "months") : "{$span} " . _t("Date.MONTH", "month");

            case "years":
                $span = round($ago / 365);
                return ($span != 1) ? "{$span} " . _t("Date.YEARS", "years") : "{$span} " . _t("Date.YEAR", "year");
        }
    }

    public function DaysAgo($another = null) {
        if ($another) {
            $anotherJd = GregorianToJD(
                    $another->isEstimatedMonth() ? 1 : $another->getMonth(), // 
                    $another->isEstimatedDay() ? 1 : $another->getDay(), // 
                    $another->getYear()
            );
        } else {
            $time = DBDatetime::now();
            $anotherJd = GregorianToJD($time->Format('m'), $time->Format('d'), $time->Format('Y'));
        }

        return $anotherJd - GregorianToJD(
                        $this->isEstimatedMonth() ? 1 : $this->getMonth(), // 
                        $this->isEstimatedDay() ? 1 : $this->getDay(), // 
                        $this->getYear()
        );
    }

    public function isBC() {
        return $this->getYear() < 0;
    }

    public static function create_great_date($year, $month = NULL_MONTH, $day = NULL_DAY) {
        $date = new DBGreatDate();

        $date->setYear($year);
        $date->setMonth($month);
        $date->setDay($day);

        return $date;
    }

    public static function is_valid_month($value) {
        return is_numeric($value) && $value >= 1 && $value <= 12;
    }

    public static function is_valid_day($value) {
        return is_numeric($value) && $value >= 1 && $value <= 31;
    }

    public static function is_valid_year($value) {
        return is_numeric($value) && ($value >= 1 || $value <= -1);
    }

}
