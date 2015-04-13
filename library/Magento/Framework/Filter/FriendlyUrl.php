<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Filter;

use Zend\Filter\StringToLower;
use Zend\Stdlib\StringUtils;

/**
 * Friendly url filter
 */
class FriendlyUrl extends StringToLower
{

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\Filter\FilterInterface::filter()
     */
    public function filter($value)
    {
        $wrapper = StringUtils::getWrapper(mb_detect_encoding($value), 'ASCII');
        $value = $wrapper->convert($value);
        // $value = iconv(mb_detect_encoding($value), 'ASCII//TRANSLIT', $value);
        $value = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $value);
        $value = preg_replace("/[\/_| -]+/", '-', $value);
        return parent::filter(trim($value, '-'));
    }
}
