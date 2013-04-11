<?php
class Proposalgen_AdminController extends Tangent_Controller_Action
{
    /**
     * The application configuration
     *
     * @var Zend_Config
     */
    protected $config;

    protected $ApplicationName;
    protected $MPSProgramName;
    protected $privilege;
    protected $user_id;

    function init ()
    {
        /**
         * FIXME: Hardcoded privilege
         */
        $this->view->privilege = array('System Admin');
        $this->privilege       = array('System Admin');

        $this->config               = Zend_Registry::get('config');
        $this->view->app            = $this->config->app;
        $this->view->user           = Zend_Auth::getInstance()->getIdentity();
        $this->view->user_id        = Zend_Auth::getInstance()->getIdentity()->id;
        $this->user_id              = Zend_Auth::getInstance()->getIdentity()->id;
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
     * The printermodelsAction returns a list of printer_models by manufacturer
     * to populate the dropdowns in json format
     */
    public function printermodelsAction ()
    {
        $manufacturerId      = $this->getParam('manufacturerid', false);
        $jsonResponse        = new stdClass();
        $jsonResponse->rows  = array();
        $master_devicesTable = new Proposalgen_Model_DbTable_MasterDevice();
        $result              = $master_devicesTable->fetchAll(array('manufacturerId = ?' => $manufacturerId), 'modelName');

        if (count($result) > 0)
        {
            foreach ($result as $row)
            {
                $jsonResponse->rows[] = array('id' => $row['id'], 'cell' => array(
                    $row ['id'],
                    ucwords(strtolower($row ['modelName']))

                ));
            }
        }
        $this->sendJson($jsonResponse);
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

                $select      = $db->select()
                    ->from(array(
                                'md' => 'pgen_master_devices'
                           ))
                    ->joinLeft(array(
                                    'm' => 'manufacturers'
                               ), 'm.id = md.manufacturerId')
                    ->joinLeft(array(
                                    'rd' => 'pgen_replacement_devices'
                               ), 'rd.masterDeviceId = md.id')
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
                    'reportsTonerLevels'    => $row [0] ['reportsTonerLevels'] ? true : false,
                    'is_replacement_device' => $row [0] ['isReplacementDevice'],
                    'watts_power_normal'    => $row [0] ['wattsPowerNormal'],
                    'watts_power_idle'      => $row [0] ['wattsPowerIdle'],
                    'device_price'          => ($row [0] ['cost'] > 0 ? (float)$row [0] ['cost'] : ""),
                    'is_deleted'            => $row [0] ['is_deleted'],
                    'toner_array'           => $toner_array,
                    'replacement_category'  => $row [0] ['replacementCategory'],
                    'print_speed'           => $row [0] ['print_speed'],
                    'resolution'            => $row [0] ['resolution'],
                    'monthly_rate'          => $row [0] ['monthly_rate'],
                    'is_leased'             => $row [0] ['isLeased'] ? true : false,
                    'leased_toner_yield'    => $row [0] ['leasedTonerYield'],
                    'ppm_black'             => $row [0] ['ppmBlack'],
                    'ppm_color'             => $row [0] ['ppmColor'],
                    'duty_cycle'            => $row [0] ['dutyCycle'],
                    'partsCostPerPage'      => $row [0] ['partsCostPerPage'],
                    'laborCostPerPage'      => $row [0] ['laborCostPerPage'],
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

        $this->sendJson($formData);
    }

    public function devicereportsAction ()
    {
        $master_device_id = $this->_getParam('id', 0);

        $device_instance_master_devicesTable = new Proposalgen_Model_DbTable_Device_Instance_Master_Device();
        $where                               = $device_instance_master_devicesTable->getAdapter()->quoteInto('masterDeviceId = ?', $master_device_id, 'INTEGER');
        $device_instances                    = $device_instance_master_devicesTable->fetchAll($where);

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
        $this->sendJson($formdata);
    }

    /**
     * @throws exceptionThis action seems to provide json lists for the following:
     * - Manufacturers
     * - Toner Colors
     * - Part Types
     */
    public function filterlistitemsAction ()
    {
        $db       = Zend_Db_Table::getDefaultAdapter();
        $list     = $this->_getParam('list', 'man');
        $formData = new stdClass();

        try
        {
            switch ($list)
            {
                case "man" :
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
                            $formData->rows [$i] ['id']   = $row ['id'];
                            $formData->rows [$i] ['cell'] = array(
                                $row ['id'],
                                ucwords(strtolower($row ['fullname']))
                            );
                            $i++;
                        }
                    }
                    else
                    {
                        // empty form values
                        $formData = array();
                    }
                    break;

                case "color" :
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
                            $formData->rows [$i] ['id']   = $row ['id'];
                            $formData->rows [$i] ['cell'] = array(
                                $row ['id'],
                                ucwords(strtolower($row ['name']))
                            );
                            $i++;
                        }
                    }
                    else
                    {
                        // empty form values
                        $formData = array();
                    }
                    break;

