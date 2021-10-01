<?php

namespace App\Models;

use App\Traits\MagicMethodsTrait;
use App\Transformers\NotificationTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;
    use MagicMethodsTrait;

    const TABLE = 'notifications';
    const ID = 'id';
    const TYPE = 'type';
    const NOTIFIABLE_TYPE = 'notifiable_type';
    const NOTIFIABLE_ID = 'notifiable_id';
    const DATA = 'data';
    const READ_AT = 'read_at';

    /**
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'id', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at'
    ];

    protected $searchables = [
        'exact' => ['notifiable_id'],
    ];

    protected $sortables = ['id', 'created_at'];

    /**
     * @var string[]
     */
    protected $casts = [
        self::DATA => 'array',
    ];

    public function getDefaultTransformer()
    {
        return new NotificationTransformer();
    }


    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * @param Builder $builder Builder.
     *
     * @return Builder
     */
    public function scopeWhereRead(Builder $builder): Builder
    {
        return $builder->whereNotNull(self::READ_AT);
    }

    /**
     * @param Builder $builder Builder.
     *
     * @return Builder
     */
    public function scopeWhereNotRead(Builder $builder): Builder
    {
        return $builder->whereNull(self::READ_AT);
    }

    /**
     * @return Notification
     */
    public function read(): Notification
    {
        $this->setReadAt(now());
        $this->save();

        return $this;
    }
}
