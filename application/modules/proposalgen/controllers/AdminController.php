<?php

/**
 * Admin Controller: This controller handles all administrator actions.
 *
 * @author Chris Garrah
 */
class Proposalgen_AdminController extends Zend_Controller_Action
{
    protected $_redirector = null;

    function init ()
    {
        $this->config = Zend_Registry::get('config');
        $this->initView();
        $this->view->app = $this->config->app;
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->view->privilege = Zend_Auth::getInstance()->getIdentity()->privileges;
        $this->user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->privilege = Zend_Auth::getInstance()->getIdentity()->privileges;
        $this->dealer_company_id = Zend_Auth::getInstance()->getIdentity()->dealer_company_id;
        $this->MPSProgramName = $this->config->app->MPSProgramName;
        $this->view->MPSProgramName = $this->config->app->MPSProgramName;
        $this->ApplicationName = $this->config->app->ApplicationName;
    }

    /**
     * Default action - Show the list of admin options
     */
    public function indexAction ()
    {
        $this->view->title = "Admin Console";
        $session = new Zend_Session_Namespace('proposalgenerator_report');
        $config = Zend_Registry::get('config');
        $this->MPSProgramName = $config->app->MPSProgramName;
        
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
        $dealer_company = $dealer_companyTable->fetchRow($where);
        $session->dealerName = $dealer_company->company_name;
        $this->view->dealer = $dealer_company->company_name;
        return;
    } // end indexAction

    
    /**
     * The managecompaniesAction provides a list of active companies for the
     * system admin to choose from and manage.
     * The details form gets posted back
     * and updated or inserted if new
     */
    public function managecompaniesAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = 'Manage Companies';
        $date = date('Y-m-d H:i:s T');
        $this->view->repop = false;
        
        // add company form;
        $form = new Proposalgen_Form_Companies(null, "edit");
        
        // fill companies dropdown
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $dealer_companies = $dealer_companyTable->fetchAll('company_name != "MASTER"', 'company_name');
        $currElement = $form->getElement('select_company');
        $currElement->addMultiOption("0", "Add New Company");
        foreach ( $dealer_companies as $row )
        {
            $currElement->addMultiOption($row->dealer_company_id, ucwords(strtolower($row->company_name)));
        }
        
        // default delete button to disabled
        $form->getElement('delete_company')->setAttrib('disabled', 'disabled');
        
