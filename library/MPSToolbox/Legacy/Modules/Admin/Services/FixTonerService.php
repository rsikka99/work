<?php
namespace MPSToolbox\Legacy\Modules\Admin\Services;

use Exception;
use MPSToolbox\Legacy\Modules\Admin\Forms\FixTonerForm;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Forms\ImportRmsCsvForm;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceTonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerTonerAttributeModel;
use Zend_Db_Table;

/**
 * Class FixTonerService
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Services
 */
class FixTonerService
{
    /**
     * @var string
     */
    protected $_fileName;


    /**
     * @var FixTonerForm
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
     * Process upload will take MPSToolbox\Legacy\Modules\Admin\Forms\FixTonerForm values and process the upload,
     * the success is measured by the bool value returned. Error messages will be saved inside
     * the local member $errorMessages.
     *
     * @param $data
     *
     * @throws \Exception
     * @throws \Zend_Form_Exception
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

            $validHeaders = ['id', 'sku', 'yield', 'dealerSku', 'duplicateId'];
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


                $duplicateToners = [];
                $fixableToners   = [];

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

                $tonerMapper                = TonerMapper::getInstance();
                $deviceTonerMapper          = DeviceTonerMapper::getInstance();
                $dealerTonerAttributeMapper = DealerTonerAttributeMapper::getInstance();

                foreach ($duplicateToners as $csvToner)
                {
                    // Find Toner Mappings For current Toner
                    $duplicateTonersMapping = $deviceTonerMapper->fetchDeviceTonersByTonerId($csvToner['id']);

                    // Find Toner Mappings For the real toner
                    $realTonersMapping = $deviceTonerMapper->fetchDeviceTonersByTonerId($csvToner['duplicateId']);

                    $masterDeviceIds = [];
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
                    $oldSku       = $toner->sku;
                    $toner->yield = $csvToner['yield'];
                    $toner->sku   = $csvToner['sku'];

                    $tonerMapper->save($toner);

                    if (strlen($csvToner['dealerSku']) > 2)
                    {
                        $dealerTonerAttribute = $dealerTonerAttributeMapper->find([$toner->id, $dealerId]);
                        if (!$dealerTonerAttribute instanceof DealerTonerAttributeModel)
                        {
                            try
                            {
                                $dealerTonerAttribute            = new DealerTonerAttributeModel();
                                $dealerTonerAttribute->dealerId  = $dealerId;
                                $dealerTonerAttribute->tonerId   = $toner->id;
                                $dealerTonerAttribute->dealerSku = $csvToner['dealerSku'];
                                $dealerTonerAttributeMapper->insert($dealerTonerAttribute);
                            }
                            catch (Exception $e)
                            {
                            }
                        }
                        else
                        {
                            try
                            {
                                $dealerTonerAttribute->dealerSku = $csvToner['dealerSku'];
                                $dealerTonerAttributeMapper->save($dealerTonerAttribute);
                            }
                            catch (Exception $e)
                            {
                            }
                        }
                    }
                }

                $db->commit();
                $importSuccessful = true;
            }
            catch (Exception $e)
            {
                $db->rollback();
                throw new Exception(implode("|", $csvToner) . "OLD SKU: $oldSku", 0, $e);
            }
        }

        return $importSuccessful;
    }

    /**
     * @return ImportRmsCsvForm
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new FixTonerForm(['csv'], "1B", "8MB");
        }

        return $this->_form;
    }
}