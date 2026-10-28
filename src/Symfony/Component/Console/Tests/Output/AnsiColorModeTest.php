<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests\Output;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Output\AnsiColorMode;

class AnsiColorModeTest extends TestCase
{
    #[DataProvider('provideColorsConversion')]
    public function testColorsConversionToAnsi4(string $corlorHex, array $expected)
    {
        $this->assertSame((string) $expected[AnsiColorMode::Ansi4->name], AnsiColorMode::Ansi4->convertFromHexToAnsiColorCode($corlorHex));
    }

    #[DataProvider('provideColorsConversion')]
    public function testColorsConversionToAnsi8(string $corlorHex, array $expected)
    {
        $this->assertSame('8;5;'.$expected[AnsiColorMode::Ansi8->name], AnsiColorMode::Ansi8->convertFromHexToAnsiColorCode($corlorHex));
    }

    public static function provideColorsConversion(): \Generator
    {
        yield ['#606702', [
            AnsiColorMode::Ansi8->name => 100,
            AnsiColorMode::Ansi4->name => 0,
        ]];

        yield ['#f40502', [
            AnsiColorMode::Ansi8->name => 196,
            AnsiColorMode::Ansi4->name => 1,
        ]];

        yield ['#2a2a2a', [
            AnsiColorMode::Ansi8->name => 235,
            AnsiColorMode::Ansi4->name => 0,
        ]];

        yield ['#57f70f', [
            AnsiColorMode::Ansi8->name => 118,
            AnsiColorMode::Ansi4->name => 2,
        ]];

        yield ['#eec7fa', [
            AnsiColorMode::Ansi8->name => 225,
            AnsiColorMode::Ansi4->name => 7,
        ]];

        yield ['#a8a8a8', [
            AnsiColorMode::Ansi8->name => 248,
            AnsiColorMode::Ansi4->name => 7,
        ]];

        yield ['#000000', [
            AnsiColorMode::Ansi8->name => 16,
            AnsiColorMode::Ansi4->name => 0,
        ]];

        yield ['#ffffff', [
            AnsiColorMode::Ansi8->name => 231,
            AnsiColorMode::Ansi4->name => 7,
        ]];
    }

    #[DataProvider('provideAnsi4SaturationColors')]
    public function testColorsConversionToAnsi4DependsOnSaturation(string $colorHex, string $expected)
    {
        $this->assertSame($expected, AnsiColorMode::Ansi4->convertFromHexToAnsiColorCode($colorHex));
    }

    public static function provideAnsi4SaturationColors(): \Generator
    {
        // fully saturated colors keep the closest of the 8 classical colors
        yield 'red' => ['#ff0000', '1'];
        yield 'green' => ['#00ff00', '2'];
        yield 'yellow' => ['#ffff00', '3'];
        yield 'blue' => ['#0000ff', '4'];
        yield 'magenta' => ['#ff00ff', '5'];
        yield 'cyan' => ['#00ffff', '6'];
        yield 'dark red' => ['#800000', '1'];
        yield 'teal' => ['#40bf80', '6'];

        // colors with a saturation of 50% or more are rounded per channel, which can give white
        yield 'pastel red' => ['#ff8080', '7'];
        yield 'pastel green' => ['#80ff80', '7'];

        // a saturation of 25% is the lowest one that is rounded per channel
        yield 'saturation of 25%' => ['#ffbfbf', '7'];

        // colors with a saturation under 25% are black or white, depending on their brightness
        yield 'saturation below 25%' => ['#ffc0c0', '7'];
        yield 'very light pink' => ['#ffcccc', '7'];
        yield 'very light blue' => ['#ccccff', '7'];
        yield 'very light red' => ['#ffeeee', '7'];
        yield 'very light green' => ['#eeffee', '7'];
        yield 'dark desaturated red' => ['#403030', '0'];
        yield 'dark desaturated blue' => ['#303040', '0'];
        yield 'just below the brightness threshold' => ['#7f7a7a', '0'];
        yield 'brightness threshold' => ['#807a7a', '7'];

        // colors without any saturation are black or white
        yield 'black' => ['#000000', '0'];
        yield 'dark grey' => ['#404040', '0'];
        yield 'grey' => ['#808080', '7'];
        yield 'light grey' => ['#c0c0c0', '7'];
        yield 'white' => ['#ffffff', '7'];
    }

    public function testColorsConversionWithoutSharp()
    {
        $this->assertSame('8;5;102', AnsiColorMode::Ansi8->convertFromHexToAnsiColorCode('547869'));
    }

    public function testColorsConversionWithout3Characters()
    {
        $this->assertSame('8;5;241', AnsiColorMode::Ansi8->convertFromHexToAnsiColorCode('#666'));
    }

    public function testInvalidHexCode()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid "#6666" color.');

        AnsiColorMode::Ansi8->convertFromHexToAnsiColorCode('#6666');
    }
}
