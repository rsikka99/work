<?php

abstract class My_Controller_Report extends Zend_Controller_Action
{
    /**
     * The current report
     *
     * @var Proposalgen_Model_Assessment
     */
    protected $_report;

    /**
     * The current proposal
     *
     * @var Application_Model_Proposal_OfficeDepot
     */
    protected $_proposal;
    protected $_csvFormat;
    protected $_pdfFormat;
    protected $_wordFormat;
    protected $_reportId;
    protected $_reportCompanyName;
    protected $_reportAbsoluteCachePath;
    protected $_reportCachePath;
    /**
     * User that is logged into the system.
     *
     * @var Application_Model_User
     */
    protected $_user;



    /**
     * Gets the view ready to render a docx file
     */
    public function initDocx ()
    {
        require_once ('PHPWord.php');
        $this->view->phpword = new PHPWord();
    }

    /**
     * Gets the view ready to render a pdf file
     */
    public function initPdf ()
    {

    }





} // end index controller

