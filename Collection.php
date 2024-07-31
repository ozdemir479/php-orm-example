<?php

namespace MarlexORM;

class Collection
{
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function each(callable $callback)
    {
        foreach ($this->items as $item) {
            call_user_func($callback, $item);
        }
        return $this;
    }

    public function map(callable $callback)
    {
        return new self(array_map($callback, $this->items));
    }

    public function filter(callable $callback)
    {
        return new self(array_filter($this->items, $callback));
    }

    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    public function first(callable $callback = null)
    {
        if ($callback === null) {
            return reset($this->items);
        }
        foreach ($this->items as $item) {
            if (call_user_func($callback, $item)) {
                return $item;
            }
        }
        return null;
    }

    public function last(callable $callback = null)
    {
        if ($callback === null) {
            return end($this->items);
        }
        $items = array_reverse($this->items);
        foreach ($items as $item) {
            if (call_user_func($callback, $item)) {
                return $item;
            }
        }
        return null;
    }

    public function toArray()
    {
        return $this->items;
    }
}
