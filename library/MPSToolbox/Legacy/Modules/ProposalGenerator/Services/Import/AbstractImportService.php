<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import;

use Zend_Config;
use Zend_File_Transfer_Adapter_Http;
use Zend_Filter_Exception;
use Zend_Filter_Input;

/**
 * Class AbstractImportService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import
 */
class AbstractImportService
{
    /**
     * Headers that are part of the upload
     *
     * @var array
     */
    protected $csvHeaders = [];

    /**
     * Zend Filter that will filter and validate input data
     *
     * @var Zend_Filter_Input
     */
    protected $_inputFilter;

    /**
     * Common filters that are used multitude of times.
     *
     * @var array
     */
    protected $_filters = [];

    /**
     * Common validators that are used multitude of times
     *
     * @var array
     */
    protected $_validators = [];

    /**
     * @var Zend_File_Transfer_Adapter_Http
     */
    protected $_upload;

    /**
     * The headers that we receive from the import.
     *
     * @var array
     */
    public $importHeaders = [];

    /**
     * @var
     */
    public $importFile;


    /**
     * @return array
     */
    public function validatedHeaders ()
    {
        // Find a matching array
        if ((count(array_diff($this->importHeaders, $this->csvHeaders)) == 0) && (count(array_diff($this->csvHeaders, $this->importHeaders)) == 0))
        {
            return true;
        }

        return false;
    }

    /**
     * @param $config Zend_Config
     *
     * @return resource
     */
    public function getValidFile ($config)
    {
        $this->_upload = new Zend_File_Transfer_Adapter_Http();
        $this->_upload->setDestination($config->app->uploadPath);
        // Limit the amount of files to maximum 1
        $this->_upload->addValidator('Count', false, 1);
        $this->_upload->getValidator('Count')->setMessage('<span class="warning">*</span> You are only allowed to upload 1 file at a time.');
        // Limit the size of all files to be uploaded to maximum 4MB and
        $this->_upload->addValidator('FilesSize', false, ['min' => '0', 'max' => '4MB']);
        $this->_upload->getValidator('FilesSize')->setMessage('<span class="warning">*</span> File size must less than 4MB.');

        if ($this->_upload->receive())
        {
            $this->importFile    = fopen($this->_upload->getFileName(), "r");
            $this->importHeaders = fgetcsv($this->importFile);

            return true;
        }

        return $this->_upload->getErrors();
    }

    /**
     * Closes files and unlink upload from the system
     */
    public function closeFiles ()
    {
        fclose($this->importFile);
        unlink($this->_upload->getFileName());
    }

    /**
     * @param $data
     *
     * @return array|mixed|string
     */
    public function processValidation ($data)
    {
        try
        {
            $this->_inputFilter->setData($data);

            return $this->_inputFilter->isValid() ? $this->_inputFilter->getEscaped() : ["error" => ["invalid" => $this->_inputFilter->getInvalid()]];
        }
        catch (Zend_Filter_Exception $e)
        {
            return "Zend Filter Exception caught.";
        }
    }
}