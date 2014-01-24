<?php

/**
 * class Tangent_Form_UploadFile
 * Upload File Form: Gets a file from the user. Pass valid file extensions in the constructor
 * Extends Zend_Form
 *
 * @see       Zend_Form
 *
 * @author    Lee Robert
 * @version   v1.0
 */
class Tangent_Form_UploadFile extends Zend_Form
{

    protected $_fileExtensions;

    /**
     *
     * Enter description here ...
     *
     * @param array        $fileExtensions An array of valid file extensions. If none are passed there will be no file extension validation
     * @param unknown_type $viewScript
     * @param unknown_type $options
     */
    public function __construct ($fileExtensions = null, $viewScript = null, $options = null)
    {

        if (isset($fileExtensions))
        {
            $this->_fileExtensions = $fileExtensions;
        }
        if (isset($viewScript))
        {
            $this->setDecorators(array(
                array(
                    'ViewScript',
                    array(
                        'viewScript' => $viewScript))));

            $this->setElementDecorators(array(
                'ViewHelper',
                array(
                    'Errors'),
                array(
                    'Label'),
                array(
                    'HtmlTag')));
        }
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setName('upload_file_form');
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $file = new Zend_Form_Element_File('upload_file');
        $file->setLabel('File');
        $file->setDestination(DATA_PATH . "/uploads");
        if (isset($this->_fileExtensions))
        {
            if (is_array($this->_fileExtensions))
            {
                foreach ($this->_fileExtensions as $ext)
                {
                    $file->addValidator(new Zend_Validate_File_Extension($ext));
                }
            }
            else
            {
                $file->addValidator(new Zend_Validate_File_Extension($this->_fileExtensions));
            }

        }
        $file->setDecorators(array(
            array(
                'File'),
            array(
                'Errors')));

        $submitButton = new Zend_Form_Element_Submit('Upload');

        $this->addElements(array(
            $file,
            $submitButton));
    }

    /**
     * @return the $_fileExtensions
     */
    public function getFileExtensions ()
    {
        return $this->_fileExtensions;
    }

    /**
     * @param field_type $_fileExtensions
     */
    public function setFileExtensions ($_fileExtensions)
    {
        $this->_fileExtensions = $_fileExtensions;

        return $this;
    }

}

?>