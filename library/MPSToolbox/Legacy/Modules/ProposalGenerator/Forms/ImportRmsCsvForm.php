<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Forms;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsProviderMapper;
use Twitter_Bootstrap_Form_Horizontal;
use Zend_Form;
use Zend_Form_Element_File;

/**
 * Class ImportRmsCsvForm
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Forms
 */
class ImportRmsCsvForm extends Zend_Form
{
    /**
     * @var int
     */
    protected $_dealerId;

    /**
     * @var array
     */
    protected $_validFileExtensions = array();

    /**
     * @var int
     */
    protected $_minFileSize;

    /**
     * @var int
     */
    protected $_maxFileSize;

    /**
     * @param int        $dealerId
     * @param array      $validFileExtensions
     * @param int        $minFileSize
     * @param int        $maxFileSize
     * @param null|array $options
     */
    public function __construct ($dealerId, $validFileExtensions, $minFileSize = 1, $maxFileSize = 1024, $options = null)
    {
        $this->_validFileExtensions = $validFileExtensions;
        $this->_minFileSize         = $minFileSize;
        $this->_maxFileSize         = $maxFileSize;
        $this->_dealerId            = $dealerId;

        parent::__construct($options);
    }


    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->addElement('select', 'rmsProviderId', array(
            'label'        => 'RMS Provider',
            'required'     => true,
            'multiOptions' => RmsProviderMapper::getInstance()->fetchAllForDealerDropdown($this->_dealerId),
        ));

        $this->addElement('file', 'uploadFile', array(
            'label'       => 'Choose a file to upload',
            'destination' => $this->getView()->App()->uploadPath,
            'required'    => true,
            'accept'      => '.csv',
            'validators'  => array(
//                'Extension' => array('extension' => 'csv'),
//                'Count'     => array('count' => 1),
//                'File_Size' => array('min' => $this->_minFileSize, 'max' => $this->_maxFileSize)
            )
        ));

        $this->addElement('submit', 'performUpload', array(
            'label'  => 'Upload File <i class="fa fa-fw fa-upload"></i>',
            'ignore' => true,
        ));

        $this->addElement('submit', 'goBack', array(
            'label'  => '<i class="fa fa-fw fa-arrow-left"></i> Go Back',
            'ignore' => true,
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

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/fleet/upload-rms-csv.phtml'
                )
            )
        ));
    }
}