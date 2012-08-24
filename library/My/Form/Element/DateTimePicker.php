<?php

/**
 * Form Element for jQuery DateTimePicker View Helper
 *
 * @author Darius Matulionis
 * @category My
 * @package My_Form
 * @subpackage Element
 */
class My_Form_Element_DateTimePicker extends ZendX_JQuery_Form_Element_UiWidget
{
    /**
     * Use dateTimePicker view helper by default
     *
     * @var string
     */
    public $helper = "FormDateTimePicker";

    /**
     * Adds a file decorator if no one found
     */
    public function loadDefaultDecorators ()
    {
        if ($this->loadDefaultDecoratorsIsDisabled())
        {
            return $this;
        }
        
        /*
         * For some reason they decided to throw an exception within getDecorators if it doesnt have a decorator that
         * implements UiWidgetElementMarker, which makes it impossible for us to check if its there and add it on if it
         * does not. So instead we catch the error, and then add it on. From there we can manipulate the decorator array
         * so that it is the first one on the stack.
         */
        if (! $this->_jqueryDecoratorExists())
        {
            $this->addDecorator('UiWidgetElement');
            $this->removeDecorator('ViewHelper');
            
            $decorators = $this->getDecorators();
            $uiDeco = array_pop($decorators);
            array_unshift($decorators, $uiDeco);
            
            $this->setDecorators($decorators);
        }
        return $this;
    }

    /**
     * Checks if a jquery decorator has been added to the decorators
     * stack
     */
    private function _jqueryDecoratorExists ()
    {
        try
        {
            
            foreach ( $this->getDecorators() as $decorator )
            {
                if ($decorator instanceof ZendX_JQuery_Form_Decorator_UiWidgetElementMarker)
                {
                    return true;
                }
            }
        }
        catch ( Exception $e )
        {
        }
        
        return false;
    }
}
