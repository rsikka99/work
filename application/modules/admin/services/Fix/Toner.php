<?php
/**
 * Class Admin_Service_Fix_Toner
 */
class Admin_Service_Fix_Toner
{
    /**
     * @var string
     */
    protected $_fileName;


    /**
     * @var Admin_Form_Fix_Toner
     */
    protected $_form;

    /**
     * @var string
     */
    public $errorMessages;


    /**
     *
     */
    public function __construct ()
    {

    }

    /**
     * Process upload will take Admin_Form_Fix_Toner values and process the upload,
     * the success is measured by the bool value returned. Error messages will be saved inside
     * the local member $errorMessages.
     *
     * @param $data
     *
     * @return bool success
     */
    public function processUpload ($data)
    {
        $importSuccessful = false;

        $this->_fileName = $this->getForm()->getUploadedFilename();
        if ($this->getForm()->isValid($data) && $this->_fileName !== false)
        {
            $values   = $this->getForm()->getValues();
            $dealerId = $values ['dealerId'];

            $filename = $this->getForm()->getUploadedFilename();

            $fileLines = file($filename, FILE_IGNORE_NEW_LINES);
            $lineCount = count($fileLines) - 1;

            $validHeaders = array('id', 'sku', 'yield', 'dealerSku', 'duplicateId');
            foreach (str_getcsv(array_shift($fileLines)) as $header)
            {
                if (!in_array($header, $validHeaders))
                {
                    $this->errorMessages = "Invalid Header Detected ($header)";

                    return false;
                }
            }

            $db = Zend_Db_Table::getDefaultAdapter();
            try
            {
                $db->beginTransaction();


                $duplicateToners = array();
                $fixableToners   = array();

                foreach ($fileLines as $line)
                {
                    // Turn the line into an assoc array for us
                    $csvLine = array_combine($validHeaders, str_getcsv($line));

                    // Only validate lines that were combined properly (meaning they weren't empty and had the same column count as the headers)
                    if ($csvLine !== false)
                    {
                        $isDuplicate = ($csvLine['duplicateId'] > 0);

                        if ($isDuplicate)
                        {
                            $duplicateToners[] = $csvLine;
                        }
                        else
                        {
                            $fixableToners[] = $csvLine;
                        }
                    }
                }

                $tonerMapper                = Proposalgen_Model_Mapper_Toner::getInstance();
                $deviceTonerMapper          = Proposalgen_Model_Mapper_DeviceToner::getInstance();
                $dealerTonerAttributeMapper = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance();

                foreach ($duplicateToners as $csvToner)
                {
                    // Find Toner Mappings For current Toner
                    $duplicateTonersMapping = $deviceTonerMapper->fetchDeviceTonersByTonerId($csvToner['id']);

                    // Find Toner Mappings For the real toner
                    $realTonersMapping = $deviceTonerMapper->fetchDeviceTonersByTonerId($csvToner['duplicateId']);

                    $masterDeviceIds    = array();
                    $newMasterDeviceIds = array();
                    foreach ($realTonersMapping as $deviceToner)
                    {
                        $masterDeviceIds[] = $deviceToner->master_device_id;
                    }

                    // Update real toner to have any mappings this toner has
                    foreach ($duplicateTonersMapping as $deviceToner)
                    {
                        if (!in_array($deviceToner->master_device_id, $masterDeviceIds))
                        {
                            $newDeviceToner           = clone $deviceToner;
                            $newDeviceToner->toner_id = $csvToner['duplicateId'];
                            $deviceTonerMapper->insert($newDeviceToner);
                        }
                    }

                    // Delete this toner
                    $tonerMapper->delete($csvToner['id']);
                }

                foreach ($fixableToners as $csvToner)
                {
                    $toner        = $tonerMapper->find($csvToner['id']);
                    $toner->yield = $csvToner['yield'];
                    $toner->sku   = $csvToner['sku'];

                    $tonerMapper->save($toner);

                    $dealerTonerAttribute = $dealerTonerAttributeMapper->find(array($toner->id, $dealerId));
                    if (!$dealerTonerAttribute instanceof Proposalgen_Model_Dealer_Toner_Attribute)
                    {
                        $dealerTonerAttribute            = new Proposalgen_Model_Dealer_Toner_Attribute();
                        $dealerTonerAttribute->dealerId  = $dealerId;
                        $dealerTonerAttribute->tonerId   = $toner->id;
                        $dealerTonerAttribute->dealerSku = $csvToner['dealerSku'];
                        $dealerTonerAttributeMapper->insert($dealerTonerAttribute);
                    }
                    else
                    {
                        $dealerTonerAttribute->dealerSku = $csvToner['dealerSku'];
                        $dealerTonerAttributeMapper->save($dealerTonerAttribute);
                    }

                }

                $db->commit();
                $importSuccessful = true;
            }
            catch (Exception $e)
            {
                $db->rollback();
                throw $e;
            }
        }

        return $importSuccessful;
    }

    /**
     * @return Proposalgen_Form_ImportRmsCsv
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Admin_Form_Fix_Toner(array('csv'), "1B", "8MB");
        }

        return $this->_form;
    }
}