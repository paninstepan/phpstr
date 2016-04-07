<?php

use Zx\Uphp\Str;
use Zx\Uphp\StrBuilder;
 
class StrBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testBuilder()
    {
        $builder = new StrBuilder();
        $builder->add('This')->add('is')->add('test')->add('text');
        $this->assertEquals(4, $builder->len());

        $this->assertTrue($builder->contains('text'));
        $this->assertFalse($builder->contains('hello'));
        $this->assertEquals('test', $builder->get(2));
    }

    public function testAddArray()
    {
        $array = ['this', 'is', 'test', 'text'];
        $builder = new StrBuilder();
        $builder->addArray($array);
        
        $this->assertEquals($array, $builder->toArray());
    }

    public function testImplode()
    {
        $builder = new StrBuilder();
        $builder->add('This')
            ->add('is')
            ->add('another')
            ->add('test')
            ->add('text');
        $this->assertTrue($builder->concat()->toLower()->equals('thisisanothertesttext'));
        
    }

    public function testToString()
    {
        $builder = new StrBuilder();
        $builder->add('some');
        $builder->add('test');
        $builder->add('string');

        $this->assertEquals('someteststring', (string) $builder);
    }

    public function testSort()
    {
        $builder = new StrBuilder();
        $arr = ['a','b','c','d','e'];
        shuffle($arr);
        $builder->addArray($arr);
        $builder->sort();
        $this->assertEquals('a', $builder->get(0));

        $this->assertEquals('a', $builder->first());
        $this->assertEquals('e', $builder->last());
    }

    public function testAdd()
    {
        $b = new StrBuilder();
        $b->addIf('some', true);
        $b->addIf('text', false);

        $this->assertEquals(1, $b->len());
        $this->assertEquals('some', $b->first());
        $this->assertEquals('some', $b->last());
    }

    public function testEquals()
    {
        $b1 = new StrBuilder();
        $b1->addArray(['a', 'b', 'c', 'd', 'e']);
        
        $b2 = new StrBuilder();
        $b2->addArray(['b', 'c', 'd', 'a', 'e']);

        $this->assertTrue($b2->sort()->equals($b1->sort()));
    }

    public function testRemove()
    {
        $b1 = new StrBuilder();
        $b1->addArray(['a', 'b', 'c']);
        
        $b2 = new StrBuilder();
        $b2->addArray(['b']);

        $c = $b1->remove($b2);
        $this->assertEquals(['a', 'c'], $c->toArray());
        

        $b1 = new StrBuilder();
        $b1->addArray(['a', 'b', 'c', 'd', 'e']);
        
        $b2 = new StrBuilder();
        $b2->addArray(['b', 'e']);

        $c = $b1->remove($b2);
        $this->assertEquals(['a', 'c', 'd'], $c->toArray());
        
    }

    public function testCase()
    {

        $b = new StrBuilder();
        $b->addArray(['A', 'B', 'C', 'D']);
        $b->toLower();

        $this->assertEquals(['a', 'b', 'c', 'd'], $b->toArray());
        $this->assertNotEquals(['A', 'B', 'C', 'D'],  $b->toArray());
    }

    public function testMap()
    {
        $b = new StrBuilder();
        $b->addArray(['A', 'B', 'C', 'D']);
        $b->map(function(Zx\Uphp\Str $str){
            if ($str->contains('A')) {
                $str->append('A');
            }
            return $str;
        });

        $this->assertEquals(['AA', 'B', 'C', 'D'], $b->toArray());
    }

    public function testFilter()
    {
        $b = new StrBuilder();
        $b->addArray(['a', 'b', 'c', 'd', '']);
        $b->filter();
        $this->assertEquals(['a', 'b', 'c', 'd'], $b->toArray());


        $b = new StrBuilder();
        $b->addArray(['a', false, 'b', false, 'c', false, 'd', '', null, '               ']);
        $b->filter();
        $this->assertEquals(['a', 'b', 'c', 'd'], $b->toArray());

        
        $b = new StrBuilder();
        $b->addArray(['a', false, 'b', false, 'c', false, 'd', '', null, '               ']);
        $b->filter(function (Zx\Uphp\Str $str) {
            $str->strip();
            if ($str->isEmpty() || $str->equals('a')) {
                return false;
            }
            return true;
        });
        $this->assertEquals(['b', 'c', 'd'], $b->toArray());
        
    }

    public function testConstruct()
    {
        $b = new StrBuilder('s');
        $this->assertEquals(1, $b->len());

        $b = new StrBuilder(['a', 'adfasdf', 'adfasdf', 'asdfasdf']);
        $this->assertEquals(4, $b->len());
    }

    public function testLimit()
    {
        $b = new StrBuilder(['a', 'b', 'c', 'd', 'e', 'asfasdfasdf']);
        $b->limit(2);
        $this->assertEquals('a', $b->first());
        $this->assertEquals('b', $b->last());
    }

    public function testMerge()
    {
        $b1 = new StrBuilder(['a', 'b', 'c', 'd', 'e', 'asfasdfasdf']);
        $b2 = new StrBuilder(['a', 'b', 'c', 'd', 'e', 'asfasdfasdf']);

        $b1->merge($b2);

        $this->assertEquals(6, $b1->len());
    }

    public function testClear()
    {
        $b1 = new StrBuilder(['a', 'b', 'c', 'd', 'e', 'asfasdfasdf']);
        $this->assertEquals(6, $b1->len());
        $b1->clear();
        $this->assertEquals(0, $b1->len());
    }

    public function testUnique()
    {
        $b1 = new StrBuilder(['a', 'a', 'a', 'b', 'b', 'b', 'c', 'd', 'e', 'asfasdfasdf']);
        $this->assertEquals(10, $b1->len());
        $b1->unique();
        $this->assertEquals(6, $b1->len());
    }


    
}