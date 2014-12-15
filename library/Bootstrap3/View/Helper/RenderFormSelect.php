<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormSelect
 *
 * @author Lee Robert
 *
 */
class RenderFormSelect extends RenderFormAbstract
{
    /**
     * @param \Zend_Form_Element $element
     *
     * @param array              $elementClasses
     *
     * @return string
     */
    public function RenderFormSelect (\Zend_Form_Element $element, $elementClasses = array())
    {
        $html = array();
        if ($element instanceof \Zend_Form_Element_Multi)
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

            /**
             * Get the element attributes
             */
            $attributes = $element->getAttribs();
            if ($element->isRequired())
            {
                $attributes['required'] = 'true';
            }

            if ($element instanceof \Zend_Form_Element_Multiselect)
            {
                $attributes['multiple'] = 'true';
            }

            /**
             *  Prep attributes
             */
            $attributes['class']  = $this->processClasses($attributes, $elementClasses);
            $serializedAttributes = $this->serializeAttributes($attributes, array('options'));
            $element->setAttribs($attributes);

            $name = ($element instanceof \Zend_Form_Element_Multiselect) ? $element->getName() . '[]' : $element->getName();

            $html[] = sprintf('<select id="%3$s" name="%1$s" %2$s>', $name, $serializedAttributes, $element->getId());

            if ($element instanceof \Zend_Form_Element_Multiselect)
            {
                foreach ($element->getMultiOptions() as $value => $option)
                {
                    $selected = (is_array($element->getValue()) && in_array($value, $element->getValue())) ? 'selected' : '';

                    $html[] = sprintf('<option value="%1$s" %2$s>%3$s</option>', $value, $selected, $option);
                }
            }
            else
            {
                foreach ($element->getMultiOptions() as $value => $option)
                {
                    $selected = ($value == $element->getValue()) ? 'selected' : '';

                    $html[] = sprintf('<option value="%1$s" %2$s>%3$s</option>', $value, $selected, $option);
                }
            }
            $html[] = '</select>';
        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render "%1$s" as a &lt;select&gt;</div>', $elementType);
        }

        return implode('', $html);
    }
}