                case "type" :
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
                            $formData->rows [$i] ['id']   = $row ['id'];
                            $formData->rows [$i] ['cell'] = array(
                                $row ['id'],
                                $row ['name']
                            );
                            $i++;
                        }
                    }
                    else
                    {
                        // empty form values
                        $formData = array();
                    }
                    break;
            }
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to build criteria list.", 0, $e);
        }

        $this->sendJson($formData);
    }

    /**
     * The devicetonersAction accepts a parameter for the deviceid and gets the
     * device
     * toners from the database.
     * Returns the parts array in a json encoded format.
     */
    public function devicetonersAction ()
    {
        $gridFormData   = new stdClass();
        $toners         = array();
        $masterDeviceId = $this->_getParam('deviceid', false);
        $tonerArray     = $this->_getParam('list', '');

        /**
         * If we passed a list of toners, it means those are all the toners assigned to a device.
         * Otherwise we'll fetch the toners that are assigned to the device already.
         */
        if ($tonerArray != '')
        {
            $toners = Proposalgen_Model_Mapper_Toner::getInstance()->fetchListOfToners($tonerArray);
        }
        else if ($masterDeviceId !== false)
        {
            $toners = Proposalgen_Model_Mapper_Toner::getInstance()->fetchTonersAssignedToDevice($masterDeviceId);
        }

        if (count($toners) > 0)
        {
            $gridFormData->page    = 1;
            $gridFormData->total   = 1;
            $gridFormData->records = 50;
            $gridFormData->rows    = array();
            foreach ($toners as $row)
            {
                $formDataRow       = new stdClass();
                $formDataRow->id   = $row['id'];
                $formDataRow->cell = array(
                    $row ['id'],
                    $row ['sku'],
                    $row ['manufacturer_name'],
                    Proposalgen_Model_PartType::$PartTypeNames[$row ['partTypeId']],
                    Proposalgen_Model_TonerColor::$ColorNames[$row['tonerColorId']],
                    $row ['yield'],
                    $row ['cost'],
                    $row ['master_device_id'],
                    $row ['master_device_id'],
                    null
                );

                $gridFormData->rows[] = $formDataRow;
            }
        }

        $this->sendJson($gridFormData);
    }

    public function replacementtonersAction ()
    {

        $db       = Zend_Db_Table::getDefaultAdapter();
        $toner_id = $this->_getParam('tonerid', 0);

        $formData = array();
        if ($toner_id > 0)
        {
            $filter   = $this->_getParam('filter', false);
            $criteria = trim($this->_getParam('criteria', false));

            $formData = new stdClass();
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
                    $formData->page    = $page;
                    $formData->total   = $total_pages;
                    $formData->records = $count;
                    foreach ($result as $row)
                    {
                        // Always uppercase OEM, but just captialize everything else
                        $type_name = ucwords(strtolower($row ['type_name']));
                        if ($type_name == "Oem")
                        {
                            $type_name = "OEM";
                        }

                        $formData->rows [$i] ['id']   = $row ['toners_id'];
                        $formData->rows [$i] ['cell'] = array(
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
                    $formData = array();
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
            $formData = array();
        }

        $this->sendJson($formData);
    }

    public function devicetonercountAction ()
    {
        $db       = Zend_Db_Table::getDefaultAdapter();
        $toner_id = $this->_getParam('tonerid', 0);

        $formData = null;
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

            $formData = array(
                'total_count'  => $total_devices_count,
                'device_count' => $num_devices_count
            );
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device count.", 0, $e);
        }

        $this->sendJson($formData);
    }

    public function addtonerAction ()
    {

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

        $this->sendJson(array("message" => $message));
    }

    /**
     */
    public function edittonerAction ()
    {
        // Disable the default layout
        $db = Zend_Db_Table::getDefaultAdapter();

        $tonerTable        = new Proposalgen_Model_DbTable_Toner();
        $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();

        // grab all variables from $_POST
        $id              = $this->_getParam('id', null);
        $toner_id        = $this->_getParam('toner_id', null);
        $toner_sku       = $this->_getParam('toner_sku', null);
        $part_type_id    = $this->_getParam('part_type_id', null);
        $manufacturer_id = $this->_getParam('manufacturer_id', null);
        $toner_color_id  = $this->_getParam('toner_color_id', null);
        $toner_yield     = $this->_getParam('toner_yield', null);
        $toner_price     = $this->_getParam('toner_price', null);
        $operation       = $this->_getParam('oper', null);


        $message = '';

        if ($operation == "del")
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
                    $db->rollback();
                    $message = "An error has occurred and the toner was not updated.";
                }
            }
        }

        $this->sendJson(array("message" => $message));
    }

    public function replacetonerAction ()
    {
        $message     = '';
        $toner_count = 0;
        $db          = Zend_Db_Table::getDefaultAdapter();

        $replace_mode = $this->_getParam('replace_mode', '');
        $replace_id   = $this->_getParam('replace_toner_id', 0);
        $with_id      = $this->_getParam('with_toner_id', 0);
        $apply_all    = $this->_getParam('chkAllToners', 0);

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
                            $device_tonerMapper    = Proposalgen_Model_Mapper_DeviceToner::getInstance();
                            $device_toner          = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchRow('toner_id = ' . $replace_id . ' AND master_device_id = ' . $master_device_id);
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
                                $device_tonerMapper    = Proposalgen_Model_Mapper_DeviceToner::getInstance();
                                $device_toner          = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchRow('toner_id = ' . $replace_id . ' AND master_device_id = ' . $master_device_id);
                                $device_toner->tonerId = $with_id;
                                $device_tonerMapper->save($device_toner);
                                $toner_count += 1;
                            }
                        }
                    }
                    $this->_flashMessenger->addMessage(array(
                                                            "success" => "The toner has been replaced and deleted successfully."
                                                       ));
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

        $this->sendJson(array("message" => $message));
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

        $this->sendJson(array("message" => $message));
    }

    public function searchtonersAction ()
    {
        $db       = Zend_Db_Table::getDefaultAdapter();
        $formData = new stdClass();

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
                    $formData->rows [$i] ['id']   = $row ['toner_id'];
                    $formData->rows [$i] ['cell'] = array(
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
                $formData = array();
            }
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find toners.", 0, $e);
        } // end catch


        // encode user data to return to the client:
        $this->sendJson($formData);
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
                $form->removeElement('manufacturer_displayname');
                $form->removeElement('save_manufacturer');
                $form->removeElement('delete_manufacturer');
                $form->removeElement('back_button');

                $this->view->message = "<h3 style='margin: 20px 0px 0px 0px; border-bottom: 0px;'>Adding Manufacturer... please wait.</h3>";


                $db->beginTransaction();
                try
                {
                    if ($formData ['options'] == "new")
                    {
                        $manufacturer_name        = ucwords(strtolower($formData ["manufacturer_name"]));
                        $manufacturer_displayname = ucwords(strtolower($formData ["manufacturer_displayname"]));
                        $manufacturerData         = array(
                            'fullname'    => $manufacturer_name,
                            'displayname' => $manufacturer_displayname,
                            'isDeleted'   => 0
                        );
                        $where                    = $manufacturersTable->getAdapter()->quoteInto('fullName = ?', $manufacturer_name);
                        $manufacturer             = $manufacturersTable->fetchAll($where);

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
                $manufacturersTable       = new Proposalgen_Model_DbTable_Manufacturer();
                $manufacturer_id          = $formData ['select_manufacturer'];
                $manufacturer_name        = strtoupper($formData ['manufacturer_name']);
                $manufacturer_displayname = strtoupper($formData ['manufacturer_displayname']);
                $db->beginTransaction();
                try
                {

                    if (array_key_exists('save_manufacturer', $formData) && $formData ['save_manufacturer'] == "Save")
                    {

                        $manufacturerData = array(

                            'fullName'    => $manufacturer_name,
                            'displayName' => $manufacturer_displayname,
                        );

                        //Check if fullName is already used
                        $where        = $manufacturersTable->getAdapter()->quoteInto('id <> ' . $manufacturer_id . ' AND fullName = ?', $manufacturer_name);
                        $manufacturer = $manufacturersTable->fetchRow($where);
                        if ($manufacturer_id > 0)
                        {
                            if (count($manufacturer) > 0)
                            {
                                $this->_flashMessenger->addMessage(array(
                                                                        "error" => 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" already exists.'
                                                                   ));
                            }
                            else
                            {
                                $where = $manufacturersTable->getAdapter()->quoteInto('id = ?', $manufacturer_id, 'INTEGER');
                                $manufacturersTable->update($manufacturerData, $where);
                                $this->_flashMessenger->addMessage(array(
                                                                        "success" => 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" Updated'
                                                                   ));
                            }
                        }
                        else
                        {
                            if (count($manufacturer) > 0)
                            {
                                if ($manufacturer ['isDeleted'] == 1)
                                {
                                    $manufacturerData = array(
                                        'displayName' => $manufacturer_displayname,
                                        'isDeleted'   => 0
                                    );
                                    $where            = $manufacturersTable->getAdapter()->quoteInto('id = ?', $manufacturer ['id'], 'INTEGER');
                                    $manufacturersTable->update($manufacturerData, $where);
                                    $this->_flashMessenger->addMessage(array(
                                                                            "success" => 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" Added.'
                                                                       ));
                                }
                                else
                                {
                                    $this->_flashMessenger->addMessage(array(
                                                                            "error" => 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" already exists.'
                                                                       ));
                                }
                            }
                            else
                            {
                                $manufacturersTable->insert($manufacturerData);
                                $this->_flashMessenger->addMessage(array(
                                                                        "success" => 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" Added.'
                                                                   ));
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
                            $where              = $master_deviceTable->getAdapter()->quoteInto('manufacturerId = ?', $manufacturer_id, 'INTEGER');
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
                            $this->_flashMessenger->addMessage(array(
                                                                    "success" => 'Manufacturer "' . ucwords(strtolower($manufacturer_name)) . '" Deleted.'
                                                               ));
                        }
                        else
                        {
                            $this->_flashMessenger->addMessage(array(
                                                                    "error" => "No manufacturer was selected to be deleted."
                                                               ));
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
                    $form->getElement('manufacturer_displayname')->setValue('');
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
        $manufacturerId    = $this->_getParam('manufacturerid', false);
        $manufacturerTable = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturer      = $manufacturerTable->fetchRow(array('id = ?' . $manufacturerId));

        try
        {
            if (count($manufacturer) > 0)
            {
                $formData = array(
                    'manufacturer_name'        => Trim(ucwords(strtolower($manufacturer ['fullname']))),
                    'manufacturer_displayname' => Trim(ucwords(strtolower($manufacturer ['displayname']))),
                    'is_deleted'               => ($manufacturer ['isDeleted'] == 1 ? true : false)
                );
            }
            else
            {
                // empty form values
                $formData = array(
                    'manufacturer_name'        => '',
                    'manufacturer_displayname' => '',
                    'is_deleted'               => false
                );
            }
        }
        catch (Exception $e)
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find manufacturer.", 0, $e);
        } // end catch


        $this->sendJson($formData);
    }

    /**
     * The uploadpricingAction allows the system admin or dealer to select a .csv file with pricing
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
                        // create an associative array of the csv information
                        $finalDevices = array();
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

    public function bulkdevicepricingAction ()
    {
        $this->view->title       = "Update Pricing";
        $this->view->parts_list  = array();
        $this->view->device_list = array();
        $db                      = Zend_Db_Table::getDefaultAdapter();

        $dealer         = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $reportSettings = $dealer->getReportSettings();

        $this->view->default_labor = $reportSettings['laborCostPerPage'];
        $this->view->default_parts = $reportSettings['partsCostPerPage'];

        // fill manufacturers dropdown
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('isDeleted = false', 'fullName');
        $this->view->manufacturer_list = $manufacturers;

        if ($this->_request->isPost())
        {
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
                                    $passvalid = 1;
                                    $this->_flashMessenger->addMessage(array(
                                                                            "error" => "All values must be greater than 0. Please correct it and try again."
                                                                       ));
                                    break;
                                }
                                else if ($price != '' && !is_numeric($price))
                                {
                                    $passvalid = 1;
                                    $this->_flashMessenger->addMessage(array(
                                                                            "error" => "All values must be numeric. Please correct it and try again."
                                                                       ));
                                    break;
                                }
                                else if ($price != '')
                                {
                                    $tonerAttribute = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->findTonerAttributeByTonerId($toner_id, Zend_Auth::getInstance()->getIdentity()->dealerId);
                                    if ($tonerAttribute)
                                    {
                                        $tonerAttribute->cost = $price;
                                        Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->save($tonerAttribute);
                                    }
                                    else
                                    {

                                        $tonerAttribute           = new Proposalgen_Model_Dealer_Toner_Attribute();
                                        $tonerAttribute->dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
                                        $tonerAttribute->tonerId  = $toner_id;
                                        $tonerAttribute->cost     = $price;
                                        Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->insert($tonerAttribute);

                                    }
                                }
                            }
                        }

                        if ($passvalid == 0)
                        {
                            $this->_flashMessenger->addMessage(array(
                                                                    "success" => "The toner pricing updates have been applied successfully."
                                                               ));
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
                        /* @var $dealerMasterDeviceAttribute Proposalgen_Model_Dealer_Master_Device_Attribute [] */

                        $dealerMasterDeviceAttribute = array();
                        $dealerId                    = Zend_Auth::getInstance()->getIdentity()->dealerId;
                        foreach ($formData as $key => $value)
                        {

                            // This can either be partsCostPerPage or laborCostPerPage.
                            // Regardless we can get the mater device it from the end of the element

                            // Find out the cost we are dealing with
                            $element = false;
                            if (strstr($key, "laborCostPerPage"))
                            {
                                $element        = (strstr($key, "laborCostPerPage")) ? 'laborCostPerPage' : false;
                                $masterDeviceId = str_replace("laborCostPerPage", "", $key);
                                $price          = $value;

                                if ($price != '' && !is_numeric($price))
                                {
                                    $this->_flashMessenger->addMessage(array("error" => "All values must be numeric. Please correct it and try again."));
                                    break;
                                }
                                else if ($price != '')
                                {
                                    if ($price == 0)
                                    {
                                        $price = new Zend_Db_Expr('NULL');
                                    }

                                    if ($value > 0)
                                    {
                                        if (isset($dealerMasterDeviceAttribute [$masterDeviceId]))
                                        {
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->laborCostPerPage = $price;
                                        }
                                        else
                                        {
                                            $dealerMasterDeviceAttribute[$masterDeviceId]                   = new Proposalgen_Model_Dealer_Master_Device_Attribute();
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->laborCostPerPage = $price;
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->dealerId         = $dealerId;
                                            $dealerMasterDeviceAttribute[$masterDeviceId]->masterDeviceId   = $masterDeviceId;
                                        }
                                    }

                                }
                            }
                            else if (strstr($key, "partsCostPerPage"))
                            {
                                $element        = (strstr($key, "partsCostPerPage")) ? 'partsCostPerPage' : false;
                                $masterDeviceId = str_replace("partsCostPerPage", "", $key);
                                $price          = $value;

                                if ($price != '' && !is_numeric($price))
                                {
                                    $this->_flashMessenger->addMessage(array("error" => "All values must be numeric. Please correct it and try again."));
                                    break;
                                }
                                else if ($price != '')
                                {
                                    if ($price == 0)
                                    {
                                        $price = new Zend_Db_Expr('NULL');
                                    }

                                    if (isset($dealerMasterDeviceAttribute [$masterDeviceId]))
                                    {
                                        $dealerMasterDeviceAttribute[$masterDeviceId]->partsCostPerPage = $price;
                                    }
                                    else
                                    {
                                        $dealerMasterDeviceAttribute[$masterDeviceId]                   = new Proposalgen_Model_Dealer_Master_Device_Attribute();
                                        $dealerMasterDeviceAttribute[$masterDeviceId]->partsCostPerPage = $price;
                                        $dealerMasterDeviceAttribute[$masterDeviceId]->dealerId         = $dealerId;
                                        $dealerMasterDeviceAttribute[$masterDeviceId]->masterDeviceId   = $masterDeviceId;
                                    }
                                }

                            }
                        }

                        foreach ($dealerMasterDeviceAttribute as $key => $value)
                        {
                            $masterAttribute = Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->find(array($key, Zend_Auth::getInstance()->getIdentity()->dealerId));
                            if ($masterAttribute)
                            {
                                if (isset($value->laborCostPerPage))
                                {
                                    $masterAttribute->laborCostPerPage = $value->laborCostPerPage;
                                }
                                if (isset($value->partsCostPerPage))
                                {
                                    $masterAttribute->partsCostPerPage = $value->partsCostPerPage;
                                }
                                Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->save($masterAttribute);
                            }
                            else
                            {
                                Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->insert($value);
                            }
                        }

                        if ($passvalid == 0)
                        {
                            $this->_flashMessenger->addMessage(array(
                                                                    "success" => "The device pricing updates have been applied successfully."
                                                               ));
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
            catch
            (Exception $e)
            {
                throw new Exception("Passing exception up the chain.", 0, $e);
                $db->rollback();
                $this->_flashMessenger->addMessage(array(
                                                        "error" => "Error: The updates were not saved."
                                                   ));
            }
        }
    }

    public function bulkfilepricingAction ()
    {
        Zend_Session::start();
        $this->view->title = "Import & Export Pricing";
        $db                = Zend_Db_Table::getDefaultAdapter();
        $dealerId          = Zend_Auth::getInstance()->getIdentity()->dealerId;

        if ($this->_request->isPost())
        {
            $formData                   = $this->_request->getPost();
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
                        $key_dealer_sku        = null;
                        $key_parts_cpp         = null;
                        $key_labor_cpp         = null;
                        $key_system_cost       = null;

                        $import_type = false;
                        /**
                         * Finds where each column is located inside the CSV
                         */
                        $array_key = 0;
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
                            else if (strtolower($value) == "dealer price")
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
                            else if (strtolower($value) == "dealer sku")
                            {
                                $key_dealer_sku = $array_key;
                            }
                            else if (strtolower($value) == "labor cpp")
                            {
                                $key_labor_cpp = $array_key;
                            }
                            else if (strtolower($value) == "parts cpp")
                            {
                                $key_parts_cpp = $array_key;
                            }
                            else if (strtolower($value) == "system price")
                            {
                                $key_system_cost = $array_key;
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
                                            $columns [3] = "Price";
                                            $columns [4] = "Dealer Sku";
                                            $columns [5] = "Labor CPP";
                                            $columns [6] = "Parts CPP";


                                            $table   = new Proposalgen_Model_DbTable_MasterDevice();
                                            $where   = $table->getAdapter()->quoteInto('id = ?', $master_device_id, 'INTEGER');
                                            $printer = $table->fetchRow($where);

                                            if (count($printer) > 0)
                                            {
                                                // save into array
                                                $final_devices [0] = $master_device_id;
                                                $final_devices [1] = $devices [$key] [$key_manufacturer];
                                                $final_devices [2] = $devices [$key] [$key_printer_model];
                                                $final_devices [3] = $devices [$key] [$key_new_price];
                                                $final_devices [4] = $devices [$key] [$key_dealer_sku];
                                                $final_devices [5] = $devices [$key] [$key_labor_cpp];
                                                $final_devices [6] = $devices [$key] [$key_parts_cpp];
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
                                            $columns [6] = "System Price";
                                            $columns [7] = "New Price";
                                            $columns [8] = "Dealer Sku";

                                            $table = new Proposalgen_Model_DbTable_Toner();
                                            $where = $table->getAdapter()->quoteInto('id = ?', $toner_id, 'INTEGER');
                                            $toner = $table->fetchRow($where);

                                            if (count($toner) > 0)
                                            {
                                                // get current costs
//                                                $current_toner_price = $toner ['systemCost'];
                                                // save into array
                                                $final_devices [0] = $toner_id;
                                                $final_devices [1] = $devices [$key] [$key_manufacturer];
                                                $final_devices [2] = $devices [$key] [$key_part_type];
                                                $final_devices [3] = $devices [$key] [$key_sku];
                                                $final_devices [4] = $devices [$key] [$key_color];
                                                $final_devices [5] = $devices [$key] [$key_yield];
                                                $final_devices [6] = $devices [$key] [$key_system_cost];
                                                $final_devices [7] = $devices [$key] [$key_new_price];
                                                $final_devices [8] = $devices [$key] [$key_dealer_sku];
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

                                // Loop through the file and save.
                                $inputFilter = new Zend_Filter_Input(array(
                                                                          'cost'             => array(
                                                                              'StringTrim',
                                                                          ),
                                                                          'laborCostPerPage' => array(
                                                                              'StringTrim',
                                                                          ),
                                                                          'partsCostPerPage' => array(
                                                                              'StringTrim',
                                                                          ),
                                                                     ), array(
                                                                             'cost'             => array(
                                                                                 'Float',
                                                                                 array(
                                                                                     'name'    => 'GreaterThan',
                                                                                     'options' => array(
                                                                                         'min' => 0,
                                                                                     )
                                                                                 )
                                                                             ),
                                                                             'laborCostPerPage' => array(
                                                                                 'Float',
                                                                                 array(
                                                                                     'name'    => 'GreaterThan',
                                                                                     'options' => array(
                                                                                         'min' => 0,
                                                                                     )
                                                                                 )
                                                                             ),
                                                                             'partsCostPerPage' => array(
                                                                                 'Float',
                                                                                 array(
                                                                                     'name'    => 'GreaterThan',
                                                                                     'options' => array(
                                                                                         'min' => 0,
                                                                                     )
                                                                                 )
                                                                             ),
                                                                        )
                                );

                                foreach ($results as $key => $value)
                                {

                                    $exists = false;
                                    $insert = false;
                                    $update = false;
                                    $delete = false;

                                    // update records
                                    if ($import_type == 'printer')
                                    {
                                        if (in_array("System Admin", $this->privilege))
                                        {
                                            // Set the data inside the input filter to hold the various costs that
                                            // have been inputted
                                            $inputFilter->setData(array('cost' => $value ['Price']));
                                            $importCost = $inputFilter->cost;

                                            $inputFilter->setData(array('laborCostPerPage' => $value ['Labor CPP']));
                                            $importLaborCpp = $inputFilter->laborCostPerPage;

                                            $inputFilter->setData(array('partsCostPerPage' => $value ['Parts CPP']));
                                            $importPartsCpp = $inputFilter->partsCostPerPage;

                                            $importDealerSku  = $value ['Dealer Sku'];
                                            $master_device_id = $value ['Master Printer ID'];

                                            $dataArray = array(
                                                'masterDeviceId'   => $master_device_id,
                                                'dealerId'         => $dealerId,
                                                'dealerSku'        => $importDealerSku,
                                                'cost'             => $importCost,
                                                'laborCostPerPage' => $importLaborCpp,
                                                'partsCostPerPage' => $importPartsCpp,
                                            );

                                            // Does the master device exist in our database?
                                            $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($master_device_id);
                                            if (count($masterDevice->toArray()) > 0)
                                            {
                                                // Do we have the master device already in this dealer device table
                                                $masterDeviceAttribute = Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->find(array($master_device_id, $dealerId));
                                                if ($masterDeviceAttribute)
                                                {
                                                    // If we have a master device attribute and the row is empty, delete the row
                                                    if (empty($importCost) && empty($importDealerSku))
                                                    {
                                                        Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->delete($masterDeviceAttribute);
                                                    }
                                                    else
                                                    {
                                                        // Have any values changed
                                                        $hasChanged = false;

                                                        if (!$inputFilter->isValid('cost'))
                                                        {
                                                            $importCost = null;
                                                        }
                                                        if (!$inputFilter->isValid('partsCostPerPage'))
                                                        {
                                                            $importPartsCpp = null;
                                                        }
                                                        if (!$inputFilter->isValid('laborCostPerPage'))
                                                        {
                                                            $importLaborCpp = null;
                                                        }

                                                        if ((float)$importCost !== (float)$masterDeviceAttribute->cost)
                                                        {
                                                            if ($importCost === null)
                                                            {
                                                                $importCost = new Zend_Db_Expr('null');
                                                            }
                                                            $masterDeviceAttribute->cost = $importCost;
                                                            $hasChanged                  = true;
                                                        }

                                                        if ((float)$importPartsCpp !== (float)$masterDeviceAttribute->partsCostPerPage)
                                                        {
                                                            if ($importPartsCpp === null)
                                                            {
                                                                $importPartsCpp = new Zend_Db_Expr('null');
                                                            }
                                                            $masterDeviceAttribute->partsCostPerPage = $importPartsCpp;
                                                            $hasChanged                              = true;
                                                        }

                                                        if ((float)$importLaborCpp !== (float)$masterDeviceAttribute->laborCostPerPage)
                                                        {
                                                            if ($importLaborCpp === null)
                                                            {
                                                                $importLaborCpp = new Zend_Db_Expr('null');
                                                            }
                                                            $masterDeviceAttribute->laborCostPerPage = $importLaborCpp;
                                                            $hasChanged                              = true;
                                                        }

                                                        if ((float)$importPartsCpp !== (float)$masterDeviceAttribute->partsCostPerPage)
                                                        {
                                                            if ($importPartsCpp === null)
                                                            {
                                                                $importPartsCpp = new Zend_Db_Expr('null');
                                                            }
                                                            $masterDeviceAttribute->partsCostPerPage = $importPartsCpp;
                                                            $hasChanged                              = true;

                                                        }
                                                        if ($importDealerSku !== $masterDeviceAttribute->dealerSku)
                                                        {
                                                            if ($importDealerSku === null)
                                                            {
                                                                $importDealerSku = new Zend_Db_Expr('null');
                                                            }
                                                            $masterDeviceAttribute->dealerSku = $importDealerSku;
                                                            $hasChanged                       = true;
                                                        }

                                                        if ($hasChanged)
                                                        {
                                                            Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->save($masterDeviceAttribute);

                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    if ($importCost > 0 || !empty($importDealerSku))
                                                    {
                                                        $masterDeviceAttribute = new Proposalgen_Model_Dealer_Master_Device_Attribute();
                                                        $masterDeviceAttribute->populate($dataArray);
                                                        Proposalgen_Model_Mapper_Dealer_Master_Device_Attribute::getInstance()->insert($masterDeviceAttribute);
                                                    }

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
                                        if (in_array("System Admin", $this->privilege) && $company == 1)
                                        {
                                            $inputFilter->setData(array('cost' => $value ['New Price']));
                                            // Filter the data -
                                            $importTonerId   = $value ['Toner ID'];
                                            $importDealerSku = trim($value ['Dealer Sku']);
                                            $importCost      = $inputFilter->cost;

                                            $dataArray = array(
                                                'tonerId'   => $importTonerId,
                                                'dealerSku' => $importDealerSku,
                                                'cost'      => $importCost,
                                                'dealerId'  => $dealerId,
                                            );

                                            $toner = Proposalgen_Model_Mapper_Toner::getInstance()->find($value ['Toner ID']);
                                            if (count($toner->toArray()) > 0)
                                            {

                                                $tonerAttribute = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->find(array($importTonerId, $dealerId));
                                                // Does the toner attribute exists ?
                                                if ($tonerAttribute)
                                                {
                                                    // If cost && sku are empty  or cost = 0 -> delete.
                                                    // Delete
                                                    if (empty($importCost) && empty($importDealerSku))
                                                    {
                                                        // If the attributes are empty after being found, delete them.
                                                        Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->delete($tonerAttribute);
                                                    }
                                                    else
                                                    {
                                                        // Have any values changed
                                                        $hasChanged = false;

                                                        if (!$inputFilter->isValid('cost'))
                                                        {
                                                            $importCost = null;
                                                        }

                                                        if ((float)$importCost !== (float)$tonerAttribute->cost)
                                                        {
                                                            if ($importCost === null)
                                                            {
                                                                $importCost = new Zend_Db_Expr('NULL');
                                                            }
                                                            $tonerAttribute->cost = $importCost;
                                                            $hasChanged           = true;
                                                        }

                                                        if ($tonerAttribute->dealerSku != $importDealerSku)
                                                        {
                                                            if (empty($importDealerSku))
                                                            {
                                                                $importDealerSku = new Zend_Db_Expr('NULL');
                                                            }
                                                            $tonerAttribute->dealerSku = $importDealerSku;
                                                            $hasChanged                = true;
                                                        }

                                                        if ($hasChanged)
                                                        {
                                                            Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->save($tonerAttribute);

                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    if ($importCost > 0 || !empty($importDealerSku))
                                                    {
                                                        $tonerAttribute = new Proposalgen_Model_Dealer_Toner_Attribute();
                                                        $tonerAttribute->populate($dataArray);
                                                        Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance()->insert($tonerAttribute);
                                                    }
                                                }
                                            }
                                        }

                                        else if ($hdnRole != "user" && (!in_array("Standard User", $this->privilege) && $company > 1))
                                        {
                                            $toner_id          = $results[$key] ['Toner ID'];
                                            $manufacturer_name = $results[$key] ['Manufacturer'];
                                            $toner_dealer_sku  = $results[$key] ['SKU'];
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
                                            $toner_dealer_sku  = $results[$key] ['SKU'];
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
                                $this->_flashMessenger->addMessage(array(

                                                                        "success" => "Your pricing updates have been applied successfully."
                                                                   ));
                                $db->commit();
                            }
                            catch
                            (Exception $e)
                            {

                                $db->rollback();
                                $this->_flashMessenger->addMessage(array(
                                                                        "error" => "An error has occurred during the update and your changes were not applied. Please review your file and try again."
                                                                   ));
                            }


                            ///////////////////////////////
                            //////////////////////////////////////////
                            ////End Saving
                            //////////////////////////////////////////
                        }
                    }
                    catch (Exception $e)
                    {
                        $db->rollback();
                        $this->_flashMessenger->addMessage(array(
                                                                "error" => " An error has occurred during the update and your changes were not applied. Please review your file and try again."
                                                           ));
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

    //This is the code for updating system pricing for master devices and toners.
    // To make this fully work, the function masterDevicesList needs to be (copied?) changed to return the system pricing instead of the dealer pricing. Same goes for tonersList
    /*
    public function bulkSystemdevicepricingAction ()
    {
        $this->view->title       = "Update Pricing";
        $this->view->parts_list  = array();
        $this->view->device_list = array();
        $db                      = Zend_Db_Table::getDefaultAdapter();

        $dealer         = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $reportSettings = $dealer->getReportSettings();

        $this->view->default_labor = $reportSettings['laborCostPerPage'];
        $this->view->default_parts = $reportSettings['partsCostPerPage'];

        // fill manufacturers dropdown
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('isDeleted = false', 'fullName');
        $this->view->manufacturer_list = $manufacturers;

        if ($this->_request->isPost())
        {
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
                                    $passvalid = 1;
                                    $this->_flashMessenger->addMessage(array(
                                                                            "error" => "All values must be greater than 0. Please correct it and try again."
                                                                       ));
                                    break;
                                }
                                else if ($price != '' && !is_numeric($price))
                                {
                                    $passvalid = 1;
                                    $this->_flashMessenger->addMessage(array(
                                                                            "error" => "All values must be numeric. Please correct it and try again."
                                                                       ));
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
                                }
                            }
                        }

                        if ($passvalid == 0)
                        {
                            $this->_flashMessenger->addMessage(array(
                                                                    "success" => "The toner pricing updates have been applied successfully."
                                                               ));
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

                            // This can either be partsCostPerPage or laborCostPerPage.
                            // Regardless we can get the mater device it from the end of the element

                            // Find out the cost we are dealing with
                            $element = false;
                            if (strstr($key, "laborCostPerPage"))
                            {
                                $element        = (strstr($key, "laborCostPerPage")) ? 'laborCostPerPage' : false;
                                $masterDeviceId = str_replace("laborCostPerPage", "", $key);
                            }
                            else if (strstr($key, "partsCostPerPage"))
                            {
                                $element        = (strstr($key, "partsCostPerPage")) ? 'partsCostPerPage' : false;
                                $masterDeviceId = str_replace("partsCostPerPage", "", $key);
                            }

                            // If we have an element;
                            if ($element)
                            {
                                // Get the master device id
                                $price = $value;
                                if ($price != '' && !is_numeric($price))
                                {
                                    $passvalid = 1;
                                    $this->_flashMessenger->addMessage(array("error" => "All values must be numeric. Please correct it and try again."));
                                    break;
                                }
                                else if ($price != '')
                                {
                                    if ($price == 0)
                                    {
                                        $price = new Zend_Db_Expr('NULL');
                                    }

                                    $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($masterDeviceId);
                                    // Do we have the master device
                                    if ($masterDevice instanceof Proposalgen_Model_MasterDevice)
                                    {
                                        $masterDevice->$element = $price;
                                        Proposalgen_Model_Mapper_MasterDevice::getInstance()->save($masterDevice);
                                    }
                                    else
                                    {
                                        $passvalid = 1;
                                        $this->_flashMessenger->addMessage(array("error" => "There was and error finding a master device. Please try again"));
                                        break;
                                    }
                                }
                            }
                        }

                        if ($passvalid == 0)
                        {
                            $this->_flashMessenger->addMessage(array(
                                                                    "success" => "The device pricing updates have been applied successfully."
                                                               ));
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
                $this->_flashMessenger->addMessage(array(
                                                        "error" => "Error: The updates were not saved."
                                                   ));
            }
        }
    }
    */

    public function exportpricingAction ()
    {
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        //$company = $this->_getParam('company', $this->dealer_company_id);
        $pricing = $this->_getParam('pricing', 'printer');


        // filename for CSV file
        $filename = "system_pricing" . "_" . $pricing . "_pricing_" . date('m_d_Y') . ".csv";

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
                    'Price',
                    'Labor CPP',
                    'Parts CPP',
                );
                if (in_array("System Admin", $this->privilege))
                {
                    $select = $db->select()
                        ->from(array(
                                    'md' => 'pgen_master_devices'
                               ), array(
                                       'id AS master_id',
                                       'modelName',
                                  ))
                        ->joinLeft(array(
                                        'm' => 'manufacturers'
                                   ), 'm.id = md.manufacturerId', array(
                                                                       'fullname'
                                                                  ))
                        ->joinLeft(array(
                                        'dmda' => 'dealer_master_device_attributes'
                                   ), 'dmda.masterDeviceId = md.id',
                            array(
                                 'cost',
                                 'laborCostPerPage',
                                 'partsCostPerPage',
                            ))
                        ->order(array(
                                     'm.fullname',
                                     'md.modelName',
                                ));
                    $stmt   = $db->query($select);
                    $result = $stmt->fetchAll();

                }
                else if (in_array("Standard User", $this->privilege))
                {
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
                        $price,
                        $value ['laborCostPerPage'],
                        $value ['partsCostPerPage'],
                    );
                }

            }
            /* Begin toner export export logic here */
            else
            {
                $fieldTitles = array(
                    'Toner ID',
                    'Manufacturer',
                    'Type',
                    'SKU',
                    'Color',
                    'Yield',
                    'System Price',
                    'Dealer Price',
                );
                $dealerId    = Zend_Auth::getInstance()->getIdentity()->dealerId;
                // Get Count
                $select = $db->select()
                    ->from(array(
                                't' => 'pgen_toners'), array(
                                                            'id AS toners_id', 'sku', 'yield', "systemCost" => "cost"
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
                    ->joinLeft(array(
                                    'dta' => 'dealer_toner_attributes'
                               ), "dta.tonerId = t.id AND dta.dealerId = {$dealerId}", array('cost'))
                    ->where("t.id > 0")
                    ->group('t.id')
                    ->order(array(
                                 'tm.fullname'
                            ));
                $stmt   = $db->query($select);
                $result = $stmt->fetchAll();


                foreach ($result as $key => $value)
                {
                    $fieldList [] = array(
                        $value ['toners_id'],
                        $value ['fullname'],
                        $value ['part_type'],
                        $value ['sku'],
                        $value ['toner_color'],
                        $value ['yield'],
                        $value ['systemCost'],
                        $value ['cost'],
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
                    $response->rows [$i] ['id'] = $masterDevice->id;
                    $response->rows [$i]        = array(
                        "masterID"                   => $masterDevice->id,
                        "manufacturerId"             => $masterDevice->getManufacturer()->fullname,
                        "printer_model"              => $masterDevice->modelName,
                        "labor_cost_per_page"        => number_format($masterDevice->laborCostPerPage, 4),
                        "parts_cost_per_page"        => number_format($masterDevice->partsCostPerPage, 4),
                        "labor_cost_per_page_dealer" => number_format($masterDevice->getDealerAttributes()->laborCostPerPage, 4),
                        "parts_cost_per_page_dealer" => number_format($masterDevice->getDealerAttributes()->partsCostPerPage, 4));
                    $i++;
                }
            }
        }
        catch (Exception $e)
        {
            Throw new Exception($e->getMessage);
        }
        $this->sendJson($response);
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
            else if ($filter == "toner_sku" || $filter == "toner_SKU")
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
            if ($filter == "manufacturerId")
            {
                $filter = "tm.id";
                $where  = ' AND ' . $filter . ' = ' . $criteria;
            }
            else
            {
                $where = ' AND ' . $filter . ' LIKE("%' . $criteria . '%")';
            }
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

        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
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
                ->joinLeft(array(
                                'dta' => 'dealer_toner_attributes'
                           ), "t.id = dta.tonerId AND dealerId = {$dealerId}", array('cost AS toner_dealer_price', 'dealerSku'))
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

                    $formData->rows [$i] ['id'] = $row ['toner_id'];
                    $formData->rows [$i]        = array(
                        "toner_id"           => $row ['toner_id'],
                        "toner_SKU"          => $row ['toner_SKU'],
                        "manufacturer_name"  => ucwords(strtolower($row ['toner_manufacturer'])),
                        "part_type_id"       => $type_name,
                        "toner_color_name"   => ucwords(strtolower($row ['toner_color_name'])),
                        "toner_yield"        => $row ['toner_yield'],
                        "toner_price"        => $row ['toner_price'],
                        "toner_dealer_price" => $row ['toner_dealer_price'],
                        "new_toner_price"    => $row ['master_device_id'],
                        "is_added"           => $row ['is_added'],
                        "device_list"        => ucwords(strtolower($row ['device_list'])),
                        "dealer_sku"         => $row['dealerSku']
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

        $this->sendJson($formData);
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

        $sql = "SELECT concat(displayname, ' ', modelName) AS device_name, pgen_master_devices.id, displayname, modelName FROM manufacturers
        JOIN pgen_master_devices ON pgen_master_devices.manufacturerId = manufacturers.id
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
                    $this->_flashMessenger->addMessage(array(
                                                            "success" => "The matchups have been saved."
                                                       ));
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
                $this->_flashMessenger->addMessage(array(
                                                        "error" => "Error."
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
            $this->sendJson(array(
                                 'error' => $errorMessage
                            ));
        }
        else
        {
            $this->sendJson(array(
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
            if ($searchCriteria != 'onlyUnmapped' && ($searchCriteria === '' || $searchValue === '' || !$filterCriteriaValidator->isValid($searchCriteria)))
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
            $this->sendJson($jqGrid->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array(
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

        // fill manufacturer drop down
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
                $replacementTable = new Proposalgen_Model_DbTable_ReplacementDevice();
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
                            $where                = $replacementTable->getAdapter()->quoteInto('replacementCategory = ?', $replacement_category);
                            $replacement          = $replacementTable->fetchAll($where);

                            if (count($replacement) > 1)
                            {
                                $where = $replacementTable->getAdapter()->quoteInto('masterDeviceId = ?', $key, 'INTEGER');
                                $replacementTable->delete($where);
                                $this->_flashMessenger->addMessage(array(
                                                                        "success" => "The selected printer(s) are no longer marked as replacement printers."
                                                                   ));
                            }
                            else
                            {
                                $this->_flashMessenger->addMessage(array(
                                                                        "error" => "Could not delete all replacement printers as one or more was the last printer for it's replacement category."
                                                                   ));
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

                $replacementTable         = new Proposalgen_Model_DbTable_ReplacementDevice();
                $replacement_devicesTable = new Proposalgen_Model_DbTable_ReplacementDevice();
                $replacementTableMapper   = Proposalgen_Model_Mapper_ReplacementDevice::getInstance();

                $replacement_devicesData = array(
                    'dealerId'            => Zend_Auth::getInstance()->getIdentity()->dealerId,
                    'replacementCategory' => strtoupper($replacement_category),
                    'printSpeed'          => $print_speed,
                    'resolution'          => $resolution,
                    'monthlyRate'         => $monthly_rate
                );

                if ($form_mode == "add")
                {

                    // check to see if replacement device exists
                    $where               = array('masterDeviceId = ?' => $printer_model, "dealerId = ?" => Zend_Auth::getInstance()->getIdentity()->dealerId);
                    $replacement_devices = $replacementTableMapper->fetchAll($where, null, 1);
                    if (count($replacement_devices) > 0)
                    {
                        $replacement_devicesTable->update($replacement_devicesData, $where);
                        $this->view->message = "<p>The replacement printer has been updated.</p>";
                    }
                    else
                    {

                        $replacement_devicesData ['masterDeviceId'] = $printer_model;
                        $replacement_devicesTable->insert($replacement_devicesData);

                        $this->view->message = "<p>The replacement printer has been added.</p>";
                    }

                }
                else if ($form_mode == "edit")
                {
                    $is_valid = true;
                    if (strtoupper($hdnOriginalCategory) !== strtoupper($replacement_category))
                    {

                        $where       = $replacementTable->getAdapter()->quoteInto('replacementCategory = ?', $hdnOriginalCategory);
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
                        $where = $replacement_devicesTable->getAdapter()->quoteInto('masterDeviceId = ?', $printer_model, 'INTEGER');
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
        $this->sendJson($this->view->message);
    }

    public function replacementprinterslistAction ()
    {
        $formData = new stdClass();
        try
        {
            // get pf device list filter by manufacturer
            $replacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->fetchAllForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);

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

        $this->sendJson($formData);
    }

    public function replacementdetailsAction ()
    {
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
                    ->where('masterDeviceId = ?', $device_id, 'INTEGER');
                $stmt   = $db->query($select);
                $row    = $stmt->fetchAll();

                $formData = array(
                    'replacement_category' => $row [0] ['replacementCategory'],
                    'print_speed'          => $row [0] ['printSpeed'],
                    'resolution'           => $row [0] ['resolution'],
                    'monthly_rate'         => $row [0] ['monthlyRate']
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
            Throw new exception("Error: Unable to find replacement device.", 0, $e);
        } // end catch


        $this->sendJson($formData);
    }

    /**
     * This action is used for searching for master devices via ajax
     */
    public function searchForDeviceAction ()
    {
        $searchTerm     = "%" . implode('%', explode(' ', $this->_getParam('searchTerm', ''))) . "%";
        $manufacturerId = $this->_getParam('manufacturerId', false);

        $filterByManufacturer = null;
        if ($manufacturerId !== false)
        {
            $manufacturer = Proposalgen_Model_Mapper_Manufacturer::getInstance()->find($manufacturerId);
            if ($manufacturer instanceof Proposalgen_Model_Manufacturer)
            {
                $filterByManufacturer = $manufacturer;
            }
        }

        $jsonResponse = Proposalgen_Model_Mapper_MasterDevice::getInstance()->searchByName($searchTerm, $filterByManufacturer);

        $this->sendJson($jsonResponse);

    }
}