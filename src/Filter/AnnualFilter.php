<?php

namespace HudhaifaS\Filters;

use HudhaifaS\Filters\DateFilter;
use SilverStripe\ORM\DataQuery;

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Nov 11, 2016 - 8:32:02 AM
 */
class AnnualFilter
        extends DateFilter {

    protected function applyOneDate(DataQuery $query) {
        $this->model = $query->applyRelation($this->relation);

        $yearClause = $this->getDbName();

        $date = $this->getDate();

        return $query->where([
                    $yearClause => $date['y'],
        ]);
    }

}
