<?php

/**
 * Manage Devices Controller: This controller handles management of devices and toners.
 * You access this controller from the mapping page, tickets and admin menu.
 *
 * @author John Sadler
 */
class Proposalgen_ManagedevicesController extends Zend_Controller_Action
{

    function init ()
    {
        $this->view->title = 'Manage Printers and Toners';
        $this->config      = Zend_Registry::get('config');
        $this->initView();
        $this->view->app     = $this->config->app;
        $this->view->user    = Zend_Auth::getInstance()->getIdentity();
        $this->view->user_id = Zend_Auth::getInstance()->getIdentity()->id;
        //$this->view->privilege      = Zend_Auth::getInstance()->getIdentity()->privileges;
        $this->user_id = Zend_Auth::getInstance()->getIdentity()->id;
        //$this->privilege            = Zend_Auth::getInstance()->getIdentity()->privileges;
        //$this->dealer_company_id    = Zend_Auth::getInstance()->getIdentity()->dealer_company_id;
        $this->MPSProgramName       = $this->config->app->MPSProgramName;
        $this->view->MPSProgramName = $this->config->app->MPSProgramName;
        $this->ApplicationName      = $this->config->app->ApplicationName;
    }

    public function indexAction ()
    {
    }

    public function managedevicesAction ()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/libs/jqgrid/plugins/grid.celledit.js'), 'text/javascript');
        $db = Zend_Db_Table::getDefaultAdapter();

        // add device form
        $form = new Proposalgen_Form_Device(null, "edit");
        $form->removeElement('serial_number');
        $form->removeElement('override_price');
        $form->removeElement('save_device');
        $form->removeElement('delete_device');
        $form->removeElement('back_button');

        // fill manufacturer dropdown
        $list = "";
        // $isPrintModelSet is used to see if data has been saved successfully.
        $isPrintModelSet               = false;
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('isDeleted = 0', 'fullName');
        $currElement                   = $form->getElement('manufacturer_id');
        $this->view->manufacturer_list = $manufacturers;

        // add link to the manage manufacturer page
        $currElement->setDescription('<a id="edit_man_link" href="javascript: do_action(\'manufacturer\');">Add New Manufacturer</a>');
        $currElement->addMultiOption('0', 'Select Manufacturer');
        foreach ($manufacturers as $row)
        {
            $currElement->addMultiOption($row ['id'], ucwords(strtolower($row ['fullname'])));
            if (empty($list) == false)
            {
                $list .= ";";
            }
            $list .= $row ['id'] . ":" . ucwords(strtolower($row ['fullname']));
        }
        $this->view->manufacturers = $list;

        // fill toner_config dropdown
        $toner_configTable = new Proposalgen_Model_DbTable_TonerConfig();
        $toner_configs     = $toner_configTable->fetchAll(null, 'name');
        $currElement       = $form->getElement('toner_config_id');
        $currElement->addMultiOption('', 'Select Toner Config');
        foreach ($toner_configs as $row)
        {
            $currElement->addMultiOption($row ['id'], ucwords(strtolower($row ['name'])));
        }

        // return part_type list
        $list           = "";
        $part_typeTable = new Proposalgen_Model_DbTable_PartType();
        $part_types     = $part_typeTable->fetchAll();
        foreach ($part_types as $row)
        {
            $part_type = ucwords(strtolower($row ['name']));
            if ($part_type == "Oem")
            {
                $part_type = "OEM";
            }

            if (empty($list) == false)
            {
                $list .= ";";
            }
            $list .= $row ['id'] . ":" . $part_type;
        }
        $this->view->partTypeList = $list;

        // return color list
        $list             = "";
        $toner_colorTable = new Proposalgen_Model_DbTable_TonerColor();
        $toner_colors     = $toner_colorTable->fetchAll();
        foreach ($toner_colors as $row)
        {
            if (empty($list) == false)
            {
                $list .= ";";
            }
            $list .= $row ['id'] . ":" . ucwords(strtolower($row ['name']));
        }
        $this->view->tonerColorList = $list;

        $this->view->blackOnlyList     = "1:Black";
        $this->view->seperateColorList = "1:Black;2:Cyan;3:Magenta;4:Yellow";
        $this->view->threeColorList    = "5:3 Color";
        $this->view->fourColorList     = "6:4 Color";

