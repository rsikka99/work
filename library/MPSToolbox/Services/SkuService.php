<?php

namespace MPSToolbox\Services;

use MPSToolbox\Forms\SkuImageForm;
use MPSToolbox\Forms\SkuAttributesForm;
use MPSToolbox\Forms\SkuQuoteForm;
use MPSToolbox\Forms\SkuDistributorsForm;
use MPSToolbox\Legacy\Entities\DealerEntity;
use Tangent\Logger\Logger;
use Zend_Form;

/**
 * Class SkuService
 * @package MPSToolbox\Services
 */
class SkuService
{
    /** @var  \stdClass */
    public $hardware;

    /**
     * @var array
     */
    public $data;

    protected $_dealerId;
    protected $_isAllowed;
    protected $_isAdmin;


    public function __construct ($properties, $sku, $dealerId, $isAllowed = false, $isAdmin = false)
    {
        $this->properties     = $properties;
        $this->_dealerId      = $dealerId;
        $this->_isAllowed     = $isAllowed;
        $this->_isAdmin       = $isAdmin;
        $this->sku = $sku;
    }

    /**
     * Shows the forms
     * @return array
     */

    public function getForms ()
    {
        $formsToShow = [];
        $formsToShow['skuAttributes'] = $this->getSkuAttributesForm();
        $formsToShow['skuQuote'] = $this->getSkuQuoteForm();
        $formsToShow['skuImage']  = $this->getSkuImageForm();
        $formsToShow['skuDistributors']  = $this->getSkuDistributorsForm();
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
        if (empty($data)) $data=[];

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

    public function uploadImage($upload) {
        $publicFilePath = '/img/sku/'.$upload['name'];
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

        $file = $this->sku['imageFile'];
        if ($file) {
            $publicFilePath = '/img/sku/'.$file;
            $filePath       = PUBLIC_PATH . $publicFilePath;
            unlink($filePath);
        }

        $file = $this->sku['id'].'_'.time().'.'.$ext;
        $publicFilePath = '/img/sku/'.$file;
        $filePath       = PUBLIC_PATH . $publicFilePath;
        rename($tmpFilePath, $filePath);
        return $file;
    }

    public function downloadImageFromImageUrl($url=null) {
        if (!$url) $url = $this->hardware->imageUrl;
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

        $file = $this->sku['imageFile'];
        if ($file) {
            $publicFilePath = '/img/sku/'.$file;
            $filePath       = PUBLIC_PATH . $publicFilePath;
            unlink($filePath);
        }

        $file = $this->sku['id'].'_'.time().'.'.$ext;
        $publicFilePath = '/img/sku/'.$file;
        $filePath       = PUBLIC_PATH . $publicFilePath;
        file_put_contents($filePath, file_get_contents($url));
        $this->sku['imageFile'] = $file;
    }

    /**
     * @return SkuAttributesForm
     */
    public function getSkuAttributesForm ()
    {
        if (!isset($this->_skuAttributesForm))
        {
            $this->_skuAttributesForm = new SkuAttributesForm($this->properties,  null, $this->_isAllowed);

            if ($this->data && !$this['sku'])
            {
                $this->_skuAttributesForm->populate($this->data);
            }
            else if ($this->sku)
            {
                $this->_skuAttributesForm->populate(json_decode($this->sku['properties'], true));
            }
        }

        return $this->_skuAttributesForm;
    }

    public function getSkuImageForm () {
        if (!isset($this->_imageForm)) {
            $this->_imageForm = new SkuImageForm(null, $this->_isAllowed);
            if ($this->sku) {
                $this->_imageForm->populate($this->sku);
            }
        }
        return $this->_imageForm;
    }

    public function getSkuDistributorsForm() {
        if (!isset($this->_distributorsForm)) {
            $this->_distributorsForm = new SkuDistributorsForm(null, $this->_isAllowed);
            if ($this->sku) {
                $this->_distributorsForm->skuId = $this->sku['id'];
                $this->_distributorsForm->populate($this->sku);
            }
        }
        return $this->_distributorsForm;
    }

    /**
     * Gets the Sku Quote Form
     *
     * @return SkuQuoteForm
     */
    public function getSkuQuoteForm ()
    {
        if (!isset($this->_skuQuoteForm))
        {
            $this->_skuQuoteForm = new SkuQuoteForm();
            if ($this->data && !$this->sku)
            {
                $this->_skuQuoteForm->populate($this->data);
            }
            else if ($this->sku && ($this->sku['id']>0))
            {
                $db=\Zend_Db_Table::getDefaultAdapter();
                $dealerId = DealerEntity::getDealerId();
                $line = $db->query('select * from dealer_sku where skuId='.intval($this->sku['id']).' and dealerId='.intval($dealerId))->fetch();
                $this->_skuQuoteForm->populate($line);
            }
        }

        return $this->_skuQuoteForm;
    }

}
