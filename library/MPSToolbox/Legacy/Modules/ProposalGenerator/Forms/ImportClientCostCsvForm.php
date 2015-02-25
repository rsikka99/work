<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Forms;

use Twitter_Bootstrap_Form_Horizontal;
use Zend_Form_Element_File;

/**
 * Class ImportClientCostCsvForm
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Forms
 */
class ImportClientCostCsvForm extends Twitter_Bootstrap_Form_Horizontal
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
    public function __construct ($validFileExtensions, $minFileSize = 1, $maxFileSize = 8192, $options = null)
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
            'accept'      => '.csv',
            'validators'  => [
                'Extension' => ['extension' => 'csv'],
                'Count'     => ['count' => 1],
                'File_Size' => ['min' => $this->_minFileSize, 'max' => $this->_maxFileSize],
            ],
        ]);

        $this->addElement('button', 'performUpload', [
            'label' => 'Upload File',
            'class' => 'btn btn-primary',
            'type'  => 'submit',
        ]);

        $this->addElement('button', 'goBack', [
            'label' => 'Go Back',
            'class' => 'btn btn-default',
            'type'  => 'submit',
        ]);

        // Add the buttons the the form actions
        $this->addDisplayGroup(['performUpload', 'goBack'], 'actions', [
            'disableLoadDefaultDecorators' => true,
            'decorators'                   => ['Actions'],
        ]);
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