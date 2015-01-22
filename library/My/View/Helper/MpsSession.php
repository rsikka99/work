<?php

class My_View_Helper_MpsSession extends Zend_View_Helper_Abstract
{

    /**
     * @var Zend_Session_Namespace
     */
    protected $mpsSession;

    /**
     * Returns application settings
     */
    public function MpsSession ()
    {
        if (!isset($this->mpsSession))
        {
            if (Zend_Session::namespaceIsset('mps-tools'))
            {
                $this->mpsSession = new Zend_Session_Namespace('mps-tools');
            }
            else
            {
                // TODO: Initialize the session
                $this->mpsSession = new Zend_Session_Namespace('mps-tools');
            }
        }

        return $this->mpsSession;
    }
}