<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Mappers\UserMapper;
use MPSToolbox\Legacy\Models\UserModel;
use MPSToolbox\Legacy\Modules\Admin\Forms\UserForm;
use MPSToolbox\Legacy\Modules\Admin\Mappers\RoleMapper;
use MPSToolbox\Legacy\Modules\Admin\Mappers\UserRoleMapper;
use MPSToolbox\Legacy\Modules\Admin\Models\RoleModel;
use MPSToolbox\Legacy\Modules\Admin\Models\UserRoleModel;
use MPSToolbox\Legacy\Modules\DealerManagement\Services\UserService;
use Tangent\Controller\Action;

/**
 * Class Admin_UserController
 */
class Admin_UserController extends Action
{
    /**
     * Whether or not the current user has root access
     *
     * @var bool
     */
    protected $_currentUserIsRoot;

    public function init ()
    {
        $this->_currentUserIsRoot = (Zend_Auth::getInstance()->getIdentity()->id == 1);
    }

    /**
     * Used to view all users
     */
    public function indexAction ()
    {
        $this->_pageTitle = array('System', 'Users', 'User Management');
        // Fetch all the users
        $userMapper = new UserMapper();
        $users      = $userMapper->fetchUserList($this->_currentUserIsRoot);

        // Display all of the users
        $this->view->users = $users;
    }

