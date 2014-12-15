<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormElementErrors
 *
 * @author Lee Robert
 *
 */
class RenderFormElementErrors extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param string             $helpClass
     *
     * @param bool               $addFeedback
     *
     * @return string
     */
    public function RenderFormElementErrors (\Zend_Form_Element $element, $helpClass = 'help-block', $addFeedback = true)
    {
        $html = array();
        if ($element instanceof \Zend_Form_Element)
        {
            if ($element->hasErrors())
            {
                if ($addFeedback)
                {
                    $html[] = '<span class="glyphicon glyphicon-remove form-control-feedback"></span>';
                }
                $html[] = sprintf('<span class="%1$s">%2$s</span>', $helpClass, implode('<br>', $element->getMessages()));
            }
        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render the &lt;errors&gt; for a "%1$s"</div>', $elementType);
        }

        return implode('', $html);
    }
}
