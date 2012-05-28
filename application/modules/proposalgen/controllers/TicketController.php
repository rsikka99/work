<?php

/**
 * TicketController - Controller to manage all ticket actions
 * 
 * @author Lee Robert
 * @version 1.0
 */
class Proposalgen_TicketController extends Zend_Controller_Action
{

    function init ()
    {
        // Context switch to do automatic json work
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('view', 'json')
            ->addActionContext('edit', 'json')
            ->addActionContext('create', 'json')
            ->addActionContext('delete', 'json')
            ->initContext();
        $this->user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->privilege = Zend_Auth::getInstance()->getIdentity()->privileges;
        $this->view->privilege = Zend_Auth::getInstance()->getIdentity()->privileges;
    }

    function indexAction ()
    {
        // Should implement ajax to work with all the actions here, but we
    // should be able to access those actions via html too
    }

    public function manageticketsAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = 'Manage Tickets';
        
        // fill status filter
        $ticket_statusesTable = new Proposalgen_Model_DbTable_TicketStatuses();
        $ticket_statuses_filter = $ticket_statusesTable->fetchAll();
        foreach ( $ticket_statuses_filter as $row )
        {
            $statuses_list_array [$row->status_id] = $row->status_name;
        }
        $this->view->statuslist = $statuses_list_array;
        $this->view->defaultstatus = - 1;
        
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            $db->beginTransaction();
            try
            {
		        if ($formData ['form_mode'] == 'delete')
		        {
		            $response = 1;
		            foreach ( $formData as $key => $value )
		            {
		                if (strstr($key, "jqg_requests_list_"))
		                {
		                    $ticket_id = str_replace("jqg_requests_list_", "", $key);
		                    $response = $this->deleteTicket($ticket_id);
		                    if ($response == 0)
		                    {
		                        $this->view->message = "There was an error while trying to delete the ticket " . $ticket_id . ". Please contact your administrator.";
		                        exit();
		                    }
		                }
		            }
		            if ($response == 1)
		            {
		                $db->commit();
		                $this->view->message = "The ticket(s) were successfully deleted.";
		            }
		        }
            }
            catch (Exception $e)
            {
                $db->rollback();
                $this->view->message = "An error has occurred and none of the selected tickets were deleted.";
            }
        }
    }

    public function managemyticketsAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = 'Manage Tickets';
        
        // fill status filter
        $ticket_statusesTable = new Proposalgen_Model_DbTable_TicketStatuses();
        $ticket_statuses_filter = $ticket_statusesTable->fetchAll();
        foreach ( $ticket_statuses_filter as $row )
        {
            $statuses_list_array [$row->status_id] = $row->status_name;
        }
        $this->view->statuslist = $statuses_list_array;
        $this->view->defaultstatus = - 1;
        
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            $db->beginTransaction();
            try
            {
                if ($formData ['form_mode'] == 'delete')
                {
                    $response = 1;
                    foreach ( $formData as $key => $value )
                    {
                        if (strstr($key, "jqg_requests_list_"))
                        {
                            $ticket_id = str_replace("jqg_requests_list_", "", $key);
                            $response = $this->deleteTicket($ticket_id);
                            if ($response == 0)
                            {
                                $this->view->message = "There was an error while trying to delete the ticket " . $ticket_id . ". Please contact your administrator.";
                                exit();
                            }
                        }
                    }
                    if ($response == 1)
                    {
                        $db->commit();
                        $this->view->message = "The ticket(s) were successfully deleted.";
                    }
                }
            }
            catch (Exception $e)
            {
                $db->rollback();
                $this->view->message = "An error has occurred and none of the selected tickets were deleted.";
            }
        }
    }

    public function ticketdetailsAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $date = date('Y-m-d H:i:s T');
        $this->view->title = 'Tickets Details';
        $ticket_id = $this->_getParam('id', 0);
        
        // fill status filter
        $ticket_statusesTable = new Proposalgen_Model_DbTable_TicketStatuses();
        $ticket_statuses_filter = $ticket_statusesTable->fetchAll();
        foreach ( $ticket_statuses_filter as $row )
        {
            $statuses_list_array [$row->status_id] = $row->status_name;
        }
        $this->view->statuslist = $statuses_list_array;
        
        // update tickets viewed
        $ticket_viewedModel = new Proposalgen_Model_TicketViewed();
        $ticket_viewedModel->setTicketId($ticket_id);
        $ticket_viewedModel->setUserId($this->user_id);
        $ticket_viewedModel->setDateViewed($date);
        $ticket_viewed_id = Proposalgen_Model_Mapper_TicketViewed::getInstance()->save($ticket_viewedModel);
        
        if ($this->_request->isPost())
        {
            $db->beginTransaction();
            try
            {
                $formData = $this->_request->getPost();
                // print_r($formData); die;
                
                // if not system admin and status > 2 then reset status to new
                if (! in_array("System Admin", $this->privilege) && $formData ['cboStatus'] > 2)
                {
                    $ticket_status = Proposalgen_Model_TicketStatus::STATUS_OPEN;
                }
                else
                {
                    $ticket_status = $formData ['cboStatus'];
                }
                
                // save ticket
                $ticketTable = new Proposalgen_Model_DbTable_Tickets();
                $ticketData = array (
                        'status_id' => $ticket_status, 
                        'date_updated' => $date 
                );
                $where = $ticketTable->getAdapter()->quoteInto('ticket_id = ?', $ticket_id, 'INTEGER');
                $ticketTable->update($ticketData, $where);
                
                // save comments
                if ($formData ['txtComment'] != '')
                {
                    $ticket_commentsTable = new Proposalgen_Model_DbTable_TicketComments();
                    $ticket_commentsData = array (
                            'ticket_id' => $ticket_id, 
                            'user_id' => $this->user_id, 
                            'comment_date' => $date, 
                            'comment_text' => $formData ['txtComment'] 
                    );
                    $ticket_commentsTable->insert($ticket_commentsData);
                
                }
                
                // return message
                $this->view->message = "Ticket " . $ticket_id . " has been updated successfully.";
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollback();
                $this->view->message = "An error has occurred and the ticket was not saved.";
            }
        
        }
        
        if ($ticket_id > 0)
        {
            // load details for ticket
            $ticketsMapper = Proposalgen_Model_Mapper_Ticket::getInstance();
            $tickets = $ticketsMapper->find($ticket_id);
            $this->view->ticket_number = $tickets->TicketId;
            $this->view->ticket_title = $tickets->Title;
            $this->view->reported_by = $tickets->User->UserName;
            $this->view->ticket_type = $tickets->Category->CategoryName;
            $this->view->ticket_details = $tickets->Description;
            $this->view->ticket_status = ucwords(strtolower($tickets->Status->StatusName));
            $this->view->ticket_status_id = ucwords(strtolower($tickets->Status->StatusId));
            
            // get comment history
            $ticket_comments_array = array ();
            $ticket_commentsMapper = Proposalgen_Model_Mapper_TicketComment::getInstance();
            $ticket_comments = $ticket_commentsMapper->fetchAll(array (
                    'ticket_id = ?' => $ticket_id 
            ));
            
            foreach ( $ticket_comments as $row )
            {
                $comment_date = new Zend_Date($row->CommentDate, "yyyy-mm-dd HH:ii:ss");
                $ticket_comments_array [] = array (
                        'username' => $row->User->UserName, 
                        'comment_date' => $comment_date->toString('mm/dd/yyyy'), 
                        'comment_text' => $row->CommentText 
                );
            }
            $this->view->ticket_comments = $ticket_comments_array;
            
            // find pf_device and unknown_device information
            $ticketpfrequestMapper = Proposalgen_Model_Mapper_TicketPFRequest::getInstance();
            $ticketpfrequest = $ticketpfrequestMapper->find($ticket_id);
            $this->view->devices_pf_id = $ticketpfrequest->DevicePfId;
            $this->view->device_pf_name = ucwords(strtolower($ticketpfrequest->DevicePf->PfDbManufacturer . ' ' . $ticketpfrequest->DevicePf->PfDbDeviceName));
            $this->view->user_suggested_name = ucwords(strtolower($ticketpfrequest->DeviceManufacturer . ' ' . $ticketpfrequest->PrinterModel));
            
            // check for existing mapping
            $this->view->is_mapped = false;
            
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'mmpf' => 'master_matchup_pf' 
            ))
                ->join(array (
                    'md' => 'master_device' 
            ), 'md.master_device_id = mmpf.master_device_id')
                ->join(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = md.mastdevice_manufacturer')
                ->where('devices_pf_id = ?', $ticketpfrequest->DevicePfId, 'INTEGER');
            $stmt = $db->query($select);
            $row = $stmt->fetch();
            
            if (count($row) > 1)
            {
                $this->view->is_mapped = true;
                $this->view->mapped_to_device = ucwords(strtolower($row ['manufacturer_name'] . ' ' . $row ['printer_model']));
            }
        
        }
    
    }

    public function ticketlistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 4;
            
            // get filtered status
        $condition = "status_id = ?";
        $status_id = $this->_getParam('status', 0);
        if ($status_id == 0)
        {
            $condition = "status_id > ?";
        }
        else if ($status_id == - 1)
        {
            $condition = "status_id IN (1,2)";
        }
        
        try
        {
            $ticketsMapper = Proposalgen_Model_Mapper_Ticket::getInstance();
            $tickets = $ticketsMapper->fetchAll(array (
                    $condition => $status_id 
            ));
            
            $count = count($tickets);
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit;
            if ($start < 0)
                $start = 0;
            
            $ticketsMapper = Proposalgen_Model_Mapper_Ticket::getInstance();
            $tickets = $ticketsMapper->fetchAll(array (
                    $condition => $status_id 
            ), ($sidx . ' ' . $sord));
            
            if (count($tickets) > 0)
            {
                $i = 0;
                $formdata->page = $page;
                $formdata->total = $total_pages;
                $formdata->records = $count;
                foreach ( $tickets as $row )
                {
                    // get company name for user
                    $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
                    $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', $row->User->DealerCompanyId, 'INTEGER');
                    $dealer_company = $dealer_companyTable->fetchRow($where);
                    $company_name = $dealer_company ['company_name'];
                    
                    $formdata->rows [$i] ['id'] = $row->TicketId;
                    $formdata->rows [$i] ['cell'] = array (
                            $row->TicketId, 
                            $row->Title, 
                            $row->User->UserName, 
                            $company_name, 
                            $row->Category->CategoryName, 
                            $this->convertDate($row->DateCreated), 
                            ucwords(strtolower($row->Status->StatusName)) 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find requests.", 0, $e);
        
        } // end catch
          
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function myticketlistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 4;
            
            // get filtered status
        $condition = "status_id = ?";
        $status_id = $this->_getParam('status', 0);
        if ($status_id == 0)
        {
            $condition = "status_id > ?";
        }
        else if ($status_id == - 1)
        {
            $condition = "status_id IN (1,2)";
        }
        
        try
        {
            $ticketsMapper = Proposalgen_Model_Mapper_Ticket::getInstance();
            $tickets = $ticketsMapper->fetchAll(array (
                    'user_id = ?' => $this->user_id, 
                    $condition => $status_id 
            ));
            
            $count = count($tickets);
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            if ($page > $total_pages)
                $page = $total_pages;
            $start = $limit * $page - $limit;
            if ($start < 0)
                $start = 0;
            
            $ticketsMapper = Proposalgen_Model_Mapper_Ticket::getInstance();
            $tickets = $ticketsMapper->fetchAll(array (
                    'user_id = ?' => $this->user_id, 
                    $condition => $status_id 
            ), $sidx . ' ' . $sord);
            
            if (count($tickets) > 0)
            {
                $i = 0;
                $formdata->page = $page;
                $formdata->total = $total_pages;
                $formdata->records = $count;
                foreach ( $tickets as $row )
                {
                    $formdata->rows [$i] ['id'] = $row->TicketId;
                    $formdata->rows [$i] ['cell'] = array (
                            $row->TicketId, 
                            $row->Title, 
                            $row->Category->CategoryName, 
                            $row->User->UserName, 
                            $this->convertDate($row->DateCreated), 
                            ucwords(strtolower($row->Status->StatusName)) 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find requests.", 0, $e);
        
        } // end catch
          
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }
    
    public function deleteTicket($ticket_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // delete related records and ticket
        $db->beginTransaction();
        try {
			$ticketsViewedMapper = Proposalgen_Model_Mapper_TicketViewed::getInstance()->delete('ticket_id = ' . $ticket_id);
			$ticketCommentsMapper = Proposalgen_Model_Mapper_TicketComment::getInstance()->delete('ticket_id = ' . $ticket_id);
			$ticketPFRequestsMapper = Proposalgen_Model_Mapper_TicketPFRequest::getInstance()->delete('ticket_id = ' . $ticket_id);
	        $ticketsMapper = Proposalgen_Model_Mapper_Ticket::getInstance()->delete('ticket_id = ' . $ticket_id);
            $db->commit();
	        return 1;
        }
        catch (Exception $e)
        {
            $db->rollback();
            return 0;
        }
    }

    public function convertDate ($date)
    {
        if ($date)
        {
            return (strftime("%x", strtotime($date)));
        }
        else
        {
            return " ";
        }
    }

} // end ticket controller

