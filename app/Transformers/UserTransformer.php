<?php

namespace App\Transformers;

use App\Models\Model;
use League\Fractal\TransformerAbstract;

class UserTransformer extends DefaultTransformer
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Model $model)
    {
        $data = parent::transform($model);

        $data['roles'] = $model->roles->transformIt();

        return $data;
    }
}
