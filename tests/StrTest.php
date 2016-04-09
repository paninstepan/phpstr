<?php
 
use Zx\Uphp\Str;
use Zx\Uphp\StrBuilder;
 
class StrTest extends PHPUnit_Framework_TestCase
{
 
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

        $str = new Str('some string for test');
        $parts = $str->explode(' ');
        $this->assertEquals(4, count($parts));
        $this->assertEquals('some', $parts[0]);
        $this->assertEquals('string', $parts[1]);
        $this->assertTrue(is_object($parts[0]));
        $this->assertEquals('some', $str->explode(' ', 0));
        
        $str = new Str('another test string');
        $this->assertEquals('string', $str->explode(' ', 2));

        $str = new Str('Any string here');

        $this->assertTrue($str->explode(' ') instanceof StrBuilder);
        $str->append(' another');
        $this->assertTrue($str == 'Any string here another');
        $this->assertTrue($str->contains('any'));
        $this->assertTrue($str->contains('Any'));

        $this->assertFalse($str->contains('any', true));
        $this->assertTrue($str->contains('Any', true));

        $str = new Str('Hello');
        $this->assertEquals(5, $str->len());
        $this->assertEquals(5, count($str));

        $str1 = new Str('hello');
        $str2 = new Str('Hello');

        $this->assertFalse($str1->equals($str2));
        $this->assertTrue($str1->equals($str2, false));

        $str2->append('ooooo');
        $this->assertFalse($str1->equals($str2, false));

        $str1 = new Str('Проверка');
        $str2 = new Str('проверка');

        $this->assertFalse($str1->equals($str2));
        $this->assertTrue($str1->equals($str2, false));


        $str1 = new Str('Проверка');
        $str2 = clone $str1;

        $this->assertTrue($str2 instanceof Str);
        $this->assertEquals($str1, $str2);
        $str2->append(' слово');
        $this->assertNotEquals($str1, $str2);
     }
 
}