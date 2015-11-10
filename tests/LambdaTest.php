<?php
/**
 * @package axy\phpcode\compile
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\phpcode\compile\tests;

use axy\phpcode\compile\Lambda;

/**
 * coversDefaultClass axy\phpcode\compile\Lambda;
 */
class LambdaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * covers ::getInnerCode
     * covers ::getArgs
     * covers ::getOuterCode
     */
    public function testGetters()
    {
        $lambda = new Lambda('return $x;', ['x'], ['ns\Test']);
        $this->assertSame('return $x;', $lambda->getInnerCode());
        $this->assertSame(['x'], $lambda->getArgs());
        $this->assertSame(['ns\Test'], $lambda->getUsed());
    }

    /**
     * covers ::getInnerCode
     * covers ::getArgs
     * covers ::getOuterCode
     */
    public function testGettersDefaults()
    {
        $lambda = new Lambda(['$y=$x+5;', 'return $x;']);
        $this->assertSame('$y=$x+5;'.PHP_EOL.'return $x;', $lambda->getInnerCode());
        $this->assertSame([], $lambda->getArgs());
        $this->assertSame([], $lambda->getUsed());
    }

    /**
     * covers ::getOuterCode
     */
    public function testGetOuterCode()
    {
        $code = [
            '$z = $x + $y;',
            'return $z * 2;',
        ];
        $lambda = new Lambda($code, ['x', 'y'], ['\ns\Test']);
        $expected = [
            'use \ns\Test;',
            'return function ($x,$y) {',
            '$z = $x + $y;',
            'return $z * 2;',
            '};',
        ];
        $expected = implode(PHP_EOL, $expected);
        $this->assertSame($expected, $lambda->getOuterCode());
    }

    /**
     * covers ::getCallback
     * covers ::call
     * covers ::__invoke
     */
    public function testGetCallback()
    {
        $code = 'return Helper::sum($x, $y) * 2;';
        $args = ['x', 'y'];
        $uses = ['\\'.__NAMESPACE__.'\tst\Helper'];
        $lambda = new Lambda($code, $args, $uses);
        $callback = $lambda->getCallback();
        $this->assertInstanceOf('Closure', $callback);
        $this->assertSame(6, $callback(1, 2));
        $this->assertSame(10, $lambda(2, 3));
        $this->assertSame(14, $lambda->call([3, 4]));
    }

    /**
     * covers ::getContentForFile
     * covers ::save
     */
    public function testFile()
    {
        $code = '$z = $x + $y;';
        $args = ['x', 'y'];
        $uses = ['\\'.__NAMESPACE__.'\tst\Helper as Hlp'];
        $lambda = new Lambda($code, $args, $uses);
        $lambda->appendCodeLine('return Hlp::sum($x, $z) * 2;');
        $fn = __DIR__.'/tmp/compiled.php';
        if (is_file($fn)) {
            unlink($fn);
        }
        $lambda->save($fn, 'Comment');
        $this->assertFileExists($fn);
        $content = trim(file_get_contents($fn));
        $this->assertSame($content, trim($lambda->getContentForFile('Comment')));
        /** @var \Closure $callback */
        /** @noinspection PhpIncludeInspection */
        $callback = (include $fn);
        $this->assertInstanceOf('Closure', $callback);
        $this->assertSame(8, $callback(1, 2));
    }
}
