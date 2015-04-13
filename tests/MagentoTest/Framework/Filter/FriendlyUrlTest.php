<?php
namespace MagentoTest\Framework\Filter;

use Magento\Framework\Filter\FriendlyUrl;

/**
 */
class FriendlyUrlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var FriendlyUrl
     */
    protected $filter;

    protected function setUp()
    {
        $this->filter = new FriendlyUrl();
    }

    public function filterDataProvider()
    {
        return [
            'special letters'            => ['äåö-ÅÄÖÜé', ''],
            'special letters with space' => ['äfoåöo Å BÄaÖrÜ é', 'foo-bar'],
            'special chars'              => ['!Å§$-%b&a?z)', 'baz'],
            'special chars with space'   => ['f!oO"- §b$a%&z', 'foo-baz'],
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
}
