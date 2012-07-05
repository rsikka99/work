<?php

class Admin_TonerController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }
    
    // Shows all the toners in a table
    public function indexAction ()
    {
        // Get all toners
        $tonerMapper = new Admin_Model_Mapper_Toner();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($tonerMapper));
        
        // Set current page
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set max items per page
        $paginator->setItemCountPerPage(25);
        
        // Save entries to view paginatior
        $this->view->paginator = $paginator;
    }

    public function deleteAction ()
    {
    }

    public function createAction ()
    {
    }

    public function editAction ()
    {
    }

    public function viewAction ()
    {
        // Get Toner Details
        $this->view->toner = Admin_Model_Mapper_Toner::getInstance()->find($this->_getParam('id', false));
    }
}

