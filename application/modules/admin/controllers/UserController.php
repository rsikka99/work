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
            throw new Exception("Error Getting Stuff", 500);
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
                    $mapper = new Application_Model_UserMapper();
                    $user = new Application_Model_User();
                    $user->populate($values);
                                        
                    // Save to the database
                    $userId = $mapper->insert($user);
                    
                    /*
                     * $userProfileMapper = new Application_Model_User_ProfileMapper();
                     * $userProfileMapper->save($userProfile);
                     */
                    
                    $this->_helper->flashMessenger(array (
                            'success' => "User '" . $this->view->escape($values ["username"]) . "' saved sucessfully." 
                    ));
                    
                    // Reset the form after everything is saved successfully
                    $form->reset();
                
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
        $firstname = $user->getUserProfile()->getFirstName();
        $lastname = $user->getUserProfile()->getLastName();
        
        $form = new Application_Form_Delete("Are you sure you want to delete {$username} ({$firstname} {$lastname})?");
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (! isset($values ['cancel']))
            {
                
                if ($form->isValid($values))
                {
                    // Delete the user. Only cascade if they have a profile
                    if ($user->getUserProfile() === null)
                    {
                        $mapper->delete($user->getId(), false);
                    }
                    else
                    {
                        $mapper->delete($user->getId(), true);
                    }
                    
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
        
        $form = new Admin_Form_User(Admin_Form_User::MODE_EDIT);
        
        $values = $user->toArray(); 

        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (! isset($values ['cancel']))
            {
                
                if ($form->isValid($values))
                {
                    $mapper = new Application_Model_UserMapper();
                    $user = new Application_Model_User();
                    if (isset($values["password"]))
                    {
                        unset($values["passwordconfirm"]);
                        $values["password"] = $this->cryptPassword($values ["password"]);
                    }
                    $user->populate($values);
                    $user->setId($userId);
                    
                    
                    // Save to the database with cascade insert turned on
                    $userId = $mapper->save($user, $userId);
                    
                    /*
                     * $userProfileMapper = new Application_Model_User_ProfileMapper();
                     * $userProfileMapper->save($userProfile);
                     */
                    
                    $this->_helper->flashMessenger(array (
                            'success' => "User '" . $this->view->escape($values ["username"]) . "' saved sucessfully." 
                    ));
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => 'Please correct the errors below' 
                    ));
                    $form->populate($values);
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
            }
        }
        else
        {
            $form->populate($values);
        }
        
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

