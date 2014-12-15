<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormSubmit
 *
 * @author Lee Robert
 *
 */
class RenderFormSubmit extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     *
     * @return string
     */
    public function RenderFormSubmit (\Zend_Form_Element $element, $elementClasses = array())
    {
        $html = array();

        if ($element instanceof \Zend_Form_Element_Button || $element instanceof \Zend_Form_Element_Submit)
        {
            if (!is_array($elementClasses))
            {
                $elementClasses = array($elementClasses);
            }

            $attributes = $element->getAttribs();

            /**
             *  Prep attributes
             */
            $attributes['class']  = $this->processClasses($attributes, $elementClasses);
            $serializedAttributes = $this->serializeAttributes($attributes, array('options'));
            $element->setAttribs($attributes);

            $html[] = sprintf('<button type="submit" name="%2$s" value="%3$s" %1$s >%4$s</button>', $serializedAttributes, $element->getName(), $element->getValue(), $element->getLabel());
        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render %1$s as an &lt;button type="submit"&gt;</div>', $elementType);
        }

        return implode('', $html);
    }
}
