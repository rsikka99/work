<?php

class Hardwareoptimization_XlsController extends Hardwareoptimization_Library_Controller_Action {
    public function indexAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $optimizationView = $this->getOptimizationViewModel();
        $optimization = $optimizationView->getOptimization();
        $client = $optimization->getClient();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("MPS Toolbox")
            ->setLastModifiedBy("MPS Toolbox")
            ->setTitle("Hardwareoptimization for ".$client->companyName)
            ->setSubject("Hardwareoptimization for ".$client->companyName)
            ->setDescription("Hardwareoptimization for ".$client->companyName)
            ->setKeywords("Hardwareoptimization for ".$client->companyName)
            ->setCategory("Hardwareoptimization for ".$client->companyName);

        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Data');
        $sheet->setCellValue('A1','Manufacturer');
        $sheet->setCellValue('B1','Model');
        $sheet->setCellValue('C1','IP Address');
        $sheet->setCellValue('D1','Serial');
        $sheet->setCellValue('E1','Age');
        $sheet->setCellValue('F1','Black AMPV');
        $sheet->setCellValue('G1','Color AMPV');
        $sheet->setCellValue('H1','Total AMPV');
        $sheet->setCellValue('I1','Black CPP');
        $sheet->setCellValue('J1','Color CPP');
        $sheet->setCellValue('K1','Current EMC');
        $sheet->setCellValue('L1','Adjusted EMC');
        $sheet->setCellValue('M1','Replacement Model');
        $sheet->setCellValue('N1','Device Cost');
        $sheet->setCellValue('O1','Customer Device Cost');
        $sheet->setCellValue('P1','Dealer Elite Black CPP');
        $sheet->setCellValue('Q1','Dealer Elite Color CPP');
        $sheet->setCellValue('R1','Black Margin %');
        $sheet->setCellValue('S1','Color Margin %');
        $sheet->setCellValue('T1','Customer Black CPP');
        $sheet->setCellValue('U1','Customer Color CPP');
        $sheet->setCellValue('V1','Maint + Labor');
        $sheet->setCellValue('W1','Customer Supply Cost');
        $sheet->setCellValue('X1','Dealer Maint + Labor');
        $sheet->setCellValue('Y1','Customer Maint + Labor EMC');
        $sheet->setCellValue('Z1','Maint + Labor EYC');
        $sheet->setCellValue('AA1','Future EMC');
        $sheet->setCellValue('AB1','Future EMC Delta');

