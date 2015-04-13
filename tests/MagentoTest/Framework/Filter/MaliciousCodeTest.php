<?php
namespace MagentoTest\Framework\Filter;

use Magento\Framework\Filter\MaliciousCode;

/**
 */
class MaliciousCodeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MaliciousCode
     */
    protected $filter;

    protected function setUp()
    {
        $this->filter = new MaliciousCode();
    }

    public function filterDataProvider()
    {
        return [
            'comments'                         => ['/* Test */', ''],
            'tabs'                             => ["Te\tst", 'Test'],
            'javascript prefix'                => ['Tejavascript:st', 'Test'],
            'import styles'                    => ['Te@importst', 'Test'],
            'js behavior in style attribute'   => ['<Test style="behavior:run()" /></Test>', '<Test ></Test>'],
            'js expression in style attribute' => ['<Test style="expression(\'run()\')" /></Test>', '<Test ></Test>'],
            'js attributes'                    => ['<div onerror="run()"></div>', '<div ></div>'],
            'tags'                             => ['<script attr="test"></script>', ''],
            'base64 usage'                     => ['<src="base64:123456789" attr="test" />', '<>'],
            'normal'                           => [
                '<html><head><title>Test</title></head><body><h1>Welcome</h1></body></html>',
                '<html><head><title>Test</title></head><body><h1>Welcome</h1></body></html>',
            ],
        ];
    }

    /**
     * @covers ::filter
     * @dataProvider filterDataProvider
     */
    public function testFilter($value, $expected)
    {
        $actual = $this->filter->filter($value);
        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::addExpression
     * @covers ::setExpressions
     */
    public function testSetAddExpression()
    {
        $this->filter->addExpression('/(.*)/is');
        $actual = $this->filter->filter('remove anything');
        $this->assertSame('', $actual);

        $this->filter->setExpressions([
            '/(remove)/is',
        ]);
        $actual = $this->filter->filter('remove anything');
        $this->assertSame(' anything', $actual);

        // text keeps the same because expressions are overwritten
        $actual = $this->filter->filter('<script attr="test"></script>');
        $this->assertSame('<script attr="test"></script>', $actual);
    }
}
