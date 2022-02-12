<?php declare(strict_types = 1);

namespace Statistics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    public function scopeByStatKey(Builder $builder,string $key,string $boolean = 'and')
    {
        return $builder->whereJsonLength("values->$key",'>',0,$boolean);
    }

    public function scopeByStatKeys(Builder $builder,\Countable $keys,string $boolean = 'or')
    {
        $query = $builder->byStatKey(current($keys),$boolean);

        foreach ($keys as $index => $key) {
            $query->byStatKey($key,'and',$boolean);
        }

        return $query;
    }

    public static function findByStatKey(string $table,string $key):self
    {
        return (new static())
            ->newQuery()
            ->whereKey($table)
            ->byStatKey($key)
            ->first();
    }

    public static function findByManyStatKeys(string $table,\Countable $keys,string $boolean = 'or'): Collection
    {
        return (new static())
            ->newQuery()
            ->whereKey($table)
            ->where(self::whereKeys($keys,$boolean))
            ->get();
    }
}
