<?php

namespace Zx\Uphp;

use Traversable;

/**
 * Class Str
 *
 * @see https://github.com/hoaproject/Ustring/blob/master/Ustring.php
 */
class Str implements \ArrayAccess, \Countable, \IteratorAggregate
{
    private $s = '';

    public function __construct($s = '')
    {
        $this->s = (string) $s;
    }

    public function set($s)
    {
        $this->s = $s;
    }

    public function len()
    {
        return mb_strlen($this->s);
    }

    public function isEmpty()
    {
        $s = new Str($this);
        $s->strip();
        if ($s->len() > 0) {
            return false;
        }
        return true;
    }

    public function contains($val, $caseSensitive = false)
    {
        $arr = [];
        if (is_array($val)) {
            $arr = $val;
        } else {
            $arr = [$val];
        }
        $values = array_map(function($v) {
            return preg_quote($v, '/');
        }, $arr);
        $modif = 'iu';
        if ($caseSensitive) {
            $modif = 'u';
        }
        return (bool) $this->match('/('.implode('|', $values).')/' . $modif);
    }

    public function append($s)
    {
        $this->s .= ((string) $s);
        return $this;
    }

    public function prepend($s)
    {
        $this->s = ((string) $s) . $this->s;
        return $this;
    }

    /**
     * @return StrBuilder|Str|Str[]
     */
    public function explode($str, $part = null)
    {
        if ($part !== null) {
            $parts = explode($str, $this->s);
            if (!empty($parts[$part])) {
                $this->s = $parts[$part];
            } else {
                $this->s = '';
            }
            return $this;
        }
        return new StrBuilder(explode($str, $this->s));
    }

    public function strip()
    {
        $this->s = trim($this->s);
        return $this;
    }

    public function xremove($pattern)
    {
        $this->s = preg_replace($pattern, '', $this->s);
        return $this;
    }

    public function removeEmpty()
    {
        return $this->xremove('/[\s]+/iu');
    }

    public function xreplace($pattern, $replace)
    {
        $this->s = preg_replace($pattern, $replace, $this->s);
        return $this;
    }

    public function replace($search, $replace)
    {
        $this->s = str_replace($search ,$replace, $this->s);
        return $this;
    }

    public function remove($search)
    {
        $this->s = str_replace($search, '', $this->s);
        return $this;
    }

    public function match($pattern, &$matches = null, $flags = 0, $offset = 0, $global = false)
    {
        $pattern = static::safePattern($pattern);
        if (0 === $flags) {
            if (true === $global) {
                $flags = static::GROUP_BY_PATTERN;
            }
        } else {
            $flags &= ~PREG_SPLIT_OFFSET_CAPTURE;
        }
        $offset = strlen(mb_substr($this->s, 0, $offset));
        if (true === $global) {
            return preg_match_all($pattern, $this->s, $matches, $flags, $offset);
        }
        return preg_match($pattern, $this->s, $matches, $flags, $offset);
    }

    public function toLower()
    {
        $this->s = mb_strtolower($this->s);
        return $this;
    }

    public function toUpper()
    {
        $this->s = mb_strtoupper($this->s);
        return $this;
    }

    public function __toString()
    {
        return $this->s;
    }

    /**
     * Ensure that the pattern is safe for Unicode: add the “u” option.
     *
     * @param   string  $pattern    Pattern.
     * @return  string
     */
    public static function safePattern($pattern)
    {
        $delimiter = mb_substr($pattern, 0, 1);
        $options   = mb_substr(mb_strrchr($pattern, $delimiter, false), mb_strlen($delimiter));
        if (false === strpos($options, 'u')) {
            $pattern .= 'u';
        }
        return $pattern;
    }

    /**
     * Compute offset (negative, unbound etc.).
     *
     * @param   int        $offset    Offset.
     * @return  int
     */
    protected function computeOffset($offset)
    {
        $length = mb_strlen($this->s);
        if (0 > $offset) {
            $offset = -$offset % $length;
            if (0 !== $offset) {
                $offset = $length - $offset;
            }
        } elseif ($offset >= $length) {
            $offset %= $length;
        }
        return $offset;
    }

    /**
     * Get a specific chars of the current string.
     *
     * @param   int     $offset    Offset (can be negative and unbound).
     * @return  string
     */
    public function offsetGet($offset)
    {
        return mb_substr($this->s, $this->computeOffset($offset), 1);
    }

    /**
     * Set a specific character of the current string.
     *
     * @param   int     $offset    Offset (can be negative and unbound).
     * @param   string  $value     Value.
     * @return  Str
     */
    public function offsetSet($offset, $value)
    {
        $head = null;
        $offset = $this->computeOffset($offset);
        if (0 < $offset) {
            $head = mb_substr($this->s, 0, $offset);
        }
        $tail = mb_substr($this->s, $offset + 1);
        $this->s = $head . $value . $tail;
        return $this;
    }

    /**
     * Delete a specific character of the current string.
     *
     * @param   int     $offset    Offset (can be negative and unbound).
     * @return  string
     */
    public function offsetUnset($offset)
    {
        return $this->offsetSet($offset, null);
    }

    /**
     * Check if a specific offset exists.
     *
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return true;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *       </p>
     *       <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        return mb_strlen($this->s);
    }

    /**
     * Iterator over chars.
     *
     * @return  \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator(preg_split('#(?<!^)(?!$)#u', $this->s));
    }

    /**
     * String compare
     *
     * @return bool
     */
    public function equals($s, $caseSensitive = true)
    {
        if ($caseSensitive) {
            return (strcmp($this->s, (string) $s) == 0);
        } else {
            if ($this->len() != (new Str($s))->len()) {
                return false;
            }
            return $this->contains($s);
        }
    }

    /**
     * @return Str
     */
    public function copy()
    {
        $class = get_called_class();
        return new $class($this->s);
    }

    /**
     * @return Str
     */
    public function __clone()
    {
        return $this->copy();
    }
}