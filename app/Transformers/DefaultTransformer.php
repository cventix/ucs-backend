<?php


namespace App\Transformers;


use App\Models\Model;
use League\Fractal\TransformerAbstract;

class DefaultTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @param Model $model
     * @return array
     */
    public function transform(Model $model)
    {
        $data = $model->toArray();

        if ($model->showMediaFieldsOnDefaultTransformer) {
            $data = array_merge($data, $model->getMediaFields());
        }

        if (method_exists($model, 'tagNames')) {
            $data['tags'] = $model->tagNames();
        }

        return $data;
    }
}
