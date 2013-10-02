<?php

use Phinx\Migration\AbstractMigration;

class InsertQgenData extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('INSERT INTO `quote_settings` (`id`, `pageCoverageMonochrome`, `pageCoverageColor`, `deviceMargin`, `pageMargin`, `pricingConfigId`, `adminCostPerPage`) VALUES
(1, 4.5, 20, 15, 20, 2, 0.0035),
(2, 4.5, 20, 15, 20, 2, 0.0035);
');

        $this->execute('INSERT INTO `dealer_settings` (`dealerId`, `quoteSettingId`) VALUES
(1, 1),
(2, 2);
');

        $this->execute('INSERT INTO `leasing_schemas` (`id`, `dealerId`, `name`) VALUES
(1, 2, \'default\');
');

        $this->execute('INSERT INTO `leasing_schema_ranges` (`id`, `leasingSchemaId`, `startRange`) VALUES
(1, 1, 0),
(2, 1, 3000),
(3, 1, 10000),
(4, 1, 25000);
');


        $this->execute('INSERT INTO `leasing_schema_terms` (`id`, `leasingSchemaId`, `months`) VALUES
(1, 1, 12),
(2, 1, 24),
(3, 1, 36),
(4, 1, 39),
(5, 1, 42),
(6, 1, 48),
(7, 1, 60),
(8, 1, 63);
');

        $this->execute('INSERT INTO `leasing_schema_rates` (`leasingSchemaTermId`, `leasingSchemaRangeId`, `rate`) VALUES
(1, 1, 0.0990),
(1, 2, 0.0965),
(1, 3, 0.0954),
(1, 4, 0.0944),

(2, 1, 0.0529),
(2, 2, 0.0502),
(2, 3, 0.0501),
(2, 4, 0.0499),

(3, 1, 0.0364),
(3, 2, 0.0338),
(3, 3, 0.0336),
(3, 4, 0.0333),

(4, 1, 0.0343),
(4, 2, 0.0322),
(4, 3, 0.0320),
(4, 4, 0.0317),

(5, 1, 0.0335),
(5, 2, 0.0308),
(5, 3, 0.0301),
(5, 4, 0.0300),

(6, 1, 0.0309),
(6, 2, 0.0273),
(6, 3, 0.0269),
(6, 4, 0.0268),

(7, 1, 0.0269),
(7, 2, 0.0229),
(7, 3, 0.0222),
(7, 4, 0.0221),

(8, 1, 0.0260),
(8, 2, 0.0217),
(8, 3, 0.0214),
(8, 4, 0.0213);
');


        $this->execute('INSERT INTO `clients` (`id`, `dealerId`, `accountNumber`, `companyName`, `legalName`, `employeeCount`) VALUES
(1, 1, \'0000001\', \'Tangent MTW\', \'Tangent MTW Incorporated\', 12),
(2, 1, \'0000002\', \'Starbucks\', \'Starbucks\', 12000),
(3, 1, \'0000003\', \'Novellis\', \'Novellis co-operatives\', 1200),
(4, 1, \'0000004\', \'Samsung\', \'Samsung Incorporated\', 9800),
(5, 2, \'0000001\', \'Tangent MTW\', \'Tangent MTW Incorporated\', 12),
(6, 2, \'0000002\', \'Starbucks\', \'Starbucks\', 12000),
(7, 2, \'0000003\', \'Novellis\', \'Novellis co-operatives\', 1200),
(8, 2, \'0000004\', \'Samsung\', \'Samsung Incorporated\', 9800);
');

        $this->execute('INSERT INTO `contacts` (`id`, `clientId`, `firstName`, `lastName`, `countryCode`, `areaCode`, `exchangeCode`, `number`, `extension`) VALUES
(1, 1, \'Norm\', \'McConkey\', 1, 613, 507, 5151, null),
(2, 2, \'Tyson\', \'Riehl\', 1, 613, 333, 4444, null),
(3, 3, \'Shawn\', \'Wilder\', 1, 613, 666, 6666, null),
(4, 4, \'Lee\', \'Robert\', 1, 613, 123, 1234, null),
(5, 5, \'Norm\', \'McConkey\', 1, 613, 507, 5151, null),
(6, 6, \'Tyson\', \'Riehl\', 1, 613, 333, 4444, null),
(7, 7, \'Shawn\', \'Wilder\', 1, 613, 666, 6666, null),
(8, 8, \'Lee\', \'Robert\', 1, 613, 123, 1234, null);
');

        $this->execute('INSERT INTO `addresses` (`id`, `clientId`, `addressLine1`, `addressline2`, `city`, `region`, `postCode`, `countryId`) VALUES
(1, 1, \'945 Princess Street\', \'Suite 234\', \'Kingston\', \'9\', \'K7L3N6\', 1),
(2, 2, \'Tyson Avenue\', \'\', \'Kingston\', \'9\', \'k7n2s1\', 1),
(3, 3, \'Shawn Lane\', \'\', \'Kingston\', \'9\', \'k2h7s6\', 1),
(4, 4, \'Lee Street\', \'\', \'Kingston\', \'9\', \'k1b2s7\', 1),
(5, 5, \'945 Princess Street\', \'Suite 234\', \'Kingston\', \'9\', \'K7L3N6\', 1),
(6, 6, \'Tyson Avenue\', \'\', \'Kingston\', \'9\', \'k7n2s1\', 1),
(7, 7, \'Shawn Lane\', \'\', \'Kingston\', \'9\', \'k2h7s6\', 1),
(8, 8, \'Lee Street\', \'\', \'Kingston\', \'9\', \'k1b2s7\', 1);
');


        $this->execute('INSERT INTO `options` (`id`, `dealerId`, `name`, `description`, `cost`, `dealerSku`, `oemSku`) VALUES
(7, 2, \'HP 3-bin Stapler/Stacker with Output\', \'Staples up to 50 pages and stacks up to 1600 sheets\', 2099.99, \'\', \'CC517A\'),
(8, 2, \'HP LaserJet MFP 3000-sheet Stapler/Stacker\', \'Stacks up to 3000 sheets\', 1999.99, \'\', \'C8085A\'),
(9, 2, \'Xerox Cart, W/Storage Capacity\', \'Printer Cart with Storage Capacity\', 299.99, \'\', \'097S03636\'),
(10, 2, \'HP Booklet Maker/Finisher with Output\', \'Capacity of 2000 sheets and 25 saddle-stitched booklets\', 3199.99, \'\', \'CC516A\'),
(11, 2, \'HP LaserJet MFP Multifunction Finisher\', \'Supports up to 50 pages per minute, can staple up to 50 sheets\', 2399.99, \'\', \'C8088B\'),
(12, 2, \'Xerox Productivity Kit (Includes Hard Drive)\', \'Expands workflow options for Xerox equipment\', 499.99, \'\', \'097S04141\'),
(13, 2, \'HP LaserJet MFP Analog Fax Accessory 300\', \'Support efficient information sharing and improve work team productivity.\', 299.99, \'\', \'Q3701A\'),
(14, 2, \'Xerox 525 Sheet Feeder, Adjustable Up To Legal\', \'Capacity of 525 sheets, can support legal paper\', 299.99, \'\', \'097S04142\'),
(15, 2, \'HP 8-Bin Mailbox\', \'Stacks up to 250 pages per bin\', 1499.99, \'\', \'Q5693A\'),
(16, 2, \'Xerox Tray, Main, 525 Sheets\', \'Capacity of 525 sheets\', 199.99, \'\', \'097S04143\'),
(17, 2, \'HP 3,000-sheet Stacker\', \'Capacity of 3000 sheets\', 1799.99, \'\', \'C8084A\'),
(18, 2, \'Kyocera SD-144-512(A)\', \'512MB Memory\', 349, \'\', \'855D200295\'),
(19, 2, \'Kyocera SD-144-1(A)\', \'1GB Memory\', 499, \'\', \'855D200296\'),
(20, 2, \'Kyocera CF-4G\', \'4GB Compact Flash Memory Card (Must be installed when using HyPAS applications)\', 45, \'\', \'855D200615\'),
(21, 2, \'Kyocera Card Reader Holder D\', \'Card Reader Attachment\', 49.95, \'\', \'1702M90UN0\'),
(22, 2, \'Kyocera Stand\', \'Copier Stand\', 245, \'\', \'855D200608\'),
(23, 2, \'Kyocera PF-520\', \'Paper Drawer - 500 sheets\', 309, \'\', \'1203NA2US0\'),
(24, 2, \'Kyocera PF-530\', \'Paper Drawer - 500 sheets - Multipurpose\', 415, \'\', \'1203M92US0\'),
(25, 2, \'Kyocera PF-320\', \'500 sheets optional paper feed cassette\', 163, \'\', \'1203NY7US0\'),
(26, 2, \'Kyocera PF-315+\', \'2,000 sheets optional large capacity paper feed unit * Must also choose Kyocera PB-325 Base Unit\', 489, \'\', \'1203KF0KL1\'),
(27, 2, \'Kyocera IB-32\', \'IEEE 1284 compliant NIC\', 73, \'\', \'1503N50UN0\'),
(28, 2, \'Kyocera IB-51\', \'Wireless LAN NIC\', 385, \'\', \'1505J50UN0\'),
(29, 2, \'Kyocera IB-50\', \'Gigabit Ether Net Board\', 265, \'\', \'1505JV0UN0\'),
(30, 2, \'Kyocera HD-6\', \'SSD Memory Storage Device\', 274, \'\', \'1505J40UN0\'),
(31, 2, \'Kyocera UG-33\', \'Upgrade Kit for Thin Print Support\', 92, \'\', \'1503NT0UN0\'),
(32, 2, \'Kyocera Data Security Kit (E)\', \'Hard Drive Encryption/Overwrite Kit\', 348, \'\', \'855D200600\'),
(33, 2, \'Kyocera SDHC Card-32G\', \'SD Card Memory for storage, 32GB\', 66, \'\', \'855D200648\'),
(34, 2, \'Kyocera SDHC Card-16G\', \'SD Card Memory for storage, 16GB\', 27, \'\', \'855D200647\'),
(35, 2, \'Kyocera PB-325\', \'Base unit for FS-4300DN/4200DN/4100DN/2100DN with PF-315+\', 179, \'\', \'1903N10UN0\'),
(36, 2, \'Kyocera PT-320\', \'Face-up rear output tray, 250 sheets for FS-4300DN/4200DN/4100DN\', 33, \'\', \'1203N70UN0\'),
(37, 2, \'Kyocera DP-771\', \'175 sheet Dual Scan Document Processor\', 1080, \'\', \'1203NW6US0\'),
(38, 2, \'Kyocera DP-770\', \'100 sheet Reversing Automatic Document Processor\', 851, \'\', \'1203NV6US0\'),
(39, 2, \'Kyocera DF-770(B)*\', \'1,000 sheets Finisher * Requires AK-730 Attachment Kit\', 924, \'\', \'1203NC2US1\'),
(40, 2, \'Kyocera DF-790(B)*\', \'4,000 sheets Finisher * Requires AK-730 Attachment Kit\', 1500, \'\', \'1203NB2US1\'),
(41, 2, \'Kyocera BF-730\', \'Booklet and Tri Folding Unit for DF-790(B)\', 900, \'\', \'1203ND0UN0\'),
(42, 2, \'Kyocera MT-730\', \'7 Bin Mailbox for 4,000 Sheet Finisher\', 600, \'\', \'1203N00UN0\'),
(43, 2, \'Kyocera PH-7A\', \'2/3 Hole Punch Unit for DF-770(B)/790(B)\', 402, \'\', \'1203NK2US0\'),
(44, 2, \'Kyocera JS-731\', \'Outer Job Separator\', 114, \'\', \'1203NM0UN0\'),
(45, 2, \'Kyocera JS-732**\', \'Inner Job Separator ** Cannot install with DF-790(B) or DF-770(B) finishers\', 122.6, \'\', \'1203N80UN0\'),
(46, 2, \'Kyocera PF-770\', \'3,000 Sheet Side Large Capacity Tray - Letter\', 851, \'\', \'1203NG7US0\'),
(47, 2, \'Kyocera PF-730\', \'Dual 500 Sheet Paper Trays\', 773, \'\', \'1203NJ7US0\'),
(48, 2, \'Kyocera PF-740\', \'Dual 1,500 Sheet Paper Trays\', 828, \'\', \'1203NF7US0\'),
(49, 2, \'Kyocera Fax System(V)\', \'Fax Board\', 693, \'\', \'1505JT2US0\'),
(50, 2, \'Kyocera Internet FAX Kit (A)*\', \'Internet FAX Kit * Requires Fax System(V)\', 180, \'\', \'1703MC0UN0\'),
(51, 2, \'Kyocera Printed Document Guard Kit (A)\', \'Printed Document Guard\', 693, \'\', \'1703L90UN0\'),
(52, 2, \'Kyocera Keyboard Holder (A)\', \'Keyboard Tray Kit\', 68, \'\', \'1709AF0UN0\'),
(53, 2, \'Kyocera UG-34\', \'Optional Printer Emulation for IBM Prorinter, Epson LQ-850, Diabro 630\', 336, \'\', \'855D200604\'),
(54, 2, \'Kyocera Banner Guide(A)\', \'MPT Guide attachment to assist the feeding of banner paper.\', 186, \'\', \'1202K90UN0\'),
(55, 2, \'Kyocera Banner Guide(A)\', \'MPT Guide attachment to assist the feeding of banner paper.\', 186, \'\', \'1202K90UN0\'),
(56, 2, \'Kyocera Card Reader Holder(B)\', \'HID Card Reader Holder for Card Authentication Kit(B)\', 53, \'\', \'1709AD0UN0\'),
(57, 2, \'Kyocera MM-16-128\', \'Additional Fax Memory Board\', 120, \'\', \'1503MB0UN0\'),
(58, 2, \'Kyocera AK-730\', \'Attachment Kit for DF-770(B)/790(B)\', 96, \'\', \'1703NB0UN0\'),
(59, 2, \'Kyocera Stand\', \'Copier Stand\', 214, \'\', \'855D200605\'),
(60, 2, \'Kyocera DT-730\', \'Original Hard Copy Holder\', 27, \'\', \'1902LC0UN1\'),
(61, 2, \'Kyocera Platen Cover Type E\', \'Platen Cover\', 71, \'\', \'1202H70UN0\'),
(62, 2, \'Kyocera PF-780\', \'500 Sheet Side Multi-Media Tray\', 501, \'\', \'1203NL7US0\'),
(63, 2, \'Kyocera Printing System (12)***\', \'EFI FIERY Controller for TASkalfa 6550ci/7550ci with Spot-On *** Requires Printing System Interface Kit (A)\', 4352, \'\', \'1503NR0KL1\'),
(64, 2, \'Kyocera Printing System Interface Kit (A)\', \'Interface kit for Printing System(12), includes mounting kit and circuit board\', 450, \'\', \'1503NR0KL2\'),
(65, 2, \'Kyocera Copy Tray(D)\', \'Copy Tray\', 27, \'\', \'1902LF0UN1\');
');


        $this->execute('INSERT INTO `devices` (`masterDeviceId`, `dealerId`, `cost`, `dealerSku`, `oemSku`, `description`) VALUES
(79, 2, 1000, \'\', \'8870/DN\', \'\'),
(95, 2, 1500, \'\', \'CC395A\', \'\'),
(401, 2, 3000, \'\', \'Q3939A\', \'- Fax accessory\'),
(445, 2, 5000, \'S7938341\', \'8570/DN\', \'\'),
(459, 2, 2500, \'\', \'CF116A\', \'\'),
(471, 2, 4699.99, \'S8145808\', \'CE504A\', \'\'),
(478, 2, 1299.99, \'\', \'CE992A\', \'\'),
(479, 2, 3806.79, \'\', \'CE504A\', \'\'),
(480, 2, 2595, \'\', \'1102M92US0\', \'Standard: 4-in-1, Copy, Scan, Network Print, Fax\r\nDocument Processor (Reversing 50 sheets), Duplex, 1GB RAM\'),
(481, 2, 1569, \'\', \'1102LV2US0\', \'62ppm A4 Monochrome Printer\r\nStd. Duplex, Ethernet Network, 256MB RAM\'),
(482, 2, 4941, \'\', \'1102LH2US0\', \'55 PPM A3 B&W MFP\'),
(483, 2, 14500, \'\', \'1102LB2US0\', \'65/65 PPM A3 Color MFP\');');

        $this->execute('INSERT INTO `dealer_master_device_attributes` (`masterDeviceId`, `dealerId`, `laborCostPerPage`, `partsCostPerPage`) VALUES
(79, 2, 0, 0),
(95, 2, 0, 0),
(401, 2, 0, 0),
(445, 2, 0, 0),
(459, 2, 0, 0),
(471, 2, 0, 0),
(478, 2, 0, 0),
(479, 2, 0, 0),
(480, 2, 0, 0),
(481, 2, 0, 0),
(482, 2, 0, 0),
(483, 2, 0, 0);
');

        $this->execute('INSERT INTO `device_options` (`masterDeviceId`, `optionId`, `dealerId`, `includedQuantity`) VALUES
(79, 9, 2, 0),
(79, 12, 2, 0),
(79, 14, 2, 0),
(79, 16, 2, 0),
(95, 8, 2, 0),
(95, 11, 2, 0),
(95, 13, 2, 0),
(95, 15, 2, 0),
(95, 17, 2, 0),
(401, 7, 2, 0),
(401, 10, 2, 0),
(480, 18, 2, 0),
(480, 19, 2, 0),
(480, 20, 2, 0),
(480, 21, 2, 0),
(480, 22, 2, 0),
(480, 23, 2, 0),
(480, 24, 2, 0),
(481, 18, 2, 0),
(481, 19, 2, 0),
(481, 25, 2, 0),
(481, 26, 2, 0),
(481, 27, 2, 0),
(481, 28, 2, 0),
(481, 29, 2, 0),
(481, 30, 2, 0),
(481, 31, 2, 0),
(481, 32, 2, 0),
(481, 33, 2, 0),
(481, 34, 2, 0),
(481, 35, 2, 0),
(481, 36, 2, 0),
(482, 29, 2, 0),
(482, 32, 2, 0),
(482, 37, 2, 0),
(482, 38, 2, 0),
(482, 39, 2, 0),
(482, 40, 2, 0),
(482, 41, 2, 0),
(482, 42, 2, 0),
(482, 43, 2, 0),
(482, 44, 2, 0),
(482, 45, 2, 0),
(482, 46, 2, 0),
(482, 47, 2, 0),
(482, 48, 2, 0),
(482, 49, 2, 0),
(482, 50, 2, 0),
(482, 51, 2, 0),
(482, 52, 2, 0),
(482, 53, 2, 0),
(482, 54, 2, 0),
(482, 56, 2, 0),
(482, 57, 2, 0),
(482, 58, 2, 0),
(482, 59, 2, 0),
(482, 60, 2, 0),
(482, 61, 2, 0),
(483, 32, 2, 0),
(483, 40, 2, 0),
(483, 41, 2, 0),
(483, 42, 2, 0),
(483, 43, 2, 0),
(483, 44, 2, 0),
(483, 46, 2, 0),
(483, 47, 2, 0),
(483, 48, 2, 0),
(483, 49, 2, 0),
(483, 50, 2, 0),
(483, 51, 2, 0),
(483, 52, 2, 0),
(483, 53, 2, 0),
(483, 54, 2, 0),
(483, 56, 2, 0),
(483, 57, 2, 0),
(483, 62, 2, 0),
(483, 63, 2, 0),
(483, 64, 2, 0),
(483, 65, 2, 0);
');


    }

    /**
     * Migrate Down.
     */
    public function down ()
    {

    }
}