    /**
     * Used to create a new user
     */
    public function createAction ()
    {
        $this->_pageTitle = array('Users', 'Create User');

        $dealerId = $this->getRequest()->getUserParam('id', false);

        $db   = Zend_Db_Table_Abstract::getDefaultAdapter();
        $form = new UserForm(UserForm::MODE_CREATE, $dealerId);

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                if ($form->isValid($values))
                {
                    $dealer = DealerMapper::getInstance()->find($values['dealerId']);
                    if ($dealer && $dealer->getNumberOfLicensesUsed() < $dealer->userLicenses)
                    {
                        // Save to the database
                        try
                        {
                            $db->beginTransaction();
                            $mapper = new UserMapper();

                            $user = new UserModel();
                            $user->populate($values);
                            $user->password = UserModel::cryptPassword($user->password);
                            $userId         = $mapper->insert($user);

                            if (!isset($values["userRoles"]))
                            {
                                $values['userRoles'] = array();
                            }

                            // Save changes to the user roles
                            if (isset($values ["userRoles"]))
                            {
                                $userRole         = new UserRoleModel();
                                $userRole->userId = $userId;
                                $userRoleMapper   = new UserRoleMapper();
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
                                $this->_flashMessenger->addMessage(array(
                                    'success' => "User '" . $this->view->escape($values ["email"]) . "' saved successfully."
                                ));

                                // Reset the form after everything is saved successfully
                                $form->reset();

                                // Select the same dealer so we can keep creating users easier
                                $form->populate(array('dealerId' => $values['dealerId']));
                            }
                            $db->commit();

                            if ($values['send_email'])
                            {
                                UserService::sendNewUserEmail($user, $values['password']);
                            }
                        }
                        catch (Zend_Db_Statement_Mysqli_Exception $e)
                        {
                            $db->rollBack();
                            // Check to see what error code was thrown
                            switch ($e->getCode())
                            {
                                // Duplicate column
                                case 1062 :
                                    $this->_flashMessenger->addMessage(array(
                                        'danger' => 'Username already exists.'
                                    ));
                                    break;
                                default :
                                    $this->_flashMessenger->addMessage(array(
                                        'danger' => 'Error saving to database.  Please try again.'
                                    ));
                                    break;
                            }

                            $form->populate($request->getPost());
                        }
                        catch (Exception $e)
                        {
                            $db->rollBack();
                            \Tangent\Logger\Logger::logException($e);
                            $this->_flashMessenger->addMessage(array(
                                'danger' => 'There was an error processing this request.  Please try again.'
                            ));
                            $form->populate($request->getPost());
                        }
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                            'danger' => "This dealer has reached its maxiumum user licenses of {$dealer->userLicenses}"
                        ));
                    }
                }
                else
                {
                    $this->_flashMessenger->addMessage(array(
                        'danger' => 'Please correct the errors below'
                    ));
                    $form->populate($request->getPost());
                }
            }
            else
            {
                if ($dealerId !== false)
                {
                    $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
                }
                else
                {
                    $this->redirectToRoute('admin.users');
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
        $this->_pageTitle = array('Users', 'Delete User');
        $userId           = $this->getRequest()->getUserParam('id', false);
        $dealerId         = $this->getRequest()->getUserParam('dealerId', false);

        // If they haven't provided an id, send them back to the view all users page
        if (!$userId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to delete first.'));
            $this->redirectToRoute('admin.users');
        }

        if ($userId === '1')
        {
            $this->_flashMessenger->addMessage(array('danger' => 'Insufficient Privilege: You cannot delete the root user.'));
            $this->redirectToRoute('admin.users');
        }

        $mapper = new UserMapper();
        $user   = $mapper->find($userId);

        // If the user doesn't exist, send them back t the view all users page
        if (!$user)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'There was an error selecting the user to delete.'));
            $this->redirectToRoute('admin.users');
        }


        $form = new DeleteConfirmationForm("Are you sure you want to delete {$user->email} ({$user->firstname} {$user->lastname})?");

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
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'There was an error while deleting the user'));
                }

                if ($dealerId !== false)
                {
                    $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
                }
                else
                {
                    $this->redirectToRoute('admin.users');
                }
            }
            else
            {
                if ($dealerId !== false)
                {
                    $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
                }
                else
                {
                    $this->redirectToRoute('admin.users');
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * Used to edit a user
     */
    public function editAction ()
    {
        $this->_pageTitle = array('Users', 'Edit User');
        $userId           = $this->getRequest()->getUserParam('id', false);
        $dealerId         = $this->getRequest()->getUserParam('dealerId', false);

        // If they haven't provided an id, send them back to the view all users page
        if (!$userId)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a user to delete first.'));
            if ($dealerId !== false)
            {
                $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
            }
            else
            {
                $this->redirectToRoute('admin.users');
            }
        }

        if ($userId == '1' && !$this->_currentUserIsRoot)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'Insufficient Privilege: You cannot edit the root user.'));
            if ($dealerId !== false)
            {
                $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
            }
            else
            {
                $this->redirectToRoute('admin.users');
            }
        }

        // Get the user
        $mapper = new UserMapper();
        $user   = $mapper->find($userId);

        // Get all available roles. In theory this should be a managable amount
        $roleMapper = new RoleMapper();
        $roles      = $roleMapper->fetchAll();

        $userRoleMapper = new UserRoleMapper();
        $userRoles      = $userRoleMapper->fetchAll(array('userId = ?' => $userId));

        // We need to get the current users roles
        $currentUserRoles = array();
        foreach ($userRoles as $userRole)
        {
            $currentUserRoles [] = $userRole->roleId;
        }

        // If the user doesn't exist, send them back to the view all users page
        if (!$user)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'There was an error selecting the user to delete.'));
            if ($dealerId !== false)
            {
                $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
            }
            else
            {
                $this->redirectToRoute('admin.users');
            }
        }

        // Create a new form with the mode and roles set
        $form = new UserForm(UserForm::MODE_EDIT);

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
                        $passwordChanged = false;

                        $formValues = $form->getValues();
                        // Validate the roles
                        if (isset($formValues ["userRoles"]))
                        {
                            foreach ($formValues ["userRoles"] as $roleId)
                            {
                                $roleIsValid = false;
                                /* @var $role RoleModel */
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
                            $formValues ["password"] = UserModel::cryptPassword($formValues ["password"]);
                            $passwordChanged         = true;
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

                        $mapper = new UserMapper();
                        $user   = new UserModel();
                        $user->populate($formValues);
                        $user->id = $userId;

                        // Save to the database with cascade insert turned on
                        $mapper->save($user, $userId);

                        if (!isset($formValues['userRoles']))
                        {
                            $formValues['userRoles'] = array();
                        }
                        // Save changes to the user roles
                        $userRole         = new UserRoleModel();
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
                        /* @var $userRole UserRoleModel */
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

                        $this->_flashMessenger->addMessage(array(
                            'success' => "User '" . $this->view->escape($formValues ["email"]) . "' saved successfully."
                        ));
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch (InvalidArgumentException $e)
                {
                    $this->_flashMessenger->addMessage(array(
                        'danger' => $e->getMessage()
                    ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                if ($dealerId !== false)
                {
                    $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
                }
                else
                {
                    $this->redirectToRoute('admin.users');
                }
            }
        }


        $this->view->form = $form;
    }

    public function profileAction ()
    {
        $this->_pageTitle = array('Profile', 'Users');

        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        if (!$userId)
        {
            $this->_flashMessenger->addMessage(array(
                'warning' => 'Please select a user to delete first.'
            ));
            $this->redirectToRoute('admin.users');
        }

        // Get the user
        $userMapper = new UserMapper();
        $user       = $userMapper->find($userId);

        $form = new UserForm(UserForm::MODE_USER_EDIT);
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
                        $usersByEmail = UserMapper::getInstance()->fetchUserByEmail($values['email']);
                        if ($user->email == $values['email'] || $usersByEmail === false)
                        {
                            // Update storage
                            $identity            = Zend_Auth::getInstance()->getIdentity();
                            $identity->firstname = $values ['firstname'];
                            $identity->lastname  = $values ['lastname'];
                            $identity->email     = $values ['email'];

                            $user->populate($values);
                            $userMapper->save($user, $userId);

                            $this->_flashMessenger->addMessage(array(
                                'success' => "Your profile has been updated successfully."
                            ));
                        }
                        else
                        {
                            $form->getElement('email')->addError("Email already exists");
                            $this->_flashMessenger->addMessage(array(
                                'warning' => "Your profile was not updated successfully please try again."
                            ));
                        }
                    }
                }
                    // If anything goes wrong show error message
                catch (Exception $e)
                {
                    $this->_flashMessenger->addMessage(array(
                        'warning' => "Your profile was not updated successfully please try again."
                    ));
                }
            }
            else
            {
                // Redirect user back to the home page
                $this->redirectToRoute('admin');
            }
        }
        $this->view->form = $form;
        $this->view->user = $user;
    }
}

