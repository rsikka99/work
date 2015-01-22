<?php

use MPSToolbox\Legacy\Entities\ClientEntity;

class My_View_Helper_Identity extends Zend_View_Helper_Abstract
{

    /**
     * @var Zend_Session_Namespace|mixed
     */
    protected $identity;

    /**
     * Fetches the current identity
     */
    public function Identity ()
    {
        if (!isset($this->identity))
        {
            $this->identity = Zend_Auth::getInstance()->getIdentity();
        }

        return $this->identity;
    }
}