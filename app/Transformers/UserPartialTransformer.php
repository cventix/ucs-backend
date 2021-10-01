<?php

namespace App\Transformers;

use App\Models\Model;

class UserPartialTransformer extends DefaultTransformer
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Model $model)
    {
        $data = [
            'id' => $model->id,
            'fullname' => $model->fullname,
            'email' => $model->email,
            'username' => $model->username
        ];

        $data = array_merge($data, $model->getMediaFields());

        if (!empty($model->user_type))
            $data['user_type'] = $model->user_type;

        if (!empty($model->access_level))
            $data['access_level'] = $model->access_level;

        return $data;
    }
}
