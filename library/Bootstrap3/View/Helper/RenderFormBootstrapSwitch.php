<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormBootstrapSwitch
 *
 * @author Lee Robert
 *
 */
class RenderFormBootstrapSwitch extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     *
     * @return string
     */
    public function RenderFormBootstrapSwitch (\Zend_Form_Element $element, $elementClasses = [])
    {
        $html = [];

        if (!is_array($elementClasses))
        {
            $elementClasses = [$elementClasses];
        }

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

        if ($element instanceof \Zend_Form_Element_MultiCheckbox)
        {
            foreach ($element->getMultiOptions() as $value => $option)
            {
                $checked = (is_array($element->getValue()) && in_array($value, $element->getValue())) ? 'checked="checked"' : '';


                $html[] = sprintf('<input name="%5$s[]" type="checkbox" value="%1$s" %4$s %2$s>',
                    $value,
                    $serializedAttributes,
                    $option,
                    $checked,
                    $element->getName()
                );
            }
        }
        elseif ($element instanceof \Zend_Form_Element_Checkbox)
        {
            $checked = ($element->checked) ? 'checked="checked"' : '';

            $html[] = sprintf('<input name="%1$s" type="hidden" value="%2$s">',
                $element->getName(),
                $element->getUncheckedValue()
            );

            $html[] = sprintf('<input name="%5$s" type="checkbox" value="%1$s" %4$s %2$s>',
                $element->getCheckedValue(),
                $serializedAttributes,
                $element->getLabel(),
                $checked,
                $element->getName()
            );
        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render "%1$s" as a bootstrap switch &lt;input type="checkbox"&gt;</div>', $elementType);
        }

        return implode(PHP_EOL, $html);
    }
}
