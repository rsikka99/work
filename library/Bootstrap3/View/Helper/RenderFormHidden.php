<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormHidden
 *
 * @author Lee Robert
 *
 */
class RenderFormHidden extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     *
     * @return string
     */
    public function RenderFormHidden (\Zend_Form_Element $element, $elementClasses = [])
    {
        $html = [];
        if ($element instanceof \Zend_Form_Element_Hidden)
        {
            if (!is_array($elementClasses))
            {
                $elementClasses = [$elementClasses];
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

            $html [] = sprintf(
                '<input type="hidden" name="%1$s" value="%2$s" title="%3$s" %4$s id="%5$s">',
                $element->getName(),
                $element->getValue(),
                $element->getDescription(),
                $serializedAttributes,
                $element->getId()
            );
        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render %1$s as an &lt;input type="hidden"&gt;</div>', $elementType);
        }

        return implode('', $html);
    }
}
