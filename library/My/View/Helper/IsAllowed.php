<?php
class My_View_Helper_IsAllowed extends Zend_View_Helper_Abstract
{
    /**
     * @var int
     */
    protected $_userId;

    /**
     * @var Application_Model_Acl
     */
    protected $_acl;

    public function __construct ()
    {
        $this->_acl = Application_Model_Acl::getInstance();

        if (Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_userId = Zend_Auth::getInstance()->getIdentity()->id;
        }
    }

    /**
     * Checks to see if the currently logged in user has access to the resource.
     *
     * @param $resource
     * @param $privilege
     *
     * @return bool
     */
    public function isAllowed ($resource, $privilege = null)
    {
        if ($this->_acl->isAllowed($this->_userId, $resource, $privilege))
        {
            return true;
        }

        return false;
    }
}