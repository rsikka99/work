<?php

namespace MPSToolbox\Legacy\Modules\Admin\Forms;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsProviderMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsProviderRmsProviderMapper;
use Zend_Form;

/**
 * Class DealerRmsProvidersForm
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Forms
 */
class DealerRmsProvidersForm extends Zend_Form
{

    public function init ()
    {
        $this->setMethod('POST');

        /**
         * Compatible Toner Vendor RmsProvider List
         */
        $rmsProviderRmsProviders = RmsProviderMapper::getInstance()->fetchAllForDropdown();
        $this->addElement('multiCheckbox', 'rmsProviderIds', array(
            'label'        => 'Compatible Vendors',
            'multiOptions' => $rmsProviderRmsProviders,
        ));

        /**
         * Form Actions
         */
        $this->addElement('submit', 'submit', array(
            'label'  => 'Save',
            'ignore' => true,
        ));

        $this->addElement('submit', 'cancel', array(
            'label'  => 'Cancel',
            'ignore' => true,
        ));


    }

    /**
     * @return $this|Zend_Form
     */
    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/admin/dealer-rms-providers-form.phtml'
                )
            )
        ));

        return $this;
    }
}