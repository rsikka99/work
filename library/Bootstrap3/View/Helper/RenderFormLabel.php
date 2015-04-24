<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormLabel
 *
 * @author Lee Robert
 *
 */
class RenderFormLabel extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     *
     * @return string
     */
    public function RenderFormLabel (\Zend_Form_Element $element, $elementClasses = [])
    {
        $html = [];

        if ($element instanceof \Zend_Form_Element)
        {
            if (!is_array($elementClasses))
            {
                $elementClasses = [$elementClasses];
            }

            array_unshift($elementClasses, 'control-label');
            if ($element->isRequired())
            {
                array_unshift($elementClasses, 'required');
            }

            $html[] = sprintf('<label class="%1$s" for="%2$s">%3$s</label>', implode(' ', $elementClasses), $element->getName(), $element->getLabel());
        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render the &lt;label&gt; for a "%1$s"</div>', $elementType);
        }

        return implode('', $html);
    }
}
