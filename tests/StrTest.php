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

    public function testExplode()
    {
        $str = new Str('some string for test');
        $parts = $str->explode(' ');
        $this->assertEquals(4, count($parts));
        $this->assertEquals('some', $parts[0]);
        $this->assertEquals('string', $parts[1]);
        $this->assertTrue(is_object($parts[0]));
        $this->assertEquals('some', $str->explode(' ', 0));
        
        $str = new Str('another test string');
        $this->assertEquals('string', $str->explode(' ', 2));
    }
 
}