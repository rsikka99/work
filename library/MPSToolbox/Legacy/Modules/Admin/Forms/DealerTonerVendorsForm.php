<?php

namespace MPSToolbox\Legacy\Modules\Admin\Forms;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use Zend_Form;

/**
 * Class DealerTonerVendorsForm
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Forms
 */
class DealerTonerVendorsForm extends Zend_Form
{

    public function init ()
    {
        $this->setMethod('POST');

        /**
         * Compatible Toner Vendor Manufacturer List
         */
        $tonerVendorManufacturers = TonerVendorManufacturerMapper::getInstance()->fetchAllForDropdown();
        $this->addElement('multiCheckbox', 'manufacturerIds', [
            'label'        => 'Compatible Vendors',
            'multiOptions' => $tonerVendorManufacturers,
        ]);

        /**
         * Form Actions
         */
        $this->addElement('submit', 'submit', [
            'label'  => 'Save',
            'ignore' => true,
        ]);

        $this->addElement('submit', 'cancel', [
            'label'  => 'Cancel',
            'ignore' => true,
        ]);


    }

    /**
     * @return $this|Zend_Form
     */
    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/admin/dealer-toner-vendors-form.phtml']]]);

        return $this;
    }
}