<?php

class Admin_UserController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    /**
     * Used to view all users
     */
    public function indexAction ()
    {
        // Fetch all the users
        $mapper = new Application_Model_Mapper_User();
        $users  = $mapper->fetchAll();

        // Display all of the users
        $this->view->users = $users;
    }

    /**
     * Used to create a new user
     */
    public function createAction ()
    {
        $db         = Zend_Db_Table_Abstract::getDefaultAdapter();
        $roleMapper = new Admin_Model_Mapper_Role();
        $roles      = $roleMapper->fetchAll();
        $form       = new Admin_Form_User(Admin_Form_User::MODE_CREATE, $roles);

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();

            if (!isset($values ['cancel']))
            {

                if ($form->isValid($values))
                {
                    // Save to the database
                    try
                    {
                        $db->beginTransaction();


                        $mapper = new Application_Model_Mapper_User();
                        $user   = new Application_Model_User();
                        $user->populate($values);
                        $user->password = $this->cryptPassword($user->password);
                        $userId         = $mapper->insert($user);
                        // Save changes to the user roles
                        if (isset($values ["userRoles"]))
                        {
                            $userRole         = new Admin_Model_UserRole();
                            $userRole->userId = $userId;
                            $userRoleMapper   = new Admin_Model_Mapper_UserRole();
                            $userRoles        = $userRoleMapper->fetchAll(array(
                                                                               'userId = ?' => $userId
                                                                          ));
                            // Loop through our new roles
                            foreach ($values ["userRoles"] as $roleId)
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
                            $this->_helper->flashMessenger(array(
                                                                'success' => "User '" . $this->view->escape($values ["username"]) . "' saved sucessfully."
                                                           ));

                            // Reset the form after everything is saved successfully
                            $form->reset();
                        }

                        $db->commit();

                        /*
                         * Send Email
                         */


                    }
                    catch (Zend_Db_Statement_Mysqli_Exception $e)
                    {
                        $db->rollBack();
                        // Check to see what error code was thrown
                        switch ($e->getCode())
                        {
                            // Duplicate column
                            case 1062 :
                                $this->_helper->flashMessenger(array(
                                                                    'danger' => 'Username already exists.'
                                                               ));
                                break;
                            default :
                                $this->_helper->flashMessenger(array(
                                                                    'danger' => 'Error saving to database.  Please try again.'
                                                               ));
                                break;
                        }

                        $form->populate($request->getPost());
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        My_Log::logException($e);
                        $this->_helper->flashMessenger(array(
                                                            'danger' => 'There was an error processing this request.  Please try again.'
                                                       ));
                        $form->populate($request->getPost());
                    }
                }
                else
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => 'Please correct the errors below'
                                                   ));
                    $form->populate($request->getPost());
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * Used to delete a user
     */
    public function deleteAction ()
    {
        $userId = $this->_getParam('id', false);

        // If they haven't provided an id, send them back to the view all users page
        if (!$userId)
        {
            $this->_helper->flashMessenger(array(
                                                'warning' => 'Please select a user to delete first.'
                                           ));
            $this->_redirect('/admin/user');
        }
        if ($userId === '1')
        {
            $this->_helper->flashMessenger(array(
                                                'danger' => 'Insufficient Privilege: cannot delete root user.'
                                           ));
            $this->_redirect('/admin/user');
        }

        $mapper = new Application_Model_Mapper_User();
        $user   = $mapper->find($userId);

        // If the user doesn't exist, send them back t the view all users page
        if (!$user)
        {
            $this->_helper->flashMessenger(array(
                                                'danger' => 'There was an error selecting the user to delete.'
                                           ));
            $this->_redirect('/admin/user');
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

                    $this->_helper->flashMessenger(array(
                                                        'success' => "User deleted."
                                                   ));

                    $this->_redirect('/admin/user');
                }
                else
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => 'There was an error while deleting the user'
                                                   ));
                    $this->_redirect('/admin/user');
                }
            }
            else
            {
                // User has cancelled.
                $this->_redirect('/admin/user');
            }
        }

        $this->view->form = $form;
    }

    /**
     * Used to edit a user
     */
    public function editAction ()
    {
        $userId = $this->_getParam('id', false);

        // If they haven't provided an id, send them back to the view all users page
        if (!$userId)
        {
            $this->_helper->flashMessenger(array(
                                                'warning' => 'Please select a user to delete first.'
                                           ));
            $this->_redirect('/admin/user');
        }

        // Get the user
        $mapper = new Application_Model_Mapper_User();
        $user   = $mapper->find($userId);

        // Get all available roles. In theory this should be a managable amount
        $roleMapper = new Admin_Model_Mapper_Role();
        $roles      = $roleMapper->fetchAll();

        $userRoleMapper = new Admin_Model_Mapper_UserRole();
        $userRoles      = $userRoleMapper->fetchAll(array(
                                                         'userId = ?' => $userId
                                                    ));

        // We need to get the current users roles
        $currentUserRoles = array();
        foreach ($userRoles as $userRole)
        {
            $currentUserRoles [] = $userRole->roleId;
        }

        // If the user doesn't exist, send them back t the view all users page
        if (!$user)
        {
            $this->_helper->flashMessenger(array(
                                                'danger' => 'There was an error selecting the user to delete.'
                                           ));
            $this->_redirect('/admin/user');
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
                                    if ($role->getId() == $roleId)
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
                            $formValues ["password"] = $this->cryptPassword($formValues ["password"]);
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

                        $this->_helper->flashMessenger(array(
                                                            'success' => "User '" . $this->view->escape($formValues ["username"]) . "' saved successfully."
                                                       ));
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => $e->getMessage()
                                                   ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }


        $this->view->form = $form;
    }

    /**
     * Encrypts a password using a salt.
     *
     * @param string $password
     *
     * @throws Exception
     * @return string
     */
    private function cryptPassword ($password)
    {
        if (!defined("CRYPT_SHA512") || CRYPT_SHA512 != 1)
        {
            throw new Exception("Error, SHA512 encryption not available");
        }

        // What method to use (6 is SHA512)
        $method = '6';
        // How many rounds to do.
        $rounds = 'rounds=5000';
        // Random string to make it better
        $pepper = 'lunchisdabest';

        // Combine them all '$6$rounds=5000$randomstring$'
        $salt = sprintf('$%1$s$%2$s$%3$s$', $method, $rounds, $pepper);

        return crypt($password, $salt);
    }

    public function profileAction ()
    {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        if (!$userId)
        {
            $this->_helper->flashMessenger(array(
                                                'warning' => 'Please select a user to delete first.'
                                           ));
            $this->_redirect('/admin/user');
        }

        // Get the user
        $userMapper = new Application_Model_Mapper_User();
        $user       = $userMapper->find($userId);

        $form = new Admin_Form_User(2);
        $form->populate($user->toArray());

        $request = $this->getRequest();

        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            // If we cancelled we don't need to validate anything
            if (!isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Update the user information
                        $user->populate($values);
                        $userMapper->save($user, $userId);

                        // Update storage
                        $storage              = Zend_Auth::getInstance()->getStorage();
                        $indentity            = Zend_Auth::getInstance()->getIdentity();
                        $indentity->firstname = $values ['firstname'];
                        $indentity->lastname  = $values ['lastname'];
                        $indentity->email     = $values ['email'];

                        $this->_helper->flashMessenger(array(
                                                            'success' => "User {$user->username} has been updated successfully."
                                                       ));
                    }
                }
                    // If anything goes wrong show error message
                catch (Exception $e)
                {
                    $this->_helper->flashMessenger(array(
                                                        'warning' => "User was not updated successfully please try again.	"
                                                   ));
                }
            }
            else
            {
                // Redirect user back to the home page
                $this->_helper->redirector('index', 'index', 'default');
            }
        }
        $this->view->form = $form;
    }
}

