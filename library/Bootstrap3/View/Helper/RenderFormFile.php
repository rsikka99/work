<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormFile
 *
 * @author Lee Robert
 *
 */
class RenderFormFile extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     *
     * @return string
     */
    public function RenderFormFile (\Zend_Form_Element $element, $elementClasses = [])
    {
        $html = [];
        if ($element instanceof \Zend_Form_Element_File)
        {
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


            $html [] = sprintf('<input type="hidden" name="MAX_FILE_SIZE" value="%s" id="MAX_FILE_SIZE">', $element->getMaxFileSize());
            $html [] = sprintf('<input type="file" name="%1$s" value="%2$s" %3$s>', $element->getName(), $element->getValue(), $serializedAttributes);


        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render %1$s as an &lt;input type="text"&gt;</div>', $elementType);
        }

        return implode('', $html);
    }
}
