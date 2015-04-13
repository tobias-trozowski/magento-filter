<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Filter\Template;

use Zend\Filter\FilterInterface;

/**
 * Simple filter template
 */
class Simple implements FilterInterface
{

    /**
     *
     * @var string
     */
    protected $startTag = '{{';

    /**
     *
     * @var string
     */
    protected $endTag = '}}';

    protected $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Set tags
     *
     * @param string $start
     * @param string $end
     * @return $this
     */
    public function setTags($start, $end)
    {
        $this->startTag = $start;
        $this->endTag = $end;
        return $this;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\Filter\FilterInterface::filter()
     */
    public function filter($value)
    {
        $callback = function ($matches) {
            if (!isset($this->data[$matches[1]])) {
                return null;
            }
            return $this->data[$matches[1]];
        };
        $expression = '#' . preg_quote($this->startTag, '#') . '(.*?)' . preg_quote($this->endTag, '#') . '#';
        return preg_replace_callback($expression, $callback, $value);
    }
}
