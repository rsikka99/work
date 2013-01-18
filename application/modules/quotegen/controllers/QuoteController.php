<?php

class Quotegen_QuoteController extends Quotegen_Library_Controller_Quote
{

    public function indexAction ()
    {
        // Display all of the quotes
        $mapper = Quotegen_Model_Mapper_Quote::getInstance();
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
        
        if (! $quoteId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a quote to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $quoteMapper = new Quotegen_Model_Mapper_Quote();
        $quote = $quoteMapper->find($quoteId);
        
        if (! $quote->getId())
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the quote to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $client = Quotegen_Model_Mapper_Client::getInstance()->find($quote->getClientId());
        $message = "Are you sure you want to delete the quote for {$client->companyName} ?";
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (! isset($values ['cancel']))
            {
                if ($form->isValid($values))
                {
                    
                    $quoteMapper->delete($quote);
                    
                    $this->_helper->flashMessenger(array (
                            'success' => "Quote was deleted successfully." 
                    ));
                    $this->_helper->redirector('index');
                }
            }
            else
            {
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }
}

