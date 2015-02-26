<?php

namespace MPSToolbox\Legacy\Modules\DealerManagement\Services;

use MPSToolbox\Legacy\Mappers\DealerBrandingMapper;
use MPSToolbox\Legacy\Models\DealerBrandingModel;
use MPSToolbox\Legacy\Modules\DealerManagement\Forms\DealerBrandingForm;
use Tangent\Service\BaseService;

/**
 * Class DealerBrandingService
 *
 * @package MPSToolbox\Legacy\Modules\DealerManagement\Services
 */
class DealerBrandingService extends BaseService
{
    const ERROR_DEALER_BRANDING_DOES_NOT_EXIST = 'ERROR_DEALER_BRANDING_DOES_NOT_EXIST';
    const ERROR_FORM_INVALID                   = 'ERROR_FORM_INVALID';
    /**
     * The form
     *
     * @var DealerBrandingForm
     */
    protected $_dealerBrandingForm;

    /**
     * Constructor
     */
    public function __construct ()
    {
        // Nothing to do right now
    }

    /**
     * Gets the form
     *
     * @return DealerBrandingForm
     */
    public function getDealerBrandingForm ()
    {
        if (!isset($this->_dealerBrandingForm))
        {
            $this->_dealerBrandingForm = new DealerBrandingForm();
        }

        return $this->_dealerBrandingForm;
    }

    /**
     * Handles creation
     *
     * @param array $data     The post data
     * @param int   $dealerId The dealer we are creating this for
     *
     * @return bool
     */
    public function create ($data, $dealerId)
    {
        $success      = false;
        $filteredData = $this->validateAndFilterData($data);
        if ($filteredData !== false)
        {
            $dealerBranding           = new DealerBrandingModel();
            $dealerBranding->dealerId = $dealerId;
            $dealerBranding->populate($filteredData);

            DealerBrandingMapper::getInstance()->insert($dealerBranding);
        }

        return $success;
    }

    /**
     * Handles updates
     *
     * @param array $data
     * @param int   $id
     *
     * @return bool
     */
    public function update ($data, $id)
    {
        $success              = false;
        $dealerBrandingMapper = DealerBrandingMapper::getInstance();
        $dealerBranding       = $dealerBrandingMapper->find($id);

        if ($dealerBranding instanceof DealerBrandingModel)
        {
            $filteredData = $this->validateAndFilterData($data);

            if ($filteredData !== false)
            {
                $dealerBranding->populate($filteredData);
                $dealerBrandingMapper->save($dealerBranding);

                $success = true;
            }
        }
        else
        {
            $this->addError(self::ERROR_DEALER_BRANDING_DOES_NOT_EXIST, 'A branding for this dealer was not found');
        }

        return $success;
    }

    /**
     * Handles deletion
     *
     * @param int $id The id to delete
     *
     * @return int The number of rows deleted.
     */
    public function delete ($id)
    {
        $success = false;
        if (DealerBrandingMapper::getInstance()->delete($id) > 0)
        {
            $success = true;
        }
        else
        {
            $this->addError(self::ERROR_DEALER_BRANDING_DOES_NOT_EXIST, 'A branding for this dealer was not found');
        }

        return $success;
    }

    /**
     * Validates the data with the form
     *
     * @param array $formData
     *            The array of data to validate
     *
     * @return array The array of valid and filtered data, or false on error.
     */
    protected function validateAndFilterData ($formData)
    {
        if ($this->_dealerBrandingForm->isValid($formData))
        {
            return $this->_dealerBrandingForm->getValues();
        }
        else
        {
            $this->addError(self::ERROR_FORM_INVALID, 'The form has errors');
        }

        return false;
    }
}