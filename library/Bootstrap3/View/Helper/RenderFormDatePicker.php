<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormDatePicker
 *
 * @author Lee Robert
 *
 */
class RenderFormDatePicker extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     *
     * @return string
     */
    public function RenderFormDatePicker (\Zend_Form_Element $element, $elementClasses = [])
    {
        $html = [];
        if ($element instanceof \ZendX_JQuery_Form_Element_DatePicker)
        {
            if (!is_array($elementClasses))
            {
                $elementClasses = [$elementClasses];
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
            $serializedAttributes = $this->serializeAttributes($attributes, ['options']);
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
