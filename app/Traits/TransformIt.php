<?php


namespace App\Traits;


use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;
use Spatie\Fractal\Fractal;
use Spatie\QueryBuilder\QueryBuilder;

trait TransformIt
{
    public function transformIt(TransformerAbstract $transformer = null) {
        return $this->baseTransform($this, $transformer);
    }

    /**
     * @param $base
     * @param TransformerAbstract|null $transformer
     * @return array
     */
    public function baseTransform($base, TransformerAbstract $transformer = null) {
        /** @var Fractal $fractal */
        $fractal = null;
        $data = null;
        $defaultTransformer = null;

        if ($base instanceof Model) {
            $defaultTransformer = $base->getDefaultTransformer();
            $data = $base;
        } elseif ($base instanceof Collection) {
            $first = $base->first();
            if ($first instanceof Model) {
                $defaultTransformer = $first->getDefaultTransformer();
            }
            $data = $base;
        } elseif ($base instanceof Builder) {
            $query = QueryBuilder::for($base);

            $model = $query->getModel();

            if ($model instanceof Model) {
                $defaultTransformer = $model->getDefaultTransformer();

                // if sort parameter exists, remove all default orders
                if (!empty(request()->input('sort')))
                    $query->getQuery()->orders = null;

                $query->allowedFilters($model->getSearchables())->allowedSorts($model->getSortables());

                $search = request()->input('search');
                if (!empty($search)) {
                    $query->search($search);
                }
            }

            $perPage = request()->input('per_page');
            $perPage = is_numeric($perPage) ? (int) $perPage : 10;

            /** @var LengthAwarePaginator $data */
            $data = $query->paginate($perPage);
        }

        if ($transformer != null) {
            $fractal = fractal($data, $transformer);
        } elseif ($defaultTransformer) {
            $fractal = fractal($data, $defaultTransformer);
        } else {
            return [];
        }

        $output = $fractal->toArray();
        return $data instanceof LengthAwarePaginator ? $output : $output['data'];
    }
}
