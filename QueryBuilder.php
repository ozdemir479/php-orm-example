<?php

namespace MarlexORM;

use PDO;

class QueryBuilder
{
    private $pdo;
    private $table;
    private $select = '*';
    private $where = [];
    private $joins = [];
    private $orderBy = [];
    private $limit;
    private $offset;
    private $groupBy = [];
    private $having = [];
    private $union = [];
    private $bindings = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }

    public function select(string $columns)
    {
        $this->select = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value)
    {
        $this->where[] = [$column, $operator, $value];
        $this->bindings[":{$column}"] = $value;
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second)
    {
        $this->joins[] = [$table, $first, $operator, $second];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC')
    {
        $this->orderBy[] = [$column, $direction];
        return $this;
    }

    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function groupBy(string $column)
    {
        $this->groupBy[] = $column;
        return $this;
    }

    public function having(string $column, string $operator, $value)
    {
        $this->having[] = [$column, $operator, $value];
        $this->bindings[":{$column}"] = $value;
        return $this;
    }

    public function union($queryBuilder)
    {
        $this->union[] = $queryBuilder;
        return $this;
    }

    private function buildQuery()
    {
        $query = "SELECT {$this->select} FROM {$this->table}";

        foreach ($this->joins as $join) {
            list($table, $first, $operator, $second) = $join;
            $query .= " JOIN {$table} ON {$first} {$operator} {$second}";
        }

        if (!empty($this->where)) {
            $query .= ' WHERE ';
            $conditions = [];
            foreach ($this->where as $condition) {
                list($column, $operator, $value) = $condition;
                $conditions[] = "{$column} {$operator} :{$column}";
            }
            $query .= implode(' AND ', $conditions);
        }

        if (!empty($this->groupBy)) {
            $query .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        if (!empty($this->having)) {
            $query .= ' HAVING ';
            $conditions = [];
            foreach ($this->having as $condition) {
                list($column, $operator, $value) = $condition;
                $conditions[] = "{$column} {$operator} :{$column}";
            }
            $query .= implode(' AND ', $conditions);
        }

        if (!empty($this->orderBy)) {
            $query .= ' ORDER BY ';
            $orders = [];
            foreach ($this->orderBy as $order) {
                list($column, $direction) = $order;
                $orders[] = "{$column} {$direction}";
            }
            $query .= implode(', ', $orders);
        }

        if (isset($this->limit)) {
            $query .= " LIMIT {$this->limit}";
        }

        if (isset($this->offset)) {
            $query .= " OFFSET {$this->offset}";
        }

        if (!empty($this->union)) {
            foreach ($this->union as $union) {
                $query .= " UNION " . $union->buildQuery();
            }
        }

        return $query;
    }

    public function get()
    {
        $stmt = $this->pdo->prepare($this->buildQuery());
        foreach ($this->bindings as $placeholder => $value) {
            $stmt->bindValue($placeholder, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
