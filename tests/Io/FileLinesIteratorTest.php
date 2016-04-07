<?php

use Zx\Uphp\Io\FileLinesIterator;

class FileLinesIteratorTest extends PHPUnit_Framework_TestCase
{
    public function testIteratorProvider()
    {
        return [
            [['first string', 'second string' , 'third string', 'fourth string'], 4]
        ];
    }

    /**
     * @dataProvider testIteratorProvider
     */
    public function testIterator($data, $count)
    {
        $filename = $this->tempFile('create', $data);
        $iterator = new FileLinesIterator($filename);

        $some = [];
        foreach($iterator as $num => $line) {
            $some[] = (string) $line->strip();
        }
        $this->assertEquals($data, $some);
        $this->assertEquals($count, $iterator->lines());
        
        $some = [];
        foreach($iterator as $num => $line) {
            $some[] = (string) $line->strip();
        }
        $this->assertEquals($data, $some);
        $this->assertEquals($count, $iterator->lines());
        
        $this->tempFile('delete', null, $filename);

    }

    public function testIteratorSkipEmptyProvider()
    {
        return [
            [['first string', '', 'second string' , '', 'third string', '', 'fourth string'],
             ['first string', 'second string', 'third string', 'fourth string']],
            
            [['second string' , '', 'third string', '', 'fourth string'],
             ['second string', 'third string', 'fourth string']],
            
            [['second string' , '', '', '', '' ,'', '', '', '', 'third string', '', 'fourth string'],
             ['second string', 'third string', 'fourth string']],
            
            [['', '', '', '', 'second string' , '', '', '', '' ,'', '', '', '', 'third string', '', 'fourth string'],
             ['second string', 'third string', 'fourth string']]
        ];
    }

    /**
     * @dataProvider testIteratorSkipEmptyProvider
     */
    public function testIteratorSkipEmpty($data, $data2)
    {
        $count = count($data);
        
        $filename = $this->tempFile('create', $data);
        $iterator = new FileLinesIterator($filename);
        $this->assertEquals($count, $iterator->lines());
        $this->assertEquals($data, $iterator->toArray());

        $iterator = new FileLinesIterator($filename);
        $iterator->skipEmpty();
        $this->assertEquals($count, $iterator->lines());
        $this->assertEquals($data2, $iterator->toArray());
        $this->tempFile('delete', null, $filename);
    }

    public function testIteratorSkipCallbackProvider()
    {
        return [
            [
                ['first string', '', 'second string' , '', 'third string', '', 'fourth string'],
                function (Zx\Uphp\Str $line) {
                    if ($line->strip()->equals('')){
                        return true;
                    }
                    return false;
                },
                ['first string', 'second string' , 'third string', 'fourth string']
            ],
            
            [
                ['second string' , '', 'third string', '', 'fourth string'],
                function (Zx\Uphp\Str $line) {
                    if ($line->contains('third')) {
                        return true;
                    }
                    return false;
                },
                ['second string' , '', '', 'fourth string']
            ],
            
            [
                ['second string' , '', 'third string', '', 'fourth string'],
                function (Zx\Uphp\Str $line) {
                    if ($line->contains('string')) {
                        return true;
                    }
                    return false;
                },
                ['', '']
            ]
            
        ];
    }

    /**
     * @dataProvider testIteratorSkipCallbackProvider
     */
    public function testIteratorSkipCallback($data, $callback, $data2)
    {
        $filename = $this->tempFile('create', $data);
        $iterator = new FileLinesIterator($filename);
        $iterator->skipCallback($callback);
        
        $this->assertEquals($iterator->toArray(), $data2);

        $this->tempFile('delete', null, $filename);
    }



    private function tempFile($command = 'create', $data = [], $filename = null)
    {
        if ($command == 'create') {
            $filename = tempnam(sys_get_temp_dir(), 'FileLinesIteratorTest');
            file_put_contents($filename, implode("\n", $data));
        } else if ($command == 'delete') {
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
        return $filename;
    }
}
