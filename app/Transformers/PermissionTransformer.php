<?php

namespace App\Transformers;

use App\Models\Model;
use League\Fractal\TransformerAbstract;

class PermissionTransformer extends TransformerAbstract
{    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'roles' => $model->roles->transformIt()
        ];
    }
}
