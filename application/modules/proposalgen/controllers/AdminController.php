<?php

/**
 * Admin Controller: This controller handles all administrator actions.
 *
 * @author Chris Garrah
 */
class Proposalgen_AdminController extends Zend_Controller_Action
{
    protected $config;

    public function setConfig ($config)
    {
        $this->config = $config;
    }

    public function getConfig ()
    {
        return $this->config;
    }

    function init ()
    {
        $this->config = Zend_Registry::get('config');
        $this->initView();
        $this->view->app       = $this->config->app;
        $this->view->user      = Zend_Auth::getInstance()->getIdentity();
        $this->view->user_id   = Zend_Auth::getInstance()->getIdentity()->id;
        $this->view->privilege = array('System Admin'); //Zend_Auth::getInstance()->getIdentity()->privileges;
        $this->user_id         = Zend_Auth::getInstance()->getIdentity()->id;
        $this->privilege       = array('System Admin'); //Zend_Auth::getInstance()->getIdentity()->privileges;
        //$this->dealer_company_id = Zend_Auth::getInstance()->getIdentity()->dealer_company_id;
        $this->MPSProgramName       = $this->config->app->MPSProgramName;
        $this->view->MPSProgramName = $this->config->app->MPSProgramName;
        $this->ApplicationName      = $this->config->app->ApplicationName;
    }

    /**
     * Default action - Show the list of admin options
     */
    public function indexAction ()
    {
        $this->view->title    = "Admin Console";
        $session              = new Zend_Session_Namespace('proposalgenerator_report');
        $config               = Zend_Registry::get('config');
        $this->MPSProgramName = $config->app->MPSProgramName;

    } // end indexAction


    /**
     * The managecompaniesAction provides a list of active companies for the
     * system admin to choose from and manage.
     * The details form gets posted back
     * and updated or inserted if new
     */
    public function managecompaniesAction ()
    {
        $db                = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = 'Manage Companies';
        $date              = date('Y-m-d H:i:s T');
        $this->view->repop = false;

        // add company form;
        $form = new Proposalgen_Form_Companies(null, "edit");

        // fill companies dropdown
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $dealer_companies    = $dealer_companyTable->fetchAll('company_name != "MASTER"', 'company_name');
        $currElement         = $form->getElement('select_company');
        $currElement->addMultiOption("0", "Add New Company");
        foreach ($dealer_companies as $row)
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
                $date                = date('Y-m-d H:i:s T');
                $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
                $dealer_company_id   = $formData ['select_company'];
                $dealer_company_name = $formData ['company_name'];
                $pricing_margin      = $formData ['pricing_margin'];

                $db->beginTransaction();
                try
                {
                    if (array_key_exists('save_company', $formData) && $formData ['save_company'] == "Save")
                    {
                        // company data
                        $companyData = array(
                            'company_name'         => $dealer_company_name,
                            'company_logo'         => null,
                            'company_report_color' => null,
                            'dc_pricing_margin'    => $pricing_margin
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
                            $where          = $dealer_companyTable->getAdapter()->quoteInto('company_name = ?', $dealer_company_name);
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
                            $where      = $usersTable->getAdapter()->quoteInto('dealer_company_id = ?', $dealer_company_id);
                            $users      = $usersTable->fetchAll($where);
                            // Delete all the users for the company
                            foreach ($users as $key)
                            {
                                $selUserID = $key ['user_id'];
                                $status    = $this->deleteUser($selUserID);

                                if (!$status)
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
                                $where                      = $dealer_device_overideTable->getAdapter()->quoteInto($criteria, null);
                                $dealer_device_overideTable->delete($where);

                                // dealer toner override
                                $dealer_toner_overideTable = new Proposalgen_Model_DbTable_DealerTonerOverride();
                                $where                     = $dealer_toner_overideTable->getAdapter()->quoteInto($criteria, null);
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
                    $currElement      = $form->getElement('select_company');
                    $currElement->clearMultiOptions();
                    $currElement->addMultiOption('0', 'Add New Company');
                    foreach ($dealer_companies as $row)
                    {
                        $currElement->addMultiOption($row ['dealer_company_id'], ucwords(strtolower($row ['company_name'])));
                    }

                    // reset form
                    $form->getElement('company_name')->setValue('');
                    $form->getElement('pricing_margin')->setValue('25');
                }
                catch (Zend_Db_Exception $e)
                {
                    $db->rollback();
                    $this->view->message = 'Database Error: Company "' . $formData ["company_name"] . '" could not be saved.';
                }
                catch (Exception $e)
                {
                    // CRITICAL UPDATE EXCEPTION
                    $db->rollback();
                    Throw new exception("Critical Company Update Error.", 0, $e);
                } // end catch
            }
            else
            {
                $this->view->repop   = true;
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
                $where               = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $companyid);
                $row                 = $dealer_companyTable->fetchRow($where);

                $formdata = array(
                    'dealer_company_id'    => $row ['dealer_company_id'],
                    'company_name'         => $row ['company_name'],
                    'company_logo'         => $row ['company_logo'],
                    'company_report_color' => $row ['company_report_color'],
                    'pricing_margin'       => $row ['dc_pricing_margin'],
                    'is_deleted'           => $row ['is_deleted']
                );
            }
            else
            {
                $formdata = array();
            }
        }
        catch (Exception $e)
        {
            // CRITICAL EXCEPTION
            Throw new exception("Critical Error: Unable to find company.", 0, $e);
        } // end catch


        // Encode company data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function deleteUser ($selUserID)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        try
        {
            $unknown_devivce_instance = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
            $where                    = $unknown_devivce_instance->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $unknown_devivce_instance->delete($where);

            $report  = new Proposalgen_Model_DbTable_Reports();
            $where   = $report->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $reports = $report->fetchAll($where);

            // Delete all the reports
            foreach ($reports as $key)
            {
                $this->deleteReport($key ['report_id']);
            }

            $devices_pf      = new Proposalgen_Model_DbTable_PFDevices();
            $where           = $devices_pf->getAdapter()->quoteInto('created_by = ?', $selUserID, 'INTEGER');
            $devices_pf_data = array(
                'created_by' => null
            );
            $devices_pf->update($devices_pf_data, $where);

            $pf_device_matchup_users = new Proposalgen_Model_DbTable_PFMatchupUsers();
            $where                   = $pf_device_matchup_users->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $pf_device_matchup_users->delete($where);

            $user_toner_override = new Proposalgen_Model_DbTable_UserTonerOverride();
            $where               = $user_toner_override->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $user_toner_override->delete($where);

            $user_privileges = new Proposalgen_Model_DbTable_UserPrivileges();
            $where           = $user_privileges->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $user_privileges->delete($where);

            $user_device_override = new Proposalgen_Model_DbTable_UserDeviceOverride();
            $where                = $user_device_override->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $user_device_override->delete($where);

            $user_sessions = new Proposalgen_Model_DbTable_UserSessions();
            $where         = $user_sessions->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $user_sessions->delete($where);

            $user  = new Proposalgen_Model_DbTable_Users();
            $where = $user->getAdapter()->quoteInto("user_id = ?", $selUserID, 'INTEGER');
            $user->delete($where);

            return true;
        }
        catch (Exception $e)
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
        $db   = Zend_Db_Table::getDefaultAdapter();
        $type = '';

        // Disable the default layout
        $this->_helper->layout->disableLayout();

        $userID = $this->_getParam('userid', false);

        if (in_array("Dealer Admin", $this->privilege))
        {
            $type              = 'dealer';
            $companyTable      = new Proposalgen_Model_DbTable_DealerCompany();
            $where             = $companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
            $company           = $companyTable->fetchRow($where);
            $dealer_company_id = $company ['company_name'];
        }
        else
        {
            $type              = 'dealer';
            $companyTable      = new Proposalgen_Model_DbTable_DealerCompany();
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
                    ->from(array(
                                'u' => 'users'
                           ))
                    ->join(array(
                                'up' => 'user_privileges'
                           ), 'u.user_id = up.user_id', array(
                                                             'up.priv_id'
                                                        ))
                    ->join(array(
                                'p' => 'privileges'
                           ), 'up.priv_id = p.priv_id', array(
                                                             'p.priv_type'
                                                        ))
                    ->join(array(
                                'dc' => 'dealer_company'
                           ), 'dc.dealer_company_id = u.dealer_company_id', array(
                                                                                 'dc.company_name'
                                                                            ))
                    ->where('u.user_id = ?', $userID);
                $row    = $db->fetchRow($select);

                if ($type != 'dealer')
                {
                    $dealer_company_id = $row ['dealer_company_id'];
                }

                $formdata = array(
                    'dealer_company_id' => $dealer_company_id,
                    'username'          => $row ['username'],
                    'firstname'         => $row ['firstname'],
                    'lastname'          => $row ['lastname'],
                    'phone'             => $row ['telephone'],
                    'email'             => $row ['email'],
                    'password'          => null,
                    'is_activated'      => $row ['is_activated'],
                    'priv_id'           => $row ['priv_id'],
                    'priv_type'         => $row ['priv_type']
                );
            }
            else
            {
                if ($type != 'dealer')
                {
                    $dealer_company_id = 0;
                }

                $formdata = array(
                    'dealer_company_id' => $dealer_company_id,
                    'username'          => '',
                    'firstname'         => '',
                    'lastname'          => '',
                    'phone'             => '',
                    'email'             => '',
                    'password'          => null,
                    'is_activated'      => false,
                    'priv_id'           => 0,
                    'priv_type'         => ''
                );
            }
        }
        catch (Exception $e)
        {
            // CRITICAL EXCEPTION
            Throw new exception("Critical Error:Unable to find user.", 0, $e);
        } // end catch


        // Encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * The printermodelsAction returns a list of printer_models by manufacturer
     * to populate the dropdowns in json format
     */
    public function printermodelsAction ()
    {
        $manufacturer_id     = $_GET ['manufacturerid'];
        $master_devicesTable = new Proposalgen_Model_DbTable_MasterDevice();
        $where               = $master_devicesTable->getAdapter()->quoteInto('manufacturerId = ?', $manufacturer_id, 'INTEGER');
        $result              = $master_devicesTable->fetchAll($where, 'modelName');

        $i        = 0;
        $response = null;
        if (count($result) > 0)
        {
            foreach ($result as $row)
            {
                $response->rows [$i] ['id']   = $row ['id'];
                $response->rows [$i] ['cell'] = array(
                    $row ['id'],
                    ucwords(strtolower($row ['modelName']))
                );
                $i++;
            }
        }
        else
        {
            $response->rows [$i] ['id']   = 0;
            $response->rows [$i] ['cell'] = array(
                0,
                ''
            );
        }
        $this->_helper->json($response);
    }

    /**
     * The devicedetailsAction accepts a parameter for the deviceid and gets the
     * device
     * details from the database.
     * Returns the details array in a json encoded format.
     */
    public function devicedetailsAction ()
    {
        $db       = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);

        try
        {
            if ($deviceID > 0)
            {
                // get toners for device
                $select = $db->select()
                    ->from(array(
                                't' => 'pgen_toners'
                           ))
                    ->join(array(
                                'td' => 'pgen_device_toners'
                           ), 't.id = td.toner_id')
                    ->where('td.master_device_id = ?', $deviceID);
                $stmt   = $db->query($select);

                $result      = $stmt->fetchAll();
                $toner_array = '';
                foreach ($result as $key)
                {
                    if (!empty($toner_array))
                    {
                        $toner_array .= ",";
                    }
                    $toner_array .= "'" . $key ['toner_id'] . "'";
                }

                $select      = new Zend_Db_Select($db);
                $select      = $db->select()
                    ->from(array(
                                'md' => 'pgen_master_devices'
                           ))
                    ->joinLeft(array(
                                    'm' => 'manufacturers'
                               ), 'm.id = md.manufacturerId')
                    ->joinLeft(array(
                                    'rd' => 'pgen_replacement_devices'
                               ), 'rd.master_device_id = md.id')
                    ->where('md.id = ?', $deviceID);
                $stmt        = $db->query($select);
                $row         = $stmt->fetchAll();
                $launch_date = new Zend_Date($row [0] ['launchDate'], "yyyy/mm/dd HH:ii:ss");
                $formData    = array(
                    'launch_date'           => $launch_date->toString('mm/dd/yyyy'),
                    'toner_config_id'       => $row [0] ['tonerConfigId'],
                    'is_copier'             => $row [0] ['isCopier'] ? true : false,
                    'is_scanner'            => $row [0] ['isScanner'] ? true : false,
                    'is_fax'                => $row [0] ['isFax'] ? true : false,
                    'is_duplex'             => $row [0] ['isDuplex'] ? true : false,
                    'is_replacement_device' => $row [0] ['isReplacementDevice'],
                    'watts_power_normal'    => $row [0] ['wattsPowerNormal'],
                    'watts_power_idle'      => $row [0] ['wattsPowerIdle'],
                    'device_price'          => ($row [0] ['cost'] > 0 ? (float)$row [0] ['cost'] : ""),
                    'is_deleted'            => $row [0] ['is_deleted'],
                    'toner_array'           => $toner_array,
                    'replacement_category'  => $row [0] ['replacement_category'],
                    // 'is_letter_legal' => $row [0] ['is_letter_legal'],
                    'print_speed'           => $row [0] ['print_speed'],
                    'resolution'            => $row [0] ['resolution'],
                    // 'paper_capacity' => $row [0] ['paper_capacity'],
                    // 'cpp_above' => $row [0]
                    // ['CPP_above_ten_thousand_pages'],
                    'monthly_rate'          => $row [0] ['monthly_rate'],
                    'is_leased'             => $row [0] ['isLeased'] ? true : false,
                    'leased_toner_yield'    => $row [0] ['leasedTonerYield'],
                    'ppm_black'             => $row [0] ['ppmBlack'],
                    'ppm_color'             => $row [0] ['ppmColor'],
                    'duty_cycle'            => $row [0] ['dutyCycle'],
                );
            }
            else
            {
                // empty form values
                $formData = array();
            }
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device.", 0, $e);
        } // end catch

