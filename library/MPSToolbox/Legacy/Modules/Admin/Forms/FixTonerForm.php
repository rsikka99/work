<?php

namespace MPSToolbox\Legacy\Modules\Admin\Forms;

use MPSToolbox\Legacy\Mappers\DealerMapper;
use Zend_Form_Element_File;
use Zend_Form;

/**
 * Class FixTonerForm
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Forms
 */
class FixTonerForm extends Zend_Form
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

        $dealers    = DealerMapper::getInstance()->fetchAll();
        $dealerList = array(0 => "Select Company...");
        foreach ($dealers as $dealer)
        {
            $dealerList[$dealer->id] = $dealer->dealerName;
        }

        $this->addElement('select', 'dealerId', array(
            'label'        => 'Owner Of Dealer SKU',
            'required'     => true,
            'multiOptions' => $dealerList
        ));

        $this->addElement('file', 'uploadFile', array(
            'label'       => 'Choose a file to upload',
            'destination' => $this->getView()->App()->uploadPath,
            'required'    => true,
            'accept'      => '.csv',
            'validators'  => array(
                'Extension' => array('extension' => 'csv'),
                'Count'     => array('count' => 1),
                'File_Size' => array('min' => $this->_minFileSize,
                                     'max' => $this->_maxFileSize)
            )
        ));

        $this->addElement('button', 'performUpload', array(
            'label' => 'Upload File',
            'type'  => 'submit',
        ));

        $this->addElement('button', 'cancel', array(
            'type'           => 'submit',
            'label'          => 'Cancel',
            'formnovalidate' => true,
        ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/admin/fix-toner-form.phtml'
                )
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