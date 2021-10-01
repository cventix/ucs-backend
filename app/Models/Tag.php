<?php


namespace App\Models;


/**
 * App\Models\Tag
 *
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|Model createdAt($startDate, $endDate = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model search($search)
 * @method static \Illuminate\Database\Eloquent\Builder|Model updatedAt($startDate, $endDate = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereName($value)
 * @mixin \Eloquent
 */
class Tag extends Model
{
    protected $primaryKey = 'name';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    protected $searchables = [
        'name',
    ];

    protected $sortables = [
        'name',
    ];

}
