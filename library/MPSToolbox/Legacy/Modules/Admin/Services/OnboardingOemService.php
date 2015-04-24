<?php
namespace MPSToolbox\Legacy\Modules\Admin\Services;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerTonerAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ManufacturerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use Zend_Filter_Input;
use Zend_Filter_StringTrim;

/**
 * Class OnboardingOemService
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Services
 */
class OnboardingOemService extends OnboardingAbstractService
{
    const COLUMN_TONER_MANUFACTURER = 'tonerManufacturer';
    const COLUMN_TONER_SKU          = 'tonerSku';
    const COLUMN_TONER_DEALER_SKU   = 'tonerDealerSku';
    const COLUMN_TONER_COLOR        = 'tonerColor';
    const COLUMN_TONER_DEALER_COST  = 'tonerDealerCost';
    const COLUMN_TONER_YIELD        = 'tonerYield';

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = [
        'Manufacturer'     => self::COLUMN_TONER_MANUFACTURER,
        'Manufacturer Sku' => self::COLUMN_TONER_SKU,
        'Dealer Sku'       => self::COLUMN_TONER_DEALER_SKU,
        'Color'            => self::COLUMN_TONER_COLOR,
        'Cost'             => self::COLUMN_TONER_DEALER_COST,
        'Yield'            => self::COLUMN_TONER_YIELD,
    ];

    /**
     * The fields that must be present within the CSV format
     *
     * @var array
     */
    protected $_requiredHeaders = [
        self::COLUMN_TONER_MANUFACTURER => true,
        self::COLUMN_TONER_SKU          => true,
        self::COLUMN_TONER_DEALER_SKU   => true,
        self::COLUMN_TONER_COLOR        => true,
        self::COLUMN_TONER_DEALER_COST  => true,
        self::COLUMN_TONER_YIELD        => true,
    ];


    /**
     * The constructor for this object.
     * Sets up filters and validators for our data.
     */
    public function __construct ()
    {
        $filters = [
            '*'                            => [
                'StringTrim',
            ],
            self::COLUMN_TONER_DEALER_COST => [
                'StringTrim',
                'Float',
            ],
            self::COLUMN_TONER_YIELD       => [
                'StringTrim',
                'Float',
            ],
        ];

        $this->_inputFilter = new Zend_Filter_Input($filters, []);

        // If we haven't set a filter for the data, use this one
        $this->_inputFilter->setDefaultEscapeFilter(new Zend_Filter_StringTrim());
    }

    public function processFile ($filename, $dealerId)
    {
        $csvLines = $this->getCsvContents($filename);

        $messages = [];

        if (is_array($csvLines) && count($csvLines) > 0)
        {
            $dealerTonerAttributeMapper = DealerTonerAttributeMapper::getInstance();
            $manufacturerMapper         = ManufacturerMapper::getInstance();
            $manufacturers              = [];
            $tonerColors                = TonerColorModel::$ColorNames;
            $tonerMapper                = TonerMapper::getInstance();

            foreach ($csvLines as $csvLine)
            {
                /**
                 * Compatible Manufacturer
                 */
                $manufacturerName = $csvLine[self::COLUMN_TONER_MANUFACTURER];

                if (!isset($manufacturers[$manufacturerName]))
                {
                    $manufacturerList = $manufacturerMapper->searchByName($manufacturerName);
                    $manufacturer     = reset($manufacturerList);

                    if ($manufacturer instanceof ManufacturerModel)
                    {
                        $manufacturers[$manufacturerName] = $manufacturer->id;
                    }
                    else
                    {
                        $messages[]                       = sprintf('The manufacturer "%1$s" can not be found. Check your spelling or create the manufacturer in the system.', $manufacturerName);
                        $manufacturers[$manufacturerName] = false;
                    }
                }

                /**
                 * Toner Color
                 */
                $compTonerColorName = $csvLine[self::COLUMN_TONER_COLOR];
                $validTonerColor    = false;
                foreach ($tonerColors as $tonerColorName)
                {
                    if (strcasecmp($tonerColorName, $compTonerColorName) === 0)
                    {
                        $validTonerColor = true;
                        break;
                    }
                }

                if (!$validTonerColor)
                {
                    $messages[] = sprintf('The toner color "%1$s" is not valid. Valid Names are: "%2$s"', $compTonerColorName, implode('", "', $tonerColors));
                }
            }

            /**
             * As long as we have no messages we can proceed and attempt to update pricing
             */
            if (count($messages) < 1)
            {
                foreach ($csvLines as $csvLine)
                {
                    $manufacturerName = $csvLine[self::COLUMN_TONER_MANUFACTURER];
                    $tonerSku         = $csvLine[self::COLUMN_TONER_SKU];
                    $dealerCost       = $csvLine[self::COLUMN_TONER_DEALER_COST];
                    $dealerSku        = $csvLine[self::COLUMN_TONER_DEALER_SKU];

                    /**
                     * Get the color of the toner
                     */
                    $compTonerColorName = $csvLine[self::COLUMN_TONER_COLOR];
                    $oemTonerColorId    = 1;
                    foreach ($tonerColors as $tonerColorId => $tonerColorName)
                    {
                        if (strcasecmp($tonerColorName, $compTonerColorName) === 0)
                        {
                            $oemTonerColorId = $tonerColorId;
                            break;
                        }
                    }

                    $toner = $tonerMapper->fetchBySku($tonerSku, $manufacturers[$manufacturerName]);
                    if ($toner instanceof TonerModel)
                    {
                        if ($toner->tonerColorId == $oemTonerColorId)
                        {
                            $dealerTonerAttribute = $dealerTonerAttributeMapper->findTonerAttributeByTonerId($toner->id, $dealerId);
                            if ($dealerTonerAttribute instanceof DealerTonerAttributeModel)
                            {
                                $dealerTonerAttribute->cost = $dealerCost;

                                if (strlen($dealerSku) > 0)
                                {
                                    $dealerTonerAttribute->dealerSku = $dealerSku;
                                }

                                $dealerTonerAttributeMapper->save($dealerTonerAttribute);
                            }
                            else
                            {
                                $dealerTonerAttribute           = new DealerTonerAttributeModel();
                                $dealerTonerAttribute->tonerId  = $toner->id;
                                $dealerTonerAttribute->dealerId = $dealerId;
                                $dealerTonerAttribute->cost     = $dealerCost;

                                if (strlen($dealerSku) > 0 && strcasecmp($toner->sku, $dealerSku) !== 0)
                                {
                                    $dealerTonerAttribute->dealerSku = $dealerSku;
                                }

                                $dealerTonerAttributeMapper->insert($dealerTonerAttribute);
                            }
                        }
                        else
                        {
                            $messages[] = sprintf('Toner SKU "%1$s" for manufacturer "%2$s" was found but the colors did not match.', $tonerSku, $manufacturerName);
                        }
                    }
                    else
                    {
                        $messages[] = sprintf('Toner SKU "%1$s" for manufacturer "%2$s" was not found.', $tonerSku, $manufacturerName);
                    }
                }
            }
        }
        else
        {
            $messages[] = $csvLines;
        }

        return $messages;
    }
}

