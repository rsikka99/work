<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormRadio
 *
 * @author Lee Robert
 *
 */
class RenderFormRadio extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     *
     * @return string
     */
    public function RenderFormRadio (\Zend_Form_Element $element, $elementClasses = [])
    {
        $html = [];

        if ($element instanceof \Zend_Form_Element_Radio)
        {
            if (!is_array($elementClasses))
            {
                $elementClasses = [$elementClasses];
            }

            if ($element->hasErrors())
            {
                $elementClasses[] = 'has-error';
            }

            if ($element->isRequired())
            {
                $attributes['required'] = 'true';
            }

            /**
             *  Prep attributes
             */
            $attributes           = $element->getAttribs();
            $attributes['class']  = $this->processClasses($attributes, $elementClasses);
            $serializedAttributes = $this->serializeAttributes($attributes, ['options']);
            $element->setAttribs($attributes);

            foreach ($element->getMultiOptions() as $value => $option)
            {
                $html[]  = '<div class="radio"><label>';
                $checked = ($value == $element->getValue()) ? 'checked="checked"' : '';
                $html[]  = sprintf('<input name="%5$s" type="radio" value="%1$s" %4$s %2$s> %3$s', $value, $serializedAttributes, $option, $checked, $element->getName());
                $html[]  = '</label></div>';
            }
        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render "%1$s" as an &lt;input type="radio"&gt;</div>', $elementType);
        }

        return implode(PHP_EOL, $html);
    }
}