        // check if this page has been posted to
        if ($this->_request->isPost())
        {
            $repop_form = 0;
            $formData   = $this->_request->getPost();
            // conditional requirements
            $form->set_validation($formData);
            // get form mode
            $form_mode = $formData ['form_mode'];
            $date      = date('Y-m-d H:i:s');
            // validate fields
            if ($formData ["manufacturer_id"] == 0)
            {
                $this->_helper->flashMessenger(array (
                                                     'error' => 'You must select a manufacturer.'
                                               ));
            }
            else if ($form_mode == "edit" && $formData ["printer_model"] == 0)
            {
                $this->_helper->flashMessenger(array (
                                                     'error' => 'You must select a printer model.'
                                               ));
            }
            else if ($form_mode == "add" && trim($formData ["new_printer"]) == "")
            {
                $this->_helper->flashMessenger(array (
                                                     'error' => 'You must enter a printer model name.'
                                               ));
            }
            else if ($formData ["toner_config_id"] == 0)
            {
                $this->_helper->flashMessenger(array (
                                                     'error' => 'Toner Config not selected. Please try again.'
                                               ));
            }
            else if ($formData ["watts_power_normal"] < 1)
            {
                $this->_helper->flashMessenger(array (
                                                     'error' => 'Power Consumption Normal must be greater then zero.'
                                               ));
            }
            else if ($formData ["watts_power_idle"] < 1)
            {
                $this->_helper->flashMessenger(array (
                                                     'error' => 'Power Consumption Idle must be greater then zero.'
                                               ));

            }
            else
            {
                if ($formData ['save_flag'] == "save")
                {
                    // update the selected device
                    $db->beginTransaction();
                    try
                    {
                        $master_device_id = 0;
                        if ($form_mode == "edit")
                        {
                            $master_device_id = $formData ['printer_model'];
                        }
                        $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();

                        // validate toners against toner_config
                        $has_toner    = false;
                        $has_black    = false;
                        $has_yellow   = false;
                        $has_magenta  = false;
                        $has_cyan     = false;
                        $has_3color   = false;
                        $has_4color   = false;
                        $toners_valid = false;

                        $toner_config_id = $formData ['toner_config_id'];
                        $toner_array     = explode(",", $formData ["toner_array"]);
                        foreach ($toner_array as $key)
                        {
                            $toner_id = str_replace("'", "", $key);
                            if ($toner_id > 0)
                            {
                                // get color and type from $key
                                $select   = $db->select()
                                    ->from(array(
                                                't' => 'pgen_toners'
                                           ))
                                    ->join(array(
                                                'tc' => 'pgen_toner_colors'
                                           ), 'tc.id = t.tonerColorId', array(
                                                                             'name AS toner_color_name'
                                                                        ))
                                    ->join(array(
                                                'pt' => 'pgen_part_types'
                                           ), 'pt.id = t.partTypeId', array(
                                                                           'name AS type_name'
                                                                      ))->where('t.id = ?', $toner_id);
                                $stmt     = $db->query($select);
                                $curToner = $stmt->fetchAll();

                                if (count($curToner) > 0)
                                {

                                    $has_toner = true;
                                    $curColor  = strtolower($curToner [0] ['toner_color_name']);
                                    $curType   = strtolower($curToner [0] ['type_name']);
                                    if ($curColor == "black")
                                    {
                                        $has_black = true;
                                    }
                                    else if ($curColor == "yellow")
                                    {
                                        $has_yellow = true;
                                    }
                                    else if ($curColor == "magenta")
                                    {
                                        $has_magenta = true;
                                    }
                                    else if ($curColor == "cyan")
                                    {
                                        $has_cyan = true;
                                    }
                                    else if ($curColor == "3 color")
                                    {
                                        $has_3color = true;
                                    }
                                    else if ($curColor == "4 color")
                                    {
                                        $has_4color = true;
                                    }
                                }
                            }
                        }
                        $toner_errors       = "";
                        $toner_error_colors = "";
                        if ($has_toner)
                        {
                            // Has toners, validate to make sure they match the device
                            switch ($toner_config_id)
                            {
                                case Proposalgen_Model_TonerConfig::BLACK_ONLY:
                                    // BLACK ONLY
                                    if ($has_3color || $has_4color || $has_cyan || $has_magenta || $has_yellow)
                                    {
                                        $repop_form   = 1;
                                        $toners_valid = false;
                                        $toner_errors = "Error: You are trying to add invalid toners to this printer. Only Black Toners are allowed.";
                                    }
                                    else if ($has_black)
                                    {
                                        $toners_valid = true;
                                    }
                                    else
                                    {
                                        $repop_form   = 1;
                                        $toner_errors = "Error: Missing a Black Toner. Please add one and try again.";
                                    }
                                    break;
                                case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED:
                                    // 3 COLOR - SEPARATED
                                    if ($has_3color || $has_4color)
                                    {
                                        $repop_form   = 1;
                                        $toners_valid = false;
                                        $toner_errors = "Error: You are trying to add invalid toners to this printer. Only Black, Yellow, Magenta and Cyan Toners are allowed.";
                                    }
                                    else if ($has_black)
                                    {
                                        if ($has_yellow)
                                        {
                                            if ($has_magenta)
                                            {
                                                if ($has_cyan)
                                                {
                                                    $toners_valid = true;
                                                }
                                                else
                                                {
                                                    $toner_error_colors = "Cyan";
                                                }
                                            }
                                            else
                                            {
                                                if (!empty($toner_error_colors))
                                                {
                                                    $toner_error_colors .= ", ";
                                                }
                                                $toner_error_colors = "Magenta";
                                            }
                                        }
                                        else
                                        {
                                            if (!empty($toner_error_colors))
                                            {
                                                $toner_error_colors .= ", ";
                                            }
                                            $toner_error_colors = "Yellow";
                                        }
                                    }
                                    else
                                    {
                                        if (!empty($toner_error_colors))
                                        {
                                            $toner_error_colors .= ", ";
                                        }
                                        $toner_error_colors = "Black";
                                    }

                                    if ($toner_error_colors != '')
                                    {
                                        $repop_form   = 1;
                                        $toner_errors = "Error: Missing a " . $toner_error_colors . " Toner. Please add one and try again.";
                                    }
                                    break;
                                case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED:
                                    // 3 COLOR - COMBINED
                                    if ($has_4color || $has_cyan || $has_magenta || $has_yellow)
                                    {
                                        $repop_form   = 1;
                                        $toners_valid = false;
                                        $toner_errors = "Error: You are trying to add invalid toners to this printer. Only 3 Color and Black Toners are allowed.";
                                    }
                                    else if ($has_black)
                                    {
                                        if ($has_3color)
                                        {
                                            $toners_valid = true;
                                        }
                                        else
                                        {
                                            $toner_error_colors = "3 Color";
                                        }
                                    }
                                    else
                                    {
                                        if (!empty($toner_error_colors))
                                        {
                                            $toner_error_colors .= ", ";
                                        }
                                        $toner_error_colors = "Black";
                                    }

                                    if ($toner_error_colors != '')
                                    {
                                        $repop_form   = 1;
                                        $toner_errors = "Error: Missing a " . $toner_error_colors . " Toner. Please add one and try again.";
                                    }
                                    break;
                                case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED:
                                    // 4 COLOR - COMBINED
                                    if ($has_3color || $has_black || $has_cyan || $has_magenta || $has_yellow)
                                    {
                                        $repop_form   = 1;
                                        $toners_valid = false;
                                        $toner_errors = "Error: You are trying to add invalid toners to this printer. Only 4 Color Toners are allowed.";
                                    }
                                    else if ($has_4color)
                                    {
                                        $toners_valid = true;
                                    }
                                    else
                                    {
                                        $repop_form   = 1;
                                        $toner_errors = "Error: Missing a 4 Color Toner. Please add one and try again.";
                                    }
                                    break;
                            }
                        }
                        else
                        {
                            // if leased, then toners not required
                            if ($formData ["is_leased"])
                            {
                                $toners_valid = true;
                            }
                            else
                            {
                                $toners_valid = false;
                                $toner_errors = "Error: You must add required toners before saving this device.";
                            }
                        }

                        if ($toners_valid == true)
                        {
                            $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();
                            $launch_date       = new Zend_Date($formData ["launch_date"]);

                            // save master device
                            $master_deviceData = array(
                                'launchDate'       => $launch_date->toString('yyyy-MM-dd HH:mm:ss'),
                                'tonerConfigId'    => $toner_config_id,
                                'isCopier'         => $formData ["is_copier"],
                                'isScanner'        => $formData ["is_scanner"],
                                'isFax'            => $formData ["is_fax"],
                                'isDuplex'         => $formData ["is_duplex"],
                                'wattsPowerNormal' => $formData ["watts_power_normal"],
                                'wattsPowerIdle'   => $formData ["watts_power_idle"],
                                'cost'             => ($formData ["device_price"] == 0 ? null : $formData ["device_price"]),
                                'ppmBlack'         => ($formData ["ppm_black"] > 0) ? $formData ["ppm_black"] : null,
                                'ppmColor'         => ($formData ["ppm_color"] > 0) ? $formData ["ppm_color"] : null,
                                'dutyCycle'        => ($formData ["duty_cycle"] > 0) ? $formData ["duty_cycle"] : null,
                                'isLeased'         => $formData ["is_leased"],
                                'leasedTonerYield' => ($formData ["is_leased"] ? $formData ["leased_toner_yield"] : null)
                            );
                            if ($master_device_id > 0)
                            {
                                // get printer_model
                                $where         = $master_deviceTable->getAdapter()->quoteInto('id = ?', $master_device_id, 'INTEGER');
                                $master_device = $master_deviceTable->fetchRow($where);
                                $printer_model = $master_device ['modelName'];

                                // edit device
                                $master_deviceTable->update($master_deviceData, $where);

                                // remove all device_toners for master
                                // device

                                $where = $device_tonerTable->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                                $device_tonerTable->delete($where);

                                // save new toners
                                if ($has_toner)
                                {
                                    foreach ($toner_array as $key)
                                    {
                                        $toner_id = str_replace("'", "", $key);
                                        if ($toner_id > 0)
                                        {
                                            $device_tonerData = array(
                                                'toner_id'         => $toner_id,
                                                'master_device_id' => $master_device_id
                                            );

                                            $device_tonerTable->insert($device_tonerData);
                                        }
                                    }
                                }

                                $repop_form                = 1;
                                $this->view->printer_model = $master_device_id;
                                $this->_helper->flashMessenger(array (
                                                                     'success' => 'Device "' . $printer_model . '" has been updated.'
                                                               ));
                                $isPrintModelSet           = true;

                                // set selected printer model to new printer model
                                $this->view->printer_model = $master_device_id;
                            }
                            else
                            {
                                // How does it get meters when it saves it get printer_model
                                $manufacturer_id = $formData ['manufacturer_id'];
                                if ($manufacturer_id > 0)
                                {
                                    // Add creation_date to array.
                                    $master_deviceData ["manufacturerId"] = $manufacturer_id;
                                    $master_deviceData ["modelName"]      = $formData ["new_printer"];
                                    $master_deviceData ["dateCreated"]    = $date;
                                    // Check for master device flagged as deleted.
                                    $where                 = $master_deviceTable->getAdapter()->quoteInto('manufacturerId = ' . $manufacturer_id . ' AND modelName = ?', $formData ["new_printer"]);
                                    $master_device_flagged = $master_deviceTable->fetchRow($where);

                                    if (count($master_device_flagged) > 0)
                                    {
                                        $master_device_id = $master_device_flagged ['id'];
//                                        $where               = $master_deviceTable->getAdapter()->quoteInto('master_device_id = ?' . $master_device_id, 'INTEGER');
                                        $this->_helper->flashMessenger(array (
                                                                             'error' => "The printer you're trying to add already exists."
                                                                       ));

                                    }
                                    else
                                    {
                                        // Create a new master device, and populate and insert.
                                        $masterDevice = new Proposalgen_Model_MasterDevice();
                                        $masterDevice->populate($master_deviceData);
                                        // Get the current date for the devices date created.
                                        $masterDevice->dateCreated = $date;
                                        $master_device_id          = Proposalgen_Model_Mapper_MasterDevice::getInstance()->insert($masterDevice);
                                        $this->_helper->flashMessenger(array (
                                                                             'success' => 'Printer "' . $formData ["new_printer"] . '" has been saved.'
                                                                       ));
                                        if ($has_toner)
                                        {
                                            // For each toner attempt to see if current configuration exists. If not, add an entry in the deviceToner table
                                            foreach ($toner_array as $key)
                                            {
                                                $toner_id = str_replace("'", "", $key);
                                                if ($toner_id > 0)
                                                {
                                                    $device_tonerData = array(
                                                        'toner_id'         => $toner_id,
                                                        'master_device_id' => $master_device_id
                                                    );
                                                    $where            = $device_tonerTable->getAdapter()->quoteInto('toner_id = ' . $toner_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                                    $device_toners    = $device_tonerTable->fetchRow($where);

                                                    if (count($device_toners) == 0)
                                                    {
                                                        $device_tonerTable->insert($device_tonerData);
                                                    }
                                                }
                                            }
                                        }
                                        // Save the printer model to the view so it shows the recently
                                        // saved printer on the page after reload.
                                        $this->view->printer_model = $master_device_id;
                                        $isPrintModelSet           = true;
                                        $form_mode                 = 'edit';
                                    }
                                    $repop_form                = 1;
                                    $this->view->printer_model = $master_device_id;
                                }
                                else
                                {
                                    $this->_helper->flashMessenger(array (
                                                                         'error' => 'Error: No manufacturer has been selected.'
                                                                   ));
                                }
                            }
                            $db->commit();
                        }
                        else
                        {
                            // invalid toners - return message
                            $db->rollback();
                            $repop_form                = 1;
                            $this->view->printer_model = $formData ['printer_model'];
                            $this->_helper->flashMessenger(array (
                                                                 'error' => $toner_errors
                                                           ));
                        }
                    }
                    catch (Zend_Db_Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array (
                                                             'error' => 'Database Error: "' . $formData ["new_printer"] . '" could not be saved. Make sure the printer does not already exist.'
                                                       ));
                        Throw new exception("Critical Device Update Error.", 0, $e);
                    }
                    catch (Exception $e)
                    {
                        // CRITICAL UPDATE EXCEPTION
                        $db->rollback();
                        Throw new exception("Critical Device Update Error.", 0, $e);
                    } // end catch
                }
                else if ($formData ['save_flag'] == 'delete')
                {
                    // always attempt to repop after delete
                    $repop_form = 1;

                    $db->beginTransaction();
                    try
                    {

                        $printer_model      = '';
                        $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();
                        $master_device_id   = $formData ['printer_model'];
                        $where              = $master_deviceTable->getAdapter()->quoteInto('id = ?', $master_device_id, 'INTEGER');
                        $master_device      = $master_deviceTable->fetchRow($where);

                        if (count($master_device) > 0)
                        {

                            $printer_model = $master_device ['modelName'];
                            // NEED TO CHECK IF REPLACEMENT DEVICE
                            $replacement_devicesTable = new Proposalgen_Model_DbTable_ReplacementDevices();
                            $where                    = $replacement_devicesTable->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                            $replacement_devices      = $replacement_devicesTable->fetchAll($where);

                            if (count($replacement_devices) > 0)
                            {
                                $db->rollback();
                                $this->_helper->flashMessenger(array (
                                                                     'warning' => "This printer is currently configured as a replacement printer. Please remove it as a replacement printer and try again."
                                                               ));
                            }
                            else
                            {
                                // Set reports modified flag
                                $reportTableMapper = Proposalgen_Model_Mapper_Report::getInstance();
                                $reportTableMapper->setDevicesModifiedFlagOnReports($master_device_id);

                                // DELETE MASTER DEVICE
                                $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();
                                $where              = $master_deviceTable->getAdapter()->quoteInto('id = ?', $master_device_id, 'INTEGER');
                                $master_deviceTable->delete($where);

                                $db->commit();
                                $form_mode                 = 'delete';
                                $this->view->printer_model = 0;
                                $this->_helper->flashMessenger(array (
                                                                     'success' => "Printer " . $printer_model . " has been deleted."
                                                               ));
                            }
                        }
                        else
                        {
                            $db->rollback();
                            $this->_helper->flashMessenger(array (
                                                                 'error' => "No printer was selected. Please select a printer and try again."
                                                           ));
                        }
                    }
                    catch (Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array (
                                                             'error' => "There was an error and the printer was not deleted."
                                                       ));
                    }
                }
            }

