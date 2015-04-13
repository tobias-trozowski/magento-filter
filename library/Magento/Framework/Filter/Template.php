<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Filter;

use Magento\Framework\Stdlib\StringUtils;
use Zend\Filter\AbstractFilter;

/**
 * Template constructions filter
 * @see http://www.magentocommerce.com/knowledge-base/entry/defining-transactional-variables
 */
class Template extends AbstractFilter
{

    /**
     * Construction regular expression
     */
    const CONSTRUCTION_PATTERN = '/{{([a-z]{0,10})(.*?)}}/si';

    /**
     * #@+
     * Construction logic regular expression
     */
    const CONSTRUCTION_DEPEND_PATTERN = '/{{depend\s*(.*?)}}(.*?){{\\/depend\s*}}/si';

    const CONSTRUCTION_IF_PATTERN = '/{{if\s*(.*?)}}(.*?)({{else}}(.*?))?{{\\/if\s*}}/si';

    /**
     * Assigned template variables
     *
     * @var array
     */
    protected $templateVars = [];

    /**
     * Include processor
     *
     * @var callable null
     */
    protected $includeProcessor = null;

    /**
     * Sets template variables that's can be called through {var name} statement
     *
     * @param array $variables
     *
     * @return \Space10\Filter\Template
     */
    public function setVariables(array $variables)
    {
        foreach ($variables as $name => $value) {
            $this->templateVars[$name] = $value;
        }
        return $this;
    }

    /**
     * Sets the processor of includes.
     *
     * @param callable $callback
     *            it must return string
     *
     * @return \Space10\Filter\Template
     */
    public function setIncludeProcessor(array $callback)
    {
        $this->includeProcessor = $callback;
        return $this;
    }

    /**
     * Sets the processor of includes.
     *
     * @return callable null
     */
    public function getIncludeProcessor()
    {
        return is_callable($this->includeProcessor) ? $this->includeProcessor : null;
    }

    /**
     * Filter the string as template.
     *
     * @param string $value
     *
     * @return string
     * @throws \Exception
     * @see \Zend\Filter\FilterInterface::filter()
     */
    public function filter($value)
    {
        // "depend" and "if" operands should be first
        foreach ([
                     self::CONSTRUCTION_DEPEND_PATTERN => 'dependDirective',
                     self::CONSTRUCTION_IF_PATTERN     => 'ifDirective',
                 ] as $pattern => $directive) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach ($constructions as $construction) {
                    $callback = [
                        $this,
                        $directive,
                    ];
                    if (!is_callable($callback)) {
                        continue;
                    }
                    try {
                        $replacedValue = call_user_func($callback, $construction);
                    } catch (\Exception $e) {
                        throw $e;
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }

        if (preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach ($constructions as $construction) {
                $callback = [
                    $this,
                    $construction[1] . 'Directive',
                ];
                if (!is_callable($callback)) {
                    continue;
                }
                $replacedValue = call_user_func($callback, $construction);

                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }
        return $value;
    }

    /**
     *
     * @param string[] $construction
     *
     * @return string
     */
    public function varDirective($construction)
    {
        if (count($this->templateVars) == 0) {
            // If template preprocessing
            return $construction[0];
        }

        $replacedValue = $this->getVariable($construction[2], '');
        return $replacedValue;
    }

    /**
     *
     * @param string[] $construction
     *
     * @return mixed
     */
    public function includeDirective($construction)
    {
        // Processing of {include template=... [...]} statement
        $includeParameters = $this->getIncludeParameters($construction[2]);
        if (!isset($includeParameters['template']) or !$this->getIncludeProcessor()) {
            // Not specified template or not set include processor
            $replacedValue = '{Error in include processing}';
        } else {
            // Including of template
            $templateCode = $includeParameters['template'];
            unset($includeParameters['template']);
            $includeParameters = array_merge_recursive($includeParameters, $this->templateVars);
            $replacedValue = call_user_func($this->getIncludeProcessor(), $templateCode, $includeParameters);
        }
        return $replacedValue;
    }

    /**
     *
     * @param string[] $construction
     *
     * @return string
     */
    public function dependDirective($construction)
    {
        if (count($this->templateVars) == 0) {
            // If template preprocessing
            // return $construction[0];
            return '';
        }

        if ($this->getVariable($construction[1], '') == '') {
            return '';
        } else {
            return $construction[2];
        }
    }

    /**
     *
     * @param string[] $construction
     *
     * @return string
     */
    public function ifDirective($construction)
    {
        if (count($this->templateVars) == 0) {
            // return $construction[0];
            return '';
        }

        if ($this->getVariable($construction[1], '') == '') {
            if (isset($construction[3]) && isset($construction[4])) {
                return $construction[4];
            }
            return '';
        } else {
            return $construction[2];
        }
    }

    /**
     * Return associative array of include construction.
     *
     * @param string $value
     *            raw parameters
     *
     * @return array
     */
    protected function getIncludeParameters($value)
    {
        $tokenizer = new Template\Tokenizer\Parameter();
        $tokenizer->setString($value);
        $params = $tokenizer->tokenize();
        foreach ($params as $key => $value) {
            if (substr($value, 0, 1) === '$') {
                $params[$key] = $this->getVariable(substr($value, 1), null);
            }
        }
        return $params;
    }

    /**
     * Return variable value for var construction
     *
     * @param string $value
     *            raw parameters
     * @param string $default
     *            default value
     *
     * @return string
     * @todo fix instanceof \Sirrus\Stdlib\Object
     */
    protected function getVariable($value, $default = '{no_value_defined}')
    {
        $tokenizer = new Template\Tokenizer\Variable();
        $tokenizer->setString($value);
        $stackVars = $tokenizer->tokenize();
        $result = $default;
        $last = 0;
        $count = count($stackVars);
        for ($i = 0; $i < $count; $i++) {
            if ($i == 0 && isset($this->templateVars[$stackVars[$i]['name']])) {
                // Getting of template value
                $stackVars[$i]['variable'] = &$this->templateVars[$stackVars[$i]['name']];
            } else {
                if (isset($stackVars[$i - 1]['variable']) /*&& $stackVars[$i - 1]['variable'] instanceof \Magento\Stdlib\Object*/) {
                    // If object calling methods or getting properties
                    if ($stackVars[$i]['type'] == 'property') {
                        $caller = 'get' . StringUtils::upperCaseWords($stackVars[$i]['name'], '_', '');
                        if (method_exists($stackVars[$i - 1]['variable'], $caller)) {
                            $stackVars[$i]['variable'] = $stackVars[$i - 1]['variable']->{$caller}();
                        } elseif (property_exists($stackVars[$i - 1]['variable'],
                                $stackVars[$i]['name']) && isset($stackVars[$i - 1]['variable']->{$stackVars[$i]['name']})
                        ) {
                            $stackVars[$i]['variable'] = $stackVars[$i - 1]['variable']->{$stackVars[$i]['name']};
                        }
                    } else {
                        if ($stackVars[$i]['type'] == 'method') {
                            // Calling of object method
                            if (method_exists($stackVars[$i - 1]['variable'],
                                $stackVars[$i]['name'])
                            ) {
                                $stackVars[$i]['variable'] = call_user_func_array(
                                    [
                                        $stackVars[$i - 1]['variable'],
                                        $stackVars[$i]['name'],
                                    ], $stackVars[$i]['args']);
                            }
                        }
                    }
                    $last = $i;
                }
            }
        }

        if (isset($stackVars[$last]['variable'])) {
            // If value for construction exists set it
            $result = $stackVars[$last]['variable'];
        }
        return $result;
    }
}
