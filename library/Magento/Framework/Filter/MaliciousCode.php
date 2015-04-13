<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Filter;

use Zend\Filter\FilterInterface;

/**
 * Malicious code filter
 */
class MaliciousCode implements FilterInterface
{

    /**
     * Regular expressions for cutting malicious code
     *
     * @var string[]
     */
    protected $expressions = [
        // comments, must be first
        '/(\/\*.*\*\/)/Us',
        // tabs
        '/(\t)/',
        // javasript prefix
        '/(javascript\s*:)/Usi',
        // import styles
        '/(@import)/Usi',
        // js in the style attribute
        '/style=[^<]*((expression\s*?\([^<]*?\))|(behavior\s*:))[^<]*(?=\>)/Uis',
        // js attributes
        '/(ondblclick|onclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onload|onunload|onerror)=[^<]*(?=\>)/Uis',
        // tags
        '/<\/?(script|meta|link|frame|iframe).*>/Uis',
        // base64 usage
        '/src=[^<]*base64[^<]*(?=\>)/Uis',
    ];

    /**
     * Filter value
     *
     * @param string|array $value
     *
     * @return string array value
     */
    public function filter($value)
    {
        return preg_replace($this->expressions, '', $value);
    }

    /**
     * Add expression
     *
     * @param string $expression
     *
     * @return $this
     */
    public function addExpression($expression)
    {
        if (!in_array($expression, $this->expressions)) {
            $this->expressions[] = $expression;
        }
        return $this;
    }

    /**
     * Set expressions
     *
     * @param array $expressions
     *
     * @return $this
     */
    public function setExpressions(array $expressions)
    {
        $this->expressions = $expressions;
        return $this;
    }
}
