<?php

/**
 * Class Proposalgen_Form_ImportClientCostCsv
 */
class Proposalgen_Form_ImportClientCostCsv extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * @var array
     */
    protected $_validFileExtensions = array();
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

        $this->addElement('file', 'uploadFile', array(
            'label'       => 'Choose a file to upload',
            'destination' => $this->getView()->App()->uploadPath,
            'required'    => true,
            'accept'      => '.csv',
            'validators'  => array(
                'Extension' => array('extension' => 'csv'),
                'Count'     => array('count' => 1),
                'File_Size' => array('min' => $this->_minFileSize, 'max' => $this->_maxFileSize)
            )
        ));

        $this->addElement('button', 'performUpload', array(
            'label' => 'Upload File',
            'class' => 'btn btn-primary',
            'type'  => 'submit',
        ));

        $this->addElement('button', 'goBack', array(
            'label' => 'Go Back',
            'class' => 'btn btn-inverse',
            'type'  => 'submit',
        ));

        // Add the buttons the the form actions
        $this->addDisplayGroup(array('performUpload', 'goBack'), 'actions', array(
            'disableLoadDefaultDecorators' => true,
            'decorators'                   => array(
                'Actions'
            )
        ));
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