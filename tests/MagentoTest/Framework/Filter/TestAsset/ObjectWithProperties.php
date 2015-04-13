<?php
namespace MagentoTest\Framework\Filter\TestAsset;

class ObjectWithProperties
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $value;

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
}
