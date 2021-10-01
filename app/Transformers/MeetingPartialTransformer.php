<?php

namespace App\Transformers;

use App\Models\Model;
use League\Fractal\TransformerAbstract;

class MeetingPartialTransformer extends TransformerAbstract
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
            'title' => $model->title,
            'started_at' => $model->started_at,
            'duration' => $model->duration
        ];
    }
}
