<?php

class Proposalgen_PricingimportController extends Zend_Controller_Action
{

    public function postDispatch ()
    {
        $this->view->Layout()->setLayout('blueprint');
        $this->view->HeadLink()->appendStylesheet($this->view->baseUrl('/themes/' . $this->view->App()->theme . '/proposalgenerator/pricingimport.css'));
    }

    public function indexAction ()
    {
        $uploadFileForm = new Tangent_Form_UploadFile(array(
                                                           "csv"
                                                      ), 'forms/uploadFile.phtml');
        if ($this->_request->isPost())
        {
            $formData = $this->getRequest()->getPost();
            if ($uploadFileForm->isValid($formData) && $uploadFileForm->upload_file->isUploaded() && $uploadFileForm->upload_file->receive())
            {
                // File is uploaded - Now we should validate it before moving
                // on.
                $piSession           = new Zend_Session_Namespace('pricingImport');
                $piSession->filename = $uploadFileForm->upload_file->getFileName();
                $this->_redirect('/pricingimport/confirm');
            }
            else
            {
                $uploadFileForm->populate($formData);
            }
        }
        $this->view->uploadFileForm = $uploadFileForm;
    } // end indexAction

    public function confirmAction ()
    {
        $piSession = new Zend_Session_Namespace('pricingImport');
        if (isset($piSession->filename))
        {
            $this->view->filename = $piSession->filename;
            $csv                  = new Proposalgen_Model_CSV_PricingImport_OfficeDepot();
            $csv->importCSV($piSession->filename);
            $this->view->csv = $csv;
        }
        else
        {
            $this->_redirect('/pricingimport/index');
        }
    }

    public function dumpvalidimportAction ()
    {
        $piSession = new Zend_Session_Namespace('pricingImport');
        if (isset($piSession->filename))
        {
            $this->view->filename = $piSession->filename;
            $csv                  = new Proposalgen_Model_CSV_PricingImport_OfficeDepot();
            $csv->importCSV($piSession->filename);
            $rows                  = $csv->getData();
            $this->view->csvHeader = implode(",", array_keys($rows [0]));

            $csvRows  = "";
            $firstRun = true;
            foreach ($rows as $row)
            {
                $csvRows .= ($firstRun) ? "" : "\n";
                $firstColumn = true;
                foreach ($row as $column)
                {
                    if ($firstColumn)
                    {
                        $firstColumn = false;
                    }
                    else
                    {
                        $csvRows .= ",";
                    }
                    if (strpos($column, ",") !== false)
                    {
                        $csvRows .= "\"$column\"";
                    }
                    else
                    {
                        $csvRows .= $column;
                    }
                }
                if ($firstRun)
                {
                    $firstRun = false;
                }
            }

            $this->view->csvRows = $csvRows;
            Tangent_Functions::setHeadersForDownload("NEW PRICING IMPORT.csv");
            $this->view->Layout()->disableLayout();
        }
        else
        {
            $this->_redirect('/pricingimport/index');
        }
    }

