<?php

namespace App\Transformers;

use App\Models\Model;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        // 'permissions'
    ];

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
            // 'permissions' => $model->permissions->transformIt()
        ];
    }

    // public function includePermissions(Model $model)
    // {
    //     $permissions = $model->permissions;
    //     return $this->collection($permissions, new PermissionTransformer());
    // }
}