        if ($this->_request->isPost())
        {
            // get form values
            $formData = $this->_request->getPost();
            
            if ($form->isValid($formData))
            {
                $date = date('Y-m-d H:i:s T');
                $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
                $dealer_company_id = $formData ['select_company'];
                $dealer_company_name = $formData ['company_name'];
                $pricing_margin = $formData ['pricing_margin'];
                
                $db->beginTransaction();
                try
                {
                    if (array_key_exists('save_company', $formData) && $formData ['save_company'] == "Save")
                    {
                        // company data
                        $companyData = array (
                                'company_name' => $dealer_company_name, 
                                'company_logo' => null, 
                                'company_report_color' => null, 
                                'dc_pricing_margin' => $pricing_margin 
                        );
                        
                        if ($dealer_company_id > 0)
                        {
                            // update company
                            $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $dealer_company_id, 'INTEGER');
                            $dealer_companyTable->update($companyData, $where);
                            $this->view->message = 'Company "' . $dealer_company_name . '" has been updated';
                        }
                        else
                        {
                            $where = $dealer_companyTable->getAdapter()->quoteInto('company_name = ?', $dealer_company_name);
                            $dealer_company = $dealer_companyTable->fetchRow($where);
                            
                            if (count($dealer_company) > 0)
                            {
                                $this->view->message = 'Company "' . $dealer_company_name . '" already exists.';
                            }
                            else
                            {
                                $dealer_companyTable->insert($companyData);
                                $this->view->message = 'Company "' . $dealer_company_name . '" Added.';
                            }
                        }
                    }
                    else if (array_key_exists('delete_company', $formData) && $formData ['delete_company'] == "Delete")
                    {
                        if ($dealer_company_id > 0)
                        {
                            $status = true;
                            
                            // Get users for company
                            $usersTable = new Proposalgen_Model_DbTable_Users();
                            $where = $usersTable->getAdapter()->quoteInto('dealer_company_id = ?', $dealer_company_id);
                            $users = $usersTable->fetchAll($where);
                            // Delete all the users for the company
                            foreach ( $users as $key )
                            {
                                $selUserID = $key ['user_id'];
                                $status = $this->deleteUser($selUserID);
                                
                                if (! $status)
                                {
                                    $this->view->message = "An error has occurred while deleting the companies users and the company was not deleted. Please try again. If the problem persists, please contact your administrator.";
                                    exit();
                                }
                            }
                            
                            // Delete the company
                            if ($status)
                            {
                                $criteria = "dealer_company_id = " . $dealer_company_id;
                                
                                // dealer device override
                                $dealer_device_overideTable = new Proposalgen_Model_DbTable_DealerDeviceOverride();
                                $where = $dealer_device_overideTable->getAdapter()->quoteInto($criteria, null);
                                $dealer_device_overideTable->delete($where);
                                
                                // dealer toner override
                                $dealer_toner_overideTable = new Proposalgen_Model_DbTable_DealerTonerOverride();
                                $where = $dealer_toner_overideTable->getAdapter()->quoteInto($criteria, null);
                                $dealer_toner_overideTable->delete($where);
                                
                                // delete dealer companay
                                $where = $dealer_companyTable->getAdapter()->quoteInto($criteria, null);
                                $dealer_companyTable->delete($where);
                                
                                $this->view->message = "\"" . $dealer_company_name . "\" was successfully deleted.";
                            }
                        }
                    }
                    $db->commit();
                    
                    // refill manufaturers dropdown
                    $dealer_companies = $dealer_companyTable->fetchAll('company_name != "Master"', 'company_name');
                    $currElement = $form->getElement('select_company');
                    $currElement->clearMultiOptions();
                    $currElement->addMultiOption('0', 'Add New Company');
                    foreach ( $dealer_companies as $row )
                    {
                        $currElement->addMultiOption($row ['dealer_company_id'], ucwords(strtolower($row ['company_name'])));
                    }
                    
                    // reset form
                    $form->getElement('company_name')->setValue('');
                    $form->getElement('pricing_margin')->setValue('25');
                }
                catch ( Zend_Db_Exception $e )
                {
                    $db->rollback();
                    $this->view->message = 'Database Error: Company "' . $formData ["company_name"] . '" could not be saved.';
                }
                catch ( Exception $e )
                {
                    // CRITICAL UPDATE EXCEPTION
                    $db->rollback();
                    Throw new exception("Critical Company Update Error.", 0, $e);
                } // end catch
            }
            else
            {
                $this->view->repop = true;
                $this->view->message = 'Error: Invalid data. Please review your entries and try again.';
            }
        }
        
        $this->view->companyForm = $form;
    }

    /**
     * The companydetailsAction accepts a companyid as a GET parameter, and
     * returns
     * information about the corresponding user in json format.
     * Jquery code
     * requesting data from this action is located in on the main layout page.
     */
    public function companydetailsAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // Disable the default layout
        $this->_helper->layout->disableLayout();
        
        $companyid = $this->_getParam('companyid', false);
        
        // clear any existing form validation
        $form = new Proposalgen_Form_Companies(null, 'edit');
        $form->reset();
        
        // return company details
        try
        {
            if ($companyid > 0)
            {
                $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
                $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $companyid);
                $row = $dealer_companyTable->fetchRow($where);
                
                $formdata = array (
                        'dealer_company_id' => $row ['dealer_company_id'], 
                        'company_name' => $row ['company_name'], 
                        'company_logo' => $row ['company_logo'], 
                        'company_report_color' => $row ['company_report_color'], 
                        'pricing_margin' => $row ['dc_pricing_margin'], 
                        'is_deleted' => $row ['is_deleted'] 
                );
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // CRITICAL EXCEPTION
            Throw new exception("Critical Error: Unable to find company.", 0, $e);
        } // end catch
        

        // Encode company data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * The manageusersAction provides a list of active users for the
     * system admin or dealer to choose from and manage.
     * The details are posted back
     * and updated or inserted if new
     */
    public function manageusersAction ()
    {
        $this->view->title = 'Manage Users';
        $db = Zend_Db_Table::getDefaultAdapter();
        $pword = '';
        $type = '';
        $dealer_company_id = $this->dealer_company_id;
        
        // if dealer admin, sets the where variable
        if (in_array("Dealer Admin", $this->privilege))
        {
            $type = 'dealer';
            $wherePriv = "priv_type <> 'System Admin'";
            $whereUsers = "dealer_company_id = " . $dealer_company_id;
            $whereCompany = " AND dealer_company_id = " . $dealer_company_id;
        }
        else
        {
            $type = '';
            $whereUsers = null;
            $wherePriv = null;
            $whereCompany = null;
        }
        $dealer_company_name = '';
        $company_list_array = array ();
        
        // fill companies filter
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $dealer_companies_filter = $dealer_companyTable->fetchAll('company_name <> "Master"', 'company_name');
        foreach ( $dealer_companies_filter as $row )
        {
            $type = 'dealer';
            $dealer_company_name = "Office Depot";
            if ($row->company_name == $dealer_company_name)
            {
                // $dealer_company_id = $row->dealer_company_id;
                // $company_list_array[$row->dealer_company_id] =
                // $row->company_name;
                $dealer_company_id = 2;
            }
        }
        $this->view->companyList = $company_list_array;
        
        // create user form
        $form = new Proposalgen_Form_User(null, $type);
        
        // fill location dropdown on offering form
        $currElement = $form->getElement('select_user');
        
        // grab all current users from the db
        $userTable = new Proposalgen_Model_DbTable_Users();
        $allUsers = $userTable->fetchAll($whereUsers, array (
                'lastname', 
                'firstname', 
                'username' 
        ));
        $currElement->addMultiOption("0", "Add New User");
        foreach ( $allUsers as $row )
        {
            $currElement->addMultiOption($row->user_id, ucwords(strtolower($row->lastname)) . ", " . ucwords(strtolower($row->firstname)) . " (" . strtolower($row->username) . ")");
        }
        
        // fill privileges drop down
        $privilegeTable = new Proposalgen_Model_DbTable_Privileges();
        $allPrivileges = $privilegeTable->fetchAll($wherePriv, 'priv_type');
        $currElement = $form->getElement('privileges');
        $currElement->addMultiOption('', 'Select a Privilege');
        foreach ( $allPrivileges as $row )
        {
            if ($row->priv_type == "Dealer Admin")
            {
                // skip it - PrintIQ does not have a dealer admin
            }
            else
            {
                $currElement->addMultiOption($row->priv_id, $row->priv_type);
            }
        }
        
        // if a dealer admin is using this page, the company dropdown is set to
        // the company the dealer belongs to
        // and the drop down is disabled.
        if ($type == 'dealer')
        {
            $form->getElement('select_company')->setValue($dealer_company_name);
        }
        else
        {
            // fill companies dropdown
            $dealer_companies = $dealer_companyTable->fetchAll('is_deleted = 0' . $whereCompany, 'company_name');
            $currElement = $form->getElement('select_company');
            $currElement->addMultiOption('', 'Select a Company');
            foreach ( $dealer_companies as $row )
            {
                $currElement->addMultiOption($row->dealer_company_id, ucwords(strtolower($row->company_name)));
            }
        }
        
        // disable delete by default
        $form->getElement('delete_user')->setAttrib('disabled', 'disabled');
        
        // check if this page has been posted to
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            
            if ($formData ['select_company'] == "1" && $type != "dealer")
            {
                $currElement = $form->getElement('select_company');
                $currElement->addMultiOption(1, 'Master');
                $currElement->setAttrib('disabled', 'disabled');
            }
            
            // conditional requirements
            $form->set_validation($formData);
            
            if ($form->isValid($formData))
            {
                // get selected user
                $isvalid = true;
                $currElement = $form->getElement('select_user');
                $selUserID = $currElement->getValue();
                $user_privilegesTable = new Proposalgen_Model_DbTable_UserPrivileges();
                
                // Update the selected user
                $db->beginTransaction();
                try
                {
                    if (array_key_exists('save_user', $formData) && $formData ['save_user'] == "Save")
                    {
                        $date = date('Y-m-d H:i:s T');
                        
                        if ($type != 'dealer')
                        {
                            $dealer_company_id = $formData ["select_company"];
                        }
                        
                        // OVERRIDE DEALER COMPANY ID DEPENDING ON WHAT IS
                        // SELECTED IN THE PRIVILEGE DROPDOWN
                        $dealer_company_id = 2;
                        if ($formData ['privileges'] == '1')
                        {
                            $dealer_company_id = 1;
                        }
                        
                        // user data
                        $userdata = array (
                                'dealer_company_id' => $dealer_company_id, 
                                'username' => $formData ["username"], 
                                'firstname' => $formData ["userFirstName"], 
                                'lastname' => $formData ["userLastName"], 
                                'telephone' => $formData ["userPhone"], 
                                'email' => $formData ["userEmail"], 
                                'update_password' => $formData ["must_change"], 
                                'is_activated' => $formData ["is_activated"] 
                        );
                        
                        // privilege data
                        $privilegedata = array (
                                'priv_id' => $formData ["privileges"] 
                        );
                        
                        // find company name
                        $companyname = '';
                        $where = $dealer_companyTable->getAdapter()->quoteInto("dealer_company_id = ?", $dealer_company_id);
                        $dealer_company = $dealer_companyTable->fetchRow($where);
                        if (count($dealer_company) > 0)
                        {
                            $companyname = $dealer_company ['company_name'];
                        }
                        
                        if ($selUserID > 0)
                        {
                            // prep password for saving
                            $pword = '';
                            if ($formData ["update_password"] == "1")
                            {
                                // password_mode (true == auto)
                                if ($formData ["password_mode"] == "true")
                                {
                                    $pword = $formData ["auto_password"];
                                }
                                else
                                {
                                    $pword = $formData ["password"];
                                }
                                
                                // prepare message to be sent by email
                                $subject = $this->ApplicationName . " Password Change";
                                
                                $body = "";
                                $body .= "<body>";
                                $body .= "<p>Your " . $this->ApplicationName . " password has been changed by the administrator.</p>";
                                $body .= "<p></p>";
                                $body .= "<p>Your new password is: " . $pword . "</p>";
                                $body .= "<p></p>";
                                
                                if ($formData ["must_change"] == 1)
                                {
                                    $body .= "<p>You will be required to change your password the first time you log in.</p>";
                                    $body .= "<p></p>";
                                }
                                
                                $body .= "<p>Please use this password to log into the application.</p>";
                                $body .= "</body>";
                                
                                $userdata ["password"] = md5($pword);
                                $userdata ["update_password"] = $formData ["must_change"];
                            }
                            
                            // update user
                            $where = $userTable->getAdapter()->quoteInto('user_id = ?', $selUserID);
                            $userTable->update($userdata, $where);
                            
                            // update privilege
                            $privilegedata ['user_id'] = $selUserID;
                            $where = $privilegeTable->getAdapter()->quoteInto('user_id = ?', $selUserID);
                            $user_privilegesTable->update($privilegedata, $where);
                        }
                        else
                        {
                            // add user
                            $userdata ["is_activated"] = true;
                            $userdata ["date_created"] = $date;
                            
                            // check to make sure username doesn't exist
                            $where = $userTable->getAdapter()->quoteInto('username = ?', $formData ["username"]);
                            $user = $userTable->fetchRow($where);
                            
                            if (count($user) > 0)
                            {
                                $isvalid = false;
                                $this->view->message = 'Username "' . $formData ["username"] . '" already exists. Please enter a different username and try again.';
                            }
                            else
                            {
                                // prep password
                                if ($formData ["password_mode"] == "true")
                                {
                                    $pword = $formData ["auto_password"];
                                }
                                else
                                {
                                    $pword = $formData ["password"];
                                }
                                $userdata ["password"] = md5($pword);
                                $userdata ["update_password"] = $formData ["must_change"];
                                
                                $userid = $userTable->insert($userdata);
                                
                                // add privilege
                                $privilegedata ['user_id'] = $userid;
                                $user_privilegesTable->insert($privilegedata);
                                
                                // prepare message to be sent by email
                                $subject = "Your " . $this->ApplicationName . " Account is Ready";
                                
                                $body = "";
                                $body .= "<body>";
                                $body .= "<h2>Your " . $this->ApplicationName . " account has been created and is ready to be used.</h2>";
                                $body .= "<p>The information submitted is as follows:</p>";
                                $body .= "<ul>";
                                $body .= "<li>Company: " . $companyname . "</li>";
                                $body .= "<li>Username: " . $formData ['username'] . "</li>";
                                $body .= "<li>First Name: " . $formData ['userFirstName'] . "</li>";
                                $body .= "<li>Last Name: " . $formData ['userLastName'] . "</li>";
                                $body .= "<li>Phone: " . $formData ['userPhone'] . "</li>";
                                $body .= "<li>Email Address: " . $formData ['userEmail'] . "</li>";
                                $body .= "<li>Password: " . $pword . "</li>";
                                $body .= "</ul>";
                                
                                if ($formData ["must_change"] == 1)
                                {
                                    $body .= "<p>You will be required to change your password the first time you log in.</p>";
                                    $body .= "<p></p>";
                                }
                                
                                // $body .= "<p>Your administrator is " .
                                // Zend_Auth::getInstance()->getIdentity()->username
                                // . " and their email address is ";
                                // $body .= "<a href='mailto:" .
                                // Zend_Auth::getInstance()->getIdentity()->email
                                // . "'>" .
                                // Zend_Auth::getInstance()->getIdentity()->email
                                // . "</a>.</p>";
                                // $body .= "<p></p>";
                                // $body .= "<p>If there is a problem with any
                                // of the details above, please contact your
                                // administrator right away.";
                                // $body .= "<p></p>";
                                $body .= "<p>Visit the <a href='" . $this->view->ServerUrl() . $this->view->baseUrl('/auth/login') . "' target='_blank'>" . $this->ApplicationName . "</a> application online to log into your account at any time.</p>";
                                $body .= "<p></p>";
                                $body .= "</body>";
                            }
                        }
                        
                        if ($isvalid == true)
                        {
                            $this->view->message = 'User "' . $formData ["userFirstName"] . " " . $formData ["userLastName"] . '" Saved.';
                        }
                    }
                    else if (array_key_exists('delete_user', $formData) && $formData ['delete_user'] == "Delete")
                    {
                        $can_delete = true;
                        $criteria = "user_id = " . $selUserID;
                        
                        // get current users privileges
                        $priv_array = array ();
                        
                        $select = new Zend_Db_Select($db);
                        $select = $db->select()
                            ->from(array (
                                'u' => 'users' 
                        ))
                            ->joinLeft(array (
                                'up' => 'user_privileges' 
                        ), 'up.user_id = u.user_id')
                            ->joinLeft(array (
                                'p' => 'privileges' 
                        ), 'p.priv_id = up.priv_id')
                            ->where('u.user_id = ?', $selUserID, 'INTEGER');
                        $stmt = $db->query($select);
                        $result = $stmt->fetchAll();
                        foreach ( $result as $key )
                        {
                            $priv_array += array (
                                    $key ['priv_type'] 
                            );
                        }
                        
                        // if last system admin don't allow delete
                        if (in_array("System Admin", $priv_array))
                        {
                            $select = new Zend_Db_Select($db);
                            $select = $db->select()
                                ->from(array (
                                    'u' => 'users' 
                            ))
                                ->joinLeft(array (
                                    'up' => 'user_privileges' 
                            ), 'up.user_id = u.user_id')
                                ->joinLeft(array (
                                    'p' => 'privileges' 
                            ), 'p.priv_id = up.priv_id')
                                ->where('p.priv_type = "System Admin"');
                            // echo $select; die;
                            $stmt = $db->query($select);
                            $result = $stmt->fetchAll();
                            
                            if (count($result) == 1)
                            {
                                $can_delete = false;
                            }
                        }
                        
                        if ($can_delete == true)
                        {
                            $status = $this->deleteUser($selUserID);
                            
                            // return message
                            if ($status)
                            {
                                $this->view->message = 'User "' . $formData ["userFirstName"] . " " . $formData ["userLastName"] . '" was deleted.';
                            }
                            else
                            {
                                $this->view->message = 'An error occurred. User "' . $formData ["userFirstName"] . " " . $formData ["userLastName"] . '" was not deleted. Please try again.';
                            }
                        }
                        else
                        {
                            $this->view->message = 'This account is the only remaining System Admin account and can not be deleted.';
                        }
                    }
                    $db->commit();
                    
                    // if we have an email body, send the email
                    if (! empty($body))
                    {
                        $email = new Custom_Common();
                        $email_config = Zend_Registry::get('config');
                        
                        $fromemail = $email_config->email->username;
                        $fromname = $this->ApplicationName;
                        $toemail = $formData ["userEmail"];
                        $toname = ucwords(strtolower(($formData ["userFirstName"] . ' ' . $formData ["userLastName"])));
                        
                        try
                        {
                            $email->send_email($body, $fromname, $fromemail, $toname, $toemail, $subject);
                        }
                        catch ( Exception $e )
                        {
                            $this->view->message .= "\nThere was an error sending an email to '" . $toemail . "'. Please contact your system administrator.";
                        }
                    }
                    
                    // fill users dropdown
                    $allUsers = $userTable->fetchAll($whereUsers, array (
                            'lastname', 
                            'firstname', 
                            'username' 
                    ));
                    $currElement->clearMultiOptions();
                    $currElement->addMultiOption("0", "Add New User");
                    foreach ( $allUsers as $row )
                    {
                        $currElement->addMultiOption($row->user_id, ucwords(strtolower($row->lastname)) . ", " . ucwords(strtolower($row->firstname)) . " (" . strtolower($row->username) . ")");
                    }
                    
                    if ($isvalid == false)
                    {
                        // repop form
                        $form->getElement('select_user')->setValue($formData ['select_user']);
                        $form->getElement('privileges')->setValue($formData ['privileges']);
                        $form->getElement('select_company')->setValue($formData ['select_company']);
                        $form->getElement('username')->setValue($formData ['username']);
                        $form->getElement('userFirstName')->setValue($formData ['userFirstName']);
                        $form->getElement('userLastName')->setValue($formData ['userLastName']);
                        $form->getElement('userPhone')->setValue($formData ['userPhone']);
                        $form->getElement('userEmail')->setValue($formData ['userEmail']);
                        $form->getElement('update_password')->setValue($formData ['update_password']);
                        $form->getElement('password')->setValue($formData ['password']);
                        $form->getElement('passwordConfirm')->setValue($formData ['passwordConfirm']);
                        $form->getElement('auto_password')->setValue($formData ['auto_password']);
                        $form->getElement('password_mode')->setValue($formData ['password_mode']);
                        $form->getElement('must_change')->setValue($formData ['must_change']);
                        $form->getElement('is_activated')->setValue($formData ['is_activated']);
                    }
                    else
                    {
                        // reset form
                        $currElement->setValue('');
                        $form->getElement('select_user')->setValue('');
                        $form->getElement('privileges')->setValue('');
                        if ($type != 'dealer')
                        {
                            $form->getElement('select_company')->setValue('');
                        }
                        $form->getElement('username')->setValue('');
                        $form->getElement('userFirstName')->setValue('');
                        $form->getElement('userLastName')->setValue('');
                        $form->getElement('userPhone')->setValue('');
                        $form->getElement('userEmail')->setValue('');
                    }
                }
                catch ( Zend_Db_Exception $e )
                {
                    $this->view->message = 'A database error has occurred and user "' . $formData ["userFirstName"] . " " . $formData ["userLastName"] . '" could not be saved. If the problem persists, please contact your administrator.';
                }
                catch ( Exception $e )
                {
                    $db->rollback();
                    // CRITICAL UPDATE EXCEPTION
                    throw new exception("A database error has occurred and the user cound not be saved.", 0, $e);
                }
            }
            else
            {
                // if formdata was not valid, repopulate form(error messages
                // from validations are automatically added)
                $form->populate($formData);
            } // end else
        }
        else
        {
            // generate random password
            $pword = new Custom_Common();
            $pword = $pword->create_password();
            $currElement = $form->getElement('auto_password');
            $currElement->setValue($pword);
        } // end if
        

        // Send form to the view script
        $this->view->form = $form;
    }

    public function deleteUser ($selUserID)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        try
        {
            $unknown_devivce_instance = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
            $where = $unknown_devivce_instance->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $unknown_devivce_instance->delete($where);
            
            $report = new Proposalgen_Model_DbTable_Reports();
            $where = $report->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $reports = $report->fetchAll($where);
            
            // Delete all the reports
            foreach ( $reports as $key )
            {
                $this->deleteReport($key ['report_id']);
            }
            
            $devices_pf = new Proposalgen_Model_DbTable_PFDevices();
            $where = $devices_pf->getAdapter()->quoteInto('created_by = ?', $selUserID, 'INTEGER');
            $devices_pf_data = array (
                    'created_by' => null 
            );
            $devices_pf->update($devices_pf_data, $where);
            
            $pf_device_matchup_users = new Proposalgen_Model_DbTable_PFMatchupUsers();
            $where = $pf_device_matchup_users->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $pf_device_matchup_users->delete($where);
            
            $user_toner_override = new Proposalgen_Model_DbTable_UserTonerOverride();
            $where = $user_toner_override->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $user_toner_override->delete($where);
            
            $user_privileges = new Proposalgen_Model_DbTable_UserPrivileges();
            $where = $user_privileges->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $user_privileges->delete($where);
            
            $user_device_override = new Proposalgen_Model_DbTable_UserDeviceOverride();
            $where = $user_device_override->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $user_device_override->delete($where);
            
            $user_sessions = new Proposalgen_Model_DbTable_UserSessions();
            $where = $user_sessions->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $user_sessions->delete($where);
            
            $user = new Proposalgen_Model_DbTable_Users();
            $where = $user->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $user->delete($where);
            
            return true;
        }
        catch ( Exception $e )
        {
            $db->rollback();
            return false;
        }
    }

    /**
     * The userdataAction accepts a userid as a GET parameter, and returns
     * information about the corresponding user in json format.
     * Jquery code
     * requesting data from this action is located in on the main layout page.
     * The data returned is used to populate the form fields in the
     * edituserAction.
     */
    public function userdataAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $type = '';
        
        // Disable the default layout
        $this->_helper->layout->disableLayout();
        
        $userID = $this->_getParam('userid', false);
        
        if (in_array("Dealer Admin", $this->privilege))
        {
            $type = 'dealer';
            $companyTable = new Proposalgen_Model_DbTable_DealerCompany();
            $where = $companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
            $company = $companyTable->fetchRow($where);
            $dealer_company_id = $company ['company_name'];
        }
        else
        {
            $type = 'dealer';
            $companyTable = new Proposalgen_Model_DbTable_DealerCompany();
            $dealer_company_id = "Office Depot";
        }
        
        // clear any existing form validation
        $form = new Proposalgen_Form_User(null, $type);
        $form->reset();
        
        // return user details
        try
        {
            if ($userID > 0)
            {
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        'u' => 'users' 
                ))
                    ->join(array (
                        'up' => 'user_privileges' 
                ), 'u.user_id = up.user_id', array (
                        'up.priv_id' 
                ))
                    ->join(array (
                        'p' => 'privileges' 
                ), 'up.priv_id = p.priv_id', array (
                        'p.priv_type' 
                ))
                    ->join(array (
                        'dc' => 'dealer_company' 
                ), 'dc.dealer_company_id = u.dealer_company_id', array (
                        'dc.company_name' 
                ))
                    ->where('u.user_id = ?', $userID);
                $row = $db->fetchRow($select);
                
                if ($type != 'dealer')
                {
                    $dealer_company_id = $row ['dealer_company_id'];
                }
                
                $formdata = array (
                        'dealer_company_id' => $dealer_company_id, 
                        'username' => $row ['username'], 
                        'firstname' => $row ['firstname'], 
                        'lastname' => $row ['lastname'], 
                        'phone' => $row ['telephone'], 
                        'email' => $row ['email'], 
                        'password' => null, 
                        'is_activated' => $row ['is_activated'], 
                        'priv_id' => $row ['priv_id'], 
                        'priv_type' => $row ['priv_type'] 
                );
            }
            else
            {
                if ($type != 'dealer')
                {
                    $dealer_company_id = 0;
                }
                
                $formdata = array (
                        'dealer_company_id' => $dealer_company_id, 
                        'username' => '', 
                        'firstname' => '', 
                        'lastname' => '', 
                        'phone' => '', 
                        'email' => '', 
                        'password' => null, 
                        'is_activated' => false, 
                        'priv_id' => 0, 
                        'priv_type' => '' 
                );
            }
        }
        catch ( Exception $e )
        {
            // CRITICAL EXCEPTION
            Throw new exception("Critical Error:Unable to find user.", 0, $e);
        } // end catch
        

        // Encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * The printermodelsAction returns a list of printer_models by manufacturer
     * to populate the dropdowns in json format
     */
    public function printermodelsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $manufacturer_id = $_GET ['manufacturerid'];
        $master_devicesTable = new Proposalgen_Model_DbTable_MasterDevice();
        $where = $master_devicesTable->getAdapter()->quoteInto('mastdevice_manufacturer = ?', $manufacturer_id, 'INTEGER');
        $result = $master_devicesTable->fetchAll($where, 'printer_model');
        
        $i = 0;
        $responce = null;
        if (count($result) > 0)
        {
            foreach ( $result as $row )
            {
                $responce->rows [$i] ['id'] = $row ['master_device_id'];
                $responce->rows [$i] ['cell'] = array (
                        $row ['master_device_id'], 
                        ucwords(strtolower($row ['printer_model'])) 
                );
                $i ++;
            }
        }
        else
        {
            $responce->rows [$i] ['id'] = 0;
            $responce->rows [$i] ['cell'] = array (
                    0, 
                    '' 
            );
        }
        echo json_encode($responce);
    }

    /**
     * The devicedetailsAction accepts a parameter for the deviceid and gets the
     * device
     * details from the database.
     * Returns the details array in a json encoded format.
     */
    public function devicedetailsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);
        
        try
        {
            if ($deviceID > 0)
            {
                // get toners for device
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        't' => 'toner' 
                ))
                    ->join(array (
                        'td' => 'device_toner' 
                ), 't.toner_id = td.toner_id')
                    ->where('td.master_device_id = ?', $deviceID);
                $stmt = $db->query($select);
                $result = $stmt->fetchAll();
                
                $toner_array = '';
                foreach ( $result as $key )
                {
                    if (! empty($toner_array))
                    {
                        $toner_array .= ",";
                    }
                    $toner_array .= "'" . $key ['toner_id'] . "'";
                }
                
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        'md' => 'master_device' 
                ))
                    ->joinLeft(array (
                        'm' => 'manufacturer' 
                ), 'm.manufacturer_id = md.mastdevice_manufacturer')
                    ->joinLeft(array (
                        'rd' => 'replacement_devices' 
                ), 'rd.master_device_id = md.master_device_id')
                    ->where('md.master_device_id = ?', $deviceID);
                $stmt = $db->query($select);
                $row = $stmt->fetchAll();
                
                $launch_date = new Zend_Date($row [0] ['launch_date'], "yyyy/mm/dd HH:ii:ss");
                $formdata = array (
                        'launch_date' => $launch_date->toString('mm/dd/yyyy'), 
                        'toner_config_id' => $row [0] ['toner_config_id'], 
                        'is_copier' => $row [0] ['is_copier'] ? true : false, 
                        'is_scanner' => $row [0] ['is_scanner'] ? true : false, 
                        'is_fax' => $row [0] ['is_fax'] ? true : false, 
                        'is_duplex' => $row [0] ['is_duplex'] ? true : false, 
                        'is_replacement_device' => $row [0] ['is_replacement_device'], 
                        'watts_power_normal' => $row [0] ['watts_power_normal'], 
                        'watts_power_idle' => $row [0] ['watts_power_idle'], 
                        'device_price' => ($row [0] ['device_price'] > 0 ? money_format('%i', ($row [0] ['device_price'])) : ""), 
                        'is_deleted' => $row [0] ['is_deleted'], 
                        'toner_array' => $toner_array, 
                        'replacement_category' => $row [0] ['replacement_category'], 
                        // 'is_letter_legal' => $row [0] ['is_letter_legal'],
                        'print_speed' => $row [0] ['print_speed'], 
                        'resolution' => $row [0] ['resolution'], 
                        // 'paper_capacity' => $row [0] ['paper_capacity'],
                        // 'cpp_above' => $row [0]
                        // ['CPP_above_ten_thousand_pages'],
                        'monthly_rate' => $row [0] ['monthly_rate'], 
                        'is_leased' => $row [0] ['is_leased'] ? true : false, 
                        'leased_toner_yield' => $row [0] ['leased_toner_yield'] 
                );
            }
            else
            {
                // empty form values
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device.", 0, $e);
        } // end catch
        

        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function devicereportsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $master_device_id = $this->_getParam('id', 0);
        
        $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
        $where = $device_instanceTable->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
        $device_instances = $device_instanceTable->fetchAll($where);
        
        try
        {
            $formdata = array (
                    'report_count' => count($device_instances) 
            );
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to get report count.", 0, $e);
        } // end catch
        

        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function filterlistitemsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $list = $this->_getParam('list', 'man');
        
        try
        {
            switch ($list)
            {
                case "man" :
                    $select = new Zend_Db_Select($db);
                    $select = $db->select();
                    $select->from(array (
                            'm' => 'manufacturer' 
                    ));
                    $select->where('is_deleted = 0');
                    $select->order('manufacturer_name');
                    $stmt = $db->query($select);
                    $result = $stmt->fetchAll();
                    $count = count($result);
                    
                    if ($count > 0)
                    {
                        $i = 0;
                        foreach ( $result as $row )
                        {
                            $formdata->rows [$i] ['id'] = $row ['manufacturer_id'];
                            $formdata->rows [$i] ['cell'] = array (
                                    $row ['manufacturer_id'], 
                                    ucwords(strtolower($row ['manufacturer_name'])) 
                            );
                            $i ++;
                        }
                    }
                    else
                    {
                        // empty form values
                        $formdata = array ();
                    }
                    break;
                
                case "color" :
                    $select = new Zend_Db_Select($db);
                    $select = $db->select();
                    $select->from(array (
                            'tc' => 'toner_color' 
                    ));
                    $select->order('toner_color_name');
                    $stmt = $db->query($select);
                    $result = $stmt->fetchAll();
                    $count = count($result);
                    
                    if ($count > 0)
                    {
                        $i = 0;
                        foreach ( $result as $row )
                        {
                            $formdata->rows [$i] ['id'] = $row ['toner_color_id'];
                            $formdata->rows [$i] ['cell'] = array (
                                    $row ['toner_color_id'], 
                                    ucwords(strtolower($row ['toner_color_name'])) 
                            );
                            $i ++;
                        }
                    }
                    else
                    {
                        // empty form values
                        $formdata = array ();
                    }
                    break;
                
                case "type" :
                    $select = new Zend_Db_Select($db);
                    $select = $db->select();
                    $select->from(array (
                            'pt' => 'part_type' 
                    ));
                    $select->order('type_name');
                    $stmt = $db->query($select);
                    $result = $stmt->fetchAll();
                    $count = count($result);
                    
                    if ($count > 0)
                    {
                        $i = 0;
                        foreach ( $result as $row )
                        {
                            $formdata->rows [$i] ['id'] = $row ['part_type_id'];
                            $formdata->rows [$i] ['cell'] = array (
                                    $row ['part_type_id'], 
                                    $row ['type_name'] 
                            );
                            $i ++;
                        }
                    }
                    else
                    {
                        // empty form values
                        $formdata = array ();
                    }
                    break;
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to build criteria list.", 0, $e);
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * The devicetonersAction accepts a parameter for the deviceid and gets the
     * device
     * toners from the database.
     * Returns the parts array in a json encoded format.
     */
    public function devicetonersAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);
        $toner_array = $this->_getParam('list', false);
        
        $formdata = null;
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 1;
        
        try
        {
            $where = '';
            if ($toner_array != '')
            {
                $fieldList = array (
                        'fullname', 
                        '(null) AS master_device_id' 
                );
                $where = 't.id IN(' . $toner_array . ')';
            }
            else
            {
                $fieldList = array (
                        'fullname' 
                );
                $where = 'dt.master_device_id = ' . $deviceID;
            }
            
            $select = new Zend_Db_Select($db);
            $select = $db->select();
            $select->from(array (
                    't' => 'pgen_toners' 
            ));
            if ($toner_array == '')
            {
                $select->joinLeft(array (
                        'dt' => 'pgen_device_toners' 
                ), 't.id = dt.toner_id');
            }
            $select->joinLeft(array (
                    'pt' => 'pgen_part_types' 
            ), 'pt.id = t.part_type_id');
            $select->joinLeft(array (
                    'tc' => 'pgen_toner_colors' 
            ), 'tc.id = t.toner_color_id');
            $select->joinLeft(array (
                    'm' => 'manufacturers' 
            ), 'm.id = t.manufacturer_id', $fieldList);
            $select->where($where);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            $count = count($result);
            
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            
            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit;
            
            if ($count > 0)
            {
                $i = 0;
                $type_name = '';
                $formdata->page = $page;
                $formdata->total = $total_pages;
                $formdata->records = $count;
                foreach ( $result as $row )
                {
                    // Always uppercase OEM, but just captialize everything else
                    $type_name = ucwords(strtolower($row ['type_name']));
                    if ($type_name == "Oem")
                    {
                        $type_name = "OEM";
                    }
                    
                    $formdata->rows [$i] ['id'] = $row ['toner_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['toner_id'], 
                            $row ['toner_SKU'], 
                            ucwords(strtolower($row ['manufacturer_name'])), 
                            $type_name, 
                            ucwords(strtolower($row ['toner_color_name'])), 
                            $row ['toner_yield'], 
                            $row ['toner_price'], 
                            $row ['master_device_id'], 
                            $row ['master_device_id'], 
                            null 
                    );
                    $i ++;
                }
            }
            else
            {
                // empty form values
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device parts.", 0, $e);
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function replacementtonersAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $toner_id = $this->_getParam('tonerid', 0);
        $filter = $this->_getParam('filter', false);
        $criteria = trim($this->_getParam('criteria', false));
        
        $formdata = null;
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 'manufacturer_name';
        
        $where = '';
        if (! empty($filter) && ! empty($criteria) && $filter != 'machine_compatibility')
        {
            if ($filter == 'toner_yield')
            {
                $where = ' AND ' . $filter . ' = ' . $criteria;
            }
            else
            {
                if ($filter == "manufacturer_name")
                {
                    $filter = "m.manufacturer_name";
                }
                $where = ' AND ' . $filter . ' LIKE("%' . $criteria . '%")';
            }
        }
        
        try
        {
            // GET TONER
            $toner = Proposalgen_Model_Mapper_Toner::getInstance()->find($toner_id);
            $toner_color_id = $toner->getTonerColorId();
            
            // GET NUMBER OF DEVICES USING THIS TONER
            $total_devices = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchAll('toner_id = ' . $toner_id);
            $total_devices_count = count($total_devices);
            
            // GET NUMBER OF DEVICES WHERE LAST TONER FOR THIS COLOR
            $num_devices_count = 0;
            foreach ( $total_devices as $key )
            {
                $master_device_id = $key->getMasterDeviceId();
                
                // GET ALL SAME COLOR TONERS FOR DEVICE
                $select = new Zend_Db_Select($db);
                $select = $db->select();
                $select->from(array (
                        'dt' => 'device_toner' 
                ));
                $select->joinLeft(array (
                        't' => 'toner' 
                ), 'dt.toner_id = t.toner_id');
                $select->where('t.toner_color_id = ' . $toner_color_id . ' AND dt.master_device_id = ' . $master_device_id);
                $stmt = $db->query($select);
                $num_devices = $stmt->fetchAll();
                
                if (count($num_devices) == 1)
                {
                    $num_devices_count += 1;
                }
            }
            
            // GET SAME COLOR TONERS
            $select = new Zend_Db_Select($db);
            $select = $db->select();
            $select->from(array (
                    't' => 'toner' 
            ));
            $select->joinLeft(array (
                    'pt' => 'part_type' 
            ), 'pt.part_type_id = t.part_type_id');
            $select->joinLeft(array (
                    'tc' => 'toner_color' 
            ), 'tc.toner_color_id = t.toner_color_id');
            $select->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = t.manufacturer_id');
            $select->where('t.toner_id != ' . $toner_id . ' AND t.toner_color_id = ' . $toner_color_id . $where);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            $count = count($result);
            
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            
            if ($page > $total_pages)
                $page = $total_pages;
            
            $start = $limit * $page - $limit;
            
            $select = new Zend_Db_Select($db);
            $select = $db->select();
            $select->from(array (
                    't' => 'toner' 
            ));
            $select->joinLeft(array (
                    'pt' => 'part_type' 
            ), 'pt.part_type_id = t.part_type_id');
            $select->joinLeft(array (
                    'tc' => 'toner_color' 
            ), 'tc.toner_color_id = t.toner_color_id');
            $select->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = t.manufacturer_id');
            $select->where('t.toner_id != ' . $toner_id . ' AND t.toner_color_id = ' . $toner_color_id . $where);
            $select->order($sidx . ' ' . $sord);
            $select->limit($limit, $start);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            if ($count > 0)
            {
                $i = 0;
                $type_name = '';
                $formdata->page = $page;
                $formdata->total = $total_pages;
                $formdata->records = $count;
                foreach ( $result as $row )
                {
                    // Always uppercase OEM, but just captialize everything else
                    $type_name = ucwords(strtolower($row ['type_name']));
                    if ($type_name == "Oem")
                    {
                        $type_name = "OEM";
                    }
                    
                    $formdata->rows [$i] ['id'] = $row ['toner_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['toner_id'], 
                            $row ['toner_SKU'], 
                            ucwords(strtolower($row ['manufacturer_name'])), 
                            $type_name, 
                            ucwords(strtolower($row ['toner_color_name'])), 
                            $row ['toner_yield'], 
                            $row ['toner_price'], 
                            $num_devices_count, 
                            $total_devices_count 
                    );
                    $i ++;
                }
            }
            else
            {
                // empty form values
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device parts.", 0, $e);
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function devicetonercountAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $toner_id = $this->_getParam('tonerid', 0);
        
        $formdata = null;
        try
        {
            // GET TONER
            $toner = Proposalgen_Model_Mapper_Toner::getInstance()->find($toner_id);
            $toner_color_id = $toner->getTonerColorId();
            
            // GET NUMBER OF DEVICES USING THIS TONER
            $total_devices = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchAll('toner_id = ' . $toner_id);
            $total_devices_count = count($total_devices);
            
            // GET NUMBER OF DEVICES WHERE LAST TONER FOR THIS COLOR
            $num_devices_count = 0;
            foreach ( $total_devices as $key )
            {
                $master_device_id = $key->getMasterDeviceId();
                
                // GET ALL SAME COLOR TONERS FOR DEVICE
                $select = new Zend_Db_Select($db);
                $select = $db->select();
                $select->from(array (
                        'dt' => 'device_toner' 
                ));
                $select->joinLeft(array (
                        't' => 'toner' 
                ), 'dt.toner_id = t.toner_id');
                $select->where('t.toner_color_id = ' . $toner_color_id . ' AND dt.master_device_id = ' . $master_device_id);
                $stmt = $db->query($select);
                $num_devices = $stmt->fetchAll();
                
                if (count($num_devices) == 1)
                {
                    $num_devices_count += 1;
                }
            }
            
            $formdata = array (
                    'total_count' => $total_devices_count, 
                    'device_count' => $num_devices_count 
            );
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device count.", 0, $e);
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function addtonerAction ()
    {
        // Disable the default layout
        $this->_helper->layout->disableLayout();
        
        // grab all variables from $_POST
        $toner_id = $this->_getParam('toner_id', false);
        $toner_sku = $this->_getParam('toner_sku', false);
        $part_type_id = $this->_getParam('part_type_id', false);
        $manufacturer_id = $this->_getParam('manufacturer_id', false);
        $toner_color_id = $this->_getParam('toner_color_id', false);
        $toner_yield = $this->_getParam('toner_yield', false);
        $toner_price = $this->_getParam('toner_price', false);
        $master_device_id = $this->_getParam('master_device_id', false);
        
        // echo "SKU=".$toner_sku."<br />Type=".$part_type_id."<br
        // />Man=".$manufacturer_id."<br />Color=".$toner_color_id."<br
        // />Yield=".$toner_yield."<br />Price=".$toner_price."<br />"; die;
        

        // validate
        $message = '';
        if ($toner_id == 0 && (empty($toner_sku) || empty($part_type_id) || empty($manufacturer_id) || empty($toner_color_id) || empty($toner_yield) || empty($toner_price)))
        {
            $message = "You must complete all fields before adding a new part. Please try again.";
        }
        
        if (empty($message))
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();
            
            $db->beginTransaction();
            try
            {
                if ($toner_id > 0)
                {
                    $device_tonerData = array (
                            'toner_id' => $toner_id, 
                            'master_device_id' => $master_device_id 
                    );
                    // make sure device_toner does not exist
                    $where = $device_tonerTable->getAdapter()->quoteInto('toner_id = ' . $toner_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                    $result = $device_tonerTable->fetchRow($where);
                    
                    if (count($result) == 0)
                    {
                        $device_tonerTable->insert($device_tonerData);
                        $message = "The toner has been added.";
                    }
                    else
                    {
                        $message = "This toner is already a part for the device.";
                    }
                }
                else
                {
                    $tonerTable = new Proposalgen_Model_DbTable_Toner();
                    $tonerData = array (
                            'toner_sku' => $toner_sku, 
                            'part_type_id' => $part_type_id, 
                            'manufacturer_id' => $manufacturer_id, 
                            'toner_color_id' => $toner_color_id, 
                            'toner_yield' => $toner_yield, 
                            'toner_price' => $toner_price 
                    );
                    
                    // make sure toner does not exist
                    $where = $tonerTable->getAdapter()->quoteInto('(toner_SKU = "' . $toner_sku . '") OR (manufacturer_id = ' . $manufacturer_id . ' AND toner_color_id = ' . $toner_color_id . ' AND toner_yield = ' . $toner_yield . ')', null);
                    $toners = $tonerTable->fetchRow($where);
                    
                    if (count($toners) > 0)
                    {
                        $toner_id = $toners ['toner_id'];
                    }
                    else
                    {
                        $toner_id = $tonerTable->insert($tonerData);
                    }
                    
                    // update device_toner
                    $device_tonerData = array (
                            'toner_id' => $toner_id, 
                            'master_device_id' => $master_device_id 
                    );
                    $device_tonerTable->insert($device_tonerData);
                    $message = "The toner has been added.";
                }
                
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $message = "An error has occurred and the toner was not saved.";
            }
        }
        
        // encode user data to return to the client:
        $this->view->data = $message;
    }

    /**
     */
    public function edittonerAction ()
    {
        // Disable the default layout
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->disableLayout();
        $tonerTable = new Proposalgen_Model_DbTable_Toner();
        $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();
        
        // grab all variables from $_POST
        $id = $this->_getParam('id', null);
        $toner_id = $this->_getParam('toner_id', null);
        $toner_sku = $this->_getParam('toner_sku', null);
        $part_type_id = $this->_getParam('part_type_id', null);
        $manufacturer_id = $this->_getParam('manufacturer_id', null);
        $manufacturer_name = $this->_getParam('manufacturer_name', null);
        $toner_color_id = $this->_getParam('toner_color_id', null);
        $toner_yield = $this->_getParam('toner_yield', null);
        $toner_price = $this->_getParam('toner_price', null);
        $master_device_id = $this->_getParam('deviceid', null);
        $oper = $this->_getParam('oper', null);
        
        // used for cell editing
        $field = '';
        $value = '';
        $message = '';
        
        /*
         * if($id > 0) { if($toner_sku) { $field = 'toner_sku'; $value = $toner_sku; if(empty($value)) { $message = "The
         * SKU is not valid. Please try again."; } } else if($part_type_id) { $field = 'part_type_id'; $value =
         * $part_type_id; } else if($manufacturer_name) { $field = 'manufacturer_name'; $value = $manufacturer_name; }
         * else if($toner_color_id) { $field = 'toner_color_id'; $value = $toner_color_id; } else if($toner_yield) {
         * $field = 'toner_yield'; $value = $toner_yield; if(!is_numeric($value)) { $message = "The Yield is not valid.
         * Please try again."; } } else if($toner_price) { $field = 'toner_price'; $value =
         * str_replace("$","",$toner_price); if(!is_numeric($value)) { $message = "The price is not valid. Please try
         * again."; } }
         */
        
        if ($oper == "del")
        {
            // check to see if toner is being used
            $where = $device_tonerTable->getAdapter()->quoteinto("toner_id = ?", $id, "INTEGER");
            $devices = $device_tonerTable->fetchAll($where);
            
            if (count($devices) > 0)
            {
                $message = "We are unable to delete toner as it's already assigned to a printer.";
            }
            else
            {
                $where = $tonerTable->getAdapter()->quoteInto("toner_id = ?", $id, "INTEGER");
                $tonerTable->delete($where);
                $message = "The toner has been deleted.";
            }
        }
        else
        {
            if ((empty($toner_sku) || empty($part_type_id) || empty($manufacturer_id) || empty($toner_color_id) || empty($toner_yield) || empty($toner_price)))
            {
                $message = "All fields must have a valid value before saving. Please try again.";
            }
            else if (! is_numeric($toner_yield))
            {
                $message = "Toner Yield is not a valid number. Please try again.";
            }
            else if (! is_numeric($toner_price))
            {
                $message = "Toner Price is not a valid number. Please try again.";
            }
            else if (! ($toner_price > 0))
            {
                $message = "Toner Price must be greater than 0. Please try again.";
            }
            
            if (empty($message))
            {
                $db->beginTransaction();
                try
                {
                    $tonerTable = new Proposalgen_Model_DbTable_Toner();
                    $tonerData = array (
                            'toner_sku' => $toner_sku, 
                            'part_type_id' => $part_type_id, 
                            'manufacturer_id' => $manufacturer_id, 
                            'toner_color_id' => $toner_color_id, 
                            'toner_yield' => $toner_yield, 
                            'toner_price' => $toner_price 
                    );
                    
                    if ($toner_id > 0)
                    {
                        $where = $tonerTable->getAdapter()->quoteInto('toner_id = ?', $toner_id, 'INTEGER');
                        $toner_id = $tonerTable->update($tonerData, $where);
                        $message = "The toner has been updated.";
                    }
                    else
                    {
                        // make sure toner does not exist
                        $where = $tonerTable->getAdapter()->quoteInto('(toner_SKU = "' . $toner_sku . '")', null);
                        $toners = $tonerTable->fetchRow($where);
                        
                        if (count($toners) > 0)
                        {
                            $message = "The toner already exists.";
                        }
                        else
                        {
                            $toner_id = $tonerTable->insert($tonerData);
                            
                            $message = "The toner has been added.";
                        }
                    }
                    $db->commit();
                }
                catch ( Exception $e )
                {
                    $db->rollback();
                    $message = "An error has occurred and the toner was not updated.<br />";
                    // *
                    $message .= "Toner ID: " . $toner_id . "<br />";
                    $message .= "Toner SKU: " . $toner_sku . "<br />";
                    $message .= "Part Type: " . $part_type_id . "<br />";
                    $message .= "Manufacturer ID: " . $manufacturer_id . "<br />";
                    $message .= "Color ID: " . $toner_color_id . "<br />";
                    $message .= "Toner Yield: " . $toner_yield . "<br />";
                    $message .= "Toner Price: " . $toner_price . "<br />";
                    $message .= "Master Device ID: " . $master_device_id . "<br />";
                    // */
                }
            }
        }
        
        // encode user data to return to the client:
        $this->view->data = $message;
    }

    public function replacetonerAction ()
    {
        $message = '';
        $toner_count = 0;
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->disableLayout();
        
        $replace_mode = $this->_getParam('replace_mode', '');
        $replace_id = $this->_getParam('replace_toner_id', 0);
        $with_id = $this->_getParam('with_toner_id', 0);
        $apply_all = $this->_getParam('chkAllToners', 0);
        
        /*
         * / DEBUG echo "replace_mode=" . $replace_mode . "<br />"; echo "replace_id=" . $replace_id . "<br />"; echo
         * "with_id=" . $with_id . "<br />"; echo "apply_all=" . $apply_all . "<br />"; die; //
         */
        
        // GET TONER
        $toner = Proposalgen_Model_Mapper_Toner::getInstance()->find($replace_id);
        $toner_color_id = $toner->getTonerColorId();
        
        // GET ALL DEVICES USING THIS TONER
        $total_devices = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchAll('toner_id = ' . $replace_id);
        
        $db->beginTransaction();
        try
        {
            $message = "The toner has been deleted successfully.";
            
            if ($replace_mode == 'optional_replace' && $with_id > 0)
            {
                // LOOP THROUGH ALL DEVICES AND UPDATE TO REPLACEMENT TONER ID
                // ($with_id)
                foreach ( $total_devices as $key )
                {
                    $master_device_id = $key->getMasterDeviceId();
                    
                    // UPDATE ALL DEVICES WITH THIS TONER (replace_id) TO
                    // REPLACEMENT TONER (with_id)
                    $device_tonerMapper = Proposalgen_Model_Mapper_DeviceToner::getInstance();
                    $device_toner = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchRow('toner_id = ' . $replace_id . ' AND master_device_id = ' . $master_device_id);
                    $device_toner->setTonerId($with_id);
                    $device_tonerMapper->save($device_toner);
                    $toner_count += 1;
                }
                $message = "The toner has been replaced and deleted successfully.";
            }
            else if ($replace_mode == 'require_replace')
            {
                if ($with_id > 0)
                {
                    // LOOP THROUGH ALL DEVICES AND UPDATE TO REPLACEMENT TONER
                    // ID ($with_id)
                    foreach ( $total_devices as $key )
                    {
                        $master_device_id = $key->getMasterDeviceId();
                        
                        if ($apply_all == 1)
                        {
                            // UPDATE ALL DEVICES WITH THIS TONER (replace_id)
                            // TO REPLACEMENT TONER (with_id)
                            $device_tonerMapper = Proposalgen_Model_Mapper_DeviceToner::getInstance();
                            $device_toner = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchRow('toner_id = ' . $replace_id . ' AND master_device_id = ' . $master_device_id);
                            $device_toner->setTonerId($with_id);
                            $device_tonerMapper->save($device_toner);
                            $toner_count += 1;
                        }
                        else
                        {
                            // UPDATE ONLY DEVICES WHERE THIS IS THE LAST OF
                            // IT'S COLOR ($toner_color_id)
                            $select = new Zend_Db_Select($db);
                            $select = $db->select();
                            $select->from(array (
                                    'dt' => 'device_toner' 
                            ));
                            $select->joinLeft(array (
                                    't' => 'toner' 
                            ), 'dt.toner_id = t.toner_id');
                            $select->where('t.toner_color_id = ' . $toner_color_id . ' AND dt.master_device_id = ' . $master_device_id);
                            $stmt = $db->query($select);
                            $num_devices = $stmt->fetchAll();
                            
                            if (count($num_devices) == 1)
                            {
                                // UPDATE THIS DEVICE WITH REPLCEMENT TONER
                                // (with_id)
                                $device_tonerMapper = Proposalgen_Model_Mapper_DeviceToner::getInstance();
                                $device_toner = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchRow('toner_id = ' . $replace_id . ' AND master_device_id = ' . $master_device_id);
                                $device_toner->setTonerId($with_id);
                                $device_tonerMapper->save($device_toner);
                                $toner_count += 1;
                            }
                        }
                    }
                    $message = "The toner has been replaced and deleted successfully.";
                }
                else
                {
                    $db->rollback();
                    $message = "You must select a replacement toner from the list.";
                }
            }
            
            // *****************************************************************
            // ALL MODES END UP DELETING TONER
            // *****************************************************************
            

            // REMOVE DEALER TONER OVERRIDES
            $dealer_toner_OverrideTable = new Proposalgen_Model_DbTable_DealerTonerOverride();
            $where = $dealer_toner_OverrideTable->getAdapter()->quoteInto('toner_id = ?', $replace_id, 'INTEGER');
            $dealer_toner_OverrideTable->delete($where);
            
            // REMOVE USER TONER OVERRIDES
            $user_toner_OverrideTable = new Proposalgen_Model_DbTable_UserTonerOverride();
            $where = $user_toner_OverrideTable->getAdapter()->quoteInto('toner_id = ?', $replace_id, 'INTEGER');
            $user_toner_OverrideTable->delete($where);
            
            // REMOVE DEVICE TONER MAPPINGS
            $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();
            $where = $device_tonerTable->getAdapter()->quoteInto('toner_id = ?', $replace_id, 'INTEGER');
            $device_tonerTable->delete($where);
            
            // REMOVE TONER
            $tonerTable = new Proposalgen_Model_DbTable_Toner();
            $where = $tonerTable->getAdapter()->quoteInto('toner_id = ?', $replace_id, 'INTEGER');
            $tonerTable->delete($where);
            
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollBack();
            $message = "An error has occurred and the toner was not replaced.";
        }
        
        // RETURN MESSAGE
        $this->view->data = $message;
    }

    public function removetonerAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $message = array ();
        
        $toner_id = $this->_getParam('toner_id', false);
        $master_device_id = $this->_getParam('master_device_id', false);
        
        $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();
        
        $db->beginTransaction();
        try
        {
            if ($toner_id > 0 && $master_device_id > 0)
            {
                $where = $device_tonerTable->getAdapter()->quoteInto('master_device_id = ' . $master_device_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                $device_tonerTable->delete($where);
            }
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
            $message [] = "An error has occurred and the toner was not removed.";
        }
        
        // encode user data to return to the client:
        $this->view->data = $message;
    }

    public function searchtonersAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $field = $this->_getParam('search_field', false);
        $value = $this->_getParam('search_value', false);
        
        // build where
        $where = "";
        if (! empty($field))
        {
            $where .= $field . ' = "' . $value . '"';
        }
        
        try
        {
            // select toners for device
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    't' => 'toner' 
            ))
                ->join(array (
                    'pt' => 'part_type' 
            ), 'pt.part_type_id = t.part_type_id')
                ->join(array (
                    'tc' => 'toner_color' 
            ), 'tc.toner_color_id = t.toner_color_id')
                ->join(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = t.manufacturer_id');
            if (! empty($where))
            {
                $select->where($where);
            }
            $select->order(array (
                    'm.manufacturer_name', 
                    't.toner_color_id', 
                    'toner_yield' 
            ));
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            if (count($result) > 0)
            {
                $i = 0;
                foreach ( $result as $row )
                {
                    $formdata->rows [$i] ['id'] = $row ['toner_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['toner_id'], 
                            $row ['toner_SKU'], 
                            $row ['type_name'], 
                            $row ['manufacturer_name'], 
                            $row ['toner_color_name'], 
                            $row ['toner_yield'], 
                            "$" . money_format('%i', ($row ['toner_price'])) 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find toners.", 0, $e);
        } // end catch
        

        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * The manufacturersAction provides a list of manufacturers for the
     * system admin to choose from and manage.
     * The details form gets posted back
     * and updated or inserted if new
     */
    public function manufacturersAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = "Manufacturers";
        
        // add manufacturers form
        $form = new Proposalgen_Form_Manufacturers(null, "edit");
        
        // fill manufacturers dropdown
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers = $manufacturersTable->fetchAll('is_deleted = 0', 'manufacturer_name');
        
        // add "New Manufacturer" option
        $currElement = $form->getElement('select_manufacturer');
        $currElement->addMultiOption('0', 'Add New Manufacturer');
        foreach ( $manufacturers as $row )
        {
            $currElement->addMultiOption($row ['manufacturer_id'], ucwords(strtolower($row ['manufacturer_name'])));
        }
        
        $form->getElement('delete_manufacturer')->setAttrib('disabled', 'disabled');
        
        // check if this page has been posted to
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            // print_r($formData); die;
            

            if (isset($formData ['form_mode']) && $formData ['form_mode'] == 'manufacturer')
            {
                $form->getElement('form_mode')->setValue('manufacturer');
                $form->getElement('back_button')->setAttrib('onClick', 'javascript: document.location.href="../managedevices/managedevices";');
            }
            
            if (isset($formData ["ticket_id"]) && $formData ['ticket_id'] != "-1" && ! isset($formData ['form_mode']))
            {
                $hdnID = $formData ['hdnID'];
                $hdnItem = $formData ['hdnItem'];
                $ticket_id = $formData ['ticket_id'];
                $devices_pf_id = $formData ['devices_pf_id'];
                
                if ($ticket_id > 0)
                {
                    $this->view->action = "ticket";
                    $form->getElement('form_mode')->setValue("ticket");
                    $form->getElement('ticket_id')->setValue($ticket_id);
                }
                else
                {
                    $this->view->action = "mapping";
                    $form->getElement('form_mode')->setValue("mapping");
                    $form->getElement('hdnID')->setValue($hdnID);
                    $form->getElement('hdnItem')->setValue($hdnItem);
                }
                $form->getElement('devices_pf_id')->setValue($devices_pf_id);
                
                $form->removeElement('select_manufacturer');
                $form->removeElement('manufacturer_name');
                $form->removeElement('save_manufacturer');
                $form->removeElement('delete_manufacturer');
                $form->removeElement('back_button');
                
                $this->view->message = "<h3 style='margin: 20px 0px 0px 0px; border-bottom: 0px;'>Adding Manufacturer... please wait.</h3>";
                
                $db->beginTransaction();
                try
                {
                    if ($formData ['options'] == "new")
                    {
                        $manufacturer_name = ucwords(strtolower($formData ["manufacturer_name"]));
                        $manufacturerData = array (
                                'manufacturer_name' => $manufacturer_name, 
                                'is_deleted' => 0 
                        );
                        $where = $manufacturersTable->getAdapter()->quoteInto('manufacturer_name = ?', $manufacturer_name);
                        $manufacturer = $manufacturersTable->fetchAll($where);
                        
                        if (count($manufacturer) == 0)
                        {
                            $manufacturersTable->insert($manufacturerData);
                        }
                    }
                    $db->commit();
                }
                catch ( Exception $e )
                {
                    $db->rollback();
                }
            }
            else if (isset($formData ['manufacturer_name']) && $form->isValid($formData))
            {
                $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
                $manufacturer_id = $formData ['select_manufacturer'];
                $manufacturer_name = strtoupper($formData ['manufacturer_name']);
                
                $db->beginTransaction();
                try
                {
                    if (array_key_exists('save_manufacturer', $formData) && $formData ['save_manufacturer'] == "Save")
                    {
                        $manufacturerData = array (
                                'manufacturer_name' => $manufacturer_name 
                        );
                        
                        if ($manufacturer_id > 0)
                        {
                            $where = $manufacturersTable->getAdapter()->quoteInto('manufacturer_id = ?', $manufacturer_id, 'INTEGER');
                            $manufacturersTable->update($manufacturerData, $where);
                            $this->view->message = 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" Updated';
                        }
                        else
                        {
                            $where = $manufacturersTable->getAdapter()->quoteInto('manufacturer_name = ?', $manufacturer_name);
                            $manufacturer = $manufacturersTable->fetchRow($where);
                            
                            if (count($manufacturer) > 0)
                            {
                                if ($manufacturer ['is_deleted'] == 1)
                                {
                                    $manufacturerData = array (
                                            'is_deleted' => 0 
                                    );
                                    $where = $manufacturersTable->getAdapter()->quoteInto('manufacturer_id = ?', $manufacturer ['manufacturer_id'], 'INTEGER');
                                    $manufacturersTable->update($manufacturerData, $where);
                                    $this->view->message = 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" Added.';
                                }
                                else
                                {
                                    $this->view->message = 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" already exists.';
                                }
                            }
                            else
                            {
                                $manufacturersTable->insert($manufacturerData);
                                $this->view->message = 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" Added.';
                            }
                        }
                    }
                    else if (array_key_exists('delete_manufacturer', $formData) && $formData ['delete_manufacturer'] == "Delete")
                    {
                        if ($manufacturer_id > 0)
                        {
                            $do_full_delete = false;
                            
                            // check to see if any devices are using the
                            // manufacturer
                            $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();
                            $where = $master_deviceTable->getAdapter()->quoteInto('mastdevice_manufacturer = ?', $manufacturer_id, 'INTEGER');
                            $master_device = $master_deviceTable->fetchAll($where);
                            
                            if (count($master_device) == 0)
                            {
                                $tonerTable = new Proposalgen_Model_DbTable_Toner();
                                $where = $tonerTable->getAdapter()->quoteInto('manufacturer_id = ?', $manufacturer_id, 'INTEGER');
                                $toner = $tonerTable->fetchAll($where);
                                if (count($toner) == 0)
                                {
                                    $do_full_delete = true;
                                }
                            }
                            
                            $where = $manufacturersTable->getAdapter()->quoteInto('manufacturer_id = ?', $manufacturer_id, 'INTEGER');
                            if ($do_full_delete)
                            {
                                $manufacturersTable->delete($where);
                            }
                            else
                            {
                                $manufacturerData = array (
                                        'is_deleted' => 1 
                                );
                                $manufacturersTable->update($manufacturerData, $where);
                            }
                            $this->view->message = 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" Deleted.';
                        }
                        else
                        {
                            $this->view->message = "No manufacturer was selected to be deleted.";
                        }
                    }
                    $db->commit();
                    
                    // fill manufacturers dropdown
                    $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
                    $manufacturers = $manufacturersTable->fetchAll('is_deleted = 0', 'manufacturer_name');
                    
                    // add "New Manufacturer" option
                    $currElement = $form->getElement('select_manufacturer');
                    $currElement->clearMultiOptions();
                    $currElement->addMultiOption('0', 'Add New Manufacturer');
                    foreach ( $manufacturers as $row )
                    {
                        $currElement->addMultiOption($row ['manufacturer_id'], ucwords(strtolower($row ['manufacturer_name'])));
                    }
                    
                    // reset form
                    $currElement->setValue('');
                    $form->getElement('manufacturer_name')->setValue('');
                }
                catch ( Exception $e )
                {
                    $db->rollback();
                    Throw new exception("Critical Manufacturer Update Error.", 0, $e);
                }
            }
        }
        $this->view->manufacturersForm = $form;
    }

    /**
     * The manufacturerdetailsAction accepts a parameter for the manufacturerid
     * and gets the
     * details from the database.
     * Returns the details array in a json encoded format.
     */
    public function manufacturerdetailsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $manufacturerID = $this->_getParam('manufacturerid', false);
        
        $manufacturerTable = new Proposalgen_Model_DbTable_Manufacturer();
        $where = $manufacturerTable->getAdapter()->quoteInto('manufacturer_id = ?', $manufacturerID, 'INTEGER');
        $manufacturer = $manufacturerTable->fetchRow('manufacturer_id = ' . $manufacturerID);
        
        try
        {
            if (count($manufacturer) > 0)
            {
                $formdata = array (
                        'manufacturer_name' => Trim(ucwords(strtolower($manufacturer ['manufacturer_name']))), 
                        'is_deleted' => ($manufacturer ['is_deleted'] == 1 ? true : false) 
                );
            }
            else
            {
                // empty form values
                $formdata = array (
                        'manufacturer_name' => '', 
                        'is_deleted' => false 
                );
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find manufacturer.", 0, $e);
        } // end catch
        

        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * The uploadpricingAction allows the system admin or dealer to select a .
     *
     *
     * csv file with pricing
     * to upload into the database for a specific report. The file must be
     * formatted and contain
     * required columns to be accepted. A preview of the upload is available and
     * must be confirmed
     * before the actual upload is complete
     */
    public function uploadpricingAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = "Upload Pricing File";
        
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->setDestination($this->config->app->uploadPath);
            
            // Limit the extensions to csv files
            $upload->addValidator('Extension', false, array (
                    'csv' 
            ));
            $upload->getValidator('Extension')->setMessage('<p><span class="warning">*</span> File "' . basename($_FILES ['uploadedfile'] ['name']) . '" has an <em>invalid</em> extension. A <span style="color: red;">.csv</span> is required.</p>');
            
            // Limit the amount of files to maximum 1
            $upload->addValidator('Count', false, 1);
            $upload->getValidator('Count')->setMessage('<p><span class="warning">*</span> You are only allowed to upload 1 file at a time.</p>');
            
            // Limit the size of all files to be uploaded to maximum 4MB and
            // mimimum 500B
            $upload->addValidator('FilesSize', false, array (
                    'min' => '500B', 
                    'max' => '4MB' 
            ));
            $upload->getValidator('FilesSize')->setMessage('<p><span class="warning">*</span> File size must be between 500B and 4MB.</p>');
            
            if ($upload->receive())
            {
                $is_valid = true;
                
                try
                {
                    $lines = file($upload->getFileName(), FILE_IGNORE_NEW_LINES);
                    
                    // required fields list
                    $required = array (
                            'printermodelid', 
                            'modelname', 
                            'manufacturer', 
                            'cpp_labor', 
                            'cpp_parts', 
                            'black oem sku', 
                            'black oem cost', 
                            'black oem yield', 
                            'cyan oem sku', 
                            'cyan oem cost', 
                            'cyan oem yield', 
                            'magenta oem sku', 
                            'magenta oem cost', 
                            'magenta oem yield', 
                            'yellow oem sku', 
                            'yellow oem cost', 
                            'yellow oem yield', 
                            'black compatible sku', 
                            'black compatible cost', 
                            'black compatible yield', 
                            'cyan compatible sku', 
                            'cyan compatible cost', 
                            'cyan compatible yield', 
                            'magenta compatible sku', 
                            'magenta compatible cost', 
                            'magenta compatible yield', 
                            'yellow compatible sku', 
                            'yellow compatible cost', 
                            'yellow compatible yield' 
                    );
                    
                    // grab the first row of items(the column headers)
                    $headers = str_getcsv(strtolower($lines [0]));
                    
                    // check headers to make sure required fields exist.
                    foreach ( $required as $key => $value )
                    {
                        if (! in_array(strtolower($required [$key]), $headers))
                        {
                            if (empty($this->view->message))
                            {
                                $this->view->message = "<h3>Upload failed</h3>";
                            }
                            $this->view->message .= "<p><span class=\"warning\">*</span> This file is missing required column: " . $required [$key] . ".</p>";
                            // throw exception
                            $is_valid = false;
                        }
                    }
                    
                    if ($is_valid)
                    {
                        // create an associative array of the csv infomation
                        foreach ( $lines as $key => $value )
                        {
                            if ($key > 0)
                            {
                                $devices [$key] = str_getcsv($value);
                                
                                // combine the column headers and the device
                                // data into one associative array
                                $finalDevices [] = array_combine($headers, $devices [$key]);
                            }
                        }
                        $this->view->headerArray = $headers;
                        $this->view->resultsArray = $finalDevices;
                        $this->view->message = "<p>Please review the data and click confirm to complete the upload.</p>";
                        
                        // store array in session to be used by
                        // confirmationAction to save the values to the database
                        $columns = new Zend_Session_Namespace('columns_array');
                        $columns->array = $headers;
                        
                        $results = new Zend_Session_Namespace('results_array');
                        $results->array = $finalDevices;
                    }
                }
                catch ( Exception $e )
                {
                    $this->view->message = "<p><span class=\"warning\">*</span> Error: File could not be uploaded.</p>";
                }
                
                // delete the uploaded file
                unlink($upload->getFileName());
            }
            else
            {
                // if upload fails, print error message message
                $this->view->errMessages = $upload->getMessages();
            }
        }
        
        return;
    }

    /**
     * The confirmationAction gets called after the uplaod preview has been
     * confirmed.
     * It saves each
     * record into a device instance table associated with a report and user.
     * The list is taken to another
     * page which requires that the user map each device to a master device or
     * adds a new user device with the
     * option to make a request to make it master. They also have the option to
     * ignore or exclude devices.
     */
    public function confirmationAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->formTitle = 'Upload Confirmation';
        
        // loop through $finalDevices and seperate into proper locations
        if ($this->_request->isPost())
        {
            $date = date('Y-m-d H:i:s T');
            
            // get arrays from indexAction
            $results = new Zend_Session_Namespace('results_array');
            $columns = new Zend_Session_Namespace('columns_array');
            
            $db->beginTransaction();
            try
            {
                // add new data from import
                foreach ( $results->array as $key => $value )
                {
                    $manufacturername = $results->array [$key] ['modelmfg'];
                    $devicename = strtolower($results->array [$key] ['modelname']);
                    $devicename = str_replace($manufacturername . ' ', '', $devicename);
                    
                    // NOTE: if Hewlet-packard we also need to stip away HP
                    if ($manufacturername == 'hewlett-packard')
                    {
                        $devicename = str_replace('hp ', '', $devicename);
                    }
                    $devicename = ucwords(trim($devicename));
                    
                    // prep HP manufacturer to Hewlett-Packard (do we want to do
                    // this???)
                    if ($manufacturername == "hp")
                    {
                        $manufacturername = "hewlett-packard";
                    }
                    $manufacturername = ucwords(trim($manufacturername));
                    
                    if ($devicename == '' || $manufacturername == '')
                    {
                        // skip record
                    }
                    else
                    {
                        $manufacturerTable = new Proposalgen_Model_DbTable_Manufacturer();
                        $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();
                        $devices_pfTable = new Proposalgen_Model_DbTable_PFDevices();
                        $master_matchup_pfTable = new Proposalgen_Model_DbTable_PFMasterMatchup();
                        $tonerTable = new Proposalgen_Model_DbTable_Toner();
                        $part_typeTable = new Proposalgen_Model_DbTable_PartType();
                        $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();
                        
                        // get manufacturer_id
                        $where = $manufacturerTable->getAdapter()->quoteInto('manufacturer_name = ?', $manufacturername);
                        $manufacturer = $manufacturerTable->fetchRow($where);
                        
                        if (count($manufacturer) > 0)
                        {
                            $manufacturer_id = $manufacturer ['manufacturer_id'];
                        }
                        else
                        {
                            $data = array (
                                    'manufacturer_name' => $manufacturername 
                            );
                            $manufacturer_id = $manufacturerTable->insert($data);
                        }
                        
                        if ($manufacturer_id > 0)
                        {
                            // get toner_config_id
                            $toner_config_id = 1;
                            
                            // prep date
                            if (! empty($mapping_array->array [$key] ['dateintroduction']))
                            {
                                $launch_date = new Zend_Date($mapping_array->array [$key] ['dateintroduction'], "mm/dd/yyyy HH:ii:ss");
                            }
                            else
                            {
                                $launch_date = new Zend_Date("0/0/0000 0:0:0", "mm/dd/yyyy HH:ii:ss");
                            }
                            
                            // save master_device
                            $data = array (
                                    'mastdevice_manufacturer' => $manufacturer_id, 
                                    'printer_model' => $devicename, 
                                    'toner_config_id' => $toner_config_id, 
                                    'is_copier' => $results->array [$key] ['is_copier'], 
                                    'is_fax' => $results->array [$key] ['is_fax'], 
                                    'is_scanner' => $results->array [$key] ['is_scanner'], 
                                    'watts_power_normal' => $results->array [$key] ['wattspowernormal'], 
                                    'watts_power_idle' => $results->array [$key] ['wattspoweridle'], 
                                    'launch_date' => $launch_date->toString('yyyy/mm/dd HH:ss'), 
                                    'service_cost_per_page' => $results->array [$key] ['cpp service'] 
                            );
                            $master_device_id = $master_deviceTable->insert($data);
                            
                            // save devices_pf
                            $data = array (
                                    'pf_model_id' => $results->array [$key] ['printermodelid'], 
                                    'pf_db_devicename' => $devicename, 
                                    'pf_db_manufacturer' => $manufacturername, 
                                    'date_created' => $date, 
                                    'created_by' => $this->user_id 
                            );
                            $devices_pf_id = $devices_pfTable->insert($data);
                            
                            if ($master_device_id > 0 && $devices_pf_id > 0)
                            {
                                // save master_matchup_pf
                                $data = array (
                                        'master_device_id' => $master_device_id, 
                                        'devices_pf_id' => $devices_pf_id 
                                );
                                $master_matchup_pf = $master_matchup_pfTable->insert($data);
                                
                                // save toner
                                $color_array = array (
                                        'black', 
                                        'cyan', 
                                        'magenta', 
                                        'yellow' 
                                );
                                $type_array = array (
                                        'oem', 
                                        'compatible' 
                                );
                                
                                foreach ( $color_array as $key )
                                {
                                    foreach ( $type_array as $key2 )
                                    {
                                        // get part_type_id
                                        $where = $part_typeTable->getAdapter()->quoteInto('type_name = ?', $key2);
                                        $part_type = $part_typeTable->fetchRow($where);
                                        
                                        if (count($part_type) > 0)
                                        {
                                            $part_type_id = $part_type ['part_type_id'];
                                        }
                                        else
                                        {
                                            // default to OEM
                                            $part_type_id = 1;
                                        }
                                        
                                        $data = array (
                                                'toner_SKU' => $results->array [$key] [$key . ' ' . $key2 . ' sku'], 
                                                'toner_price' => $results->array [$key] [$key . ' ' . $key2 . ' cost'], 
                                                'toner_yield' => $results->array [$key] [$key . ' ' . $key2 . ' yield'], 
                                                'part_type_id' => $part_type_id, 
                                                'manufacturer_id' => $manufacturer_id, 
                                                'toner_color_id' => $key 
                                        );
                                        $toner_id = $tonerTable->insert($data);
                                    }
                                }
                                
                                if ($toner_id > 0)
                                {
                                    // save device_toner
                                    $data = array (
                                            'toner_id' => $toner_id, 
                                            'master_device_id' => $master_device_id 
                                    );
                                    $device_toner = $device_tonerTable->insert($data);
                                }
                            }
                        }
                    }
                }
                $db->commit();
                $this->_redirect('/admin/confirmation');
            }
            catch ( Zend_Db_Exception $e )
            {
                $db->rollBack();
                $this->view->message = "Unknown Error.";
            }
            catch ( Exception $e )
            {
                $db->rollBack();
                $this->view->message = "Error: Your file was not saved. Please double check the file and try again. If you continue to experience problems saving, contact your administrator.<br /><br />";
            }
        }
    }

    function resizeImage ($image, $width, $height, $scale)
    {
        list ( $imagewidth, $imageheight, $imageType ) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType)
        {
            case "image/gif" :
                $source = imagecreatefromgif($image);
                break;
            case "image/pjpeg" :
            case "image/jpeg" :
            case "image/jpg" :
                $source = imagecreatefromjpeg($image);
                break;
            case "image/png" :
            case "image/x-png" :
                $source = imagecreatefrompng($image);
                break;
        }
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $width, $height);
        
        switch ($imageType)
        {
            case "image/gif" :
                imagegif($newImage, $image);
                break;
            case "image/pjpeg" :
            case "image/jpeg" :
            case "image/jpg" :
                imagejpeg($newImage, $image, 90);
                break;
            case "image/png" :
            case "image/x-png" :
                imagepng($newImage, $image);
                break;
        }
        
        chmod($image, 0777);
        return $image;
    }
    
    // You do not need to alter these functions
    function resizeThumbnailImage ($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale)
    {
        list ( $imagewidth, $imageheight, $imageType ) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);
        
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType)
        {
            case "image/gif" :
                $source = imagecreatefromgif($image);
                break;
            case "image/pjpeg" :
            case "image/jpeg" :
            case "image/jpg" :
                $source = imagecreatefromjpeg($image);
                break;
            case "image/png" :
            case "image/x-png" :
                $source = imagecreatefrompng($image);
                break;
        }
        imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
        switch ($imageType)
        {
            case "image/gif" :
                imagegif($newImage, $thumb_image_name);
                break;
            case "image/pjpeg" :
            case "image/jpeg" :
            case "image/jpg" :
                imagejpeg($newImage, $thumb_image_name, 90);
                break;
            case "image/png" :
            case "image/x-png" :
                imagepng($newImage, $thumb_image_name);
                break;
        }
        chmod($thumb_image_name, 0777);
        return $thumb_image_name;
    }
    
    // You do not need to alter these functions
    function getHeight ($image)
    {
        $size = getimagesize($image);
        $height = $size [1];
        return $height;
    }
    
    // You do not need to alter these functions
    function getWidth ($image)
    {
        $size = getimagesize($image);
        $width = $size [0];
        return $width;
    }

    public function showimageAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->disableLayout();
        $page = $this->_getParam('page', null);
        $size = $this->_getParam('size', null);
        $default = $this->_getParam('default', false);
        
        if ($size == "thumb")
        {
            $field = "company_logo";
        }
        else
        {
            $field = "full_company_logo";
        }
        
        $db->begintransaction();
        try
        {
            $userTable = new Proposalgen_Model_DbTable_Users();
            $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
            if (in_array("Standard User", $this->privilege) || $page == "managemysettings")
            {
                $where = $userTable->getAdapter()->quoteInto("user_id = ?", $this->user_id, 'INTEGER');
                $user = $userTable->fetchRow($where);
                $image = base64_decode($user [$field]);
                
                if (count($user) > 0 && empty($user [$field]) || $default == true)
                {
                    $where = $dealer_companyTable->getAdapter()->quoteInto("dealer_company_id = ?", $this->dealer_company_id, 'INTEGER');
                    $dealer_company = $dealer_companyTable->fetchRow($where);
                    $image = base64_decode($dealer_company [$field]);
                    
                    if (count($dealer_company) > 0 && empty($dealer_company [$field]))
                    {
                        $where = $dealer_companyTable->getAdapter()->quoteInto("company_name = 'MASTER'", null);
                        $dealer_company = $dealer_companyTable->fetchRow($where);
                        $image = base64_decode($dealer_company [$field]);
                    }
                }
            }
            else if (in_array("Dealer Admin", $this->privilege))
            {
                $where = $dealer_companyTable->getAdapter()->quoteInto("dealer_company_id = ?", $this->dealer_company_id, 'INTEGER');
                $dealer_company = $dealer_companyTable->fetchRow($where);
                $image = base64_decode($dealer_company [$field]);
                
                if ((count($dealer_company) > 0 && empty($dealer_company [$field])) || $default == true)
                {
                    $where = $dealer_companyTable->getAdapter()->quoteInto("company_name = 'MASTER'", null);
                    $dealer_company = $dealer_companyTable->fetchRow($where);
                    $image = base64_decode($dealer_company [$field]);
                }
            }
            else if (in_array("System Admin", $this->privilege))
            {
                $where = $dealer_companyTable->getAdapter()->quoteInto("company_name = 'MASTER'", null);
                $dealer_company = $dealer_companyTable->fetchRow($where);
                $image = base64_decode($dealer_company [$field]);
            }
            
            $this->view->data = $image;
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
            echo $e;
        }
    }

    public function removeimageAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->disableLayout();
        $page = $this->_getParam('page', null);
        
        $db->begintransaction();
        try
        {
            $upload_dir = $this->config->app->uploadPath; // The directory for
            // the images to be
            // saved in
            $upload_path = $upload_dir . "/"; // The path to where the image
            // will be saved
            $large_image_prefix = "resize_"; // The prefix name to large image
            $thumb_image_prefix = "thumbnail_"; // The prefix name to the thumb
            // image
            $large_image_name = $large_image_prefix . $_SESSION ['random_key']; // New
            // name
            // of
            // the
            // large
            // image
            // (append
            // the
            // timestamp
            // to
            // the
            // filename)
            $thumb_image_name = $thumb_image_prefix . $_SESSION ['random_key']; // New
            // name
            // of
            // the
            // thumbnail
            // image
            // (append
            // the
            // timestamp
            // to
            // the
            // filename)
            $large_image_location = $upload_path . $large_image_name . $_SESSION ['user_file_ext'];
            $thumb_image_location = $upload_path . $thumb_image_name . $_SESSION ['user_file_ext'];
            
            $data ["full_company_logo"] = null;
            $data ["company_logo"] = null;
            
            if (in_array("Standard User", $this->privilege) || $page == "managemysettings")
            {
                // table is users table
                $table = new Proposalgen_Model_DbTable_Users();
                $where = $table->getAdapter()->quoteInto("user_id = ?", $this->user_id, 'INTEGER');
            }
            else
            {
                // table is dealer_company table
                $table = new Proposalgen_Model_DbTable_DealerCompany();
                $where = $table->getAdapter()->quoteInto("dealer_company_id = ?", $this->dealer_company_id, 'INTEGER');
            }
            
            $dealer_company = $table->update($data, $where);
            $this->view->data = "<p>The image has been removed and the default image is being used.</p>";
            
            // Delete the physical files from the server
            if (file_exists($large_image_location))
            {
                unlink($large_image_location);
            }
            if (file_exists($thumb_image_location))
            {
                unlink($thumb_image_location);
            }
            
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
        }
    }

    public function init_upload_settings ()
    {
        $this->max_file = "1"; // Maximum file size in MB
        $this->max_width = "800"; // Max width allowed for the large image
        $this->max_height = "400"; // Max height allowed for the large image
        $this->thumb_width = "375"; // Width of thumbnail image
        $this->thumb_height = "150"; // Height of thumbnail image
        $this->current_large_image_width = null;
        $this->current_large_image_height = null;
        
        // only assign a new timestamp if the session variable is empty
        if (! isset($_SESSION ['random_key']) || strlen($_SESSION ['random_key']) == 0)
        {
            $_SESSION ['random_key'] = strtotime(date('Y-m-d H:i:s')); // assign
            // the
            // timestamp
            // to the
            // session
            // variable
            $_SESSION ['user_file_ext'] = "";
        }
        
        $this->upload_dir = $this->config->app->uploadPath; // The directory for
        // the images to be
        // saved in
        $this->upload_path = $this->upload_dir . "/"; // The path to where the
        // image will be saved
        $large_image_prefix = "resize_"; // The prefix name to large image
        $thumb_image_prefix = "thumbnail_"; // The prefix name to the thumb
        // image
        $this->large_image_name = $large_image_prefix . $_SESSION ['random_key']; // New
        // name
        // of
        // the
        // large
        // image
        // (append
        // the
        // timestamp
        // to
        // the
        // filename)
        $this->thumb_image_name = $thumb_image_prefix . $_SESSION ['random_key']; // New
        // name
        // of
        // the
        // thumbnail
        // image
        // (append
        // the
        // timestamp
        // to
        // the
        // filename)
        

        // Only one of these image types should be allowed for upload
        // $allowed_image_types
        // =
        // array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
        $this->allowed_image_types = array (
                'image/pjpeg' => "jpg", 
                'image/jpeg' => "jpg", 
                'image/jpg' => "jpg" 
        );
        $allowed_image_ext = array_unique($this->allowed_image_types); // do not
        // change
        // this
        $image_ext = ""; // initialise variable, do not change this.
        foreach ( $allowed_image_ext as $mime_type => $ext )
        {
            $this->image_ext .= strtoupper($ext) . " ";
        }
        
        // using a session for scalability in the future... in case we decide to
        // try to allow .gif or .png files
        // this may require another field for the file extension to be added to
        // the database
        if (empty($_SESSION ['user_file_ext']))
        {
            $_SESSION ['user_file_ext'] = '.jpg';
        }
        
        // Image Locations
        $this->large_image_location = $this->upload_path . $this->large_image_name . $_SESSION ['user_file_ext'];
        $this->thumb_image_location = $this->upload_path . $this->thumb_image_name . $_SESSION ['user_file_ext'];
    }

    public function rebuild_logos ($level)
    {
        $result = array ();
        $dealer_company_id = Zend_Auth::getInstance()->getIdentity()->dealer_company_id;
        
        try
        {
            // get proper result
            if ($level == "report")
            {
                // check report table
                $session = new Zend_Session_Namespace('proposalgenerator_report');
                $report_id = $session->report_id;
                if ($report_id > 0)
                {
                    $table = new Proposalgen_Model_DbTable_Reports();
                    $where = $table->getAdapter()->quoteInto('report_id = ?', $report_id, 'INTEGER');
                    $result = $table->fetchRow($where);
                }
            }
            
            if ($level == "user" && count($result) == 0)
            {
                // check user table
                $table = new Proposalgen_Model_DbTable_Users();
                $where = $table->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                $result = $table->fetchRow($where);
            }
            
            if ($level == "dealer" && count($result) == 0)
            {
                // check dealer_company
                $table = new Proposalgen_Model_DbTable_DealerCompany();
                $where = $table->getAdapter()->quoteInto('dealer_company_id = ?', $dealer_company_id, 'INTEGER');
                $result = $table->fetchRow($where);
            }
            
            if ($level == "admin" && count($result) == 0)
            {
                // check master
                $table = new Proposalgen_Model_DbTable_DealerCompany();
                $where = $table->getAdapter()->quoteInto('company_name = ?', 'MASTER', 'INTEGER');
                $result = $table->fetchRow($where);
            }
            
            if (count($result) > 0)
            {
                if (! file_exists($this->large_image_location) && ! empty($result ['full_company_logo']))
                {
                    $full_image = base64_decode($result ['full_company_logo']);
                    $full_image = imagecreatefromstring($full_image);
                    imagejpeg($full_image, $this->large_image_location, 75);
                    
                    if (! file_exists($this->thumb_image_location) && ! empty($result ['company_logo']))
                    {
                        $thumb_image = base64_decode($result ['company_logo']);
                        $thumb_image = imagecreatefromstring($thumb_image);
                        imagejpeg($thumb_image, $this->thumb_image_location, 75);
                    }
                }
            }
        }
        catch ( Exception $e )
        {
        }
    }

    public function scale_image ($height, $width)
    {
        try
        {
            // Scale the image if it is greater than the width set above
            if ($height > $this->max_height || $width > $this->max_width)
            {
                $width_over = $width - $this->max_width;
                $height_over = $height - $this->max_height;
                
                $scaled_width = $this->max_width / $width;
                $scaled_height = $this->max_height / $height;
                
                if ($scaled_height < $scaled_width)
                {
                    $scale = $scaled_height;
                }
                else
                {
                    $scale = $scaled_width;
                }
                $uploaded = $this->resizeImage($this->large_image_location, $width, $height, $scale);
            }
            else
            {
                $scale = 1;
                $uploaded = $this->resizeImage($this->large_image_location, $width, $height, $scale);
            }
        }
        catch ( Exception $e )
        {
        }
    }

    /**
     * Allows system admins to set the default settings for the system
     * BOOKMARK: SYSTEMADMIN SETTINGS
     */
    public function managesettingsAction ()
    {
        $this->view->title = 'Manage Settings';
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // Get Override Settings
        $dealerCompany = Proposalgen_Model_DealerCompany::getMasterCompany();
        $dealerSettings = $dealerCompany->getReportSettings(false); // Get the
        // settings
        // without
        // any
        // overrides
        // for the
        // values on
        // the form
        

        // Grab the settings form
        $form = new Proposalgen_Form_Settings_SystemAdmin();
        
        // Add all the pricing configs
        $pricingConfigs = Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll();
        foreach ( $pricingConfigs as $pricingConfig )
        {
            if ($pricingConfig->getPricingConfigId() !== 1)
            {
                $form->getElement('pricing_config_id')->addMultiOption($pricingConfig->getPricingConfigId(), $pricingConfig->getConfigName());
            }
        }
        
        // Set form values based on the system selected settings
        foreach ( $dealerSettings as $setting => $value )
        {
            $form->getElement($setting)->setValue((empty($value) ? "" : $value));
        }
        
        if ($this->_request->isPost())
        {
            // get form values
            $formData = $this->_request->getPost();
            //print_r($formData); die;
            

            if ($form->isValid($formData))
            {
                if (isset($formData ['save_settings']))
                {
                    $db->beginTransaction();
                    try
                    {
                        // Make all empty values = null
                        foreach ( $formData as $value )
                        {
                            $value = (empty($value)) ? null : $value;
                        }
                        
                        /*
                         * Required
                         */
                        $dealerCompany->setDcEstimatedPageCoverageMono($formData ["estimated_page_coverage_mono"]);
                        $dealerCompany->setDcEstimatedPageCoverageColor($formData ["estimated_page_coverage_color"]);
                        $dealerCompany->setDcActualPageCoverageMono($formData ["actual_page_coverage_mono"]);
                        $dealerCompany->setDcActualPageCoverageColor($formData ["actual_page_coverage_color"]);
                        
                        /*
                         * Null is acceptable
                         */
                        $dealerCompany->setDcMonthlyLeasePayment($formData ["monthly_lease_payment"]);
                        $dealerCompany->setDcDefaultPrinterCost($formData ["default_printer_cost"]);
                        $dealerCompany->setDcLeasedBwPerPage($formData ["leased_bw_per_page"]);
                        $dealerCompany->setDcLeasedColorPerPage($formData ["leased_color_per_page"]);
                        $dealerCompany->setDcMpsBwPerPage($formData ["mps_bw_per_page"]);
                        $dealerCompany->setDcMpsColorPerPage($formData ["mps_color_per_page"]);
                        $dealerCompany->setDcKilowattsPerHour($formData ["kilowatts_per_hour"]);
                        $dealerCompany->setPricingConfigId($formData ["pricing_config_id"]);
                        $dealerCompany->setDcServiceCostPerPage($formData ["service_cost_per_page"]);
                        $dealerCompany->setDcAdminChargePerPage($formData ["admin_charge_per_page"]);
                        
                        $dealerCompany->setDcReportMargin($formData ["pricing_margin"]);
                        
                        // Save User
                        Proposalgen_Model_Mapper_DealerCompany::getInstance()->save($dealerCompany);
                        
                        $this->_helper->flashMessenger(array (
                                "success" => "Your settings have been updated." 
                        ));
                        $db->commit();
                    }
                    catch ( Zend_Db_Exception $e )
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array (
                                "error" => "An error occured while saving your settings." 
                        ));
                    }
                    catch ( Exception $e )
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array (
                                "error" => "An error occured while saving your settings." 
                        ));
                    }
                }
            }
            else
            {
                $this->_helper->flashMessenger(array (
                        "error" => "Please review the errors below." 
                ));
                $form->populate($formData);
            }
        }
        
        // add form to page
        $form->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'forms/settings/systemadmin.phtml' 
                        ) 
                ) 
        ));
        
        $this->view->settingsForm = $form;
    }

    /**
     * Allows the user to set their own settings in the override hierarchy
     * BOOKMARK: USER SETTINGS
     */
    public function managemysettingsAction ()
    {
        $this->view->title = 'Manage My Settings';
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $message = '';
        $hasErrors = false;
        
        // Get Override Settings
        $userDealerCompany = Proposalgen_Model_DealerCompany::getCurrentUserCompany();
        $dealerName = ucwords(strtolower($userDealerCompany->getCompanyName()));
        
        $dealerCompany = Proposalgen_Model_DealerCompany::getMasterCompany();
        $dealerSettings = $dealerCompany->getReportSettings();
        
        $user = Proposalgen_Model_User::getCurrentUser();
        $userSettings = $user->getReportSettings(false); // Get the user
        // settings with no
        // overrides
        

        // Grab the settings form
        $form = new Proposalgen_Form_Settings_User();
        
        $pricingConfigs = Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll();
        
        // Add all the pricing configs
        foreach ( $pricingConfigs as $pricingConfig )
        {
            $form->getElement('pricing_config_id')->addMultiOption($pricingConfig->getPricingConfigId(), ($pricingConfig->getPricingConfigId() !== 1) ? $pricingConfig->getConfigName() : "");
        }
        
        // Set form values based on the users selected settings
        foreach ( $userSettings as $setting => $value )
        {
            $form->getElement($setting)->setValue((empty($value) ? "" : $value));
        }
        
        if ($this->_request->isPost())
        {
            // get form values
            $formData = $this->_request->getPost();
            
            if ($form->isValid($formData))
            {
                if (isset($formData ['save_settings']))
                {
                    $db->beginTransaction();
                    try
                    {
                        // Make all empty values = null
                        foreach ( $formData as $value )
                        {
                            $value = (empty($value)) ? null : $value;
                        }
                        /*
                         * Required
                         */
                        $user->setUserEstimatedPageCoverageMono($formData ["estimated_page_coverage_mono"]);
                        $user->setUserEstimatedPageCoverageColor($formData ["estimated_page_coverage_color"]);
                        $user->setUserActualPageCoverageMono($formData ["actual_page_coverage_mono"]);
                        $user->setUserActualPageCoverageColor($formData ["actual_page_coverage_color"]);
                        
                        /*
                         * Null is acceptable
                         */
                        $user->setUserMonthlyLeasePayment($formData ["monthly_lease_payment"]);
                        $user->setUserDefaultPrinterCost($formData ["default_printer_cost"]);
                        $user->setUserLeasedBwPerPage($formData ["leased_bw_per_page"]);
                        $user->setUserLeasedColorPerPage($formData ["leased_color_per_page"]);
                        $user->setUserMpsBwPerPage($formData ["mps_bw_per_page"]);
                        $user->setUserMpsColorPerPage($formData ["mps_color_per_page"]);
                        $user->setUserKilowattsPerHour($formData ["kilowatts_per_hour"]);
                        $user->setPricingConfigId($formData ["pricing_config_id"]);
                        $user->setUserServiceCostPerPage($formData ["service_cost_per_page"]);
                        $user->setUserAdminChargePerPage($formData ["admin_charge_per_page"]);
                        
                        $user->setUserPricingMargin($formData ["pricing_margin"]);
                        
                        // Save User
                        Proposalgen_Model_Mapper_User::getInstance()->save($user);
                        
                        $this->_helper->flashMessenger(array (
                                "success" => "Your settings have been updated." 
                        ));
                        $db->commit();
                    }
                    catch ( Zend_Db_Exception $e )
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array (
                                "error" => "An error occured while saving your settings." 
                        ));
                    }
                    catch ( Exception $e )
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array (
                                "error" => "An error occured while saving your settings." 
                        ));
                    }
                }
            }
            else
            {
                $this->_helper->flashMessenger(array (
                        "error" => "Please review the errors below." 
                ));
                $form->populate($formData);
            }
        }
        
        $defaultSettings = $dealerSettings;
        if ($defaultSettings ["pricing_config_id"] !== 1)
        {
            $defaultSettings ["pricing_config_id"] = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($defaultSettings ["pricing_config_id"])->getConfigName();
        }
        else
        {
            $defaultSettings ["pricing_config_id"] = "";
        }
        
        // add form to page
        $form->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'forms/settings/user.phtml', 
                                'dealerData' => $defaultSettings, 
                                'dealerName' => $dealerName, 
                                'message' => $message 
                        ) 
                ) 
        ));
        $this->view->settingsForm = $form;
    }

    /**
     */
    public function managedealerpricingAction ()
    {
        // get list of requests that are not completed from database
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/grid.celledit.js', 'text/javascript');
        
        $this->view->title = 'Manage Dealer Pricing';
        $date = date('Y-m-d H:i:s T');
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // get users company name
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
        $dealer_company = $dealer_companyTable->fetchRow($where);
        
        if (count($dealer_company) > 0)
        {
            $company_name = $dealer_company->company_name;
        }
        else
        {
            $company_name = "Dealer";
        }
        
        // add device form
        $form = new Proposalgen_Form_Device(null, "edit");
        $form->removeElement('save_device');
        $form->removeElement('delete_device');
        $form->removeElement('back_button');
        
        // fill manufacturer dropdown
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers = $manufacturersTable->fetchAll('is_deleted = 0', 'manufacturer_name');
        $currElement = $form->getElement('manufacturer_id');
        $currElement->addMultiOption('0', 'Select Manufacturer');
        foreach ( $manufacturers as $row )
        {
            $currElement->addMultiOption($row ['manufacturer_id'], ucwords(strtolower($row ['manufacturer_name'])));
        }
        
        // remove unneeded fields from form
        $form->removeElement('launch_date');
        $form->removeElement('serial_number');
        $form->removeElement('new_printer');
        $form->removeElement('toner_config_id');
        $form->removeElement('is_copier');
        $form->removeElement('is_scanner');
        $form->removeElement('is_fax');
        $form->removeElement('is_duplex');
        // $form->removeElement('is_mps_supported');
        $form->removeElement('jit_supplies_supported');
        $form->removeElement('watts_power_normal');
        $form->removeElement('watts_power_idle');
        $form->removeElement('is_deleted');
        $form->removeElement('delete_device');
        $form->removeElement('is_replacement_device');
        $form->removeElement('replacement_category');
        $form->removeElement('is_letter_legal');
        $form->removeElement('print_speed');
        $form->removeElement('resolution');
        $form->removeElement('paper_capacity');
        $form->removeElement('cpp_above');
        $form->removeElement('monthly_rate');
        
        // rename pricing label
        $currElement = $form->getElement('device_price');
        $currElement->setLabel($company_name . ' Price:');
        $currElement->setAttrib('readonly', true);
        $currElement->setRequired(false);
        $currElement->removeValidator("Float");
        $currElement->removeValidator("GreaterThan");
        $currElement->setAttrib('style', 'text-align: right; border: 0px; background-color: #ffffff;');
        
        // check if this page has been posted to
        if ($this->_request->isPost())
        {
            
            $formData = $this->_request->getPost();
            
            if ($form->isValid($formData))
            {
                // validate fields
                if ($formData ["manufacturer_id"] == 0)
                {
                    $this->view->message = 'Error: You must select a manufacturer.';
                }
                else if ($formData ["printer_model"] == 0)
                {
                    $this->view->message = 'Error: You must select a printer model.';
                }
                else if ($formData ["override_price"] != '' && $formData ["override_price"] <= 0)
                {
                    $this->view->message = 'Error: You must blank our the value to delete it or enter a number greater then zero.';
                }
                else
                {
                    // valid form data
                    if ($formData ['save_flag'] == "save")
                    {
                        // update the selected device
                        $db->beginTransaction();
                        try
                        {
                            $currElement = $form->getElement('printer_model');
                            $master_device_id = $currElement->getValue();
                            $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();
                            
                            $dealer_device_overrideTable = new Proposalgen_Model_DbTable_DealerDeviceOverride();
                            $dealer_device_overrideData = array (
                                    'override_device_price' => $formData ["override_price"] 
                            );
                            
                            // get users(dealers) company_id
                            $userTable = new Proposalgen_Model_DbTable_Users();
                            $where = $userTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                            $user = $userTable->fetchRow('user_id = ' . $this->user_id);
                            $company_id = $user ['dealer_company_id'];
                            
                            if ($master_device_id > 0)
                            {
                                // get printer_model
                                $where = $master_deviceTable->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                                $master_device = $master_deviceTable->fetchRow($where);
                                $printer_model = $master_device ['printer_model'];
                                
                                // check to see if override already exists
                                $where = $dealer_device_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $company_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                $dealer_device_override = $dealer_device_overrideTable->fetchRow($where);
                                
                                if (empty($formData ["override_price"]))
                                {
                                    $dealer_device_overrideTable->delete($where);
                                }
                                else if (count($dealer_device_override) > 0)
                                {
                                    $dealer_device_overrideTable->update($dealer_device_overrideData, $where);
                                }
                                else
                                {
                                    $dealer_device_overrideData ['dealer_company_id'] = $company_id;
                                    $dealer_device_overrideData ['master_device_id'] = $master_device_id;
                                    $dealer_device_overrideTable->insert($dealer_device_overrideData);
                                }
                                $this->view->message = 'The Printer has been updated.';
                            }
                            else
                            {
                                $this->view->message = 'Database Error: Printer Model could not be found.';
                            }
                            
                            $toner_array = array ();
                            if ($formData ['toner_array'])
                            {
                                $toner_id = 0;
                                $override_price = 0;
                                
                                $toner_array = explode(",", $formData ['toner_array']);
                                foreach ( $toner_array as $key )
                                {
                                    $key = str_replace("'", "", $key);
                                    $parts = explode(":", $key);
                                    $toner_id = $parts [0];
                                    $override_price = $parts [1];
                                    
                                    // validate
                                    $message = '';
                                    if ($override_price != '' && ! is_numeric($override_price))
                                    {
                                        $message = "Please enter a valid price greater then zero.";
                                    }
                                    else if ($override_price != '' && $override_price <= 0)
                                    {
                                        $message = "Please blank out price to remove it or enter a price greater then zero.";
                                    }
                                    
                                    if (empty($message))
                                    {
                                        try
                                        {
                                            // update database
                                            $dealer_toner_overrideTable = new Proposalgen_Model_DbTable_DealerTonerOverride();
                                            $dealer_toner_overrideData = array (
                                                    'override_toner_price' => $override_price 
                                            );
                                            
                                            // get users(dealers) company_id
                                            $userTable = new Proposalgen_Model_DbTable_Users();
                                            $where = $userTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                                            $user = $userTable->fetchRow($where);
                                            $company_id = $user ['dealer_company_id'];
                                            
                                            if ($toner_id > 0)
                                            {
                                                // check to see if override
                                                // exists for user/toner
                                                $where = $dealer_toner_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $company_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                                                $dealer_toner_override = $dealer_toner_overrideTable->fetchRow($where);
                                                
                                                if (empty($override_price))
                                                {
                                                    $dealer_toner_overrideTable->delete($where);
                                                }
                                                else if (count($dealer_toner_override) > 0)
                                                {
                                                    $dealer_toner_overrideTable->update($dealer_toner_overrideData, $where);
                                                }
                                                else
                                                {
                                                    $dealer_toner_overrideData ['dealer_company_id'] = $company_id;
                                                    $dealer_toner_overrideData ['toner_id'] = $toner_id;
                                                    $dealer_toner_overrideTable->insert($dealer_toner_overrideData);
                                                }
                                                $this->view->message = 'The Printer has been updated.';
                                            }
                                            else
                                            {
                                                $message = "Toner was not found.";
                                            }
                                        }
                                        catch ( Exception $e )
                                        {
                                            $db->rollback();
                                            $this->view->message = "An error has occurred and the toner price override was not saved.";
                                        }
                                    }
                                    else
                                    {
                                        $db->rollback();
                                        $this->view->message = $message;
                                    }
                                }
                            }
                            $db->commit();
                            
                            // set selected printer model to new printer model
                            $this->view->printer_model = $master_device_id;
                            
                            // destroying the request session.
                            Zend_Session::namespaceUnset('request');
                        }
                        catch ( Zend_Db_Exception $e )
                        {
                            $db->rollback();
                            $this->view->message = 'Database Error: Override price for "' . $printer_model . '" could not be set.';
                        }
                        catch ( Exception $e )
                        {
                            // CRITICAL UPDATE EXCEPTION
                            $db->rollback();
                            Throw new exception("Critical Override Price Error.", 0, $e);
                        } // end catch
                    } // end elseif
                }
            }
            else
            {
                // if formdata was not valid, repopulate form(error messages
                // from validations are automatically added)
                $form->populate($formData);
            } // end else
        } // end if
        

        $this->view->deviceform = $form;
        
        // ********************************************************************/
    }

    /**
     * The dealerdevicedetailsAction accepts a parameter for the deviceid and
     * gets the device
     * details from the database.
     * Returns the details array in a json encoded format.
     */
    public function dealerdevicedetailsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);
        
        // get company id
        $usersTable = new Proposalgen_Model_DbTable_Users();
        $where = $usersTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
        $user = $usersTable->fetchRow($where);
        $company_id = $user ['dealer_company_id'];
        try
        {
            if ($deviceID > 0)
            {
                // get dealer pricing margin from master
                $dealer_pricing_margin = ($this->getPricingMargin('dealer') / 100) + 1;
                
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        'md' => 'master_device' 
                ))
                    ->joinLeft(array (
                        'ddo' => 'dealer_device_override' 
                ), 'ddo.master_device_id = md.master_device_id AND ddo.dealer_company_id = ' . $company_id)
                    ->where('md.master_device_id = ?', $deviceID);
                $stmt = $db->query($select);
                $result = $stmt->fetchAll();
                // get device price
                $dealerOverride = new Proposalgen_Model_DbTable_DealerDeviceOverride();
                $where = $dealerOverride->getAdapter()->quoteInto('dealer_company_id = ' . $this->dealer_company_id . ' AND master_device_id = ?', $deviceID, 'INTEGER');
                $OverrideRow = $dealerOverride->fetchRow($where);
                if ($OverrideRow ['override_device_price'])
                    $deviceOverride = money_format('%i', $OverrideRow ['override_device_price']);
                else
                    $deviceOverride = null;
                $deviceTable = new Proposalgen_Model_DbTable_MasterDevice();
                $where = $deviceTable->getAdapter()->quoteInto('master_device_id = ?', $deviceID, 'INTEGER');
                $row = $deviceTable->fetchRow($where);
                if ($row ['device_price'])
                    $devicePrice = money_format('%i', $row ['device_price'] * $dealer_pricing_margin);
                else
                    $devicePrice = "-";
                if (count($result) > 0)
                {
                    $formdata = array (
                            'device_price' => $devicePrice, 
                            'override_price' => $deviceOverride 
                    );
                }
                else
                {
                    // empty form values
                    $formdata = array ();
                }
            }
            else
            {
                // empty form values
                $formdata = array ();
            }
        }
        catch ( Zend_Db_Exception $e )
        {
            $db->rollback();
            $this->view->message = 'Database Error: Device not found.';
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device.", 0, $e);
        } // end catch
        

        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * The dealertonersAction accepts a parameter for the deviceid and gets the
     * device
     * toners from the database.
     * Returns the parts array in a json encoded format.
     */
    public function dealertonersAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);
        
        // get company id
        $usersTable = new Proposalgen_Model_DbTable_Users();
        $where = $usersTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
        $user = $usersTable->fetchRow($where);
        $company_id = $user ['dealer_company_id'];
        
        try
        {
            if ($deviceID > 0)
            {
                // get dealer pricing margin
                $dealer_pricing_margin = ($this->getPricingMargin('dealer') / 100) + 1;
                
                // select toners for device
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        't' => 'toner' 
                ))
                    ->join(array (
                        'dt' => 'device_toner' 
                ), 't.toner_id = dt.toner_id')
                    ->join(array (
                        'pt' => 'part_type' 
                ), 'pt.part_type_id = t.part_type_id')
                    ->join(array (
                        'tc' => 'toner_color' 
                ), 'tc.toner_color_id = t.toner_color_id')
                    ->joinLeft(array (
                        'dto' => 'dealer_toner_override' 
                ), 'dto.toner_id = t.toner_id AND dealer_company_id = ' . $company_id, array (
                        'override_toner_price' 
                ))
                    ->where('dt.master_device_id = ?', $deviceID);
                $stmt = $db->query($select);
                $result = $stmt->fetchAll();
                
                if (count($result) > 0)
                {
                    $i = 0;
                    $formdata->page = 1;
                    $formdata->total = 1;
                    $formdata->records = count($result);
                    foreach ( $result as $row )
                    {
                        // Always uppercase OEM, but just captialize everything
                        // else
                        $type_name = ucwords(strtolower($row ['type_name']));
                        if ($type_name == "Oem")
                        {
                            $type_name = "OEM";
                        }
                        
                        $formdata->rows [$i] ['id'] = $row ['toner_id'];
                        $formdata->rows [$i] ['cell'] = array (
                                $row ['toner_id'], 
                                $row ['toner_SKU'], 
                                $type_name, 
                                ucwords(strtolower($row ['toner_color_name'])), 
                                $row ['toner_yield'], 
                                money_format('%i', ($row ['toner_price'] * $dealer_pricing_margin)), 
                                money_format('%i', ($row ['override_toner_price'] > 0 ? $row ['override_toner_price'] : null)), 
                                $row ['manufacturer_id'], 
                                $row ['master_device_id'] 
                        );
                        $i ++;
                    }
                }
                else
                {
                    $formdata = array ();
                }
            }
            else
            {
                // empty form values
                $formdata->rows [1] ['id'] = 0;
                $formdata->rows [1] ['cell'] = array (
                        0, 
                        '', 
                        '', 
                        '', 
                        0, 
                        0, 
                        0, 
                        0, 
                        0 
                );
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device parts.", 0, $e);
        } // end catch
        

        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function editdealertonerAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->disableLayout();
        
        // grab all variables
        $toner_id = $this->_getParam('toner_id', false);
        $toner_price = $this->_getParam('override_price', false);
        $master_device_id = $this->_getParam('master_device_id', false);
        
        // validate
        $message = '';
        if ($toner_price != '' && ! is_numeric($toner_price))
        {
            $message = "Please enter a valid price greater then zero.";
        }
        else if ($toner_price != '' && $toner_price <= 0)
        {
            $message = "Please blank out price to remove it or enter a price greater then zero.";
        }
        
        if (empty($message))
        {
            $db->beginTransaction();
            try
            {
                // update database
                $dealer_toner_overrideTable = new Proposalgen_Model_DbTable_DealerTonerOverride();
                $dealer_toner_overrideData = array (
                        'override_toner_price' => $toner_price 
                );
                
                // get users(dealers) company_id
                $userTable = new Proposalgen_Model_DbTable_Users();
                $where = $userTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                $user = $userTable->fetchRow($where);
                $company_id = $user ['dealer_company_id'];
                
                if ($toner_id > 0)
                {
                    // check to see if override exists for user/toner
                    $where = $dealer_toner_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $company_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                    $dealer_toner_override = $dealer_toner_overrideTable->fetchRow($where);
                    
                    if (empty($toner_price))
                    {
                        $dealer_toner_overrideTable->delete($where);
                        $message = "Toner price override has been removed.";
                    }
                    else if (count($dealer_toner_override) > 0)
                    {
                        $dealer_toner_overrideTable->update($dealer_toner_overrideData, $where);
                        $message = "Toner price override has been updated.";
                    }
                    else
                    {
                        $dealer_toner_overrideData ['dealer_company_id'] = $company_id;
                        $dealer_toner_overrideData ['toner_id'] = $toner_id;
                        $dealer_toner_overrideTable->insert($dealer_toner_overrideData);
                        $message = "Toner price override has been set.";
                    }
                }
                else
                {
                    $message = "Toner was not found.";
                }
                
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->view->message = "An error has occurred and the toner price override was not saved.";
            }
        }
        
        // output message
        $this->view->data = $message;
    }

    /**
     */
    public function managemypricingAction ()
    {
        // get list of requests that are not completed from database
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/grid.celledit.js', 'text/javascript');
        
        $this->view->title = 'Manage My Pricing';
        $date = date('Y-m-d H:i:s T');
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // add device form
        $form = new Proposalgen_Form_Device(null, "edit");
        $form->removeElement('save_device');
        $form->removeElement('delete_device');
        $form->removeElement('back_button');
        
        // get users company name
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
        $dealer_company = $dealer_companyTable->fetchRow($where);
        
        if (count($dealer_company) > 0)
        {
            $company_name = $dealer_company->company_name;
        }
        else
        {
            $company_name = "Dealer";
        }
        
        // fill manufacturer dropdown
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers = $manufacturersTable->fetchAll('is_deleted = 0', 'manufacturer_name');
        $currElement = $form->getElement('manufacturer_id');
        $currElement->addMultiOption('0', 'Select Manufacturer');
        foreach ( $manufacturers as $row )
        {
            $currElement->addMultiOption($row ['manufacturer_id'], ucwords(strtolower($row ['manufacturer_name'])));
        }
        
        // remove unneeded fields from form
        $form->removeElement('launch_date');
        $form->removeElement('serial_number');
        $form->removeElement('new_printer');
        $form->removeElement('toner_config_id');
        $form->removeElement('is_copier');
        $form->removeElement('is_scanner');
        $form->removeElement('is_fax');
        $form->removeElement('is_duplex');
        // $form->removeElement('is_mps_supported');
        $form->removeElement('jit_supplies_supported');
        $form->removeElement('watts_power_normal');
        $form->removeElement('watts_power_idle');
        $form->removeElement('is_deleted');
        $form->removeElement('delete_device');
        $form->removeElement('is_replacement_device');
        $form->removeElement('replacement_category');
        $form->removeElement('is_letter_legal');
        $form->removeElement('print_speed');
        $form->removeElement('resolution');
        $form->removeElement('paper_capacity');
        $form->removeElement('cpp_above');
        $form->removeElement('monthly_rate');
        
        // rename pricing label
        $currElement = $form->getElement('device_price');
        $currElement->setLabel('Default Price:');
        $currElement->setAttrib('readonly', true);
        $currElement->setRequired(false);
        $currElement->removeValidator("Float");
        $currElement->removeValidator("GreaterThan");
        $currElement->setAttrib('style', 'text-align: right; border: 0px; background-color: #ffffff;');
        
        // check if this page has been posted to
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            
            if ($form->isValid($formData))
            {
                // validate fields
                if ($formData ["manufacturer_id"] == 0)
                {
                    $this->view->message = 'Error: You must select a manufacturer.';
                }
                else if ($formData ["printer_model"] == 0)
                {
                    $this->view->message = 'Error: You must select a printer model.';
                }
                else if ($formData ["override_price"] != '' && $formData ["override_price"] <= 0)
                {
                    $this->view->message = 'Error: You must blank out the value to delete it or enter a number greater then zero.';
                }
                else
                {
                    // valid form data
                    if ($formData ['save_flag'] == "save")
                    {
                        // update the selected device
                        $db->beginTransaction();
                        try
                        {
                            $currElement = $form->getElement('printer_model');
                            $master_device_id = $currElement->getValue();
                            $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();
                            
                            $user_device_overrideTable = new Proposalgen_Model_DbTable_UserDeviceOverride();
                            $user_device_overrideData = array (
                                    'override_device_price' => $formData ["override_price"] 
                            );
                            
                            // get users company_id
                            $userTable = new Proposalgen_Model_DbTable_Users();
                            $where = $userTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                            $user = $userTable->fetchRow($where);
                            $company_id = $user ['dealer_company_id'];
                            
                            if ($master_device_id > 0)
                            {
                                // get printer_model
                                $where = $master_deviceTable->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                                $master_device = $master_deviceTable->fetchRow($where);
                                $printer_model = $master_device ['printer_model'];
                                
                                // check to see if override already exists
                                $where = $user_device_overrideTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                $user_device_override = $user_device_overrideTable->fetchRow($where);
                                
                                if (empty($formData ["override_price"]))
                                {
                                    $user_device_overrideTable->delete($where);
                                }
                                else if (count($user_device_override) > 0)
                                {
                                    $user_device_overrideTable->update($user_device_overrideData, $where);
                                }
                                else
                                {
                                    $user_device_overrideData ['user_id'] = $this->user_id;
                                    $user_device_overrideData ['master_device_id'] = $master_device_id;
                                    $user_device_overrideTable->insert($user_device_overrideData);
                                }
                                $this->view->message = 'The Printer has been updated.';
                            }
                            else
                            {
                                $this->view->message = 'Database Error: Printer Model could not be found.';
                            }
                            
                            $toner_array = array ();
                            if ($formData ['toner_array'])
                            {
                                $toner_id = 0;
                                $override_price = 0;
                                
                                $toner_array = explode(",", $formData ['toner_array']);
                                foreach ( $toner_array as $key )
                                {
                                    $key = str_replace("'", "", $key);
                                    $parts = explode(":", $key);
                                    $toner_id = $parts [0];
                                    $override_price = $parts [1];
                                    
                                    // validate
                                    $message = '';
                                    if ($override_price != '' && ! is_numeric($override_price))
                                    {
                                        $message = "Please enter a valid price greater then zero.";
                                    }
                                    else if ($override_price != '' && $override_price <= 0)
                                    {
                                        $message = "Please blank out price to remove it or enter a price greater then zero.";
                                    }
                                    
                                    if (empty($message))
                                    {
                                        try
                                        {
                                            // update database
                                            $user_toner_overrideTable = new Proposalgen_Model_DbTable_UserTonerOverride();
                                            $user_toner_overrideData = array (
                                                    'override_toner_price' => $override_price 
                                            );
                                            
                                            // get users(dealers) company_id
                                            $userTable = new Proposalgen_Model_DbTable_Users();
                                            $where = $userTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                                            $user = $userTable->fetchRow($where);
                                            $company_id = $user ['dealer_company_id'];
                                            
                                            if ($toner_id > 0)
                                            {
                                                // check to see if override
                                                // exists for user/toner
                                                $where = $user_toner_overrideTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                                                $user_toner_override = $user_toner_overrideTable->fetchRow($where);
                                                
                                                if (empty($override_price))
                                                {
                                                    $user_toner_overrideTable->delete($where);
                                                }
                                                else if (count($user_toner_override) > 0)
                                                {
                                                    $user_toner_overrideTable->update($user_toner_overrideData, $where);
                                                }
                                                else
                                                {
                                                    $user_toner_overrideData ['toner_id'] = $toner_id;
                                                    $user_toner_overrideData ['user_id'] = $this->user_id;
                                                    $user_toner_overrideTable->insert($user_toner_overrideData);
                                                }
                                                $this->view->message = 'The Printer has been updated.';
                                            }
                                            else
                                            {
                                                $message = "Toner was not found.";
                                            }
                                        }
                                        catch ( Exception $e )
                                        {
                                            $db->rollback();
                                            $message = "<p>An error has occurred and the toner price override was not saved.</p>";
                                        }
                                    }
                                    else
                                    {
                                        $db->rollback();
                                        $this->view->message = $message;
                                    }
                                }
                            }
                            $db->commit();
                            
                            // set selected printer model to new printer model
                            $this->view->printer_model = $master_device_id;
                            
                            // destroying the request session.
                            Zend_Session::namespaceUnset('request');
                        }
                        catch ( Zend_Db_Exception $e )
                        {
                            $db->rollback();
                            $this->view->message = 'Database Error: Override price for "' . $printer_model . '" could not be set.';
                            echo $e;
                        }
                        catch ( Exception $e )
                        {
                            // CRITICAL UPDATE EXCEPTION
                            $db->rollback();
                            Throw new exception("Critical Override Price Error.", 0, $e);
                            echo $e;
                        } // end catch
                    } // end elseif
                }
            }
            else
            {
                // if formdata was not valid, repopulate form(error messages
                // from validations are automatically added)
                $form->populate($formData);
            } // end else
        } // end if
        

        $this->view->deviceform = $form;
        
        // ********************************************************************/
    }

    /**
     * The mydevicedetailsAction accepts a parameter for the deviceid and gets
     * the device
     * details from the database.
     * Returns the details array in a json encoded format.
     */
    public function mydevicedetailsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);
        
        // get company id
        $usersTable = new Proposalgen_Model_DbTable_Users();
        $where = $usersTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
        $user = $usersTable->fetchRow($where);
        $company_id = $user ['dealer_company_id'];
        
        try
        {
            if ($deviceID > 0)
            {
                // initialize price
                $device_price = 0;
                
                // get pricing margins from dealer and master
                $dealer_pricing_margin = ($this->getPricingMargin('dealer') / 100) + 1;
                
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        'md' => 'master_device' 
                ))
                    ->joinLeft(array (
                        'ddo' => 'dealer_device_override' 
                ), 'ddo.master_device_id = md.master_device_id AND ddo.dealer_company_id = ' . $company_id, array (
                        'dealer_device_price' => 'override_device_price' 
                ))
                    ->joinLeft(array (
                        'udo' => 'user_device_override' 
                ), 'udo.master_device_id = md.master_device_id AND udo.user_id = ' . $this->user_id, array (
                        'user_device_price' => 'override_device_price' 
                ))
                    ->where('md.master_device_id = ?', $deviceID);
                $stmt = $db->query($select);
                $result = $stmt->fetchAll();
                // print_r($result); die;
                // get device price
                $dealerOverride = new Proposalgen_Model_DbTable_DealerDeviceOverride();
                $OverrideRow = $dealerOverride->fetchRow('dealer_company_id = ' . $this->dealer_company_id . ' AND master_device_id = ' . $deviceID);
                $deviceTable = new Proposalgen_Model_DbTable_MasterDevice();
                $row = $deviceTable->fetchRow('master_device_id =' . $deviceID);
                if ($OverrideRow ['override_device_price'])
                    $devicePrice = money_format('%i', $OverrideRow ['override_device_price']);
                elseif ($row ['device_price'])
                    $devicePrice = money_format('%i', $row ['device_price'] * $dealer_pricing_margin);
                else
                    $devicePrice = "-";
                if (count($result) > 0)
                {
                    // find price
                    $formdata = array (
                            'device_price' => $devicePrice, 
                            'override_price' => money_format('%i', ($result [0] ['user_device_price'] > 0 ? $result [0] ['user_device_price'] : null)) 
                    );
                }
                else
                {
                    // empty form values
                    $formdata = array ();
                }
            }
            else
            {
                // empty form values
                $formdata = array ();
            }
        }
        catch ( Zend_Db_Exception $e )
        {
            $db->rollback();
            $this->view->message = 'Database Error: Device not found.';
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device.", 0, $e);
        } // end catch
        

        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * The mytonersAction accepts a parameter for the deviceid and gets the
     * device
     * toners from the database.
     * Returns the parts array in a json encoded format.
     */
    public function mytonersAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);
        
        try
        {
            if ($deviceID > 0)
            {
                // get users company_id
                $userTable = new Proposalgen_Model_DbTable_Users();
                $user = $userTable->fetchRow('user_id = ' . $this->user_id);
                $company_id = $user ['dealer_company_id'];
                
                // get dealer pricing margin
                $dealer_pricing_margin = ($this->getPricingMargin('dealer') / 100) + 1;
                
                // select toners for device
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        't' => 'toner' 
                ))
                    ->join(array (
                        'dt' => 'device_toner' 
                ), 't.toner_id = dt.toner_id')
                    ->join(array (
                        'pt' => 'part_type' 
                ), 'pt.part_type_id = t.part_type_id')
                    ->join(array (
                        'tc' => 'toner_color' 
                ), 'tc.toner_color_id = t.toner_color_id')
                    ->joinLeft(array (
                        'dto' => 'dealer_toner_override' 
                ), 'dto.toner_id = t.toner_id AND dealer_company_id = ' . $company_id, array (
                        'override_toner_price AS dealer_toner_price' 
                ))
                    ->joinLeft(array (
                        'uto' => 'user_toner_override' 
                ), 'uto.toner_id = t.toner_id AND user_id = ' . $this->user_id, array (
                        'override_toner_price' 
                ))
                    ->where('dt.master_device_id = ?', $deviceID);
                $stmt = $db->query($select);
                $result = $stmt->fetchAll();
                
                if (count($result) > 0)
                {
                    $i = 0;
                    $toner_price = 0;
                    $formdata->page = 1;
                    $formdata->total = 1;
                    $formdata->records = count($result);
                    foreach ( $result as $row )
                    {
                        if ($row ['dealer_toner_price'] > 0)
                        {
                            $toner_price = $row ['dealer_toner_price'];
                        }
                        else
                        {
                            $toner_price = $row ['toner_price'] * $dealer_pricing_margin;
                        }
                        
                        // Always uppercase OEM, but just captialize everything
                        // else
                        $type_name = ucwords(strtolower($row ['type_name']));
                        if ($type_name == "Oem")
                        {
                            $type_name = "OEM";
                        }
                        
                        $formdata->rows [$i] ['id'] = $row ['toner_id'];
                        $formdata->rows [$i] ['cell'] = array (
                                $row ['toner_id'], 
                                $row ['toner_SKU'], 
                                $type_name, 
                                ucwords(strtolower($row ['toner_color_name'])), 
                                $row ['toner_yield'], 
                                money_format('%i', $toner_price), 
                                money_format('%i', ($row ['override_toner_price'] > 0 ? $row ['override_toner_price'] : null)), 
                                $row ['manufacturer_id'], 
                                $row ['master_device_id'] 
                        );
                        $i ++;
                    }
                }
                else
                {
                    $formdata = array ();
                }
            }
            else
            {
                // empty form values
                $formdata->rows [1] ['id'] = 0;
                $formdata->rows [1] ['cell'] = array (
                        0, 
                        '', 
                        '', 
                        '', 
                        0, 
                        0, 
                        0, 
                        0, 
                        0 
                );
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device parts.", 0, $e);
        } // end catch
        

        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function editmytonerAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->disableLayout();
        
        // grab all variables from $_POST
        $toner_id = $this->_getParam('id', false);
        $toner_price = $this->_getParam('override_price', false);
        $master_device_id = $this->_getParam('deviceid', false);
        
        // validate
        $message = '';
        if ($toner_price != '' && ! is_numeric($toner_price))
        {
            $message = "Please enter a valid price greater then zero.";
        }
        else if ($toner_price != '' && $toner_price <= 0)
        {
            $message = "Please blank out price to remove it or enter a price greater then zero.";
        }
        
        if (empty($message))
        {
            $db->beginTransaction();
            try
            {
                // update database
                $user_toner_overrideTable = new Proposalgen_Model_DbTable_UserTonerOverride();
                $user_toner_overrideData = array (
                        'override_toner_price' => $toner_price 
                );
                
                // get users(dealers) company_id
                $userTable = new Proposalgen_Model_DbTable_Users();
                $where = $userTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                $user = $userTable->fetchRow($where);
                $company_id = $user ['dealer_company_id'];
                
                if ($toner_id > 0)
                {
                    // check to see if override exists for user/toner
                    $where = $user_toner_overrideTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                    $user_toner_override = $user_toner_overrideTable->fetchRow($where);
                    
                    if (empty($toner_price))
                    {
                        $user_toner_overrideTable->delete($where);
                        $message = "Toner price override has been removed.";
                    }
                    else if (count($user_toner_override) > 0)
                    {
                        $user_toner_overrideTable->update($user_toner_overrideData, $where);
                        $message = "Toner price override has been updated.";
                    }
                    else
                    {
                        $user_toner_overrideData ['toner_id'] = $toner_id;
                        $user_toner_overrideData ['user_id'] = $this->user_id;
                        $user_toner_overrideTable->insert($user_toner_overrideData);
                        $message = "Toner price override has been set.";
                    }
                }
                else
                {
                    $message = "Toner was not found.";
                }
                
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $message = "<p>An error has occurred and the toner price override was not saved.</p><div>" . $e . "</div>";
            }
        }
        
        // output message
        $this->view->data = $message;
    }

    /**
     * The managemyrequestAction allows for non admin type users to review the
     * requests they have made for
     * new device to be added and to remove requests if needed.
     */
    public function managemyrequestsAction ()
    {
        $this->view->title = 'Manage My Requests';
        $date = date('Y-m-d H:i:s T');
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $headers = array (
                "Request #", 
                "Device", 
                "Resolved Date" 
        );
        $userRows = array ();
        $this->view->headerArray = $headers;
        
        $db->beginTransaction();
        // get list of requests that are not completed from database
        try
        {
            $requests = new Proposalgen_Model_DbTable_PFModelRequest();
            $this->view->requests = $requests->select();
            
            $where = $requests->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
            $allRequests = $requests->fetchAll($where);
            $db->commit();
            foreach ( $allRequests as $key )
                $userRows [] = $key;
        }
        catch ( Zend_Db_Exception $e )
        {
            $db->rollback();
        }
        catch ( Exception $e )
        {
            // CRITICAL UPDATE EXCEPTION
            $db->rollback();
        } // end catch
        

        $this->view->headerArray = $headers;
        $this->view->requestRows = $userRows;
    }

    /**
     * The ignoremyrequestAction allows for non system admin user to remove
     * devices that they requested.
     */
    public function ignoremyrequestAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $removeRequest = new Proposalgen_Model_DbTable_PFModelRequest();
        $request_id = $_POST ['request_id'];
        
        $db->beginTransaction();
        try
        {
            // destroying the request and commiting the change to the database.
            $where = $removeRequest->getAdapter()->quoteInto('request_id = ?', $request_id, 'INTEGER');
            $removeRequest->delete($where);
            $db->commit();
        }
        catch ( Zend_Db_Exception $e )
        {
            $db->rollback();
            $this->view->message = 'Database Error: Request could not be removed.';
        }
        catch ( Exception $e )
        {
            // CRITICAL UPDATE EXCEPTION
            $db->rollback();
            Throw new exception("Critical Device Update Error.", 0, $e);
        } // end catch
    }

    public function filtercompaniesAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $company_id = $_GET ['companyid'];
        
        $where = 'p.priv_id > 0';
        if ($company_id > 0)
        {
            $where = 'p.priv_id = ' . $company_id;
        }
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'u' => 'users' 
        ))
            ->joinLeft(array (
                'dc' => 'dealer_company' 
        ), 'dc.dealer_company_id = u.dealer_company_id')
            ->joinLeft(array (
                'up' => 'user_privileges' 
        ), 'up.user_id = u.user_id')
            ->joinLeft(array (
                'p' => 'privileges' 
        ), 'p.priv_id = up.priv_id')
            ->where($where)
            ->order('lastname', 'firstname');
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        
        $i = 0;
        $responce = null;
        if (count($result) > 0)
        {
            foreach ( $result as $row )
            {
                $responce->rows [$i] ['id'] = $row ['user_id'];
                $responce->rows [$i] ['cell'] = array (
                        $row ['user_id'], 
                        ucwords(strtolower($row ['lastname'])) . ', ' . ucwords(strtolower($row ['firstname'])) . ' (' . strtolower($row ['username']) . ')' 
                );
                $i ++;
            }
        }
        else
        {
            $responce->rows [$i] ['id'] = 0;
            $responce->rows [$i] ['cell'] = array (
                    0, 
                    '' 
            );
        }
        echo json_encode($responce);
    }

    public function getPricingMargin ($type, $dealer_id = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $master_margin = 0;
        $dealer_margin = 0;
        $user_margin = 0;
        $pricing_margin = 0;
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'u' => 'users' 
        ))
            ->joinLeft(array (
                'dc' => 'dealer_company' 
        ), 'dc.dealer_company_id = u.dealer_company_id')
            ->where('dc.company_name = "MASTER"');
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        if (count($result) > 0)
        {
            $master_margin = $result [0] ['dc_pricing_margin'];
        }
        
        $select = new Zend_Db_Select($db);
        if ($dealer_id > 0)
        {
            $select = $db->select()
                ->from(array (
                    'dc' => 'dealer_company' 
            ))
                ->where('dc.dealer_company_id = ' . $dealer_id);
        }
        else
        {
            $select = $db->select()
                ->from(array (
                    'u' => 'users' 
            ))
                ->joinLeft(array (
                    'dc' => 'dealer_company' 
            ), 'dc.dealer_company_id = u.dealer_company_id')
                ->where('u.user_id = ' . $this->user_id);
        }
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        if (count($result) > 0)
        {
            $dealer_margin = $result [0] ['dc_pricing_margin'];
        }
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'u' => 'users' 
        ))
            ->where('u.user_id = ?', $this->user_id);
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        if (count($result) > 0)
        {
            $user_margin = $result [0] ['user_pricing_margin'];
        }
        
        if ($type == "master")
        {
            $pricing_margin = $master_margin;
        }
        else if ($type == "dealer")
        {
            $pricing_margin = $dealer_margin;
        }
        else
        {
            $pricing_margin = $user_margin;
        }
        return ($pricing_margin);
    }

    public function managemyreportsAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = 'Manage My Reports';
        $this->view->filter = array (
                '0' => 'All My Reports', 
                '1' => 'My Finished', 
                '2' => 'My Unfinished' 
        );
        
        if (in_array("Dealer Admin", $this->privilege))
        {
            $this->view->filter ['3'] = 'All Company Reports';
        }
        
        if ($this->_request->isPost())
        {
            $reportTable = new Proposalgen_Model_DbTable_Reports();
            $formData = $this->_request->getPost();
            
            $db->beginTransaction();
            try
            {
                if ($formData ['form_mode'] == 'view' && $formData ['report_id'] > 0)
                {
                    $session = new Zend_Session_Namespace('proposalgenerator_report');
                    $session->report_id = $formData ["report_id"];
                    $this->_redirect('/report');
                }
                else if ($formData ['form_mode'] == 'delete')
                {
                    $response = 1;
                    foreach ( $formData as $key => $value )
                    {
                        if (strstr($key, "jqg_reports_list_"))
                        {
                            $report_id = str_replace("jqg_reports_list_", "", $key);
                            $response = $this->deleteReport($report_id);
                            if ($response == 0)
                            {
                                $this->view->message = "There was an error while trying to delete the report " . $report_id . ". Please contact your administrator.";
                                exit();
                            }
                        }
                    }
                    if ($response == 1)
                    {
                        $this->view->message = "The report(s) were successfully deleted.";
                    }
                }
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->view->message = "There was an error while trying to delete the reports. Please contact your administrator.";
            }
        }
    }

    public function myreportslistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $filter = $this->_getParam('filter', false);
        
        try
        {
            $where = '';
            if ($filter == '1')
                $where = 'r.user_id = ' . $this->user_id . ' AND r.report_stage = "finished"';
            elseif ($filter == '2')
                $where = 'r.user_id = ' . $this->user_id . ' AND (r.report_stage != "finished" OR r.report_stage IS NULL)';
            elseif ($filter == '3')
            {
                $userTable = new Proposalgen_Model_DbTable_Users();
                $users = $userTable->fetchAll('dealer_company_id = ' . $this->dealer_company_id);
                foreach ( $users as $row )
                {
                    if (! empty($where))
                    {
                        $where .= ' OR ';
                    }
                    $where .= 'r.user_id = ' . $row ['user_id'];
                }
            }
            else
            {
                $where = 'r.user_id = ' . $this->user_id;
            }
            
            // select reports
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'r' => 'reports' 
            ))
                ->joinLeft(array (
                    'u' => 'users' 
            ), 'u.user_id = r.user_id', array (
                    'username' 
            ))
                ->where($where);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            if (count($result) > 0)
            {
                $i = 0;
                foreach ( $result as $row )
                {
                    $dateCreated = $this->convertDate($row ['date_created']);
                    $dateModified = $this->convertDate($row ['last_modified']);
                    $formdata->rows [$i] ['id'] = $row ['report_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['report_id'], 
                            $row ['username'], 
                            ucwords(strtolower($row ['customer_company_name'])), 
                            $dateCreated, 
                            $dateModified, 
                            ($row ['report_stage'] == "finished" ? "Yes" : "No"), 
                            null 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            echo $e->getMessage();
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function deleteReport ($report_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $db->beginTransaction();
        if ($report_id > 0)
        {
            try
            {
                // delete answers
                $textAnswersTable = new Proposalgen_Model_DbTable_TextAnswers();
                $numericAnswersTable = new Proposalgen_Model_DbTable_NumericAnswers();
                $dateAnswersTable = new Proposalgen_Model_DbTable_DateAnswers();
                
                // delete all answers for report
                $where = $numericAnswersTable->getAdapter()->quoteInto('report_id = ?', $report_id, 'INTEGER');
                $numericAnswersTable->delete($where);
                $where = $textAnswersTable->getAdapter()->quoteInto('report_id = ?', $report_id, 'INTEGER');
                $textAnswersTable->delete($where);
                $where = $dateAnswersTable->getAdapter()->quoteInto('report_id = ?', $report_id, 'INTEGER');
                $dateAnswersTable->delete($where);
                
                // delete any meters, device_instances, unknown_device_instances
                // and requests for report
                $metersTable = new Proposalgen_Model_DbTable_Meters();
                $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
                
                $where = $device_instanceTable->getAdapter()->quoteInto('report_id = ?', $report_id, 'INTEGER');
                $device_instances = $device_instanceTable->fetchAll($where);
                foreach ( $device_instances as $key => $value )
                {
                    $where = $metersTable->getAdapter()->quoteInto('device_instance_id = ?', $device_instances [$key] ['device_instance_id'], 'INTEGER');
                    $metersTable->delete($where);
                    
                    $where = $device_instanceTable->getAdapter()->quoteInto('device_instance_id = ?', $device_instances [$key] ['device_instance_id'], 'INTEGER');
                    $device_instanceTable->delete('device_instance_id = ' . $device_instances [$key] ['device_instance_id']);
                }
                
                // delete unknown_device_instances for report_id
                $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                $where = $unknown_device_instanceTable->getAdapter()->quoteInto('report_id = ?', $report_id, 'INTEGER');
                $unknown_device_instanceTable->delete($where);
                
                // delete any upload data
                $upload_data_collectorTable = new Proposalgen_Model_DbTable_UploadDataCollector();
                $where = $upload_data_collectorTable->getAdapter()->quoteInto('report_id = ?', $report_id, 'INTEGER');
                $upload_data_collectorTable->delete($where);
                
                // delete the report
                $reportsTable = new Proposalgen_Model_DbTable_Reports();
                $where = $reportsTable->getAdapter()->quoteInto('report_id = ?', $report_id, 'INTEGER');
                $reportsTable->delete($where);
                $db->commit();
                return 1;
            }
            catch ( Zend_Db_Exception $e )
            {
                $db->rollback();
                echo $e;
                die();
                throw new Exception("Unknown Database Error.", 0, $e);
                return 0;
            }
            catch ( Exception $e )
            {
                $db->rollback();
                echo $e;
                die();
                throw new Exception("Unknown Database Error.", 0, $e);
                return 0;
            }
        }
    }

    public function bulkdevicepricingAction ()
    {
        $this->view->title = "Update Pricing";
        $this->view->parts_list = array ();
        $this->view->device_list = array ();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // fill companies
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $dealer_companies = $dealer_companyTable->fetchAll('is_deleted = false', 'company_name');
        $this->view->company_list = $dealer_companies;
        
        // fill manufacturers dropdown
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers = $manufacturersTable->fetchAll('is_deleted = false', 'manufacturer_name');
        $this->view->manufacturer_list = $manufacturers;
        
        // get default prices
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
        $dealer_company = $dealer_companyTable->fetchRow($where);
        
        if (count($dealer_company) > 0)
        {
            $this->view->default_price = money_format('%i', $dealer_company ['dc_default_printer_cost']);
            $this->view->default_service = money_format('%.4n', $dealer_company ['dc_service_cost_per_page']);
        }
        
        if ($this->_request->isPost())
        {
            $summary = "";
            $passvalid = 0;
            $formData = $this->_request->getPost();
            
            // check post back for update
            $db->beginTransaction();
            try
            {
                // return current dropdown states
                $this->view->company_filter = $formData ['company_filter'];
                $this->view->pricing_filter = $formData ['pricing_filter'];
                $this->view->search_filter = $formData ['criteria_filter'];
                $this->view->search_criteria = $formData ['txtCriteria'];
                $this->view->repop_page = $formData ["hdnPage"];
                
                if ($formData ['hdnMode'] == "update")
                {
                    // $dealer_company_id = $formData ['company_filter'];
                    $dealer_company_id = 1;
                    
                    if ($dealer_company_id > 1)
                    {
                        // Save Dealer Company Overrides
                        if ($formData ['pricing_filter'] == 'toner')
                        {
                            foreach ( $formData as $key => $value )
                            {
                                if (strstr($key, "txtDealerTonerPrice"))
                                {
                                    $toner_id = str_replace("txtDealerTonerPrice", "", $key);
                                    
                                    // check if new price is populated.
                                    if ($formData ['txtDealerTonerPrice' . $toner_id] != $formData ['hdnDealerTonerPrice' . $toner_id])
                                    {
                                        $dealer_toner_overrideTable = new Proposalgen_Model_DbTable_DealerTonerOverride();
                                        $where = $dealer_toner_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $dealer_company_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                                        $price = $formData ['txtDealerTonerPrice' . $toner_id];
                                        
                                        // delete entry if blanked out
                                        if ($price != '' && ! is_numeric($price))
                                        {
                                            $passvalid = 1;
                                            $this->view->message = "Value must be numeric. Please correct it and try again.";
                                            break;
                                        }
                                        else if ($price == "0")
                                        {
                                            $dealer_toner_overrideTable->delete($where);
                                        }
                                        else if ($price > 0)
                                        {
                                            $dealer_toner_overrideData = array (
                                                    'dealer_company_id' => $dealer_company_id, 
                                                    'toner_id' => $toner_id, 
                                                    'override_toner_price' => $price 
                                            );
                                            
                                            // check to see if device override
                                            // exists
                                            $dealer_toner_override = $dealer_toner_overrideTable->fetchRow($where);
                                            
                                            if (count($dealer_toner_override) > 0)
                                            {
                                                $dealer_toner_overrideTable->update($dealer_toner_overrideData, $where);
                                                $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['override_toner_price'] . ' to ' . $price . '<br />';
                                            }
                                            else
                                            {
                                                $dealer_toner_overrideTable->insert($dealer_toner_overrideData);
                                                $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['toner_price'] . ' to ' . $price . '<br />';
                                            }
                                        }
                                    }
                                }
                            }
                            
                            if ($passvalid == 0)
                            {
                                $this->view->message = "<p>The toner pricing updates have been applied successfully.</p>";
                            }
                        }
                        else
                        {
                            foreach ( $formData as $key => $value )
                            {
                                if (strstr($key, "txtDealerDevicePrice"))
                                {
                                    $master_device_id = str_replace("txtDealerDevicePrice", "", $key);
                                    
                                    // check if new price is populated.
                                    if ($formData ['txtDealerDevicePrice' . $master_device_id] != $formData ['hdnDealerDevicePrice' . $master_device_id])
                                    {
                                        $dealer_device_overrideTable = new Proposalgen_Model_DbTable_DealerDeviceOverride();
                                        $where = $dealer_device_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $dealer_company_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                        $price = $formData ['txtDealerDevicePrice' . $master_device_id];
                                        
                                        // delete entry if blanked out
                                        if ($price != '' && ! is_numeric($price))
                                        {
                                            $passvalid = 1;
                                            $this->view->message = "Value must be numeric. Please correct it and try again.";
                                            break;
                                        }
                                        else if ($price == "0")
                                        {
                                            $dealer_device_overrideTable->delete($where);
                                        }
                                        else if ($price > 0)
                                        {
                                            $dealer_device_overrideData = array (
                                                    'dealer_company_id' => $dealer_company_id, 
                                                    'master_device_id' => $master_device_id, 
                                                    'override_device_price' => $price 
                                            );
                                            
                                            // check to see if device override
                                            // exists
                                            $dealer_device_override = $dealer_device_overrideTable->fetchRow($where);
                                            
                                            if (count($dealer_device_override) > 0)
                                            {
                                                $dealer_device_overrideTable->update($dealer_device_overrideData, $where);
                                                $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['override_device_price'] . ' to ' . $price . '<br />';
                                            }
                                            else
                                            {
                                                $dealer_device_overrideTable->insert($dealer_device_overrideData);
                                                $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['device_price'] . ' to ' . $price . '<br />';
                                            }
                                        }
                                    }
                                }
                            }
                            
                            if ($passvalid == 0)
                            {
                                $this->view->message = "<p>The printer pricing updates have been applied successfully.</p>";
                            }
                        }
                    }
                    else
                    {
                        // Save Master Company Pricing Changes
                        if ($formData ['pricing_filter'] == 'toner')
                        {
                            // loop through $result
                            foreach ( $formData as $key => $value )
                            {
                                if (strstr($key, "txtTonerPrice"))
                                {
                                    $toner_id = str_replace("txtTonerPrice", "", $key);
                                    $price = $formData ['txtTonerPrice' . $toner_id];
                                    
                                    // check if new price is populated.
                                    if ($price == "0")
                                    {
                                        $passvalid = 1;
                                        $this->view->message = "All values must be greater than 0. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price != '' && ! is_numeric($price))
                                    {
                                        $passvalid = 1;
                                        $this->view->message = "All values must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price != '')
                                    {
                                        $tonerTable = new Proposalgen_Model_DbTable_Toner();
                                        $tonerData = array (
                                                'toner_price' => $price 
                                        );
                                        $where = $tonerTable->getAdapter()->quoteInto('toner_id = ?', $toner_id, 'INTEGER');
                                        $tonerTable->update($tonerData, $where);
                                        $summary .= "Updated part from " . $key ['toner_price'] . " to " . $price . "<br />";
                                    }
                                }
                            }
                            
                            if ($passvalid == 0)
                            {
                                $this->view->message = "<p>The toner pricing updates have been applied successfully.</p>";
                            }
                            else
                            {
                                $db->rollBack();
                                
                                // build repop values
                                $repop_array = '';
                                foreach ( $formData as $key => $value )
                                {
                                    if (strstr($key, "txtTonerPrice"))
                                    {
                                        $toner_id = str_replace("txtTonerPrice", "", $key);
                                        $price = $formData ['txtTonerPrice' . $toner_id];
                                        
                                        // build repop array
                                        if ($repop_array != '')
                                        {
                                            $repop_array .= ',';
                                        }
                                        $repop_array .= $toner_id . ':' . $price;
                                    }
                                }
                                $this->view->repop_array = $repop_array;
                            }
                        }
                        else
                        {
                            foreach ( $formData as $key => $value )
                            {
                                if (strstr($key, "txtDevicePrice"))
                                {
                                    $master_device_id = str_replace("txtDevicePrice", "", $key);
                                    $price = $formData ['txtDevicePrice' . $master_device_id];
                                    
                                    // check if new price is populated.
                                    if ($price != '' && ! is_numeric($price))
                                    {
                                        $passvalid = 1;
                                        $this->view->message = "All values must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price != '')
                                    {
                                        if ($price == 0)
                                        {
                                            $price = null;
                                        }
                                        $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();
                                        
                                        if ($formData ['pricing_filter'] == 'labor')
                                        {
                                            $master_deviceData = array (
                                                    'labor_cost_per_page' => $price 
                                            );
                                        }
                                        else if ($formData ['pricing_filter'] == 'parts')
                                        {
                                            $master_deviceData = array (
                                                    'parts_cost_per_page' => $price 
                                            );
                                        }
                                        else
                                        {
                                            $master_deviceData = array (
                                                    'device_price' => $price 
                                            );
                                        }
                                        
                                        $where = $master_deviceTable->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                                        $master_deviceTable->update($master_deviceData, $where);
                                        $summary .= "Updated " . $key ['manufacturer_name'] . ' ' . $key ['printer_model'] . ' from ' . $key ['device_price'] . ' to ' . $price . '<br />';
                                    }
                                }
                            }
                            
                            if ($passvalid == 0)
                            {
                                if (! empty($summary))
                                {
                                    $this->view->message = "<p>The device pricing updates have been applied successfully.</p>";
                                }
                                else
                                {
                                    $this->view->message = "<p>You have not updated any pricing. Please enter a new price and try again.</p>";
                                }
                            }
                            else
                            {
                                $db->rollBack();
                                
                                // build repop values
                                $repop_array = '';
                                foreach ( $formData as $key => $value )
                                {
                                    if (strstr($key, "txtDevicePrice"))
                                    {
                                        $master_device_id = str_replace("txtDevicePrice", "", $key);
                                        $price = $formData ['txtDevicePrice' . $master_device_id];
                                        
                                        // build repop array
                                        if ($repop_array != '')
                                        {
                                            $repop_array .= ',';
                                        }
                                        $repop_array .= $master_device_id . ':' . $price;
                                    }
                                }
                                $this->view->repop_array = $repop_array;
                            }
                        }
                    }
                }
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->view->message = "Error: The updates were not saved.";
            }
        }
    }

    public function bulkpartspricingAction ()
    {
        $this->view->title = "Bulk Toner Pricing Update";
        $this->view->parts_list = array ();
        $db = Zend_Db_Table::getDefaultAdapter();
        $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();
        
        // fill manufacturers dropdown
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers = $manufacturersTable->fetchAll('is_deleted = false', 'manufacturer_name');
        $this->view->manufacturer_list = $manufacturers;
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                't' => 'toner' 
        ))
            ->joinLeft(array (
                'tm' => 'manufacturer' 
        ), 'tm.manufacturer_id = t.manufacturer_id')
            ->joinLeft(array (
                'dt' => 'device_toner' 
        ), 'dt.toner_id = t.toner_id')
            ->joinLeft(array (
                'md' => 'master_device' 
        ), 'md.master_device_id = dt.master_device_id')
            ->joinLeft(array (
                'mm' => 'manufacturer' 
        ), 'mm.manufacturer_id = md.mastdevice_manufacturer')
            ->joinLeft(array (
                'tc' => 'toner_color' 
        ), 'tc.toner_color_id = t.toner_color_id')
            ->group('t.toner_id')
            ->order(array (
                'mm.manufacturer_name', 
                't.toner_SKU' 
        ));
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        
        // fill devices_array
        $devices_array = array ();
        foreach ( $result as $key )
        {
            $toner_devices = $db->select()
                ->from(array (
                    'md' => 'master_device' 
            ))
                ->joinLeft(array (
                    'dt' => 'device_toner' 
            ), 'dt.master_device_id = md.master_device_id')
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer')
                ->where('dt.toner_id = ?', $key ['toner_id'], 'INTEGER')
                ->order(array (
                    'manufacturer_name', 
                    'printer_model' 
            ));
            $stmt = $db->query($toner_devices);
            $toner_devices_list = $stmt->fetchAll();
            
            foreach ( $toner_devices_list as $key2 )
            {
                if ($key ['toner_id'] == $key2 ['toner_id'])
                {
                    $value = ucwords(strtolower($key2 ['manufacturer_name'] . ' ' . $key2 ['printer_model']));
                    if (isset($devices_array [$key ['toner_id']]) == true)
                    {
                        $devices_array [$key ['toner_id']] = $devices_array [$key ['toner_id']] . ";" . $value;
                    }
                    else
                    {
                        $devices_array [$key ['toner_id']] = $value;
                    }
                }
            }
        }
        $this->view->devices = $devices_array;
        
        if ($this->_request->isPost())
        {
            $summary = "";
            $passvalid = 0;
            $formData = $this->_request->getPost();
            
            // check post back for update
            $db->beginTransaction();
            try
            {
                if ($formData ['hdnMode'] == "update")
                {
                    // loop through $result
                    foreach ( $formData as $key => $value )
                    {
                        if (strstr($key, "txtPrice"))
                        {
                            $toner_id = str_replace("txtPrice", "", $key);
                            $price = $formData ['txtPrice' . $toner_id];
                            
                            // check if new price is populated.
                            if ($price != '' && ! is_numeric($price))
                            {
                                $passvalid = 1;
                                $this->view->message = "Value must be numeric. Please correct it and try again.";
                                break;
                            }
                            else if ($price != '')
                            {
                                if ($price == 0)
                                {
                                    $price = null;
                                }
                                $tonerTable = new Proposalgen_Model_DbTable_Toner();
                                $tonerData = array (
                                        'toner_price' => $price 
                                );
                                $where = $tonerTable->getAdapter()->quoteInto('toner_id = ?', $toner_id, 'INTEGER');
                                $tonerTable->update($tonerData, $where);
                                $summary .= "Updated part from " . $key ['toner_price'] . " to " . $price . "<br />";
                            }
                        }
                    }
                    
                    if ($passvalid == 0)
                    {
                        $this->view->message = "<p>The toner pricing updates have been applied successfully.</p>";
                    }
                    $this->view->manufacturer_id = $formData ['manufacturer_filter'];
                }
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->view->message = "Error: The updates were not saved.";
            }
        }
        
        // send results to screen to populate grid
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        if (count($result) > 0)
        {
            $this->view->parts_list = $result;
        }
    }

    public function bulkdealerdevicepricingAction ()
    {
        $this->view->title = "Update Company Pricing";
        $this->view->device_list = array ();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // fill manufacturers dropdown
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers = $manufacturersTable->fetchAll('is_deleted = false', 'manufacturer_name');
        $this->view->manufacturer_list = $manufacturers;
        
        // get master company default prices
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', 1, 'INTEGER');
        $dealer_company = $dealer_companyTable->fetchRow($where);
        
        if (count($dealer_company) > 0)
        {
            $this->view->default_price = money_format('%i', $dealer_company ['dc_default_printer_cost']);
            $this->view->default_service = money_format('%.4n', $dealer_company ['dc_service_cost_per_page']);
        }
        
        // get dealer company default prices
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
        $dealer_company = $dealer_companyTable->fetchRow($where);
        
        // override master prices if dealers exist
        if (count($dealer_company) > 0)
        {
            if ($dealer_company ['dc_default_printer_cost'])
            {
                $this->view->default_price = money_format('%i', $dealer_company ['dc_default_printer_cost']);
            }
            if ($dealer_company ['dc_service_cost_per_page'])
            {
                $this->view->default_service = money_format('%.4n', $dealer_company ['dc_service_cost_per_page']);
            }
        }
        
        if ($this->_request->isPost())
        {
            $summary = "";
            $passvalid = 0;
            $formData = $this->_request->getPost();
            
            // check post back for update
            $db->beginTransaction();
            try
            {
                // return current dropdown states
                $this->view->pricing_filter = $formData ['pricing_filter'];
                $this->view->search_filter = $formData ['criteria_filter'];
                $this->view->search_criteria = $formData ['txtCriteria'];
                
                if ($formData ['hdnMode'] == "update")
                {
                    if ($formData ['pricing_filter'] == 'toner')
                    {
                        foreach ( $formData as $key => $value )
                        {
                            if (strstr($key, "txtTonerPrice"))
                            {
                                $toner_id = str_replace("txtTonerPrice", "", $key);
                                
                                // check if new price is populated.
                                if ($formData ['txtTonerPrice' . $toner_id] != $formData ['hdnTonerPrice' . $toner_id])
                                {
                                    $dealer_toner_overrideTable = new Proposalgen_Model_DbTable_DealerTonerOverride();
                                    $where = $dealer_toner_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $this->dealer_company_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                                    $price = $formData ['txtTonerPrice' . $toner_id];
                                    
                                    // delete entry if blanked out
                                    if ($price != '' && ! is_numeric($price))
                                    {
                                        $passvalid = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {
                                        $dealer_toner_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {
                                        $dealer_toner_overrideData = array (
                                                'dealer_company_id' => $this->dealer_company_id, 
                                                'toner_id' => $toner_id, 
                                                'override_toner_price' => $price 
                                        );
                                        
                                        // check to see if device override
                                        // exists
                                        $dealer_toner_override = $dealer_toner_overrideTable->fetchRow($where);
                                        
                                        if (count($dealer_toner_override) > 0)
                                        {
                                            $dealer_toner_overrideTable->update($dealer_toner_overrideData, $where);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['override_toner_price'] . ' to ' . $price . '<br />';
                                        }
                                        else
                                        {
                                            $dealer_toner_overrideTable->insert($dealer_toner_overrideData);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['toner_price'] . ' to ' . $price . '<br />';
                                        }
                                    }
                                }
                            }
                        }
                        
                        if ($passvalid == 0)
                        {
                            $this->view->message = "<p>The toner pricing updates have been applied successfully.</p>";
                        }
                    }
                    elseif ($formData ['pricing_filter'] == 'labor')
                    {
                        // loop through $result
                        foreach ( $formData as $key => $value )
                        {
                            if (strstr($key, "txtDevicePrice"))
                            {
                                $master_device_id = str_replace("txtDevicePrice", "", $key);
                                
                                // check if new price is populated.
                                if ($formData ['txtDevicePrice' . $master_device_id] != $formData ['hdnDevicePrice' . $master_device_id])
                                {
                                    $dealer_laborCPP_overrideTable = new Proposalgen_Model_DbTable_DealerLaborCPPOverride();
                                    $where = $dealer_laborCPP_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $this->dealer_company_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                    $price = $formData ['txtDevicePrice' . $master_device_id];
                                    
                                    // delete entry if blanked out
                                    if ($price != '' && ! is_numeric($price))
                                    {
                                        $passvalid = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {
                                        $dealer_laborCPP_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {
                                        $dealer_device_overrideData = array (
                                                'dealer_company_id' => $this->dealer_company_id, 
                                                'master_device_id' => $master_device_id, 
                                                'override_labor_CPP' => $price 
                                        );
                                        
                                        // check to see if device override
                                        // exists
                                        $dealer_device_override = $dealer_laborCPP_overrideTable->fetchRow($where);
                                        
                                        if (count($dealer_device_override) > 0)
                                        {
                                            $dealer_laborCPP_overrideTable->update($dealer_device_overrideData, $where);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['override_labor_CPP'] . ' to ' . $price . '<br />';
                                        }
                                        else
                                        {
                                            $dealer_laborCPP_overrideTable->insert($dealer_device_overrideData);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['labor_cost_per_page'] . ' to ' . $price . '<br />';
                                        }
                                    }
                                }
                            }
                        }
                        
                        if ($passvalid == 0)
                        {
                            $this->view->message = "<p>The printer pricing updates have been applied successfully.</p>";
                        }
                    }
                    elseif ($formData ['pricing_filter'] == 'parts')
                    {
                        // loop through $result
                        foreach ( $formData as $key => $value )
                        {
                            if (strstr($key, "txtDevicePrice"))
                            {
                                $master_device_id = str_replace("txtDevicePrice", "", $key);
                                
                                // check if new price is populated.
                                if ($formData ['txtDevicePrice' . $master_device_id] != $formData ['hdnDevicePrice' . $master_device_id])
                                {
                                    $dealer_partsCPP_overrideTable = new Proposalgen_Model_DbTable_DealerPartsCPPOverride();
                                    $where = $dealer_partsCPP_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $this->dealer_company_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                    $price = $formData ['txtDevicePrice' . $master_device_id];
                                    
                                    // delete entry if blanked out
                                    if ($price != '' && ! is_numeric($price))
                                    {
                                        $passvalid = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {
                                        $dealer_partsCPP_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {
                                        $dealer_device_overrideData = array (
                                                'dealer_company_id' => $this->dealer_company_id, 
                                                'master_device_id' => $master_device_id, 
                                                'override_parts_CPP' => $price 
                                        );
                                        
                                        // check to see if device override
                                        // exists
                                        $dealer_device_override = $dealer_partsCPP_overrideTable->fetchRow($where);
                                        
                                        if (count($dealer_device_override) > 0)
                                        {
                                            $dealer_partsCPP_overrideTable->update($dealer_device_overrideData, $where);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['override_parts_CPP'] . ' to ' . $price . '<br />';
                                        }
                                        else
                                        {
                                            $dealer_partsCPP_overrideTable->insert($dealer_device_overrideData);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['parts_cost_per_page'] . ' to ' . $price . '<br />';
                                        }
                                    }
                                }
                            }
                        }
                        
                        if ($passvalid == 0)
                        {
                            $this->view->message = "<p>The printer pricing updates have been applied successfully.</p>";
                        }
                    }
                    else
                    {
                        // loop through $result
                        foreach ( $formData as $key => $value )
                        {
                            if (strstr($key, "txtDevicePrice"))
                            {
                                $master_device_id = str_replace("txtDevicePrice", "", $key);
                                
                                // check if new price is populated.
                                if ($formData ['txtDevicePrice' . $master_device_id] != $formData ['hdnDevicePrice' . $master_device_id])
                                {
                                    $dealer_device_overrideTable = new Proposalgen_Model_DbTable_DealerDeviceOverride();
                                    $where = $dealer_device_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $this->dealer_company_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                    $price = $formData ['txtDevicePrice' . $master_device_id];
                                    
                                    // delete entry if blanked out
                                    if ($price != '' && ! is_numeric($price))
                                    {
                                        $passvalid = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {
                                        $dealer_device_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {
                                        $dealer_device_overrideData = array (
                                                'dealer_company_id' => $this->dealer_company_id, 
                                                'master_device_id' => $master_device_id, 
                                                'override_device_price' => $price 
                                        );
                                        
                                        // check to see if device override
                                        // exists
                                        $dealer_device_override = $dealer_device_overrideTable->fetchRow($where);
                                        
                                        if (count($dealer_device_override) > 0)
                                        {
                                            $dealer_device_overrideTable->update($dealer_device_overrideData, $where);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['override_device_price'] . ' to ' . $price . '<br />';
                                        }
                                        else
                                        {
                                            $dealer_device_overrideTable->insert($dealer_device_overrideData);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['device_price'] . ' to ' . $price . '<br />';
                                        }
                                    }
                                }
                            }
                        }
                        
                        if ($passvalid == 0)
                        {
                            $this->view->message = "<p>The printer pricing updates have been applied successfully.</p>";
                        }
                    }
                }
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->view->message = "Error: The updates were not saved.";
            }
        }
    }

    public function bulkdealerpartspricingAction ()
    {
        $this->view->title = "Bulk Toner Pricing Update";
        $this->view->parts_list = array ();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // fill manufacturers dropdown
        $list_where = "";
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers = $manufacturersTable->fetchAll('is_deleted = false', 'manufacturer_name');
        $this->view->manufacturer_list = $manufacturers;
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                't' => 'toner' 
        ))
            ->joinLeft(array (
                'tm' => 'manufacturer' 
        ), 'tm.manufacturer_id = t.manufacturer_id', array (
                'manufacturer_name' 
        ))
            ->joinLeft(array (
                'dt' => 'device_toner' 
        ), 'dt.toner_id = t.toner_id')
            ->joinLeft(array (
                'md' => 'master_device' 
        ), 'md.master_device_id = dt.master_device_id')
            ->joinLeft(array (
                'tc' => 'toner_color' 
        ), 'tc.toner_color_id = t.toner_color_id')
            ->joinLeft(array (
                'dto' => 'dealer_toner_override' 
        ), 'dto.toner_id = t.toner_id AND dto.dealer_company_id = ' . $this->dealer_company_id, array (
                'override_toner_price' 
        ))
            ->group('t.toner_id')
            ->order(array (
                'tm.manufacturer_name', 
                't.toner_SKU' 
        ));
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        
        // fill devices_array
        $devices_array = array ();
        foreach ( $result as $key )
        {
            $toner_devices = $db->select()
                ->from(array (
                    'md' => 'master_device' 
            ))
                ->joinLeft(array (
                    'dt' => 'device_toner' 
            ), 'dt.master_device_id = md.master_device_id')
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer')
                ->where('dt.toner_id = ?', $key ['toner_id'], 'INTEGER')
                ->order(array (
                    'manufacturer_name', 
                    'printer_model' 
            ));
            $stmt = $db->query($toner_devices);
            $toner_devices_list = $stmt->fetchAll();
            
            foreach ( $toner_devices_list as $key2 )
            {
                if ($key ['toner_id'] == $key2 ['toner_id'])
                {
                    $value = ucwords(strtolower($key2 ['manufacturer_name'] . ' ' . $key2 ['printer_model']));
                    if (isset($devices_array [$key ['toner_id']]) == true)
                    {
                        $devices_array [$key ['toner_id']] = $devices_array [$key ['toner_id']] . "<br />" . $value;
                    }
                    else
                    {
                        $devices_array [$key ['toner_id']] = $value;
                    }
                }
            }
        }
        $this->view->devices = $devices_array;
        
        if ($this->_request->isPost())
        {
            $summary = "";
            $passvalid = 0;
            $formData = $this->_request->getPost();
            $dealer_toner_overrideTable = new Proposalgen_Model_DbTable_DealerTonerOverride();
            
            // check post back for update
            $db->beginTransaction();
            try
            {
                if ($formData ['hdnMode'] == "update")
                {
                    
                    foreach ( $formData as $key => $value )
                    {
                        if (strstr($key, "txtPrice"))
                        {
                            $toner_id = str_replace("txtPrice", "", $key);
                            
                            // check if new price is populated.
                            if ($formData ['txtPrice' . $toner_id] != $formData ['hdnPrice' . $toner_id])
                            {
                                $where = $dealer_toner_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $this->dealer_company_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                                $price = $formData ['txtPrice' . $toner_id];
                                
                                // delete entry if blanked out
                                if ($price != '' && ! is_numeric($price))
                                {
                                    $passvalid = 1;
                                    $this->view->message = "Value must be numeric. Please correct it and try again.";
                                    break;
                                }
                                else if ($price == "0")
                                {
                                    $dealer_toner_overrideTable->delete($where);
                                }
                                else if ($price > 0)
                                {
                                    $dealer_toner_overrideData = array (
                                            'dealer_company_id' => $this->dealer_company_id, 
                                            'toner_id' => $toner_id, 
                                            'override_toner_price' => $price 
                                    );
                                    
                                    // check to see if device override exists
                                    $dealer_toner_override = $dealer_toner_overrideTable->fetchRow($where);
                                    
                                    if (count($dealer_toner_override) > 0)
                                    {
                                        $dealer_toner_overrideTable->update($dealer_toner_overrideData, $where);
                                        $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['override_toner_price'] . ' to ' . $price . '<br />';
                                    }
                                    else
                                    {
                                        $dealer_toner_overrideTable->insert($dealer_toner_overrideData);
                                        $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['toner_price'] . ' to ' . $price . '<br />';
                                    }
                                }
                            }
                        }
                    }
                    
                    if ($passvalid == 0)
                    {
                        $this->view->message = "<p>The toner pricing updates have been applied successfully.</p>";
                    }
                    $this->view->manufacturer_id = $formData ['manufacturer_filter'];
                }
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->view->message = "Error: The updates were not saved.";
            }
        }
        
        // send results to screen to populate grid
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        if (count($result) > 0)
        {
            $this->view->parts_list = $result;
        }
    }

    public function bulkuserpricingAction ()
    {
        $this->view->title = "Update My Pricing";
        $this->view->device_list = array ();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // fill manufacturers dropdown
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers = $manufacturersTable->fetchAll('is_deleted = false', 'manufacturer_name');
        $this->view->manufacturer_list = $manufacturers;
        
        // get master company default prices
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', 1, 'INTEGER');
        $dealer_company = $dealer_companyTable->fetchRow($where);
        if (count($dealer_company) > 0)
        {
            $this->view->default_price = money_format('%i', $dealer_company ['dc_default_printer_cost']);
            $this->view->default_service = money_format('%.4n', $dealer_company ['dc_service_cost_per_page']);
        }
        
        // override master prices if dealers exist
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
        $dealer_company = $dealer_companyTable->fetchRow($where);
        if (count($dealer_company) > 0)
        {
            if ($dealer_company ['dc_default_printer_cost'])
            {
                $this->view->default_price = money_format('%i', $dealer_company ['dc_default_printer_cost']);
            }
            if ($dealer_company ['dc_service_cost_per_page'])
            {
                $this->view->default_parts = money_format('%.4n', $dealer_company ['dc_service_cost_per_page']);
            }
        }
        
        if ($this->_request->isPost())
        {
            $summary = "";
            $passvalid = 0;
            $formData = $this->_request->getPost();
            
            // check post back for update
            $db->beginTransaction();
            try
            {
                // return current dropdown states
                $this->view->pricing_filter = $formData ['pricing_filter'];
                $this->view->search_filter = $formData ['criteria_filter'];
                $this->view->search_criteria = $formData ['txtCriteria'];
                $this->view->repop_page = $formData ["hdnPage"];
                
                if ($formData ['hdnMode'] == "update")
                {
                    if ($formData ['pricing_filter'] == 'toner')
                    {
                        foreach ( $formData as $key => $value )
                        {
                            if (strstr($key, "txtTonerPrice"))
                            {
                                $toner_id = str_replace("txtTonerPrice", "", $key);
                                
                                // check if new price is populated.
                                if ($formData ['txtTonerPrice' . $toner_id] != $formData ['hdnTonerPrice' . $toner_id])
                                {
                                    $user_toner_overrideTable = new Proposalgen_Model_DbTable_UserTonerOverride();
                                    $where = $user_toner_overrideTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                                    $price = $formData ['txtTonerPrice' . $toner_id];
                                    
                                    // delete entry if blanked out
                                    if ($price != '' && ! is_numeric($price))
                                    {
                                        $passvalid = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {
                                        $user_toner_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {
                                        $user_toner_overrideData = array (
                                                'user_id' => $this->user_id, 
                                                'toner_id' => $toner_id, 
                                                'override_toner_price' => $price 
                                        );
                                        
                                        // check to see if device override
                                        // exists
                                        $user_toner_override = $user_toner_overrideTable->fetchRow($where);
                                        
                                        if (count($user_toner_override) > 0)
                                        {
                                            $user_toner_overrideTable->update($user_toner_overrideData, $where);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['override_toner_price'] . ' to ' . $price . '<br />';
                                        }
                                        else
                                        {
                                            $user_toner_overrideTable->insert($user_toner_overrideData);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['toner_price'] . ' to ' . $price . '<br />';
                                        }
                                    }
                                }
                            }
                        }
                        
                        if ($passvalid == 0)
                        {
                            $this->view->message = "<p>The toner pricing updates have been applied successfully.</p>";
                        }
                        else
                        {
                            $db->rollBack();
                            
                            // build repop values
                            $repop_array = '';
                            foreach ( $formData as $key => $value )
                            {
                                if (strstr($key, "txtTonerPrice"))
                                {
                                    $toner_id = str_replace("txtTonerPrice", "", $key);
                                    $price = $formData ['txtTonerPrice' . $toner_id];
                                    
                                    // build repop array
                                    if ($repop_array != '')
                                    {
                                        $repop_array .= ',';
                                    }
                                    $repop_array .= $toner_id . ':' . $price;
                                }
                            }
                            $this->view->repop_array = $repop_array;
                        }
                    }
                    else
                    {
                        // loop through $result
                        foreach ( $formData as $key => $value )
                        {
                            if (strstr($key, "txtDevicePrice"))
                            {
                                $master_device_id = str_replace("txtDevicePrice", "", $key);
                                
                                // check if new price is populated.
                                if ($formData ['txtDevicePrice' . $master_device_id] != $formData ['hdnDevicePrice' . $master_device_id])
                                {
                                    $user_device_overrideTable = new Proposalgen_Model_DbTable_UserDeviceOverride();
                                    $where = $user_device_overrideTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                    $price = $formData ['txtDevicePrice' . $master_device_id];
                                    
                                    // delete entry if blanked out
                                    if ($price != '' && ! is_numeric($price))
                                    {
                                        $passvalid = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {
                                        $user_device_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {
                                        $user_device_overrideData = array (
                                                'user_id' => $this->user_id, 
                                                'master_device_id' => $master_device_id, 
                                                'override_device_price' => $price 
                                        );
                                        
                                        // check to see if device override
                                        // exists
                                        $user_device_override = $user_device_overrideTable->fetchRow($where);
                                        
                                        if (count($user_device_override) > 0)
                                        {
                                            $user_device_overrideTable->update($user_device_overrideData, $where);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['override_device_price'] . ' to ' . $price . '<br />';
                                        }
                                        else
                                        {
                                            $user_device_overrideTable->insert($user_device_overrideData);
                                            $summary .= "Updated " . ucwords(strtolower($key ['manufacturer_name'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['device_price'] . ' to ' . $price . '<br />';
                                        }
                                    }
                                }
                            }
                        }
                        
                        if ($passvalid == 0)
                        {
                            $this->view->message = "<p>The printer pricing updates have been applied successfully.</p>";
                        }
                        else
                        {
                            $db->rollBack();
                            
                            // build repop values
                            $repop_array = '';
                            foreach ( $formData as $key => $value )
                            {
                                if (strstr($key, "txtDevicePrice"))
                                {
                                    $master_device_id = str_replace("txtDevicePrice", "", $key);
                                    $price = $formData ['txtDevicePrice' . $master_device_id];
                                    
                                    // build repop array
                                    if ($repop_array != '')
                                    {
                                        $repop_array .= ',';
                                    }
                                    $repop_array .= $master_device_id . ':' . $price;
                                }
                            }
                            $this->view->repop_array = $repop_array;
                        }
                    }
                }
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->view->message = "Error: The updates were not saved.";
            }
        }
    }

    public function bulkfilepricingAction ()
    {
        $this->view->title = "Import & Export Pricing";
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // fill companies
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $dealer_companies = $dealer_companyTable->fetchAll('is_deleted = false', 'company_name');
        $this->view->company_list = $dealer_companies;
        
        // find company name
        $this->view->company_filter = $this->dealer_company_id;
        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
        $dealer_company = $dealer_companyTable->fetchRow($where);
        if (count($dealer_company) > 0)
        {
            $this->view->company_name = $dealer_company ['company_name'];
        }
        
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            
            // get company
            if (isset($formData ['company_filter']))
            {
                $company = $formData ['company_filter'];
            }
            else
            {
                $company = $this->dealer_company_id;
            }
            // for OD default to always master
            $company = 1;
            $this->view->company_filter = $company;
            
            // hdnRole is used when logged in as a dealer to differenciate
            // between if the dealer is on "update company pricing" or "update
            // my pricing"
            $hdnRole = $formData ['hdnRole'];
            
            if (isset($formData ['hdnMode']))
            {
                // ************************************************************/
                // * Initial Page Load
                // ************************************************************/
            }
            else if ($formData ['hdnAction'] == "import")
            {
                // ************************************************************/
                // * Save Imported File To Database
                // ************************************************************/
                

                // get arrays from indexAction
                $headers = new Zend_Session_Namespace('import_headers_array');
                $results = new Zend_Session_Namespace('import_results_array');
                
                $db->beginTransaction();
                try
                {
                    // detect file type (printers or toners)
                    $import_type = "printer";
                    foreach ( $headers->array as $key => $value )
                    {
                        if (strtolower($value) == "toner id")
                        {
                            $import_type = "toner";
                            break;
                        }
                    }
                    
                    // loop through file and save
                    foreach ( $results->array as $key => $value )
                    {
                        $exists = false;
                        $insert = false;
                        $update = false;
                        $delete = false;
                        
                        // update records
                        if ($import_type == 'printer')
                        {
                            // 0=master_device_id; 1=manufacturer_name;
                            // 2=printer_model; 3=device_price;
                            // 4=parts_cost_per_page; 5=labor_cost_per_page
                            if (in_array("System Admin", $this->privilege) && $company == 1)
                            {
                                $master_device_id = $results->array [$key] ['Master Printer ID'];
                                $manufacturer_name = $results->array [$key] ['Manufacturer'];
                                $printer_model = $results->array [$key] ['Printer Model'];
                                $device_price = $results->array [$key] ['New Price'];
                                
                                $table = new Proposalgen_Model_DbTable_MasterDevice();
                                $data = array (
                                        'device_price' => $device_price 
                                );
                                $where = $table->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                                
                                // check to see if it exists - no inserts in the
                                // Master Tables
                                $check = $table->fetchRow($where);
                                if (count($check) > 0)
                                {
                                    $exists = true;
                                    
                                    // don't allow price of 0
                                    if ($device_price == 0)
                                    {
                                        $device_price = null;
                                    }
                                    
                                    // don't update if values match
                                    if ($check ['device_price'] != $device_price)
                                    {
                                        $update = true;
                                    }
                                }
                            }
                            else if ($hdnRole != "user" && (! in_array("Standard User", $this->privilege) && $company > 1))
                            {
                                $master_device_id = $results->array [$key] ['Master Printer Id'];
                                $manufacturer_name = $results->array [$key] ['Manufacturer'];
                                $printer_model = $results->array [$key] ['Printer Model'];
                                $device_price = $results->array [$key] ['New Override Price'];
                                
                                $table = new Proposalgen_Model_DbTable_DealerDeviceOverride();
                                $data = array (
                                        'override_device_price' => $device_price 
                                );
                                $where = $table->getAdapter()->quoteInto('dealer_company_id = ' . $company . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                
                                // check to see if it exists
                                $select = new Zend_Db_Select($db);
                                $select = $db->select()
                                    ->from(array (
                                        'md' => 'master_device' 
                                ), array (
                                        'device_price' 
                                ))
                                    ->joinLeft(array (
                                        'ddo' => 'dealer_device_override' 
                                ), 'ddo.master_device_id = md.master_device_id AND ddo.dealer_company_id = ' . $company, array (
                                        'override_device_price' 
                                ))
                                    ->where('md.master_device_id = ' . $master_device_id);
                                $stmt = $db->query($select);
                                $check = $stmt->fetchAll();
                                
                                if (count($check) > 0)
                                {
                                    if ($check [0] ['override_device_price'] > 0)
                                    {
                                        $exists = true;
                                        
                                        // don't update if values match
                                        if ($device_price == 0 || empty($device_price))
                                        {
                                            $delete = true;
                                        }
                                        else if ($check [0] ['device_price'] != $device_price)
                                        {
                                            $update = true;
                                        }
                                    }
                                    else
                                    {
                                        $exists = false;
                                        if ($device_price > 0 && $check [0] ['device_price'] != $device_price)
                                        {
                                            $insert = true;
                                            
                                            $data ['dealer_company_id'] = $company;
                                            $data ['master_device_id'] = $master_device_id;
                                        }
                                    }
                                }
                            }
                            else if (in_array("Standard User", $this->privilege) || $hdnRole == "user")
                            {
                                $master_device_id = $results->array [$key] ['Master Printer Id'];
                                $manufacturer_name = $results->array [$key] ['Manufacturer'];
                                $printer_model = $results->array [$key] ['Printer Model'];
                                $device_price = $results->array [$key] ['New Override Price'];
                                
                                $table = new Proposalgen_Model_DbTable_UserDeviceOverride();
                                $data = array (
                                        'override_device_price' => $device_price 
                                );
                                $where = $table->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                
                                // check to see if it exists
                                $select = new Zend_Db_Select($db);
                                $select = $db->select()
                                    ->from(array (
                                        'md' => 'master_device' 
                                ), array (
                                        'device_price' 
                                ))
                                    ->joinLeft(array (
                                        'udo' => 'user_device_override' 
                                ), 'udo.master_device_id = md.master_device_id AND udo.user_id = ' . $this->user_id, array (
                                        'override_device_price' 
                                ))
                                    ->where('md.master_device_id = ' . $master_device_id);
                                $stmt = $db->query($select);
                                $check = $stmt->fetchAll();
                                
                                if (count($check) > 0)
                                {
                                    if ($check [0] ['override_device_price'] > 0)
                                    {
                                        $exists = true;
                                        
                                        // don't update if values match
                                        if ($device_price == 0 || empty($device_price))
                                        {
                                            $delete = true;
                                        }
                                        else if ($check [0] ['device_price'] != $device_price)
                                        {
                                            $update = true;
                                        }
                                    }
                                    else
                                    {
                                        $exists = false;
                                        if ($device_price > 0 && $check [0] ['device_price'] != $device_price)
                                        {
                                            $insert = true;
                                            
                                            $data ['user_id'] = $this->user_id;
                                            $data ['master_device_id'] = $master_device_id;
                                        }
                                    }
                                }
                            }
                        }
                        else
                        {
                            // 0=toner_id; 1=manufacturer_name; 2=toner_SKU;
                            // 3=toner_price
                            if (in_array("System Admin", $this->privilege) && $company == 1)
                            {
                                $toner_id = $results->array [$key] ['Toner ID'];
                                $manufacturer_name = $results->array [$key] ['Manufacturer'];
                                $toner_sku = $results->array [$key] ['SKU'];
                                $toner_price = $results->array [$key] ['New Price'];
                                
                                $table = new Proposalgen_Model_DbTable_Toner();
                                $data = array (
                                        'toner_price' => $toner_price 
                                );
                                $where = $table->getAdapter()->quoteInto('toner_id = ?', $toner_id, 'INTEGER');
                                
                                // check to see if it exists - no inserts in the
                                // Master Tables
                                $check = $table->fetchRow($where);
                                if (count($check) > 0)
                                {
                                    $exists = true;
                                    
                                    // don't update if values match
                                    if (($check ['toner_price'] != $toner_price) && $toner_price > 0)
                                    {
                                        $update = true;
                                    }
                                }
                            }
                            else if ($hdnRole != "user" && (! in_array("Standard User", $this->privilege) && $company > 1))
                            {
                                $toner_id = $results->array [$key] ['Toner ID'];
                                $manufacturer_name = $results->array [$key] ['Manufacturer'];
                                $toner_sku = $results->array [$key] ['SKU'];
                                $toner_price = $results->array [$key] ['New Override Price'];
                                
                                $table = new Proposalgen_Model_DbTable_DealerTonerOverride();
                                $data = array (
                                        'override_toner_price' => $toner_price 
                                );
                                $where = $table->getAdapter()->quoteInto('dealer_company_id = ' . $company . ' AND toner_id = ?', $toner_id, 'INTEGER');
                                
                                // check to see if it exists
                                $select = new Zend_Db_Select($db);
                                $select = $db->select()
                                    ->from(array (
                                        't' => 'toner' 
                                ), array (
                                        'toner_price' 
                                ))
                                    ->joinLeft(array (
                                        'dto' => 'dealer_toner_override' 
                                ), 'dto.toner_id = t.toner_id AND dto.dealer_company_id = ' . $company, array (
                                        'override_toner_price' 
                                ))
                                    ->where('t.toner_id = ?', $toner_id);
                                $stmt = $db->query($select);
                                $check = $stmt->fetchAll();
                                
                                if (count($check) > 0)
                                {
                                    if ($check [0] ['override_toner_price'] > 0)
                                    {
                                        $exists = true;
                                        
                                        // don't update if values match
                                        if ($toner_price == 0 || empty($toner_price))
                                        {
                                            $delete = true;
                                        }
                                        else if ($check [0] ['toner_price'] != $toner_price)
                                        {
                                            $update = true;
                                        }
                                    }
                                    else
                                    {
                                        $exists = false;
                                        if ($toner_price > 0 && $check [0] ['toner_price'] != $toner_price)
                                        {
                                            $insert = true;
                                            
                                            $data ['dealer_company_id'] = $company;
                                            $data ['toner_id'] = $toner_id;
                                        }
                                    }
                                }
                            }
                            else if (in_array("Standard User", $this->privilege) || $hdnRole == "user")
                            {
                                $toner_id = $results->array [$key] ['Toner ID'];
                                $manufacturer_name = $results->array [$key] ['Manufacturer'];
                                $toner_sku = $results->array [$key] ['SKU'];
                                $toner_price = $results->array [$key] ['New Override Price'];
                                
                                $table = new Proposalgen_Model_DbTable_UserTonerOverride();
                                $data = array (
                                        'override_toner_price' => $toner_price 
                                );
                                $where = $table->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                                
                                // check to see if it exists
                                $select = new Zend_Db_Select($db);
                                $select = $db->select()
                                    ->from(array (
                                        't' => 'toner' 
                                ), array (
                                        'toner_price' 
                                ))
                                    ->joinLeft(array (
                                        'uto' => 'user_toner_override' 
                                ), 'uto.toner_id = t.toner_id AND uto.user_id = ' . $this->user_id, array (
                                        'override_toner_price' 
                                ))
                                    ->where('t.toner_id = ?', $toner_id);
                                $stmt = $db->query($select);
                                $toner = $stmt->fetchAll();
                                
                                if (count($check) > 0)
                                {
                                    if ($check [0] ['override_toner_price'] > 0)
                                    {
                                        $exists = true;
                                        
                                        // don't update if values match
                                        if ($toner_price == 0 || empty($toner_price))
                                        {
                                            $delete = true;
                                        }
                                        else if ($check [0] ['toner_price'] != $toner_price)
                                        {
                                            $update = true;
                                        }
                                    }
                                    else
                                    {
                                        $exists = false;
                                        if ($toner_price > 0 && $check [0] ['toner_price'] != $toner_price)
                                        {
                                            $insert = true;
                                            
                                            $data ['user_id'] = $this->user_id;
                                            $data ['toner_id'] = $toner_id;
                                        }
                                    }
                                }
                            }
                        }
                        
                        // update database
                        if ($exists == true)
                        {
                            if ($delete == true)
                            {
                                $table->delete($where);
                            }
                            else if ($update == true)
                            {
                                $table->update($data, $where);
                            }
                        }
                        else if ($insert == true)
                        {
                            $table->insert($data);
                        }
                    }
                    $this->view->message = "Your pricing updates have been applied successfully.";
                    $db->commit();
                }
                catch ( Exception $e )
                {
                    $db->rollback();
                    $this->view->message = "<span class=\"warning\">*</span> An error has occurred during the update and your changes were not applied. Please review your file and try again.";
                }
            }
            else
            {
                // ************************************************************/
                // * Upload File and Build Preview
                // ************************************************************/
                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->setDestination($this->config->app->uploadPath);
                
                // Limit the extensions to csv files
                $upload->addValidator('Extension', false, 'csv');
                $upload->getValidator('Extension')->setMessage('<span class="warning">*</span> File "' . basename($_FILES ['uploadedfile'] ['name']) . '" has an <em>invalid</em> extension. A <span style="color: red;">.csv</span> is required.');
                
                // Limit the amount of files to maximum 1
                $upload->addValidator('Count', false, 1);
                $upload->getValidator('Count')->setMessage('<span class="warning">*</span> You are only allowed to upload 1 file at a time.');
                
                // Limit the size of all files to be uploaded to maximum 4MB and
                // mimimum 500B
                $upload->addValidator('FilesSize', false, array (
                        'min' => '500B', 
                        'max' => '4MB' 
                ));
                $upload->getValidator('FilesSize')->setMessage('<span class="warning">*</span> File size must be between 500B and 4MB.');
                
                if ($upload->receive())
                {
                    $is_valid = true;
                    $columns = array ();
                    $headers = array ();
                    $final_devices = array ();
                    $finalDevices = array ();
                    
                    $db->beginTransaction();
                    try
                    {
                        $lines = file($upload->getFileName(), FILE_IGNORE_NEW_LINES);
                        
                        // grab the first row of items(the column headers)
                        $headers = str_getcsv(strtolower($lines [0]));
                        
                        // detect file type (printers or toners)
                        $array_key = 0;
                        
                        // default column keys
                        $key_toner_id = null;
                        $key_manufacturer = null;
                        $key_part_type = null;
                        $key_sku = null;
                        $key_color = null;
                        $key_yield = null;
                        $key_new_price = null;
                        $key_master_printer_id = null;
                        $key_printer_model = null;
                        
                        foreach ( $headers as $key => $value )
                        {
                            if (strtolower($value) == "toner id")
                            {
                                $import_type = "toner";
                                $key_toner_id = $array_key;
                            }
                            else if (strtolower($value) == "manufacturer")
                            {
                                $key_manufacturer = $array_key;
                            }
                            else if (strtolower($value) == "type")
                            {
                                $key_part_type = $array_key;
                            }
                            else if (strtolower($value) == "sku")
                            {
                                $key_sku = $array_key;
                            }
                            else if (strtolower($value) == "color")
                            {
                                $key_color = $array_key;
                            }
                            else if (strtolower($value) == "yield")
                            {
                                $key_yield = $array_key;
                            }
                            else if (strtolower($value) == "price")
                            {
                                $key_new_price = $array_key;
                            }
                            else if (strtolower($value) == "master printer id")
                            {
                                $import_type = "printer";
                                $key_master_printer_id = $array_key;
                            }
                            else if (strtolower($value) == "printer model")
                            {
                                $key_printer_model = $array_key;
                            }
                            $array_key += 1;
                        }
                        
                        if ($is_valid)
                        {
                            // create an associative array of the csv infomation
                            foreach ( $lines as $key => $value )
                            {
                                if ($key > 0)
                                {
                                    $devices [$key] = str_getcsv($value);
                                    
                                    // get current pricing
                                    if ($import_type == "printer")
                                    {
                                        $current_device_price = 0;
                                        $current_parts_cpp = 0;
                                        $current_labor_cpp = 0;
                                        
                                        $master_device_id = $devices [$key] [0];
                                        if (in_array("System Admin", $this->privilege) && $company == 1)
                                        {
                                            $columns [0] = "Master Printer ID";
                                            $columns [1] = "Manufacturer";
                                            $columns [2] = "Printer Model";
                                            $columns [3] = "Current Price";
                                            $columns [4] = "New Price";
                                            
                                            $table = new Proposalgen_Model_DbTable_MasterDevice();
                                            $where = $table->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                                            $printer = $table->fetchRow($where);
                                            
                                            if (count($printer) > 0)
                                            {
                                                // get current costs
                                                $current_device_price = $printer ['device_price'];
                                                
                                                // save into array
                                                $final_devices [0] = $master_device_id;
                                                $final_devices [1] = $devices [$key] [$key_manufacturer];
                                                $final_devices [2] = $devices [$key] [$key_printer_model];
                                                $final_devices [3] = $current_device_price;
                                                $final_devices [4] = $devices [$key] [$key_new_price];
                                            }
                                        }
                                        else if ($this->view->hdnRole != "user" && (! in_array("Standard User", $this->privilege) && $company > 1))
                                        {
                                            $columns [0] = "Master Printer ID";
                                            $columns [1] = "Manufacturer";
                                            $columns [2] = "Printer Model";
                                            $columns [3] = "Master Price";
                                            $columns [4] = "Override Price";
                                            $columns [5] = "New Override Price";
                                            
                                            $select = new Zend_Db_Select($db);
                                            $select = $db->select()
                                                ->from(array (
                                                    'md' => 'master_device' 
                                            ), array (
                                                    'device_price' 
                                            ))
                                                ->joinLeft(array (
                                                    'ddo' => 'dealer_device_override' 
                                            ), 'ddo.master_device_id = md.master_device_id AND ddo.dealer_company_id = ' . $company, array (
                                                    'override_device_price' 
                                            ))
                                                ->where('md.master_device_id = ' . $master_device_id);
                                            $stmt = $db->query($select);
                                            $printer = $stmt->fetchAll();
                                            
                                            if (count($printer) > 0)
                                            {
                                                // get current costs
                                                $current_device_price = $printer [0] ['device_price'];
                                                $current_override_price = $printer [0] ['override_device_price'];
                                                
                                                // save into array
                                                $final_devices [0] = $master_device_id;
                                                $final_devices [1] = $devices [$key] [$key_manufacturer];
                                                $final_devices [2] = $devices [$key] [$key_printer_model];
                                                $final_devices [3] = $current_device_price;
                                                $final_devices [4] = $current_override_price;
                                                $final_devices [5] = $devices [$key] [$key_new_price];
                                            }
                                        }
                                        else if (in_array("Standard User", $this->privilege) || $this->view->hdnRole == "user")
                                        {
                                            $columns [0] = "Master Printer ID";
                                            $columns [1] = "Manufacturer";
                                            $columns [2] = "Printer Model";
                                            $columns [3] = "Master Price";
                                            $columns [4] = "Override Price";
                                            $columns [5] = "New Override Price";
                                            
                                            $select = new Zend_Db_Select($db);
                                            $select = $db->select()
                                                ->from(array (
                                                    'md' => 'master_device' 
                                            ), array (
                                                    'device_price' 
                                            ))
                                                ->joinLeft(array (
                                                    'udo' => 'user_device_override' 
                                            ), 'udo.master_device_id = md.master_device_id AND udo.user_id = ' . $this->user_id, array (
                                                    'override_device_price' 
                                            ))
                                                ->where('md.master_device_id = ' . $master_device_id);
                                            $stmt = $db->query($select);
                                            $printer = $stmt->fetchAll();
                                            
                                            if (count($printer) > 0)
                                            {
                                                // get current costs
                                                $current_device_price = $printer [0] ['device_price'];
                                                $current_override_price = $printer [0] ['override_device_price'];
                                                
                                                // save into array
                                                $final_devices [0] = $master_device_id;
                                                $final_devices [1] = $devices [$key] [$key_manufacturer];
                                                $final_devices [2] = $devices [$key] [$key_printer_model];
                                                $final_devices [3] = $current_device_price;
                                                $final_devices [4] = $current_override_price;
                                                $final_devices [5] = $devices [$key] [$key_new_price];
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $current_toner_price = 0;
                                        
                                        $toner_id = $devices [$key] [0];
                                        if (in_array("System Admin", $this->privilege) && $company == 1)
                                        {
                                            $columns [0] = "Toner ID";
                                            $columns [1] = "Manufacturer";
                                            $columns [2] = "Part Type";
                                            $columns [3] = "SKU";
                                            $columns [4] = "Color";
                                            $columns [5] = "Yield";
                                            $columns [6] = "Current Price";
                                            $columns [7] = "New Price";
                                            
                                            $table = new Proposalgen_Model_DbTable_Toner();
                                            $where = $table->getAdapter()->quoteInto('toner_id = ?', $toner_id, 'INTEGER');
                                            $toner = $table->fetchRow($where);
                                            
                                            if (count($toner) > 0)
                                            {
                                                // get current costs
                                                $current_toner_price = $toner ['toner_price'];
                                                
                                                // save into array
                                                $final_devices [0] = $toner_id;
                                                $final_devices [1] = $devices [$key] [$key_manufacturer];
                                                $final_devices [2] = $devices [$key] [$key_part_type];
                                                $final_devices [3] = $devices [$key] [$key_sku];
                                                $final_devices [4] = $devices [$key] [$key_color];
                                                $final_devices [5] = $devices [$key] [$key_yield];
                                                $final_devices [6] = $current_toner_price;
                                                $final_devices [7] = $devices [$key] [$key_new_price];
                                            }
                                        }
                                        else if ($this->view->hdnRole != "user" && (! in_array("Standard User", $this->privilege) && $company > 1))
                                        {
                                            $columns [0] = "Toner ID";
                                            $columns [1] = "Manufacturer";
                                            $columns [2] = "Part Type";
                                            $columns [3] = "SKU";
                                            $columns [4] = "Color";
                                            $columns [5] = "Yield";
                                            $columns [6] = "Master Price";
                                            $columns [7] = "Override Price";
                                            $columns [8] = "New Override Price";
                                            
                                            $select = new Zend_Db_Select($db);
                                            $select = $db->select()
                                                ->from(array (
                                                    't' => 'toner' 
                                            ), array (
                                                    'toner_price' 
                                            ))
                                                ->joinLeft(array (
                                                    'dto' => 'dealer_toner_override' 
                                            ), 'dto.toner_id = t.toner_id AND dto.dealer_company_id = ' . $company, array (
                                                    'override_toner_price' 
                                            ))
                                                ->where('t.toner_id = ?', $toner_id);
                                            $stmt = $db->query($select);
                                            $toner = $stmt->fetchAll();
                                            
                                            if (count($toner) > 0)
                                            {
                                                // get current costs
                                                $current_toner_price = $toner [0] ['toner_price'];
                                                $current_override_price = $toner [0] ['override_toner_price'];
                                                
                                                // save into array
                                                $final_devices [0] = $toner_id;
                                                $final_devices [1] = $devices [$key] [$key_manufacturer];
                                                $final_devices [2] = $devices [$key] [$key_part_type];
                                                $final_devices [3] = $devices [$key] [$key_sku];
                                                $final_devices [4] = $devices [$key] [$key_color];
                                                $final_devices [5] = $devices [$key] [$key_yield];
                                                $final_devices [6] = $current_toner_price;
                                                $final_devices [7] = $current_override_price;
                                                $final_devices [8] = $devices [$key] [$key_new_price];
                                            }
                                        }
                                        else if (in_array("Standard User", $this->privilege) || $this->view->hdnRole == "user")
                                        {
                                            $columns [0] = "Toner ID";
                                            $columns [1] = "Manufacturer";
                                            $columns [2] = "Part Type";
                                            $columns [3] = "SKU";
                                            $columns [4] = "Color";
                                            $columns [5] = "Yield";
                                            $columns [6] = "Master Price";
                                            $columns [7] = "Override Price";
                                            $columns [8] = "New Override Price";
                                            
                                            $select = new Zend_Db_Select($db);
                                            $select = $db->select()
                                                ->from(array (
                                                    't' => 'toner' 
                                            ), array (
                                                    'toner_price' 
                                            ))
                                                ->joinLeft(array (
                                                    'uto' => 'user_toner_override' 
                                            ), 'uto.toner_id = t.toner_id AND uto.user_id = ' . $this->user_id, array (
                                                    'override_toner_price' 
                                            ))
                                                ->where('t.toner_id = ?', $toner_id);
                                            $stmt = $db->query($select);
                                            $toner = $stmt->fetchAll();
                                            
                                            if (count($toner) > 0)
                                            {
                                                // get current costs
                                                $current_toner_price = $toner [0] ['toner_price'];
                                                $current_override_price = $toner [0] ['override_toner_price'];
                                                
                                                // save into array
                                                $final_devices [0] = $toner_id;
                                                $final_devices [1] = $devices [$key] [$key_manufacturer];
                                                $final_devices [2] = $devices [$key] [$key_part_type];
                                                $final_devices [3] = $devices [$key] [$key_sku];
                                                $final_devices [4] = $devices [$key] [$key_color];
                                                $final_devices [5] = $devices [$key] [$key_yield];
                                                $final_devices [6] = $current_toner_price;
                                                $final_devices [7] = $current_override_price;
                                                $final_devices [8] = $devices [$key] [$key_new_price];
                                            }
                                        }
                                    }
                                    
                                    // combine the column headers and the device
                                    // data into one associative array
                                    $devices [$key] = $final_devices;
                                    if ($devices [$key])
                                    {
                                        $finalDevices [] = array_combine($columns, $devices [$key]);
                                    }
                                }
                            }
                            $this->view->columnsArray = $columns;
                            $this->view->headerArray = $headers;
                            $this->view->resultsArray = $finalDevices;
                            
                            // store array in session to be used by
                            // confirmationAction to save the values to the
                            // database
                            $columns_session = new Zend_Session_Namespace('import_headers_array');
                            $columns_session->array = $columns;
                            
                            $results_session = new Zend_Session_Namespace('import_results_array');
                            $results_session->array = $finalDevices;
                        }
                    }
                    catch ( Exception $e )
                    {
                        $db->rollback();
                        $this->view->message = "<span class=\"warning\">*</span> An error has occurred during the update and your changes were not applied. Please review your file and try again.";
                    }
                    
                    // delete the file we just uploaded
                    unlink($upload->getFileName());
                }
                else
                {
                    // if upload fails, print error message message
                    $this->view->errMessages = $upload->getMessages();
                }
            }
            $this->view->hdnRole = $formData ['hdnRole'];
        }
        return;
    }

    public function exportpricingAction ()
    {
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $company = $this->_getParam('company', $this->dealer_company_id);
        $pricing = $this->_getParam('pricing', 'printer');
        
        // for OD default company to Master
        $company = 1;
        
        // hdnRole is used when logged in as a dealer to differenciate between
        // if the dealer is on "update company pricing" or "update my pricing"
        $hdnRole = $this->_getParam('hdnRole', 'dealer');
        
        // get company name for filename
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $company, 'INTEGER');
        $dealer_company = $dealer_companyTable->fetchRow($where);
        if (count($dealer_company) > 0)
        {
            $company_name = $dealer_company ['company_name'];
        }
        
        // filename for CSV file
        $filename = strtolower(str_replace(" ", "_", $company_name)) . "_" . $pricing . "_pricing_" . date('m_d_Y') . ".csv";
        
        // check post back for update
        $db->beginTransaction();
        try
        {
            // Get device list
            if ($pricing == 'printer')
            {
                $fieldTitles = array (
                        'Master Printer ID', 
                        'Manufacturer', 
                        'Printer Model', 
                        'Price' 
                );
                
                if (in_array("System Admin", $this->privilege) && $company == 1)
                {
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array (
                            'md' => 'master_device' 
                    ), array (
                            'master_device_id', 
                            'mastdevice_manufacturer', 
                            'printer_model', 
                            'device_price' 
                    ))
                        ->joinLeft(array (
                            'm' => 'manufacturer' 
                    ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                            'manufacturer_id', 
                            'manufacturer_name' 
                    ))
                        ->order(array (
                            'm.manufacturer_name', 
                            'md.printer_model' 
                    ));
                    $stmt = $db->query($select);
                    $result = $stmt->fetchAll();
                }
                else if ($hdnRole != "user" && (! in_array("Standard User", $this->privilege) && $company > 1))
                {
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array (
                            'md' => 'master_device' 
                    ), array (
                            'master_device_id', 
                            'mastdevice_manufacturer', 
                            'printer_model', 
                            'device_price' 
                    ))
                        ->joinLeft(array (
                            'm' => 'manufacturer' 
                    ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                            'manufacturer_id', 
                            'manufacturer_name' 
                    ))
                        ->joinLeft(array (
                            'ddo' => 'dealer_device_override' 
                    ), 'ddo.master_device_id = md.master_device_id AND ddo.dealer_company_id = ' . $company, array (
                            'override_device_price' 
                    ))
                        ->order(array (
                            'm.manufacturer_name', 
                            'md.printer_model' 
                    ));
                    $stmt = $db->query($select);
                    $result = $stmt->fetchAll();
                }
                else if (in_array("Standard User", $this->privilege || $hdnRole == "user"))
                {
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array (
                            'md' => 'master_device' 
                    ), array (
                            'master_device_id', 
                            'mastdevice_manufacturer', 
                            'printer_model', 
                            'device_price' 
                    ))
                        ->joinLeft(array (
                            'm' => 'manufacturer' 
                    ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                            'manufacturer_id', 
                            'manufacturer_name' 
                    ))
                        ->joinLeft(array (
                            'udo' => 'user_device_override' 
                    ), 'udo.master_device_id = md.master_device_id AND udo.user_id = ' . $this->user_id, array (
                            'override_device_price' 
                    ))
                        ->order(array (
                            'm.manufacturer_name', 
                            'md.printer_model' 
                    ));
                    $stmt = $db->query($select);
                    $result = $stmt->fetchAll();
                }
                
                foreach ( $result as $key => $value )
                {
                    $price = 0;
                    
                    // prep pricing
                    if (in_array("System Admin", $this->privilege) && $company == 1)
                    {
                        $price = $value ['device_price'];
                    }
                    else
                    {
                        $price = $value ['override_device_price'];
                    }
                    
                    $fieldList [] = array (
                            $value ['master_device_id'], 
                            $value ['manufacturer_name'], 
                            $value ['printer_model'], 
                            $price 
                    );
                }
            }
            else
            {
                $fieldTitles = array (
                        'Toner ID', 
                        'Manufacturer', 
                        'Type', 
                        'SKU', 
                        'Color', 
                        'Yield', 
                        'Price' 
                );
                
                if (in_array("System Admin", $this->privilege) && $company == 1)
                {
                    // get count
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array (
                            't' => 'toner' 
                    ))
                        ->joinLeft(array (
                            'dt' => 'device_toner' 
                    ), 'dt.toner_id = t.toner_id', array (
                            'master_device_id' 
                    ))
                        ->joinLeft(array (
                            'tm' => 'manufacturer' 
                    ), 'tm.manufacturer_id = t.manufacturer_id', array (
                            'manufacturer_name' 
                    ))
                        ->joinLeft(array (
                            'tc' => 'toner_color' 
                    ), 'tc.toner_color_id = t.toner_color_id')
                        ->joinLeft(array (
                            'pt' => 'part_type' 
                    ), 'pt.part_type_id = t.part_type_id')
                        ->where('t.toner_id > 0')
                        ->group('t.toner_id')
                        ->order(array (
                            'tm.manufacturer_name' 
                    ));
                    $stmt = $db->query($select);
                    $result = $stmt->fetchAll();
                }
                else if ($hdnRole != "user" && (! in_array("Standard User", $this->privilege) && $company > 1))
                {
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array (
                            't' => 'toner' 
                    ))
                        ->joinLeft(array (
                            'tm' => 'manufacturer' 
                    ), 'tm.manufacturer_id = t.manufacturer_id', array (
                            'manufacturer_name' 
                    ))
                        ->joinLeft(array (
                            'dt' => 'device_toner' 
                    ), 'dt.toner_id = t.toner_id')
                        ->joinLeft(array (
                            'md' => 'master_device' 
                    ), 'md.master_device_id = dt.master_device_id')
                        ->joinLeft(array (
                            'tc' => 'toner_color' 
                    ), 'tc.toner_color_id = t.toner_color_id')
                        ->joinLeft(array (
                            'pt' => 'part_type' 
                    ), 'pt.part_type_id = t.part_type_id')
                        ->joinLeft(array (
                            'dto' => 'dealer_toner_override' 
                    ), 'dto.toner_id = t.toner_id AND dto.dealer_company_id = ' . $company, array (
                            'dealer_company_id', 
                            'override_toner_price' 
                    ))
                        ->group('t.toner_id')
                        ->order(array (
                            'tm.manufacturer_name', 
                            'md.printer_model', 
                            't.toner_SKU' 
                    ));
                    $stmt = $db->query($select);
                    $result = $stmt->fetchAll();
                }
                else if (in_array("Standard User", $this->privilege || $hdnRole == "user"))
                {
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array (
                            't' => 'toner' 
                    ))
                        ->joinLeft(array (
                            'tm' => 'manufacturer' 
                    ), 'tm.manufacturer_id = t.manufacturer_id', array (
                            'manufacturer_name' 
                    ))
                        ->joinLeft(array (
                            'dt' => 'device_toner' 
                    ), 'dt.toner_id = t.toner_id')
                        ->joinLeft(array (
                            'md' => 'master_device' 
                    ), 'md.master_device_id = dt.master_device_id')
                        ->joinLeft(array (
                            'tc' => 'toner_color' 
                    ), 'tc.toner_color_id = t.toner_color_id')
                        ->joinLeft(array (
                            'pt' => 'part_type' 
                    ), 'pt.part_type_id = t.part_type_id')
                        ->joinLeft(array (
                            'uto' => 'user_toner_override' 
                    ), 'uto.toner_id = t.toner_id AND uto.user_id = ' . $this->user_id, array (
                            'user_id', 
                            'override_toner_price' 
                    ))
                        ->group('t.toner_id')
                        ->order(array (
                            'tm.manufacturer_name', 
                            'md.printer_model', 
                            't.toner_SKU' 
                    ));
                    $stmt = $db->query($select);
                    $result = $stmt->fetchAll();
                }
                
                foreach ( $result as $key => $value )
                {
                    $price = 0;
                    
                    // prep pricing
                    if (in_array("System Admin", $this->privilege) && $company == 1)
                    {
                        $price = $value ['toner_price'];
                    }
                    else
                    {
                        $price = $value ['override_toner_price'];
                    }
                    
                    $fieldList [] = array (
                            $value ['toner_id'], 
                            $value ['manufacturer_name'], 
                            $value ['type_name'], 
                            $value ['toner_SKU'], 
                            $value ['toner_color_name'], 
                            $value ['toner_yield'], 
                            $price 
                    );
                }
            }
        }
        catch ( Exception $e )
        {
            throw new Exception("CSV File could not be opened/written for export.", 0, $e);
        }
        
        $this->view->fieldTitles = implode(",", $fieldTitles);
        $newFieldList = "";
        foreach ( $fieldList as $row )
        {
            $newFieldList .= implode(",", $row);
            $newFieldList .= "\n";
        }
        $this->view->fieldList = $newFieldList;
        
        Tangent_Functions::setHeadersForDownload($filename);
    }

    public function importpricingAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = "Import Pricing File";
        
        if ($this->_request->isPost())
        {
        }
        else
        {
        }
    }

    public function masterdeviceslistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $type = $this->_getParam('type', 'printers');
        $filter = $this->_getParam('filter', false);
        $criteria = $this->_getParam('criteria', false);
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 'm.manufacturer_name';
        
        $where = '';
        if (! empty($filter) && ! empty($criteria))
        {
            $where = $filter . ' LIKE("%' . $criteria . '%")';
        }
        
        try
        {
            // get count
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'md' => 'master_device' 
            ))
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'manufacturer_name' 
            ));
            if ($where != '')
            {
                $select->where($where);
            }
            $select->order(array (
                    'md.master_device_id', 
                    'm.manufacturer_name', 
                    'md.printer_model' 
            ));
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $count = count($result);
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit;
            if ($start < 0)
                $start = 0;
                
                // select master devices
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'md' => 'master_device' 
            ))
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'manufacturer_name' 
            ));
            if ($where != '')
            {
                $select->where($where);
            }
            $select->order($sidx . ' ' . $sord);
            $select->limit($limit, $start);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $formdata->page = $page;
            $formdata->total = $total_pages;
            $formdata->records = $count;
            if (count($result) > 0)
            {
                $i = 0;
                foreach ( $result as $row )
                {
                    $price = 0;
                    if ($type == 'labor')
                    {
                        $price = number_format($row ['labor_cost_per_page'], 4, '.', '');
                    }
                    else if ($type == 'parts')
                    {
                        $price = number_format($row ['parts_cost_per_page'], 4, '.', '');
                    }
                    else
                    {
                        $price = number_format($row ['device_price'], 2, '.', '');
                    }
                    
                    $formdata->rows [$i] ['id'] = $row ['master_device_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            ucwords(strtolower($row ['manufacturer_name'])), 
                            ucwords(strtolower($row ['printer_model'])), 
                            $price 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            echo $e->getMessage();
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function dealerdeviceslistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $type = $this->_getParam('type', 'printers');
        $dealer_company_id = $this->_getParam('compid', $this->dealer_company_id);
        $filter = $this->_getParam('filter', false);
        $criteria = $this->_getParam('criteria', false);
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 'm.manufacturer_name';
        
        $where = '';
        if (! empty($filter) && ! empty($criteria))
        {
            $where = $filter . ' LIKE("%' . $criteria . '%")';
        }
        
        try
        {
            // select master devices
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'md' => 'master_device' 
            ), array (
                    'master_device_id', 
                    'mastdevice_manufacturer', 
                    'printer_model', 
                    'device_price', 
                    'labor_cost_per_page', 
                    'parts_cost_per_page' 
            ))
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'manufacturer_name' 
            ))
                ->joinLeft(array (
                    'ddo' => 'dealer_device_override' 
            ), 'ddo.master_device_id = md.master_device_id AND ddo.dealer_company_id = ' . $dealer_company_id, array (
                    'override_device_price' 
            ))
                ->where($where)
                ->order(array (
                    'm.manufacturer_name', 
                    'md.printer_model' 
            ));
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $count = count($result);
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit;
            if ($start < 0)
                $start = 0;
                
                // select master devices
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'md' => 'master_device' 
            ), array (
                    'master_device_id', 
                    'mastdevice_manufacturer', 
                    'printer_model', 
                    'device_price', 
                    'labor_cost_per_page', 
                    'parts_cost_per_page' 
            ))
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'manufacturer_name' 
            ))
                ->joinLeft(array (
                    'ddo' => 'dealer_device_override' 
            ), 'ddo.master_device_id = md.master_device_id AND ddo.dealer_company_id = ' . $dealer_company_id, array (
                    'override_device_price' 
            ))
                ->joinLeft(array (
                    'dlo' => 'dealer_labor_CPP_override' 
            ), 'dlo.master_device_id = md.master_device_id AND dlo.dealer_company_id = ' . $dealer_company_id, array (
                    'override_labor_CPP' 
            ))
                ->joinLeft(array (
                    'dpo' => 'dealer_parts_CPP_override' 
            ), 'dpo.master_device_id = md.master_device_id AND dpo.dealer_company_id = ' . $dealer_company_id, array (
                    'override_parts_CPP' 
            ))
                ->where($where)
                ->order($sidx . ' ' . $sord)
                ->limit($limit, $start);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $formdata->page = $page;
            $formdata->total = $total_pages;
            $formdata->records = $count;
            if (count($result) > 0)
            {
                $i = 0;
                $price_margin = ($this->getPricingMargin('dealer', $dealer_company_id) / 100) + 1;
                foreach ( $result as $row )
                {
                    $printer_cost = 0;
                    
                    $price = 0;
                    if ($type == 'labor')
                    {
                        $price = number_format($row ['labor_cost_per_page'], 4, '.', '');
                        $overridePrice = number_format($row ['override_labor_CPP'], 4, '.', '');
                    }
                    else if ($type == 'parts')
                    {
                        $price = number_format($row ['parts_cost_per_page'], 4, '.', '');
                        $overridePrice = number_format($row ['override_parts_CPP'], 4, '.', '');
                    }
                    else
                    {
                        $price = number_format(($row ['device_price'] * $price_margin), 2, '.', '');
                        $overridePrice = number_format($row ['override_device_price'], 2, '.', '');
                    }
                    
                    $formdata->rows [$i] ['id'] = $row ['master_device_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            ucwords(strtolower($row ['manufacturer_name'])), 
                            ucwords(strtolower($row ['printer_model'])), 
                            $price, 
                            ($overridePrice > 0 ? $overridePrice : null) 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            echo $e->getMessage();
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function userdevicesAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $type = $this->_getParam('type', 'printers');
        $user_id = $this->user_id;
        $filter = $this->_getParam('filter', false);
        $criteria = $this->_getParam('criteria', false);
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 'm.manufacturer_name';
        
        $where = '';
        if (! empty($filter) && ! empty($criteria))
        {
            $where = $filter . ' LIKE("%' . $criteria . '%")';
        }
        
        try
        {
            // select master devices
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'md' => 'master_device' 
            ), array (
                    'master_device_id', 
                    'mastdevice_manufacturer', 
                    'printer_model', 
                    'device_price' 
            ))
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'manufacturer_name' 
            ))
                ->joinLeft(array (
                    'udo' => 'user_device_override' 
            ), 'udo.master_device_id = md.master_device_id AND udo.user_id = ' . $user_id, array (
                    'override_device_price' 
            ));
            if ($where != '')
            {
                $select->where($where);
            }
            $select->order(array (
                    'm.manufacturer_name', 
                    'md.printer_model' 
            ));
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $count = count($result);
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit;
            if ($start < 0)
                $start = 0;
                
                // select master devices
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'md' => 'master_device' 
            ), array (
                    'master_device_id', 
                    'mastdevice_manufacturer', 
                    'printer_model', 
                    'device_price' 
            ))
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'manufacturer_name' 
            ))
                ->joinLeft(array (
                    'udo' => 'user_device_override' 
            ), 'udo.master_device_id = md.master_device_id AND udo.user_id = ' . $user_id, array (
                    'override_device_price' 
            ));
            if ($where != '')
            {
                $select->where($where);
            }
            $select->order($sidx . ' ' . $sord);
            $select->limit($limit, $start);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $formdata->page = $page;
            $formdata->total = $total_pages;
            $formdata->records = $count;
            if (count($result) > 0)
            {
                $i = 0;
                $price_margin = ($this->getPricingMargin('dealer', $this->dealer_company_id) / 100) + 1;
                foreach ( $result as $row )
                {
                    $printer_cost = 0;
                    
                    $price = number_format(($row ['device_price'] * $price_margin), 2, '.', '');
                    
                    $formdata->rows [$i] ['id'] = $row ['master_device_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            ucwords(strtolower($row ['manufacturer_name'])), 
                            ucwords(strtolower($row ['printer_model'])), 
                            $price, 
                            ($row ['override_device_price'] > 0 ? money_format('%i', $row ['override_device_price']) : null) 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            echo $e->getMessage();
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function tonerslistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $master_device_id = $this->_getParam('deviceid', false);
        $filter = $this->_getParam('filter', false);
        $criteria = trim($this->_getParam('criteria', false));
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 1;
        
        $where = '';
        $where_compatible = '';
        if (! empty($filter) && ! empty($criteria) && $filter != 'machine_compatibility')
        {
            if ($filter == 'toner_yield')
            {
                $where = ' AND ' . $filter . ' = ' . $criteria;
            }
            else
            {
                if ($filter == "manufacturer_name")
                {
                    $filter = "tm.manufacturer_name";
                }
                $where = ' AND ' . $filter . ' LIKE("%' . $criteria . '%")';
            }
        }
        else if (! empty($filter) && $filter == 'machine_compatibility')
        {
            if (strtolower($criteria) == "hp")
            {
                $criteria = "hewlett-packard";
            }
            $where_compatible = $criteria;
        }
        
        if ($master_device_id > 0)
        {
            $toner_fields_list = array (
                    'toner_id', 
                    'toner_SKU', 
                    'toner_yield', 
                    'toner_price', 
                    '(SELECT master_device_id FROM device_toner sdt WHERE sdt.toner_id = t.toner_id AND sdt.master_device_id = ' . $master_device_id . ') AS is_added', 
                    'GROUP_CONCAT(CONCAT(mdm.manufacturer_name," ",md.printer_model) SEPARATOR "; ") AS machine_compatibility' 
            );
        }
        else
        {
            $toner_fields_list = array (
                    'toner_id', 
                    'toner_SKU', 
                    'toner_yield', 
                    'toner_price', 
                    '(null) AS is_added', 
                    'GROUP_CONCAT(CONCAT(mdm.manufacturer_name," ",md.printer_model) SEPARATOR "; ") AS machine_compatibility' 
            );
        }
        $formdata = null;
        
        try
        {
            // get count
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    't' => 'toner' 
            ), $toner_fields_list)
                ->joinLeft(array (
                    'dt' => 'device_toner' 
            ), 'dt.toner_id = t.toner_id', array (
                    'master_device_id' 
            ))
                ->joinLeft(array (
                    'tm' => 'manufacturer' 
            ), 'tm.manufacturer_id = t.manufacturer_id', array (
                    'tm.manufacturer_name AS toner_manufacturer' 
            ))
                ->joinLeft(array (
                    'md' => 'master_device' 
            ), 'md.master_device_id = dt.master_device_id')
                ->joinLeft(array (
                    'mdm' => 'manufacturer' 
            ), 'mdm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'mdm.manufacturer_name' 
            ))
                ->joinLeft(array (
                    'tc' => 'toner_color' 
            ), 'tc.toner_color_id = t.toner_color_id')
                ->joinLeft(array (
                    'pt' => 'part_type' 
            ), 'pt.part_type_id = t.part_type_id')
                ->where('t.toner_id > 0' . $where);
            
            if ($where_compatible)
            {
                $select->where("CONCAT(mdm.manufacturer_name,' ',md.printer_model) LIKE '%" . $where_compatible . "%'");
            }
            $select->group('t.toner_id');
            $select->order(array (
                    'tm.manufacturer_name' 
            ));
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $count = count($result);
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit;
            if ($start < 0)
                $start = 0;
            
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    't' => 'toner' 
            ), $toner_fields_list)
                ->joinLeft(array (
                    'dt' => 'device_toner' 
            ), 'dt.toner_id = t.toner_id', array (
                    'master_device_id' 
            ))
                ->joinLeft(array (
                    'tm' => 'manufacturer' 
            ), 'tm.manufacturer_id = t.manufacturer_id', array (
                    'tm.manufacturer_name AS toner_manufacturer' 
            ))
                ->joinLeft(array (
                    'md' => 'master_device' 
            ), 'md.master_device_id = dt.master_device_id')
                ->joinLeft(array (
                    'mdm' => 'manufacturer' 
            ), 'mdm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'mdm.manufacturer_name' 
            ))
                ->joinLeft(array (
                    'tc' => 'toner_color' 
            ), 'tc.toner_color_id = t.toner_color_id')
                ->joinLeft(array (
                    'pt' => 'part_type' 
            ), 'pt.part_type_id = t.part_type_id')
                ->where('t.toner_id > 0' . $where);
            
            if ($where_compatible)
            {
                $select->where("CONCAT(mdm.manufacturer_name,' ',md.printer_model) LIKE '%" . $where_compatible . "%'");
            }
            $select->group('t.toner_id');
            $select->order($sidx . ' ' . $sord);
            $select->limit($limit, $start);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $formdata->page = $page;
            $formdata->total = $total_pages;
            $formdata->records = $count;
            if (count($result) > 0)
            {
                $i = 0;
                foreach ( $result as $row )
                {
                    // Always uppercase OEM, but just captialize everything else
                    $type_name = ucwords(strtolower($row ['type_name']));
                    if ($type_name == "Oem")
                    {
                        $type_name = "OEM";
                    }
                    
                    $formdata->rows [$i] ['id'] = $row ['toner_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['toner_id'], 
                            $row ['toner_SKU'], 
                            ucwords(strtolower($row ['toner_manufacturer'])), 
                            $type_name, 
                            ucwords(strtolower($row ['toner_color_name'])), 
                            $row ['toner_yield'], 
                            $row ['toner_price'], 
                            $row ['master_device_id'], 
                            $row ['is_added'], 
                            ucwords(strtolower($row ['machine_compatibility'])) 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            echo $e->getMessage();
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function dealertonerlistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $master_device_id = $this->_getParam('deviceid', false);
        $dealer_company_id = $this->_getParam('compid', $this->dealer_company_id);
        $filter = $this->_getParam('filter', false);
        $criteria = $this->_getParam('criteria', false);
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 1;
        
        $where = '';
        $where_compatible = '';
        if (! empty($filter) && ! empty($criteria) && $filter != 'machine_compatibility')
        {
            if ($filter == "manufacturer_name")
            {
                $filter = "tm.manufacturer_name";
            }
            $where = $filter . ' LIKE("%' . $criteria . '%")';
        }
        else if (! empty($filter) && $filter == 'machine_compatibility')
        {
            if (strtolower($criteria) == "hp")
            {
                $criteria = "hewlett-packard";
            }
            $where_compatible = $criteria;
        }
        
        try
        {
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    't' => 'toner' 
            ), array (
                    't.toner_id', 
                    't.toner_SKU', 
                    't.toner_yield', 
                    't.toner_price', 
                    '(null) AS is_added', 
                    'GROUP_CONCAT(CONCAT(mdm.manufacturer_name," ",md.printer_model) SEPARATOR "; ") AS machine_compatibility' 
            ))
                ->joinLeft(array (
                    'tm' => 'manufacturer' 
            ), 'tm.manufacturer_id = t.manufacturer_id', array (
                    'tm.manufacturer_name AS toner_manufacturer' 
            ))
                ->joinLeft(array (
                    'dt' => 'device_toner' 
            ), 'dt.toner_id = t.toner_id')
                ->joinLeft(array (
                    'md' => 'master_device' 
            ), 'md.master_device_id = dt.master_device_id')
                ->joinLeft(array (
                    'mdm' => 'manufacturer' 
            ), 'mdm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'mdm.manufacturer_name' 
            ))
                ->joinLeft(array (
                    'tc' => 'toner_color' 
            ), 'tc.toner_color_id = t.toner_color_id')
                ->joinLeft(array (
                    'pt' => 'part_type' 
            ), 'pt.part_type_id = t.part_type_id')
                ->joinLeft(array (
                    'dto' => 'dealer_toner_override' 
            ), 'dto.toner_id = t.toner_id AND dto.dealer_company_id = ' . $dealer_company_id, array (
                    'dealer_company_id', 
                    'override_toner_price' 
            ))
                ->where($where);
            if ($where_compatible)
            {
                $select->where("CONCAT(mdm.manufacturer_name,' ',md.printer_model) LIKE '%" . $where_compatible . "%'");
            }
            $select->group('t.toner_id');
            $select->order(array (
                    'tm.manufacturer_name', 
                    'md.printer_model', 
                    't.toner_SKU' 
            ));
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $count = count($result);
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit;
            if ($start < 0)
                $start = 0;
                
                // select master devices
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    't' => 'toner' 
            ), array (
                    't.toner_id', 
                    't.toner_SKU', 
                    't.toner_yield', 
                    't.toner_price', 
                    '(null) AS is_added', 
                    'GROUP_CONCAT(CONCAT(mdm.manufacturer_name," ",md.printer_model) SEPARATOR "; ") AS machine_compatibility' 
            ))
                ->joinLeft(array (
                    'tm' => 'manufacturer' 
            ), 'tm.manufacturer_id = t.manufacturer_id', array (
                    'tm.manufacturer_name AS toner_manufacturer' 
            ))
                ->joinLeft(array (
                    'dt' => 'device_toner' 
            ), 'dt.toner_id = t.toner_id')
                ->joinLeft(array (
                    'md' => 'master_device' 
            ), 'md.master_device_id = dt.master_device_id')
                ->joinLeft(array (
                    'mdm' => 'manufacturer' 
            ), 'mdm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'mdm.manufacturer_name' 
            ))
                ->joinLeft(array (
                    'tc' => 'toner_color' 
            ), 'tc.toner_color_id = t.toner_color_id')
                ->joinLeft(array (
                    'pt' => 'part_type' 
            ), 'pt.part_type_id = t.part_type_id')
                ->joinLeft(array (
                    'dto' => 'dealer_toner_override' 
            ), 'dto.toner_id = t.toner_id AND dto.dealer_company_id = ' . $dealer_company_id, array (
                    'dealer_company_id', 
                    'override_toner_price' 
            ))
                ->where($where);
            if ($where_compatible)
            {
                $select->where("CONCAT(mdm.manufacturer_name,' ',md.printer_model) LIKE '%" . $where_compatible . "%'");
            }
            $select->group('t.toner_id');
            $select->order($sidx . ' ' . $sord);
            $select->limit($limit, $start);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $formdata->page = $page;
            $formdata->total = $total_pages;
            $formdata->records = $count;
            if (count($result) > 0)
            {
                $i = 0;
                $price_margin = ($this->getPricingMargin('dealer', $dealer_company_id) / 100) + 1;
                foreach ( $result as $row )
                {
                    $type_name = ucwords(strtolower($row ['type_name']));
                    if ($type_name == "Oem")
                    {
                        $type_name = "OEM";
                    }
                    
                    $formdata->rows [$i] ['id'] = $row ['toner_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['toner_id'], 
                            $row ['toner_SKU'], 
                            ucwords(strtolower($row ['toner_manufacturer'])), 
                            $type_name, 
                            ucwords(strtolower($row ['toner_color_name'])), 
                            $row ['toner_yield'], 
                            ucwords(strtolower($row ['machine_compatibility'])), 
                            money_format('%i', $row ['toner_price'] * $price_margin), 
                            ($row ['override_toner_price'] > 0 ? money_format('%i', $row ['override_toner_price']) : null), 
                            null, 
                            $row ['master_device_id'] 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            echo $e->getMessage();
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function usertonersAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $master_device_id = $this->_getParam('deviceid', false);
        $user_id = $this->user_id;
        $filter = $this->_getParam('filter', false);
        $criteria = $this->_getParam('criteria', false);
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 1;
        
        $where = '';
        $where_compatible = '';
        if (! empty($filter) && ! empty($criteria) && $filter != 'machine_compatibility')
        {
            if ($filter == "manufacturer_name")
            {
                $filter = "tm.manufacturer_name";
            }
            $where = $filter . ' LIKE("%' . $criteria . '%")';
        }
        else if (! empty($filter) && $filter == 'machine_compatibility')
        {
            if (strtolower($criteria) == "hp")
            {
                $criteria = "hewlett-packard";
            }
            $where_compatible = $criteria;
        }
        
        try
        {
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    't' => 'toner' 
            ), array (
                    't.toner_id', 
                    't.toner_SKU', 
                    't.toner_yield', 
                    't.toner_price', 
                    'GROUP_CONCAT(CONCAT(mdm.manufacturer_name," ",md.printer_model) SEPARATOR "; ") AS machine_compatibility' 
            ))
                ->joinLeft(array (
                    'tm' => 'manufacturer' 
            ), 'tm.manufacturer_id = t.manufacturer_id', array (
                    'tm.manufacturer_name AS toner_manufacturer' 
            ))
                ->joinLeft(array (
                    'dt' => 'device_toner' 
            ), 'dt.toner_id = t.toner_id')
                ->joinLeft(array (
                    'md' => 'master_device' 
            ), 'md.master_device_id = dt.master_device_id')
                ->joinLeft(array (
                    'mdm' => 'manufacturer' 
            ), 'mdm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'mdm.manufacturer_name' 
            ))
                ->joinLeft(array (
                    'tc' => 'toner_color' 
            ), 'tc.toner_color_id = t.toner_color_id')
                ->joinLeft(array (
                    'pt' => 'part_type' 
            ), 'pt.part_type_id = t.part_type_id')
                ->joinLeft(array (
                    'uto' => 'user_toner_override' 
            ), 'uto.toner_id = t.toner_id AND uto.user_id = ' . $user_id, array (
                    'user_id', 
                    'override_toner_price' 
            ));
            if ($where != '')
            {
                $select->where($where);
            }
            if (! empty($where_compatible))
            {
                $select->where("CONCAT(mdm.manufacturer_name,' ',md.printer_model) LIKE '%" . $where_compatible . "%'");
            }
            $select->group('t.toner_id');
            $select->order(array (
                    'tm.manufacturer_name', 
                    'md.printer_model', 
                    't.toner_SKU' 
            ));
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $count = count($result);
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit;
            if ($start < 0)
                $start = 0;
            
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    't' => 'toner' 
            ), array (
                    't.toner_id', 
                    't.toner_SKU', 
                    't.toner_yield', 
                    't.toner_price', 
                    'GROUP_CONCAT(CONCAT(mdm.manufacturer_name," ",md.printer_model) SEPARATOR "; ") AS machine_compatibility' 
            ))
                ->joinLeft(array (
                    'tm' => 'manufacturer' 
            ), 'tm.manufacturer_id = t.manufacturer_id', array (
                    'tm.manufacturer_name AS toner_manufacturer' 
            ))
                ->joinLeft(array (
                    'dt' => 'device_toner' 
            ), 'dt.toner_id = t.toner_id')
                ->joinLeft(array (
                    'md' => 'master_device' 
            ), 'md.master_device_id = dt.master_device_id')
                ->joinLeft(array (
                    'mdm' => 'manufacturer' 
            ), 'mdm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'mdm.manufacturer_name' 
            ))
                ->joinLeft(array (
                    'tc' => 'toner_color' 
            ), 'tc.toner_color_id = t.toner_color_id')
                ->joinLeft(array (
                    'pt' => 'part_type' 
            ), 'pt.part_type_id = t.part_type_id')
                ->joinLeft(array (
                    'uto' => 'user_toner_override' 
            ), 'uto.toner_id = t.toner_id AND uto.user_id = ' . $user_id, array (
                    'user_id', 
                    'override_toner_price' 
            ));
            if ($where != '')
            {
                $select->where($where);
            }
            if (! empty($where_compatible))
            {
                $select->where("CONCAT(mdm.manufacturer_name,' ',md.printer_model) LIKE '%" . $where_compatible . "%'");
            }
            $select->group('t.toner_id');
            $select->order($sidx . ' ' . $sord);
            $select->limit($limit, $start);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $formdata->page = $page;
            $formdata->total = $total_pages;
            $formdata->records = $count;
            if (count($result) > 0)
            {
                $i = 0;
                $price_margin = ($this->getPricingMargin('dealer', $this->dealer_company_id) / 100) + 1;
                foreach ( $result as $row )
                {
                    $type_name = ucwords(strtolower($row ['type_name']));
                    if ($type_name == "Oem")
                    {
                        $type_name = "OEM";
                    }
                    $formdata->rows [$i] ['id'] = $row ['toner_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['toner_id'], 
                            $row ['toner_SKU'], 
                            ucwords(strtolower($row ['toner_manufacturer'])), 
                            $type_name, 
                            ucwords(strtolower($row ['toner_color_name'])), 
                            $row ['toner_yield'], 
                            money_format('%i', $row ['toner_price'] * $price_margin), 
                            ($row ['override_toner_price'] > 0 ? money_format('%i', $row ['override_toner_price']) : null), 
                            null, 
                            $row ['master_device_id'], 
                            ucwords(strtolower($row ['machine_compatibility'])) 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            echo $e->getMessage();
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    protected function getmodelsAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $terms = explode(" ", trim($_REQUEST ["searchText"]));
        $searchTerm = "%";
        foreach ( $terms as $term )
        {
            $searchTerm .= "$term%";
        }
        // Fetch Devices like term
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $sql = "SELECT concat(manufacturer_name, ' ', printer_model) as device_name, master_device_id, manufacturer_name, printer_model FROM manufacturer
        JOIN master_device on master_device.mastdevice_manufacturer = manufacturer.manufacturer_id
        WHERE concat(manufacturer_name, ' ', printer_model) LIKE '%$searchTerm%' AND manufacturer.is_deleted = 0 ORDER BY device_name ASC LIMIT 10;";
        
        $results = $db->fetchAll($sql);
        // $results is an array of device names
        $devices = array ();
        foreach ( $results as $row )
        {
            $deviceName = $row ["manufacturer_name"] . " " . $row ["printer_model"];
            $deviceName = ucwords(strtolower($deviceName));
            $devices [] = array (
                    "label" => $deviceName, 
                    "value" => $row ["master_device_id"], 
                    "manufacturer" => ucwords(strtolower($row ["manufacturer_name"])) 
            );
        }
        $lawl = Zend_Json::encode($devices);
        print $lawl;
    }

    public function managematchupsAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = 'Manage Printer Matchups';
        $this->view->source = "PrintFleet";
        $this->view->pf_model_id = '';
        
        // fill manufacturers dropdown
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers = $manufacturersTable->fetchAll('is_deleted = false', 'manufacturer_name');
        $this->view->manufacturer_list = $manufacturers;
        
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            // print_r($formData); die;
            

            if (isset($formData ['ticket_id']))
            {
                $this->view->form_mode = $formData ['form_mode'];
                $this->view->ticket_id = $formData ['ticket_id'];
                $this->view->devices_pf_id = $formData ['devices_pf_id'];
            }
            
            $db->beginTransaction();
            try
            {
                if (isset($formData ['hdnIdArray']))
                {
                    $master_matchup_pfTable = new Proposalgen_Model_DbTable_PFMasterMatchup();
                    $id_array = (explode(",", $formData ['hdnIdArray']));
                    $this->view->criteria_filter = $formData ['criteria_filter'];
                    
                    foreach ( $id_array as $key )
                    {
                        $devices_pf_id = $formData ['hdnDevicesPFID' . $key];
                        $master_device_id = $formData ['hdnMasterDevicesValue' . $key];
                        
                        if ($devices_pf_id > 0 && $master_device_id > 0)
                        {
                            $master_matchup_pfData ['master_device_id'] = $master_device_id;
                            
                            // check to see if matchup exists for devices_pf_id
                            $where = $master_matchup_pfTable->getAdapter()->quoteInto('devices_pf_id = ?', $devices_pf_id, 'INTEGER');
                            $matchup = $master_matchup_pfTable->fetchRow($where);
                            
                            if (count($matchup) > 0)
                            {
                                $master_matchup_pfTable->update($master_matchup_pfData, $where);
                            }
                            else
                            {
                                $master_matchup_pfData ['devices_pf_id'] = $devices_pf_id;
                                $master_matchup_pfTable->insert($master_matchup_pfData);
                            }
                        }
                        else if ($devices_pf_id > 0)
                        {
                            // no matchup set so remove any records for device
                            $where = $master_matchup_pfTable->getAdapter()->quoteInto('devices_pf_id = ?', $devices_pf_id, 'INTEGER');
                            $master_matchup_pfTable->delete($where);
                        }
                        unset($master_matchup_pfData);
                    }
                    $db->commit();
                    $this->view->message = "<p>The matchups have been saved.</p>";
                }
                else
                {
                    // set criteria = pf model id
                    $device_pfTable = new Proposalgen_Model_DbTable_PFDevices();
                    $device_pf = $device_pfTable->fetchRow('devices_pf_id = ' . $formData ['devices_pf_id']);
                    
                    if (count($device_pf) > 0)
                    {
                        $this->view->pf_model_id = $device_pf ['pf_model_id'];
                    }
                }
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->view->message = "Error";
            }
        }
    }

    public function matchuplistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $devices_pf_id = $this->_getParam('id', null);
        $filter = $this->_getParam('filter', false);
        $criteria = $this->_getParam('criteria', false);
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 'pf_db_manufacturer';
        
        try
        {
            $where = '';
            $master_device_list = '';
            if ($devices_pf_id > 0)
            {
                $where = 'dpf.devices_pf_id = ' . $devices_pf_id;
            }
            else if (! empty($filter) && ! empty($criteria))
            {
                if ($filter == 'model')
                {
                    $where = 'pf_model_id LIKE("%' . $criteria . '%")';
                }
                else if ($filter == 'printer')
                {
                    $where = 'CONCAT(pf_db_manufacturer, " ", pf_db_devicename) LIKE("%' . $criteria . '%")';
                }
            }
            
            // get pf device list filter by manufacturer
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'dpf' => 'devices_pf' 
            ))
                ->joinLeft(array (
                    'mmpf' => 'master_matchup_pf' 
            ), 'mmpf.devices_pf_id = dpf.devices_pf_id', array (
                    'master_device_id' 
            ))
                ->joinLeft(array (
                    'md' => 'master_device' 
            ), 'md.master_device_id = mmpf.master_device_id', array (
                    'printer_model' 
            ))
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'manufacturer_name' 
            ));
            
            if (! empty($where))
            {
                $select->where($where);
            }
            
            $select->group(array (
                    'dpf.devices_pf_id' 
            ));
            $select->order(array (
                    'pf_db_manufacturer ASC' 
            ));
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $count = count($result);
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit;
            if ($start < 0)
                $start = 0;
            
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'dpf' => 'devices_pf' 
            ), array (
                    'devices_pf_id', 
                    'pf_model_id', 
                    'pf_printer' => new Zend_Db_Expr("CONCAT(pf_db_manufacturer, ' ', pf_db_devicename)") 
            ))
                ->joinLeft(array (
                    'mmpf' => 'master_matchup_pf' 
            ), 'mmpf.devices_pf_id = dpf.devices_pf_id', array (
                    'master_device_id' 
            ))
                ->joinLeft(array (
                    'md' => 'master_device' 
            ), 'md.master_device_id = mmpf.master_device_id', array (
                    'printer_model' 
            ))
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'manufacturer_name' 
            ));
            
            if (! empty($where))
            {
                $select->where($where);
            }
            
            $select->group(array (
                    'dpf.devices_pf_id' 
            ));
            $select->order($sidx . ' ' . $sord);
            $select->limit($limit, $start);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            $formdata->page = $page;
            $formdata->total = $total_pages;
            $formdata->records = $count;
            
            // return results
            if (count($result) > 0)
            {
                $i = 0;
                foreach ( $result as $row )
                {
                    $mapped_to = '';
                    $mapped_to_id = '';
                    $mapped_to_manufacturer = '';
                    $devices_pf_id = $row ['devices_pf_id'];
                    
                    // set up mapped to suggestions
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array (
                            'mmpf' => 'master_matchup_pf' 
                    ))
                        ->joinLeft(array (
                            'md' => 'master_device' 
                    ), 'md.master_device_id = mmpf.master_device_id')
                        ->joinLeft(array (
                            'm' => 'manufacturer' 
                    ), 'm.manufacturer_id = md.mastdevice_manufacturer')
                        ->where('mmpf.devices_pf_id = ' . $devices_pf_id);
                    $stmt = $db->query($select);
                    $master_devices = $stmt->fetchAll();
                    
                    if (count($master_devices) > 0)
                    {
                        $mapped_to = $master_devices [0] ['printer_model'];
                        $mapped_to_id = $master_devices [0] ['master_device_id'];
                        $mapped_to_manufacturer = $master_devices [0] ['manufacturer_name'];
                    }
                    
                    $formdata->rows [$i] ['id'] = $row ['devices_pf_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $devices_pf_id, 
                            $row ['master_device_id'], 
                            $row ['pf_model_id'], 
                            ucwords(strtolower($row ['pf_printer'])), 
                            ucwords(strtolower($row ['manufacturer_name'] . ' ' . $row ['printer_model'])), 
                            ucwords(strtolower($mapped_to)), 
                            $mapped_to_id, 
                            ucwords(strtolower($mapped_to_manufacturer)) 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            echo $e->getMessage();
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function managereplacementsAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = 'Manage Replacement Printers';
        $this->view->repop = false;
        
        $form = new Proposalgen_Form_ReplacementPrinter(null, '');
        $this->view->replacement_form = $form;
        
        // fill manufacturer dropdown
        $list = "";
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers = $manufacturersTable->fetchAll('is_deleted = 0', 'manufacturer_name');
        $currElement = $form->getElement('manufacturer_id');
        $currElement->addMultiOption('0', 'Select Manufacturer');
        foreach ( $manufacturers as $row )
        {
            $currElement->addMultiOption($row ['manufacturer_id'], ucwords(strtolower($row ['manufacturer_name'])));
        }
        
        // fill replacement_category
        $currElement = $form->getElement('replacement_category');
        $currElement->addMultiOption('', 'Select a Category');
        $currElement->addMultiOption('BLACK & WHITE', 'Black & White');
        $currElement->addMultiOption('BLACK & WHITE MFP', 'Black & White MFP');
        $currElement->addMultiOption('COLOR', 'Color');
        $currElement->addMultiOption('COLOR MFP', 'Color MFP');
        
        if ($this->_request->isPost())
        {
            try
            {
                $replacementTable = new Proposalgen_Model_DbTable_ReplacementDevices();
                $formData = $this->_request->getPost();
                $form_mode = $formData ['form_mode'];
                $hdnIds = $formData ['hdnIds'];
                
                if ($form_mode == "delete")
                {
                    $ids = explode(",", $hdnIds);
                    
                    foreach ( $ids as $key )
                    {
                        if (isset($formData ['jqg_grid_list_' . $key]) && $formData ['jqg_grid_list_' . $key] == "on")
                        {
                            $replacement_category = $formData ['replacement_category_' . $key];
                            $where = $replacementTable->getAdapter()->quoteInto('replacement_category = ?', $replacement_category);
                            $replacement = $replacementTable->fetchAll($where);
                            
                            if (count($replacement) > 1)
                            {
                                $where = $replacementTable->getAdapter()->quoteInto('master_device_id = ?', $key, 'INTEGER');
                                $replacementTable->delete($where);
                                $this->view->message = "<p>The selected printer(s) are no longer marked as replacement printers.</p>";
                            }
                            else
                            {
                                $message = "<p>Could not delete all replacement printers as one or more was the last printer for it's replacement category.</p>";
                            }
                        }
                    }
                }
                
                if (empty($message))
                {
                    $db->commit();
                }
                else
                {
                    $db->rollback();
                    $this->view->message = $message;
                }
            }
            catch ( Exception $e )
            {
                $db->rollback();
                Throw new exception("An error has occurred deleting replacement printers.", 0, $e);
            }
        }
    }

    public function savereplacementprinterAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $form = new Proposalgen_Form_ReplacementPrinter(null, '');
        $formData = $this->_getAllParams();
        
        $hdnManId = $formData ['hdnManId'];
        $hdnMasId = $formData ['hdnMasId'];
        $manufacturer_id = $formData ['manufacturer_id'];
        $printer_model = $formData ['printer_model'];
        $hdnOriginalCategory = $formData ['hdnOriginalCategory'];
        $replacement_category = $formData ['replacement_category'];
        $print_speed = $formData ['print_speed'];
        $resolution = $formData ['resolution'];
        $monthly_rate = $formData ['monthly_rate'];
        $form_mode = $formData ['form_mode'];
        
        // validation
        $validation = '';
        if ($manufacturer_id == "")
        {
            $validation = "manufacturer_id,You must have a manufacturer selected.";
        }
        else if ($printer_model == "")
        {
            $validation = "printer_model,You must have a printer model selected.";
        }
        else if ($replacement_category == "")
        {
            $validation = "replacement_category,You must select a replacement category.";
        }
        else if ($print_speed == "")
        {
            $validation = "print_speed,You must enter a valid print speed.";
        }
        else if ($resolution == "")
        {
            $validation = "resolution,You must enter a valid resolution.";
        }
        else if ($monthly_rate == "")
        {
            $validation = "monthly_rate,You must enter a valid monthly rate.";
        }
        
        if (empty($validation))
        {
            $message = '';
            
            $db->beginTransaction();
            try
            {
                $replacementTable = new Proposalgen_Model_DbTable_ReplacementDevices();
                $replacement_devicesTable = new Proposalgen_Model_DbTable_ReplacementDevices();
                $replacement_devicesData = array (
                        'replacement_category' => strtoupper($replacement_category), 
                        'print_speed' => $print_speed, 
                        'resolution' => $resolution, 
                        'monthly_rate' => $monthly_rate 
                );
                
                if ($form_mode == "add")
                {
                    // check to see if replacement device exists
                    $where = $replacement_devicesTable->getAdapter()->quoteInto('master_device_id = ?', $printer_model, 'INTEGER');
                    $replacement_devices = $replacement_devicesTable->fetchRow($where);
                    if (count($replacement_devices) > 0)
                    {
                        $replacement_devicesTable->update($replacement_devicesData, $where);
                        $this->view->message = "<p>The replacement printer has been updated.</p>";
                    }
                    else
                    {
                        $replacement_devicesData ['master_device_id'] = $printer_model;
                        $replacement_devicesTable->insert($replacement_devicesData);
                        $this->view->message = "<p>The replacement printer has been added.</p>";
                    }
                }
                else if ($form_mode == "edit")
                {
                    $is_valid = true;
                    if (strtoupper($hdnOriginalCategory) != strtoupper($replacement_category))
                    {
                        $where = $replacementTable->getAdapter()->quoteInto('replacement_category = ?', $hdnOriginalCategory);
                        $replacement = $replacementTable->fetchAll($where);
                        
                        if (count($replacement) > 1)
                        {
                            $is_valid = true;
                        }
                        else
                        {
                            $is_valid = false;
                            $message = "<p>You are not able to update the Replacement Category on this printer as it's the last printer of the " . ucwords(strtolower($hdnOriginalCategory)) . " category.</p>";
                        }
                    }
                    
                    if ($is_valid == true)
                    {
                        $where = $replacement_devicesTable->getAdapter()->quoteInto('master_device_id = ?', $printer_model, 'INTEGER');
                        $replacement_devicesTable->update($replacement_devicesData, $where);
                        $this->view->message = "<p>The replacement printer has been updated.</p>";
                    }
                }
                
                if ($message == "")
                {
                    $db->commit();
                }
                else
                {
                    $db->rollback();
                    $this->view->message = $message;
                }
            }
            catch ( Exception $e )
            {
                $db->rollback();
                Throw new exception("Error: error in manage replacements.", 0, $e);
            }
        }
        else
        {
            // if formdata was not valid, repopulate form(error messages from
            // validations are automatically added)
            $this->view->message = $validation;
        }
        $this->view->data = $this->view->message;
    }

    public function replacementprinterslistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        try
        {
            // get pf device list filter by manufacturer
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'md' => 'master_device' 
            ), array (
                    'master_device_id', 
                    'mastdevice_manufacturer', 
                    'printer_model' 
            ))
                ->join(array (
                    'rd' => 'replacement_devices' 
            ), 'rd.master_device_id = md.master_device_id', array (
                    'replacement_category' 
            ))
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                    'manufacturer_name' 
            ));
            $select->order(array (
                    'manufacturer_name ASC', 
                    'printer_model ASC' 
            ));
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            // return results
            if (count($result) > 0)
            {
                $i = 0;
                foreach ( $result as $row )
                {
                    $formdata->rows [$i] ['id'] = $row ['master_device_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['mastdevice_manufacturer'], 
                            $row ['master_device_id'], 
                            ucwords(strtolower($row ['manufacturer_name'] . ' ' . $row ['printer_model'])), 
                            ucwords(strtolower($row ['replacement_category'])), 
                            null 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            Throw new exception("Error: Unable to find replacement device.", 0, $e);
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function replacementdetailsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $device_id = $this->_getParam('deviceid', false);
        
        try
        {
            if ($device_id > 0)
            {
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        'rd' => 'replacement_devices' 
                ))
                    ->where('master_device_id = ?', $device_id, 'INTEGER');
                $stmt = $db->query($select);
                $row = $stmt->fetchAll();
                
                $formdata = array (
                        'replacement_category' => $row [0] ['replacement_category'], 
                        'print_speed' => $row [0] ['print_speed'], 
                        'resolution' => $row [0] ['resolution'], 
                        'monthly_rate' => $row [0] ['monthly_rate'] 
                );
            }
            else
            {
                // empty form values
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Error: Unable to find replacement device.", 0, $e);
        } // end catch
        

        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function convertDate ($date)
    {
        if ($date)
            return (strftime("%x", strtotime($date)));
        else
            return " ";
    }

    public function transferreportsAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = 'Transfer Reports';
        $where = null;
        $order = null;
        $count = null;
        $offset = null;
        $id_list = null;
        
        $User = Proposalgen_Model_Mapper_User::getInstance();
        
        //*************************************************
        // postback
        //*************************************************
        

        if ($this->_request->isPost())
        {
            $reportTable = new Proposalgen_Model_DbTable_Reports();
            $formData = $this->_request->getPost();
            // print_r($formData); die;
            

            $db->beginTransaction();
            try
            {
                $reportMapper = Proposalgen_Model_Mapper_Report::getInstance();
                $report_id = $formData ['reportlist'];
                
                // check transfer type
                if ($formData ['transfertype'] == 'transfer')
                {
                    $new_user_id = $formData ['newuser'];
                    
                    // update report
                    

                    $reportMapper = Proposalgen_Model_Mapper_Report::getInstance();
                    $report = Proposalgen_Model_Mapper_Report::getInstance()->find($report_id);
                    $report->setReportId($report_id);
                    $report->setUserId($new_user_id);
                    $reportMapper->save($report);
                    
                    // update unknown_device_instance records
                    $udiTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                    $data ['user_id'] = $new_user_id;
                    $where = $udiTable->getAdapter()->quoteInto('report_id = ?', $report_id, 'INTEGER');
                    
                    // Perform the update.
                    $udiTable->update($data, $where);
                    
                    $this->_helper->flashMessenger(array (
                            "success" => "Report Transfer Complete." 
                    ));
                }
                else if ($formData ['transfertype'] == 'clone')
                {
                    $reportMapper->cloneReport($report_id, $formData ['hdntransferlist']);
                    
                    $this->_helper->flashMessenger(array (
                            "success" => "Report Cloning Complete." 
                    ));
                }
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->_helper->flashMessenger(array (
                        "error" => "There was an error while trying to transfer the report. Please contact your administrator." 
                ));
            }
        }
        
        //*************************************************
        // get users
        //*************************************************
        

        // if system admin (all users) else if dealer admin or standard user (company users only)
        $where = null;
        if (in_array("Standard User", $this->privilege))
        {
            $where = 'u.user_id = ' . $this->user_id;
        }
        else if (in_array("Dealer Admin", $this->privilege))
        {
            $where = 'dealer_company_id = ' . $this->dealer_company_id;
        }
        else if (in_array("System Admin", $this->privilege))
        {
            // nothing else
        }
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'u' => 'users' 
        ))
            ->joinLeft(array (
                'up' => 'user_privileges' 
        ), 'u.user_id = up.user_id');
        if ($where)
        {
            $select->where($where);
        }
        $select->order('username ASC');
        $stmt = $db->query($select);
        $users = $stmt->fetchAll();
        $this->view->users_list = $users;
        
        //*************************************************
        // get to users
        //*************************************************
        

        // if system admin (all users) else if dealer admin or standard user (company users only)
        $where = null;
        if (! in_array("System Admin", $this->privilege))
        {
            $where = 'dealer_company_id=' . $this->dealer_company_id;
        }
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'u' => 'users' 
        ))
            ->joinLeft(array (
                'up' => 'user_privileges' 
        ), 'u.user_id = up.user_id');
        if ($where)
        {
            $select->where($where);
        }
        $select->order('username ASC');
        $stmt = $db->query($select);
        $users = $stmt->fetchAll();
        $this->view->to_users_list = $users;
        
        //*************************************************
        // get companies
        //*************************************************
        

        // if system admin (show all reports) else if dealer admin (above users reports only) else if standard user (show only users reports)
        $where = 'dealer_company_id > 1 ';
        if (! in_array("System Admin", $this->privilege))
        {
            $where .= 'AND dealer_company_id = ' . $this->dealer_company_id;
        }
        $order = 'company_name ASC';
        
        $companiesTable = new Proposalgen_Model_DbTable_DealerCompany();
        $companies = $companiesTable->fetchAll($where, $order, $count, $offset);
        $this->view->company_list = $companies;
        
        //*************************************************
        // get reports
        //*************************************************
        

        // if system admin (show all reports) else if dealer admin (above users reports only) else if standard user (show only users reports)
        $where = null;
        if (! in_array("Standard User", $this->privilege))
        {
            //build id string
            foreach ( $this->view->users_list as $key )
            {
                if ($id_list)
                {
                    $id_list .= ',';
                }
                $id_list .= $key ['user_id'];
            }
            $where = 'user_id IN (' . $id_list . ')';
        }
        else
        {
            $where = 'user_id=' . $this->user_id;
        }
        $order = 'date_created DESC';
        
        $reportsTable = new Proposalgen_Model_DbTable_Reports();
        $reports = $reportsTable->fetchAll($where, $order, $count, $offset);
        $this->view->reports_list = $reports;
    }

    public function filterreportslistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $filterfield = $this->_getParam('filterfield', null);
        $filtervalue = $this->_getParam('filtervalue', null);
        $startdate = $this->_getParam('startdate', null);
        $enddate = $this->_getParam('enddate', null);
        
        try
        {
            $where = null;
            if ($filterfield == 'date_created' && $startdate && $enddate)
            {
                $where = 'r.date_created BETWEEN "' . date("Y-m-d", strtotime($startdate)) . '" AND "' . date("Y-m-d", strtotime($enddate)) . '"';
            }
            else if ($filterfield && $filtervalue)
            {
                if ($filterfield == 'user_id')
                {
                    $filterfield = 'r.user_id';
                }
                $where = $filterfield . ' = ' . $filtervalue;
            }
            
            if (in_array("Standard User", $this->privilege))
            {
                $where = 'r.user_id = ' . $this->user_id;
            }
            
            // select reports
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'r' => 'reports' 
            ))
                ->joinLeft(array (
                    'u' => 'users' 
            ), 'u.user_id = r.user_id', array (
                    'username' 
            ));
            if ($where)
            {
                $select->where($where);
            }
            $select->order(array (
                    'date_created DESC', 
                    'customer_company_name ASC' 
            ));
            //echo $select; die;
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            if (count($result) > 0)
            {
                $i = 0;
                foreach ( $result as $row )
                {
                    $formdata->rows [$i] ['id'] = $row ['report_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['report_id'], 
                            $row ['customer_company_name'] . ' (' . $row ['username'] . ' on ' . date("m-d-Y", strtotime($row ['date_created'])) . ')' 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            //echo $e->getMessage();
            $formdata = array ();
        }
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function filteruserslistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $filter = $this->_getParam('filter', 'all');
        
        try
        {
            $where = null;
            if (strtolower($filter) == 'my')
            {
                $where = 'u.dealer_company_id = ' . $this->dealer_company_id;
            }
            elseif (strtolower($filter) == 'all')
            {
                //nothing to add
            }
            else
            {
                $where = 'dealer_company_id = ' . $filter;
            }
            
            // select users
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'u' => 'users' 
            ))
                ->joinLeft(array (
                    'up' => 'user_privileges' 
            ), 'u.user_id = up.user_id');
            if ($where)
            {
                $select->where($where);
            }
            $select->order('username ASC');
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            if (count($result) > 0)
            {
                $i = 0;
                foreach ( $result as $row )
                {
                    $formdata->rows [$i] ['id'] = $row ['user_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['user_id'], 
                            strtolower($row ['username']) 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            //echo $e->getMessage();
            $formdata = array ();
        }
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function filtercompanieslistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        try
        {
            $where = 'dealer_company_id > 1 ';
            if (! in_array("System Admin", $this->privilege))
            {
                $where .= 'AND dealer_company_id = ' . $this->dealer_company_id;
            }
            
            // select users
            $select = new Zend_Db_Select($db);
            $select = $db->select()->from(array (
                    'dc' => 'dealer_company' 
            ));
            if ($where)
            {
                $select->where($where);
            }
            $select->order('company_name ASC');
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            if (count($result) > 0)
            {
                $i = 0;
                foreach ( $result as $row )
                {
                    $formdata->rows [$i] ['id'] = $row ['dealer_company_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['dealer_company_id'], 
                            $row ['company_name'] 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            //echo $e->getMessage();
            $formdata = array ();
        }
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }
} //end class AdminController