    public function commitAction ()
    {
        $piSession = new Zend_Session_Namespace('pricingImport');
        if (isset($piSession->filename))
        {
            // Load the CSV
            $this->view->filename = $piSession->filename;
            $csv                  = new Proposalgen_Model_CSV_PricingImport_OfficeDepot();
            $csv->importCSV($piSession->filename);

            // Database Section
            $db = Zend_Db_Table::getDefaultAdapter();
            try
            {
                $db->beginTransaction();
                // Delete everything dependant on device information in the
                // database
                Proposalgen_Model_Mapper_TicketViewed::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_TicketComment::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_TicketPFRequest::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_Ticket::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_UnknownDeviceInstance::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_DeviceInstanceMeter::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_DeviceInstance::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_Rms_Upload_Row::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_UserDeviceOverride::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_UserTonerOverride::getInstance()->delete("TRUE");
//                Proposalgen_Model_Mapper_DealerDeviceOverride::getInstance()->delete("TRUE");
//                Proposalgen_Model_Mapper_DealerTonerOverride::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_DeviceToner::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_Toner::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_TextualAnswer::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_NumericAnswer::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_DateAnswer::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_MasterMatchupPf::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_PfDeviceMatchupUser::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_DevicePf::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_MasterDevice::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_Manufacturer::getInstance()->delete("TRUE");
                Proposalgen_Model_Mapper_Report::getInstance()->delete("TRUE");

                $manufacturers = array();
                $globalToners  = array();
                $tonerMapper   = Proposalgen_Model_Mapper_Toner::getInstance();
                $OEMMfg        = "clover technologies group";
                // Import device and toner information from the CSV
                foreach ($csv->getData() as $row)
                {
                    $modelName = trim(substr($row ["modelname"], strpos($row ['modelmfg'], $row ["modelname"]) + strlen($row ['modelmfg'])));

                    // Manufacturer
                    $tempMfg = strtolower($row ['modelmfg']);

                    if (array_key_exists($tempMfg, $manufacturers))
                    {
                        $manufacturerId = $manufacturers [$tempMfg];
                    }
                    else
                    {
                        $manufacturer              = new Proposalgen_Model_Manufacturer();
                        $manufacturer->fullname    = ($row ['modelmfg']);
                        $manufacturer->displayname = ($row ['modelmfg']);
                        $manufacturer->isDeleted   = false;
                        $manufacturerId            = Proposalgen_Model_Mapper_Manufacturer::getInstance()->save($manufacturer);
                        $manufacturers [$tempMfg]  = $manufacturerId;
                    }

                    $toners = array();
                    foreach (Proposalgen_Model_CSV_PricingImport_OfficeDepot::$validToners as $partTypeId => $tonersByColor)
                    {
                        foreach ($tonersByColor as $tonerColorId => $tonerArray)
                        {
                            foreach ($tonerArray as $tonerRow)

                            {
                                $row ["$tonerRow yield"] = str_replace(",", "", $row ["$tonerRow yield"]);
                                $row ["$tonerRow cost"]  = str_replace(",", "", $row ["$tonerRow cost"]);
                                if (strlen($row ["$tonerRow sku"]) > 1 && $row ["$tonerRow cost"] > 0 && $row ["$tonerRow yield"] > 0)
                                {
                                    $toner      = new Proposalgen_Model_Toner();
                                    $tonerMfgId = $manufacturerId;
                                    if (array_key_exists("$tonerRow man.", $row))
                                    {
                                        if (strlen($row ["$tonerRow man."]) > 1)
                                        {
                                            // Toner Manufacturer
                                            $tonerTempMfg = strtolower($row ["$tonerRow man."]);
                                            if (array_key_exists($tonerTempMfg, $manufacturers))
                                            {
                                                $tonerMfgId = $manufacturers [$tonerTempMfg];
                                            }
                                            else
                                            {
                                                $manufacturer                  = new Proposalgen_Model_Manufacturer();
                                                $manufacturer->fullname        = $row ["$tonerRow man."];
                                                $manufacturer->displayname     = $row ["$tonerRow man."];
                                                $manufacturer->isDeleted       = false;
                                                $tonerMfgId                    = Proposalgen_Model_Mapper_Manufacturer::getInstance()->save($manufacturer);
                                                $manufacturers [$tonerTempMfg] = $tonerMfgId;
                                            }
                                        }
                                        else
                                        {
                                            // Compatible Toner Manufacturer
                                            if (array_key_exists($OEMMfg, $manufacturers))
                                            {
                                                $tonerMfgId = $manufacturers [$OEMMfg];
                                            }
                                            else
                                            {
                                                $manufacturer              = new Proposalgen_Model_Manufacturer();
                                                $manufacturer->fullname    = ucwords($OEMMfg);
                                                $manufacturer->displayname = ucwords($OEMMfg);
                                                $manufacturer->isDeleted   = false;
                                                $tonerMfgId                = Proposalgen_Model_Mapper_Manufacturer::getInstance()->save($manufacturer);
                                                $manufacturers [$OEMMfg]   = $tonerMfgId;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $tonerMfgId = $manufacturerId;
                                    }

                                    // Create and cache the toner if it doenst
                                    // already exist
                                    if (!array_key_exists($row ["$tonerRow sku"], $globalToners))
                                    {
                                        $toner->sku                            = $row ["$tonerRow sku"];
                                        $toner->cost                           = $row ["$tonerRow cost"];
                                        $toner->yield                          = $row ["$tonerRow yield"];
                                        $toner->partTypeId                     = $partTypeId;
                                        $toner->manufacturerId                 = $tonerMfgId;
                                        $toner->tonerColorId                   = $tonerColorId;
                                        $toner->id                             = $tonerMapper->save($toner);
                                        $globalToners [$row ["$tonerRow sku"]] = $toner;
                                    }
                                    $toners [$partTypeId] [$tonerColorId] [] = $globalToners [$row ["$tonerRow sku"]];
                                }
                            }
                        }
                    }
                    // Get toner config based on the toners we've found
                    $tonerConfigId = $row ["tonerConfig"];

                    foreach ($toners as $partType => $tonersByPartType)
                    {
                        if (array_key_exists(Proposalgen_Model_TonerColor::FOUR_COLOR, $tonersByPartType))
                        {
                            $tonerConfigId = Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED;
                            break;
                        }
                        else if (array_key_exists(Proposalgen_Model_TonerColor::THREE_COLOR, $tonersByPartType))
                        {
                            $tonerConfigId = Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED;
                            break;
                        }
                        else if (array_key_exists(Proposalgen_Model_TonerColor::CYAN, $tonersByPartType) || array_key_exists(Proposalgen_Model_TonerColor::MAGENTA, $tonersByPartType) || array_key_exists(Proposalgen_Model_TonerColor::YELLOW, $tonersByPartType))
                        {
                            $tonerConfigId = Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED;
                            break;
                        }
                    }

                    // See if the printer is duplex capable
                    $duplex = false;
                    // If our duplex row was not set, we check the names to see
                    // if we can guess
                    if (is_null($row ["is_duplex"]))
                    {

                        if (preg_match('/MF[PC]/i', $modelName) > 0)
                        {
                            $duplex = true;
                        }
                        else if (preg_match('/^.*[DX](?!.*[\d ].*)/i', $modelName) > 0)
                        {
                            $duplex = true;
                        }
                    }
                    else
                    {
                        $duplex = (bool)$row ["is_duplex"];
                    }

                    // Master Device
                    $launchDate                        = new DateTime($row ["dateintroduction"]);
                    $masterDevice                      = new Proposalgen_Model_MasterDevice();
                    $masterDevice->ManufacturerId      = $manufacturerId;
                    $masterDevice->PrinterModel        = $modelName;
                    $masterDevice->TonerConfigId       = $tonerConfigId;
                    $masterDevice->IsCopier            = $row ["is_copier"];
                    $masterDevice->IsFax               = $row ["is_fax"];
                    $masterDevice->IsScanner           = $row ["is_scanner"];
                    $masterDevice->IsDuplex            = $duplex;
                    $masterDevice->IsReplacementDevice = false;
                    $masterDevice->WattsPowerNormal    = $row ["wattspowernormal"];
                    $masterDevice->WattsPowerIdle      = $row ["wattspowersave"];
                    $masterDevice->LaunchDate          = date('Y-m-d H:i:s', $launchDate->getTimestamp());
                    $masterDevice->DateCreated         = date('Y-m-d H:i:s');
                    $masterDevice->IsLeased            = 0;
                    $masterDevice->LeasedTonerYield    = null;
                    $masterDeviceId                    = Proposalgen_Model_Mapper_MasterDevice::getInstance()->save($masterDevice);

                    // Save toners and map them to the master device
                    $deviceTonerMapper           = Proposalgen_Model_Mapper_DeviceToner::getInstance();
                    $deviceToner                 = new Proposalgen_Model_DeviceToner();
                    $deviceToner->masterDeviceId = $masterDeviceId;

                    foreach ($toners as $partType => $tonersByPartType)
                    {
                        foreach ($tonersByPartType as $tonerColor => $tonersByColor)
                        {
                            /* @var $toner Proposalgen_Model_Toner */
                            foreach ($tonersByColor as $toner)
                            {
                                $deviceToner->tonerId = $toner->id;
                                $deviceTonerMapper->save($deviceToner);
                            }
                        }
                    }

                    // Printfleet Device
                    if (isset($row ["printermodelid"]))
                    {
                        $devicePf = new Proposalgen_Model_DevicePf();

                        $devicePf->createdBy = Proposalgen_Model_User::getCurrentUserId();

                        $devicePf->pfModelId        = $row ["printermodelid"];
                        $devicePf->pfDbDeviceName   = $modelName;
                        $devicePf->pfDbManufacturer = $row ["modelmfg"];
                        $devicePf->dateCreated      = date('Y-m-d H:i:s');
                        $devicePfId                 = Proposalgen_Model_Mapper_DevicePf::getInstance()->save($devicePf);

                        // Master Matchup to DevicePf
                        $masterMatchupPf                 = new Proposalgen_Model_MasterMatchupPf();
                        $masterMatchupPf->devicesPfId    = $devicePfId;
                        $masterMatchupPf->masterDeviceId = $masterDeviceId;
                        Proposalgen_Model_Mapper_MasterMatchupPf::getInstance()->save($masterMatchupPf);
                    }
                }

                $db->commit();
            }
            catch (Exception $e)
            {
                $db->rollback();
                throw new Exception("Error Importing Pricing!", 0, $e);
            }
        }
        else
        {
            $this->_redirect('/pricingimport/index');
        }
    }

    public function addreplacementsAction ()
    {
        $masterDeviceMapper      = Proposalgen_Model_Mapper_MasterDevice::getInstance();
        $manufacturerMapper      = Proposalgen_Model_Mapper_Manufacturer::getInstance();
        $replacementDeviceMapper = Proposalgen_Model_Mapper_ReplacementDevice::getInstance();

        $hp    = $manufacturerMapper->fetchRow(array(
                                                    "manufacturer_name = ?" => "Hewlett-packard"
                                               ))->ManufacturerId;
        $xerox = $manufacturerMapper->fetchRow(array(
                                                    "manufacturer_name = ?" => "Xerox"
                                               ))->ManufacturerId;

        // BLACK AND WHITE
        $device = $masterDeviceMapper->fetchRow(array(
                                                     "mastdevice_manufacturer = ?" => $hp,
                                                     "printer_model = ?"           => "Laserjet 4250dtn"
                                                ));
        if ($device)
        {
            $replacementDevice                      = new Proposalgen_Model_ReplacementDevice();
            $replacementDevice->masterDeviceId      = $device->MasterDeviceId;
            $replacementDevice->replacementCategory = Proposalgen_Model_ReplacementDevice::REPLACMENT_BW;
            $replacementDevice->printSpeed          = 45;
            $replacementDevice->resolution          = 1200;
            $replacementDevice->monthlyRate         = 99;
            $replacementDeviceMapper->save($replacementDevice);
        }

        // BLACK AND WHITE
        $device = $masterDeviceMapper->fetchRow(array(
                                                     "mastdevice_manufacturer = ?" => $hp,
                                                     "printer_model = ?"           => "Laserjet P4015dn"
                                                ));
        if ($device)
        {
            $replacementDevice                      = new Proposalgen_Model_ReplacementDevice();
            $replacementDevice->masterDeviceId      = $device->MasterDeviceId;
            $replacementDevice->replacementCategory = Proposalgen_Model_ReplacementDevice::REPLACMENT_BW;
            $replacementDevice->printSpeed          = 52;
            $replacementDevice->resolution          = 1200;
            $replacementDevice->monthlyRate         = 149;
            $replacementDeviceMapper->save($replacementDevice);
        }
        // BLACK AND WHITE MFP
        $device = $masterDeviceMapper->fetchRow(array(
                                                     "mastdevice_manufacturer = ?" => $hp,
                                                     "printer_model = ?"           => "Laserjet M3035xs Mfp"
                                                ));
        if ($device)
        {
            $replacementDevice                      = new Proposalgen_Model_ReplacementDevice();
            $replacementDevice->masterDeviceId      = $device->MasterDeviceId;
            $replacementDevice->replacementCategory = Proposalgen_Model_ReplacementDevice::REPLACMENT_BWMFP;
            $replacementDevice->printSpeed          = 35;
            $replacementDevice->resolution          = 1200;
            $replacementDevice->monthlyRate         = 199;
            $replacementDeviceMapper->save($replacementDevice);
        }
        // COLOR
        $device = $masterDeviceMapper->fetchRow(array(
                                                     "mastdevice_manufacturer = ?" => $xerox,
                                                     "printer_model = ?"           => "ColorQube 8870dn"
                                                ));
        if ($device)
        {
            $replacementDevice                         = new Proposalgen_Model_ReplacementDevice();
            $replacementDevice->setMasterDeviceId      = $device->MasterDeviceId;
            $replacementDevice->setReplacementCategory = Proposalgen_Model_ReplacementDevice::REPLACMENT_COLOR;
            $replacementDevice->setPrintSpeed          = 40;
            $replacementDevice->setResolution          = 2400;
            $replacementDevice->setMonthlyRate         = 199;
            $replacementDeviceMapper->save($replacementDevice);
        }
        // COLOR MFP
        $device = $masterDeviceMapper->fetchRow(array(
                                                     "mastdevice_manufacturer = ?" => $xerox,
                                                     "printer_model = ?"           => "Phaser 8860 Mfp"
                                                ));
        if ($device)
        {
            $replacementDevice                      = new Proposalgen_Model_ReplacementDevice();
            $replacementDevice->masterDeviceId      = $device->MasterDeviceId;
            $replacementDevice->replacementCategory = Proposalgen_Model_ReplacementDevice::REPLACMENT_COLORMFP;
            $replacementDevice->printSpeed          = 30;
            $replacementDevice->resolution          = 2400;
            $replacementDevice->monthlyRate         = 249;
            $replacementDeviceMapper->save($replacementDevice);
        }
    }
}