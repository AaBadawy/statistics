<?php declare(strict_types = 1);

namespace Statistics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $guarded = [];

    protected $casts = ['values' => 'array'];

    public $timestamps = false;

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     */
    public function getIncrementing() : bool
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     */
    public function getKeyType() : string
    {
        return 'string';
    }

    /**
     * Get the table associated with the model.
     *
     */
    public function getTable() : string
    {
        return config('statistics.table', 'statistics');
    }

    public function getKeyName()
    {
        return config('statistics.primary_key_column','table');
    }

    public function scopeByStatKey(Builder $builder,string $key)
    {
        return $builder->whereJsonLength("values->$key",'>',0);
    }

    public static function findByKey(string $table,string $key)
    {
        return (new static())
            ->newQuery()
            ->whereKey($table)
            ->byStatKey($key)
            ->first();
    }
}
