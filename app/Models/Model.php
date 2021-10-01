<?php


namespace App\Models;


use App\Components\FilterAlgorithm;
use App\Traits\TransformIt;
use App\Transformers\DefaultTransformer;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\QueryBuilder\Filters\FiltersExact;
use Spatie\QueryBuilder\Filters\FiltersPartial;

/**
 * App\Models\Model
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Model createdAt($startDate, $endDate = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model search($search)
 * @method static \Illuminate\Database\Eloquent\Builder|Model updatedAt($startDate, $endDate = null)
 * @mixin \Eloquent
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    use TransformIt;

    protected $defaultTransformer = DefaultTransformer::class;

    protected $searchables = [];

    protected $sortables = [];

    public $showMediaFieldsOnDefaultTransformer = true;

    public function getDefaultTransformer()
    {
        return new $this->defaultTransformer;
    }

    public function getSearchables()
    {
        $searchables = [];
        foreach ($this->searchables as $type => $attributes) {
            $attributes = is_array($attributes) ? $attributes : [$attributes];
            foreach ($attributes as $attribute) {
                if ($type == 'partial') {
                    $searchables[] = FilterAlgorithm::partial($attribute);
                } elseif ($type == 'exact') {
                    $searchables[] = FilterAlgorithm::exact($attribute);
                } elseif ($type == 'scope') {
                    $searchables[] = FilterAlgorithm::scope($attribute);
                }
            }
        }

        return $searchables;
    }

    public function getSortables()
    {
        return $this->sortables;
    }

    public function scopeSearch($query, $search)
    {
        $query->where(function ($innerQuery) use ($search) {
            /** @var FilterAlgorithm $field */
            foreach ($this->getSearchables() as $field) {
                // Dirty hack for skip zeroable fields (mysql 'foo' == 0 bug)
                if (substr($field->getName(), 0, 3) == 'is_' || substr($field->getName(), 0, 4) == 'has_')
                    continue;

                $filter = $field->getFilter();
                if ($filter instanceof FiltersPartial) {
                    $innerQuery->Orwhere($field->getName(), 'LIKE', "%$search%");
                } elseif ($filter instanceof FiltersExact) {
                    $innerQuery->Orwhere($field->getName(), '=', $search);
                }
            }
        });

        return $query;
    }

    /**
     * @return array
     */
    public function getMediaFields()
    {
        $fields = [];

        if (method_exists($this, 'getRegisteredMediaCollections')) {
            $mediaCollections = $this->getRegisteredMediaCollections();
            /** @var MediaCollection $mediaCollection */
            foreach ($mediaCollections as $mediaCollection) {
                if ($this->hasMedia($mediaCollection->name)) {
                    if ($mediaCollection->singleFile) {
                        $fields[$mediaCollection->name] = $this->getMedia($mediaCollection->name)->first()->getTemporaryUrl(Carbon::now()->addMinutes(5));
                    } else {
                        $fields[$mediaCollection->name] = $this->getMedia($mediaCollection->name)->map(function ($media) {
                            /** @var Media $media */
                            return $media->getTemporaryUrl(Carbon::now()->addMinutes(5));
                        })->toArray();
                    }
                } else {
                    $fields[$mediaCollection->name] = null;
                }
            }
        }

        return $fields;
    }

    /**
     * @param bool|false $required
     * @return array
     */
    public function getMediaFieldsRules($required = false)
    {
        $fields = [];

        if (method_exists($this, 'getRegisteredMediaCollections')) {
            $mediaCollections = $this->getRegisteredMediaCollections();
            /** @var MediaCollection $mediaCollection */
            foreach ($mediaCollections as $mediaCollection) {
                $fieldName = $mediaCollection->name;
                if (!$mediaCollection->singleFile) {
                    $fields[$fieldName] = ['array'];
                    $fieldName = $fieldName . '.*';
                }

                $rules = ['bail', 'file', 'file_exists_and_readable'];
                if ($required)
                    $rules[] = 'required';
                $mimeTypes = implode(',', $mediaCollection->acceptsMimeTypes);
                if ($mimeTypes)
                    $rules[] = 'mimetypes:' . $mimeTypes;

                $fields[$fieldName] = $rules;
            }
        }

        return $fields;
    }

    protected function dateScopeProvider($query, $fieldName, $startDate, $endDate)
    {
        try {
            if (!empty($startDate))
                $startDate = Carbon::parse($startDate);

            if (!empty($endDate))
                $endDate = Carbon::parse($endDate);
        } catch (\Exception $ex) {
            return $query;
        }

        if ($this->isDateAttribute($fieldName)) {
            if ($startDate && $endDate) {
                return $query->where($fieldName, '>=', $startDate)
                    ->where($fieldName, '<=', $endDate);
            } else if ($startDate) {
                return $query->where($fieldName, '>=', $startDate);
            } else if ($endDate) {
                return $query->where($fieldName, '<=', $endDate);
            }
        }

        return $query;
    }

    public function scopeCreatedAt($query, $startDate, $endDate = null)
    {
        return $this->dateScopeProvider($query, 'created_at', $startDate, $endDate);
    }

    public function scopeUpdatedAt($query, $startDate, $endDate = null)
    {
        return $this->dateScopeProvider($query, 'updated_at', $startDate, $endDate);
    }

    protected static function booted()
    {
        parent::booted();

        static::saved(function (Model $model) {
            $validator = Validator::make(request()->all(), $model->getMediaFieldsRules());

            if (!$validator->fails()) {
                $validated = $validator->validated();

                foreach ($validated as $key => $value) {
                    /** @var InteractsWithMedia $model */
                    if (!is_array($value)) {
                        $value = [$value];
                        $model->clearMediaCollection($key);
                    }

                    foreach ($value as $file) {
                        $model->addMedia($file)->toMediaCollection($key);
                    }
                }
            }
        });
    }
}
