<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use Twitter_Bootstrap_Form_Horizontal;

/**
 * Class QuoteForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuoteForm extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * @var int
     */
    protected $_leasingSchemaId;

    /**
     * @param null|array $options
     */
    public function __construct ($options = null)
    {
        parent::__construct($options);
        QuoteNavigationForm::addFormActionsToForm(QuoteNavigationForm::BUTTONS_SAVE, $this);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $clientList          = [];
        $clientListValidator = [];

        foreach (ClientMapper::getInstance()->fetchAll() as $client)
        {
            $clientList [$client->id] = $client->companyName;
            $clientListValidator []   = $client->id;
        }

        $clients = $this->createElement('select', 'clientId');
        $clients->setLabel('Select Client:');
        $clients->addMultiOptions($clientList);
        $clients->addValidator('InArray', false, [
            $clientListValidator,
        ]);
        $this->addElement($clients);

        $this->addElement('radio', 'quoteType', [
            'label'        => 'Type Of Quote:',
            'filters'      => ['StringTrim', 'StripTags'],
            'multiOptions' => [
                'purchased' => 'Purchased',
                'leased'    => 'Leased',
            ],
            'required'     => true,
        ]);

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'index/form/quote.phtml']]]);
    }
}