<?php
namespace MagentoTest\Framework\Filter;

use Magento\Framework\Filter\Template;
use MagentoTest\Framework\Filter\TestAsset\ObjectWithMethods;
use MagentoTest\Framework\Filter\TestAsset\ObjectWithProperties;

/**
 */
class TemplateTest extends \PHPUnit_Framework_TestCase
{

    public function filterDataProvider()
    {
        $variablesComplex = [
            'user' => new ObjectWithMethods('Foo Bar', 'Baz'),
            'var1' => 'value1',
            'var2' => 'value2',
            'var3' => '',
        ];
        $expectedComplex = <<<TXT
Welcome to the unit tests!

Hello, my name is Foo Bar.

Text which depends of name "Foo Bar"

Text if var2 is there

Text if var3 is not there

The End!
TXT;
        return [
            /* variable directive */
            'variable directive 1' => [
                __DIR__ . '/_files/vars.txt',
                [
                    'var1' => 'Foo Bar Baz',
                ],
                sprintf("Foo Bar Baz%s%s %s ", PHP_EOL, PHP_EOL, PHP_EOL),
            ],
            'variable directive 2' => [
                __DIR__ . '/_files/vars.txt',
                [
                    'user1' => new ObjectWithMethods('Foo Bar', 'Baz'),
                ],
                sprintf("%s%sFoo Bar Baz%s ", PHP_EOL, PHP_EOL, PHP_EOL),
            ],
            'variable directive 3' => [
                __DIR__ . '/_files/vars.txt',
                [
                    'user2' => new ObjectWithProperties('Foo Bar', 'Baz'),
                ],
                sprintf("%s%s %sFoo Bar Baz", PHP_EOL, PHP_EOL, PHP_EOL),
            ],
            'variable directive 4' => [
                __DIR__ . '/_files/vars.txt',
                [
                    'var1' => 'Foo Bar Baz',
                    'user1' => new ObjectWithMethods('Foo Bar', 'Baz'),
                    'user2' => new ObjectWithProperties('Foo Bar', 'Baz'),
                ],
                sprintf("Foo Bar Baz%s%sFoo Bar Baz%sFoo Bar Baz", PHP_EOL, PHP_EOL, PHP_EOL),
            ],
            /* depend directive */
            'depend directive (empty vars)'                     => [__DIR__ . '/_files/depend.txt', [], sprintf("%s", PHP_EOL)],
            'depend directive (variable not used in tpl)'       => [__DIR__ . '/_files/depend.txt', ['foo'], sprintf("%s", PHP_EOL)],
            'depend directive 1'                                => [__DIR__ . '/_files/depend.txt', ['var1' => 'Foo Bar Baz'], sprintf("Foo Bar Baz%s", PHP_EOL, PHP_EOL)],
            'depend directive 2'     => [__DIR__ . '/_files/depend.txt', ['var2' => 'Foo Bar Baz'], sprintf("%sFoo Bar Baz", PHP_EOL, PHP_EOL)],
            'depend directive 3'     => [__DIR__ . '/_files/depend.txt', ['var1' => 'Foo Bar Baz','var2' => 'Foo Bar Baz'], sprintf("Foo Bar Baz%sFoo Bar Baz", PHP_EOL, PHP_EOL)],

            /* condition directive */
            'if directive (empty vars)'                     => [__DIR__ . '/_files/condition.txt', [], sprintf("%s", PHP_EOL)],
            'if directive (variable not used in tpl)'       => [__DIR__ . '/_files/condition.txt', ['foo'], sprintf("%svar2 is not set", PHP_EOL)],
            'if directive 1'                                => [__DIR__ . '/_files/condition.txt', ['var1' => 'Foo Bar Baz'], sprintf("Foo Bar Baz%svar2 is not set", PHP_EOL, PHP_EOL)],
            'if directive 2'     => [__DIR__ . '/_files/condition.txt', ['var2' => 'Foo Bar Baz'], sprintf("%sFoo Bar Baz", PHP_EOL, PHP_EOL)],
            'if directive 3'     => [__DIR__ . '/_files/condition.txt', ['var1' => 'Foo Bar Baz','var2' => 'Foo Bar Baz'], sprintf("Foo Bar Baz%sFoo Bar Baz", PHP_EOL, PHP_EOL)],

            'all'              => [__DIR__ . '/_files/all.txt', $variablesComplex, $expectedComplex],
        ];
    }

    /**
     * @param $template
     * @param $variables
     * @param $expectedText
     *
     * @throws \Exception
     * @dataProvider filterDataProvider
     */
    public function testFilter($template, $variables, $expectedText)
    {
        $dataFile = file_get_contents($template);

        $filter = new Template();

        $filter->setVariables($variables);
        $actual = $filter->filter($dataFile);

        $this->assertSame($expectedText, $actual);
    }
}
