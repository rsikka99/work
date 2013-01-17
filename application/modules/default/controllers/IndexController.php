<?php
class Default_IndexController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // action body
//        My_Log::log('Sample Log (DefaultModule - IndexController - IndexAction): Logging Started', Zend_Log::INFO);
//        My_Log::log('Sample Log (DefaultModule - IndexController - IndexAction): Candying that bacon.', Zend_Log::INFO);
//        My_Log::log('Sample Log (DefaultModule - IndexController - IndexAction): User is not authenticated!', Zend_Log::INFO);
//        My_Log::log('Sample Log (DefaultModule - IndexController - IndexAction): User has 4 login attempts left.', Zend_Log::INFO);

//        if (Zend_Auth::getInstance()->hasIdentity())
//        {
//            $userId = Zend_Auth::getInstance()->getIdentity()->id;
//            $acl = Application_Model_Acl::getInstance();
//
//            if ($acl->isAllowed($userId, "default_index_index", Application_Model_Acl::PRIVILEGE_VIEW))
//            {
//                echo "<pre>Var dump initiated at " . __LINE__ . " of:\n" . __FILE__ . "\n\n";
//                var_dump("User has view level privileges");
//                die();
//            }
//        }

    }
}