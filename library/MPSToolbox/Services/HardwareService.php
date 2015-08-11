<?php

namespace MPSToolbox\Services;

use MPSToolbox\Entities\ExtComputerEntity;
use MPSToolbox\Entities\ExtHardwareEntity;
use MPSToolbox\Forms\HardwareImageForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\DeviceAttributesForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HardwareQuoteForm;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use Zend_Form;

/**
 * Class ManageMasterDevicesService
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Services
 */
class HardwareService
{
    /**
     * @var int
     */
    public $hardwareId;

    /** @var  ExtHardwareEntity */
    public $hardware;

    /**
     * @var array
     */
    public $data;

    protected $_dealerId;
    protected $_isAllowed;
    protected $_isAdmin;
    public    $isQuoteDevice = false;


    /**
     * @param int  $hardwareId The id of the Master Device
     * @param int  $dealerId
     * @param bool $isAllowed
     * @param bool $isAdmin
     */
    public function __construct ($hardwareId, $dealerId, $isAllowed = false, $isAdmin = false)
    {
        $this->hardwareId     = $hardwareId;
        $this->_dealerId      = $dealerId;
        $this->_isAllowed     = $isAllowed;
        $this->_isAdmin       = $isAdmin;

        if ($hardwareId) $this->hardware = ExtComputerEntity::find($hardwareId);
    }

    /**
     * Shows the forms
     *
     * @param bool $showSupplies
     * @param bool $showDeviceAttributes
     * @param bool $showHardwareOptimization
     * @param bool $showHardwareQuote
     * @param bool $showAvailableOptions
     * @param bool $showHardwareConfigurations
     *
     * @return array
     */

    public function getForms ()
    {
        $formsToShow = [];
        #$formsToShow['deviceAttributes'] = $this->getDeviceAttributesForm();
        #$formsToShow['hardwareQuote'] = $this->getHardwareQuoteForm();
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


    /**
     * @param array $validatedData
     * @param bool  $approved
     *
     * @return bool|string
     */
    public function saveAttributes ($validatedData, $approved = false)
    {
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
            $publicFilePath = '/img/devices/'.$file;
            $filePath       = PUBLIC_PATH . $publicFilePath;
            unlink($filePath);
        }

        $file = $this->hardware->getId().'_'.time().'.'.$ext;
        $publicFilePath = '/img/devices/'.$file;
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
            $publicFilePath = '/img/devices/'.$file;
            $filePath       = PUBLIC_PATH . $publicFilePath;
            unlink($filePath);
        }

        $file = $this->hardware->getId().'_'.time().'.'.$ext;
        $publicFilePath = '/img/devices/'.$file;
        $filePath       = PUBLIC_PATH . $publicFilePath;
        file_put_contents($filePath, file_get_contents($url));
        $this->hardware->setImageFile($file);
    }

    /**
     * This saves a hardware quote
     *
     * @param $validatedData
     *
     * @return boolean
     */
    public function saveHardwareQuote ($validatedData)
    {
        return true;
    }


    /**
     * Gets the Device Attributes Form
     *
     * @return DeviceAttributesForm
     */
    public function getDeviceAttributesForm ()
    {
        if (!isset($this->_deviceAttributesForm))
        {
            $this->_deviceAttributesForm = new DeviceAttributesForm(null, $this->_isAllowed);
            $masterDevice                = MasterDeviceMapper::getInstance()->find($this->masterDeviceId);

            if ($this->data && !$masterDevice)
            {
                $this->_deviceAttributesForm->populate($this->data);
            }
            else if ($masterDevice)
            {
                $this->_deviceAttributesForm->populate($masterDevice->toArray());
            }
        }

        return $this->_deviceAttributesForm;
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
            $device                   = DeviceMapper::getInstance()->find([$this->masterDeviceId, $this->_dealerId]);

            if ($device)
            {
                $this->_hardwareQuoteForm->populate($device->toArray());
                $this->_hardwareQuoteForm->populate(['isSelling' => true]);
                $this->isQuoteDevice = true;
            }
        }

        return $this->_hardwareQuoteForm;
    }

}
