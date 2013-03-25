<?php
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
        // Fetch all the users
        $userMapper = new Application_Model_Mapper_User();
        $users      = $userMapper->fetchUserListForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);

        // Display all of the users
        $this->view->users = $users;
    }


    /**
     * Used to create a new user
     */
    public function createAction ()
    {
        $userService = new Dealermanagement_Service_User();
        $roles       = Admin_Model_Mapper_Role::getInstance()->getRolesAvailableForDealers();
        $form        = $userService->getForm($roles, true);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            $db = Zend_Db_table::getDefaultAdapter();
            try
            {
                $db->beginTransaction();

                if ($userService->create($postData))
                {
                    $this->_flashMessenger->addMessage(array('success' => 'User created.'));
                    $db->commit();
                }
                else
                {
                    $db->rollBack();
                }
            }
            catch (Exception $e)
            {
                $db->rollBack();
            }
        }

        $this->view->form = $form;
    }

    /**
     * Used to delete a user
     */
    public function deleteAction ()
    {
        $userId   = $this->_getParam('id', false);
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        // If they haven't provided an id, send them back to the view all users page
        if (!$userId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to delete first.'));
            $this->redirector('index');
        }

        if ($userId === '1')
        {
            $this->_flashMessenger->addMessage(array('danger' => 'Insufficient Privilege: You cannot delete the root user.'));
            $this->redirector('index');
        }

        // Get the user
        $mapper = new Application_Model_Mapper_User();
        $user   = $mapper->find($userId);
        if ($user->dealerId != $dealerId)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'Insufficient Privilege: You cannot edit this user.'));
            $this->redirector('index');
        }
        // If the user doesn't exist, send them back t the view all users page
        if (!$user)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'There was an error selecting the user to delete.'));
            $this->redirector('index');
        }

        $form = new Application_Form_Delete("Are you sure you want to delete {$user->username} ({$user->firstname} {$user->lastname})?");

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();

            if (!isset($values ['cancel']))
            {

                if ($form->isValid($values))
                {
                    $mapper->delete($user);

                    $this->_flashMessenger->addMessage(array('success' => "User deleted."));

                    $this->redirector('index');
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'There was an error while deleting the user'));
                    $this->redirector('index');
                }
            }
            else
            {
                // User has cancelled.
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
        $userId   = $this->_getParam('id', false);
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;

        // If they haven't provided an id, send them back to the view all users page
        if (!$userId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to delete first.'));
            $this->redirector('index');
        }

        if ($userId == '1' && !$this->_currentUserIsRoot)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'Insufficient Privilege: You cannot edit the root user.'));
            $this->redirector('index');
        }


        // Get the user
        $mapper = new Application_Model_Mapper_User();
        $user   = $mapper->find($userId);
        if ($user->dealerId != $dealerId)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'Insufficient Privilege: You cannot edit this user.'));
            $this->redirector('index');
        }

        // Get all available roles. In theory this should be a manageable amount
        $roleMapper = new Admin_Model_Mapper_Role();
        $roles      = $roleMapper->fetchAll();

        $userRoleMapper = new Admin_Model_Mapper_UserRole();
        $userRoles      = $userRoleMapper->fetchAll(array('userId = ?' => $userId));

        // We need to get the current users roles
        $currentUserRoles = array();
        foreach ($userRoles as $userRole)
        {
            $currentUserRoles [] = $userRole->roleId;
        }

        // If the user doesn't exist, send them back t the view all users page
        if (!$user)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'There was an error selecting the user to delete.'));
            $this->redirector('index');
        }

        // Create a new form with the mode and roles set
        $form = new Admin_Form_User(Admin_Form_User::MODE_EDIT, $roles);

        // Prepare the data for the form
        $values               = $user->toArray();
        $values ['userRoles'] = $currentUserRoles;

        $form->populate($values);

        $request = $this->getRequest();

        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $postData = $request->getPost();

            // If we cancelled we don't need to validate anything
            if (!isset($postData ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($postData))
                    {
                        $formValues = $form->getValues();
                        // Validate the roles
                        if (isset($formValues ["userRoles"]))
                        {
                            foreach ($formValues ["userRoles"] as $roleId)
                            {
                                $roleIsValid = false;
                                /* @var $role Admin_Model_Role */
                                foreach ($roles as $role)
                                {
                                    if ($role->id == $roleId)
                                    {
                                        $roleIsValid = true;
                                        break;
                                    }
                                }

                                // Give an error if a role was not valid
                                if (!$roleIsValid)
                                {
                                    throw new Exception("Invalid Role Selected!");
                                }
                            }
                        }

                        // Set the password values if we checked the reset password box
                        if (isset($formValues ["password"]) && !empty($formValues ["password"]) && $formValues ["reset_password"])
                        {
                            unset($formValues ["passwordconfirm"]);
                            $formValues ["password"] = Application_Model_User::cryptPassword($formValues ["password"]);
                        }
                        else
                        {
                            // This whole password area could be done in a more logical sequence
                            if ($formValues ["reset_password"])
                            {
                                throw new InvalidArgumentException("You must specify a new password");
                            }
                            unset($formValues ["password"]);
                            unset($formValues ["passwordconfirm"]);
                        }

                        // Unset an empty date
                        if (isset($formValues ["frozenUntil"]) && empty($formValues ["frozenUntil"]))
                        {
                            unset($formValues ["frozenUntil"]);
                        }

                        // Unset an empty date
                        if (isset($formValues ["eulaAccepted"]) && empty($formValues ["eulaAccepted"]))
                        {
                            unset($formValues ["eulaAccepted"]);
                        }

                        $mapper = new Application_Model_Mapper_User();
                        $user   = new Application_Model_User();
                        $user->populate($formValues);
                        $user->id = $userId;

                        // Save to the database with cascade insert turned on
                        $mapper->save($user, $userId);

                        // Save changes to the user roles
                        if (isset($formValues ["userRoles"]))
                        {
                            $userRole         = new Admin_Model_UserRole();
                            $userRole->userId = $userId;

                            // Loop through our new roles
                            foreach ($formValues ["userRoles"] as $roleId)
                            {
                                $hasRole = false;

                                foreach ($userRoles as $existingUserRole)
                                {
                                    if ($existingUserRole->roleId == $roleId)
                                    {
                                        $hasRole = true;
                                        break;
                                    }
                                }

                                // If the role is new
                                if (!$hasRole)
                                {
                                    $userRole->roleId = $roleId;
                                    $userRoleMapper->insert($userRole);
                                }
                            }


                            // Loop through our old roles to see which were removed
                            /* @var $userRole Admin_Model_UserRole */
                            foreach ($userRoles as $userRole)
                            {
                                $hasRole = false;
                                foreach ($formValues ["userRoles"] as $roleId)
                                {
                                    if ($userRole->roleId == $roleId)
                                    {
                                        $hasRole = true;
                                        break;
                                    }
                                }

                                // If the old role is no longer needed
                                if (!$hasRole)
                                {
                                    // Delete role
                                    $userRoleMapper->delete($userRole);
                                }
                            }
                        }
                        else
                        {
                        }

                        $this->_flashMessenger->addMessage(array('success' => "User '" . $this->view->escape($formValues ["username"]) . "' saved successfully."));
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_flashMessenger->addMessage(array('danger' => $e->getMessage()));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirector('index');
            }
        }


        $this->view->form = $form;
    }
}