<?php


namespace App\Traits;


use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;

trait Taggable
{
    /**
     * @return mixed
     */
    public function tags()
    {
        return $this->morphToMany(
            Tag::class,
            'taggable',
            'taggables',
            'taggable_id',
            'tag',
            'id',
            'name'
        );
    }

    /**
     * @return mixed
     */
    public function tagNames() {
        return $this->tags()->pluck('tag')->all();
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeWithTags(Builder $builder, ...$tags) {
        $tags = !is_array($tags) ? [$tags] : $tags;

        if (!empty($tags)) {
            $builder->whereHas('tags', function (Builder $query) use ($tags) {
                $query->whereIn('name', $tags);
            });
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     * @param array $ids
     * @return Builder
     */
    public function scopeWithTagIds(Builder $builder, array $ids) {
        if (!empty($ids)) {
            $builder->whereHas('tags', function (Builder $query) use ($ids) {
                $query->whereIn('id', $ids);
            });
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeWithoutTags(Builder $builder, ...$tags) {
        $tags = !is_array($tags) ? [$tags] : $tags;

        if (!empty($tags)) {
            $builder->whereHas('tags', function (Builder $query) use ($tags) {
                $query->whereNotIn('name', $tags);
            })->orWhereDoesntHave('tags');
        }

        return $builder;
    }
}
