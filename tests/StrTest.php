<?php
 
use Zx\Uphp\Str;
 
class StrTest extends PHPUnit_Framework_TestCase {
 
    public function testStr()
    {
        $str = new Str();
        $str->set('test');
        $this->assertTrue($str->len() == 4);
        $this->assertTrue($str->equals('test'));
        $this->assertTrue($str->equals(new Str('test')));
        $this->assertTrue((bool)$str->contains('st'));
        $this->assertTrue((bool)$str->match('/es/'));
        $this->assertTrue('t' == $str->explode('e', 0));

        $test = '';
        foreach($str as $ch) {
            $test .= $ch;
        }
        $this->assertTrue($test == $str);
    }
 
}