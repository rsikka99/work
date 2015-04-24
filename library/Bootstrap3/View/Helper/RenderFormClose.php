<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormClose
 *
 * @author Lee Robert
 *
 */
class RenderFormClose extends RenderFormAbstract
{

    /**
     * @param \Zend_Form $form
     *
     * @return string
     */
    public function RenderFormClose (\Zend_Form $form)
    {
        $html = [];
        if ($form instanceof \Zend_Form)
        {
            $html[] = "</form>";

        }
        else
        {
            $elementType = get_class($form);
            $html[]      = '<form>';
            $html[]      = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render the &lt;/form&gt; for a "%1$s"</div>', $elementType);
        }

        return implode('', $html);
    }
}
