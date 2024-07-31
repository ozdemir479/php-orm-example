<?php

namespace MarlexORM;

abstract class Model
{
    protected $table;
    protected $fillable = [];

    public static function query()
    {
        return new QueryBuilder(DatabaseConnection::getConnection());
    }

    public static function create(array $attributes)
    {
        $instance = new static;
        foreach ($attributes as $key => $value) {
            if (in_array($key, $instance->fillable)) {
                $instance->$key = $value;
            }
        }
        $instance->save();
        return $instance;
    }

    public static function find($id)
    {
        $results = self::query()->table((new static)->table)->where('id', '=', $id)->get();
        return !empty($results) ? $results[0] : null;
    }

    public static function all()
    {
        $results = self::query()->table((new static)->table)->get();
        return new Collection($results);
    }

    public static function count()
    {
        $results = self::query()->table((new static)->table)->select('COUNT(*) AS count')->get();
        return $results[0]['count'];
    }

    public static function sum($column)
    {
        $results = self::query()->table((new static)->table)->select("SUM({$column}) AS sum")->get();
        return $results[0]['sum'];
    }

    public static function max($column)
    {
        $results = self::query()->table((new static)->table)->select("MAX({$column}) AS max")->get();
        return $results[0]['max'];
    }

    public static function min($column)
    {
        $results = self::query()->table((new static)->table)->select("MIN({$column}) AS min")->get();
        return $results[0]['min'];
    }

    public static function avg($column)
    {
        $results = self::query()->table((new static)->table)->select("AVG({$column}) AS avg")->get();
        return $results[0]['avg'];
    }

    public function save()
    {
        $columns = array_keys(get_object_vars($this));
        $values = array_values(get_object_vars($this));

        $query = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', array_fill(0, count($values), '?')) . ")";
        $stmt = DatabaseConnection::getConnection()->prepare($query);
        $stmt->execute($values);
    }
}
