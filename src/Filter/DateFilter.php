<?php

namespace HudhaifaS\Filters;

use HudhaifaS\FieldType\DBGreatDate;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Nov 11, 2016 - 8:32:54 AM
 */
abstract class DateFilter
        extends PartialMatchFilter {

    protected function getDate() {
        $date = new DBGreatDate();
        $date->setValue($this->getValue());

        return [
            'd' => $date->getDay(),
            'm' => $date->getMonth(),
            'y' => $date->getYear()
        ];
    }

    protected function applyOne(DataQuery $query) {
        return $this->applyOneDate($query);
    }

    abstract protected function applyOneDate(DataQuery $query);
}