        $objPHPExcel->getActiveSheet()->getStyle('A1:AB1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

        $arr = \MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationMapper::getInstance()->fetchAllForHardwareOptimization(
            $optimization->id,
            $optimizationView->getCostPerPageSettingForDealer(),
            $optimizationView->getCostPerPageSettingForReplacements(),
            $client->getClientSettings()->optimizationSettings,
            99999,
            0,
            false
        );

        $form = new \MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\DeviceSwapChoiceForm($arr['deviceInstances'], $optimization->dealerId, $optimization->id);

        $row=2;
        foreach ($arr['jsonData'] as $i=>$line) {
            $replacementDeviceElement       = $form->getElement("deviceInstance_{$line['deviceInstanceId']}");

            $row = $i+2;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':AB'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->getStyle('F'.$row.':L'.$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $objPHPExcel->getActiveSheet()->getStyle('I'.$row.':J'.$row)->getNumberFormat()->setFormatCode('0.0000');
            $objPHPExcel->getActiveSheet()->getStyle('P'.$row.':Q'.$row)->getNumberFormat()->setFormatCode('0.0000');
            $objPHPExcel->getActiveSheet()->getStyle('T'.$row.':V'.$row)->getNumberFormat()->setFormatCode('0.0000');



            $objPHPExcel->getActiveSheet()->getStyle('L'.$row)->getFont()->setBold(true);

            /** @var \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel $device */
            $device = $arr['deviceInstances'][$i];
            $deviceStr = explode('</br>',$line['device']);
            $replacement = $replacementDeviceElement->getValue();
            $replacement_cost = '';
            if ($replacement == 'Keep') $replacement_cost = '0';
            if (is_numeric($replacement)) {
                $masterDevice = \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper::getInstance()->find($replacement);
                $replacement = $masterDevice->getFullDeviceName();
                $dealerDevice = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper::getInstance()->find([$masterDevice->id, $optimization->dealerId]);
                if ($dealerDevice) {
                    $replacement_cost = $dealerDevice->cost;
                }
            }

            $sheet->setCellValue('A'.$row, $deviceStr[0]);
            $sheet->setCellValue('B'.$row, $deviceStr[1]);
            $sheet->setCellValue('C'.$row, $device->ipAddress);
            $sheet->setCellValue('D'.$row, $device->serialNumber);
            $sheet->setCellValue('E'.$row, $device->getAge());
            $sheet->setCellValue('F'.$row, $line['monoAmpv']);
            $sheet->setCellValue('G'.$row, $line['colorAmpv']);
            $sheet->setCellValue('H'.$row, $line['monoAmpv'] + $line['colorAmpv']);
            $sheet->setCellValue('I'.$row, $line['rawMonoCpp']);
            $sheet->setCellValue('J'.$row, $line['rawColorCpp']);
            $sheet->setCellValue('K'.$row, '=F'.$row.'*I'.$row.'+G'.$row.'*J'.$row);
            $sheet->setCellValue('L'.$row, '=K'.$row.'*Summary!B$23+K'.$row);
            $sheet->setCellValue('M'.$row, $replacement);
            $sheet->setCellValue('N'.$row, $replacement_cost);
            $sheet->setCellValue('O'.$row, '=N'.$row.'*Summary!B$24+N'.$row);
            $sheet->setCellValue('P'.$row, $line['hwMonoCpp']);
            $sheet->setCellValue('Q'.$row, $line['hwColorCpp']);
            $sheet->setCellValue('R'.$row, '0.2');
            $sheet->setCellValue('S'.$row, '0.3');
            $sheet->setCellValue('T'.$row, '=P'.$row.'*R'.$row.'+P'.$row);
            $sheet->setCellValue('U'.$row, '=Q'.$row.'*S'.$row.'+Q'.$row);
            $sheet->setCellValue('V'.$row, $optimizationView->getCostPerPageSettingForDealer()->monochromePartsCostPerPage + $optimizationView->getCostPerPageSettingForDealer()->monochromeLaborCostPerPage + $optimizationView->getCostPerPageSettingForDealer()->adminCostPerPage);
            $sheet->setCellValue('W'.$row, '=F'.$row.'*T'.$row.'+G'.$row.'*U'.$row);
            $sheet->setCellValue('X'.$row, '=H'.$row.'*V'.$row);
            $sheet->setCellValue('Y'.$row, '=X'.$row.'*Summary!B$25+X'.$row);
            $sheet->setCellValue('Z'.$row, '=Y'.$row.'*Summary!B$26');
            $sheet->setCellValue('AA'.$row, '=W'.$row.'+Y'.$row);
            $sheet->setCellValue('AB'.$row, '=L'.$row.'-AA'.$row);
        }

        $sheet = new PHPExcel_Worksheet(null, 'Summary');
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $objPHPExcel->addSheet($sheet);
        $sheet->setCellValue('A2', 'HW Cost (Dealer)');
        $sheet->setCellValue('B2', '=SUM(Data!N2:N'.$row.')');
        $sheet->setCellValue('A3', 'HW Cost (Customer)');
        $sheet->setCellValue('B3', '=SUM(Data!O2:O'.$row.')');
        $sheet->setCellValue('A4', 'Current EMC Total');
        $sheet->setCellValue('B4', '=SUM(Data!K2:K'.$row.')');
        $sheet->setCellValue('A5', 'Future EMC Total');
        $sheet->setCellValue('B5', '=SUM(Data!AA2:AA184)');
        $sheet->setCellValue('A6', 'EMC Savings');
        $sheet->setCellValue('B6', '=B4-B5');
        $sheet->setCellValue('A7', '1YR EMC Savings');
        $sheet->setCellValue('B7', '=B6*12');

        $sheet->setCellValue('A22', 'Settings');
        $sheet->setCellValue('A23', 'Adjusted EMC %');
        $sheet->setCellValue('B23', '0');
        $sheet->setCellValue('A24', 'Device Margin %');
        $sheet->setCellValue('B24', '0.15');
        $sheet->setCellValue('A25', 'Maint + Labor Margin');
        $sheet->setCellValue('B25', '0.1');
        $sheet->setCellValue('A26', 'Maint + Labor Months');
        $sheet->setCellValue('B26', '48');

        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Hardwareoptimization '.$client->companyName.'.xlsx"');
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $tmp = tempnam(sys_get_temp_dir(),'tmp');
        $objWriter->save($tmp);
        readfile($tmp);
        unlink($tmp);
    }
}