            // add failed, repop form with entered values
            $this->view->form_mode = $form_mode;
            if ($repop_form == 1)
            {
                $this->view->repop = true;

                $form->getElement('save_flag')->setValue($formData ['save_flag']);
                $form->getElement('toner_array')->setValue($formData ['toner_array']);
                $form->getElement('form_mode')->setValue($form_mode);

                $form->getElement('manufacturer_id')->setValue($formData ['manufacturer_id']);

                if (isset($formData ['printer_model']))
                {
                    $form->getElement('printer_model')->setValue($formData ['printer_model']);
                    // If we didn't successfully update the database, get the printer model from the form.
                    // If we successfully update the database, this is already set.
                    if (!$isPrintModelSet)
                    {
                        $this->view->printer_model = $formData ['printer_model'];
                    }
                }
                $form->getElement('new_printer')->setValue($formData ['new_printer']);
                $form->getElement('launch_date')->setValue($formData ['launch_date']);
                $form->getElement('device_price')->setValue($formData ['device_price']);
                $form->getElement('toner_config_id')->setValue($formData ['toner_config_id']);
                $form->getElement('is_copier')->setAttrib('checked', $formData ['is_copier']);
                $form->getElement('is_scanner')->setAttrib('checked', $formData ['is_scanner']);
                $form->getElement('is_fax')->setAttrib('checked', $formData ['is_fax']);
                $form->getElement('is_duplex')->setAttrib('checked', $formData ['is_duplex']);
                $form->getElement('watts_power_normal')->setValue($formData ['watts_power_normal']);
                $form->getElement('watts_power_idle')->setValue($formData ['watts_power_idle']);

                $form->getElement('is_leased')->setAttrib('checked', $formData ['is_leased']);
                $form->getElement('leased_toner_yield')->setValue($formData ['leased_toner_yield']);

                $form->getElement('ppm_black')->setValue($formData ['ppm_black']);
                $form->getElement('ppm_color')->setValue($formData ['ppm_color']);
                $form->getElement('duty_cycle')->setValue($formData ['duty_cycle']);
            }
        }
        $this->view->deviceform = $form;
    }

    public function manageticketdevicesAction ()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/libs/jqgrid/plugins/grid.celledit.js'), 'text/javascript');
        $db = Zend_Db_Table::getDefaultAdapter();

        // add device form
        $form = new Proposalgen_Form_Device(null, "edit");
        $form->removeElement('serial_number');
        $form->removeElement('override_price');
        $form->removeElement('save_device');
        $form->removeElement('delete_device');
        $form->removeElement('back_button');

        // remove new man and edit model links
        $form->getElement('printer_model')->setDescription('');
        $form->getElement('new_printer')->setDescription('');

        // fill manufacturer dropdown
        $list                          = "";
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('isDeleted = 0', 'fullname');
        $currElement                   = $form->getElement('manufacturer_id');
        $this->view->manufacturer_list = $manufacturers;

        // add link to the manage manufacturer page
        $currElement->addMultiOption('0', 'Select Manufacturer');
        foreach ($manufacturers as $row)
        {
            $currElement->addMultiOption($row ['id'], ucwords(strtolower($row ['fullname'])));
            if (empty($list) == false)
            {
                $list .= ";";
            }
            $list .= $row ['id'] . ":" . ucwords(strtolower($row ['fullname']));
        }
        $this->view->manufacturers = $list;

        // fill toner_config dropdown
        $toner_configTable = new Proposalgen_Model_DbTable_TonerConfig();
        $toner_configs     = $toner_configTable->fetchAll(null, 'name');
        $currElement       = $form->getElement('toner_config_id');
        $currElement->addMultiOption('', 'Select Toner Config');
        foreach ($toner_configs as $row)
        {
            $currElement->addMultiOption($row ['id'], ucwords(strtolower($row ['name'])));
        }

        // return part_type list
        $list           = "";
        $part_type      = "";
        $part_typeTable = new Proposalgen_Model_DbTable_PartType();
        $part_types     = $part_typeTable->fetchAll();
        foreach ($part_types as $row)
        {
            $part_type = ucwords(strtolower($row ['name']));
            if ($part_type == "Oem")
            {
                $part_type = "OEM";
            }

            if (empty($list) == false)
            {
                $list .= ";";
            }
            $list .= $row ['id'] . ":" . $part_type;
        }
        $this->view->partTypeList = $list;

        // return color list
        $list             = "";
        $toner_colorTable = new Proposalgen_Model_DbTable_TonerColor();
        $toner_colors     = $toner_colorTable->fetchAll();
        foreach ($toner_colors as $row)
        {
            if (empty($list) == false)
            {
                $list .= ";";
            }
            $list .= $row ['id'] . ":" . ucwords(strtolower($row ['name']));
        }
        $this->view->tonerColorList = $list;

        $this->view->blackOnlyList     = "1:Black";
        $this->view->seperateColorList = "1:Black;2:Cyan;3:Magenta;4:Yellow";
        $this->view->threeColorList    = "5:3 Color";
        $this->view->fourColorList     = "6:4 Color";

        // check if this page has been posted to
        if ($this->_request->isPost())
        {
            $repop_form = 0;
            $formData   = $this->_request->getPost();
            // print_r($formData); die;


            // conditional requirements
            $form->set_validation($formData);

            if ($formData ['form_mode'] == "ticket")
            {
                $ticket_id     = $formData ['ticket_id'];
                $devices_pf_id = $formData ['devices_pf_id'];

                try
                {

                    // get ticket
                    $ticketsMapper = Proposalgen_Model_Mapper_Ticket::getInstance();
                    $ticket        = $ticketsMapper->find($ticket_id);

                    if (count($ticket) > 0)
                    {

                        // get device info
                        $ticket_pf_requestMapper = Proposalgen_Model_Mapper_TicketPFRequest::getInstance();
                        $result                  = $ticket_pf_requestMapper->find($ticket_id);

                        if ($result)
                        {
                            // get printer details
                            $devices_pf_id       = $result->devicePfId;
                            $device_manufacturer = $result->deviceManufacturer;
                            $printer_model       = $result->printerModel;
                            $launch_date         = $result->launchDate;
                            $device_price        = ($result->devicePrice > 0 ? $result->devicePrice : null);
                            $toner_config        = $result->tonerConfig;
                            $is_copier           = $result->isCopier;
                            $is_fax              = $result->isFax;
                            $is_scanner          = $result->isScanner;
                            $is_duplex           = $result->isDuplex;
                            $ppm_black           = ($result->ppmBlack > 0 ? $result->ppmBlack : null);
                            $ppm_color           = ($result->ppmColor > 0 ? $result->ppmColor : null);
                            $duty_cycle          = ($result->dutyCycle > 0 ? $result->dutyCycle : null);
                            $watts_power_normal  = ($result->wattsPowerNormal > 0 ? $result->wattsPowerNormal : null);
                            $watts_power_idle    = ($result->wattsPowerIdle > 0 ? $result->wattsPowerIdle : null);
                            $is_leased           = null;
                            $leased_toner_yield  = null;

                            // check to see if manufacturer exists
                            $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
                            $where              = $manufacturersTable->getAdapter()->quoteInto('UPPER(fullname) = ?', strtoupper(trim($device_manufacturer)));
                            $manufacturers      = $manufacturersTable->fetchRow($where);

                            if (count($manufacturers) > 0)
                            {
                                $manufacturer_id = $manufacturers ['id'];
                            }
                            else
                            {
                                $manufacturer_id           = 0;
                                $this->view->ticket_error  = true;
                                $this->view->form_mode     = 'save';
                                $this->view->ticket_id     = $ticket_id;
                                $this->view->devices_pf_id = $devices_pf_id;
                                $this->view->manufacturer  = ucwords(trim($device_manufacturer));
                            }

                            // populate form
                            $launch_date = new Zend_Date($launch_date, "yyyy-MM-dd HH:ii:ss");

                            // load device values
                            $form->getElement('ticket_id')->setValue($ticket_id);
                            $form->getElement('devices_pf_id')->setValue($devices_pf_id);
                            $form->getElement('form_mode')->setValue('save');
                            $form->getElement('manufacturer_id')->setValue($manufacturer_id);
                            $form->getElement('printer_model')->setValue(null);
                            $form->getElement('new_printer')->setValue($printer_model);
                            $form->getElement('launch_date')->setValue($launch_date->toString('MM/dd/yyyy'));
                            $form->getElement('device_price')->setValue($device_price);
                            $form->getElement('toner_config_id')->setValue($toner_config);
                            $form->getElement('is_copier')->setAttrib('checked', ($is_copier));
                            $form->getElement('is_fax')->setAttrib('checked', ($is_fax));
                            $form->getElement('is_scanner')->setAttrib('checked', ($is_scanner));
                            $form->getElement('is_duplex')->setAttrib('checked', ($is_duplex));
                            $form->getElement('ppm_black')->setValue($ppm_black);
                            $form->getElement('ppm_color')->setValue($ppm_color);
                            $form->getElement('duty_cycle')->setValue($duty_cycle);
                            $form->getElement('watts_power_normal')->setValue($watts_power_normal);
                            $form->getElement('watts_power_idle')->setValue($watts_power_idle);
                            $form->getElement('is_leased')->setAttrib('checked', ($is_leased));
                            $form->getElement('leased_toner_yield')->setValue($leased_toner_yield);

                        }
                        else
                        {
                            // no request made
                        }
                    }
                }
                catch (Exception $e)
                {
                    // echo $e; die();
                }
                // change done button to return to managetickets

                $this->view->done_action = "javascript: document.location.href='" . $this->view->baseUrl("/proposalgen/ticket/ticketdetails") . "?id=" . $ticket_id . "'";

            }
            else if ($form->isValid($formData))
            {

                $date = date('Y-m-d H:i:s');
                // validate fields
                if ($formData ["manufacturer_id"] == 0)
                {
                    $repop_form          = 1;
                    $this->_helper->flashMessenger(array (
                                                         'error' => 'Error: You must select a manufacturer.'
                                                   ));
                }
                else if (trim($formData ["new_printer"]) == "")
                {
                    $repop_form          = 1;
                    $this->_helper->flashMessenger(array (
                                                         'error' => 'Error: You must enter a printer model name.'
                                                   ));
                }
                else if ($formData ["toner_config_id"] == 0)
                {
                    $repop_form          = 1;
                    $this->_helper->flashMessenger(array (
                                                         'error' => 'Error: Toner Config not selected. Please try again.'
                                                   ));
                }
                else if ($formData ["watts_power_normal"] < 1)
                {
                    $repop_form          = 1;
                    $this->_helper->flashMessenger(array (
                                                         'error' => 'Error: Power Consumption Normal must be greater then zero.'
                                                   ));
                }
                else if ($formData ["watts_power_idle"] < 1)
                {
                    $repop_form          = 1;
                    $this->_helper->flashMessenger(array (
                                                         'error' => 'Error: Power Consumption Idle must be greater then zero.'
                                                   ));
                }
                else
                {

                    if ($formData ['save_flag'] == "save")
                    {
                        // update the selected device
                        $db->beginTransaction();
                        try
                        {
                            $master_device_id   = $formData ['hdnID'];
                            $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();

                            // validate toners against toner_config
                            $has_toner    = false;
                            $has_black    = false;
                            $has_yellow   = false;
                            $has_magenta  = false;
                            $has_cyan     = false;
                            $has_3color   = false;
                            $has_4color   = false;
                            $toners_valid = false;

                            $toner_config_id = $formData ['toner_config_id'];
                            $toner_array     = explode(",", $formData ["toner_array"]);

                            foreach ($toner_array as $key)
                            {
                                $toner_id = str_replace("'", "", $key);

                                if ($toner_id > 0)
                                {
                                    // get color and type from $key
                                    $select   = new Zend_Db_Select($db);
                                    $select   = $db->select()
                                        ->from(array(
                                                    't' => 'pgen_toners'
                                               ))
                                        ->join(array(
                                                    'tc' => 'pgen_toner_colors'
                                               ), 'tc.id = t.tonerColorId', array(
                                                                                 'name AS toner_color_name'
                                                                            ))
                                        ->join(array(
                                                    'pt' => 'pgen_part_types'
                                               ), 'pt.id = t.partTypeId', array(
                                                                               'name AS type_name'
                                                                          ))->where('t.id = ?', $toner_id);
                                    $stmt     = $db->query($select);
                                    $curToner = $stmt->fetchAll();
                                    if (count($curToner) > 0)
                                    {
                                        $has_toner = true;

                                        $curColor = strtolower($curToner [0] ['toner_color_name']);
                                        $curType  = strtolower($curToner [0] ['type_name']);
                                        if ($curColor == "black")
                                        {
                                            $has_black = true;
                                        }
                                        else if ($curColor == "yellow")
                                        {
                                            $has_yellow = true;
                                        }
                                        else if ($curColor == "magenta")
                                        {
                                            $has_magenta = true;
                                        }
                                        else if ($curColor == "cyan")
                                        {
                                            $has_cyan = true;
                                        }
                                        else if ($curColor == "3 color")
                                        {
                                            $has_3color = true;
                                        }
                                        else if ($curColor == "4 color")
                                        {
                                            $has_4color = true;
                                        }
                                    }
                                }
                            }
                            $toner_errors       = "";
                            $toner_error_colors = "";
                            if ($has_toner)
                            {
                                // Has toners, validate to make sure they match the device
                                switch ($toner_config_id)
                                {
                                    case Proposalgen_Model_TonerConfig::BLACK_ONLY:
                                        if ($has_3color || $has_4color || $has_cyan || $has_magenta || $has_yellow)
                                        {
                                            $repop_form   = 1;
                                            $toners_valid = false;
                                            $toner_errors = "Error: You are trying to add invalid toners to this printer. Only Black Toners are allowed.";
                                        }
                                        else if ($has_black)
                                        {
                                            $toners_valid = true;
                                        }
                                        else
                                        {
                                            $repop_form   = 1;
                                            $toner_errors = "Error: Missing a Black Toner. Please add one and try again.";
                                        }
                                        break;
                                    case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED:
                                        if ($has_3color || $has_4color)
                                        {
                                            $repop_form   = 1;
                                            $toners_valid = false;
                                            $toner_errors = "Error: You are trying to add invalid toners to this printer. Only Black, Yellow, Magenta and Cyan Toners are allowed.";
                                        }
                                        else if ($has_black)
                                        {
                                            if ($has_yellow)
                                            {
                                                if ($has_magenta)
                                                {
                                                    if ($has_cyan)
                                                    {
                                                        $toners_valid = true;
                                                    }
                                                    else
                                                    {
                                                        $toner_error_colors = "Cyan";
                                                    }
                                                }
                                                else
                                                {
                                                    if (!empty($toner_error_colors))
                                                    {
                                                        $toner_error_colors .= ", ";
                                                    }
                                                    $toner_error_colors = "Magenta";
                                                }
                                            }
                                            else
                                            {
                                                if (!empty($toner_error_colors))
                                                {
                                                    $toner_error_colors .= ", ";
                                                }
                                                $toner_error_colors = "Yellow";
                                            }
                                        }
                                        else
                                        {
                                            if (!empty($toner_error_colors))
                                            {
                                                $toner_error_colors .= ", ";
                                            }
                                            $toner_error_colors = "Black";
                                        }

                                        if ($toner_error_colors != '')
                                        {
                                            $repop_form   = 1;
                                            $toner_errors = "Error: Missing a " . $toner_error_colors . " Toner. Please add one and try again.";
                                        }
                                        break;
                                    case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED:
                                        if ($has_4color || $has_cyan || $has_magenta || $has_yellow)
                                        {
                                            $repop_form   = 1;
                                            $toners_valid = false;
                                            $toner_errors = "Error: You are trying to add invalid toners to this printer. Only 3 Color and Black Toners are allowed.";
                                        }
                                        else if ($has_black)
                                        {
                                            if ($has_3color)
                                            {
                                                $toners_valid = true;
                                            }
                                            else
                                            {
                                                $toner_error_colors = "3 Color";
                                            }
                                        }
                                        else
                                        {
                                            if (!empty($toner_error_colors))
                                            {
                                                $toner_error_colors .= ", ";
                                            }
                                            $toner_error_colors = "Black";
                                        }

                                        if ($toner_error_colors != '')
                                        {
                                            $repop_form   = 1;
                                            $toner_errors = "Error: Missing a " . $toner_error_colors . " Toner. Please add one and try again.";
                                        }
                                        break;
                                    case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED:
                                        if ($has_3color || $has_black || $has_cyan || $has_magenta || $has_yellow)
                                        {
                                            $repop_form   = 1;
                                            $toners_valid = false;
                                            $toner_errors = "Error: You are trying to add invalid toners to this printer. Only 4 Color Toners are allowed.";
                                        }
                                        else if ($has_4color)
                                        {
                                            $toners_valid = true;
                                        }
                                        else
                                        {
                                            $repop_form   = 1;
                                            $toner_errors = "Error: Missing a 4 Color Toner. Please add one and try again.";
                                        }
                                        break;
                                }
                            }
                            else
                            {
                                // if leased, then toners not required
                                if ($formData ["is_leased"])
                                {
                                    $toners_valid = true;
                                }
                                else
                                {
                                    $toners_valid = false;
                                    $toner_errors = "Error: You must add required toners before saving this device.";
                                }
                            }

                            if ($toners_valid == true)
                            {
                                $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();
                                $launch_date       = new Zend_Date($formData ["launch_date"]);

                                // save master device
                                $master_deviceData = array(
                                    'launch_date'        => $launch_date->toString('yyyy-MM-dd HH:mm:ss'),
                                    'toner_config_id'    => $toner_config_id,
                                    'is_copier'          => $formData ["is_copier"],
                                    'is_scanner'         => $formData ["is_scanner"],
                                    'is_fax'             => $formData ["is_fax"],
                                    'is_duplex'          => $formData ["is_duplex"],
                                    'watts_power_normal' => $formData ["watts_power_normal"],
                                    'watts_power_idle'   => $formData ["watts_power_idle"],
                                    'cost'               => ($formData ["device_price"] == 0 ? null : $formData ["device_price"]),
                                    'ppm_black'          => $formData ["ppm_black"],
                                    'ppm_color'          => $formData ["ppm_color"],
                                    'duty_cycle'         => $formData ["duty_cycle"],
                                    'is_leased'          => $formData ["is_leased"],
                                    'leased_toner_yield' => ($formData ["is_leased"] ? $formData ["leased_toner_yield"] : null)
                                );

                                if ($master_device_id > 0)
                                {

                                    // get printer_model
                                    $where         = $master_deviceTable->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                                    $master_device = $master_deviceTable->fetchRow($where);
                                    $printer_model = $master_device ['printer_model'];

                                    // edit device
                                    $master_deviceTable->update($master_deviceData, $where);

                                    // remove all device_toners for master device
                                    $where = $device_tonerTable->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                                    $device_tonerTable->delete($where);

                                    // save new toners
                                    if ($has_toner)
                                    {
                                        foreach ($toner_array as $key)
                                        {
                                            $toner_id = str_replace("'", "", $key);
                                            if ($toner_id > 0)
                                            {
                                                $device_tonerData = array(
                                                    'toner_id'         => $toner_id,
                                                    'master_device_id' => $master_device_id
                                                );
                                                $device_tonerTable->insert($device_tonerData);
                                            }
                                        }
                                    }
                                    $repop_form                = 1;
                                    $this->view->printer_model = $master_device_id;
                                    $this->_helper->flashMessenger(array (
                                                                         'success' => 'Device "' . $printer_model . '" has been updated.'
                                                                   ));

                                    // Set selected printer model to new printer model
                                    $this->view->printer_model = $master_device_id;
                                }
                                else
                                {

                                    // get printer_model
                                    $manufacturer_id = $formData ['manufacturer_id'];

                                    if ($manufacturer_id > 0)
                                    {
                                        // add creation_date to array
                                        $master_deviceData ["manufacturer_id"] = $manufacturer_id;
                                        $master_deviceData ["printer_model"]   = $formData ["new_printer"];
                                        $master_deviceData ['date_created']    = $date;

                                        // check for master device
                                        $where                 = $master_deviceTable->getAdapter()->quoteInto('manufacturer_id = ' . $manufacturer_id . ' AND printer_model = ?', $formData ["new_printer"]);
                                        $master_device_flagged = $master_deviceTable->fetchRow($where);

                                        if (count($master_device_flagged) > 0)
                                        {
                                            $master_device_id    = $master_device_flagged ['id'];
                                            $where               = $master_deviceTable->getAdapter()->quoteInto('id = ?' . $master_device_id, 'INTEGER');
                                            $this->_helper->flashMessenger(array (
                                                                                 'error' => "The printer you're trying to add already exists."
                                                                           ));
                                        }
                                        else
                                        {
                                            $master_device_id    = $master_deviceTable->insert($master_deviceData);
                                            $this->_helper->flashMessenger(array (
                                                                                 'success' => 'Printer "' . $formData ["new_printer"] . '" has been saved.'
                                                                           ));

                                            // save toners
                                            if ($has_toner)
                                            {
                                                foreach ($toner_array as $key)
                                                {
                                                    $toner_id = str_replace("'", "", $key);
                                                    if ($toner_id > 0)
                                                    {
                                                        $device_tonerData = array(
                                                            'toner_id'         => $toner_id,
                                                            'master_device_id' => $master_device_id
                                                        );
                                                        $where            = $device_tonerTable->getAdapter()->quoteInto('toner_id = ' . $toner_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                                        $device_toners    = $device_tonerTable->fetchRow($where);
                                                        if (count($device_toners) == 0)
                                                        {
                                                            $device_tonerTable->insert($device_tonerData);
                                                        }
                                                    }
                                                }
                                            }

                                            $devices_pf_id              = $formData ["devices_pf_id"];
                                            $unknown_device_instance_id = $formData ["unknown_device_instance_id"];

                                            // update master_matchup_pf
                                            $master_matchup_pfTable = new Proposalgen_Model_DbTable_PFMasterMatchup();
                                            $master_matchup_pfData  = array(
                                                'master_device_id' => $master_device_id
                                            );
                                            $where                  = $master_matchup_pfTable->getAdapter()->quoteInto('pf_device_id = ?', $devices_pf_id, 'INTEGER');
                                            $master_matchup_pf      = $master_matchup_pfTable->fetchAll($where);

                                            if (count($master_matchup_pf) == 0)
                                            {
                                                $master_matchup_pfData ['pf_device_id'] = $devices_pf_id;
                                                $master_matchup_pfTable->insert($master_matchup_pfData);
                                            }
                                            else
                                            {
                                                $master_matchup_pfTable->update($master_matchup_pfData, $where);
                                            }
                                        }
                                        $repop_form = 1;
                                    }
                                    else
                                    {
                                        $this->_helper->flashMessenger(array (
                                                                             'error' => 'Error: No manufacturer has been selected.'
                                                                       ));
                                    }
                                }
                                $db->commit();
                            }
                            else
                            {
                                // invalid toners - return message
                                $db->rollback();
                                $repop_form          = 1;
                                $this->_helper->flashMessenger(array (
                                                                     'error' => $toner_errors
                                                               ));
                            }
                        }
                        catch (Zend_Db_Exception $e)
                        {
                            $db->rollback();
                            $this->_helper->flashMessenger(array (
                                                                 'error' => 'Database Error: "' . $formData ["new_printer"] . '" could not be saved. Make sure the printer does not already exist'
                                                           ));
                            //echo $e; die;
                        }
                        catch (Exception $e)
                        {
                            // CRITICAL UPDATE EXCEPTION
                            $db->rollback();
                            Throw new exception("Critical Device Update Error.", 0, $e);
                        } // end catch
                    }
                }
            }
            else
            {

                $db->rollback();
                $repop_form = 1;
            }

            // add failed, repop form with entered values
            if ($repop_form == 1)
            {

                $this->view->repop = true;

                $form->getElement('hdnID')->setValue($formData ['hdnID']);
                $form->getElement('hdnItem')->setValue($formData ['hdnItem']);
                $form->getElement('ticket_id')->setValue($formData ['ticket_id']);
                $form->getElement('devices_pf_id')->setValue($formData ['devices_pf_id']);
                $form->getElement('unknown_device_instance_id')->setValue($formData ['unknown_device_instance_id']);
                $form->getElement('save_flag')->setValue($formData ['save_flag']);
                $form->getElement('toner_array')->setValue($formData ['toner_array']);
                $form->getElement('form_mode')->setValue('save');

                $form->getElement('manufacturer_id')->setValue($formData ['manufacturer_id']);

                if (isset($formData ['printer_model']))
                {
                    $form->getElement('printer_model')->setValue($formData ['printer_model']);
                }
                $form->getElement('new_printer')->setValue($formData ['new_printer']);

                $form->getElement('launch_date')->setValue($formData ['launch_date']);
                $form->getElement('device_price')->setValue($formData ['device_price']);
                $form->getElement('toner_config_id')->setValue($formData ['toner_config_id']);
                $form->getElement('is_copier')->setAttrib('checked', $formData ['is_copier']);
                $form->getElement('is_scanner')->setAttrib('checked', $formData ['is_scanner']);
                $form->getElement('is_fax')->setAttrib('checked', $formData ['is_fax']);
                $form->getElement('is_duplex')->setAttrib('checked', $formData ['is_duplex']);
                $form->getElement('watts_power_normal')->setValue($formData ['watts_power_normal']);
                $form->getElement('watts_power_idle')->setValue($formData ['watts_power_idle']);

                $form->getElement('is_leased')->setAttrib('checked', $formData ['is_leased']);
                $form->getElement('leased_toner_yield')->setValue($formData ['leased_toner_yield']);

                $form->getElement('ppm_black')->setValue($formData ['ppm_black']);
                $form->getElement('ppm_color')->setValue($formData ['ppm_color']);
                $form->getElement('duty_cycle')->setValue($formData ['duty_cycle']);
            }

        } // end if
        $this->view->deviceform = $form;
    }

    public function managemappingdevicesAction ()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/libs/jqgrid/plugins/grid.celledit.js'), 'text/javascript');
        $db = Zend_Db_Table::getDefaultAdapter();

        // add device form
        $form = new Proposalgen_Form_Device(null, "edit");
        $form->removeElement('serial_number');
        $form->removeElement('override_price');
        $form->removeElement('save_device');
        $form->removeElement('delete_device');
        $form->removeElement('back_button');

        // remove new man and edit model links
        $form->getElement('printer_model')->setDescription('');
        $form->getElement('new_printer')->setDescription('');

        // fill manufacturer dropdown
        $list                          = "";
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('is_deleted = 0', 'manufacturer_name');
        $currElement                   = $form->getElement('manufacturer_id');
        $this->view->manufacturer_list = $manufacturers;

        // add link to the manage manufacturer page
        $currElement->addMultiOption('0', 'Select Manufacturer');
        foreach ($manufacturers as $row)
        {
            $currElement->addMultiOption($row ['manufacturer_id'], ucwords(strtolower($row ['manufacturer_name'])));
            if (empty($list) == false)
            {
                $list .= ";";
            }
            $list .= $row ['manufacturer_id'] . ":" . ucwords(strtolower($row ['manufacturer_name']));
        }
        $this->view->manufacturers = $list;

        // fill toner_config dropdown
        $toner_configTable = new Proposalgen_Model_DbTable_TonerConfig();
        $toner_configs     = $toner_configTable->fetchAll(null, 'toner_config_name');
        $currElement       = $form->getElement('toner_config_id');
        $currElement->addMultiOption('', 'Select Toner Config');
        foreach ($toner_configs as $row)
        {
            $currElement->addMultiOption($row ['toner_config_id'], ucwords(strtolower($row ['toner_config_name'])));
        }

        // return part_type list
        $list           = "";
        $part_type      = "";
        $part_typeTable = new Proposalgen_Model_DbTable_PartType();
        $part_types     = $part_typeTable->fetchAll();
        foreach ($part_types as $row)
        {
            $part_type = ucwords(strtolower($row ['type_name']));
            if ($part_type == "Oem")
            {
                $part_type = "OEM";
            }

            if (empty($list) == false)
            {
                $list .= ";";
            }
            $list .= $row ['partTypeId'] . ":" . $part_type;
        }
        $this->view->partTypeList = $list;

        // return color list
        $list             = "";
        $toner_colorTable = new Proposalgen_Model_DbTable_TonerColor();
        $toner_colors     = $toner_colorTable->fetchAll();
        foreach ($toner_colors as $row)
        {
            if (empty($list) == false)
            {
                $list .= ";";
            }
            $list .= $row ['toner_color_id'] . ":" . ucwords(strtolower($row ['toner_color_name']));
        }
        $this->view->tonerColorList = $list;

        // color arrays
        $this->view->blackOnlyList     = "1:Black";
        $this->view->seperateColorList = "1:Black;2:Cyan;3:Magenta;4:Yellow";
        $this->view->threeColorList    = "5:3 Color";
        $this->view->fourColorList     = "6:4 Color";

        // check if this page has been posted to
        if ($this->_request->isPost())
        {
            $repop_form = 0;
            $formData   = $this->_request->getPost();
            //print_r($formData); die;


            // conditional requirements
            $form->set_validation($formData);
            $itemtype = $formData ['hdnItem'];

            if ($formData ['form_mode'] == 'mapping')
            {
                if ($itemtype == 'master')
                {
                    $master_device_id         = $formData ['hdnID'];
                    $upload_data_collector_id = 0;
                    $this->view->hdnID        = $master_device_id;

                    // get device info
                    $master_deviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();
                    $result              = $master_deviceMapper->find($master_device_id);

                    if ($result)
                    {
                        // get printer details
                        $devices_pf_id       = 0;
                        $device_manufacturer = $result->Manufacturer->ManufacturerName;
                        $printer_model       = str_replace($device_manufacturer . ' ', '', $result->PrinterModel);
                        $launch_date         = $result->LaunchDate;
                        $device_price        = $result->DevicePrice;
                        $toner_config        = $result->TonerConfig->TonerConfigId;
                        $is_copier           = $result->IsCopier;
                        $is_fax              = $result->IsFax;
                        $is_scanner          = $result->IsScanner;
                        $is_duplex           = $result->IsDuplex;
                        $ppm_black           = ($result->PpmBlack > 0 ? $result->PpmBlack : null);
                        $ppm_color           = ($result->PpmColor > 0 ? $result->PpmColor : null);
                        $duty_cycle          = ($result->DutyCycle > 0 ? $result->DutyCycle : null);
                        $watts_power_normal  = ($result->WattsPowerNormal > 0 ? $result->WattsPowerNormal : null);
                        $watts_power_idle    = ($result->WattsPowerIdle > 0 ? $result->WattsPowerIdle : null);
                        $is_leased           = $result->IsLeased;
                        $leased_toner_yield  = $result->LeasedTonerYield;
                        $manufacturer_id     = $result->ManufacturerId;

                        // get toners for device
                        $select = new Zend_Db_Select($db);
                        $select = $db->select()
                            ->from(array(
                                        't' => 'toner'
                                   ))
                            ->join(array(
                                        'td' => 'device_toner'
                                   ), 't.toner_id = td.toner_id')
                            ->where('td.master_device_id = ?', $master_device_id);
                        $stmt   = $db->query($select);
                        $result = $stmt->fetchAll();

                        $toner_array = '';
                        foreach ($result as $key)
                        {
                            if (!empty($toner_array))
                            {
                                $toner_array .= ",";
                            }
                            $toner_array .= "'" . $key ['toner_id'] . "'";
                        }

                        // populate form
                        $launch_date = new Zend_Date($launch_date, "yyyy-MM-dd HH:ii:ss");

                        // load device values
                        $form->getElement('hdnID')->setValue($master_device_id);
                        $form->getElement('form_mode')->setValue('save');
                        $form->getElement('hdnItem')->setValue('master');
                        $form->getElement('devices_pf_id')->setValue($devices_pf_id);
                        $form->getElement('manufacturer_id')->setValue($manufacturer_id);
                        $form->getElement('printer_model')->setValue($master_device_id);
                        $form->getElement('new_printer')->setValue($printer_model);
                        $form->getElement('launch_date')->setValue($launch_date->toString('MM/dd/yyyy'));
                        $form->getElement('device_price')->setValue($device_price);
                        $form->getElement('toner_config_id')->setValue($toner_config);
                        $form->getElement('is_copier')->setAttrib('checked', ($is_copier));
                        $form->getElement('is_fax')->setAttrib('checked', ($is_fax));
                        $form->getElement('is_scanner')->setAttrib('checked', ($is_scanner));
                        $form->getElement('is_duplex')->setAttrib('checked', ($is_duplex));
                        $form->getElement('ppm_black')->setValue($ppm_black);
                        $form->getElement('ppm_color')->setValue($ppm_color);
                        $form->getElement('duty_cycle')->setValue($duty_cycle);
                        $form->getElement('watts_power_normal')->setValue($watts_power_normal);
                        $form->getElement('watts_power_idle')->setValue($watts_power_idle);
                        $form->getElement('is_leased')->setAttrib('checked', ($is_leased));
                        $form->getElement('leased_toner_yield')->setValue($leased_toner_yield);
                        $form->getElement('toner_array')->setValue($toner_array);
                    }
                }
                else
                {
                    $upload_data_collector_id = $formData ['hdnID'];
                    $this->view->hdnID        = $upload_data_collector_id;

                    // get device info
                    $upload_data_collectorMapper = Proposalgen_Model_Mapper_Rms_Upload_Row::getInstance();
                    $result                      = $upload_data_collectorMapper->find($upload_data_collector_id);

                    if ($result)
                    {
                        // get printer details
                        $devices_pf_id = $result->DevicesPfId;
                        $form->getElement('hdnItem')->setValue('');
                        $device_manufacturer = $result->Manufacturer;
                        $printer_model       = str_replace($device_manufacturer . ' ', '', $result->ModelName);
                        $launch_date         = $result->DateIntroduction;
                        $device_price        = null;
                        $toner_config        = ($result->StartMeterColor > 0 ? 2 : 1);
                        $is_copier           = $result->IsCopier;
                        $is_fax              = $result->IsFax;
                        $is_scanner          = $result->IsScanner;
                        $is_duplex           = 0;
                        $ppm_black           = ($result->PpmBlack > 0 ? $result->PpmBlack : null);
                        $ppm_color           = ($result->PpmColor > 0 ? $result->PpmColor : null);
                        $duty_cycle          = ($result->DutyCycle > 0 ? $result->DutyCycle : null);
                        $watts_power_normal  = ($result->WattsPowerNormal > 0 ? $result->WattsPowerNormal : null);
                        $watts_power_idle    = ($result->WattsPowerIdle > 0 ? $result->WattsPowerIdle : null);
                        $is_leased           = null;
                        $leased_toner_yield  = null;

                        // check to see if manufacturer exists
                        $manufacturersTable = new Proposalgen_Model_DbTable_Manufacturer();
                        $where              = $manufacturersTable->getAdapter()->quoteInto('UPPER(manufacturer_name) = ?', strtoupper(trim($device_manufacturer)));
                        $manufacturers      = $manufacturersTable->fetchRow($where);

                        if (count($manufacturers) > 0)
                        {
                            $manufacturer_id = $manufacturers ['manufacturer_id'];
                        }
                        else
                        {
                            $manufacturer_id           = 0;
                            $this->view->ticket_error  = true;
                            $this->view->devices_pf_id = $devices_pf_id;
                            $this->view->manufacturer  = ucwords(trim($device_manufacturer));
                        }

                        // populate form
                        $launch_date = new Zend_Date($launch_date, "yyyy-MM-dd HH:ii:ss");

                        // load device values
                        $form->getElement('form_mode')->setValue('save');
                        $form->getElement('hdnID')->setValue($upload_data_collector_id);
                        $form->getElement('hdnItem')->setValue('');
                        $form->getElement('devices_pf_id')->setValue($devices_pf_id);
                        $form->getElement('manufacturer_id')->setValue($manufacturer_id);
                        $form->getElement('printer_model')->setValue(null);
                        $form->getElement('new_printer')->setValue($printer_model);
                        $form->getElement('launch_date')->setValue($launch_date->toString('MM/dd/yyyy'));
                        $form->getElement('device_price')->setValue($device_price);
                        $form->getElement('toner_config_id')->setValue($toner_config);
                        $form->getElement('is_copier')->setAttrib('checked', ($is_copier));
                        $form->getElement('is_fax')->setAttrib('checked', ($is_fax));
                        $form->getElement('is_scanner')->setAttrib('checked', ($is_scanner));
                        $form->getElement('is_duplex')->setAttrib('checked', ($is_duplex));
                        $form->getElement('ppm_black')->setValue($ppm_black);
                        $form->getElement('ppm_color')->setValue($ppm_color);
                        $form->getElement('duty_cycle')->setValue($duty_cycle);
                        $form->getElement('watts_power_normal')->setValue($watts_power_normal);
                        $form->getElement('watts_power_idle')->setValue($watts_power_idle);
                        $form->getElement('is_leased')->setAttrib('checked', ($is_leased));
                        $form->getElement('leased_toner_yield')->setValue($leased_toner_yield);
                    }
                }
            }
            else if ($form->isValid($formData))
            {
                $date = date('Y-m-d H:i:s T');

                // validate fields
                if ($formData ["manufacturer_id"] == 0)
                {
                    $repop_form          = 1;
                    $this->_helper->flashMessenger(array (
                                                         'error' => 'Error: You must select a manufacturer.'
                                                   ));
                }
                else if (trim($formData ["new_printer"]) == "")
                {
                    $repop_form          = 1;
                    $this->_helper->flashMessenger(array (
                                                         'error' => 'Error: You must enter a printer model name.'
                                                   ));
                }
                else if ($formData ["toner_config_id"] == 0)
                {
                    $repop_form          = 1;
                    $this->_helper->flashMessenger(array (
                                                         'error' => 'Error: Toner Config not selected. Please try again.'
                                                   ));
                }
                else if ($formData ["watts_power_normal"] < 1)
                {
                    $repop_form          = 1;
                    $this->_helper->flashMessenger(array (
                                                         'error' => 'Error: Power Consumption Normal must be greater then zero.'
                                                   ));
                }
                else if ($formData ["watts_power_idle"] < 1)
                {
                    $repop_form          = 1;
                    $this->_helper->flashMessenger(array (
                                                         'error' => 'Error: Power Consumption Idle must be greater then zero.'
                                                   ));
                }
                else
                {
                    if ($formData ['save_flag'] == "save")
                    {
                        // update the selected device
                        $db->beginTransaction();
                        try
                        {
                            $master_device_id = 0;
                            if ($formData ['hdnItem'] == 'master')
                            {
                                $master_device_id = $formData ['hdnID'];
                            }
                            else
                            {
                                $upload_data_collector_id = $formData ['hdnID'];
                            }
                            $master_deviceTable = new Proposalgen_Model_DbTable_MasterDevice();

                            // validate toners against toner_config
                            $has_toner    = false;
                            $has_black    = false;
                            $has_yellow   = false;
                            $has_magenta  = false;
                            $has_cyan     = false;
                            $has_3color   = false;
                            $has_4color   = false;
                            $toners_valid = false;

                            $toner_config_id = $formData ['toner_config_id'];
                            $toner_array     = explode(",", $formData ["toner_array"]);

                            foreach ($toner_array as $key)
                            {
                                $toner_id = str_replace("'", "", $key);

                                if ($toner_id > 0)
                                {
                                    // get color and type from $key
                                    $select   = new Zend_Db_Select($db);
                                    $select   = $db->select()
                                        ->from(array(
                                                    't' => 'toner'
                                               ))
                                        ->join(array(
                                                    'tc' => 'toner_color'
                                               ), 'tc.toner_color_id = t.tonerColorId')
                                        ->join(array(
                                                    'pt' => 'part_type'
                                               ), 'pt.part_type_id = t.partTypeId')
                                        ->where('t.toner_id = ?', $toner_id);
                                    $stmt     = $db->query($select);
                                    $curToner = $stmt->fetchAll();
                                    if (count($curToner) > 0)
                                    {
                                        $has_toner = true;
                                        $curColor  = strtolower($curToner [0] ['toner_color_name']);
                                        $curType   = strtolower($curToner [0] ['type_name']);
                                        if ($curColor == "black")
                                        {
                                            $has_black = true;
                                        }
                                        else if ($curColor == "yellow")
                                        {
                                            $has_yellow = true;
                                        }
                                        else if ($curColor == "magenta")
                                        {
                                            $has_magenta = true;
                                        }
                                        else if ($curColor == "cyan")
                                        {
                                            $has_cyan = true;
                                        }
                                        else if ($curColor == "3 color")
                                        {
                                            $has_3color = true;
                                        }
                                        else if ($curColor == "4 color")
                                        {
                                            $has_4color = true;
                                        }
                                    }
                                }
                            }

                            $toner_errors       = "";
                            $toner_error_colors = "";
                            if ($has_toner)
                            {
                                // Has toners, validate to make sure they match
                                // the device
                                switch ($toner_config_id)
                                {
                                    case "1" :
                                        // BLACK ONLY
                                        if ($has_3color || $has_4color || $has_cyan || $has_magenta || $has_yellow)
                                        {
                                            $repop_form   = 1;
                                            $toners_valid = false;
                                            $toner_errors = "Error: You are trying to add invalid toners to this printer. Only Black Toners are allowed.";
                                        }
                                        else if ($has_black)
                                        {
                                            $toners_valid = true;
                                        }
                                        else
                                        {
                                            $repop_form   = 1;
                                            $toner_errors = "Error: Missing a Black Toner. Please add one and try again.";
                                        }
                                        break;
                                    case "2" :
                                        // 3 COLOR - SEPARATED
                                        if ($has_3color || $has_4color)
                                        {
                                            $repop_form   = 1;
                                            $toners_valid = false;
                                            $toner_errors = "Error: You are trying to add invalid toners to this printer. Only Black, Yellow, Magenta and Cyan Toners are allowed.";
                                        }
                                        else if ($has_black)
                                        {
                                            if ($has_yellow)
                                            {
                                                if ($has_magenta)
                                                {
                                                    if ($has_cyan)
                                                    {
                                                        $toners_valid = true;
                                                    }
                                                    else
                                                    {
                                                        $toner_error_colors = "Cyan";
                                                    }
                                                }
                                                else
                                                {
                                                    if (!empty($toner_error_colors))
                                                    {
                                                        $toner_error_colors .= ", ";
                                                    }
                                                    $toner_error_colors = "Magenta";
                                                }
                                            }
                                            else
                                            {
                                                if (!empty($toner_error_colors))
                                                {
                                                    $toner_error_colors .= ", ";
                                                }
                                                $toner_error_colors = "Yellow";
                                            }
                                        }
                                        else
                                        {
                                            if (!empty($toner_error_colors))
                                            {
                                                $toner_error_colors .= ", ";
                                            }
                                            $toner_error_colors = "Black";
                                        }

                                        if ($toner_error_colors != '')
                                        {
                                            $repop_form   = 1;
                                            $toner_errors = "Error: Missing a " . $toner_error_colors . " Toner. Please add one and try again.";
                                        }
                                        break;
                                    case "3" :
                                        // 3 COLOR - COMBINED
                                        if ($has_4color || $has_cyan || $has_magenta || $has_yellow)
                                        {
                                            $repop_form   = 1;
                                            $toners_valid = false;
                                            $toner_errors = "Error: You are trying to add invalid toners to this printer. Only 3 Color and Black Toners are allowed.";
                                        }
                                        else if ($has_black)
                                        {
                                            if ($has_3color)
                                            {
                                                $toners_valid = true;
                                            }
                                            else
                                            {
                                                $toner_error_colors = "3 Color";
                                            }
                                        }
                                        else
                                        {
                                            if (!empty($toner_error_colors))
                                            {
                                                $toner_error_colors .= ", ";
                                            }
                                            $toner_error_colors = "Black";
                                        }

                                        if ($toner_error_colors != '')
                                        {
                                            $repop_form   = 1;
                                            $toner_errors = "Error: Missing a " . $toner_error_colors . " Toner. Please add one and try again.";
                                        }
                                        break;
                                    case "4" :
                                        // 4 COLOR - COMBINED
                                        if ($has_3color || $has_black || $has_cyan || $has_magenta || $has_yellow)
                                        {
                                            $repop_form   = 1;
                                            $toners_valid = false;
                                            $toner_errors = "Error: You are trying to add invalid toners to this printer. Only 4 Color Toners are allowed.";
                                        }
                                        else if ($has_4color)
                                        {
                                            $toners_valid = true;
                                        }
                                        else
                                        {
                                            $repop_form   = 1;
                                            $toner_errors = "Error: Missing a 4 Color Toner. Please add one and try again.";
                                        }
                                        break;
                                }
                            }
                            else
                            {
                                // if leased, then toners not required
                                if ($formData ["is_leased"])
                                {
                                    $toners_valid = true;
                                }
                                else
                                {
                                    $toners_valid = false;
                                    $toner_errors = "Error: You must add required toners before saving this device.";
                                }
                            }

                            if ($toners_valid)
                            {
                                $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();
                                $launch_date       = new Zend_Date($formData ["launch_date"]);

                                // save master device
                                $master_deviceData = array(
                                    'launch_date'        => $launch_date->toString('yyyy-MM-dd HH:mm:ss'),
                                    'toner_config_id'    => $toner_config_id,
                                    'is_copier'          => $formData ["is_copier"],
                                    'is_scanner'         => $formData ["is_scanner"],
                                    'is_fax'             => $formData ["is_fax"],
                                    'is_duplex'          => $formData ["is_duplex"],
                                    'watts_power_normal' => $formData ["watts_power_normal"],
                                    'watts_power_idle'   => $formData ["watts_power_idle"],
                                    'device_price'       => ($formData ["device_price"] == 0 ? null : $formData ["device_price"]),
                                    'ppm_black'          => $formData ["ppm_black"],
                                    'ppm_color'          => $formData ["ppm_color"],
                                    'duty_cycle'         => $formData ["duty_cycle"],
                                    'is_leased'          => $formData ["is_leased"],
                                    'leased_toner_yield' => ($formData ["is_leased"] ? $formData ["leased_toner_yield"] : null)
                                );

                                if ($master_device_id > 0 && $formData ['hdnItem'] == 'master')
                                {
                                    // get printer_model
                                    $where         = $master_deviceTable->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                                    $master_device = $master_deviceTable->fetchRow($where);
                                    $printer_model = $master_device ['printer_model'];

                                    // edit device
                                    $master_deviceTable->update($master_deviceData, $where);

                                    // remove all device_toners for master
                                    // device
                                    $where = $device_tonerTable->getAdapter()->quoteInto('master_device_id = ?', $master_device_id, 'INTEGER');
                                    $device_tonerTable->delete($where);

                                    // save new toners
                                    if ($has_toner)
                                    {
                                        foreach ($toner_array as $key)
                                        {
                                            $toner_id = str_replace("'", "", $key);
                                            if ($toner_id > 0)
                                            {
                                                $device_tonerData = array(
                                                    'toner_id'         => $toner_id,
                                                    'master_device_id' => $master_device_id
                                                );
                                                $device_tonerTable->insert($device_tonerData);
                                            }
                                        }
                                    }
                                    $repop_form                = 1;
                                    $this->view->printer_model = $master_device_id;
                                    $this->_helper->flashMessenger(array (
                                                                         'error' => 'Device "' . $printer_model . '" has been updated.'
                                                                   ));

                                    // set selected printer model to new printer
                                    // model
                                    $this->view->printer_model = $master_device_id;
                                }
                                else
                                {
                                    // get printer_model
                                    $manufacturer_id = $formData ['manufacturer_id'];

                                    if ($manufacturer_id > 0)
                                    {
                                        // add creation_date to array
                                        $master_deviceData ["mastdevice_manufacturer"] = $manufacturer_id;
                                        $master_deviceData ["printer_model"]           = $formData ["new_printer"];
                                        $master_deviceData ['date_created']            = $date;

                                        // check for master device
                                        $where                 = $master_deviceTable->getAdapter()->quoteInto('mastdevice_manufacturer = ' . $manufacturer_id . ' AND printer_model = ?', $formData ["new_printer"]);
                                        $master_device_flagged = $master_deviceTable->fetchRow($where);

                                        if (count($master_device_flagged) > 0)
                                        {
                                            $master_device_id    = $master_device_flagged ['master_device_id'];
                                            $where               = $master_deviceTable->getAdapter()->quoteInto('master_device_id = ?' . $master_device_id, 'INTEGER');
                                            $this->_helper->flashMessenger(array (
                                                                                 'error' => "The printer you're trying to add already exists."
                                                                           ));
                                        }
                                        else
                                        {
                                            $master_device_id    = $master_deviceTable->insert($master_deviceData);
                                            $this->_helper->flashMessenger(array (
                                                                                 'success' => 'Printer "' . $formData ["new_printer"] . '" has been saved.'
                                                                           ));

                                            // save toners
                                            if ($has_toner)
                                            {
                                                foreach ($toner_array as $key)
                                                {
                                                    $toner_id = str_replace("'", "", $key);
                                                    if ($toner_id > 0)
                                                    {
                                                        $device_tonerData = array(
                                                            'toner_id'         => $toner_id,
                                                            'master_device_id' => $master_device_id
                                                        );
                                                        $where            = $device_tonerTable->getAdapter()->quoteInto('toner_id = ' . $toner_id . ' AND master_device_id = ?', $master_device_id, 'INTEGER');
                                                        $device_toners    = $device_tonerTable->fetchRow($where);
                                                        if (count($device_toners) == 0)
                                                        {
                                                            $device_tonerTable->insert($device_tonerData);
                                                        }
                                                    }
                                                }
                                            }

                                            $devices_pf_id              = $formData ["devices_pf_id"];
                                            $unknown_device_instance_id = $formData ["unknown_device_instance_id"];

                                            // update master_matchup_pf
                                            $master_matchup_pfTable = new Proposalgen_Model_DbTable_PFMasterMatchup();
                                            $master_matchup_pfData  = array(
                                                'master_device_id' => $master_device_id
                                            );
                                            $where                  = $master_matchup_pfTable->getAdapter()->quoteInto('devices_pf_id = ?', $devices_pf_id, 'INTEGER');
                                            $master_matchup_pf      = $master_matchup_pfTable->fetchAll($where);

                                            if (count($master_matchup_pf) == 0)
                                            {
                                                $master_matchup_pfData ['devices_pf_id'] = $devices_pf_id;
                                                $master_matchup_pfTable->insert($master_matchup_pfData);
                                            }
                                            else
                                            {
                                                $master_matchup_pfTable->update($master_matchup_pfData, $where);
                                            }

                                            // reset excluded flags
                                            $upload_data_collectorTable = new Proposalgen_Model_DbTable_UploadDataCollector();
                                            $upload_data_collectorData  = array(
                                                'is_excluded' => 0
                                            );
                                            $where                      = $upload_data_collectorTable->getAdapter()->quoteInto('upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                                            $upload_data_collectorTable->update($upload_data_collectorData, $where);

                                            $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                                            $unknown_device_instanceData  = array(
                                                'is_excluded' => 0
                                            );
                                            $where                        = $unknown_device_instanceTable->getAdapter()->quoteInto('upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                                            $unknown_device_instanceTable->update($unknown_device_instanceData, $where);

                                            $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
                                            $device_instanceData  = array(
                                                'is_excluded' => 0
                                            );
                                            $where                = $device_instanceTable->getAdapter()->quoteInto('upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                                            $device_instanceTable->update($device_instanceData, $where);
                                        }
                                        $repop_form                = 1;
                                        $this->view->printer_model = $master_device_id;
                                    }
                                    else
                                    {
                                        $this->_helper->flashMessenger(array (
                                                                             'error' => 'No manufacturer has been selected.'
                                                                       ));
                                    }
                                }
                                $db->commit();
                            }
                            else
                            {
                                // invalid toners - return message
                                $db->rollback();
                                $repop_form          = 1;
                                $this->_helper->flashMessenger(array (
                                                                     'error' => $toner_errors
                                                               ));
                            }
                        }
                        catch (Zend_Db_Exception $e)
                        {
                            $db->rollback();
                            $this->_helper->flashMessenger(array (
                                                                 'error' => 'Database Error: "' . $formData ["new_printer"] . '" could not be saved. Make sure the printer does not already exist'
                                                           ));
                            //echo $e; die;
                        }
                        catch (Exception $e)
                        {
                            // CRITICAL UPDATE EXCEPTION
                            $db->rollback();
                            Throw new exception("Critical Device Update Error.", 0, $e);
                        } // end catch
                    }
                }
            }
            else
            {
                $db->rollback();
                $repop_form = 1;
            }

            if ($repop_form == 1)
            {
                $this->view->repop = true;

                $form->getElement('hdnID')->setValue($formData ['hdnID']);
                $form->getElement('hdnItem')->setValue($formData ['hdnItem']);
                $form->getElement('devices_pf_id')->setValue($formData ['devices_pf_id']);
                $form->getElement('unknown_device_instance_id')->setValue($formData ['unknown_device_instance_id']);
                $form->getElement('save_flag')->setValue($formData ['save_flag']);
                $form->getElement('toner_array')->setValue($formData ['toner_array']);
                $form->getElement('form_mode')->setValue($formData ['form_mode']);

                $form->getElement('manufacturer_id')->setValue($formData ['manufacturer_id']);

                if (isset($formData ['printer_model']))
                {
                    $form->getElement('printer_model')->setValue($formData ['printer_model']);
                }
                $form->getElement('new_printer')->setValue($formData ['new_printer']);

                $form->getElement('launch_date')->setValue($formData ['launch_date']);
                $form->getElement('device_price')->setValue($formData ['device_price']);
                $form->getElement('toner_config_id')->setValue($formData ['toner_config_id']);
                $form->getElement('is_copier')->setAttrib('checked', $formData ['is_copier']);
                $form->getElement('is_scanner')->setAttrib('checked', $formData ['is_scanner']);
                $form->getElement('is_fax')->setAttrib('checked', $formData ['is_fax']);
                $form->getElement('is_duplex')->setAttrib('checked', $formData ['is_duplex']);
                $form->getElement('watts_power_normal')->setValue($formData ['watts_power_normal']);
                $form->getElement('watts_power_idle')->setValue($formData ['watts_power_idle']);

                $form->getElement('is_leased')->setAttrib('checked', $formData ['is_leased']);
                $form->getElement('leased_toner_yield')->setValue($formData ['leased_toner_yield']);

                $form->getElement('ppm_black')->setValue($formData ['ppm_black']);
                $form->getElement('ppm_color')->setValue($formData ['ppm_color']);
                $form->getElement('duty_cycle')->setValue($formData ['duty_cycle']);
            }
        }
        $this->view->deviceform = $form;
    }
}
