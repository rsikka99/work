<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormAbstract
 *
 * @author Lee Robert
 *
 */
class RenderFormAbstract extends \Zend_View_Helper_Abstract
{

    /**
     * @return string
     */
    public function RenderFormAbstract ()
    {
        $html   = [];
        $html[] = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Cannot use "%1$s" directly.</div>', get_class($this));

        return implode('', $html);
    }


    /**
     * Processes and combines the classes from the attributes array and our own array/string of classes
     *
     * @param array        $attributes
     * @param array|string $elementClasses
     *
     * @return string
     */
    protected function processClasses ($attributes, $elementClasses)
    {
        $classes = "";

        if (isset($attributes['class']))
        {
            $classes .= $attributes['class'];
        }

        if (is_array($elementClasses))
        {
            $classes .= ' ' . implode(' ', $elementClasses);
        }
        elseif (strlen($elementClasses) > 0)
        {
            $classes .= ' ' . $elementClasses;
        }

        return $classes;
    }

    /**
     * Serializes attributes into a string
     *
     * @param array $attributes
     * @param array $excludedKeys
     *
     * @return string
     */
    protected function serializeAttributes ($attributes, $excludedKeys = [])
    {
        $serializedAttributes = [];

        foreach ($attributes as $attrName => $attrValue)
        {
            if (!in_array($attrName, $excludedKeys) && $attrValue)
            {
                if (!is_array($attrValue))
                {
                    $serializedAttributes[] = sprintf('%s="%s"', $attrName, $attrValue);
                }
            }
        }

        return implode(' ', $serializedAttributes);
    }
}
