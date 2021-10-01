<?php


namespace App\Components;


use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\Filter;

class FilterAlgorithm extends AllowedFilter
{
    /**
     * @return Filter
     */
    public function getFilter() {
        return $this->filterClass;
    }
}
