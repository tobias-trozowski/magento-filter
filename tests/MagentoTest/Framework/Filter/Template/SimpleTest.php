<?php
namespace MagentoTest\Framework\Filter\Template;

/**
 */
class SimpleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::filter
     */
    public function testFilter()
    {
        $values = [
            'first name' => 'User',
            'dob'        => 'Feb 29, 2000',
        ];
        $filter = new \Magento\Framework\Filter\Template\Simple($values);
        $template = 'My name is "{{first name}}" and my date of birth is {{dob}}.';
        $actual = $filter->filter($template);
        $expected = 'My name is "User" and my date of birth is Feb 29, 2000.';
        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::setTags
     * @covers ::setData
     * @dataProvider setTagsDataProvider
     */
    public function testSetTags($startTag, $endTag)
    {
        $values = [
            'pi' => '3.14',
        ];
        $filter = new \Magento\Framework\Filter\Template\Simple($values);

        $filter->setTags($startTag, $endTag);
        $template = "PI = {$startTag}pi{$endTag}";
        $actual = $filter->filter($template);
        $expected = 'PI = 3.14';
        $this->assertSame($expected, $actual);
    }

    /**
     *
     * @return array
     */
    public function setTagsDataProvider()
    {
        return [
            '(brackets)' => ['(', ')'],
            '#hash#'     => ['#', '#'],
        ];
    }
}
