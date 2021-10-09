<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Popup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'logo',
        'title',
        'description',
        'link',
        'duration',
        'is_repetitive',
        'repeat_period',
        'dimentions',
        'styles'
    ];

    protected $casts = [
        'dimentions' => 'array',
        'styles' => 'array'
    ];

    protected $searchables = [
        'partial' => ['title', 'description'],
        'exact' => ['id'],
    ];

    protected $sortables = ['id'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->useFallbackUrl('/images/no-avatar.jpg')
            ->useFallbackPath(public_path('/images/no-avatar.jpg'))
            ->acceptsMimeTypes(['image/jpeg', 'image/png'])
            ->singleFile();
        // ->useDisk('s3')
    }


    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_popup', 'meeting_id');
    }
}
