<?php

class Quotegen_LeasingSchemasController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // Display all of the clients
        $mapper = Quotegen_Model_Mapper_LeasingSchemas::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper));
        
        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set how many items to show
        $paginator->setItemCountPerPage(15);
        
        // Pass the view the paginator
        $this->view->paginator = $paginator;
        
        // Set the default viewscript
        // TODO: This could be moved to a bootstrap file in the module.
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('client/paginator.phtml');
    }
    
}

