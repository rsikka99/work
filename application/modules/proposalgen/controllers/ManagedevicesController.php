<?php
class Proposalgen_ManagedevicesController extends Tangent_Controller_Action
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
        $isPrintModelSet = false;

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
                $this->_helper->flashMessenger(array(
                                                    'error' => 'You must select a manufacturer.'
                                               ));
                $repop_form = 1;
            }
            else if ($form_mode == "edit" && $formData ["printer_model"] == 0)
            {
                $this->_helper->flashMessenger(array(
                                                    'error' => 'You must select a printer model.'
                                               ));
                $repop_form = 1;
            }
            else if ($form_mode == "add" && trim($formData ["new_printer"]) == "")
            {
                $this->_helper->flashMessenger(array(
                                                    'error' => 'You must enter a printer model name.'
                                               ));
                $repop_form = 1;
            }
            else if ($formData ["toner_config_id"] == 0)
            {
                $this->_helper->flashMessenger(array(
                                                    'error' => 'Toner Config not selected. Please try again.'
                                               ));
                $repop_form = 1;
            }
            else if ($formData ["watts_power_normal"] < 1)
            {
                $this->_helper->flashMessenger(array(
                                                    'error' => 'Power Consumption Normal must be greater then zero.'
                                               ));
                $repop_form = 1;
            }
            else if ($formData ["watts_power_idle"] < 1)
            {
                $this->_helper->flashMessenger(array(
                                                    'error' => 'Power Consumption Idle must be greater then zero.'
                                               ));
                $repop_form = 1;
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
                                'launchDate'         => $launch_date->toString('yyyy-MM-dd HH:mm:ss'),
                                'tonerConfigId'      => $toner_config_id,
                                'isCopier'           => $formData ["is_copier"],
                                'isScanner'          => $formData ["is_scanner"],
                                'reportsTonerLevels' => $formData ["reportsTonerLevels"],
                                'isFax'              => $formData ["is_fax"],
                                'isDuplex'           => $formData ["is_duplex"],
                                'wattsPowerNormal'   => $formData ["watts_power_normal"],
                                'wattsPowerIdle'     => $formData ["watts_power_idle"],
                                'cost'               => ($formData ["device_price"] == 0 ? null : $formData ["device_price"]),
                                'ppmBlack'           => ($formData ["ppm_black"] > 0) ? $formData ["ppm_black"] : null,
                                'ppmColor'           => ($formData ["ppm_color"] > 0) ? $formData ["ppm_color"] : null,
                                'dutyCycle'          => ($formData ["duty_cycle"] > 0) ? $formData ["duty_cycle"] : null,
                                'isLeased'           => $formData ["is_leased"],
                                'leasedTonerYield'   => ($formData ["is_leased"] ? $formData ["leased_toner_yield"] : null)
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
                                $this->_helper->flashMessenger(array(
                                                                    'success' => 'Device "' . $printer_model . '" has been updated.'
                                                               ));
                                $isPrintModelSet = true;

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
                                        $this->_helper->flashMessenger(array(
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
                                        $this->_helper->flashMessenger(array(
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
                                    $this->_helper->flashMessenger(array(
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
                            $this->_helper->flashMessenger(array(
                                                                'error' => $toner_errors
                                                           ));
                        }
                    }
                    catch (Zend_Db_Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array(
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
                            $replacement_devicesTable = new Proposalgen_Model_DbTable_ReplacementDevice();
                            $where                    = $replacement_devicesTable->getAdapter()->quoteInto('masterDeviceId = ?', $master_device_id, 'INTEGER');
                            $replacement_devices      = $replacement_devicesTable->fetchAll($where);

                            if (count($replacement_devices) > 0)
                            {
                                $db->rollback();
                                $this->_helper->flashMessenger(array(
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
                                $this->_helper->flashMessenger(array(
                                                                    'success' => "Printer " . $printer_model . " has been deleted."
                                                               ));
                            }
                        }
                        else
                        {
                            $db->rollback();
                            $this->_helper->flashMessenger(array(
                                                                'error' => "No printer was selected. Please select a printer and try again."
                                                           ));
                        }
                    }
                    catch (Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array(
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
                $form->getElement('hdnID')->setValue($formData ['hdnID']);
                $form->getElement('hdnItem')->setValue($formData ['hdnItem']);
                $form->getElement('devices_pf_id')->setValue($formData ['devices_pf_id']);
                $form->getElement('unknown_device_instance_id')->setValue($formData ['unknown_device_instance_id']);
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
                $form->getElement('reportsTonerLevels')->setAttrib('checked', $formData ['reportsTonerLevels']);
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

    public function managemappingdevicesAction ()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/libs/jqgrid/plugins/grid.celledit.js'), 'text/javascript');
        $db                = Zend_Db_Table::getDefaultAdapter();
        $deviceInstanceIds = $this->_getParam('deviceInstanceIds', false);

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
        $isPrintModelSet = false;

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
            if (!isset($formData['hdnID']))
            {
                $repop_form = 1;
                $form_mode  = 'edit';
                $form->getElement('deviceInstanceId')->setValue($deviceInstanceIds);
            }
            else
            {
                // conditional requirements
                $form->set_validation($formData);
                // get form mode
                $form_mode = $formData ['form_mode'];
                $date      = date('Y-m-d H:i:s');
                // validate fields
                if ($formData ["manufacturer_id"] == 0)
                {
                    $this->_helper->flashMessenger(array(
                                                        'error' => 'You must select a manufacturer.'
                                                   ));
                    $repop_form = 1;
                }
                else if ($form_mode == "edit" && $formData ["printer_model"] == 0)
                {
                    $this->_helper->flashMessenger(array(
                                                        'error' => 'You must select a printer model.'
                                                   ));
                    $repop_form = 1;
                }
                else if ($form_mode == "add" && trim($formData ["new_printer"]) == "")
                {
                    $this->_helper->flashMessenger(array(
                                                        'error' => 'You must enter a printer model name.'
                                                   ));
                    $repop_form = 1;
                }
                else if ($formData ["toner_config_id"] == 0)
                {
                    $this->_helper->flashMessenger(array(
                                                        'error' => 'Toner Config not selected. Please try again.'
                                                   ));
                    $repop_form = 1;
                }
                else if ($formData ["watts_power_normal"] < 1)
                {
                    $this->_helper->flashMessenger(array(
                                                        'error' => 'Power Consumption Normal must be greater then zero.'
                                                   ));
                    $repop_form = 1;
                }
                else if ($formData ["watts_power_idle"] < 1)
                {
                    $this->_helper->flashMessenger(array(
                                                        'error' => 'Power Consumption Idle must be greater then zero.'
                                                   ));
                    $repop_form = 1;

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
                                    'launchDate'         => $launch_date->toString('yyyy-MM-dd HH:mm:ss'),
                                    'tonerConfigId'      => $toner_config_id,
                                    'isCopier'           => $formData ["is_copier"],
                                    'isScanner'          => $formData ["is_scanner"],
                                    'reportsTonerLevels' => $formData ["reportsTonerLevels"],
                                    'isFax'              => $formData ["is_fax"],
                                    'isDuplex'           => $formData ["is_duplex"],
                                    'wattsPowerNormal'   => $formData ["watts_power_normal"],
                                    'wattsPowerIdle'     => $formData ["watts_power_idle"],
                                    'cost'               => ($formData ["device_price"] == 0 ? null : $formData ["device_price"]),
                                    'ppmBlack'           => ($formData ["ppm_black"] > 0) ? $formData ["ppm_black"] : null,
                                    'ppmColor'           => ($formData ["ppm_color"] > 0) ? $formData ["ppm_color"] : null,
                                    'dutyCycle'          => ($formData ["duty_cycle"] > 0) ? $formData ["duty_cycle"] : null,
                                    'isLeased'           => $formData ["is_leased"],
                                    'leasedTonerYield'   => ($formData ["is_leased"] ? $formData ["leased_toner_yield"] : null)
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
                                    $this->_helper->flashMessenger(array(
                                                                        'success' => 'Device "' . $printer_model . '" has been updated.'
                                                                   ));
                                    $isPrintModelSet = true;

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
                                            $this->_helper->flashMessenger(array(
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

                                            // If we have device instance id's, insert them here.
                                            if ($formData['deviceInstanceId'])
                                            {
                                                // Explode the id's into an array
                                                $deviceSplitIds = explode(',', $formData['deviceInstanceId']);

                                                $deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceSplitIds[0]);

                                                // If we have a model id then we can add a matchup for it
                                                if ($deviceInstance->getRmsUploadRow()->rmsModelId)
                                                {
                                                    $rmsMasterMatchUp = Proposalgen_Model_Mapper_Rms_Master_Matchup::getInstance()->find(array($deviceInstance->getRmsUploadRow()->rmsProviderId, $deviceInstance->getRmsUploadRow()->rmsModelId));
                                                    if (!$rmsMasterMatchUp instanceof Proposalgen_Model_Rms_Master_Matchup)
                                                    {
                                                        /**
                                                         * We only add a matchup if it did not exist before.
                                                         */
                                                        $rmsMasterMatchUp                 = new Proposalgen_Model_Rms_Master_Matchup();
                                                        $rmsMasterMatchUp->rmsProviderId  = $deviceInstance->getRmsUploadRow()->rmsProviderId;
                                                        $rmsMasterMatchUp->rmsModelId     = $deviceInstance->getRmsUploadRow()->rmsModelId;
                                                        $rmsMasterMatchUp->masterDeviceId = $master_device_id;
                                                        Proposalgen_Model_Mapper_Rms_Master_Matchup::getInstance()->insert($rmsMasterMatchUp);
                                                    }
                                                }


                                                foreach ($deviceSplitIds as $deviceInstanceId)
                                                {
                                                    $deviceInstance                               = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceInstanceId);
                                                    $deviceInstanceMasterDevice                   = new Proposalgen_Model_Device_Instance_Master_Device();
                                                    $deviceInstanceMasterDevice->deviceInstanceId = $deviceInstance->id;
                                                    $deviceInstanceMasterDevice->masterDeviceId   = $master_device_id;
                                                    Proposalgen_Model_Mapper_Device_Instance_Master_Device::getInstance()->insert($deviceInstanceMasterDevice);
                                                }
                                            }


                                        }
                                        $repop_form                = 1;
                                        $this->view->printer_model = $master_device_id;
                                    }
                                    else
                                    {
                                        $this->_helper->flashMessenger(array(
                                                                            'error' => 'Error: No manufacturer has been selected.'
                                                                       ));
                                    }
                                }
                                $db->commit();
                                $this->_helper->_redirector('mapping', 'fleet');
                            }
                            else
                            {
                                // invalid toners - return message
                                $db->rollback();
                                $repop_form                = 1;
                                $this->view->printer_model = $formData ['printer_model'];
                                $this->_helper->flashMessenger(array(
                                                                    'error' => $toner_errors
                                                               ));
                            }
                        }
                        catch (Zend_Db_Exception $e)
                        {
                            $db->rollback();
                            $this->_helper->flashMessenger(array(
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
                }
            }
            /**
             *  If the form mode is re-populate trigger the loading of the form based on action desired
             */
            if ($repop_form == 1)
            {
                $this->view->repop = true;
                if ($deviceInstanceIds)
                {
                    /**
                     * If there are device instance that have been passed
                     */
                    $deviceInstanceSplit = explode(',', $deviceInstanceIds);
                    $deviceInstance      = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceInstanceSplit[0]);
                    if ($deviceInstance->getIsMappedToMasterDevice())
                    {
                        /**
                         *  Editing a master device
                         */
                        $deviceInstanceMasterDevice = Proposalgen_Model_Mapper_Device_Instance_Master_Device::getInstance()->find($deviceInstanceSplit[0]);
                        /* @var $masterDevice Proposalgen_Model_MasterDevice */
                        $masterDevice                      = $deviceInstanceMasterDevice->getMasterDevice();
                        $deviceData                        = array();
                        $deviceData ['manufacturer_id']    = $masterDevice->getManufacturer()->id;
                        $deviceData ['masterDeviceId']     = $masterDevice->id;
                        $deviceData ['launch_date']        = $masterDevice->launchDate;
                        $deviceData ['toner_config_id']    = $masterDevice->tonerConfigId;
                        $deviceData ['device_price']       = $masterDevice->cost;
                        $deviceData ['is_copier']          = $masterDevice->isCopier;
                        $deviceData ['is_scanner']         = $masterDevice->isScanner;
                        $deviceData ['reportsTonerLevels'] = $masterDevice->reportsTonerLevels;
                        $deviceData ['is_fax']             = $masterDevice->isFax;
                        $deviceData ['is_duplex']          = $masterDevice->isDuplex;
                        $deviceData ['watts_power_normal'] = $masterDevice->wattsPowerNormal;
                        $deviceData ['watts_power_idle']   = $masterDevice->wattsPowerIdle;
                        $deviceData ['is_leased']          = $masterDevice->isLeased;
                        $deviceData ['leased_toner_yield'] = $masterDevice->leasedTonerYield;
                        $deviceData ['ppm_black']          = $masterDevice->ppmBlack;
                        $deviceData ['ppm_color']          = $masterDevice->ppmColor;
                        $deviceData ['duty_cycle']         = $masterDevice->dutyCycle;
                        $this->view->printer_model         = $deviceData ['masterDeviceId'];
                        $this->view->adminEdit             = true;
                        $form_mode                         = 'edit';
                    }
                    else
                    {
                        /**
                         * Create a new master device
                         */
                        $rmsRow                            = $deviceInstance->getRmsUploadRow();
                        $deviceData ['manufacturer_id']    = $rmsRow->manufacturerId;
                        $deviceData ['launch_date']        = $rmsRow->launchDate;
                        $deviceData ['toner_config_id']    = $rmsRow->tonerConfigId;
                        $deviceData ['device_price']       = $rmsRow->cost;
                        $deviceData ['is_copier']          = $rmsRow->isCopier;
                        $deviceData ['is_scanner']         = $rmsRow->isScanner;
                        $deviceData ['reportsTonerLevels'] = $deviceInstance->reportsTonerLevels;
                        $deviceData ['is_fax']             = $rmsRow->isFax;
                        $deviceData ['is_duplex']          = $rmsRow->isDuplex;
                        $deviceData ['watts_power_normal'] = $rmsRow->wattsPowerNormal;
                        $deviceData ['watts_power_idle']   = $rmsRow->wattsPowerIdle;
                        $deviceData ['is_leased']          = $rmsRow->isLeased;
                        $deviceData ['leased_toner_yield'] = $rmsRow->leasedTonerYield;
                        $deviceData ['ppm_black']          = $rmsRow->ppmBlack;
                        $deviceData ['ppm_color']          = $rmsRow->ppmColor;
                        $deviceData ['duty_cycle']         = $rmsRow->dutyCycle;
                        $form->getElement('new_printer')->setValue($rmsRow->modelName);
                        $form_mode = 'add';
                    }


                    $form->getElement('form_mode')->setValue($form_mode);
                    $form->getElement('manufacturer_id')->setValue($deviceData ['manufacturer_id']);
                    $form->getElement('launch_date')->setValue($deviceData ['launch_date']);
                    $form->getElement('device_price')->setValue($deviceData ['device_price']);
                    $form->getElement('toner_config_id')->setValue($deviceData ['toner_config_id']);
                    $form->getElement('is_copier')->setAttrib('checked', $deviceData ['is_copier']);
                    $form->getElement('is_scanner')->setAttrib('checked', $deviceData ['is_scanner']);
                    $form->getElement('reportsTonerLevels')->setAttrib('checked', $deviceData ['reportsTonerLevels']);
                    $form->getElement('is_fax')->setAttrib('checked', $deviceData ['is_fax']);
                    $form->getElement('is_duplex')->setAttrib('checked', $deviceData ['is_duplex']);
                    $form->getElement('watts_power_normal')->setValue($deviceData ['watts_power_normal']);
                    $form->getElement('watts_power_idle')->setValue($deviceData ['watts_power_idle']);
                    $form->getElement('is_leased')->setAttrib('checked', $deviceData ['is_leased']);
                    $form->getElement('leased_toner_yield')->setValue($deviceData ['leased_toner_yield']);
                    $form->getElement('ppm_black')->setValue($deviceData ['ppm_black']);
                    $form->getElement('ppm_color')->setValue($deviceData ['ppm_color']);
                    $form->getElement('duty_cycle')->setValue($deviceData ['duty_cycle']);

                }
                else
                {
                    /**
                     *  If validation fails, this will catch it and persist the data to next form load
                     */
                    $form->getElement('hdnID')->setValue($formData ['hdnID']);
                    $form->getElement('hdnItem')->setValue($formData ['hdnItem']);
                    $form->getElement('devices_pf_id')->setValue($formData ['devices_pf_id']);
                    $form->getElement('unknown_device_instance_id')->setValue($formData ['unknown_device_instance_id']);
                    $form->getElement('deviceInstanceId')->setValue($formData['deviceInstanceId']);
                    $form->getElement('save_flag')->setValue($formData ['save_flag']);
                    $form->getElement('toner_array')->setValue($formData ['toner_array']);
                    $form->getElement('form_mode')->setValue($formData ['form_mode']);
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
                    $form->getElement('reportsTonerLevels')->setAttrib('checked', $formData ['reportsTonerLevels']);
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
        }
        $this->view->form_mode  = $form_mode;
        $this->view->deviceform = $form;
    }
}