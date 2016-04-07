<?php

namespace Zx\Uphp\Io;

use Zx\Uphp\Str;
use Zx\Uphp\StrBuilder;

class FileLinesIterator implements \Iterator
{
    private $filename;
    private $file;
    private $line = null;
    private $lineNum = null;
    private $skipEmpty = false;
    private $skipCallback = false;

    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this->filename = $file;
        $this->file = fopen($file, 'r');
    }

    public function skipEmpty($flag = true)
    {
        $this->skipEmpty = (bool) $flag;
        return $this;
    }

    public function skipCallback($callback)
    {
        $this->skipCallback = $callback;
        return $this;
    }

    public function lines()
    {
        $file = fopen($this->filename, 'r');
        $count = 0;
        while (($line = fgets($file)) !== false) {
            $count++;
        }
        return $count;
    }

    /**
     * @return Str[]
     */
    public function toArray()
    {
        $result = [];
        foreach ($this as $l => $n) {
            $result[] = $n->strip();
        }
        return $result;
    }

    /**
     * @return StrBuilder
     */
    public function toStrBuilder()
    {
        return new StrBuilder($this->toArray());
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return Str
     */
    public function current()
    {
        return new Str($this->line);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        if ($this->lineNum === null) {
            $this->lineNum = 0;
        } else {
            $this->lineNum++;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->lineNum;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *       Returns true on success or false on failure.
     */
    public function valid()
    {
        $this->getLine();
        if ($this->isEnd()) {
            return false;
        }
        if ($this->skipEmpty) {
            $this->skipByCallback(function(Str $line){
                if ($line->strip()->isEmpty()) {
                    return true;
                }
                return false;
            });
        }
        if ($this->skipCallback) {
            $skip = $this->skipCallback;
            $this->skipByCallback($this->skipCallback);
        }
        return (! $this->isEnd());
    }

    private function skipByCallback($callback)
    {
        if ($this->isEnd()) {
            return;
        }
        while($callback(new Str($this->line))) {
            $this->getLine();
            if ($this->isEnd()) {
                return;
            }
        }
    }

    private function isEnd()
    {
        if ($this->line === false) {
            return true;
        }
        return false;
    }

    protected function getLine()
    {
        $this->line = fgets($this->file);
        return $this->line;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        fseek($this->file, 0);
        $this->lineNum = 0;
        $this->line = null;
    }

    public function __destruct()
    {
        fclose($this->file);
    }
}