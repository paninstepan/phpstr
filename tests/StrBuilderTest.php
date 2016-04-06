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
}