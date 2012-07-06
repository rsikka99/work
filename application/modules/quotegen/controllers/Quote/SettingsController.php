<?php

class Quotegen_Quote_SettingsController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::SETTINGS_CONTROLLER);
    }

    /**
     * This function takes care of editing quote settings
     */
    public function indexAction ()
    {
        $quote = $this->_quote;
        
        if (! $quote)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Error you must select a quote first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $form = new Quotegen_Form_EditQuote();
        $form->populate($quote->toArray());
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            try
            {
                if (! isset($values ['cancel']))
                {
                    // Create a new instance of quote
                    $quote = new Quotegen_Model_Quote();
                    $quote = $this->_quote;
                    
                    // Save the new quote
                    $quoteMapper = Quotegen_Model_Mapper_Quote::getInstance();
                    $quoteMapper->save($quote, $quote->getId());
                    
                    
                    $this->_helper->flashMessenger(array (
                            'success' => "Quote updated successfully." 
                    ));
                }
                else
                {
                    $this->_helper->redirector('index');
                }
            }
            catch ( Exception $e )
            {
                $this->_helper->flashMessenger(array (
                        'warning' => "There was an issue saving these settings please try again."
                ));
            }
        }
        
        $this->view->form = $form;
    }
}

