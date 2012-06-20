
<?php

class Quotegen_OptionController extends Zend_Controller_Action
{

    public function init ()
    {
    }
    

    public function indexAction ()
    {
        // Get all current items in categories table
        $optionMapper = new Quotegen_Model_Mapper_Option();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($optionMapper));
        
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

    }
}

