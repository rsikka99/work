<?php

/**
 * App_View_Helper_Identity
 *
 * @author Lee Robert
 *
 */
class App_View_Helper_Identity extends Zend_View_Helper_Abstract
{
    static $identity;

    /**
     * @return string
     */
    public function Identity ()
    {
        if (!isset(self::$identity) && Zend_Auth::getInstance()->hasIdentity())
        {
            self::$identity = Zend_Auth::getInstance()->getIdentity();
        }

        return self::$identity;
    }
}
