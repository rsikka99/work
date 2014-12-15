<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Mappers\UserMapper;
use MPSToolbox\Legacy\Modules\Admin\Mappers\RoleMapper;
use MPSToolbox\Legacy\Modules\DealerManagement\Services\UserService;
use Tangent\Controller\Action;

/**
 * Class Dealermanagement_UserController
 */
class Dealermanagement_UserController extends Action
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
        $this->_pageTitle = ['Your Users', 'Company'];
        // Fetch all the users
        $userMapper = new UserMapper();
        $users      = $userMapper->fetchUserListForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);


        // Display all of the users
        $this->view->users = $users;
        // Get the max users allowed for this dealer
        $this->view->maxUsers = DealerMapper::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId)->userLicenses;
    }


    /**
     * Used to create a new user
     */
    public function createAction ()
    {
        $this->_pageTitle = ['Create User', 'Your Users', 'Company'];
        $roles            = RoleMapper::getInstance()->getRolesAvailableForDealers();
        $userService      = new UserService($roles, $this->_identity->dealerId, true);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (!isset($postData ['cancel']))
            {
                $db = Zend_Db_table::getDefaultAdapter();
                try
                {
                    $dealer           = DealerMapper::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
                    $currentUserCount = count(UserMapper::getInstance()->fetchUserListForDealer($dealer->id));
                    $maxLicenses      = $dealer->userLicenses;
                    if ($currentUserCount < $maxLicenses)
                    {
                        $db->beginTransaction();
                        if ($userService->create($postData))
                        {
                            $this->_flashMessenger->addMessage(['success' => 'User created. An email will be sent out to the user with instructions on how to proceed.']);
                            $db->commit();
                        }
                        else
                        {
                            foreach ($userService->getErrors() as $message)
                            {
                                $this->_flashMessenger->addMessage(['danger' => $message]);
                            }
                            $db->rollBack();
                        }
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(['danger' => 'Allocated user licenses exceed.']);
                    }
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    \Tangent\Logger\Logger::logException($e);
                }
            }
            else
            {
                $this->redirectToRoute('company.users');
            }
        }

        $this->view->form = $userService->getForm();
    }

    /**
     * Used to delete a user
     */
    public function deleteAction ()
    {
        $this->_pageTitle = ['Delete User', 'Your Users', 'Company'];
        $userId           = $this->_getParam('id', false);

        /**
         * Require ID
         */
        if (!$userId)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a user to delete first.']);
            $this->redirectToRoute('company.users');
        }

        /**
         * Never delete yourself
         */
        if ($userId == $this->_identity->id)
        {
            $this->_flashMessenger->addMessage(['warning' => 'You cannot delete yourself.']);
            $this->redirectToRoute('company.users');
        }

        /**
         * Never delete the root user
         */
        if ($userId == '1')
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a user to delete first.']);
            $this->redirectToRoute('company.users');
        }


        /**
         * Fetch the user
         */
        $mapper = new UserMapper();
        $user   = $mapper->find($userId);

        /**
         * Ensure the user exists
         */
        if (!$user)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a user to delete first.']);
            $this->redirectToRoute('company.users');
        }

        /**
         * Ensure the user belongs to the same dealership
         */
        if ($user->dealerId != $this->_identity->dealerId)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a user to delete first.']);
            $this->redirectToRoute('company.users');
        }

        $form = new DeleteConfirmationForm("Are you sure you want to delete {$user->email} ({$user->firstname} {$user->lastname})?");

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (!isset($postData ['cancel']))
            {
                $db          = Zend_Db_table::getDefaultAdapter();
                $roles       = RoleMapper::getInstance()->getRolesAvailableForDealers();
                $userService = new UserService($roles, $this->_identity->dealerId, true);
                try
                {
                    $db->beginTransaction();

                    if ($userService->delete($userId))
                    {
                        $this->_flashMessenger->addMessage(['success' => "User deleted."]);
                        $db->commit();
                        $this->redirectToRoute('company.users');
                    }
                    else
                    {
                        foreach ($userService->getErrors() as $message)
                        {
                            $this->_flashMessenger->addMessage(['danger' => $message]);
                        }
                        $db->rollBack();
                    }
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    \Tangent\Logger\Logger::logException($e);
                }
            }
            else
            {
                $this->redirectToRoute('company.users');
            }
        }

        $this->view->form = $form;
    }

    /**
     * Used to edit a user
     */
    public function editAction ()
    {
        $this->_pageTitle = ['Edit User', 'Your Users', 'Company'];
        $userId           = $this->_getParam('id', false);

        // If they haven't provided an id, send them back to the view all users page
        if (!$userId)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a user to edit.']);
            $this->redirectToRoute('company.users');
        }

        if ($userId == '1' && !$this->_currentUserIsRoot)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a user to edit.']);
            $this->redirectToRoute('company.users');
        }

        /**
         * Fetch the user
         */
        $mapper = new UserMapper();
        $user   = $mapper->find($userId);

        /**
         * Ensure the user exists
         */
        if (!$user)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a user to edit.']);
            $this->redirectToRoute('company.users');
        }

        /**
         * Ensure the user belongs to the same dealership
         */
        if ($user->dealerId != $this->_identity->dealerId)
        {
            $this->_flashMessenger->addMessage(['warning' => 'Please select a user to edit.']);
            $this->redirectToRoute('company.users');
        }

        $roles       = RoleMapper::getInstance()->getRolesAvailableForDealers();
        $userService = new UserService($roles, $this->_identity->dealerId);

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
                        $this->_flashMessenger->addMessage(['success' => 'User saved. If you changed the password then an email will be sent out to the user with the new password and instructions on how to proceed.']);
                        $db->commit();
                    }
                    else
                    {
                        foreach ($userService->getErrors() as $message)
                        {
                            $this->_flashMessenger->addMessage(['danger' => $message]);
                        }
                        $db->rollBack();
                    }
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    \Tangent\Logger\Logger::logException($e);
                }
            }
            else
            {
                $this->redirectToRoute('company.users');
            }
        }

        $this->view->form = $form;
    }
}