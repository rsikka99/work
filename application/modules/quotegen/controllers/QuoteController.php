<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteMapper;

/**
 * Class Quotegen_QuoteController
 */
class Quotegen_QuoteController extends Quotegen_Library_Controller_Quote
{

    public function indexAction ()
    {
        // Display all of the quotes
        $mapper    = QuoteMapper::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper));

        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(15);

        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    public function deleteAction ()
    {
        $quoteId = $this->_getParam('id', false);

        if (!$quoteId)
        {
            $this->_flashMessenger->addMessage([
                'warning' => 'Please select a quote to delete first.'
            ]);
            $this->redirectToRoute('quotes.view-and-print-reports');
        }

        $quoteMapper = new QuoteMapper();
        $quote       = $quoteMapper->find($quoteId);

        if (!$quote->id)
        {
            $this->_flashMessenger->addMessage([
                'danger' => 'There was an error selecting the quote to delete.'
            ]);
            $this->redirectToRoute('quotes.view-and-print-reports');
        }

        $client  = ClientMapper::getInstance()->find($quote->clientId);
        $message = "Are you sure you want to delete the quote for {$client->companyName} ?";
        $form    = new DeleteConfirmationForm($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();

            if (!isset($values ['cancel']))
            {
                if ($form->isValid($values))
                {

                    $quoteMapper->delete($quote);

                    $this->_flashMessenger->addMessage([
                        'success' => "Quote was deleted successfully."
                    ]);
                    $this->redirectToRoute('quotes.reports', ['quoteId' => $quote->id]);
                }
            }
            else
            {
                $this->redirectToRoute('quotes.reports', ['quoteId' => $quote->id]);
            }
        }
        $this->view->form = $form;
    }
}

