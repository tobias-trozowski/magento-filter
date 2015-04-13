<?php
namespace MagentoTest\Framework\Filter\TestAsset;

class ObjectWithMethods
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return ObjectWithMethods
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return ObjectWithMethods
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
