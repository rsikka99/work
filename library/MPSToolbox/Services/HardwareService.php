<?php

namespace MPSToolbox\Services;

use MPSToolbox\Entities\DealerEntity;
use MPSToolbox\Entities\ExtComputerEntity;
use MPSToolbox\Entities\ExtDealerHardwareEntity;
use MPSToolbox\Entities\ExtHardwareEntity;
use MPSToolbox\Forms\HardwareImageForm;
use MPSToolbox\Forms\HardwareAttributesForm;
use MPSToolbox\Forms\HardwareQuoteForm;
use Tangent\Logger\Logger;
use Zend_Form;

/**
 * Class HardwareService
 * @package MPSToolbox\Services
 */
class HardwareService
{
    /** @var  ExtHardwareEntity */
    public $hardware;

    /**
     * @var array
     */
    public $data;
    public $type;

    protected $_dealerId;
    protected $_isAllowed;
    protected $_isAdmin;


    /**
     * @param int  $hardwareId
     * @param int  $dealerId
     * @param bool $isAllowed
     * @param bool $isAdmin
     */
    public function __construct ($hardware, $dealerId, $isAllowed = false, $isAdmin = false, $type = 'computers')
    {
        $this->_dealerId      = $dealerId;
        $this->_isAllowed     = $isAllowed;
        $this->_isAdmin       = $isAdmin;
        $this->type = $type;
        $this->hardware = $hardware;
    }

    /**
     * Shows the forms
     * @return array
     */

    public function getForms ()
    {
        $formsToShow = [];
        $formsToShow['hardwareAttributes'] = $this->getHardwareAttributesForm();
        $formsToShow['hardwareQuote'] = $this->getHardwareQuoteForm();
        $formsToShow['hardwareImage']  = $this->getHardwareImageForm();
        return $formsToShow;
    }

    /**
     * @param Zend_Form $form
     * @param string    $data []
     * @param string    $formName
     *
     * @return array|null
     */
    public function validateData ($form, $data, $formName)
    {
        $json = null;
        $form->populate($data);

        if ($form->isValid($data))
        {
            $json = $form->getValues();
        }
        else
        {
            $json = [];

            foreach ($form->getMessages() as $errorElementName => $errorElement)
            {
                $count = 0;

                foreach ($errorElement as $elementErrorMessage)
                {
                    $count++;
                    $json['errorMessages'][$errorElementName] = $elementErrorMessage;
                    $json['name']                             = $formName;
                }
            }
        }

        return $json;
    }

    public function save($validData) {
        try
        {
            $dealer = DealerEntity::find($this->_dealerId);
            $dealerHardware = ExtDealerHardwareEntity::findExtDealerHardware($this->hardware,$dealer);
            if (!$dealerHardware) {
                $dealerHardware = new ExtDealerHardwareEntity();
                $dealerHardware->setHardware($this->hardware);
                $dealerHardware->setDealer($dealer);
            }

            $dealerHardware->populate($validData['hardwareQuote']);
            $dealerHardware->save();
        }
        catch (\Exception $e)
        {
            Logger::logException($e);
            return false;
        }

        return true;
    }

    public function uploadImage($upload) {
        $publicFilePath = '/img/hardware/'.$upload['name'];
        $tmpFilePath       = PUBLIC_PATH . $publicFilePath;
        move_uploaded_file($upload['tmp_name'], $tmpFilePath);

        $image_info = @getimagesize($tmpFilePath);
        if (!$image_info || ($image_info[0]<1)) {
            unlink($tmpFilePath);
            return;
        }
        $ext=null;
        switch ($image_info['mime']) {
            case 'image/jpeg' :
                $ext='jpg';
                break;
            case 'image/png' :
                $ext='png';
                break;
            case 'image/gif' :
                $ext='gif';
                break;
        }
        if (!$ext) {
            unlink($tmpFilePath);
            return;
        }

        $file = $this->hardware->getImageFile();
        if ($file) {
            $publicFilePath = '/img/hardware/'.$file;
            $filePath       = PUBLIC_PATH . $publicFilePath;
            unlink($filePath);
        }

        $file = $this->hardware->getId().'_'.time().'.'.$ext;
        $publicFilePath = '/img/hardware/'.$file;
        $filePath       = PUBLIC_PATH . $publicFilePath;
        rename($tmpFilePath, $filePath);
        $this->hardware->setImageFile($file);
    }

    public function downloadImageFromImageUrl($url=null) {
        if (!$url) $url = $this->hardware->getImageUrl();
        if (!$url) return;
        $image_info = @getimagesize($url);
        if (!$image_info || ($image_info[0]<1)) return;
        $ext=null;
        switch ($image_info['mime']) {
            case 'image/jpeg' :
                $ext='jpg';
                break;
            case 'image/png' :
                $ext='png';
                break;
            case 'image/gif' :
                $ext='gif';
                break;
        }
        if (!$ext) return;

        $file = $this->hardware->getImageFile();
        if ($file) {
            $publicFilePath = '/img/hardware/'.$file;
            $filePath       = PUBLIC_PATH . $publicFilePath;
            unlink($filePath);
        }

        $file = $this->hardware->getId().'_'.time().'.'.$ext;
        $publicFilePath = '/img/hardware/'.$file;
        $filePath       = PUBLIC_PATH . $publicFilePath;
        file_put_contents($filePath, file_get_contents($url));
        $this->hardware->setImageFile($file);
    }

    /**
     * @return HardwareAttributesForm
     */
    public function getHardwareAttributesForm ()
    {
        if (!isset($this->_hardwareAttributesForm))
        {
            $cls = "MPSToolbox\\Forms\\".ucfirst($this->type).'AttributesForm';
            $this->_hardwareAttributesForm = new $cls(null, $this->_isAllowed);

            if ($this->data && !$this->hardware)
            {
                $this->_hardwareAttributesForm->populate($this->data);
            }
            else if ($this->hardware)
            {
                $this->_hardwareAttributesForm->populate($this->hardware->toArray());
            }
        }

        return $this->_hardwareAttributesForm;
    }

    public function getHardwareImageForm () {
        if (!isset($this->_imageForm)) {
            $this->_imageForm = new HardwareImageForm(null, $this->_isAllowed);
            if ($this->hardware) {
                $this->_imageForm->populate($this->hardware->toArray());
            }
        }
        return $this->_imageForm;
    }

    /**
     * Gets the Hardware Quote Form
     *
     * @return HardwareQuoteForm
     */
    public function getHardwareQuoteForm ()
    {
        if (!isset($this->_hardwareQuoteForm))
        {
            $this->_hardwareQuoteForm = new HardwareQuoteForm();
            if ($this->data && !$this->hardware)
            {
                $this->_hardwareQuoteForm->populate($this->data);
            }
            else if ($this->hardware)
            {
                $dealer = DealerEntity::find($this->_dealerId);
                $dealerHardware = ExtDealerHardwareEntity::findExtDealerHardware($this->hardware,$dealer);
                $this->_hardwareQuoteForm->populate($dealerHardware->toArray());
            }
        }

        return $this->_hardwareQuoteForm;
    }

}
