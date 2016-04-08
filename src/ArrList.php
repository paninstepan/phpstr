<?php

namespace Zx\Uphp;

class ArrList implements \Iterator, \Countable, \ArrayAccess
{
    /**
     * @var []
     */
    protected $data;

    /**
     * @param [] $initial
     */
    public function __construct(array $initial = [])
    {
        $this->data = $initial;
    }

        /**
     * @see \Countable::count()
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @param mixed $offset
     * @see \ArrayAccess::offsetExists(midex $offset)
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @see \ArrayAccess::offsetGet(mixed $offset)
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @see \ArrayAccess::offsetSet(mied $offset, mixed $value)
     * @return mixed
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @see \ArrayAccess::offsetUnset(mixed $offset)
     * @return mixed
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * @return ArrList
     */
    public function limit($limit, $offset = 0)
    {
        $this->data = array_slice($this->data, $offset, $limit);
        return $this;
    }

    /**
     * @return ArrList
     */
    public function change($k, $value)
    {
        if (isset($this->data[$k])) {
            $this->data[$k] = $value;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function get($k)
    {
        if (isset($this->data[$k])) {
            return $this->data[$k];
        }
        return null;
    }

    /**
     * @return ArrList
     */
    public function del($k)
    {
        if (isset($this->data[$k])) {
            unset($this->data[$k]);
            $this->data = array_values($this->data);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return $this->get(0);
    }

    /**
     * @return mixed
     */
    public function last()
    {
        return $this->get($this->len() - 1);
    }

    /**
     * @param integer $mode php sort() mode constant
     * @return ArrList
     */
    public function sort($mode = SORT_STRING)
    {
        sort($this->data, $mode);
        return $this;
    }
    
    /**
     * @return ArrList
     */
    public function clear()
    {
        $this->data = [];
        return $this;
    }

    
    /**
     * @return boolean
     */
    public function valid()
    {
        if ($this->key() === null) {
            return false;
        }
        return true;
    }

    /**
     * @see \Iterator::rewind()
     * @return void
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * @see \Iterator::next()
     * @return void
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * @see \Iterator::current()
     * @return Str
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * @see \Iterator::key()
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * @param $callback Map callback closure
     * @return ArrList
     */
    public function map($callback)
    {
        $this->data = array_map($callback, $this->data);
        return $this;
    }

    /**
     * @return ArrList
     */
    public function filter($callback = null)
    {
        $count = $this->count();
        if ($callback) {
            $this->data = array_filter($this->data, $callback);
        } else {
            $this->data = array_filter($this->data);
        }
        if ($count != $this->count()) {
            $this->data = array_values($this->data);
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function isIn($s)
    {
        return in_array($s, $this->data);
    }

    /**
     * @param $s mixed
     * @return ArrList
     */
    public function add($v)
    {
        $this->data[] = $v;
        return $this;
    }

    /**
     * @param mixed $s 
     * @param boolean $cond
     * @return ArrList
     */
    public function addIf($s, $cond)
    {
        if ($cond) {
            $this->add($s);
        }
        return $this;
    }

    /**
     * @param $array []
     * @return ArrList
     */
    public function addArray(array $array)
    {
        foreach($array as $a) {
            $this->add($a);
        }
        return $this;
    }

    /**
     * @return ArrList
     */
    public function unique()
    {
        $this->data = array_unique($this->data);
        return $this;
    }

    /**
     * @return ArrList
     */
    public function __clone()
    {
        $class = get_called_class();
        return (new $class($this->data));
    }
}