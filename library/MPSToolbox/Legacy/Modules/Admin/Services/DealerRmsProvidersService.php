<?php
namespace MPSToolbox\Legacy\Modules\Admin\Services;

use MPSToolbox\Legacy\Modules\Admin\Mappers\DealerRmsProviderMapper;
use MPSToolbox\Legacy\Modules\Admin\Models\DealerRmsProviderModel;
use Tangent\Service\BaseService;

/**
 * Class DealerRmsProvidersService
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Services
 */
class DealerRmsProvidersService extends BaseService
{
    const ERROR_RMS_PROVIDER_CREATE_FAIL = 'ERROR_RMS_PROVIDER_CREATE_FAIL';
    const ERROR_RMS_PROVIDER_DELETE_FAIL = 'ERROR_RMS_PROVIDER_DELETE_FAIL';

    /**
     * Updates a dealers rms providers
     *
     * @param int   $dealerId
     * @param array $data
     */
    public function updateDealerRmsProviders ($dealerId, $data)
    {
        $currentRmsProviderRmsProviderIds = $this->getDealerRmsProvidersAsArray($dealerId);
        $dealerRmsProviderMapper          = DealerRmsProviderMapper::getInstance();

        if (isset($data['rmsProviderIds']))
        {
            foreach ($data['rmsProviderIds'] as $newRmsProviderId)
            {
                $create = true;
                foreach ($currentRmsProviderRmsProviderIds as $oldRmsProviderId)
                {
                    if ((int)$newRmsProviderId === (int)$oldRmsProviderId)
                    {
                        $create = false;
                        break;
                    }
                }

                if ($create)
                {
                    $dealerRmsProvider                = new DealerRmsProviderModel();
                    $dealerRmsProvider->dealerId      = $dealerId;
                    $dealerRmsProvider->rmsProviderId = $newRmsProviderId;
                    $dealerRmsProviderMapper->insert($dealerRmsProvider);
                }
            }

            foreach ($currentRmsProviderRmsProviderIds as $oldRmsProviderId)
            {
                $delete = true;
                foreach ($data['rmsProviderIds'] as $newRmsProviderId)
                {
                    if ((int)$newRmsProviderId === (int)$oldRmsProviderId)
                    {
                        $delete = false;
                        break;
                    }
                }

                if ($delete)
                {
                    $dealerRmsProviderMapper->delete(array((int)$dealerId, $oldRmsProviderId));
                }
            }
        }
        else
        {
            foreach ($currentRmsProviderRmsProviderIds as $oldRmsProviderId)
            {
                $dealerRmsProviderMapper->delete(array((int)$dealerId, $oldRmsProviderId));
            }
        }
    }

    /**
     * Gets the rms provider ids for a given dealer
     *
     * @param int $dealerId
     *
     * @return array
     */
    public function getDealerRmsProvidersAsArray ($dealerId)
    {
        $rmsProviderArray = array();

        foreach (DealerRmsProviderMapper::getInstance()->fetchAllForDealer($dealerId) as $dealerRmsProvider)
        {
            $rmsProviderArray[] = $dealerRmsProvider->rmsProviderId;
        }

        return $rmsProviderArray;
    }

}