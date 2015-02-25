<?php

namespace MPSToolbox\Legacy\Modules\DealerManagement\Forms;

use Zend_Form;
use Zend_Form_Element_File;

/**
 * Class ImportLeaseCsvForm
 *
 * @package MPSToolbox\Legacy\Modules\DealerManagement\Forms
 */
class ImportLeaseCsvForm extends Zend_Form
{
    /**
     * @var array
     */
    protected $_validFileExtensions = [];
    protected $_minFileSize;
    protected $_maxFileSize;

    /**
     * @param array      $validFileExtensions
     * @param int        $minFileSize
     * @param int        $maxFileSize
     * @param null|array $options
     */
    public function __construct ($validFileExtensions, $minFileSize = 1, $maxFileSize = 1024, $options = null)
    {
        $this->_validFileExtensions = $validFileExtensions;
        $this->_minFileSize         = $minFileSize;
        $this->_maxFileSize         = $maxFileSize;

        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('file', 'uploadFile', [
            'label'       => 'Choose a file to upload',
            'destination' => $this->getView()->App()->uploadPath,
            'required'    => true,
            'validators'  => [
                'Extension' => ['extension' => 'csv'],
                'Count'     => ['count' => 1],
                'File_Size' => ['min' => $this->_minFileSize, 'max' => $this->_maxFileSize],
            ],
        ]);

        $this->addElement('submit', 'performUpload', [
            'label' => 'Upload File',
        ]);

        $this->addElement('submit', 'cancel', [
            'label'          => 'Cancel',
            'formnovalidate' => true,
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/dealermanagement/import-lease-csv-form.phtml']]]);
    }


    /**
     * Gets the uploaded filename or false if the upload failed
     *
     * @return bool|string
     */
    public function getUploadedFilename ()
    {
        $filename = false;
        /* @var $uploadFileElement Zend_Form_Element_File */
        $uploadFileElement = $this->uploadFile;
        if ($uploadFileElement->isUploaded())
        {
            if ($uploadFileElement->receive())
            {
                $filename = $uploadFileElement->getFileName();
            }
        }

        return $filename;
    }
}