<?php

namespace MPSToolbox\Settings\Form;

use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Settings\Entities\DealerSettingsEntity;
use MPSToolbox\Settings\Entities\ShopSettingsEntity;

/**
 * Class QuoteSettingsForm
 *
 * @package MPSToolbox\Settings\Form
 */
class ShopSettingsForm extends \My_Form_Form
{

    public $suppliers = [];

    /**
     * Initializes the form with all the elements required
     */
    public function init ()
    {
        $this->setMethod('post');

        $this->addElement('text', 'shopifyName', [
            'label'    => 'Shopify subdomain',
            'required' => false,
            'description' => 'the first part of the domain name, eg. enter "example" when your domain is "example.myshopify.com"',
        ]);

        $this->addElement('text_float', 'hardwareMargin', [
            'label'    => 'Default Margin on Hardware',
            'required' => false,
        ]);

        $this->addElement('text_float', 'oemTonerMargin', [
            'label'    => 'Default Margin on OEM Supplies',
            'required' => false,
        ]);

        $this->addElement('text_float', 'compatibleTonerMargin', [
            'label'    => 'Default Margin on Compatible Supplies',
            'required' => false,
        ]);

        $this->addElement('text_float', 'api_key', [
            'label'    => 'API Key',
            'required' => false,
        ]);

        $this->addElement('text_float', 'api_secret', [
            'label'    => 'API Secret',
            'required' => false,
        ]);

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save',
        ]);

        // Add the cancel button
        $this->addElement('submit', 'cancel', [
            'ignore'         => true,
            'label'          => 'Cancel',
            'formnovalidate' => true,
        ]);

    }


    /**
     * @param DealerSettingsEntity $dealerSettings
     */
    public function populate($settings)
    {
        if ($settings instanceof DealerSettingsEntity)
        {
            $shopSettings = $settings->shopSettings;

            $settings = [
                'shopifyName' => $shopSettings->shopifyName,
                'hardwareMargin'   => $shopSettings->hardwareMargin,
                'oemTonerMargin'   => $shopSettings->oemTonerMargin,
                'compatibleTonerMargin'   => $shopSettings->compatibleTonerMargin,
            ];
        }

        if ($settings)
        {
            parent::populate($settings);
        }
    }

    /**
     * Returns a populated model
     *
     * @param null $model
     *
     * @return ShopSettingsEntity|null
     */
    public function getShopSettings ($model = null)
    {
        if (!$model instanceof ShopSettingsEntity)
        {
            $model = new ShopSettingsEntity();
            $model->rmsUri = '';
        }

        $model->shopifyName = $this->getValue('shopifyName');
        $model->hardwareMargin = $this->getValue('hardwareMargin');
        $model->oemTonerMargin = $this->getValue('oemTonerMargin');
        $model->compatibleTonerMargin = $this->getValue('compatibleTonerMargin');

        return $model;
    }

    /**
     * Overrides the parent to enforce a view script to render the form
     *
     * @return void|\Zend_Form
     */
    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/settings/shop-settings-form.phtml']]]);
    }
}
