<?php

class Quotegen_Quote_ReportsController extends Quotegen_Library_Controller_Quote
{
    public $contexts = array (
            'purchase-quote' => array (
                    'docx' 
            ), 
            'lease-quote' => array (
                    'docx' 
            ) 
    );

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::REPORTS_CONTROLLER);
        
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();
        
        $this->_helper->contextSwitch()->initContext();
    }

    /**
     * This function takes care of displaying reports
     */
    public function indexAction ()
    {
        $request = $this->getRequest();
        
        $form = new Quotegen_Form_Quote_General($this->_quote);
        
        $populateData = $this->_quote->toArray();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['goBack']))
            {
                if ($form->isValid($values))
                {
                    $this->_quote->populate($values);
                    $this->saveQuote();
                    Quotegen_Model_Mapper_Quote::getInstance()->save($this->_quote);
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => 'Please correct the errors below.' 
                    ));
                }
            }
            else
            {
                $this->_helper->redirector('index', 'quote_profitability', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
        }
        
        $form->populate($populateData);
        $this->view->form = $form;
        $this->view->navigationForm = new Quotegen_Form_Quote_Navigation(Quotegen_Form_Quote_Navigation::BUTTONS_BACK);
    }

    public function purchaseQuoteAction ()
    {
    }

    public function leaseQuoteAction ()
    {
    }

    /**
     * Action is responsible for create the excel output for the hardware order list
     */
    public function orderListAction ()
    {
        $quote = $this->_quote;
        $totalQuantity = 1;
        
        $titles = array (
                "Item", 
                "SKU (OEM)", 
                "SKU (DEALER)", 
                "Serial Number", 
                "Location" 
        );
        $style_header = array (
                'fill' => array (
                        'type' => PHPExcel_Style_Fill::FILL_SOLID, 
                        'color' => array (
                                'rgb' => 'E1E0F7' 
                        ) 
                ), 
                'font' => array (
                        'bold' => true 
                ) 
        );
        $style_header_device = array (
                'fill' => array (
                        'type' => PHPExcel_Style_Fill::FILL_SOLID, 
                        'color' => array (
                                'rgb' => 'E1E0F7' 
                        ) 
                ), 
                'font' => array (
                        'bold' => true 
                ) 
        );
        $style_header_titles = array (
                'fill' => array (
                        'type' => PHPExcel_Style_Fill::FILL_SOLID, 
                        'color' => array (
                                'rgb' => 'E1E0F7' 
                        ) 
                ) 
        );
        
        // Create a new PHPExcel Object * New PHPExcel objects will have one attached worksheet by default
        $objPHPExcel = new PHPExcel();
        $activeSheet = $objPHPExcel->getActiveSheet();
        $activeSheet->getStyle('A1:E1')->applyFromArray($style_header);
        
        // Ojbects in memory are held in php memory as serializeds objects, reduces memory footpirnt with little performance hit
        PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
        
        // Set properties
        $objPHPExcel->getProperties()->setTitle("Hardware Device List");
        
        // Start building the document
        $objPHPExcel->setActiveSheetIndex(0);
        $activeSheet->SetCellValue('A1', 'Hardware Device List');
        $activeSheet->mergeCells('A1:E1');
        $activeSheet->getRowDimension('1')->setRowHeight(35);
        
        // Go through each device and build and array for output
        $quoteDeviceGroups = $quote->getQuoteDeviceGroups();
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ( $quoteDeviceGroups as $quoteDeviceGroup )
        {
            /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
            foreach ( $quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice )
            {
                $quoteDevice = $quoteDeviceGroupDevice->getQuoteDevice();
                // $device builds an array used to output quote device information
                $device = array (
                        $quoteDevice->getName(), 
                        $quoteDevice->getSku(), 
                        $quoteDevice->getSku() 
                );
                
                // $deviceOptions builds an array of arrays representing quote device options
                $deviceOptions = array ();
                /* @var $quoteDeviceOption Quotegen_Model_QuoteDeviceOption */
                foreach ( $quoteDeviceGroupDevice->getQuoteDevice()->getQuoteDeviceOptions() as $quoteDeviceOption )
                {
                    $deviceOptions [] = array (
                            "name" => $quoteDeviceOption->getName(), 
                            "oemSku" => $quoteDeviceOption->getSku(), 
                            "dealerSku" => $quoteDeviceOption->getSku() 
                    );
                }
                
                // Build the datasheet
                // Object->getHighestRow() will return the row index where data has been entered in last.
                // This as well applies styles that have been defined that the beginning of the funciton
                for($deviceCount = 1; $deviceCount <= $quoteDeviceGroupDevice->getQuantity(); $deviceCount ++)
                {
                    $highestRow = $activeSheet->getHighestRow() + 2;
                    $activeSheet->setCellValue("A{$highestRow}", "Device {$totalQuantity}");
                    $activeSheet->mergeCells("A{$highestRow}:E{$highestRow}");
                    $activeSheet->getStyle("A{$highestRow}:E{$highestRow}")->applyFromArray($style_header_device);
                    
                    $highestRow = $activeSheet->getHighestRow() + 1;
                    
                    $activeSheet->fromArray($titles, null, "A{$highestRow}");
                    $activeSheet->getStyle("A{$highestRow}:E{$highestRow}")->applyFromArray($style_header_titles);
                    
                    $highestRow = $activeSheet->getHighestRow() + 1;
                    
                    $activeSheet->fromArray($device, null, "A{$highestRow}");
                    
                    // Use a foreach to go through options instead of using fromArray just in case
                    // we require to style the option rows.
                    foreach ( $deviceOptions as $option )
                    {
                        $highestRow = $activeSheet->getHighestRow() + 1;
                        $activeSheet->setCellValue("A{$highestRow}", $option ["name"]);
                        $activeSheet->setCellValue("B{$highestRow}", $option ["oemSku"]);
                        $activeSheet->setCellValue("C{$highestRow}", $option ["dealerSku"]);
                    }
                    $totalQuantity ++;
                }
            }
        }
        
        $activeSheet->getColumnDimension('A')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);
        $activeSheet->getColumnDimension('E')->setAutoSize(true);
        // Rename sheet
        $activeSheet->setTitle('Hardware Device List');
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save(PUBLIC_PATH . "/downloads/PHPExcel.xlsx");
        
        /**
         * Clearing a workbook from php's memory
         */
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
    }
}

