<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormTextArea
 *
 * @author Lee Robert
 *
 */
class RenderFormTextArea extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     *
     * @return string
     */
    public function RenderFormTextArea (\Zend_Form_Element $element, $elementClasses = [])
    {
        $html = [];
        if ($element instanceof \Zend_Form_Element_Textarea)
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

            $html [] = sprintf('<textarea name="%1$s" %2$s>%3$s</textarea>', $element->getName(), $serializedAttributes, $element->getValue());
        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render %1$s as an &lt;textarea"&gt;</div>', $elementType);
        }

        return implode('', $html);
    }
}