        $this->_helper->json($formData);
    }

    public function devicereportsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();

        $db               = Zend_Db_Table::getDefaultAdapter();
        $master_device_id = $this->_getParam('id', 0);

        $device_instance_master_devicesTable = new Proposalgen_Model_DbTable_Device_Instance_Master_Device();
        $where = $device_instance_master_devicesTable->getAdapter()->quoteInto('masterDeviceId = ?', $master_device_id, 'INTEGER');
        $device_instances = $device_instance_master_devicesTable->fetchAll($where);

        try
        {
            $formdata = array(
                'report_count' => count($device_instances)
            );
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to get report count.", 0, $e);
        } // end catch


        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function filterlistitemsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();

        $db       = Zend_Db_Table::getDefaultAdapter();
        $list     = $this->_getParam('list', 'man');
        $formdata = new stdClass();

        try
        {
            switch ($list)
            {
                case "man" :
                    $select = new Zend_Db_Select($db);
                    $select = $db->select();
                    $select->from(array(
                                       'm' => 'manufacturers'
                                  ));
                    $select->where('isDeleted = 0');
                    $select->order('fullname');
                    $stmt   = $db->query($select);
                    $result = $stmt->fetchAll();
                    $count  = count($result);

                    if ($count > 0)
                    {
                        $i = 0;
                        foreach ($result as $row)
                        {
                            $formdata->rows [$i] ['id']   = $row ['id'];
                            $formdata->rows [$i] ['cell'] = array(
                                $row ['id'],
                                ucwords(strtolower($row ['fullname']))
                            );
                            $i++;
                        }
                    }
                    else
                    {
                        // empty form values
                        $formdata = array();
                    }
                    break;

                case "color" :
                    $select = new Zend_Db_Select($db);
                    $select = $db->select();
                    $select->from(array(
                                       'tc' => 'pgen_toner_colors'
                                  ));
                    $select->order('name');
                    $stmt   = $db->query($select);
                    $result = $stmt->fetchAll();
                    $count  = count($result);

                    if ($count > 0)
                    {
                        $i = 0;
                        foreach ($result as $row)
                        {
                            $formdata->rows [$i] ['id']   = $row ['id'];
                            $formdata->rows [$i] ['cell'] = array(
                                $row ['id'],
                                ucwords(strtolower($row ['name']))
                            );
                            $i++;
                        }
                    }
                    else
                    {
                        // empty form values
                        $formdata = array();
                    }
                    break;

                case "type" :
                    $select = new Zend_Db_Select($db);
                    $select = $db->select();
                    $select->from(array(
                                       'pt' => 'pgen_part_types'
                                  ));
                    $select->order('name');
                    $stmt   = $db->query($select);
                    $result = $stmt->fetchAll();
                    $count  = count($result);

                    if ($count > 0)
                    {
                        $i = 0;
                        foreach ($result as $row)
                        {
                            $formdata->rows [$i] ['id']   = $row ['id'];
                            $formdata->rows [$i] ['cell'] = array(
                                $row ['id'],
                                $row ['name']
                            );
                            $i++;
                        }
                    }
                    else
                    {
                        // empty form values
                        $formdata = array();
                    }
                    break;
            }
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to build criteria list.", 0, $e);
        }

        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
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

        $db       = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);

        if ($deviceID !== false)
        {

            $toner_array = $this->_getParam('list', false);

            $formdata = null;
            $page     = $_GET ['page'];
            $limit    = $_GET ['rows'];
            $sidx     = $_GET ['sidx'];
            $sord     = $_GET ['sord'];
            if (!$sidx)
            {
                $sidx = 1;
            }

            try
            {
                $where = '';
                if ($toner_array != '')
                {
                    $fieldList = array(
                        'fullname AS manufacturer_name',
                        '(null) AS master_device_id'
                    );
                    $where     = 't.id IN(' . $toner_array . ')';
                }
                else
                {
                    $fieldList = array(
                        'fullname AS manufacturer_name'
                    );
                    $where     = 'dt.master_device_id = ' . $deviceID;
                }

                $select = new Zend_Db_Select($db);
                $select = $db->select();
                $select->from(array(
                                   't' => 'pgen_toners'
                              ));
                if ($toner_array == '')
                {
                    $select->joinLeft(array(
                                           'dt' => 'pgen_device_toners'
                                      ), 't.id = dt.toner_id');
                }
                $select->joinLeft(array(
                                       'pt' => 'pgen_part_types'
                                  ), 'pt.id = t.partTypeId', array(
                                                                  'name AS type_name'
                                                             ));
                $select->joinLeft(array(
                                       'tc' => 'pgen_toner_colors'
                                  ), 'tc.id = t.tonerColorId', array(
                                                                    'name AS toner_color_name'
                                                               ));
                $select->joinLeft(array(
                                       'm' => 'manufacturers'
                                  ), 'm.id = t.manufacturerId', $fieldList);
                $select->where($where);
                $stmt   = $db->query($select);
                $result = $stmt->fetchAll();
                $count  = count($result);

                if ($count > 0)
                {
                    $total_pages = ceil($count / $limit);
                }
                else
                {
                    $total_pages = 0;
                }

                if ($page > $total_pages)
                {
                    $page = $total_pages;
                }
                $start = $limit * $page - $limit;

                if ($count > 0)
                {
                    $i                 = 0;
                    $type_name         = '';
                    $formdata->page    = $page;
                    $formdata->total   = $total_pages;
                    $formdata->records = $count;
                    foreach ($result as $row)
                    {
                        // Always uppercase OEM, but just captialize everything else
                        $type_name = ucwords(strtolower($row ['type_name']));
                        if ($type_name == "Oem")
                        {
                            $type_name = "OEM";
                        }

                        $formdata->rows [$i] ['id']   = $row ['id'];
                        $formdata->rows [$i] ['cell'] = array(
                            $row ['id'],
                            $row ['sku'],
                            ucwords(strtolower($row ['manufacturer_name'])),
                            $type_name,
                            ucwords(strtolower($row ['toner_color_name'])),
                            $row ['yield'],
                            $row ['cost'],
                            $row ['master_device_id'],
                            $row ['master_device_id'],
                            null
                        );
                        $i++;
                    }
                }
                else
                {
                    // empty form values
                    $formdata = array();
                }
            }
            catch (Exception $e)
            {
                // critical exception
                throw new Exception("Passing Exception Up The Chain", null, $e);
            }
        }
        else
        {
            $formdata = array();
        }
        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function replacementtonersAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();

        $db       = Zend_Db_Table::getDefaultAdapter();
        $toner_id = $this->_getParam('tonerid', 0);

        $formdata = array();
        if ($toner_id > 0)
        {
            $filter   = $this->_getParam('filter', false);
            $criteria = trim($this->_getParam('criteria', false));

            $formdata = new stdClass();
            $page     = $_GET ['page'];
            $limit    = $_GET ['rows'];
            $sidx     = $_GET ['sidx'];
            $sord     = $_GET ['sord'];
            if (!$sidx)
            {
                $sidx = 'fullname';
            }

            $where = '';
            if (!empty($filter) && !empty($criteria) && $filter != 'machine_compatibility')
            {
                if ($filter == 'toner_yield')
                {
                    $filter = 'yield';
                    $where  = ' AND ' . $filter . ' = ' . $criteria;
                }
                else
                {
                    if ($filter == "manufacturer_name")
                    {
                        $filter = "fullname";
                    }
                    else
                    {
                        if ($filter == "toner_SKU")
                        {
                            $filter = "sku";
                        }
                        else
                        {
                            if ($filter == "type_name")
                            {
                                $filter = "pt.name";
                            }
                            else
                            {
                                if ($filter == "toner_color_name")
                                {
                                    $filter = 'tc.name';
                                }
                            }
                        }
                    }
                    $where = ' AND ' . $filter . ' LIKE("%' . $criteria . '%")';
                }
            }

            try
            {
                // GET TONER
                $toner          = Proposalgen_Model_Mapper_Toner::getInstance()->find($toner_id);
                $toner_color_id = $toner->tonerColorId;
                // GET NUMBER OF DEVICES USING THIS TONER
                $total_devices       = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchAll('toner_id = ' . $toner_id);
                $total_devices_count = count($total_devices);

                // GET NUMBER OF DEVICES WHERE LAST TONER FOR THIS COLOR
                $num_devices_count = 0;
                foreach ($total_devices as $key)
                {
                    $master_device_id = $key->masterDeviceId;

                    // GET ALL SAME COLOR TONERS FOR DEVICE
                    $select = new Zend_Db_Select($db);
                    $select = $db->select();
                    $select->from(array(
                                       'dt' => 'pgen_device_toners'
                                  ));
                    $select->joinLeft(array(
                                           't' => 'pgen_toners'
                                      ), 'dt.toner_id = t.id');
                    $select->where('t.tonerColorId = ' . $toner_color_id . ' AND dt.master_device_id = ' . $master_device_id);
                    $stmt        = $db->query($select);
                    $num_devices = $stmt->fetchAll();

                    if (count($num_devices) == 1)
                    {
                        $num_devices_count += 1;
                    }
                }

                // GET SAME COLOR TONERS
                $select = new Zend_Db_Select($db);
                $select = $db->select();
                $select->from(array(
                                   't' => 'pgen_toners'
                              ));
                $select->joinLeft(array(
                                       'pt' => 'pgen_part_types'
                                  ), 'pt.id = t.partTypeId');
                $select->joinLeft(array(
                                       'tc' => 'pgen_toner_colors'
                                  ), 'tc.id = t.tonerColorId');
                $select->joinLeft(array(
                                       'm' => 'manufacturers'
                                  ), 'm.id = t.manufacturerId');
                $select->where('t.id != ' . $toner_id . ' AND t.tonerColorId = ' . $toner_color_id . $where);
                $stmt   = $db->query($select);
                $result = $stmt->fetchAll();
                $count  = count($result);

                if ($count > 0)
                {
                    $total_pages = ceil($count / $limit);
                }
                else
                {
                    $total_pages = 1;
                }

                if ($page > $total_pages)
                {
                    $page = $total_pages;
                }

                $start  = $limit * $page - $limit;
                $select = new Zend_Db_Select($db);
                $select = $db->select();
                $select->from(array(
                                   't' => 'pgen_toners'), array('id AS toners_id', 'sku', 'yield', 'cost')
                );
                $select->joinLeft(array(
                                       'pt' => 'pgen_part_types'
                                  ), 'pt.id = t.partTypeId', array(
                                                                  'name AS type_name'
                                                             ));
                $select->joinLeft(array(
                                       'tc' => 'pgen_toner_colors'
                                  ), 'tc.id = t.tonerColorId');
                $select->joinLeft(array(
                                       'm' => 'manufacturers'
                                  ), 'm.id = t.manufacturerId');
                $select->where('t.id != ' . $toner_id . ' AND t.tonerColorId = ' . $toner_color_id . $where);
                $select->order($sidx . ' ' . $sord);
                $select->limit($limit, $start);
                $stmt   = $db->query($select);
                $result = $stmt->fetchAll();
                if ($count > 0)
                {
                    $i                 = 0;
                    $type_name         = '';
                    $formdata->page    = $page;
                    $formdata->total   = $total_pages;
                    $formdata->records = $count;
                    foreach ($result as $row)
                    {
                        // Always uppercase OEM, but just captialize everything else
                        $type_name = ucwords(strtolower($row ['type_name']));
                        if ($type_name == "Oem")
                        {
                            $type_name = "OEM";
                        }

                        $formdata->rows [$i] ['id']   = $row ['toners_id'];
                        $formdata->rows [$i] ['cell'] = array(
                            $row ['toners_id'],
                            $row ['sku'],
                            ucwords(strtolower($row ['fullname'])),
                            $type_name,
                            ucwords(strtolower($row ['name'])),
                            $row ['yield'],
                            $row ['cost'],
                            $num_devices_count,
                            $total_devices_count
                        );
                        $i++;
                    }
                }
                else
                {
                    // empty form values
                    $formdata = array();
                }
            }
            catch (Exception $e)
            {
                // critical exception
                Throw new exception("Critical Error: Unable to find device parts.", 0, $e);
            }
        }
        else
        {
            $formdata = array();
        }

        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function devicetonercountAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();

        $db       = Zend_Db_Table::getDefaultAdapter();
        $toner_id = $this->_getParam('tonerid', 0);

        $formdata = null;
        try
        {
            // GET TONER
            $toner          = Proposalgen_Model_Mapper_Toner::getInstance()->find($toner_id);
            $toner_color_id = $toner->tonerColorId;

            // GET NUMBER OF DEVICES USING THIS TONER
            $total_devices       = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchAll('toner_id = ' . $toner_id);
            $total_devices_count = count($total_devices);

            // GET NUMBER OF DEVICES WHERE LAST TONER FOR THIS COLOR
            $num_devices_count = 0;
            foreach ($total_devices as $key)
            {
                $master_device_id = $key->masterDeviceId;
                // GET ALL SAME COLOR TONERS FOR DEVICE
                $select = new Zend_Db_Select($db);
                $select = $db->select();
                $select->from(array(
                                   'dt' => 'pgen_device_toners'
                              ));
                $select->joinLeft(array(
                                       't' => 'pgen_toners'
                                  ), 'dt.toner_id = t.id');
                $select->where('t.tonerColorId = ' . $toner_color_id . ' AND dt.master_device_id = ' . $master_device_id);
                $stmt        = $db->query($select);
                $num_devices = $stmt->fetchAll();

                if (count($num_devices) == 1)
                {
                    $num_devices_count += 1;
                }
            }

            $formdata = array(
                'total_count'  => $total_devices_count,
                'device_count' => $num_devices_count
            );
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device count.", 0, $e);
        }

        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function addtonerAction ()
    {
        // Disable the default layout
        $this->_helper->layout->disableLayout();

        // grab all variables from $_POST
        $toner_id         = $this->_getParam('toner_id', false);
        $toner_sku        = $this->_getParam('toner_sku', false);
        $part_type_id     = $this->_getParam('part_type_id', false);
        $manufacturer_id  = $this->_getParam('manufacturer_id', false);
        $toner_color_id   = $this->_getParam('toner_color_id', false);
        $toner_yield      = $this->_getParam('toner_yield', false);
        $toner_price      = $this->_getParam('toner_price', false);
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
            $db                = Zend_Db_Table::getDefaultAdapter();
            $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();

            $db->beginTransaction();
            try
            {
                if ($toner_id > 0)
                {
                    $device_tonerData = array(
                        'toner_id'         => $toner_id,
                        'master_device_id' => $master_device_id
                    );
                    // make sure device_toner does not exist
                    $where  = $device_tonerTable->getAdapter()->quoteInto('toner_id = ' . $toner_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
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
                    $tonerData  = array(
                        'toner_sku'       => $toner_sku,
                        'part_type_id'    => $part_type_id,
                        'manufacturer_id' => $manufacturer_id,
                        'tonerColorId'    => $toner_color_id,
                        'toner_yield'     => $toner_yield,
                        'toner_price'     => $toner_price
                    );

                    // make sure toner does not exist
                    $where  = $tonerTable->getAdapter()->quoteInto('(toner_SKU = "' . $toner_sku . '") OR (manufacturer_id = ' . $manufacturer_id . ' AND tonerColorId = ' . $toner_color_id . ' AND toner_yield = ' . $toner_yield . ')', null);
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
                    $device_tonerData = array(
                        'toner_id'         => $toner_id,
                        'master_device_id' => $master_device_id
                    );
                    $device_tonerTable->insert($device_tonerData);
                    $message = "The toner has been added.";
                }

                $db->commit();
            }
            catch (Exception $e)
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
        $tonerTable        = new Proposalgen_Model_DbTable_Toner();
        $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();

        // grab all variables from $_POST
        $id                = $this->_getParam('id', null);
        $toner_id          = $this->_getParam('toner_id', null);
        $toner_sku         = $this->_getParam('toner_sku', null);
        $part_type_id      = $this->_getParam('part_type_id', null);
        $manufacturer_id   = $this->_getParam('manufacturer_id', null);
        $manufacturer_name = $this->_getParam('manufacturer_name', null);
        $toner_color_id    = $this->_getParam('toner_color_id', null);
        $toner_yield       = $this->_getParam('toner_yield', null);
        $toner_price       = $this->_getParam('toner_price', null);
        $master_device_id  = $this->_getParam('deviceid', null);
        $oper              = $this->_getParam('oper', null);

        // used for cell editing
        $field   = '';
        $value   = '';
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
            $where   = $device_tonerTable->getAdapter()->quoteinto("id = ?", $id, "INTEGER");
            $devices = $device_tonerTable->fetchAll($where);

            if (count($devices) > 0)
            {
                $message = "We are unable to delete toner as it's already assigned to a printer.";
            }
            else
            {
                $where = $tonerTable->getAdapter()->quoteInto("id = ?", $id, "INTEGER");
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
            else if (!is_numeric($toner_yield))
            {
                $message = "Toner Yield is not a valid number. Please try again.";
            }
            else if (!is_numeric($toner_price))
            {
                $message = "Toner Price is not a valid number. Please try again.";
            }
            else if (!($toner_price > 0))
            {
                $message = "Toner Price must be greater than 0. Please try again.";
            }

            if (empty($message))
            {
                $db->beginTransaction();
                try
                {
                    $tonerTable = new Proposalgen_Model_DbTable_Toner();
                    $tonerData  = array(
                        'sku'            => $toner_sku,
                        'partTypeId'     => $part_type_id,
                        'manufacturerId' => $manufacturer_id,
                        'tonerColorId'   => $toner_color_id,
                        'yield'          => $toner_yield,
                        'cost'           => $toner_price
                    );

                    if ($toner_id > 0)
                    {
                        $where    = $tonerTable->getAdapter()->quoteInto('id = ?', $toner_id, 'INTEGER');
                        $toner_id = $tonerTable->update($tonerData, $where);
                        $message  = "The toner has been updated.";
                    }
                    else
                    {
                        // make sure toner does not exist
                        $where  = $tonerTable->getAdapter()->quoteInto('(sku = "' . $toner_sku . '")', null);
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
                catch (Exception $e)
                {
                    Throw new exception("Critical Error: Unable to find toners.", 0, $e);
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
        $message     = '';
        $toner_count = 0;
        $db          = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->disableLayout();

        $replace_mode = $this->_getParam('replace_mode', '');
        $replace_id   = $this->_getParam('replace_toner_id', 0);
        $with_id      = $this->_getParam('with_toner_id', 0);
        $apply_all    = $this->_getParam('chkAllToners', 0);

        /*
         * / DEBUG echo "replace_mode=" . $replace_mode . "<br />"; echo "replace_id=" . $replace_id . "<br />"; echo
         * "with_id=" . $with_id . "<br />"; echo "apply_all=" . $apply_all . "<br />"; die; //
         */

        // GET TONER
        $toner          = Proposalgen_Model_Mapper_Toner::getInstance()->find($replace_id);
        $toner_color_id = $toner->tonerColorId;

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
                foreach ($total_devices as $key)
                {
                    $master_device_id = $key->masterDeviceId;
                    // UPDATE ALL DEVICES WITH THIS TONER (replace_id) TO
                    // REPLACEMENT TONER (with_id)
                    $device_tonerMapper     = Proposalgen_Model_Mapper_DeviceToner::getInstance();
                    $device_toner           = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchRow('toner_id = ' . $replace_id . ' AND master_device_id = ' . $master_device_id);
                    $device_toner->toner_id = $with_id;
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
                    foreach ($total_devices as $key)
                    {
                        $master_device_id = $key->masterDeviceId;

                        if ($apply_all == 1)
                        {

                            // UPDATE ALL DEVICES WITH THIS TONER (replace_id)
                            // TO REPLACEMENT TONER (with_id)
                            $device_tonerMapper = Proposalgen_Model_Mapper_DeviceToner::getInstance();
                            $device_toner       = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchRow('toner_id = ' . $replace_id . ' AND master_device_id = ' . $master_device_id);
                            $device_toner->tonerId = $with_id;
                            $device_tonerMapper->save($device_toner);
                            $toner_count += 1;
                        }
                        else
                        {
                            // UPDATE ONLY DEVICES WHERE THIS IS THE LAST OF
                            // IT'S COLOR ($toner_color_id)
                            $select = new Zend_Db_Select($db);
                            $select = $db->select();
                            $select->from(array(
                                               'dt' => 'pgen_device_toners'
                                          ));
                            $select->joinLeft(array(
                                                   't' => 'pgen_toners'
                                              ), 'dt.toner_id = t.id');
                            $select->where('t.tonerColorId = ' . $toner_color_id . ' AND dt.master_device_id = ' . $master_device_id);
                            $stmt        = $db->query($select);
                            $num_devices = $stmt->fetchAll();
                            if (count($num_devices) == 1)
                            {
                                // UPDATE THIS DEVICE WITH REPLCEMENT TONER
                                // (with_id)
                                $device_tonerMapper     = Proposalgen_Model_Mapper_DeviceToner::getInstance();
                                $device_toner           = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchRow('toner_id = ' . $replace_id . ' AND master_device_id = ' . $master_device_id);
                                $device_toner->tonerId = $with_id;
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


            // REMOVE USER TONER OVERRIDES
            $user_toner_OverrideTable = new Proposalgen_Model_DbTable_UserTonerOverride();
            $where                    = $user_toner_OverrideTable->getAdapter()->quoteInto('toner_id = ?', $replace_id, 'INTEGER');
            $user_toner_OverrideTable->delete($where);

            // REMOVE DEVICE TONER MAPPINGS
            $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();
            $where             = $device_tonerTable->getAdapter()->quoteInto('toner_id = ?', $replace_id, 'INTEGER');
            $device_tonerTable->delete($where);

            // REMOVE TONER
            $tonerTable = new Proposalgen_Model_DbTable_Toner();
            $where      = $tonerTable->getAdapter()->quoteInto('id = ?', $replace_id, 'INTEGER');
            $tonerTable->delete($where);

            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
            $message = "An error has occurred and the toner was not replaced.";
        }

        // RETURN MESSAGE
        $this->view->data = $message;
    }

    public function removetonerAction ()
    {
        $db      = Zend_Db_Table::getDefaultAdapter();
        $message = array();

        $toner_id         = $this->_getParam('toner_id', false);
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
        catch (Exception $e)
        {

            $db->rollback();
            $message [] = "An error has occurred and the toner was not removed.";

        }

        // encode user data to return to the client:
        $this->view->data = $message;
    }

    public function searchtonersAction ()
    {
        $db       = Zend_Db_Table::getDefaultAdapter();
        $formdata = new stdClass();

        // disable the default layout
        $this->_helper->layout->disableLayout();

        $field = $this->_getParam('search_field', false);
        $value = $this->_getParam('search_value', false);

        // build where
        $where = "";
        if (!empty($field))
        {
            $where .= $field . ' = "' . $value . '"';
        }

        try
        {
            // select toners for device
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array(
                            't' => 'toner'
                       ))
                ->join(array(
                            'pt' => 'part_type'
                       ), 'pt.part_type_id = t.partTypeId')
                ->join(array(
                            'tc' => 'toner_color'
                       ), 'tc.toner_color_id = t.tonerColorId')
                ->join(array(
                            'm' => 'manufacturer'
                       ), 'm.manufacturer_id = t.manufacturerId');
            if (!empty($where))
            {
                $select->where($where);
            }
            $select->order(array(
                                'm.manufacturer_name',
                                't.tonerColorId',
                                'toner_yield'
                           ));
            $stmt   = $db->query($select);
            $result = $stmt->fetchAll();

            if (count($result) > 0)
            {
                $i = 0;
                foreach ($result as $row)
                {
                    $formdata->rows [$i] ['id']   = $row ['toner_id'];
                    $formdata->rows [$i] ['cell'] = array(
                        $row ['toner_id'],
                        $row ['toner_SKU'],
                        $row ['type_name'],
                        $row ['manufacturer_name'],
                        $row ['toner_color_name'],
                        $row ['toner_yield'],
                        "$" . money_format('%i', ($row ['toner_price']))
                    );
                    $i++;
                }
            }
            else
            {
                $formdata = array();
            }
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find toners.", 0, $e);
        } // end catch


        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
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
        $db                = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = "Manufacturers";

        // add manufacturers form
        $form = new Proposalgen_Form_Manufacturers(null, "edit");

        // fill manufacturers dropdown
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers      = $manufacturersTable->fetchAll('isDeleted = 0', 'fullname');

        // add "New Manufacturer" option
        $currElement = $form->getElement('select_manufacturer');
        $currElement->addMultiOption('0', 'Add New Manufacturer');
        foreach ($manufacturers as $row)
        {
            $currElement->addMultiOption($row ['id'], ucwords(strtolower($row ['fullname'])));
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

            if (isset($formData ["ticket_id"]) && $formData ['ticket_id'] != "-1" && !isset($formData ['form_mode']))
            {
                $hdnID         = $formData ['hdnID'];
                $hdnItem       = $formData ['hdnItem'];
                $ticket_id     = $formData ['ticket_id'];
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
                        $manufacturerData  = array(
                            'fullname'  => $manufacturer_name,
                            'isDeleted' => 0
                        );
                        $where             = $manufacturersTable->getAdapter()->quoteInto('fullName = ?', $manufacturer_name);
                        $manufacturer      = $manufacturersTable->fetchAll($where);

                        if (count($manufacturer) == 0)
                        {
                            $manufacturersTable->insert($manufacturerData);
                        }
                    }
                    $db->commit();
                }
                catch (Exception $e)
                {
                    $db->rollback();
                }
            }
            else if (isset($formData ['manufacturer_name']) && $form->isValid($formData))
            {
                $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
                $manufacturer_id    = $formData ['select_manufacturer'];
                $manufacturer_name  = strtoupper($formData ['manufacturer_name']);

                $db->beginTransaction();
                try
                {

                    if (array_key_exists('save_manufacturer', $formData) && $formData ['save_manufacturer'] == "Save")
                    {

                        $manufacturerData = array(
                            'displayName' => $manufacturer_name,
                            'fullName'    => $manufacturer_name
                        );

                        if ($manufacturer_id > 0)
                        {

                            $where = $manufacturersTable->getAdapter()->quoteInto('id = ?', $manufacturer_id, 'INTEGER');
                            $manufacturersTable->update($manufacturerData, $where);

                            $this->view->message = 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" Updated';
                        }
                        else
                        {

                            $where        = $manufacturersTable->getAdapter()->quoteInto('fullName = ?', $manufacturer_name);
                            $manufacturer = $manufacturersTable->fetchRow($where);

                            if (count($manufacturer) > 0)
                            {
                                if ($manufacturer ['isDeleted'] == 1)
                                {
                                    $manufacturerData = array(
                                        'isDeleted' => 0
                                    );
                                    $where            = $manufacturersTable->getAdapter()->quoteInto('id = ?', $manufacturer ['id'], 'INTEGER');
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
                            $where              = $master_deviceTable->getAdapter()->quoteInto('manufacturer_id = ?', $manufacturer_id, 'INTEGER');
                            $master_device      = $master_deviceTable->fetchAll($where);

                            if (count($master_device) == 0)
                            {
                                $tonerTable = new Proposalgen_Model_DbTable_Toner();
                                $where      = $tonerTable->getAdapter()->quoteInto('manufacturerId = ?', $manufacturer_id, 'INTEGER');
                                $toner      = $tonerTable->fetchAll($where);
                                if (count($toner) == 0)
                                {
                                    $do_full_delete = true;
                                }
                            }

                            $where = $manufacturersTable->getAdapter()->quoteInto('id = ?', $manufacturer_id, 'INTEGER');
                            if ($do_full_delete)
                            {
                                $manufacturersTable->delete($where);
                            }
                            else
                            {
                                $manufacturerData = array(
                                    'isDeleted' => 1
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
                    $manufacturers      = $manufacturersTable->fetchAll('isDeleted = 0', 'fullName');

                    // add "New Manufacturer" option
                    $currElement = $form->getElement('select_manufacturer');
                    $currElement->clearMultiOptions();
                    $currElement->addMultiOption('0', 'Add New Manufacturer');

                    foreach ($manufacturers as $row)
                    {
                        $currElement->addMultiOption($row ['id'], ucwords(strtolower($row ['fullname'])));
                    }

                    // reset form
                    $currElement->setValue('');
                    $form->getElement('manufacturer_name')->setValue('');
                }
                catch (Exception $e)
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

        $db             = Zend_Db_Table::getDefaultAdapter();
        $manufacturerID = $this->_getParam('manufacturerid', false);

        $manufacturerTable = new Proposalgen_Model_DbTable_Manufacturer();
        $where             = $manufacturerTable->getAdapter()->quoteInto('id = ?', $manufacturerID, 'INTEGER');
        $manufacturer      = $manufacturerTable->fetchRow('id = ' . $manufacturerID);

        try
        {
            if (count($manufacturer) > 0)
            {
                $formdata = array(
                    'manufacturer_name' => Trim(ucwords(strtolower($manufacturer ['fullname']))),
                    'is_deleted'        => ($manufacturer ['isDeleted'] == 1 ? true : false)
                );
            }
            else
            {
                // empty form values
                $formdata = array(
                    'manufacturer_name' => '',
                    'is_deleted'        => false
                );
            }
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find manufacturer.", 0, $e);
        } // end catch


        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * The uploadpricingAction allows the system admin or dealer to select a .
     *
     *
     *
     *
     *
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
        $db                = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = "Upload Pricing File";

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            $upload   = new Zend_File_Transfer_Adapter_Http();
            $upload->setDestination($this->config->app->uploadPath);

            // Limit the extensions to csv files
            $upload->addValidator('Extension', false, array(
                                                           'csv'
                                                      ));
            $upload->getValidator('Extension')->setMessage('<p><span class="warning">*</span> File "' . basename($_FILES ['uploadedfile'] ['name']) . '" has an <em>invalid</em> extension. A <span style="color: red;">.csv</span> is required.</p>');

            // Limit the amount of files to maximum 1
            $upload->addValidator('Count', false, 1);
            $upload->getValidator('Count')->setMessage('<p><span class="warning">*</span> You are only allowed to upload 1 file at a time.</p>');

            // Limit the size of all files to be uploaded to maximum 4MB and
            // mimimum 500B
            $upload->addValidator('FilesSize', false, array(
                                                           'min' => '100B',
                                                           'max' => '4MB'
                                                      ));
            $upload->getValidator('FilesSize')->setMessage('<p><span class="warning">*</span> File size must be between 100B and 4MB.</p>');

            if ($upload->receive())
            {
                $is_valid = true;

                try
                {
                    $lines = file($upload->getFileName(), FILE_IGNORE_NEW_LINES);

                    // required fields list
                    $required = array(
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
                    foreach ($required as $key => $value)
                    {
                        if (!in_array(strtolower($required [$key]), $headers))
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
                        foreach ($lines as $key => $value)
                        {
                            if ($key > 0)
                            {
                                $devices [$key] = str_getcsv($value);

                                // combine the column headers and the device
                                // data into one associative array
                                $finalDevices [] = array_combine($headers, $devices [$key]);
                            }
                        }
                        $this->view->headerArray  = $headers;
                        $this->view->resultsArray = $finalDevices;
                        $this->view->message      = "<p>Please review the data and click confirm to complete the upload.</p>";

                        // store array in session to be used by
                        // confirmationAction to save the values to the database
                        $columns        = new Zend_Session_Namespace('columns_array');
                        $columns->array = $headers;

                        $results        = new Zend_Session_Namespace('results_array');
                        $results->array = $finalDevices;
                    }
                }
                catch (Exception $e)
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
        $db                    = Zend_Db_Table::getDefaultAdapter();
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
                foreach ($results->array as $key => $value)
                {
                    $manufacturername = $results->array [$key] ['modelmfg'];
                    $devicename       = strtolower($results->array [$key] ['modelname']);
                    $devicename       = str_replace($manufacturername . ' ', '', $devicename);

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
                        $manufacturerTable      = new Proposalgen_Model_DbTable_Manufacturer();
                        $master_deviceTable     = new Proposalgen_Model_DbTable_MasterDevice();
                        $devices_pfTable        = new Proposalgen_Model_DbTable_PFDevices();
                        $master_matchup_pfTable = new Proposalgen_Model_DbTable_PFMasterMatchup();
                        $tonerTable             = new Proposalgen_Model_DbTable_Toner();
                        $part_typeTable         = new Proposalgen_Model_DbTable_PartType();
                        $device_tonerTable      = new Proposalgen_Model_DbTable_DeviceToner();

                        // get manufacturer_id
                        $where        = $manufacturerTable->getAdapter()->quoteInto('manufacturer_name = ?', $manufacturername);
                        $manufacturer = $manufacturerTable->fetchRow($where);

                        if (count($manufacturer) > 0)
                        {
                            $manufacturer_id = $manufacturer ['manufacturer_id'];
                        }
                        else
                        {
                            $data            = array(
                                'manufacturer_name' => $manufacturername
                            );
                            $manufacturer_id = $manufacturerTable->insert($data);
                        }

                        if ($manufacturer_id > 0)
                        {
                            // get toner_config_id
                            $toner_config_id = 1;

                            // prep date
                            if (!empty($results->array [$key] ['dateintroduction']))
                            {
                                $launch_date = new Zend_Date($results->array [$key] ['dateintroduction'], "mm/dd/yyyy HH:ii:ss");
                            }
                            else
                            {
                                $launch_date = new Zend_Date("0/0/0000 0:0:0", "mm/dd/yyyy HH:ii:ss");
                            }

                            // save master_device
                            $data             = array(
                                'mastdevice_manufacturer' => $manufacturer_id,
                                'printer_model'           => $devicename,
                                'toner_config_id'         => $toner_config_id,
                                'is_copier'               => $results->array [$key] ['is_copier'],
                                'is_fax'                  => $results->array [$key] ['is_fax'],
                                'is_scanner'              => $results->array [$key] ['is_scanner'],
                                'watts_power_normal'      => $results->array [$key] ['wattspowernormal'],
                                'watts_power_idle'        => $results->array [$key] ['wattspoweridle'],
                                'launch_date'             => $launch_date->toString('yyyy/mm/dd HH:ss'),
                                'service_cost_per_page'   => $results->array [$key] ['cpp service']
                            );
                            $master_device_id = $master_deviceTable->insert($data);

                            // save devices_pf
                            $data          = array(
                                'pf_model_id'        => $results->array [$key] ['printermodelid'],
                                'pf_db_devicename'   => $devicename,
                                'pf_db_manufacturer' => $manufacturername,
                                'date_created'       => $date,
                                'created_by'         => $this->user_id
                            );
                            $devices_pf_id = $devices_pfTable->insert($data);

                            if ($master_device_id > 0 && $devices_pf_id > 0)
                            {
                                // save master_matchup_pf
                                $data              = array(
                                    'master_device_id' => $master_device_id,
                                    'devices_pf_id'    => $devices_pf_id
                                );
                                $master_matchup_pf = $master_matchup_pfTable->insert($data);

                                // save toner
                                $color_array = array(
                                    'black',
                                    'cyan',
                                    'magenta',
                                    'yellow'
                                );
                                $type_array  = array(
                                    'oem',
                                    'compatible'
                                );

                                foreach ($color_array as $key)
                                {
                                    foreach ($type_array as $key2)
                                    {
                                        // get part_type_id
                                        $where     = $part_typeTable->getAdapter()->quoteInto('type_name = ?', $key2);
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

                                        $data     = array(
                                            'toner_SKU'       => $results->array [$key] [$key . ' ' . $key2 . ' sku'],
                                            'toner_price'     => $results->array [$key] [$key . ' ' . $key2 . ' cost'],
                                            'toner_yield'     => $results->array [$key] [$key . ' ' . $key2 . ' yield'],
                                            'part_type_id'    => $part_type_id,
                                            'manufacturer_id' => $manufacturer_id,
                                            'toner_color_id'  => $key
                                        );
                                        $toner_id = $tonerTable->insert($data);
                                    }
                                }

                                if ($toner_id > 0)
                                {
                                    // save device_toner
                                    $data         = array(
                                        'toner_id'         => $toner_id,
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
            catch (Zend_Db_Exception $e)
            {
                $db->rollBack();
                $this->view->message = "Unknown Error.";
            }
            catch (Exception $e)
            {
                $db->rollBack();
                $this->view->message = "Error: Your file was not saved. Please double check the file and try again. If you continue to experience problems saving, contact your administrator.<br /><br />";
            }
        }
    }

    /**
     * Allows system admins to set the default settings for the system
     * BOOKMARK: SYSTEMADMIN SETTINGS
     */
    public function managesettingsAction ()
    {
        $this->view->title = 'Manage Settings';
        $db                = Zend_Db_Table::getDefaultAdapter();

        // Row #1 of Report Settings has all the system defaults
        $systemReportSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->find(1);
        $systemSurveySettings = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->find(1);
        $form                 = new Proposalgen_Form_Settings_SystemAdmin();

        if ($this->_request->isPost())
        {
            // Get the data that has been posted
            $formData = $this->_request->getPost();

            if ($form->isValid($formData))
            {
                if (isset($formData ['save_settings']))
                {
                    $db->beginTransaction();
                    try
                    {
                        // Make all empty values = null
                        foreach ($formData as &$value)
                        {
                            if (strlen($value) === 0)
                            {
                                $value = null;
                            }
                        }
                        // Save page coverage settings (survey settings)
                        $systemSurveySettings->populate($formData);
                        Proposalgen_Model_Mapper_Survey_Setting::getInstance()->save($systemSurveySettings, $systemSurveySettings->id);
                        // Save report settings (all other)
                        $systemReportSettings->populate($formData);
                        Proposalgen_Model_Mapper_Report_Setting::getInstance()->save($systemReportSettings, $systemReportSettings->id);

                        $this->_helper->flashMessenger(array("success" => "Your settings have been updated."));
                        $db->commit();
                    }
                    catch (Zend_Db_Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array("error" => "An error occured while saving your settings."));
                    }
                    catch (Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array("error" => "An error occured while saving your settings."));
                    }
                }
            }
            else
            {
                $this->_helper->flashMessenger(array(
                                                    "error" => "Please review the errors below."
                                               ));
                $form->populate($formData);
            }
        }

        // Add form to page
        $form->setDecorators(array(array('ViewScript', array('viewScript' => 'forms/settings/systemadmin.phtml'))));
        // Populate the form wif data
        $form->populate($systemReportSettings->toArray());
        $form->populate($systemSurveySettings->toArray());
        $this->view->settingsForm = $form;
    }

    /**
     * Allows the user to set their own settings in the override hierarchy
     * BOOKMARK: USER SETTINGS
     */
    public function managemysettingsAction ()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->view->title = 'Manage My Settings';
        $db                = Zend_Db_Table::getDefaultAdapter();
        $message           = '';

        // Get system overrides
        $userId         = Zend_Auth::getInstance()->getIdentity()->id;
        $reportSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchUserReportSetting($userId);
        $surveySettings = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchUserSurveySetting($userId);
        $pricingConfigs = Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll();
        $form           = new Proposalgen_Form_Settings_User();
        // Add all the pricing configs
        /* @var $pricingConfig Proposalgen_Model_PricingConfig */
        foreach ($pricingConfigs as $pricingConfig)
        {
            $form->getElement('assessmentPricingConfigId')->addMultiOption($pricingConfig->pricingConfigId, ($pricingConfig->pricingConfigId !== 1) ? $pricingConfig->configName : "");
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
                        foreach ($formData as &$value)
                        {
                            if (strlen($value) === 0)
                            {
                                $value = null;
                            }
                        }

                        // Save page coverage settings (survey settings)
                        $surveySettings->populate($formData);
                        Proposalgen_Model_Mapper_Survey_Setting::getInstance()->save($surveySettings, $surveySettings->id);

                        // Save report settings (all other)
                        $reportSettings->populate($formData);
                        Proposalgen_Model_Mapper_Report_Setting::getInstance()->save($reportSettings, $reportSettings->id);

                        $this->_helper->flashMessenger(array("success" => "Your settings have been updated."));
                        $db->commit();
                    }
                    catch (Zend_Db_Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array("error" => "An error occurred while saving your settings.{$e->getMessage()}"));
                    }
                    catch (Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array("error" => "An error occurred while saving your settings."));
                    }
                }
            }
            else
            {
                $this->_helper->flashMessenger(array("error" => "Please review the errors below."));
                $form->populate($formData);
            }
        }
        // Populate the values in the form.
        $form->populate($surveySettings->toArray());
        $form->populate($reportSettings->toArray());

        // Get the system defaults and unset the id's.  Merge the two system settings and set the dropdowns.
        $systemReportSettings      = Proposalgen_Model_Mapper_Report_Setting::getInstance()->find(1);
        $systemSurveySetting       = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->find(1);
        $systemReportSettingsArray = $systemReportSettings->toArray();
        $systemSurveySettingArray  = $systemSurveySetting->toArray();
        unset($systemReportSettingsArray ['id']);
        unset($systemSurveySettingArray ['id']);
        $defaultSettings = array_merge($systemReportSettingsArray, $systemSurveySettingArray);

        if ($defaultSettings ["assessmentPricingConfigId"] !== Proposalgen_Model_PricingConfig::NONE)
        {
            $defaultSettings ["assessmentPricingConfigId"] = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($defaultSettings ["assessmentPricingConfigId"])->configName;
        }
        else
        {
            $defaultSettings ["assessmentPricingConfigId"] = "";
        }

        // add form to page
        $form->setDecorators(array(
                                  array(
                                      'ViewScript',
                                      array(
                                          'viewScript' => 'forms/settings/user.phtml',
                                          'dealerData' => $defaultSettings,
//                                          'dealerName' => $dealerName,
                                          'message'    => $message
                                      )
                                  )
                             ));
        $this->view->settingsForm = $form;
    }

    /**
     */
    public function managemypricingAction ()
    {
        // get list of requests that are not completed from database
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/scripts/grid.celledit.js', 'text/javascript');

        $this->view->title = 'Manage My Pricing';
        $date              = date('Y-m-d H:i:s T');
        $db                = Zend_Db_Table::getDefaultAdapter();

        // add device form
        $form = new Proposalgen_Form_Device(null, "edit");
        $form->removeElement('save_device');
        $form->removeElement('delete_device');
        $form->removeElement('back_button');

        // get users company name
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where               = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
        $dealer_company      = $dealer_companyTable->fetchRow($where);

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
        $manufacturers      = $manufacturersTable->fetchAll('is_deleted = 0', 'manufacturer_name');
        $currElement        = $form->getElement('manufacturer_id');
        $currElement->addMultiOption('0', 'Select Manufacturer');
        foreach ($manufacturers as $row)
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
                            $currElement        = $form->getElement('printer_model');
                            $master_device_id   = $currElement->getValue();
                            $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();

                            $user_device_overrideTable = new Proposalgen_Model_DbTable_UserDeviceOverride();
                            $user_device_overrideData  = array(
                                'override_device_price' => $formData ["override_price"]
                            );

                            // get users company_id
                            $userTable  = new Proposalgen_Model_DbTable_Users();
                            $where      = $userTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                            $user       = $userTable->fetchRow($where);
                            $company_id = $user ['dealer_company_id'];

                            if ($master_device_id > 0)
                            {
                                // get printer_model
                                $where         = $master_deviceTable->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                                $master_device = $master_deviceTable->fetchRow($where);
                                $printer_model = $master_device ['printer_model'];

                                // check to see if override already exists
                                $where                = $user_device_overrideTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
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
                                    $user_device_overrideData ['user_id']          = $this->user_id;
                                    $user_device_overrideData ['master_device_id'] = $master_device_id;
                                    $user_device_overrideTable->insert($user_device_overrideData);
                                }
                                $this->view->message = 'The Printer has been updated.';
                            }
                            else
                            {
                                $this->view->message = 'Database Error: Printer Model could not be found.';
                            }

                            $toner_array = array();
                            if ($formData ['toner_array'])
                            {
                                $toner_id       = 0;
                                $override_price = 0;

                                $toner_array = explode(",", $formData ['toner_array']);
                                foreach ($toner_array as $key)
                                {
                                    $key            = str_replace("'", "", $key);
                                    $parts          = explode(":", $key);
                                    $toner_id       = $parts [0];
                                    $override_price = $parts [1];

                                    // validate
                                    $message = '';
                                    if ($override_price != '' && !is_numeric($override_price))
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
                                            $user_toner_overrideData  = array(
                                                'override_toner_price' => $override_price
                                            );

                                            // get users(dealers) company_id
                                            $userTable  = new Proposalgen_Model_DbTable_Users();
                                            $where      = $userTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                                            $user       = $userTable->fetchRow($where);
                                            $company_id = $user ['dealer_company_id'];

                                            if ($toner_id > 0)
                                            {
                                                // check to see if override
                                                // exists for user/toner
                                                $where               = $user_toner_overrideTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
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
                                                    $user_toner_overrideData ['user_id']  = $this->user_id;
                                                    $user_toner_overrideTable->insert($user_toner_overrideData);
                                                }
                                                $this->view->message = 'The Printer has been updated.';
                                            }
                                            else
                                            {
                                                $message = "Toner was not found.";
                                            }
                                        }
                                        catch (Exception $e)
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
                        catch (Zend_Db_Exception $e)
                        {
                            $db->rollback();
                            $this->view->message = 'Database Error: Override price for "' . $printer_model . '" could not be set.';
                            echo $e;
                        }
                        catch (Exception $e)
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

        $db       = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);

        // get company id
        $usersTable = new Proposalgen_Model_DbTable_Users();
        $where      = $usersTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
        $user       = $usersTable->fetchRow($where);
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
                    ->from(array(
                                'md' => 'master_device'
                           ))
                    ->joinLeft(array(
                                    'ddo' => 'dealer_device_override'
                               ), 'ddo.master_device_id = md.master_device_id AND ddo.dealer_company_id = ' . $company_id, array(
                                                                                                                                'dealer_device_price' => 'override_device_price'
                                                                                                                           ))
                    ->joinLeft(array(
                                    'udo' => 'user_device_override'
                               ), 'udo.master_device_id = md.master_device_id AND udo.user_id = ' . $this->user_id, array(
                                                                                                                         'user_device_price' => 'override_device_price'
                                                                                                                    ))
                    ->where('md.master_device_id = ?', $deviceID);
                $stmt   = $db->query($select);
                $result = $stmt->fetchAll();
                // print_r($result); die;
                // get device price
                $dealerOverride = new Proposalgen_Model_DbTable_DealerDeviceOverride();
                $OverrideRow    = $dealerOverride->fetchRow('dealer_company_id = ' . $this->dealer_company_id . ' AND master_device_id = ' . $deviceID);
                $deviceTable    = new Proposalgen_Model_DbTable_MasterDevice();
                $row            = $deviceTable->fetchRow('master_device_id =' . $deviceID);
                if ($OverrideRow ['override_device_price'])
                {
                    $devicePrice = money_format('%i', $OverrideRow ['override_device_price']);
                }
                elseif ($row ['device_price'])
                {
                    $devicePrice = money_format('%i', $row ['device_price'] * $dealer_pricing_margin);
                }
                else
                {
                    $devicePrice = "-";
                }
                if (count($result) > 0)
                {
                    // find price
                    $formdata = array(
                        'device_price'   => $devicePrice,
                        'override_price' => money_format('%i', ($result [0] ['user_device_price'] > 0 ? $result [0] ['user_device_price'] : null))
                    );
                }
                else
                {
                    // empty form values
                    $formdata = array();
                }
            }
            else
            {
                // empty form values
                $formdata = array();
            }
        }
        catch (Zend_Db_Exception $e)
        {
            $db->rollback();
            $this->view->message = 'Database Error: Device not found.';
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device.", 0, $e);
        } // end catch


        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
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

        $db       = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);
        $formdata = new stdClass();

        try
        {
            if ($deviceID > 0)
            {
                // get users company_id
                $userTable  = new Proposalgen_Model_DbTable_Users();
                $user       = $userTable->fetchRow('user_id = ' . $this->user_id);
                $company_id = $user ['dealer_company_id'];

                // get dealer pricing margin
                $dealer_pricing_margin = ($this->getPricingMargin('dealer') / 100) + 1;

                // select toners for device
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array(
                                't' => 'toner'
                           ))
                    ->join(array(
                                'dt' => 'device_toner'
                           ), 't.toner_id = dt.toner_id')
                    ->join(array(
                                'pt' => 'part_type'
                           ), 'pt.part_type_id = t.partTypeId')
                    ->join(array(
                                'tc' => 'toner_color'
                           ), 'tc.toner_color_id = t.tonerColorId')
                    ->joinLeft(array(
                                    'dto' => 'dealer_toner_override'
                               ), 'dto.toner_id = t.toner_id AND dealer_company_id = ' . $company_id, array(
                                                                                                           'override_toner_price AS dealer_toner_price'
                                                                                                      ))
                    ->joinLeft(array(
                                    'uto' => 'user_toner_override'
                               ), 'uto.toner_id = t.toner_id AND user_id = ' . $this->user_id, array(
                                                                                                    'override_toner_price'
                                                                                               ))
                    ->where('dt.master_device_id = ?', $deviceID);
                $stmt   = $db->query($select);
                $result = $stmt->fetchAll();

                if (count($result) > 0)
                {
                    $i                 = 0;
                    $toner_price       = 0;
                    $formdata->page    = 1;
                    $formdata->total   = 1;
                    $formdata->records = count($result);
                    foreach ($result as $row)
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

                        $formdata->rows [$i] ['id']   = $row ['toner_id'];
                        $formdata->rows [$i] ['cell'] = array(
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
                        $i++;
                    }
                }
                else
                {
                    $formdata = array();
                }
            }
            else
            {
                // empty form values
                $formdata->rows [1] ['id']   = 0;
                $formdata->rows [1] ['cell'] = array(
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
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device parts.", 0, $e);
        } // end catch


        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function editmytonerAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->disableLayout();

        // grab all variables from $_POST
        $toner_id         = $this->_getParam('id', false);
        $toner_price      = $this->_getParam('override_price', false);
        $master_device_id = $this->_getParam('deviceid', false);

        // validate
        $message = '';
        if ($toner_price != '' && !is_numeric($toner_price))
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
                $user_toner_overrideData  = array(
                    'override_toner_price' => $toner_price
                );

                // get users(dealers) company_id
                $userTable  = new Proposalgen_Model_DbTable_Users();
                $where      = $userTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                $user       = $userTable->fetchRow($where);
                $company_id = $user ['dealer_company_id'];

                if ($toner_id > 0)
                {
                    // check to see if override exists for user/toner
                    $where               = $user_toner_overrideTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
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
                        $user_toner_overrideData ['user_id']  = $this->user_id;
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
            catch (Exception $e)
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
        $date              = date('Y-m-d H:i:s T');
        $db                = Zend_Db_Table::getDefaultAdapter();

        $headers                 = array(
            "Request #",
            "Device",
            "Resolved Date"
        );
        $userRows                = array();
        $this->view->headerArray = $headers;

        $db->beginTransaction();
        // get list of requests that are not completed from database
        try
        {
            $requests             = new Proposalgen_Model_DbTable_PFModelRequest();
            $this->view->requests = $requests->select();

            $where       = $requests->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
            $allRequests = $requests->fetchAll($where);
            $db->commit();
            foreach ($allRequests as $key)
            {
                $userRows [] = $key;
            }
        }
        catch (Zend_Db_Exception $e)
        {
            $db->rollback();
        }
        catch (Exception $e)
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
        $db            = Zend_Db_Table::getDefaultAdapter();
        $removeRequest = new Proposalgen_Model_DbTable_PFModelRequest();
        $request_id    = $_POST ['request_id'];

        $db->beginTransaction();
        try
        {
            // destroying the request and commiting the change to the database.
            $where = $removeRequest->getAdapter()->quoteInto('request_id = ?', $request_id, 'INTEGER');
            $removeRequest->delete($where);
            $db->commit();
        }
        catch (Zend_Db_Exception $e)
        {
            $db->rollback();
            $this->view->message = 'Database Error: Request could not be removed.';
        }
        catch (Exception $e)
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
        $db         = Zend_Db_Table::getDefaultAdapter();
        $company_id = $_GET ['companyid'];

        $where = 'p.priv_id > 0';
        if ($company_id > 0)
        {
            $where = 'p.priv_id = ' . $company_id;
        }

        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array(
                        'u' => 'users'
                   ))
            ->joinLeft(array(
                            'dc' => 'dealer_company'
                       ), 'dc.dealer_company_id = u.dealer_company_id')
            ->joinLeft(array(
                            'up' => 'user_privileges'
                       ), 'up.user_id = u.user_id')
            ->joinLeft(array(
                            'p' => 'privileges'
                       ), 'p.priv_id = up.priv_id')
            ->where($where)
            ->order('lastname', 'firstname');
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();

        $i        = 0;
        $responce = null;
        if (count($result) > 0)
        {
            foreach ($result as $row)
            {
                $responce->rows [$i] ['id']   = $row ['user_id'];
                $responce->rows [$i] ['cell'] = array(
                    $row ['user_id'],
                    ucwords(strtolower($row ['lastname'])) . ', ' . ucwords(strtolower($row ['firstname'])) . ' (' . strtolower($row ['username']) . ')'
                );
                $i++;
            }
        }
        else
        {
            $responce->rows [$i] ['id']   = 0;
            $responce->rows [$i] ['cell'] = array(
                0,
                ''
            );
        }
        echo json_encode($responce);
    }

    public function getPricingMargin ($type, $dealer_id = null)
    {
        $db             = Zend_Db_Table::getDefaultAdapter();
        $master_margin  = 0;
        $dealer_margin  = 0;
        $user_margin    = 0;
        $pricing_margin = 0;

        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array(
                        'u' => 'users'
                   ))
            ->joinLeft(array(
                            'dc' => 'dealer_company'
                       ), 'dc.dealer_company_id = u.dealer_company_id')
            ->where('dc.company_name = "MASTER"');
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();
        if (count($result) > 0)
        {
            $master_margin = $result [0] ['dc_pricing_margin'];
        }

        $select = new Zend_Db_Select($db);
        if ($dealer_id > 0)
        {
            $select = $db->select()
                ->from(array(
                            'dc' => 'dealer_company'
                       ))
                ->where('dc.dealer_company_id = ' . $dealer_id);
        }
        else
        {
            $select = $db->select()
                ->from(array(
                            'u' => 'users'
                       ))
                ->joinLeft(array(
                                'dc' => 'dealer_company'
                           ), 'dc.dealer_company_id = u.dealer_company_id')
                ->where('u.user_id = ' . $this->user_id);
        }
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();
        if (count($result) > 0)
        {
            $dealer_margin = $result [0] ['dc_pricing_margin'];
        }

        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array(
                        'u' => 'users'
                   ))
            ->where('u.user_id = ?', $this->user_id);
        $stmt   = $db->query($select);
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
        $db                 = Zend_Db_Table::getDefaultAdapter();
        $this->view->title  = 'Manage My Reports';
        $this->view->filter = array(
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
            $formData = $this->_request->getPost();

            $db->beginTransaction();
            try
            {
                if ($formData ['form_mode'] == 'view' && $formData ['report_id'] > 0)
                {
                    $session            = new Zend_Session_Namespace('proposalgenerator_report');
                    $session->report_id = $formData ["report_id"];
                    $this->_redirect('/report');
                }
                else if ($formData ['form_mode'] == 'delete')
                {
                    $response = 1;
                    foreach ($formData as $key => $value)
                    {
                        if (strstr($key, "jqg_reports_list_"))
                        {
                            $report_id = str_replace("jqg_reports_list_", "", $key);
                            $response  = $this->deleteReport($report_id);
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
            catch (Exception $e)
            {
                $db->rollback();
                $this->view->message = "There was an error while trying to delete the reports. Please contact your administrator.";
            }
        }
    }

    /**
     * Creates a json report list
     *
     * @throws Exception
     */
    public function myreportslistAction ()
    {
        $filter   = $this->_getParam('filter', false);
        $formData = new stdClass();

        /*
         * Valid filter options:
         * 1 - Lists finished reports that the logged in user owns
         * 2 - Lists unfinished reports that the logged in user owns
         * 3- DEPRECATED (All reports within a company)
         * Default: All reports (both finished and unfinished) that the logged in user owns
         */

        try
        {
            $reportsMapper = Proposalgen_Model_Mapper_Report::getInstance();
            if ($filter == '1')
            {
                $reports = $reportsMapper->fetchAllFinishedReportsForUser($this->user_id);
            }
            elseif ($filter == '2')
            {
                $reports = $reportsMapper->fetchAllUnfinishedReportsForUser($this->user_id);
            }
            else
            {
                $reports = $reportsMapper->fetchAllReportsForUser($this->user_id);
            }

            /*
             * Massage our list of reports into a json response that can be used by jqGrid
             */
            if (count($reports) > 0)
            {
                foreach ($reports as $report)
                {
                    $finishedString = (strcasecmp($report->reportStage, Proposalgen_Model_Report_Step::STEP_FINISHED) === 0) ? "YES" : "NO";

                    $row = array(
                        'id'   => $report->id,
                        'cell' => array(
                            $report->id,
                            $report->getUser()->username,
                            $report->customerCompanyName,
                            $this->convertDate($report->dateCreated),
                            $this->convertDate($report->lastModified),
                            $finishedString,
                            null
                        )
                    );

                    $formData->rows[] = $row;
                }
            }
        }
        catch (Exception $e)
        {
            My_Log::logException($e);
            throw new Exception("Passing exception up the chain.", 0, $e);
        }

        /*
         * Send json response
         */
        $this->_helper->json($formData);
    }

    /**
     * Deletes a report.
     *
     * @param int $reportId
     *
     * @return int
     * @throws Exception
     */
    public function deleteReport ($reportId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $reportMapper        = Proposalgen_Model_Mapper_Report::getInstance();
        $reportSettingMapper = Proposalgen_Model_Mapper_Report_Setting::getInstance();
        $report              = $reportMapper->find($reportId);

        if ($report)
        {
            $db->beginTransaction();
            try
            {
                /*
                 * When we delete a report, the following should get deleted before trying to delete the report.
                 * Report Report Settings
                 * Report Settings
                 */
                $reportSettingMapper->delete($report->getReportSettings());
                $reportMapper->delete($report);
                $db->commit();

                return 1;
            }
            catch (Exception $e)
            {
                My_Log::logException($e);
                $db->rollback();
                throw new Exception("Error Deleting Report.", 0, $e);
            }
        }
    }

    public function bulkdevicepricingAction ()
    {
        $this->view->title       = "Update Pricing";
        $this->view->parts_list  = array();
        $this->view->device_list = array();
        $db                      = Zend_Db_Table::getDefaultAdapter();

        // FIXME: Hardcoded default price and default service

        $this->view->default_price   = 1000;
        $this->view->default_service = 0.0035;
        // fill companies
        //$dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        //$dealer_companies = $dealer_companyTable->fetchAll('isDeleted = false', 'company_name');
        //$this->view->company_list = $dealer_companies;


        // fill manufacturers dropdown
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('isDeleted = false', 'fullName');
        $this->view->manufacturer_list = $manufacturers;

        // get default prices
        //$dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        //$where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
        //$dealer_company = $dealer_companyTable->fetchRow($where);

        /*
        if (count($dealer_company) > 0)
        {
            $this->view->default_price = money_format('%i', $dealer_company ['dc_default_printer_cost']);
            $this->view->default_service = money_format('%.4n', $dealer_company ['dc_service_cost_per_page']);
        }
        */
        if ($this->_request->isPost())
        {
            $summary   = "";
            $passvalid = 0;
            $formData  = $this->_request->getPost();

            // check post back for update
            $db->beginTransaction();
            try
            {

                // return current dropdown states
                // $this->view->company_filter = $formData ['company_filter'];
                $this->view->pricing_filter  = $formData ['pricing_filter'];
                $this->view->search_filter   = $formData ['criteria_filter'];
                $this->view->search_criteria = $formData ['txtCriteria'];
                $this->view->repop_page      = $formData ["hdnPage"];

                if ($formData ['hdnMode'] == "update")
                {
                    //$dealer_company_id = $formData ['company_filter'];
                    //$dealer_company_id = 1;
                    // Save Master Company Pricing Changes
                    if ($formData ['pricing_filter'] == 'toner')
                    {
                        // loop through $result
                        foreach ($formData as $key => $value)
                        {
                            if (strstr($key, "txtTonerPrice"))
                            {
                                $toner_id = str_replace("txtTonerPrice", "", $key);
                                $price    = $formData ['txtTonerPrice' . $toner_id];
                                // check if new price is populated.
                                if ($price == "0")
                                {
                                    $passvalid           = 1;
                                    $this->view->message = "All values must be greater than 0. Please correct it and try again.";
                                    break;
                                }
                                else if ($price != '' && !is_numeric($price))
                                {
                                    $passvalid           = 1;
                                    $this->view->message = "All values must be numeric. Please correct it and try again.";
                                    break;
                                }
                                else if ($price != '')
                                {
                                    $tonerTable = new Proposalgen_Model_DbTable_Toner();
                                    $tonerData  = array(
                                        'cost' => $price
                                    );

                                    $where = $tonerTable->getAdapter()->quoteInto('id = ?', $toner_id, 'INTEGER');
                                    $tonerTable->update($tonerData, $where);
                                    $summary .= "Updated part from " . $key ['cost'] . " to " . $price . "<br />";
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
                            foreach ($formData as $key => $value)
                            {
                                if (strstr($key, "txtTonerPrice"))
                                {
                                    $toner_id = str_replace("txtTonerPrice", "", $key);
                                    $price    = $formData ['txtTonerPrice' . $toner_id];

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
                        foreach ($formData as $key => $value)
                        {

                            if (strstr($key, "txtDevicePrice"))
                            {
                                $master_device_id = str_replace("txtDevicePrice", "", $key);
                                $price            = $formData ['txtDevicePrice' . $master_device_id];
                                // check if new price is populated.
                                if ($price != '' && !is_numeric($price))
                                {
                                    $passvalid           = 1;
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
                                        $master_deviceData = array(
                                            'labor_cost_per_page' => $price
                                        );
                                    }
                                    else if ($formData ['pricing_filter'] == 'parts')
                                    {
                                        $master_deviceData = array(
                                            'parts_cost_per_page' => $price
                                        );
                                    }
                                    else
                                    {
                                        $master_deviceData = array(
                                            'cost' => $price
                                        );
                                    }
                                    $where = $master_deviceTable->getAdapter()->quoteInto('id = ?', $master_device_id, 'INTEGER');
                                    $master_deviceTable->update($master_deviceData, $where);
                                    $summary .= "Updated " . $key ['fullname'] . ' ' . $key ['printer_model'] . ' from ' . $key ['cost'] . ' to ' . $price . '<br />';
                                }
                            }
                        }

                        if ($passvalid == 0)
                        {
                            if (!empty($summary))
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
                            foreach ($formData as $key => $value)
                            {
                                if (strstr($key, "txtDevicePrice"))
                                {
                                    $master_device_id = str_replace("txtDevicePrice", "", $key);
                                    $price            = $formData ['txtDevicePrice' . $master_device_id];

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
            catch (Exception $e)
            {
                $db->rollback();
                $this->view->message = "Error: The updates were not saved.";
            }
        }
    }

    public function bulkpartspricingAction ()
    {
        $this->view->title      = "Bulk Toner Pricing Update";
        $this->view->parts_list = array();
        $db                     = Zend_Db_Table::getDefaultAdapter();
        $master_deviceTable     = new Proposalgen_Model_DbTable_MasterDevice();

        // fill manufacturers dropdown
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('is_deleted = false', 'manufacturer_name');
        $this->view->manufacturer_list = $manufacturers;

        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array(
                        't' => 'toner'
                   ))
            ->joinLeft(array(
                            'tm' => 'manufacturer'
                       ), 'tm.manufacturer_id = t.manufacturerId')
            ->joinLeft(array(
                            'dt' => 'device_toner'
                       ), 'dt.toner_id = t.toner_id')
            ->joinLeft(array(
                            'md' => 'master_device'
                       ), 'md.master_device_id = dt.master_device_id')
            ->joinLeft(array(
                            'mm' => 'manufacturer'
                       ), 'mm.manufacturer_id = md.mastdevice_manufacturer')
            ->joinLeft(array(
                            'tc' => 'toner_color'
                       ), 'tc.toner_color_id = t.tonerColorId')
            ->group('t.toner_id')
            ->order(array(
                         'mm.manufacturer_name',
                         't.toner_SKU'
                    ));
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();

        // fill devices_array
        $devices_array = array();
        foreach ($result as $key)
        {
            $toner_devices      = $db->select()
                ->from(array(
                            'md' => 'master_device'
                       ))
                ->joinLeft(array(
                                'dt' => 'device_toner'
                           ), 'dt.master_device_id = md.master_device_id')
                ->joinLeft(array(
                                'm' => 'manufacturer'
                           ), 'm.manufacturer_id = md.mastdevice_manufacturer')
                ->where('dt.toner_id = ?', $key ['toner_id'], 'INTEGER')
                ->order(array(
                             'manufacturer_name',
                             'printer_model'
                        ));
            $stmt               = $db->query($toner_devices);
            $toner_devices_list = $stmt->fetchAll();

            foreach ($toner_devices_list as $key2)
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
            $summary   = "";
            $passvalid = 0;
            $formData  = $this->_request->getPost();

            // check post back for update
            $db->beginTransaction();
            try
            {
                if ($formData ['hdnMode'] == "update")
                {
                    // loop through $result
                    foreach ($formData as $key => $value)
                    {
                        if (strstr($key, "txtPrice"))
                        {
                            $toner_id = str_replace("txtPrice", "", $key);
                            $price    = $formData ['txtPrice' . $toner_id];

                            // check if new price is populated.
                            if ($price != '' && !is_numeric($price))
                            {
                                $passvalid           = 1;
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
                                $tonerData  = array(
                                    'toner_price' => $price
                                );
                                $where      = $tonerTable->getAdapter()->quoteInto('toner_id = ?', $toner_id, 'INTEGER');
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
            catch (Exception $e)
            {
                $db->rollback();
                $this->view->message = "Error: The updates were not saved.";
            }
        }

        // send results to screen to populate grid
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();
        if (count($result) > 0)
        {
            $this->view->parts_list = $result;
        }
    }

    public function bulkdealerdevicepricingAction ()
    {
        $this->view->title       = "Update Company Pricing";
        $this->view->device_list = array();
        $db                      = Zend_Db_Table::getDefaultAdapter();

        // fill manufacturers dropdown
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('is_deleted = false', 'manufacturer_name');
        $this->view->manufacturer_list = $manufacturers;

        // get master company default prices
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where               = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', 1, 'INTEGER');
        $dealer_company      = $dealer_companyTable->fetchRow($where);

        if (count($dealer_company) > 0)
        {
            $this->view->default_price   = money_format('%i', $dealer_company ['dc_default_printer_cost']);
            $this->view->default_service = money_format('%.4n', $dealer_company ['dc_service_cost_per_page']);
        }

        // get dealer company default prices
        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where               = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $this->dealer_company_id, 'INTEGER');
        $dealer_company      = $dealer_companyTable->fetchRow($where);

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
            $summary   = "";
            $passvalid = 0;
            $formData  = $this->_request->getPost();

            // check post back for update
            $db->beginTransaction();
            try
            {
                // return current dropdown states
                $this->view->pricing_filter  = $formData ['pricing_filter'];
                $this->view->search_filter   = $formData ['criteria_filter'];
                $this->view->search_criteria = $formData ['txtCriteria'];

                if ($formData ['hdnMode'] == "update")
                {
                    if ($formData ['pricing_filter'] == 'toner')
                    {
                        foreach ($formData as $key => $value)
                        {
                            if (strstr($key, "txtTonerPrice"))
                            {
                                $toner_id = str_replace("txtTonerPrice", "", $key);

                                // check if new price is populated.
                                if ($formData ['txtTonerPrice' . $toner_id] != $formData ['hdnTonerPrice' . $toner_id])
                                {
                                    $dealer_toner_overrideTable = new Proposalgen_Model_DbTable_DealerTonerOverride();
                                    $where                      = $dealer_toner_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $this->dealer_company_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                                    $price                      = $formData ['txtTonerPrice' . $toner_id];

                                    // delete entry if blanked out
                                    if ($price != '' && !is_numeric($price))
                                    {
                                        $passvalid           = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {
                                        $dealer_toner_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {
                                        $dealer_toner_overrideData = array(
                                            'dealer_company_id'    => $this->dealer_company_id,
                                            'toner_id'             => $toner_id,
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
                        foreach ($formData as $key => $value)
                        {
                            if (strstr($key, "txtDevicePrice"))
                            {
                                $master_device_id = str_replace("txtDevicePrice", "", $key);

                                // check if new price is populated.
                                if ($formData ['txtDevicePrice' . $master_device_id] != $formData ['hdnDevicePrice' . $master_device_id])
                                {
                                    $dealer_laborCPP_overrideTable = new Proposalgen_Model_DbTable_DealerLaborCPPOverride();
                                    $where                         = $dealer_laborCPP_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $this->dealer_company_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                    $price                         = $formData ['txtDevicePrice' . $master_device_id];

                                    // delete entry if blanked out
                                    if ($price != '' && !is_numeric($price))
                                    {
                                        $passvalid           = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {
                                        $dealer_laborCPP_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {
                                        $dealer_device_overrideData = array(
                                            'dealer_company_id'  => $this->dealer_company_id,
                                            'master_device_id'   => $master_device_id,
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
                        foreach ($formData as $key => $value)
                        {
                            if (strstr($key, "txtDevicePrice"))
                            {
                                $master_device_id = str_replace("txtDevicePrice", "", $key);

                                // check if new price is populated.
                                if ($formData ['txtDevicePrice' . $master_device_id] != $formData ['hdnDevicePrice' . $master_device_id])
                                {
                                    $dealer_partsCPP_overrideTable = new Proposalgen_Model_DbTable_DealerPartsCPPOverride();
                                    $where                         = $dealer_partsCPP_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $this->dealer_company_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                    $price                         = $formData ['txtDevicePrice' . $master_device_id];

                                    // delete entry if blanked out
                                    if ($price != '' && !is_numeric($price))
                                    {
                                        $passvalid           = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {
                                        $dealer_partsCPP_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {
                                        $dealer_device_overrideData = array(
                                            'dealer_company_id'  => $this->dealer_company_id,
                                            'master_device_id'   => $master_device_id,
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
                        foreach ($formData as $key => $value)
                        {
                            if (strstr($key, "txtDevicePrice"))
                            {
                                $master_device_id = str_replace("txtDevicePrice", "", $key);

                                // check if new price is populated.
                                if ($formData ['txtDevicePrice' . $master_device_id] != $formData ['hdnDevicePrice' . $master_device_id])
                                {
                                    $dealer_device_overrideTable = new Proposalgen_Model_DbTable_DealerDeviceOverride();
                                    $where                       = $dealer_device_overrideTable->getAdapter()->quoteInto('dealer_company_id = ' . $this->dealer_company_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                    $price                       = $formData ['txtDevicePrice' . $master_device_id];

                                    // delete entry if blanked out
                                    if ($price != '' && !is_numeric($price))
                                    {
                                        $passvalid           = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {
                                        $dealer_device_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {
                                        $dealer_device_overrideData = array(
                                            'dealer_company_id'     => $this->dealer_company_id,
                                            'master_device_id'      => $master_device_id,
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
            catch (Exception $e)
            {
                $db->rollback();
                $this->view->message = "Error: The updates were not saved.";
            }
        }
    }

    public function bulkuserpricingAction ()
    {
        $this->view->title       = "Update My Pricing";
        $this->view->device_list = array();
        $db                      = Zend_Db_Table::getDefaultAdapter();

        // fill manufacturers dropdown
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('isDEleted = false', 'fullname');
        $this->view->manufacturer_list = $manufacturers;
        // FIXME: Hardcoded default price and default service

        $this->view->default_price   = 1000;
        $this->view->default_service = 0.0035;

        if ($this->_request->isPost())
        {
            $summary   = "";
            $passvalid = 0;
            $formData  = $this->_request->getPost();

            // check post back for update
            $db->beginTransaction();
            try
            {
                // return current dropdown states
                $this->view->pricing_filter  = $formData ['pricing_filter'];
                $this->view->search_filter   = $formData ['criteria_filter'];
                $this->view->search_criteria = $formData ['txtCriteria'];
                $this->view->repop_page      = $formData ["hdnPage"];

                if ($formData ['hdnMode'] == "update")
                {
                    if ($formData ['pricing_filter'] == 'toner')
                    {
                        foreach ($formData as $key => $value)
                        {
                            if (strstr($key, "txtTonerPrice"))
                            {
                                $toner_id = str_replace("txtTonerPrice", "", $key);

                                // check if new price is populated.
                                if ($formData ['txtTonerPrice' . $toner_id] != $formData ['hdnTonerPrice' . $toner_id])
                                {
                                    $user_toner_overrideTable = new Proposalgen_Model_DbTable_UserTonerOverride();
                                    $where                    = $user_toner_overrideTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND toner_id = ?', $toner_id, 'INTEGER');
                                    $price                    = $formData ['txtTonerPrice' . $toner_id];

                                    // delete entry if blanked out
                                    if ($price != '' && !is_numeric($price))
                                    {
                                        $passvalid           = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {
                                        $user_toner_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {
                                        $user_toner_overrideData = array(
                                            'user_id'  => $this->user_id,
                                            'toner_id' => $toner_id,
                                            'cost'     => $price
                                        );

                                        // check to see if device override
                                        // exists
                                        $user_toner_override = $user_toner_overrideTable->fetchRow($where);

                                        if (count($user_toner_override) > 0)
                                        {
                                            $user_toner_overrideTable->update($user_toner_overrideData, $where);
                                            $summary .= "Updated " . ucwords(strtolower($key ['fullname'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['cost'] . ' to ' . $price . '<br />';
                                        }
                                        else
                                        {
                                            $user_toner_overrideTable->insert($user_toner_overrideData);
                                            $summary .= "Updated " . ucwords(strtolower($key ['fullname'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['cost'] . ' to ' . $price . '<br />';
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
                            foreach ($formData as $key => $value)
                            {
                                if (strstr($key, "txtTonerPrice"))
                                {
                                    $toner_id = str_replace("txtTonerPrice", "", $key);
                                    $price    = $formData ['txtTonerPrice' . $toner_id];

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
                        foreach ($formData as $key => $value)
                        {
                            if (strstr($key, "txtDevicePrice"))
                            {
                                $master_device_id = str_replace("txtDevicePrice", "", $key);
                                // check if new price is populated.
                                if ($formData ['txtDevicePrice' . $master_device_id] != $formData ['hdnDevicePrice' . $master_device_id])
                                {

                                    $user_device_overrideTable = new Proposalgen_Model_DbTable_UserDeviceOverride();
                                    $where                     = $user_device_overrideTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                    $price                     = $formData ['txtDevicePrice' . $master_device_id];

                                    // delete entry if blanked out
                                    if ($price != '' && !is_numeric($price))
                                    {

                                        $passvalid           = 1;
                                        $this->view->message = "Value must be numeric. Please correct it and try again.";
                                        break;
                                    }
                                    else if ($price == "0")
                                    {

                                        $user_device_overrideTable->delete($where);
                                    }
                                    else if ($price > 0)
                                    {

                                        $user_device_overrideData = array(
                                            'user_id'          => $this->user_id,
                                            'master_device_id' => $master_device_id,
                                            'cost'             => $price
                                        );

                                        // check to see if device override
                                        // exists
                                        $user_device_override = $user_device_overrideTable->fetchRow($where);


                                        if (count($user_device_override) > 0)
                                        {
                                            $user_device_overrideTable->update($user_device_overrideData, $where);
                                            $summary .= "Updated " . ucwords(strtolower($key ['fullname'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['cost'] . ' to ' . $price . '<br />';
                                        }
                                        else
                                        {
                                            $user_device_overrideTable->insert($user_device_overrideData);
                                            $summary .= "Updated " . ucwords(strtolower($key ['fullname'])) . ' ' . ucwords(strtolower($key ['printer_model'])) . ' from ' . $key ['device_price'] . ' to ' . $price . '<br />';
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
                            foreach ($formData as $key => $value)
                            {
                                if (strstr($key, "txtDevicePrice"))
                                {
                                    $master_device_id = str_replace("txtDevicePrice", "", $key);
                                    $price            = $formData ['txtDevicePrice' . $master_device_id];

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
            catch (Exception $e)
            {
                $db->rollback();
                $this->view->message = "Error: The updates were not saved.";
                Throw new exception("An error has occurred deleting replacement printers.", 0, $e);
            }
        }
    }

    public function bulkfilepricingAction ()
    {
        Zend_Session::start();
        $this->view->title = "Import & Export Pricing";
        $db                = Zend_Db_Table::getDefaultAdapter();
        $headers           = array();
        $results           = array();

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            $company                    = 1;
            $this->view->company_filter = $company;

            // hdnRole is used when logged in as a dealer to differentiatek
            // between if the dealer is on "update company pricing" or "update
            // my pricing"
            $hdnRole = $formData ['hdnRole'];

            if (isset($formData ['hdnMode']))
            {
                // ************************************************************/
                // * Initial Page Load
                // ************************************************************/
            }
            else
            {

                // ************************************************************/
                // * Upload File
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
                // mimimum 100B
                $upload->addValidator('FilesSize', false, array(
                                                               'min' => '100B',
                                                               'max' => '4MB'
                                                          ));

                $upload->getValidator('FilesSize')->setMessage('<span class="warning">*</span> File size must be between 100B and 4MB.');
                if ($upload->receive())
                {
                    $is_valid      = true;
                    $columns       = array();
                    $headers       = array();
                    $final_devices = array();
                    $finalDevices  = array();

                    $db->beginTransaction();
                    try
                    {

                        $lines = file($upload->getFileName(), FILE_IGNORE_NEW_LINES);

                        // grab the first row of items(the column headers)
                        $headers = str_getcsv(strtolower($lines [0]));

                        // detect file type (printers or toners)
                        $array_key = 0;

                        // default column keys
                        $key_toner_id          = null;
                        $key_manufacturer      = null;
                        $key_part_type         = null;
                        $key_sku               = null;
                        $key_color             = null;
                        $key_yield             = null;
                        $key_new_price         = null;
                        $key_master_printer_id = null;
                        $key_printer_model     = null;

                        foreach ($headers as $key => $value)
                        {
                            if (strtolower($value) == "toner id")
                            {
                                $import_type  = "toner";
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
                                $import_type           = "printer";
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
                            // create an associative array of the csv information
                            foreach ($lines as $key => $value)
                            {
                                if ($key > 0)
                                {
                                    $devices [$key] = str_getcsv($value);

                                    // get current pricing
                                    if ($import_type == "printer")
                                    {

                                        $current_device_price = 0;
                                        $current_parts_cpp    = 0;
                                        $current_labor_cpp    = 0;

                                        $master_device_id = $devices [$key] [0];
                                        if (in_array("System Admin", $this->privilege))
                                        {

                                            $columns [0] = "Master Printer ID";
                                            $columns [1] = "Manufacturer";
                                            $columns [2] = "Printer Model";
                                            $columns [3] = "Current Price";
                                            $columns [4] = "New Price";

                                            $table   = new Proposalgen_Model_DbTable_MasterDevice();
                                            $where   = $table->getAdapter()->quoteInto('id = ?', $master_device_id, 'INTEGER');
                                            $printer = $table->fetchRow($where);

                                            if (count($printer) > 0)
                                            {
                                                // get current costs
                                                $current_device_price = $printer ['cost'];

                                                // save into array
                                                $final_devices [0] = $master_device_id;
                                                $final_devices [1] = $devices [$key] [$key_manufacturer];
                                                $final_devices [2] = $devices [$key] [$key_printer_model];
                                                $final_devices [3] = $current_device_price;
                                                $final_devices [4] = $devices [$key] [$key_new_price];
                                            }
                                        }
                                        else if ($this->view->hdnRole != "user" && (!in_array("Standard User", $this->privilege)))
                                        {
                                            $columns [0] = "Master Printer ID";
                                            $columns [1] = "Manufacturer";
                                            $columns [2] = "Printer Model";
                                            $columns [3] = "Master Price";
                                            $columns [4] = "Override Price";
                                            $columns [5] = "New Override Price";

                                            $select  = $db->select()
                                                ->from(array(
                                                            'md' => 'pgen_master_devices'
                                                       ), array(
                                                               'cost'
                                                          ))
                                                ->where('md.id = ' . $master_device_id);
                                            $stmt    = $db->query($select);
                                            $printer = $stmt->fetchAll();

                                            if (count($printer) > 0)
                                            {
                                                // get current costs
                                                $current_device_price   = $printer [0] ['cost'];
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

                                            $select  = $db->select()
                                                ->from(array(
                                                            'md' => 'pgen_master_devices'
                                                       ), array(
                                                               'cost'
                                                          ))
                                                ->joinLeft(array(
                                                                'udo' => 'pgen_user_device_overrides'
                                                           ), 'udo.master_device_id = md.id AND udo.user_id = ' . $this->user_id, array(
                                                                                                                                       'cost AS overideCost'
                                                                                                                                  ))
                                                ->where('md.id = ' . $master_device_id);
                                            $stmt    = $db->query($select);
                                            $printer = $stmt->fetchAll();

                                            if (count($printer) > 0)
                                            {
                                                // get current costs
                                                $current_device_price   = $printer [0] ['cost'];
                                                $current_override_price = $printer [0] ['overideCost'];

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
                                        if (in_array("System Admin", $this->privilege))
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
                                            $where = $table->getAdapter()->quoteInto('id = ?', $toner_id, 'INTEGER');
                                            $toner = $table->fetchRow($where);

                                            if (count($toner) > 0)
                                            {
                                                // get current costs
                                                $current_toner_price = $toner ['cost'];

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
                                        else if ($this->view->hdnRole != "user" && (!in_array("Standard User", $this->privilege)))
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
                                                ->from(array(
                                                            't' => 'pgen_toners'
                                                       ), array(
                                                               'cost'
                                                          ))
                                                ->where('t.id = ?', $toner_id);
                                            $stmt   = $db->query($select);
                                            $toner  = $stmt->fetchAll();

                                            if (count($toner) > 0)
                                            {
                                                // get current costs
                                                $current_toner_price    = $toner [0] ['cost'];
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
                                                ->from(array(
                                                            't' => 'toner'
                                                       ), array(
                                                               'toner_price'
                                                          ))
                                                ->joinLeft(array(
                                                                'uto' => 'user_toner_override'
                                                           ), 'uto.toner_id = t.toner_id AND uto.user_id = ' . $this->user_id, array(
                                                                                                                                    'override_toner_price'
                                                                                                                               ))
                                                ->where('t.toner_id = ?', $toner_id);
                                            $stmt   = $db->query($select);
                                            $toner  = $stmt->fetchAll();

                                            if (count($toner) > 0)
                                            {
                                                // get current costs
                                                $current_toner_price    = $toner [0] ['toner_price'];
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
                            $columns = $columns;
                            $results = $finalDevices;
                            $db->beginTransaction();
                            try
                            {
                                // detect file type (printers or toners)
                                $import_type = "printer";

                                foreach ($columns as $value)
                                {
                                    if (strtolower($value) == "toner id")
                                    {
                                        $import_type = "toner";
                                        break;
                                    }
                                }
                                // loop through file and save
                                foreach ($results as $key => $value)
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
                                        if (in_array("System Admin", $this->privilege))
                                        {
                                            $master_device_id  = $results[$key] ['Master Printer ID'];
                                            $manufacturer_name = $results[$key] ['Manufacturer'];
                                            $printer_model     = $results[$key] ['Printer Model'];
                                            $device_price      = $results[$key] ['New Price'];

                                            $table = new Proposalgen_Model_DbTable_MasterDevice();
                                            $data  = array(
                                                'cost' => $device_price
                                            );
                                            $where = $table->getAdapter()->quoteInto('id = ?', $master_device_id, 'INTEGER');

                                            // check to see if it exists - no inserts in the
                                            // Master Tables
                                            $toner = $table->fetchRow($where);
                                            if (count($toner) > 0)
                                            {
                                                $exists = true;

                                                // don't allow price of 0
                                                if ($device_price == 0)
                                                {
                                                    $device_price = null;
                                                }

                                                // don't update if values match
                                                if ($toner ['cost'] != $device_price)
                                                {
                                                    $update = true;
                                                }
                                            }
                                        }
                                        else if ($hdnRole != "user" && (!in_array("Standard User", $this->privilege)))
                                        {
                                            $master_device_id  = $results->array [$key] ['Master Printer Id'];
                                            $manufacturer_name = $results->array [$key] ['Manufacturer'];
                                            $printer_model     = $results->array [$key] ['Printer Model'];
                                            $device_price      = $results->array [$key] ['New Override Price'];

                                            $table = new Proposalgen_Model_DbTable_DealerDeviceOverride();
                                            $data  = array(
                                                'override_device_price' => $device_price
                                            );
                                            $where = $table->getAdapter()->quoteInto('dealer_company_id = ' . $company . ' AND master_device_id = ?', $master_device_id, 'INTEGER');

                                            // check to see if it exists
                                            $select = new Zend_Db_Select($db);
                                            $select = $db->select()
                                                ->from(array(
                                                            'md' => 'master_device'
                                                       ), array(
                                                               'device_price'
                                                          ))
                                                ->joinLeft(array(
                                                                'ddo' => 'dealer_device_override'
                                                           ), 'ddo.master_device_id = md.master_device_id AND ddo.dealer_company_id = ' . $company, array(
                                                                                                                                                         'override_device_price'
                                                                                                                                                    ))
                                                ->where('md.master_device_id = ' . $master_device_id);
                                            $stmt   = $db->query($select);
                                            $toner  = $stmt->fetchAll();

                                            if (count($toner) > 0)
                                            {
                                                if ($toner [0] ['override_device_price'] > 0)
                                                {
                                                    $exists = true;

                                                    // don't update if values match
                                                    if ($device_price == 0 || empty($device_price))
                                                    {
                                                        $delete = true;
                                                    }
                                                    else if ($toner [0] ['device_price'] != $device_price)
                                                    {
                                                        $update = true;
                                                    }
                                                }
                                                else
                                                {
                                                    $exists = false;
                                                    if ($device_price > 0 && $toner [0] ['device_price'] != $device_price)
                                                    {
                                                        $insert = true;

                                                        $data ['dealer_company_id'] = $company;
                                                        $data ['master_device_id']  = $master_device_id;
                                                    }
                                                }
                                            }
                                        }
                                        else if (in_array("Standard User", $this->privilege) || $hdnRole == "user")
                                        {

                                            $master_device_id  = $results->array [$key] ['Master Printer Id'];
                                            $manufacturer_name = $results->array [$key] ['Manufacturer'];
                                            $printer_model     = $results->array [$key] ['Printer Model'];
                                            $device_price      = $results->array [$key] ['New Override Price'];

                                            $table = new Proposalgen_Model_DbTable_UserDeviceOverride();
                                            $data  = array(
                                                'override_device_price' => $device_price
                                            );
                                            $where = $table->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');

                                            // check to see if it exists
                                            $select = new Zend_Db_Select($db);
                                            $select = $db->select()
                                                ->from(array(
                                                            'md' => 'master_device'
                                                       ), array(
                                                               'device_price'
                                                          ))
                                                ->joinLeft(array(
                                                                'udo' => 'user_device_override'
                                                           ), 'udo.master_device_id = md.master_device_id AND udo.user_id = ' . $this->user_id, array(
                                                                                                                                                     'override_device_price'
                                                                                                                                                ))
                                                ->where('md.master_device_id = ' . $master_device_id);
                                            $stmt   = $db->query($select);
                                            $toner  = $stmt->fetchAll();

                                            if (count($toner) > 0)
                                            {
                                                if ($toner [0] ['override_device_price'] > 0)
                                                {
                                                    $exists = true;

                                                    // don't update if values match
                                                    if ($device_price == 0 || empty($device_price))
                                                    {
                                                        $delete = true;
                                                    }
                                                    else if ($toner [0] ['device_price'] != $device_price)
                                                    {
                                                        $update = true;
                                                    }
                                                }
                                                else
                                                {
                                                    $exists = false;
                                                    if ($device_price > 0 && $toner [0] ['device_price'] != $device_price)
                                                    {
                                                        $insert = true;

                                                        $data ['user_id']          = $this->user_id;
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
                                            $toner_id          = $results[$key] ['Toner ID'];
                                            $manufacturer_name = $results[$key] ['Manufacturer'];
                                            $toner_sku         = $results[$key] ['SKU'];
                                            $toner_price       = $results[$key] ['New Price'];

                                            $table = new Proposalgen_Model_DbTable_Toner();
                                            $data  = array(
                                                'cost' => $toner_price
                                            );
                                            $where = $table->getAdapter()->quoteInto('id = ?', $toner_id, 'INTEGER');

                                            // check to see if it exists - no inserts in the
                                            // Master Tables
                                            $toner = $table->fetchRow($where);
                                            if (count($toner) > 0)
                                            {
                                                $exists = true;

                                                // don't update if values match
                                                if (($toner ['cost'] != $toner_price) && $toner_price > 0)
                                                {
                                                    $update = true;
                                                }
                                            }
                                        }
                                        else if ($hdnRole != "user" && (!in_array("Standard User", $this->privilege) && $company > 1))
                                        {
                                            $toner_id          = $results[$key] ['Toner ID'];
                                            $manufacturer_name = $results[$key] ['Manufacturer'];
                                            $toner_sku         = $results[$key] ['SKU'];
                                            $toner_price       = $results[$key] ['New Override Price'];

                                            $table = new Proposalgen_Model_DbTable_DealerTonerOverride();
                                            $data  = array(
                                                'override_toner_price' => $toner_price
                                            );
                                            $where = $table->getAdapter()->quoteInto('dealer_company_id = ' . $company . ' AND toner_id = ?', $toner_id, 'INTEGER');

                                            // check to see if it exists
                                            $select = new Zend_Db_Select($db);
                                            $select = $db->select()
                                                ->from(array(
                                                            't' => 'toner'
                                                       ), array(
                                                               'toner_price'
                                                          ))
                                                ->joinLeft(array(
                                                                'dto' => 'dealer_toner_override'
                                                           ), 'dto.toner_id = t.toner_id AND dto.dealer_company_id = ' . $company, array(
                                                                                                                                        'override_toner_price'
                                                                                                                                   ))
                                                ->where('t.toner_id = ?', $toner_id);
                                            $stmt   = $db->query($select);
                                            $toner  = $stmt->fetchAll();

                                            if (count($toner) > 0)
                                            {
                                                if ($toner [0] ['override_toner_price'] > 0)
                                                {
                                                    $exists = true;

                                                    // don't update if values match
                                                    if ($toner_price == 0 || empty($toner_price))
                                                    {
                                                        $delete = true;
                                                    }
                                                    else if ($toner [0] ['toner_price'] != $toner_price)
                                                    {
                                                        $update = true;
                                                    }
                                                }
                                                else
                                                {
                                                    $exists = false;
                                                    if ($toner_price > 0 && $toner [0] ['toner_price'] != $toner_price)
                                                    {
                                                        $insert = true;

                                                        $data ['dealer_company_id'] = $company;
                                                        $data ['toner_id']          = $toner_id;
                                                    }
                                                }
                                            }
                                        }
                                        else if (in_array("Standard User", $this->privilege) || $hdnRole == "user")
                                        {

                                            $toner_id          = $results[$key] ['Toner ID'];
                                            $manufacturer_name = $results[$key] ['Manufacturer'];
                                            $toner_sku         = $results[$key] ['SKU'];
                                            $toner_price       = $results[$key] ['New Override Price'];

                                            $table = new Proposalgen_Model_DbTable_UserTonerOverride();
                                            $data  = array(
                                                'override_toner_price' => $toner_price
                                            );
                                            $where = $table->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND toner_id = ?', $toner_id, 'INTEGER');

                                            // check to see if it exists
                                            $select = new Zend_Db_Select($db);
                                            $select = $db->select()
                                                ->from(array(
                                                            't' => 'toner'
                                                       ), array(
                                                               'toner_price'
                                                          ))
                                                ->joinLeft(array(
                                                                'uto' => 'user_toner_override'
                                                           ), 'uto.toner_id = t.toner_id AND uto.user_id = ' . $this->user_id, array(
                                                                                                                                    'override_toner_price'
                                                                                                                               ))
                                                ->where('t.toner_id = ?', $toner_id);
                                            $stmt   = $db->query($select);
                                            $toner  = $stmt->fetchAll();

                                            if (count($toner) > 0)
                                            {
                                                if ($toner [0] ['override_toner_price'] > 0)
                                                {
                                                    $exists = true;

                                                    // don't update if values match
                                                    if ($toner_price == 0 || empty($toner_price))
                                                    {
                                                        $delete = true;
                                                    }
                                                    else if ($toner [0] ['toner_price'] != $toner_price)
                                                    {
                                                        $update = true;
                                                    }
                                                }
                                                else
                                                {
                                                    $exists = false;
                                                    if ($toner_price > 0 && $toner [0] ['toner_price'] != $toner_price)
                                                    {
                                                        $insert = true;

                                                        $data ['user_id']  = $this->user_id;
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
                            catch (Exception $e)
                            {

                                $db->rollback();
                                $this->view->message = "<span class=\"warning\">*</span> An error has occurred during the update and your changes were not applied. Please review your file and try again.";
                            }

                            //////////////////////////////////////////
                            //////////////////////////////////////////
                            ////End Saving
                            //////////////////////////////////////////
                        }
                    }
                    catch (Exception $e)
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
    }

    public function exportpricingAction ()
    {

        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        //$company = $this->_getParam('company', $this->dealer_company_id);
        $pricing = $this->_getParam('pricing', 'printer');

        // for OD default company to Master
        $company = 1;

        // hdnRole is used when logged in as a dealer to differenciate between
        // if the dealer is on "update company pricing" or "update my pricing"
        //$hdnRole = $this->_getParam('hdnRole', 'dealer');

        // get company name for filename
        /*$dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where               = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $company, 'INTEGER');
        $dealer_company      = $dealer_companyTable->fetchRow($where);
        if (count($dealer_company) > 0)
        {
            $company_name = $dealer_company ['company_name'];
        }
        */
        // filename for CSV file
        $filename = system_pricing . "_" . $pricing . "_pricing_" . date('m_d_Y') . ".csv";

        // check post back for update
        $db->beginTransaction();
        try
        {
            // Get device list
            if ($pricing == 'printer')
            {
                $fieldTitles = array(
                    'Master Printer ID',
                    'Manufacturer',
                    'Printer Model',
                    'Price'
                );
                if (in_array("System Admin", $this->privilege))
                {
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array(
                                    'md' => 'pgen_master_devices'
                               ), array(
                                       'id AS master_id',
                                       'manufacturerId',
                                       'modelName',
                                       'cost'
                                  ))
                        ->joinLeft(array(
                                        'm' => 'manufacturers'
                                   ), 'm.id = md.manufacturerId', array(
                                                                        'id',
                                                                        'fullname'
                                                                   ))
                        ->order(array(
                                     'm.fullname',
                                     'md.modelName'
                                ));
                    $stmt   = $db->query($select);
                    $result = $stmt->fetchAll();

                }
                else if (in_array("Standard User", $this->privilege))
                {
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array(
                                    'md' => 'pgen_master_devices'
                               ), array(
                                       'id AS master_id',
                                       'manufacturer_id',
                                       'printer_model',
                                       'cost'
                                  ))
                        ->joinLeft(array(
                                        'm' => 'manufacturers'
                                   ), 'm.id = md.manufacturerId', array(
                                                                        'id AS manufacturer_id',
                                                                        'fullname'
                                                                   ))
                        ->joinLeft(array(
                                        'udo' => 'pgen_user_device_overrides'
                                   ), 'udo.master_device_id = md.id AND udo.user_id = ' . $this->user_id, array(
                                                                                                               'cost AS override_cost'
                                                                                                          ))
                        ->order(array(
                                     'm.fullname',
                                     'md.printer_model'
                                ));
                    $stmt   = $db->query($select);
                    $result = $stmt->fetchAll();
                }

                foreach ($result as $key => $value)
                {
                    $price = 0;

                    // prep pricing
                    if (in_array("System Admin", $this->privilege))
                    {
                        $price = $value ['cost'];
                    }
                    else
                    {
                        $price = $value ['override_cost'];
                    }
                    $fieldList [] = array(
                        $value ['master_id'],
                        $value ['fullname'],
                        $value ['modelName'],
                        $price
                    );
                }

            }
            else
            {
                $fieldTitles = array(
                    'Toner ID',
                    'Manufacturer',
                    'Type',
                    'SKU',
                    'Color',
                    'Yield',
                    'Price'
                );

                if (in_array("System Admin", $this->privilege))
                {
                    // get count
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array(
                                    't' => 'pgen_toners'), array(
                                                                'id AS toners_id', 'sku', 'yield', 'cost'
                                                           )
                    )
                        ->joinLeft(array(
                                        'dt' => 'pgen_device_toners'
                                   ), 'dt.toner_id = t.id', array(
                                                                 'master_device_id'
                                                            ))
                        ->joinLeft(array(
                                        'tm' => 'manufacturers'
                                   ), 'tm.id = t.manufacturerId', array(
                                                                       'fullname'
                                                                  ))
                        ->joinLeft(array(
                                        'tc' => 'pgen_toner_colors'
                                   ), 'tc.id = t.tonerColorId', array('name AS toner_color'))
                        ->joinLeft(array(
                                        'pt' => 'pgen_part_types'
                                   ), 'pt.id = t.partTypeId', 'name AS part_type')
                        ->where('t.id > 0')
                        ->group('t.id')
                        ->order(array(
                                     'tm.fullname'
                                ));
                    $stmt   = $db->query($select);
                    $result = $stmt->fetchAll();
                }
                else if (in_array("Standard User", $this->privilege))
                {
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array(
                                    't' => 'pgen_toners'
                               ))
                        ->joinLeft(array(
                                        'tm' => 'manufacturer'
                                   ), 'tm.manufacturer_id = t.manufacturerId', array(
                                                                                    'manufacturer_name'
                                                                               ))
                        ->joinLeft(array(
                                        'dt' => 'device_toner'
                                   ), 'dt.toner_id = t.toner_id')
                        ->joinLeft(array(
                                        'md' => 'master_device'
                                   ), 'md.master_device_id = dt.master_device_id')
                        ->joinLeft(array(
                                        'tc' => 'toner_color'
                                   ), 'tc.toner_color_id = t.tonerColorId', 'name AS toner_color')
                        ->joinLeft(array(
                                        'pt' => 'part_type'
                                   ), 'pt.part_type_id = t.partTypeId')
                        ->joinLeft(array(
                                        'uto' => 'user_toner_override'
                                   ), 'uto.toner_id = t.toner_id AND uto.user_id = ' . $this->user_id, array(
                                                                                                            'user_id',
                                                                                                            'override_toner_price'
                                                                                                       ))
                        ->group('t.toner_id')
                        ->order(array(
                                     'tm.manufacturer_name',
                                     'md.printer_model',
                                     't.toner_SKU'
                                ));
                    $stmt   = $db->query($select);
                    $result = $stmt->fetchAll();
                }

                foreach ($result as $key => $value)
                {
                    $price = 0;

                    // prep pricing
                    if (in_array("System Admin", $this->privilege))
                    {
                        $price = $value ['cost'];
                    }
                    else
                    {
                        $price = $value ['override_toner_price'];
                    }

                    $fieldList [] = array(
                        $value ['toners_id'],
                        $value ['fullname'],
                        $value ['part_type'],
                        $value ['sku'],
                        $value ['toner_color'],
                        $value ['yield'],
                        $price
                    );
                }
            }
        }
        catch (Exception $e)
        {
            throw new Exception("CSV File could not be opened/written for export.", 0, $e);
        }

        $this->view->fieldTitles = implode(",", $fieldTitles);
        $newFieldList            = "";
        foreach ($fieldList as $row)
        {
            $newFieldList .= implode(",", $row);
            $newFieldList .= "\n";
        }
        $this->view->fieldList = $newFieldList;
        Tangent_Functions::setHeadersForDownload($filename);
    }

    public function importpricingAction ()
    {
        $db                = Zend_Db_Table::getDefaultAdapter();
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
        // Get the total amount of mater devices that we are working with.
        $count    = Proposalgen_Model_Mapper_MasterDevice::getInstance()->count();
        $response = new stdClass();
        // Criteria is the vlaues that the client wants to search by
        $criteria = $this->_getParam('criteria', false);
        // Filter is what column the client wants to do a search by
        $filter = $this->_getParam('filter', false);
        // Order in which the columns are sorted
        $sortOrder = $this->_getParam('sord', 'asc');
        // Index in which the columns should be sorted
        $sortIndex = $this->_getParam('sidx', 'id');
        // Rows that are passed
        $limit = $this->_getParam('rows');
        // Page that the JQGrid is currently on
        $page = $this->_getParam('page');

        // Set the total pages that we have
        if ($count > 0)
        {
            $totalPages = ceil($count / $limit);
        }
        else
        {
            $totalPages = 0;
        }

        // Check to see if page number is greater than total pages, if so set pages to the highest page
        if ($page > $totalPages)
        {
            $page = $totalPages;
        }
        // Page, total, and records are needed for the JQgrid to operate
        $response->page    = $page;
        $response->total   = $totalPages;
        $response->records = $count;

        $start = $limit * $page - $limit;
        try
        {
            // Based on the filter allow the mappers to return the appropriate device
            if ($filter === 'manufacturerId')
            {
                $masterDevices = Proposalgen_Model_Mapper_MasterDevice::getInstance()->fetchAllByManufacturerId($criteria, "{$sortIndex} {$sortOrder}", $limit, $start);
            }
            else if ($filter === 'modelName')
            {
                $masterDevices = Proposalgen_Model_Mapper_MasterDevice::getInstance()->fetchAllLikePrinterModel($criteria, "{$sortIndex} {$sortOrder}", $limit, $start);
            }
            else
            {
                $masterDevices = Proposalgen_Model_Mapper_MasterDevice::getInstance()->fetchAll(null, "{$sortIndex} {$sortOrder}", $limit, $start);
            }

            if (count($masterDevices) > 0)
            {
                $i = 0;
                foreach ($masterDevices as $masterDevice)
                {
                    $response->rows [$i] ['id']   = $masterDevice->id;
                    $response->rows [$i] ['cell'] = array(
                        $masterDevice->getManufacturer()->fullname,
                        $masterDevice->modelName,
                        $masterDevice->cost
                    );
                    $i++;
                }
            }
        }
        catch (Exception $e)
        {
            Throw new Exception($e->getMessage);
        }
        $this->_helper->json($response);
    }

    public function userdevicesAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db       = Zend_Db_Table::getDefaultAdapter();
        $type     = $this->_getParam('type', 'printers');
        $user_id  = $this->user_id;
        $filter   = $this->_getParam('filter', false);
        $criteria = $this->_getParam('criteria', false);
        $formdata = new stdClass();
        $page     = $_GET ['page'];
        $limit    = $_GET ['rows'];
        $sidx     = $_GET ['sidx'];
        $sord     = $_GET ['sord'];
        if (!$sidx)
        {
            $sidx = 'm.fullname';
        }

        $where = '';
        if (!empty($filter) && !empty($criteria))
        {
            if ($filter == 'manufacturer_name')
            {
                $filter = "fullname";
            }
            $where = $filter . ' LIKE("%' . $criteria . '%")';
        }

        try
        {
            // select master devices
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array(
                            'md' => 'pgen_master_devices'
                       ), array(
                               'id',
                               'manufacturer_id',
                               'printer_model',
                               'cost'
                          ))
                ->joinLeft(array(
                                'm' => 'manufacturers'
                           ), 'm.id = md.manufacturerId', array(
                                                               'fullname'
                                                          ))
                ->joinLeft(array(
                                'udo' => 'pgen_user_device_overrides'
                           ), 'udo.master_device_id = md.id AND udo.user_id = ' . $user_id, array(
                                                                                                 'cost as override_cost'
                                                                                            ));
            if ($where != '')
            {
                $select->where($where);
            }
            $select->order(array(
                                'm.fullname',
                                'md.modelName'
                           ));
            $stmt   = $db->query($select);
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
            {
                $page = $total_pages;
            }
            $start = $limit * $page - $limit;
            if ($start < 0)
            {
                $start = 0;
            }

            // select master devices
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array(
                            'md' => 'pgen_master_devices'
                       ), array(
                               'id AS master_id',
                               'manufacturer_id',
                               'printer_model',
                               'cost'
                          ))
                ->joinLeft(array(
                                'm' => 'manufacturers'
                           ), 'm.id = md.manufacturer_id', array(
                                                                'fullname'
                                                           ))
                ->joinLeft(array(
                                'udo' => 'pgen_user_device_overrides'
                           ), 'udo.master_device_id = md.id AND udo.user_id = ' . $user_id, array(
                                                                                                 'cost as override_cost'
                                                                                            ));
            if ($where != '')
            {
                $select->where($where);
            }
            $select->order($sidx . ' ' . $sord);
            $select->limit($limit, $start);
            $stmt   = $db->query($select);
            $result = $stmt->fetchAll();

            $formdata->page    = $page;
            $formdata->total   = $total_pages;
            $formdata->records = $count;

            if (count($result) > 0)
            {
                $i = 0;

                $price_margin = 1; //($this->getPricingMargin('dealer', $this->dealer_company_id) / 100) + 1;
                foreach ($result as $row)
                {
                    $printer_cost = 0;

                    $price = number_format(($row ['cost'] * $price_margin), 2, '.', '');

                    $formdata->rows [$i] ['id']   = $row ['master_id'];
                    $formdata->rows [$i] ['cell'] = array(
                        ucwords(strtolower($row ['fullname'])),
                        ucwords(strtolower($row ['printer_model'])),
                        $price,
                        ($row ['override_cost'] > 0 ? (float)$row ['override_cost'] : null)
                    );
                    $i++;
                }
            }
            else
            {
                $formdata = array();
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Passing Exception Up The Chain", null, $e);
        }

        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function tonerslistAction ()
    {
        $db               = Zend_Db_Table::getDefaultAdapter();
        $criteria         = trim($this->_getParam('criteria', false));
        $master_device_id = $this->_getParam('deviceid', false);
        $filter           = $this->_getParam('filter', false);
        $page             = $this->_getParam('page');
        $limit            = $this->_getParam('rows');
        $sortIndex        = $this->_getParam('sidx', 1);
        $sortOrder        = $this->_getParam('sord');

        $where            = '';
        $where_compatible = '';

        // Check the filter type to build where clause
        if (!empty($filter) && !empty($criteria) && $filter != 'machine_compatibility')
        {
            if ($filter == "manufacturer_name")
            {
                $filter = "tm.fullname";
            }
            else if ($filter == "type_name")
            {
                $filter = "pt.name";
            }
            else if ($filter == "toner_sku" || $filter =="toner_SKU")
            {
                $filter = "t.sku";
            }
            else if ($filter == "toner_color_name")
            {
                $filter = "tc.name";
            }
            else if ($filter == "toner_yield")
            {
                $filter = "t.yield";
            }
            $where = ' AND ' . $filter . ' LIKE("%' . $criteria . '%")';
        }
        else if (!empty($filter) && $filter == 'machine_compatibility')
        {
            if (strtolower($criteria) == "hp")
            {
                $criteria = "hewlett-packard";
            }
            $where_compatible = $criteria;
        }

        if ($master_device_id > 0)
        {
            $toner_fields_list = array(
                'id AS toner_id',
                'sku AS toner_SKU',
                'yield AS toner_yield',
                'cost AS toner_price',
                '(SELECT master_device_id FROM pgen_device_toners AS sdt WHERE sdt.toner_id = t.id AND sdt.master_device_id = ' . $master_device_id . ') AS is_added',
                'GROUP_CONCAT(CONCAT(mdm.fullname," ",md.modelName) SEPARATOR "; ") AS device_list'
            );
        }
        else
        {
            $toner_fields_list = array(
                'id AS toner_id',
                'sku AS toner_SKU',
                'yield AS toner_yield',
                'cost AS toner_price',
                '(null) AS is_added',
                'GROUP_CONCAT(CONCAT(mdm.fullname," ",md.modelName) SEPARATOR "; ") AS device_list'
            );
        }
        $formData = null;

        try
        {
            // get count
            $select = $db->select()
                ->from(array(
                            't' => 'pgen_toners'
                       ), $toner_fields_list)
                ->joinLeft(array(
                                'dt' => 'pgen_device_toners'
                           ), 'dt.toner_id = t.id', array(
                                                         'master_device_id'
                                                    ))
                ->joinLeft(array(
                                'tm' => 'manufacturers'
                           ), 'tm.id = t.manufacturerId', array(
                                                               'tm.fullname AS toner_manufacturer'
                                                          ))
                ->joinLeft(array(
                                'md' => 'pgen_master_devices'
                           ), 'md.id = dt.master_device_id')
                ->joinLeft(array(
                                'mdm' => 'manufacturers'
                           ), 'mdm.id = md.manufacturerId', array(
                                                                 'mdm.fullname AS manufacturer_name'
                                                            ))
                ->joinLeft(array(
                                'tc' => 'pgen_toner_colors'
                           ), 'tc.id = t.tonerColorId', array(
                                                             'name AS toner_color_name'
                                                        ))
                ->joinLeft(array(
                                'pt' => 'pgen_part_types'
                           ), 'pt.id = t.partTypeId', array(
                                                           'pt.name AS type_name'
                                                      ))
                ->where('t.id > 0' . $where);

            if ($where_compatible)
            {
                $select->where("CONCAT(mdm.fullname,' ',md.modelName) LIKE '%" . $where_compatible . "%'");
            }

            $select->group('t.id');
            $stmt   = $db->query($select);
            $result = $stmt->fetchAll();
            $count  = count($result);
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            if ($page > $total_pages)
            {
                $page = $total_pages;
            }
            $start = $limit * $page - $limit;
            if ($start < 0)
            {
                $start = 0;
            }

            $select->order($sortIndex . ' ' . $sortOrder);
            $select->limit($limit, $start);
//            echo "<pre>Var dump initiated at " . __LINE__ . " of:\n" . __FILE__ . "\n\n";
//            var_dump((string)$select);
//            die();
            $stmt   = $db->query($select);
            $result = $stmt->fetchAll();

            $formData->page    = $page;
            $formData->total   = $total_pages;
            $formData->records = $count;
            if (count($result) > 0)
            {
                $i = 0;
                foreach ($result as $row)
                {
                    // Always uppercase OEM, but just captialize everything else
                    $type_name = ucwords(strtolower($row ['type_name']));
                    if ($type_name == "Oem")
                    {
                        $type_name = "OEM";
                    }

                    $formData->rows [$i] ['id']   = $row ['toner_id'];
                    $formData->rows [$i] ['cell'] = array(
                        $row ['toner_id'],
                        $row ['toner_SKU'],
                        ucwords(strtolower($row ['toner_manufacturer'])),
                        $type_name,
                        ucwords(strtolower($row ['toner_color_name'])),
                        $row ['toner_yield'],
                        $row ['toner_price'],
                        $row ['master_device_id'],
                        $row ['is_added'],
                        ucwords(strtolower($row ['device_list']))
                    );
                    $i++;
                }
            }
            else
            {
                $formData = array();
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Passing Exception Up The Chain", null, $e);
        }

        $this->_helper->json($formData);
    }

    public function usertonersAction ()
    {
        $db       = Zend_Db_Table::getDefaultAdapter();
        $user_id  = $this->user_id;
        $filter   = $this->_getParam('filter', false);
        $criteria = $this->_getParam('criteria', false);
        $formdata = new stdClass();

        $page  = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx  = $_GET ['sidx'];
        $sord  = $_GET ['sord'];
        if (!$sidx)
        {
            $sidx = 1;
        }

        $where            = '';
        $where_compatible = '';
        if (!empty($filter) && !empty($criteria) && $filter != 'machine_compatibility')
        {

            if ($filter == "manufacturer_name")
            {
                $filter = "tm.fullname";
            }
            else
            {
                if ($filter == "toner_sku")
                {
                    $filter = "t.sku";
                }
            }


            $where = $filter . ' LIKE("%' . $criteria . '%")';
        }
        else if (!empty($filter) && $filter == 'machine_compatibility')
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
                ->from(array(
                            't' => 'pgen_toners'
                       ), array(
                               't.id AS toners_id',
                               't.sku',
                               't.yield',
                               't.cost',
                               'GROUP_CONCAT(CONCAT(mdm.fullname," ",md.printer_model) SEPARATOR "; ") AS machine_compatibility'
                          ))
                ->joinLeft(array(
                                'tm' => 'manufacturers'
                           ), 'tm.id = t.manufacturerId', array(
                                                               'tm.fullname AS toner_manufacturer'
                                                          ))
                ->joinLeft(array(
                                'dt' => 'pgen_device_toners'
                           ), 'dt.toner_id = t.id')
                ->joinLeft(array(
                                'md' => 'pgen_master_devices'
                           ), 'md.id = dt.master_device_id')
                ->joinLeft(array(
                                'mdm' => 'manufacturers'
                           ), 'mdm.id = md.manufacturerId', array(
                                                                  'mdm.fullname'
                                                             ))
                ->joinLeft(array(
                                'tc' => 'pgen_toner_colors'
                           ), 'tc.id = t.tonerColorId')
                ->joinLeft(array(
                                'pt' => 'pgen_part_types'
                           ), 'pt.id = t.partTypeId')
                ->joinLeft(array(
                                'uto' => 'pgen_user_toner_overrides'
                           ), 'uto.toner_id = t.id AND uto.user_id = ' . $user_id, array(
                                                                                        'user_id',
                                                                                        'cost AS override_cost'
                                                                                   ));
            if ($where != '')
            {
                $select->where($where);
            }
            if (!empty($where_compatible))
            {
                $select->where("CONCAT(mdm.fullname,' ',md.printer_model) LIKE '%" . $where_compatible . "%'");
            }
            $select->group('t.id');
            $select->order(array(
                                'tm.fullname',
                                'md.printer_model',
                                't.sku'
                           ));
            $stmt   = $db->query($select);
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
            {
                $page = $total_pages;
            }
            $start = $limit * $page - $limit;
            if ($start < 0)
            {
                $start = 0;
            }

            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array(
                            't' => 'pgen_toners'
                       ), array(
                               't.id AS toners_id',
                               't.sku',
                               't.yield',
                               't.cost AS toner_cost',
                               'GROUP_CONCAT(CONCAT(mdm.fullname," ",md.printer_model) SEPARATOR "; ") AS machine_compatibility'
                          ))
                ->joinLeft(array(
                                'tm' => 'manufacturers'
                           ), 'tm.id = t.manufacturerId', array(
                                                               'tm.fullname AS toner_manufacturer'
                                                          ))
                ->joinLeft(array(
                                'dt' => 'pgen_device_toners'
                           ), 'dt.toner_id = t.id')
                ->joinLeft(array(
                                'md' => 'pgen_master_devices'
                           ), 'md.id = dt.master_device_id', array(
                                                                  'md.id AS master_id'
                                                             ))
                ->joinLeft(array(
                                'mdm' => 'manufacturers'
                           ), 'mdm.id = md.manufacturerId', array(
                                                                  'mdm.fullname'
                                                             ))
                ->joinLeft(array(
                                'tc' => 'pgen_toner_colors'
                           ), 'tc.id = t.tonerColorId', array(
                                                             'tc.name AS color_name'
                                                        ))
                ->joinLeft(array(
                                'pt' => 'pgen_part_types'
                           ), 'pt.id = t.partTypeId', array(
                                                           'name as type_name'
                                                      ))
                ->joinLeft(array(
                                'uto' => 'pgen_user_toner_overrides'
                           ), 'uto.toner_id = t.id AND uto.user_id = ' . $user_id, array(
                                                                                        'user_id',
                                                                                        'cost AS override_cost'
                                                                                   ));
            if ($where != '')
            {
                $select->where($where);
            }
            if (!empty($where_compatible))
            {
                $select->where("CONCAT(mdm.fullname,' ',md.printer_model) LIKE '%" . $where_compatible . "%'");
            }
            $select->group('t.id');
            $select->order($sidx . ' ' . $sord);
            $select->limit($limit, $start);
            $stmt   = $db->query($select);
            $result = $stmt->fetchAll();

            $formdata->page    = $page;
            $formdata->total   = $total_pages;
            $formdata->records = $count;
            if (count($result) > 0)
            {
                $i = 0;
                // FIXME: Hardcoded price margin
                $price_margin = 1; //($this->getPricingMargin('dealer', $this->dealer_company_id) / 100) + 1;
                foreach ($result as $row)
                {
                    $type_name = ucwords(strtolower($row ['type_name']));
                    if ($type_name == "Oem")
                    {
                        $type_name = "OEM";
                    }
                    $formdata->rows [$i] ['id']   = $row ['toners_id'];
                    $formdata->rows [$i] ['cell'] = array(
                        $row ['toners_id'],
                        $row ['sku'],
                        ucwords(strtolower($row ['toner_manufacturer'])),
                        $type_name,
                        ucwords(strtolower($row ['color_name'])),
                        $row ['yield'],
                        $row ['toner_cost'] * $price_margin,
                        ($row ['override_cost'] > 0 ? (float)$row ['override_cost'] : null),
                        null,
                        $row ['master_id'],
                        ucwords(strtolower($row ['machine_compatibility']))
                    );
                    $i++;
                }
            }
            else
            {
                $formdata = array();
            }
        }
        catch (Exception $e)
        {
            // critical exception
            echo $e->getMessage();


        }

        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    protected function getmodelsAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $terms      = explode(" ", trim($_REQUEST ["searchText"]));
        $searchTerm = "%";
        foreach ($terms as $term)
        {
            $searchTerm .= "$term%";
        }
        // Fetch Devices like term
        $db = Zend_Db_Table::getDefaultAdapter();

        $sql = "SELECT concat(displayname, ' ', modelName) as device_name, pgen_master_devices.id, displayname, modelName FROM manufacturers
        JOIN pgen_master_devices on pgen_master_devices.manufacturerId = manufacturers.id
        WHERE concat(displayname, ' ', modelName) LIKE '%$searchTerm%' AND manufacturers.isDeleted = 0 ORDER BY device_name ASC LIMIT 10;";

        $results = $db->fetchAll($sql);
        // $results is an array of device names
        $devices = array();
        foreach ($results as $row)
        {
            $deviceName = $row ["displayname"] . " " . $row ["modelName"];
            $deviceName = ucwords(strtolower($deviceName));
            $devices [] = array(
                "label"        => $deviceName,
                "value"        => $row ["id"],
                "manufacturer" => ucwords(strtolower($row ["displayname"]))
            );
        }
        $lawl = Zend_Json::encode($devices);
        print $lawl;
    }

    public function managematchupsAction ()
    {
        $db                 = Zend_Db_Table::getDefaultAdapter();
        $this->view->title  = 'Manage Printer Matchups';
        $this->view->source = "PrintFleet";
//        $this->view->pf_model_id = '';

        // fill manufacturers dropdown
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('isDeleted = false', 'fullname');
        $this->view->manufacturer_list = $manufacturers;

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            // print_r($formData); die;


            if (isset($formData ['ticket_id']))
            {
                $this->view->form_mode     = $formData ['form_mode'];
                $this->view->ticket_id     = $formData ['ticket_id'];
                $this->view->devices_pf_id = $formData ['devices_pf_id'];
            }

            $db->beginTransaction();
            try
            {
                if (isset($formData ['hdnIdArray']))
                {
                    $master_matchup_pfTable      = new Proposalgen_Model_DbTable_PFMasterMatchup();
                    $id_array                    = (explode(",", $formData ['hdnIdArray']));
                    $this->view->criteria_filter = $formData ['criteria_filter'];

                    foreach ($id_array as $key)
                    {
                        $devices_pf_id    = $formData ['hdnDevicesPFID' . $key];
                        $master_device_id = $formData ['hdnMasterDevicesValue' . $key];

                        if ($devices_pf_id > 0 && $master_device_id > 0)
                        {
                            $master_matchup_pfData ['master_device_id'] = $master_device_id;

                            // check to see if matchup exists for devices_pf_id
                            $where   = $master_matchup_pfTable->getAdapter()->quoteInto('pf_device_id = ?', $devices_pf_id, 'INTEGER');
                            $matchup = $master_matchup_pfTable->fetchRow($where);

                            if (count($matchup) > 0)
                            {
                                $master_matchup_pfTable->update($master_matchup_pfData, $where);
                            }
                            else
                            {
                                $master_matchup_pfData ['pf_device_id'] = $devices_pf_id;
                                $master_matchup_pfTable->insert($master_matchup_pfData);
                            }
                        }
                        else if ($devices_pf_id > 0)
                        {
                            // no matchup set so remove any records for device
                            $where = $master_matchup_pfTable->getAdapter()->quoteInto('master_device_id = ?', $devices_pf_id, 'INTEGER');
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
                    $device_pfTable = new Proposalgen_Model_DbTable_PFDevice();
                    $device_pf      = $device_pfTable->fetchRow('id = ' . $formData ['devices_pf_id']);

                    if (count($device_pf) > 0)
                    {
                        $this->view->pf_model_id = $device_pf ['pf_model_id'];
                    }
                }
            }
            catch (Exception $e)
            {
                throw new Exception("Passing Exception Up The Chain", null, $e);
                $db->rollback();
                $this->view->message = "Error";
                $this->_helper->flashMessenger(array(
                                                    "error" => "An error has occurred."
                                               ));
            }
        }
    }

    /**
     * This action handles mapping rms models to master devices.
     * If masterDeviceId is set to -1 it will delete the matchup
     */
    public function setmappedtoAction ()
    {
        $errorMessage = false;

        $rmsProviderId  = $this->_getParam('rmsProviderId', false);
        $rmsModelId     = $this->_getParam('rmsModelId', false);
        $masterDeviceId = $this->_getParam('masterDeviceId', false);

        if ($rmsProviderId !== false && $rmsModelId !== false && $masterDeviceId !== false)
        {
            $masterDeviceId = (int)$masterDeviceId;
            $masterDevice   = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($masterDeviceId);
            if ($masterDeviceId === 0 || $masterDevice)
            {
                $rmsDevice = Proposalgen_Model_Mapper_Rms_Device::getInstance()->find(array($rmsProviderId, $rmsModelId));

                if ($rmsDevice)
                {
                    // If all is good, lets perform our update
                    $rmsMasterMatchup = Proposalgen_Model_Mapper_Rms_Master_Matchup::getInstance()->find(array($rmsProviderId, $rmsModelId));
                    if ($rmsMasterMatchup)
                    {
                        if ($masterDeviceId > 0)
                        {
                            // Update
                            if ($rmsMasterMatchup->mas != $masterDeviceId)
                            {
                                $rmsMasterMatchup->masterDeviceId = $masterDeviceId;
                                $updateResult                     = Proposalgen_Model_Mapper_Rms_Master_Matchup::getInstance()->save($rmsMasterMatchup);
                                if ($updateResult === 0)
                                {
                                    $errorMessage = 'Error while updating the matchup';
                                }
                            }
                        }
                        else
                        {
                            // Delete
                            Proposalgen_Model_Mapper_Rms_Master_Matchup::getInstance()->delete($rmsMasterMatchup);
                        }
                    }
                    else
                    {
                        if ($masterDeviceId > 0)
                        {
                            // Insert
                            $rmsMasterMatchup                 = new Proposalgen_Model_Rms_Master_Matchup();
                            $rmsMasterMatchup->rmsProviderId  = $rmsProviderId;
                            $rmsMasterMatchup->rmsModelId     = $rmsModelId;
                            $rmsMasterMatchup->masterDeviceId = $masterDeviceId;
                            $insertResult                     = Proposalgen_Model_Mapper_Rms_Master_Matchup::getInstance()->insert($rmsMasterMatchup);
                            if (!$insertResult)
                            {
                                $errorMessage = 'Error while adding the new matchup';
                            }
                        }
                    }
                }
                else
                {
                    $errorMessage = 'Invalid Rms Device.';
                }
            }
            else
            {
                $errorMessage = 'Invalid Master Device';
            }
        }
        else
        {
            $errorMessage = 'Missing Parameter. Please make sure all parameters are provided.';
        }

        // When we have an error code, set the status to 500 and return the error
        if ($errorMessage)
        {
            $this->_response->setHttpResponseCode(500);
            $this->_helper->json(array(
                                      'error' => $errorMessage
                                 ));
        }
        else
        {
            $this->_helper->json(array(
                                      'success' => true
                                 ));
        }
    }


    /**
     * Gets the list of matchups.
     */
    public function matchuplistAction ()
    {
        $rmsDeviceMapper = Proposalgen_Model_Mapper_Rms_Device::getInstance();
        $jqGrid          = new Tangent_Service_JQGrid();

        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'rmsProviderName'),
            'sord' => $this->_getParam('sord', 'ASC'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10)
        );

        $jqGrid->parseJQGridPagingRequest($jqGridParameters);

        // Set up validation arrays
        $sortColumns = array(
            'rmsProviderId',
            'rmsProviderName',
            'rmsModelId',
            'rmsProviderDeviceName'
        );
        $jqGrid->setValidSortColumns($sortColumns);

        if ($jqGrid->sortingIsValid())
        {
            /*
             * Can filter by: model name, model id
             */
            // Get filtering Parameters


            $searchCriteria = $this->_getParam('filter', null);
            $searchValue    = $this->_getParam('criteria', null);

            // Validate filtering parameters
            $filterCriteriaValidator = new Zend_Validate_InArray(array(
                                                                      'haystack' => array(
                                                                          'printer',
                                                                          'model',
                                                                          'onlyUnmapped'
                                                                      )
                                                                 ));

            // If search criteria or value is null then we don't need either one of them. Same goes if our criteria is invalid.
            if ($searchCriteria === null || $searchValue === null || !$filterCriteriaValidator->isValid($searchCriteria))
            {
                $searchCriteria = null;
                $searchValue    = null;
            }

            // Count the total rows
            $jqGrid->setRecordCount($rmsDeviceMapper->getMatchupDevices($jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $searchCriteria, $searchValue, null, null, true));

            // Validate current page number since we don't want to be out of bounds
            if ($jqGrid->getCurrentPage() < 1)
            {
                $jqGrid->setCurrentPage(1);
            }
            else if ($jqGrid->getCurrentPage() > $jqGrid->calculateTotalPages())
            {
                $jqGrid->setCurrentPage($jqGrid->calculateTotalPages());
            }

            // Return a small subset of the results based on the jqGrid parameters
            $startRecord = $jqGrid->getRecordsPerPage() * ($jqGrid->getCurrentPage() - 1);
            $jqGrid->setRows($rmsDeviceMapper->getMatchupDevices($jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $searchCriteria, $searchValue, $jqGrid->getRecordsPerPage(), $startRecord));

            // Send back jqGrid json data
            $this->_helper->json($jqGrid->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->_helper->json(array(
                                      'error' => 'Sorting parameters are invalid'
                                 ));
        }
    }

    public function managereplacementsAction ()
    {
        $db                = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = 'Manage Replacement Printers';
        $this->view->repop = false;

        $form                         = new Proposalgen_Form_ReplacementPrinter(null, '');
        $this->view->replacement_form = $form;

        // fill manufacturer dropdown
        $list               = "";
        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers      = $manufacturersTable->fetchAll('isDeleted = 0', 'fullname');
        $currElement        = $form->getElement('manufacturer_id');
        $currElement->addMultiOption('0', 'Select Manufacturer');
        foreach ($manufacturers as $row)
        {
            $currElement->addMultiOption($row ['id'], ucwords(strtolower($row ['fullname'])));
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
                $formData         = $this->_request->getPost();
                $form_mode        = $formData ['form_mode'];
                $hdnIds           = $formData ['hdnIds'];

                if ($form_mode == "delete")
                {
                    $ids = explode(",", $hdnIds);

                    foreach ($ids as $key)
                    {
                        if (isset($formData ['jqg_grid_list_' . $key]) && $formData ['jqg_grid_list_' . $key] == "on")
                        {
                            $replacement_category = $formData ['replacement_category_' . $key];
                            $where                = $replacementTable->getAdapter()->quoteInto('replacement_category = ?', $replacement_category);
                            $replacement          = $replacementTable->fetchAll($where);

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
            catch (Exception $e)
            {
                $db->rollback();
                Throw new exception("An error has occurred deleting replacement printers.", 0, $e);
            }
        }
    }

    public function savereplacementprinterAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $form     = new Proposalgen_Form_ReplacementPrinter(null, '');
        $formData = $this->_getAllParams();

        $hdnManId             = $formData ['hdnManId'];
        $hdnMasId             = $formData ['hdnMasId'];
        $manufacturer_id      = $formData ['manufacturer_id'];
        $printer_model        = $formData ['printer_model'];
        $hdnOriginalCategory  = $formData ['hdnOriginalCategory'];
        $replacement_category = $formData ['replacement_category'];
        $print_speed          = $formData ['print_speed'];
        $resolution           = $formData ['resolution'];
        $monthly_rate         = $formData ['monthly_rate'];
        $form_mode            = $formData ['form_mode'];


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
                $replacementTable         = new Proposalgen_Model_DbTable_ReplacementDevices();
                $replacement_devicesTable = new Proposalgen_Model_DbTable_ReplacementDevices();
                $replacementTableMapper   = Proposalgen_Model_Mapper_ReplacementDevice::getInstance();

                $replacement_devicesData = array(
                    'replacement_category' => strtoupper($replacement_category),
                    'print_speed'          => $print_speed,
                    'resolution'           => $resolution,
                    'monthly_rate'         => $monthly_rate
                );

                if ($form_mode == "add")
                {
                    // check to see if replacement device exists
                    $where               = $replacement_devicesTable->getAdapter()->quoteInto('master_device_id = ?', $printer_model, 'INTEGER');
                    $replacement_devices = $replacementTableMapper->fetchRow($where);
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
                    if (strtoupper($hdnOriginalCategory) !== strtoupper($replacement_category))
                    {
                        $where       = $replacementTable->getAdapter()->quoteInto('replacement_category = ?', $hdnOriginalCategory);
                        $replacement = $replacementTableMapper->fetchAll($where);
                        if (count($replacement) > 1)
                        {
                            $is_valid = true;
                        }
                        else
                        {
                            $is_valid = false;
                            $message  = "<p>You are not able to update the Replacement Category on <br/> this printer as it's the last printer of the " . ucwords(strtolower($hdnOriginalCategory)) . " category.</p>";
                        }
                    }

                    if ($is_valid === true)
                    {
                        $where = $replacement_devicesTable->getAdapter()->quoteInto('master_device_id = ?', $printer_model, 'INTEGER');
                        $replacement_devicesTable->update($replacement_devicesData, $where);
                        $this->view->message = "<p>The replacement printer has been updated.</p>";
                    }
                }

                if ($message === "")
                {
                    $db->commit();
                }
                else
                {
                    $db->rollback();
                    $this->view->message = $message;
                }
            }
            catch (Exception $e)
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
        $this->_helper->json($this->view->message);
    }

    public function replacementprinterslistAction ()
    {
        $formData = new stdClass();
        try
        {
            // get pf device list filter by manufacturer
            $replacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->fetchAll();

            // return results
            if (count($replacementDevices) > 0)
            {
                $i = 0;
                foreach ($replacementDevices as $replacementDevice)
                {
                    $formData->rows [$i] ['id']   = $replacementDevice->masterDeviceId;
                    $formData->rows [$i] ['cell'] = array(
                        $replacementDevice->getMasterDevice()->manufacturerId,
                        $replacementDevice->masterDeviceId,
                        $replacementDevice->getMasterDevice()->getFullDeviceName(),
                        $replacementDevice->replacementCategory,
                        null
                    );
                    $i++;
                }
            }
            else
            {
                $formData = array();
            }
        }
        catch (Exception $e)
        {
            Throw new exception("Error: Unable to find replacement device.", 0, $e);
        }

        $this->_helper->json($formData);
    }

    public function replacementdetailsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();

        $db        = Zend_Db_Table::getDefaultAdapter();
        $device_id = $this->_getParam('deviceid', false);

        try
        {
            if ($device_id > 0)
            {
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array(
                                'rd' => 'pgen_replacement_devices'
                           ))
                    ->where('master_device_id = ?', $device_id, 'INTEGER');
                $stmt   = $db->query($select);
                $row    = $stmt->fetchAll();

                $formdata = array(
                    'replacement_category' => $row [0] ['replacement_category'],
                    'print_speed'          => $row [0] ['print_speed'],
                    'resolution'           => $row [0] ['resolution'],
                    'monthly_rate'         => $row [0] ['monthly_rate']
                );
            }
            else
            {
                // empty form values
                $formdata = array();
            }
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Error: Unable to find replacement device.", 0, $e);
        } // end catch


        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function convertDate ($date)
    {
        if ($date)
        {
            return (strftime("%x", strtotime($date)));
        }
        else
        {
            return " ";
        }
    }

    public function transferreportsAction ()
    {
        $db                = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = 'Transfer Reports';
        $where             = null;
        $order             = null;
        $count             = null;
        $offset            = null;
        $id_list           = null;

        $User = Proposalgen_Model_Mapper_User::getInstance();

        //*************************************************
        // postback
        //*************************************************


        if ($this->_request->isPost())
        {
            $reportTable = new Proposalgen_Model_DbTable_Reports();
            $formData    = $this->_request->getPost();
            // print_r($formData); die;


            $db->beginTransaction();
            try
            {
                $reportMapper = Proposalgen_Model_Mapper_Report::getInstance();
                $report_id    = $formData ['reportlist'];

                // check transfer type
                if ($formData ['transfertype'] == 'transfer')
                {
                    $new_user_id = $formData ['newuser'];

                    // update report


                    $reportMapper   = Proposalgen_Model_Mapper_Report::getInstance();
                    $report         = Proposalgen_Model_Mapper_Report::getInstance()->find($report_id);
                    $report->id     = $report_id;
                    $report->userId = $new_user_id;
                    $reportMapper->save($report);

                    // update unknown_device_instance records
                    $udiTable         = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                    $data ['user_id'] = $new_user_id;
                    $where            = $udiTable->getAdapter()->quoteInto('report_id = ?', $report_id, 'INTEGER');

                    // Perform the update.
                    $udiTable->update($data, $where);

                    $this->_helper->flashMessenger(array(
                                                        "success" => "Report Transfer Complete."
                                                   ));
                }
                else if ($formData ['transfertype'] == 'clone')
                {
                    $reportMapper->cloneReport($report_id, $formData ['hdntransferlist']);

                    $this->_helper->flashMessenger(array(
                                                        "success" => "Report Cloning Complete."
                                                   ));
                }
                $db->commit();
            }
            catch (Exception $e)
            {
                $db->rollback();
                $this->_helper->flashMessenger(array(
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
            ->from(array(
                        'u' => 'users'
                   ))
            ->joinLeft(array(
                            'up' => 'user_privileges'
                       ), 'u.user_id = up.user_id');
        if ($where)
        {
            $select->where($where);
        }
        $select->order('username ASC');
        $stmt                   = $db->query($select);
        $users                  = $stmt->fetchAll();
        $this->view->users_list = $users;

        //*************************************************
        // get to users
        //*************************************************


        // if system admin (all users) else if dealer admin or standard user (company users only)
        $where = null;
        if (!in_array("System Admin", $this->privilege))
        {
            $where = 'dealer_company_id=' . $this->dealer_company_id;
        }

        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array(
                        'u' => 'users'
                   ))
            ->joinLeft(array(
                            'up' => 'user_privileges'
                       ), 'u.user_id = up.user_id');
        if ($where)
        {
            $select->where($where);
        }
        $select->order('username ASC');
        $stmt                      = $db->query($select);
        $users                     = $stmt->fetchAll();
        $this->view->to_users_list = $users;

        //*************************************************
        // get companies
        //*************************************************


        // if system admin (show all reports) else if dealer admin (above users reports only) else if standard user (show only users reports)
        $where = 'dealer_company_id > 1 ';
        if (!in_array("System Admin", $this->privilege))
        {
            $where .= 'AND dealer_company_id = ' . $this->dealer_company_id;
        }
        $order = 'company_name ASC';

        $companiesTable           = new Proposalgen_Model_DbTable_DealerCompany();
        $companies                = $companiesTable->fetchAll($where, $order, $count, $offset);
        $this->view->company_list = $companies;

        //*************************************************
        // get reports
        //*************************************************


        // if system admin (show all reports) else if dealer admin (above users reports only) else if standard user (show only users reports)
        $where = null;
        if (!in_array("Standard User", $this->privilege))
        {
            //build id string
            foreach ($this->view->users_list as $key)
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

        $reportsTable             = new Proposalgen_Model_DbTable_Reports();
        $reports                  = $reportsTable->fetchAll($where, $order, $count, $offset);
        $this->view->reports_list = $reports;
    }

    public function filterreportslistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db          = Zend_Db_Table::getDefaultAdapter();
        $filterfield = $this->_getParam('filterfield', null);
        $filtervalue = $this->_getParam('filtervalue', null);
        $startdate   = $this->_getParam('startdate', null);
        $enddate     = $this->_getParam('enddate', null);
        $formdata    = new stdClass();

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
                ->from(array(
                            'r' => 'reports'
                       ))
                ->joinLeft(array(
                                'u' => 'users'
                           ), 'u.user_id = r.user_id', array(
                                                            'username'
                                                       ));
            if ($where)
            {
                $select->where($where);
            }
            $select->order(array(
                                'date_created DESC',
                                'customer_company_name ASC'
                           ));
            //echo $select; die;
            $stmt   = $db->query($select);
            $result = $stmt->fetchAll();

            if (count($result) > 0)
            {
                $i = 0;
                foreach ($result as $row)
                {
                    $formdata->rows [$i] ['id']   = $row ['report_id'];
                    $formdata->rows [$i] ['cell'] = array(
                        $row ['report_id'],
                        $row ['customer_company_name'] . ' (' . $row ['username'] . ' on ' . date("m-d-Y", strtotime($row ['date_created'])) . ')'
                    );
                    $i++;
                }
            }
            else
            {
                $formdata = array();
            }
        }
        catch (Exception $e)
        {
            // critical exception
            //echo $e->getMessage();
            $formdata = array();
        }
        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function filteruserslistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db       = Zend_Db_Table::getDefaultAdapter();
        $filter   = $this->_getParam('filter', 'all');
        $formdata = new stdClass();

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
                ->from(array(
                            'u' => 'users'
                       ))
                ->joinLeft(array(
                                'up' => 'user_privileges'
                           ), 'u.user_id = up.user_id');
            if ($where)
            {
                $select->where($where);
            }
            $select->order('username ASC');
            $stmt   = $db->query($select);
            $result = $stmt->fetchAll();

            if (count($result) > 0)
            {
                $i = 0;
                foreach ($result as $row)
                {
                    $formdata->rows [$i] ['id']   = $row ['user_id'];
                    $formdata->rows [$i] ['cell'] = array(
                        $row ['user_id'],
                        strtolower($row ['username'])
                    );
                    $i++;
                }
            }
            else
            {
                $formdata = array();
            }
        }
        catch (Exception $e)
        {
            // critical exception
            //echo $e->getMessage();
            $formdata = array();
        }
        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function filtercompanieslistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db       = Zend_Db_Table::getDefaultAdapter();
        $formdata = new stdClass();

        try
        {
            $where = 'dealer_company_id > 1 ';
            if (!in_array("System Admin", $this->privilege))
            {
                $where .= 'AND dealer_company_id = ' . $this->dealer_company_id;
            }

            // select users
            $select = new Zend_Db_Select($db);
            $select = $db->select()->from(array(
                                               'dc' => 'dealer_company'
                                          ));
            if ($where)
            {
                $select->where($where);
            }
            $select->order('company_name ASC');
            $stmt   = $db->query($select);
            $result = $stmt->fetchAll();

            if (count($result) > 0)
            {
                $i = 0;
                foreach ($result as $row)
                {
                    $formdata->rows [$i] ['id']   = $row ['dealer_company_id'];
                    $formdata->rows [$i] ['cell'] = array(
                        $row ['dealer_company_id'],
                        $row ['company_name']
                    );
                    $i++;
                }
            }
            else
            {
                $formdata = array();
            }
        }
        catch (Exception $e)
        {
            // critical exception
            //echo $e->getMessage();
            $formdata = array();
        }
        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }
} //end class AdminController
