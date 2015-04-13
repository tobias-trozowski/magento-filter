<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Filter;

use Zend\Filter\FilterInterface;

/**
 * Money format filter
 */
class Money implements FilterInterface
{

    /**
     *
     * @var string
     */
    protected $format;

    /**
     *
     * @param string $format
     */
    public function __construct($format)
    {
        $this->format = $format;
    }

    /**
     *
     * @param float $value
     * @return string
     */
    public function filter($value)
    {
        return money_format($this->format, $value);
    }
}
