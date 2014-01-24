<?php

/**
 * Class Dealermanagement_UserController
 */
class Dealermanagement_UserController extends Tangent_Controller_Action
{
    /**
     * Whether or not the current user has root access
     *
     * @var bool
     */
    protected $_currentUserIsRoot;

    /**
     * @var mixed The current users identity
     */
    protected $_identity;

    public function init ()
    {
        $this->_identity          = Zend_Auth::getInstance()->getIdentity();
        $this->_currentUserIsRoot = ($this->_identity->id == 1);

    }

    /**
     * Used to view all users
     */
    public function indexAction ()
    {
        $this->view->headTitle('Users');
        $this->view->headTitle('User Management');
        // Fetch all the users
        $userMapper = new Application_Model_Mapper_User();
        $users      = $userMapper->fetchUserListForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);


        // Display all of the users
        $this->view->users = $users;
        // Get the max users allowed for this dealer
        $this->view->maxUsers = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId)->userLicenses;
    }


    /**
     * Used to create a new user
     */
    public function createAction ()
    {
        $this->view->headTitle('Users');
        $this->view->headTitle('Create User');
        $roles       = Admin_Model_Mapper_Role::getInstance()->getRolesAvailableForDealers();
        $userService = new Dealermanagement_Service_User($roles, $this->_identity->dealerId, true);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (!isset($postData ['cancel']))
            {
                $db = Zend_Db_table::getDefaultAdapter();
                try
                {
                    $dealer           = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
                    $currentUserCount = count(Application_Model_Mapper_User::getInstance()->fetchUserListForDealer($dealer->id));
                    $maxLicenses      = $dealer->userLicenses;
                    if ($currentUserCount < $maxLicenses)
                    {
                        $db->beginTransaction();
                        if ($userService->create($postData))
                        {
                            $this->_flashMessenger->addMessage(array('success' => 'User created. An email will be sent out to the user with instructions on how to proceed.'));
                            $db->commit();
                        }
                        else
                        {
                            foreach ($userService->getErrors() as $message)
                            {
                                $this->_flashMessenger->addMessage(array('danger' => $message));
                            }
                            $db->rollBack();
                        }
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array('danger' => 'Allocated user licenses exceed.'));
                    }
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    Tangent_Log::logException($e);
                }
            }
            else
            {
                $this->redirector('index');
            }
        }

        $this->view->form = $userService->getForm();
    }

    /**
     * Used to delete a user
     */
    public function deleteAction ()
    {
        $this->view->headTitle('Users');
        $this->view->headTitle('Delete User');
        $userId = $this->_getParam('id', false);

        /**
         * Require ID
         */
        if (!$userId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to delete first.'));
            $this->redirector('index');
        }

        /**
         * Never delete yourself
         */
        if ($userId == $this->_identity->id)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'You cannot delete yourself.'));
            $this->redirector('index');
        }

        /**
         * Never delete the root user
         */
        if ($userId == '1')
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to delete first.'));
            $this->redirector('index');
        }


        /**
         * Fetch the user
         */
        $mapper = new Application_Model_Mapper_User();
        $user   = $mapper->find($userId);

        /**
         * Ensure the user exists
         */
        if (!$user)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to delete first.'));
            $this->redirector('index');
        }

        /**
         * Ensure the user belongs to the same dealership
         */
        if ($user->dealerId != $this->_identity->dealerId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to delete first.'));
            $this->redirector('index');
        }

        $form = new Application_Form_Delete("Are you sure you want to delete {$user->email} ({$user->firstname} {$user->lastname})?");

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (!isset($postData ['cancel']))
            {
                $db          = Zend_Db_table::getDefaultAdapter();
                $roles       = Admin_Model_Mapper_Role::getInstance()->getRolesAvailableForDealers();
                $userService = new Dealermanagement_Service_User($roles, $this->_identity->dealerId, true);
                try
                {
                    $db->beginTransaction();

                    if ($userService->delete($userId))
                    {
                        $this->_flashMessenger->addMessage(array('success' => "User deleted."));
                        $db->commit();
                        $this->redirector('index');
                    }
                    else
                    {
                        foreach ($userService->getErrors() as $message)
                        {
                            $this->_flashMessenger->addMessage(array('danger' => $message));
                        }
                        $db->rollBack();
                    }
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    Tangent_Log::logException($e);
                }
            }
            else
            {
                $this->redirector('index');
            }
        }

        $this->view->form = $form;
    }

    /**
     * Used to edit a user
     */
    public function editAction ()
    {
        $this->view->headTitle('Users');
        $this->view->headTitle('Edit User');
        $userId = $this->_getParam('id', false);

        // If they haven't provided an id, send them back to the view all users page
        if (!$userId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to edit.'));
            $this->redirector('index');
        }

        if ($userId == '1' && !$this->_currentUserIsRoot)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to edit.'));
            $this->redirector('index');
        }

        /**
         * Fetch the user
         */
        $mapper = new Application_Model_Mapper_User();
        $user   = $mapper->find($userId);

        /**
         * Ensure the user exists
         */
        if (!$user)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to edit.'));
            $this->redirector('index');
        }

        /**
         * Ensure the user belongs to the same dealership
         */
        if ($user->dealerId != $this->_identity->dealerId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to edit.'));
            $this->redirector('index');
        }

        $roles       = Admin_Model_Mapper_Role::getInstance()->getRolesAvailableForDealers();
        $userService = new Dealermanagement_Service_User($roles, $this->_identity->dealerId);

        $form = $userService->getForm($user);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (!isset($postData ['cancel']))
            {
                $db = Zend_Db_table::getDefaultAdapter();
                try
                {
                    $db->beginTransaction();

                    if ($userService->update($postData, $userId))
                    {
                        $this->_flashMessenger->addMessage(array('success' => 'User saved. If you changed the password then an email will be sent out to the user with the new password and instructions on how to proceed.'));
                        $db->commit();
                    }
                    else
                    {
                        foreach ($userService->getErrors() as $message)
                        {
                            $this->_flashMessenger->addMessage(array('danger' => $message));
                        }
                        $db->rollBack();
                    }
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    Tangent_Log::logException($e);
                }
            }
            else
            {
                $this->redirector('index');
            }
        }

        $this->view->form = $form;
    }
}