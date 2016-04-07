<?php

namespace Zx\Uphp;

class StrBuilder
{
    /**
     * @var []
     */
    private $data;
    
    public function __construct($s = '')
    {
        $this->data = [];
        if ($s) {
            $this->data[] = $s;
        }
    }

    public function map($callback)
    {
        $this->data = array_map($callback, $this->data);
        return $this;
    }

    public function filter($callback = null)
    {
        if ($callback) {
            $this->data = array_filter($this->data, $callback);
            $this->data = array_values($this->data);
        } else {
            $this->data = array_filter($this->data, function ($str) {
                if ($str->strip()->isEmpty()) {
                    return false;
                }
                return true;
            });
            $this->data = array_values($this->data);
        }
        return $this;
    }

    /**
     * @return StrBuilder
     */
    public function toLower()
    {
        foreach($this->data as $k => $v) {
            $this->data[$k]->toLower();
        }
        return $this;
    }

    /**
     * @return StrBuilder
     */
    public function toUpper()
    {
        foreach($this->data as $k => $v) {
            $this->data[$k]->toUpper();
        }
        return $this;
    }

    /**
     * @return Str
     */
    public function concat()
    {
        return $this->implode('');
    }

    /**
     * @return Str
     */
    public function implode($k)
    {
        return new Str(implode($k, $this->data));
    }
    
    public function contains($s)
    {
        foreach($this->data as $str) {
            if ($str->contains($s)){
                return true;
            }
        }
        return false;
    }

    public function isIn(Str $s)
    {
        return in_array($s, $this->data);
    }

    public function len()
    {
        return count($this->data);
    }

    /**
     * @param $s Str|string
     * @return StrBuilder
     */
    public function add($s)
    {
        $this->data[] = new Str((string)$s);
        return $this;
    }

    /**
     * @param $s Str|string 
     * @param $cond bool
     * @return StrBuilder 
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
     * @return StrBuilder
     */
    public function addArray(array $array)
    {
        foreach($array as $a) {
            $this->add($a);
        }
        return $this;
    }

    /**
     * @return StrBuilder
     */
    public function change($k, $value)
    {
        if (isset($this->data[$k])) {
            $this->data[$k] = $value;
        }
        return $this;
    }

    /**
     * @return Str
     */
    public function get($k)
    {
        if (isset($this->data[$k])) {
            return $this->data[$k];
        }
        return null;
    }

    /**
     * @return Str|null
     */
    public function first()
    {
        return $this->get(0);
    }

    /**
     * @return Str|null
     */
    public function last()
    {
        return $this->get($this->len() - 1);
    }

    /**
     * @return StrBuilder
     */
    public function sort()
    {
        sort($this->data, SORT_STRING);
        return $this;
    }

    /**
     * @return StrBuilder
     */
    public function remove(StrBuilder $toRemove)
    {
        $result = new StrBuilder();
        foreach($this->toArray() as $item) {
            if (!$toRemove->isIn($item)) {
                $result->add($item);
            }
        }
        return $result;
    }

    /**
     * @return []
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function equals(StrBuilder $b)
    {
        return $this->concat()->equals($b->concat());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->concat();
    }
}