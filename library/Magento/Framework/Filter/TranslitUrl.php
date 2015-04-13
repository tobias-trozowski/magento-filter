<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Filter;


class TranslitUrl extends Translit
{
    /**
     * Filter value
     *
     * @param string $string
     *
     * @return string
     */
    public function filter($string)
    {
        $string = preg_replace('#[^0-9a-z]+#i', '-', parent::filter($string));
        $string = strtolower($string);
        $string = trim($string, '-');
        return $string;
    }
}