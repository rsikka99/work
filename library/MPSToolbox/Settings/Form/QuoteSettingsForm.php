<?php

namespace MPSToolbox\Settings\Form;

use MPSToolbox\Settings\Entities\QuoteSettingsEntity as QuoteSettingsEntity;

/**
 * Class QuoteSettingsForm
 *
 * @package MPSToolbox\Settings\Form
 */
class QuoteSettingsForm extends \Zend_Form
{
    /**
     * Initializes the form with all the elements required
     */
    public function init ()
    {
        $this->setMethod('post');

        /**
         * Hardware Quote Default Settings
         */
        $this->addElement('text', 'defaultDeviceMargin', array(
            'label'    => 'Default Margin on Devices',
            'required' => true,
        ));

        $this->addElement('text', 'defaultPageMargin', array(
            'label'    => 'Default Margin on Pages',
            'required' => true,
        ));

        /**
         * Form Actions
         */
        $this->addElement('submit', 'save', array(
            'label' => 'Submit',
            'class' => 'btn btn-primary',
        ));

        $this->addElement('submit', 'cancel', array(
            'label' => 'Cancel',
            'class' => 'btn btn-default'
        ));
    }


    /**
     * @param array|QuoteSettingsEntity $quoteSettings
     */
    public function populateQuoteSettings ($quoteSettings)
    {
        if ($quoteSettings instanceof QuoteSettingsEntity)
        {
            $quoteSettings = array(
                'defaultDeviceMargin' => $quoteSettings->defaultDeviceMargin,
                'defaultPageMargin'   => $quoteSettings->defaultPageMargin,
            );
        }

        if ($quoteSettings)
        {
            $this->populate($quoteSettings);
        }
    }

    /**
     * Returns a populated model
     *
     * @param null $model
     *
     * @return QuoteSettingsEntity|null
     */
    public function getQuoteSettings ($model = null)
    {
        if (!$model instanceof QuoteSettingsEntity)
        {
            $model = new QuoteSettingsEntity();
        }

        $model->defaultDeviceMargin = $this->getValue('defaultDeviceMargin');
        $model->defaultPageMargin   = $this->getValue('defaultPageMargin');

        return $model;
    }

    /**
     * Overrides the parent to enforce a view script to render the form
     *
     * @return void|\Zend_Form
     */
    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/client-settings-form.phtml'
                )
            )
        ));
    }
}