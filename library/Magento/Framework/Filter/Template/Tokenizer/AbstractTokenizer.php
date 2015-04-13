<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Filter\Template\Tokenizer;

use Magento\Framework\Stdlib\StringUtils;

/**
 * Template constructions tokenizer
 */
abstract class AbstractTokenizer
{

    /**
     * Current index in string
     *
     * @var int
     */
    protected $currentIndex;

    /**
     * String for tokenize
     *
     * @var string
     */
    protected $string;

    /**
     * Move current index to next char.
     *
     * If index out of bounds returns false
     *
     * @return boolean
     */
    public function next()
    {
        if ($this->currentIndex + 1 >= StringUtils::strlen($this->string)) {
            return false;
        }

        $this->currentIndex ++;
        return true;
    }

    /**
     * Move current index to previous char.
     *
     * If index out of bounds returns false
     *
     * @return boolean
     */
    public function prev()
    {
        if ($this->currentIndex - 1 < 0) {
            return false;
        }

        $this->currentIndex --;
        return true;
    }

    /**
     * Return current char
     *
     * @return string
     */
    public function char()
    {
        return $this->string[$this->currentIndex];
    }

    /**
     * Set string for tokenize
     *
     * @param string $value
     * @return void
     */
    public function setString($value)
    {
        $this->string = $value;
        $this->reset();
    }

    /**
     * Move char index to begin of string
     *
     * @return void
     */
    public function reset()
    {
        $this->currentIndex = 0;
    }

    /**
     * Return true if current char is white-space
     *
     * @return boolean
     */
    public function isWhiteSpace()
    {
        return trim($this->char()) != $this->char();
    }

    /**
     * Tokenize string
     *
     * @return array
     */
    abstract public function tokenize();
}
