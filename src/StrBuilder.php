<?php

namespace Zx\Uphp;

class StrBuilder extends ArrList implements \Iterator, \Countable, \ArrayAccess
{
    /**
     * @param $s mixed|[]|Str  Initial data
     */
    public function __construct($initial = null)
    {
        $this->data = [];
        if ($initial) {
            if (is_array($initial)) {
                foreach ($initial as $v) {
                    $this->data[] = new Str((string) $v);
                }
            } else {
                $this->data[] = (string) $initial;
            }
        }
    }

    /**
     * @param $s mixed
     * @return ArrList
     */
    public function add($v)
    {
        return parent::add(new Str((string)$v));
    }

    /**
     * @return StrBuilder
     */
    public function filter($callback = null)
    {
        if ($callback !== null) {
            return parent::filter($callback);
        }
        if ($callback === null) {
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
        return $this->map(function (Str $str){
            return $str->toLower();
        });
    }

    /**
     * @return StrBuilder
     */
    public function toUpper()
    {
        return $this->map(function (Str $str){
            return $str->toUpper();
        });
    }

    /**
     * @return StrBuilder
     */
    public function removeEmpty()
    {
        return $this->map(function (Str $str){
            return $str->removeEmpty();
        });
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

    /**
     * @return boolean
     */
    public function contains($s)
    {
        foreach($this->data as $str) {
            if ($str->contains($s)){
                return true;
            }
        }
        return false;
    }

    /**
     * @return integer
     */
    public function len()
    {
        return count($this->data);
    }

    /**
     * @return StrBuilder
     */
    public function merge(StrBuilder $b, $createNew = false)
    {
        $o = $this;
        if ($createNew) {
            $o = new StrBuilder($this->toArray());
        }
        $first = $o->toArray(true);
        $second = $b->toArray(true);
        $result = array_merge($first, $second);
        $result = array_unique($result);
        $o->clear()->addArray($result);
        return $o;
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
        $this->data = $result->toArray();
        return $this;
    }

    /**
     * @return []
     */
    public function toArray($asStrings = false)
    {
        if ($asStrings) {
            return array_map(function ($s) {
                return (string) $s;
            }, $this->data);
        } else {
            return $this->data;
        }
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