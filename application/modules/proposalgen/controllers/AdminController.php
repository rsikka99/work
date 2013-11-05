<?php
/**
 * Class Proposalgen_AdminController
 */
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
    protected $dealerId;

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
        $this->dealerId             = Zend_Auth::getInstance()->getIdentity()->dealerId;
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
                                         't' => 'toners'
                                    ))
                             ->join(array(
                                         'td' => 'device_toners'
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
                                              'md' => 'master_devices'
                                         ))
                                  ->joinLeft(array(
                                                  'm' => 'manufacturers'
                                             ), 'm.id = md.manufacturerId')
                                  ->joinLeft(array(
                                                  'rd' => 'replacement_devices'
                                             ), 'rd.masterDeviceId = md.id')
                                  ->where('md.id = ?', $deviceID);
                $stmt        = $db->query($select);
                $row         = $stmt->fetchAll();
                $launch_date = new Zend_Date($row [0] ['launchDate'], "yyyy/mm/dd HH:ii:ss");
                $formData    = array(
                    'launch_date'           => $launch_date->toString('mm/dd/yyyy'),
                    'toner_config_id'       => $row [0] ['tonerConfigId'],
                    'is_copier'             => $row [0] ['isCopier'] ? true : false,
                    'is_fax'                => $row [0] ['isFax'] ? true : false,
                    'is_duplex'             => $row [0] ['isDuplex'] ? true : false,
                    'is_a3'                 => $row [0] ['isA3'] ? true : false,
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
     * @throws \Exception This action seems to provide json lists for the following:
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
                                       'tc' => 'toner_colors'
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
                    'Remove Part Types!',
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

        $formData = new stdClass();
        if ($toner_id > 0)
        {
            $filter   = $this->_getParam('filter', false);
            $criteria = trim($this->_getParam('criteria', false));
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
                    if ($filter == "manufacturer_name" || $filter == "manufacturer_id")
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
                            if ($filter == "toner_color_name")
                            {
                                $filter = 'tc.name';
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
                $deviceToners      = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchDeviceTonersByTonerId($toner_id);
                $deviceTonersCount = count($deviceToners);

                // GET NUMBER OF DEVICES WHERE LAST TONER FOR THIS COLOR
                $num_devices_count = 0;
                foreach ($deviceToners as $deviceToner)
                {
                    // GET ALL SAME COLOR TONERS FOR DEVICE
                    $select = $db->select();
                    $select->from(array(
                                       'dt' => 'device_toners'
                                  ));
                    $select->joinLeft(array(
                                           't' => 'toners'
                                      ), 'dt.toner_id = t.id');
                    $select->where('t.tonerColorId = ' . $toner_color_id . ' AND dt.master_device_id = ' . $deviceToner->master_device_id);
                    $stmt        = $db->query($select);
                    $num_devices = $stmt->fetchAll();

                    if (count($num_devices) == 1)
                    {
                        $num_devices_count += 1;
                    }
                }

                // GET SAME COLOR TONERS
                $select = $db->select();
                $select->from(array(
                                   't' => 'toners'
                              ));
                $select->joinLeft(array(
                                       'tc' => 'toner_colors'
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
                $select = $db->select();
                $select->from(array(
                                   't' => 'toners'), array('id AS toners_id', 'sku', 'yield', 'cost')
                );
                $select->joinLeft(array(
                                       'tc' => 'toner_colors'
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
                    $formData->page    = $page;
                    $formData->total   = $total_pages;
                    $formData->records = $count;
                    foreach ($result as $row)
                    {
                        $formData->rows [$i] ['id']   = $row ['toners_id'];
                        $formData->rows [$i] ['cell'] = array(
                            $row ['toners_id'],
                            $row ['sku'],
                            ucwords(strtolower($row ['fullname'])),
                            ucwords(strtolower($row ['name'])),
                            $row ['yield'],
                            $row ['cost'],
                            $num_devices_count,
                            $deviceTonersCount
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
            $deviceToners = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchDeviceTonersByTonerId($toner_id);

            // GET NUMBER OF DEVICES WHERE LAST TONER FOR THIS COLOR
            $num_devices_count = 0;
            foreach ($deviceToners as $deviceToner)
            {
                $master_device_id = $deviceToner->master_device_id;
                // GET ALL SAME COLOR TONERS FOR DEVICE
                $select = $db->select();
                $select->from(array(
                                   'dt' => 'device_toners'
                              ));
                $select->joinLeft(array(
                                       't' => 'toners'
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
                'total_count'  => count($deviceToners),
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


    public function replacetonerAction ()
    {
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
        $deviceToners = Proposalgen_Model_Mapper_DeviceToner::getInstance()->fetchDeviceTonersByTonerId($replace_id);

        $db->beginTransaction();
        try
        {
            $message = "The toner has been deleted successfully.";

            if ($replace_mode == 'optional_replace' && $with_id > 0)
            {
                // LOOP THROUGH ALL DEVICES AND UPDATE TO REPLACEMENT TONER ID
                // ($with_id)
                foreach ($deviceToners as $deviceToner)
                {
                    // UPDATE ALL DEVICES WITH THIS TONER (replace_id) TO
                    // REPLACEMENT TONER (with_id)
                    $device_toner           = Proposalgen_Model_Mapper_DeviceToner::getInstance()->find(array($replace_id, $deviceToner->master_device_id));
                    $device_toner->toner_id = $with_id;
                    Proposalgen_Model_Mapper_DeviceToner::getInstance()->save($device_toner);
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
                    foreach ($deviceToners as $deviceToner)
                    {
                        $master_device_id = $deviceToner->master_device_id;

                        if ($apply_all == 1)
                        {

                            // UPDATE ALL DEVICES WITH THIS TONER (replace_id)
                            // TO REPLACEMENT TONER (with_id)
                            $device_toner           = Proposalgen_Model_Mapper_DeviceToner::getInstance()->find(array($replace_id, $master_device_id));
                            $device_toner->toner_id = $with_id;
                            Proposalgen_Model_Mapper_DeviceToner::getInstance()->save($device_toner);
                            $toner_count += 1;
                        }
                        else
                        {
                            // UPDATE ONLY DEVICES WHERE THIS IS THE LAST OF
                            // IT'S COLOR ($toner_color_id)
                            $select = $db->select();
                            $select->from(array(
                                               'dt' => 'device_toners'
                                          ));
                            $select->joinLeft(array(
                                                   't' => 'toners'
                                              ), 'dt.toner_id = t.id');
                            $select->where('t.tonerColorId = ' . $toner_color_id . ' AND dt.master_device_id = ' . $master_device_id);
                            $stmt        = $db->query($select);
                            $num_devices = $stmt->fetchAll();

                            if (count($num_devices) == 1)
                            {
                                // UPDATE THIS DEVICE WITH REPLCEMENT TONER
                                // (with_id)
                                $device_toner           = Proposalgen_Model_Mapper_DeviceToner::getInstance()->find(array($replace_id, $master_device_id));
                                $device_toner->toner_id = $with_id;
                                Proposalgen_Model_Mapper_DeviceToner::getInstance()->save($device_toner);
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

            // REMOVE DEVICE TONER MAPPINGS
            $device_tonerTable = new Proposalgen_Model_DbTable_DeviceToner();
            $where             = $device_tonerTable->getAdapter()->quoteInto('toner_id = ?', $replace_id, 'INTEGER');
            $device_tonerTable->delete($where);

            // REMOVE TONER
            $tonerTable = new Proposalgen_Model_DbTable_Toner();
            $where      = $tonerTable->getAdapter()->quoteInto('id = ?', $replace_id, 'INTEGER');
            $tonerTable->delete($where);

            // Update the toner vendor manufacturer
            Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->updateTonerVendorByManufacturerId($toner->manufacturerId);

            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
            $message = "An error has occurred and the toner was not replaced.";
        }

        $this->sendJson(array("message" => $message));
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
        $manufacturer      = $manufacturerTable->fetchRow(array('id = ?' => $manufacturerId));

        try
        {
            if (count($manufacturer->toArray()) > 0)
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

    public function masterdeviceslistAction ()
    {
        $response = new stdClass();

        // Criteria is the values that the client wants to search by
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

        // Start is offset for MySQL query
        $start = $limit * $page - $limit;

        try
        {
            // Based on the filter allow the mappers to return the appropriate device

            $masterDevices = Proposalgen_Model_Mapper_MasterDevice::getInstance()->fetchAllMasterDevices($sortIndex, $sortOrder, $this->dealerId, $filter, $criteria, $limit, $start, false);
            $count         = Proposalgen_Model_Mapper_MasterDevice::getInstance()->fetchAllMasterDevices($sortIndex, $sortOrder, $this->dealerId, $filter, $criteria, $limit, 0, true);
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
            throw new Exception("Passing exception up the chain.", 0, $e);
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
            else if ($filter == "toner_sku" || $filter == "toner_SKU")
            {
                $filter = "t.sku";
            }
            else if ($filter == "toner_color_name")
            {
                $filter = "tc.name";
            }
            else if ($filter == "dealer_sku")
            {
                $filter = "dta.dealerSku";
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
                '(SELECT master_device_id FROM device_toners AS sdt WHERE sdt.toner_id = t.id AND sdt.master_device_id = ' . $master_device_id . ') AS is_added',
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
        $formData = new stdClass();

        $dealerId = $this->dealerId;
        try
        {
            // get count
            $select = $db->select()
                         ->from(array(
                                     't' => 'toners'
                                ), $toner_fields_list)
                         ->joinLeft(array(
                                         'dt' => 'device_toners'
                                    ), 'dt.toner_id = t.id', array(
                                                                  'master_device_id'
                                                             ))
                         ->joinLeft(array(
                                         'tm' => 'manufacturers'
                                    ), 'tm.id = t.manufacturerId', array(
                                                                        'tm.fullname AS toner_manufacturer'
                                                                   ))
                         ->joinLeft(array(
                                         'md' => 'master_devices'
                                    ), 'md.id = dt.master_device_id')
                         ->joinLeft(array(
                                         'mdm' => 'manufacturers'
                                    ), 'mdm.id = md.manufacturerId', array(
                                                                          'mdm.fullname AS manufacturer_name'
                                                                     ))
                         ->joinLeft(array(
                                         'tc' => 'toner_colors'
                                    ), 'tc.id = t.tonerColorId', array(
                                                                      'name AS toner_color_name'
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
            $stmt              = $db->query($select);
            $result            = $stmt->fetchAll();
            $formData->page    = $page;
            $formData->total   = $total_pages;
            $formData->records = $count;
            if (count($result) > 0)
            {
                $i = 0;
                foreach ($result as $row)
                {
                    $formData->rows [$i] ['id'] = $row ['toner_id'];
                    $formData->rows [$i]        = array(
                        "toner_id"           => $row ['toner_id'],
                        "toner_SKU"          => $row ['toner_SKU'],
                        "manufacturer_name"  => ucwords(strtolower($row ['toner_manufacturer'])),
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

    /**
     *
     */
    protected function getModelsAction ()
    {
        $terms      = explode(" ", trim($_REQUEST ["searchText"]));
        $searchTerm = "%";
        foreach ($terms as $term)
        {
            $searchTerm .= "$term%";
        }
        // Fetch Devices like term
        $db = Zend_Db_Table::getDefaultAdapter();

        $sql = "SELECT concat(displayname, ' ', modelName) AS device_name, master_devices.id, displayname, modelName FROM manufacturers
        JOIN master_devices ON master_devices.manufacturerId = manufacturers.id
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

        $this->sendJson($devices);
    }

    public function managematchupsAction ()
    {
        $this->view->title  = 'Manage Printer Matchups';
        $this->view->source = "PrintFleet";

        // Fill manufacturers drop down
        $manufacturersTable            = new Proposalgen_Model_DbTable_Manufacturer();
        $manufacturers                 = $manufacturersTable->fetchAll('isDeleted = false', 'fullname');
        $this->view->manufacturer_list = $manufacturers;
    }

    /**
     * This action handles mapping rms models to master devices.
     * If masterDeviceId is set to -1 it will delete the matchup
     * This is used in @see \Proposalgen_AdminController::managematchupsAction()
     */
    public function setMappedToAction ()
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

        $form                         = new Proposalgen_Form_ReplacementPrinter(null);
        $this->view->replacement_form = $form;

        // fill manufacturer drop down
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
                            $replacement_category   = $formData ['replacement_category_' . $key];
                            $replacementTableMapper = Proposalgen_Model_Mapper_ReplacementDevice::getInstance();
                            $where                  = array(
                                $replacementTable->getAdapter()->quoteInto("{$replacementTableMapper->col_replacementCategory}= ?", $replacement_category),
                                $replacementTable->getAdapter()->quoteInto("{$replacementTableMapper->col_dealerId}= ?", $this->dealerId)
                            );
                            $replacement            = $replacementTable->fetchAll($where);

                            if (count($replacement) > 1)
                            {
                                $where = array(
                                    $replacementTable->getAdapter()->quoteInto("{$replacementTableMapper->col_masterDeviceId}= ?", $key),
                                    $replacementTable->getAdapter()->quoteInto("{$replacementTableMapper->col_dealerId}= ?", $this->dealerId)
                                );
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

        $formData             = $this->getAllParams();
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
                    'dealerId'            => $this->dealerId,
                    'replacementCategory' => strtoupper($replacement_category),
                    'printSpeed'          => $print_speed,
                    'resolution'          => $resolution,
                    'monthlyRate'         => $monthly_rate
                );

                if ($form_mode == "add")
                {

                    // check to see if replacement device exists
                    $where               = array('masterDeviceId = ?' => $printer_model, "dealerId = ?" => $this->dealerId);
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

                        $where = array(
                            $replacementTable->getAdapter()->quoteInto("{$replacementTableMapper->col_replacementCategory}= ?", $hdnOriginalCategory),
                            $replacementTable->getAdapter()->quoteInto("{$replacementTableMapper->col_dealerId}= ?", Zend_Auth::getInstance()->getIdentity()->dealerId)
                        );

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
                        $replacementDevice = new Proposalgen_Model_ReplacementDevice();
                        $replacementDevice->populate($replacement_devicesData);
                        $replacementDevice->dealerId       = $this->dealerId;
                        $replacementDevice->masterDeviceId = $printer_model;
                        $replacementTableMapper->save($replacementDevice);
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
            $replacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->fetchAllForDealer($this->dealerId);

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
                $select = $db->select()
                             ->from(array(
                                         'rd' => 'replacement_devices'
                                    ))
                             ->where('masterDeviceId = ?', $device_id, 'INTEGER')
                             ->where('dealerId =  ?', $this->dealerId);
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
     * This action is used for searching for master devices via
     *
     */
    public function searchForDeviceAction ()
    {
        $onlyQuoteDevices = $this->_getParam("onlyQuoteDevices", false);
        $searchTerm       = "%" . implode('%', explode(' ', $this->_getParam('searchTerm', ''))) . "%";
        $manufacturerId   = $this->_getParam('manufacturerId', false);

        $filterByManufacturer = null;
        if ($manufacturerId !== false)
        {
            $manufacturer = Proposalgen_Model_Mapper_Manufacturer::getInstance()->find($manufacturerId);
            if ($manufacturer instanceof Proposalgen_Model_Manufacturer)
            {
                $filterByManufacturer = $manufacturer->id;
            }
        }

        if ($onlyQuoteDevices)
        {
            $jsonResponse = Quotegen_Model_Mapper_Device::getInstance()->searchByName($searchTerm, Zend_Auth::getInstance()->getIdentity()->dealerId, $filterByManufacturer);
        }
        else
        {
            $jsonResponse = Proposalgen_Model_Mapper_MasterDevice::getInstance()->searchByName($searchTerm, $filterByManufacturer);
        }


        $this->sendJson($jsonResponse);

    }
}