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
        $mapper = new Application_Model_UserMapper();
        $users = $mapper->fetchAll();
        
        // Display all of the users
        $this->view->users = $users;
    }

    /**
     * Used to view a user
     */
    public function viewAction ()
    {
        $id = $this->_getParam('id', 0);
        if ($id < 1)
        {
            throw new Exception("Error getching user", 500);
        }
        
        // Get the user
        $mapper = new Application_Model_UserMapper();
        $user = $mapper->fetch(array (
                'id = ?' => $id 
        ));
        
        // Pass false unless we found the user
        $this->view->user = false;
        if ($user)
        {
            $this->view->user = $user;
        }
    }

    /**
     * Used to create a new user
     */
    public function createAction ()
    {
        $form = new Admin_Form_User();
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (! isset($values ['cancel']))
            {
                
                if ($form->isValid($values))
                {
                    // Save to the database
                    try
                    {
                        $mapper = new Application_Model_UserMapper();
                        $user = new Application_Model_User();
                        $user->populate($values);
                        $user->setPassword($this->cryptPassword($user->getPassword()));
                        $userId = $mapper->insert($user);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "User '" . $this->view->escape($values ["username"]) . "' saved sucessfully." 
                        ));
                        
                        // Reset the form after everything is saved successfully
                        $form->reset();
                    }
                    catch ( Zend_Db_Statement_Mysqli_Exception $e )
                    {
                        // Check to see what error code was thrown
						switch ($e->getCode()){
						    // Duplicate column
							case 1062:
							    $this->_helper->flashMessenger(array (
							            'danger' => 'Username already exists.'
							    ));
							    break;
							default:
							    $this->_helper->flashMessenger(array (
							            'danger' => 'Error saving to database.  Please try again.'
							    ));
							    break;
						}
                        
                        $form->populate($request->getPost());
                    }
                    catch ( Exception $e) 
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'There was an error processing this request.  Please try again.'
                        ));
                        $form->populate($request->getPost());
                    }
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => 'Please correct the errors below' 
                    ));
                    $form->populate($request->getPost());
                }
            }
            else
            {
            // TODO handle cancel request
            // User has cancelled. We could do a redirect here if we wanted.
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
        if (! $userId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a user to delete first.' 
            ));
            $this->_redirect('/admin/user');
        }
        
        $mapper = new Application_Model_UserMapper();
        $user = $mapper->find($userId);
        
        // If the user doesn't exist, send them back t the view all users page
        if (! $user)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the user to delete.' 
            ));
            $this->_redirect('/admin/user');
        }
        
        $username = $user->getUsername();
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        
        $form = new Application_Form_Delete("Are you sure you want to delete {$username} ({$firstname} {$lastname})?");
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (! isset($values ['cancel']))
            {
                
                if ($form->isValid($values))
                {
                    $mapper->delete($user);
                    
                    $this->_helper->flashMessenger(array (
                            'success' => "User deleted." 
                    ));
                    
                    $this->_redirect('/admin/user');
                }
                else
                {
                    $this->_helper->flashMessenger(array (
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
        if (! $userId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a user to delete first.' 
            ));
            $this->_redirect('/admin/user');
        }
        
        // Get the user
        $mapper = new Application_Model_UserMapper();
        $user = $mapper->find($userId);
        
        // Get all available roles. In theory this should be a managable amount
        $rolemapper = new Admin_Model_RoleMapper();
        $roles = $rolemapper->fetchAll();
        
        $userrolemapper = new Admin_Model_UserRoleMapper();
        $userRoles = $userrolemapper->fetchAll(array (
                'userId = ?' => $userId 
        ));
        
        // We need to get the current users roles
        $currentUserRoles = array ();
        foreach ( $userRoles as $userRole )
        {
            $currentUserRoles [] = $userRole->getRoleId();
        }
        
        // If the user doesn't exist, send them back t the view all users page
        if (! $user)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the user to delete.' 
            ));
            $this->_redirect('/admin/user');
        }
        
        // Create a new form with the mode and roles set
        $form = new Admin_Form_User(Admin_Form_User::MODE_EDIT, $roles);
        
        // Prepare the data for the form
        $values = $user->toArray();
        $values ['userRoles'] = $currentUserRoles;
        
        $request = $this->getRequest();
        
        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            
            // If we cancelled we don't need to validate anything
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Validate the roles
                        if (isset($values ["userRoles"]))
                        {
                            foreach ( $values ["userRoles"] as $roleId )
                            {
                                $roleIsValid = false;
                                /* @var $role Admin_Model_Role */
                                foreach ( $roles as $role )
                                {
                                    if ($role->getId() == $roleId)
                                    {
                                        $roleIsValid = true;
                                        break;
                                    }
                                }
                                
                                // Give an error if a role was not valid
                                if (! $roleIsValid)
                                {
                                    throw new Exception("Invalid Role Selected!");
                                }
                            }
                        }
                        
                        // Set the password values if we checked the reset password box
                        if (isset($values ["password"]) && ! empty($values ["password"]) && $values ["reset_password"])
                        {
                            unset($values ["passwordconfirm"]);
                            $values ["password"] = $this->cryptPassword($values ["password"]);
                        }
                        else
                        {
                            // TODO: This whole password area could be done in a more logical sequence
                            if ($values ["reset_password"])
                            {
                                throw new InvalidArgumentException("You must specify a new password");
                            }
                            unset($values ["password"]);
                            unset($values ["passwordconfirm"]);
                        }
                        
                        // Unset an empty date
                        if (isset($values ["frozenUntil"]) && empty($values ["frozenUntil"]))
                        {
                            unset($values ["frozenUntil"]);
                        }
                        
                        // Unset an empty date
                        if (isset($values ["eulaAccepted"]) && empty($values ["eulaAccepted"]))
                        {
                            unset($values ["eulaAccepted"]);
                        }
                        
                        $mapper = new Application_Model_UserMapper();
                        $user = new Application_Model_User();
                        $user->populate($values);
                        $user->setId($userId);
                        
                        // Save to the database with cascade insert turned on
                        $userId = $mapper->save($user, $userId);
                        
                        // Save changes to the user roles
                        if (isset($values ["userRoles"]))
                        {
                            $userRole = new Admin_Model_UserRole();
                            $userRole->setUserId($userId);
                            
                            // Loop through our new roles
                            foreach ( $values ["userRoles"] as $roleId )
                            {
                                $hasRole = false;
                                /* @var $userRole Admin_Model_UserRole */
                                foreach ( $userRoles as $userRole )
                                {
                                    if ($userRole->getRoleId() == $roleId)
                                    {
                                        $hasRole = true;
                                        break;
                                    }
                                }
                                
                                // If the role is new
                                if (! $hasRole)
                                {
                                    $userRole->setRoleId($roleId);
                                    $userrolemapper->insert($userRole);
                                }
                            }
                            
                            // Loop through our old roles to see which were removed
                            /* @var $userRole Admin_Model_UserRole */
                            foreach ( $userRoles as $userRole )
                            {
                                $hasRole = false;
                                
                                foreach ( $values ["userRoles"] as $roleId )
                                {
                                    if ($userRole->getRoleId() == $roleId)
                                    {
                                        $hasRole = true;
                                        break;
                                    }
                                }
                                
                                // If the old role is no longer needed
                                if (! $hasRole)
                                {
                                    // Delete role
                                    $userrolemapper->delete($userRole);
                                }
                            }
                        }
                        else
                        {
                            // If the user deselected all the boxes, delete all the roles
                            if (count($userRoles) > 0)
                            {
                                $userrolemapper->delete(array (
                                        'userId' => $userId 
                                ));
                            }
                        }
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "User '" . $this->view->escape($values ["username"]) . "' saved sucessfully." 
                        ));
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch ( InvalidArgumentException $e )
                {
                    $this->_helper->flashMessenger(array (
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
        
        $form->populate($values);
        $this->view->form = $form;
    }

    /**
     * Encrypts a password using a salt.
     *
     * @param string $password            
     * @throws Exception
     * @return string
     */
    private function cryptPassword ($password)
    {
        if (! defined("CRYPT_SHA512") || CRYPT_SHA512 != 1)
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
}

