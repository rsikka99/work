<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormDateTimePicker
 *
 * @author Lee Robert
 *
 */
class RenderFormDateTimePicker extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     *
     * @return string
     */
    public function RenderFormDateTimePicker (\Zend_Form_Element $element, $elementClasses = array())
    {
        $html = array();
        if ($element instanceof \My_Form_Element_DateTimePicker)
        {
            if (!is_array($elementClasses))
            {
                $elementClasses = array($elementClasses);
            }
            array_unshift($elementClasses, 'form-control');

            if ($element->hasErrors())
            {
                $elementClasses[] = 'has-error';
            }

            $attributes = $element->getAttribs();
            if ($element->isRequired())
            {
                $attributes['required'] = 'true';
            }

            /**
             *  Prep attributes
             */
            $attributes['class']  = $this->processClasses($attributes, $elementClasses);
            $serializedAttributes = $this->serializeAttributes($attributes, array('options'));
            $element->setAttribs($attributes);

            $html [] = $element->renderUiWidgetElement();
        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render %1$s as a date time picker element</div>', $elementType);
        }

        return implode('', $html);
    }
}
