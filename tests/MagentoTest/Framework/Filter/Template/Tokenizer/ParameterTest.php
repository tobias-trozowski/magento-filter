<?php
namespace MagentoTest\Framework\Filter\Template\Tokenizer;

use Magento\Framework\Filter\Template\Tokenizer\Parameter;

/**
 */
class ParameterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Parameter
     */
    protected $parameter;

    protected function setUp()
    {
        $this->parameter = new Parameter();
    }

    public function dataProvider()
    {
        return [
            'default' => ['foo=\'bar\'', ['foo' => 'bar']],
            'with spaces' => [' baz =\'bar\'', ['baz' => 'bar']],
            'with spaces and backslash' => [' baz =\'ba\\r\'', ['baz' => 'bar']],
        ];
    }

    // public function testGetValue($value)
    // {
    // $this->parameter->setString($value);
    // }

    /**
     * @dataProvider dataProvider
     */
    public function testTokenize($value, $expectedParams)
    {
        $this->parameter->setString($value);
        $parameter = $this->parameter->tokenize();
        $this->assertSame($expectedParams, $parameter);
    }
}
