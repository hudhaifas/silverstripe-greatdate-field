<?php

namespace HudhaifaS\Filters;

use SilverStripe\ORM\DataQuery;

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Nov 11, 2016 - 7:31:49 AM
 */
class AnniversaryFilter
        extends DateFilter {

    protected function applyOneDate(DataQuery $query) {
        $this->model = $query->applyRelation($this->relation);

        $dayClause = $this->getDbName();
        $monthClause = $this->getDbName();

        $date = $this->getDate();
        return $query->where([
                    $dayClause => $date['d'],
                    $monthClause => $date['m'],
        ]);
    }

}
