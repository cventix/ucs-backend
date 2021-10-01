<?php

namespace App\Transformers;

use App\Models\Model;

class NotificationTransformer extends DefaultTransformer
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
            'notifiable' => $model->notifiable->transformIt(new UserPartialTransformer()),
            'data' => $model->data,
            'read_at' => $model->read_at,
            'created_at' => $model->created_at
        ];
    }
}
