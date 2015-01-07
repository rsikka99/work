<?php
/* @var $r Zend_Controller_Router_Rewrite */
$r = Zend_Controller_Front::getInstance()->getRouter();
use \Zend_Controller_Router_Route as R;

/**
 * Auth Routes
 */
//@formatter:off
$r->addRoute('auth.login',                 new R('login',                 array('module' => 'default', 'controller' => 'auth', 'action' => 'login')));
$r->addRoute('auth.logout',                new R('logout',                array('module' => 'default', 'controller' => 'auth', 'action' => 'logout')));
$r->addRoute('auth.login.forgot-password', new R('login/forgot-password', array('module' => 'default', 'controller' => 'auth', 'action' => 'forgotpassword')));
$r->addRoute('auth.login.change-password', new R('login/change-password', array('module' => 'default', 'controller' => 'auth', 'action' => 'changepassword')));

// TODO kmccully: dns error in User controller line 474
$r->addRoute('auth.login.reset-password',  new R('login/reset-password',  array('module' => 'default', 'controller' => 'auth', 'action' => 'resetpassword')));
//@formatter:on


// ***** Home Menu Routes *********************************************************************************************************************************** //


/**
 * Client Routes
 */
//@formatter:off
$r->addRoute('clients',                          new R('clients/view-all',          array('module' => 'default', 'controller' => 'index', 'action' => 'view-all-clients')));
$r->addRoute('clients.create-clients-dashboard', new R('clients/create-new',        array('module' => 'default', 'controller' => 'index', 'action' => 'create-client')));
$r->addRoute('client.edit-clients-dashboard',    new R('client',                    array('module' => 'default', 'controller' => 'index', 'action' => 'edit-client')));

// TODO kmccully: needs form for searching clients
$r->addRoute('clients.search-for-client',        new R('clients/search-for-client', array('module' => 'default', 'controller' => 'index', 'action' => 'search-for-client')));
//@formatter:on


// ***** Report Routes ************************************************************************************************************************************** //

/**
 * RMS Upload Routes
 */
//@formatter:off
$r->addRoute('rms-upload',                            new R('rms-uploads/:rmsUploadId',                            array('module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'index',                'rmsUploadId' => false)));
$r->addRoute('rms-upload.upload-file',                new R('rms-uploads/upload-file/:rmsUploadId',                array('module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'index',                'rmsUploadId' => false)));
$r->addRoute('rms-upload.mapping',                    new R('rms-uploads/mapping/:rmsUploadId',                    array('module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'mapping',              'rmsUploadId' => false)));
$r->addRoute('rms-upload.mapping.list',               new R('rms-uploads/mapping/list',                            array('module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'device-mapping-list'                         )));
$r->addRoute('rms-upload.mapping.set-mapped-to',      new R('rms-uploads/mapping/set-mapped-to',                   array('module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'set-mapped-to'                               )));

// TODO kmccully: no managemappingdevicesAction in managedevices controller
$r->addRoute('rms-upload.mapping.admin-edit-mapping', new R('rms-uploads/mapping/admin-edit-mapping',              array('module' => 'proposalgen', 'controller' => 'managedevices', 'action' => 'managemappingdevices', 'rmsUploadId' => false)));

// TODO kmccully: edit/create buttons don't work
$r->addRoute('rms-upload.mapping.user-edit-mapping',  new R('rms-uploads/mapping/user-edit-mapping/:rmsUploadId',  array('module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'edit-unknown-device',  'rmsUploadId' => false)));
$r->addRoute('rms-upload.summary',                    new R('rms-uploads/summary/:rmsUploadId',                    array('module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'summary',              'rmsUploadId' => false)));
$r->addRoute('rms-upload.summary.device-list',        new R('rms-uploads/summary/device-list',                     array('module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'device-summary-list',                        )));
$r->addRoute('rms-upload.excluded-list',              new R('rms-uploads/excluded-list',                           array('module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'excluded-list',                              )));
//@formatter:on

/**
 * Home Route
 */
//@formatter:off
$r->addRoute('app.dashboard',                     new R('/',                                            array('module' => 'default', 'controller' => 'index', 'action' => 'index'             )));
$r->addRoute('app.dashboard.change-client',       new R('/clients/change',                              array('module' => 'default', 'controller' => 'index', 'action' => 'change-client'     )));
$r->addRoute('app.dashboard.change-upload',       new R('/rms-uploads/change',                          array('module' => 'default', 'controller' => 'index', 'action' => 'change-upload'     )));
$r->addRoute('app.dashboard.delete-rms-upload',   new R('/rms-uploads/delete/:rmsUploadId',             array('module' => 'default', 'controller' => 'index', 'action' => 'delete-rms-upload' )));
$r->addRoute('app.dashboard.select-client',       new R('/select-client',                               array('module' => 'default', 'controller' => 'index', 'action' => 'select-client'     )));
$r->addRoute('app.dashboard.select-upload',       new R('/select-upload',                               array('module' => 'default', 'controller' => 'index', 'action' => 'select-upload'     )));
$r->addRoute('app.dashboard.delete-assessment',   new R('/delete-assessment/:assessmentId',             array('module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     )));
$r->addRoute('app.dashboard.delete-optimization', new R('/delete-optimization/:hardwareOptimizationId', array('module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     )));
$r->addRoute('app.dashboard.delete-healthcheck',  new R('/delete-healthcheck/:healthcheckId',           array('module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     )));
$r->addRoute('app.dashboard.delete-quote',        new R('/delete-quote/:quoteId',                       array('module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     )));
$r->addRoute('app.dashboard.no-clients',          new R('/first-client',                                array('module' => 'default', 'controller' => 'index', 'action' => 'no-clients'        )));
$r->addRoute('app.dashboard.no-uploads',          new R('/first-upload',                                array('module' => 'default', 'controller' => 'index', 'action' => 'no-uploads'        )));
//@formatter:on


/**
 * Assessment Routes
 */
//@formatter:off
$r->addRoute('assessment',                                  new R('assessment',                                  array('module' => 'assessment', 'controller' => 'index',                         'action' => 'index')));
$r->addRoute('assessment.select-upload',                    new R('assessment/select-upload',                    array('module' => 'assessment', 'controller' => 'index',                         'action' => 'select-upload')));
$r->addRoute('assessment.survey',                           new R('assessment/survey',                           array('module' => 'assessment', 'controller' => 'index',                         'action' => 'survey')));
$r->addRoute('assessment.settings',                         new R('assessment/settings',                         array('module' => 'assessment', 'controller' => 'index',                         'action' => 'settings')));
$r->addRoute('assessment.report-index',                     new R('assessment/report-index',                     array('module' => 'assessment', 'controller' => 'report_index',                  'action' => 'index')));
$r->addRoute('assessment.report-assessment',                new R('assessment/report-assessment',                array('module' => 'assessment', 'controller' => 'report_assessment',             'action' => 'index')));
$r->addRoute('assessment.report-cost-analysis',             new R('assessment/report-cost-analysis',             array('module' => 'assessment', 'controller' => 'report_costanalysis',           'action' => 'index')));
$r->addRoute('assessment.report-gross-margin',              new R('assessment/report-gross-margin',              array('module' => 'assessment', 'controller' => 'report_grossmargin',            'action' => 'index')));
$r->addRoute('assessment.report-toner-vendor-gross-margin', new R('assessment/report-toner-vendor-gross-margin', array('module' => 'assessment', 'controller' => 'report_tonervendorgrossmargin', 'action' => 'index')));
$r->addRoute('assessment.report-jit-supply-and-toner-sku',  new R('assessment/report-jit-supply-and-toner-sku',  array('module' => 'assessment', 'controller' => 'report_toners',                 'action' => 'index')));
$r->addRoute('assessment.report-old-device-list',           new R('assessment/report-old-device-list',           array('module' => 'assessment', 'controller' => 'report_olddevicelist',          'action' => 'index')));
$r->addRoute('assessment.report-printing-device-list',      new R('assessment/report-printing-device-list',      array('module' => 'assessment', 'controller' => 'report_printingdevicelist',     'action' => 'index')));
$r->addRoute('assessment.report-solution',                  new R('assessment/report-solution',                  array('module' => 'assessment', 'controller' => 'report_solution',               'action' => 'index')));
$r->addRoute('assessment.report-lease-buy-back',            new R('assessment/report-lease-buyback',             array('module' => 'assessment', 'controller' => 'report_leasebuyback',           'action' => 'index')));
$r->addRoute('assessment.report-fleet-attributes',          new R('assessment/report-fleet-attributes',          array('module' => 'assessment', 'controller' => 'report_fleetattributes',        'action' => 'index')));
$r->addRoute('assessment.report-utilization',               new R('assessment/report-utilization',               array('module' => 'assessment', 'controller' => 'report_utilization',            'action' => 'index')));
//@formatter:on

/**
 * Hardware Optimization Routes
 */
//@formatter:off
$r->addRoute('hardwareoptimization',                              new R('hardwareoptimization',                              array('module' => 'hardwareoptimization', 'controller' => 'index',                        'action' => 'index')));
$r->addRoute('hardwareoptimization.select-upload',                new R('hardwareoptimization/select-upload',                array('module' => 'hardwareoptimization', 'controller' => 'index',                        'action' => 'select-upload')));
$r->addRoute('hardwareoptimization.settings',                     new R('hardwareoptimization/settings',                     array('module' => 'hardwareoptimization', 'controller' => 'index',                        'action' => 'settings')));
$r->addRoute('hardwareoptimization.optimization',                 new R('hardwareoptimization/optimization',                 array('module' => 'hardwareoptimization', 'controller' => 'index',                        'action' => 'optimize')));
$r->addRoute('hardwareoptimization.report-index',                 new R('hardwareoptimization/report-index',                 array('module' => 'hardwareoptimization', 'controller' => 'report_index',                 'action' => 'index')));
$r->addRoute('hardwareoptimization.report-customer-optimization', new R('hardwareoptimization/report-customer-optimization', array('module' => 'hardwareoptimization', 'controller' => 'report_customer_optimization', 'action' => 'index')));
$r->addRoute('hardwareoptimization.report-dealer-optimization',   new R('hardwareoptimization/report-dealer-optimization',   array('module' => 'hardwareoptimization', 'controller' => 'report_dealer_optimization',   'action' => 'index')));
//@formatter:on

/**
 * Healthcheck Routes
 */
//@formatter:off
$r->addRoute('healthcheck',                new R('healthcheck',                            array('module' => 'healthcheck', 'controller' => 'index',                      'action' => 'index')));
$r->addRoute('healthcheck.select-upload',  new R('healthcheck/select-upload',              array('module' => 'healthcheck', 'controller' => 'index',                      'action' => 'select-upload')));
$r->addRoute('healthcheck.settings',       new R('healthcheck/settings',                   array('module' => 'healthcheck', 'controller' => 'index',                      'action' => 'settings')));
$r->addRoute('healthcheck.report',         new R('healthcheck/report-healthcheck',         array('module' => 'healthcheck', 'controller' => 'report_healthcheck',         'action' => 'index')));
$r->addRoute('healthcheck.report-printiq', new R('healthcheck/printiq-report-healthcheck', array('module' => 'healthcheck', 'controller' => 'report_printiq_healthcheck', 'action' => 'index')));
//@formatter:on

/**
 * $r->addRoute('quotes.index',          new R('quotes/index/:clientId',          array('module' => 'quotegen', 'controller' => 'index', 'action' => 'index',          'clientId' => false)));
 * $r->addRoute('quotes.existing-quote', new R('quotes/existing-quote/:clientId', array('module' => 'quotegen', 'controller' => 'index', 'action' => 'existing-quote', 'clientId' => false)));
 * $r->addRoute('quotes.create-client',  new R('quotes/create-client',            array('module' => 'quotegen', 'controller' => 'index', 'action' => 'create-client'                      )));
 * //@formatter:on
 */

/**
 * Quote Menu Routes
 *
 * TODO kmccully: Create-quotes needs nav bar options to be non-selectable, breaks
 */
//@formatter:off
$r->addRoute('quotes',                                 new R('quotes/:quoteId',                                                                     array('module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'index',                                                           )));
$r->addRoute('quotes.add-hardware',                    new R('quotes/:quoteId/add-hardware',                                                        array('module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'index',                                                           )));
$r->addRoute('quotes.add-hardware.edit',               new R('quotes/add-hardware/edit-device/:id/:quoteId',                                        array('module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'edit-quote-device',              'id' => false,                   )));
$r->addRoute('quotes.add-hardware.edit.add-options',   new R('quotes/add-hardware/edit-device/add-options/:id/:quoteId',                            array('module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'add-options-to-quote-device',    'id' => false,                   )));
$r->addRoute('quotes.add-hardware.delete',             new R('quotes/add-hardware/delete/:id/:quoteId',                                             array('module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'delete-quote-device'                                              )));
$r->addRoute('quotes.sync-all-device-configurations',  new R('quotes/sync-all-device-configurations/:quoteId',                                      array('module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'sync-all-device-configurations', 'id' => false                    )));
$r->addRoute('quotes.sync-device-configurations',      new R('quotes/sync-device-configurations/:id/:quoteId',                                      array('module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'sync-device-configuration'                                        )));
$r->addRoute('quotes.use-configuration',               new R('quotes/use-configuration/:deviceId/:configurationId',                                 array('module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'use-configuration'                                                )));
$r->addRoute('quotes.delete-option-from-quote-device', new R('quotes/delete-option-from-quote-device/:quoteId/:quoteDeviceId/:quoteDeviceOptionId', array('module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'delete-option-from-quote-device'                                  )));
$r->addRoute('quotes.group-devices',                   new R('quotes/:quoteId/group-devices',                                                       array('module' => 'quotegen', 'controller' => 'quote_groups',        'action' => 'index',                                                           )));
$r->addRoute('quotes.manage-pages',                    new R('quotes/:quoteId/manage-pages',                                                        array('module' => 'quotegen', 'controller' => 'quote_pages',         'action' => 'index',                                                           )));
$r->addRoute('quotes.hardware-financing',              new R('quotes/:quoteId/hardware-financing',                                                  array('module' => 'quotegen', 'controller' => 'quote_profitability', 'action' => 'index'                                                            )));
$r->addRoute('quotes.reports',                         new R('quotes/:quoteId/reports',                                                             array('module' => 'quotegen', 'controller' => 'quote_reports',       'action' => 'index',                                                           )));
$r->addRoute('quotes.reports.purchase',                new R('quotes/:quoteId/reports/purchase/:format',                                            array('module' => 'quotegen', 'controller' => 'quote_reports',       'action' => 'purchase-quote'                                                   )));
$r->addRoute('quotes.reports.lease',                   new R('quotes/:quoteId/reports/lease/:format',                                               array('module' => 'quotegen', 'controller' => 'quote_reports',       'action' => 'lease-quote'                                                      )));
$r->addRoute('quotes.reports.order-list',              new R('quotes/:quoteId/reports/order-list/:format',                                          array('module' => 'quotegen', 'controller' => 'quote_reports',       'action' => 'order-list'                                                       )));
$r->addRoute('quotes.reports.contract',                new R('quotes/:quoteId/reports/contract/:format',                                            array('module' => 'quotegen', 'controller' => 'quote_reports',       'action' => 'contract'                                                         )));
//@formatter:on

/**
 * Hardware Quotes - Option Categories - Not in navigation.xml
 */
//@formatter:off
$r->addRoute('quotes.category-options',        new R('quotes/categories',                   array('module' => 'quotegen', 'controller' => 'category', 'action' => 'index'                )));
$r->addRoute('quotes.category-options.view',   new R('quotes/categories/view/:id',          array('module' => 'quotegen', 'controller' => 'category', 'action' => 'view',   'id' => false)));
$r->addRoute('quotes.category-options.create', new R('quotes/category-options/create',      array('module' => 'quotegen', 'controller' => 'category', 'action' => 'create'               )));
$r->addRoute('quotes.category-options.edit',   new R('quotes/category-options/edit/:id',    array('module' => 'quotegen', 'controller' => 'category', 'action' => 'edit',   'id' => false)));
$r->addRoute('quotes.category-options.delete', new R('quotes/category-options/delete/:id',  array('module' => 'quotegen', 'controller' => 'category', 'action' => 'delete', 'id' => false)));
//@formatter:on

/**
 * Hardware Quotes - Configurations - Not in navigation.xml
 */
//@formatter:off
$r->addRoute('quotes.configurations',             new R('quotes/configurations',                                array('module' => 'quotegen', 'controller' => 'configuration', 'action' => 'index'                                                         )));

// TODO kmccully: no viewAction in configuration controller (not used anywhere)
$r->addRoute('quotes.configurations.view',        new R('quotes/configurations/view',                           array('module' => 'quotegen', 'controller' => 'configuration', 'action' => 'view'                                                          )));
$r->addRoute('quotes.configurations.create',      new R('quotes/configurations/create',                         array('module' => 'quotegen', 'controller' => 'configuration', 'action' => 'create'                                                        )));
$r->addRoute('quotes.configurations.create.id',   new R('quotes/configurations/create/:id/:page',               array('module' => 'quotegen', 'controller' => 'configuration', 'action' => 'create', 'id'              => false, 'page' => 'configurations')));
$r->addRoute('quotes.configurations.edit',        new R('quotes/configurations/edit/:configurationid',          array('module' => 'quotegen', 'controller' => 'configuration', 'action' => 'edit',   'configurationid' => false                            )));
$r->addRoute('quotes.configurations.delete',      new R('quotes/configurations/delete/:configurationid',        array('module' => 'quotegen', 'controller' => 'configuration', 'action' => 'delete', 'configurationid' => false                            )));
$r->addRoute('quotes.configurations.edit.page',   new R('quotes/configurations/edit/:configurationid/:page',    array('module' => 'quotegen', 'controller' => 'configuration', 'action' => 'edit',   'configurationid' => false, 'page' => 'configurations')));
$r->addRoute('quotes.configurations.delete.page', new R('quotes/configurations/delete/:configurationid/:page',  array('module' => 'quotegen', 'controller' => 'configuration', 'action' => 'delete', 'configurationid' => false, 'page' => 'configurations')));
//@formatter:on

/**
 * Hardware Quotes - Devices - Not in navigation.xml
 */
//@formatter:off
$r->addRoute('quotes.devices',               new R('quotes/devices',                               array('module' => 'quotegen', 'controller' => 'device', 'action' => 'index'                                           )));

// TODO kmccully: "function getMasterDevice() on a non-object" error
$r->addRoute('quotes.devices.view',          new R('quotes/devices/view/:id',                      array('module' => 'quotegen', 'controller' => 'device', 'action' => 'view'                                            )));

// TODO kmccully: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'md.manufacturer_id' in 'order clause'
$r->addRoute('quotes.devices.create',        new R('quotes/devices/create',                        array('module' => 'quotegen', 'controller' => 'device', 'action' => 'create'                                          )));

// TODO kmccully: "error selecting device to edit
$r->addRoute('quotes.devices.edit',          new R('quotes/devices/edit/:id',                      array('module' => 'quotegen', 'controller' => 'device', 'action' => 'edit',         'id' => false                     )));

// TODO kmccully: "error selecting device to delete
$r->addRoute('quotes.devices.delete',        new R('quotes/devices/delete/:id',                    array('module' => 'quotegen', 'controller' => 'device', 'action' => 'delete',       'id' => false                     )));

// TODO kmccully: "select a device to edit first"
$r->addRoute('quotes.devices.delete-option', new R('quotes/devices/delete-options/:id/:optionId',  array('module' => 'quotegen', 'controller' => 'device', 'action' => 'deleteoption', 'id' => false, 'optionId' => false)));

// TODO kmccully: toArray() on a non-object
$r->addRoute('quotes.devices.add-options',   new R('quotes/devices/add-options/:optionId',         array('module' => 'quotegen', 'controller' => 'device', 'action' => 'addoptions',                  'optionId' => false)));
//@formatter:on

/**
 * Hardware Quotes - Device Configurations - Not in navigation.xml
 */
//@formatter:off
$r->addRoute('quotes.device-configurations',               new R('quotes/device-configurations',                             array('module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'index')));
$r->addRoute('quotes.device-configurations.view',          new R('quotes/device-configurations/view/:id',                    array('module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'view')));

// TODO kmccully: Error saving device-configuration
$r->addRoute('quotes.device-configurations.create',        new R('quotes/device-configurations/create',                      array('module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'create')));
$r->addRoute('quotes.device-configurations.edit',          new R('quotes/device-configurations/edit/:id',                    array('module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'edit')));
$r->addRoute('quotes.device-configurations.add-option',    new R('quotes/device-configurations/add-option/:id',              array('module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'addoption')));
$r->addRoute('quotes.device-configurations.delete',        new R('quotes/device-configurations/delete/:id',                  array('module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'delete')));
$r->addRoute('quotes.device-configurations.delete-option', new R('quotes/device-configurations/delete-option/:id/:optionId', array('module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'deleteoption')));
//@formatter:on


// ***** Hardware Library Routes **************************************************************************************************************************** //

/**
 * Device Management Routes
 */
//@formatter:off
$r->addRoute('hardware-library.devices',                       new R('hardware-library/devices',                       array('module' => 'hardware-library', 'controller' => 'index',          'action' => 'index'                )));
$r->addRoute('hardware-library.devices',                       new R('hardware-library/devices/search',                array('module' => 'proposalgen',      'controller' => 'admin',          'action' => 'search-for-device'    )));
$r->addRoute('hardware-library.devices.grid-list',             new R('hardware-library/devices/grid-list',             array('module' => 'hardware-library', 'controller' => 'devices',        'action' => 'grid-list'            )));
$r->addRoute('hardware-library.devices.load-forms',            new R('hardware-library/devices/load-forms',            array('module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'manage-master-devices')));
$r->addRoute('hardware-library.devices.update-master-device',  new R('hardware-library/devices/update-master-device',  array('module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'update-master-device' )));
$r->addRoute('hardware-library.devices.delete',                new R('hardware-library/devices/delete-jqgrid',         array('module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'delete'               )));
$r->addRoute('hardware-library.devices.delete',                new R('hardware-library/devices/delete',                array('module' => 'quotegen',         'controller' => 'devicesetup',    'action' => 'delete'               )));
$r->addRoute('hardware-library.devices.toner-list',            new R('hardware-library/devices/toner-list',            array('module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'assigned-toner-list'  )));
$r->addRoute('hardware-library.devices.toners',                new R('hardware-library/devices/toners',                array('module' => 'hardware-library', 'controller' => 'devices',        'action' => 'add-toner'            )));
$r->addRoute('hardware-library.devices.toners.remove',         new R('hardware-library/devices/toners/remove',         array('module' => 'hardware-library', 'controller' => 'devices',        'action' => 'remove-toner'         )));
$r->addRoute('hardware-library.devices.available-toners-list', new R('hardware-library/devices/available-toners-list', array('module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'available-toners-list')));

$r->addRoute('hardware-library.toners.colors-for-configuration', new R('hardware-library/toners/colors-for-configuration', array('module' => 'hardware-library', 'controller' => 'toner', 'action' => 'colors-for-configuration')));

$r->addRoute('hardware-library.configurations.list',        new R('hardware-library/configurations/list',        array('module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'hardware-configuration-list')));
$r->addRoute('hardware-library.configurations.reload-form', new R('hardware-library/configurations/reload-form', array('module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'reload-hardware-configurations-form')));


// FIXME lrobert: Fix this damn function to be a properly separated api
$r->addRoute('hardware-library.sauron', new R('hardware-library/sauron', array('module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'sauron')));

$r->addRoute('api.devices',           new R('api/v1/devices/:deviceId',        array('module' => 'api', 'controller' => 'devices', 'action' => 'index', 'deviceId' => false)));
$r->addRoute('api.devices.grid-list', new R('api/v1/devices/grid-list',        array('module' => 'api', 'controller' => 'devices', 'action' => 'grid-list')));
$r->addRoute('api.devices.create',    new R('api/v1/devices/create',           array('module' => 'api', 'controller' => 'devices', 'action' => 'create')));
$r->addRoute('api.devices.delete',    new R('api/v1/devices/:deviceId/delete', array('module' => 'api', 'controller' => 'devices', 'action' => 'delete')));
$r->addRoute('api.devices.save',      new R('api/v1/devices/:deviceId/save',   array('module' => 'api', 'controller' => 'devices', 'action' => 'save')));

$r->addRoute('api.devices.toners',        new R('api/v1/devices/:deviceId/toners/:tonerId',        array('module' => 'api', 'controller' => 'devices', 'action' => 'view-toners', 'tonerId' => false)));
$r->addRoute('api.devices.toners.create', new R('api/v1/devices/:deviceId/toners/create',          array('module' => 'api', 'controller' => 'devices', 'action' => 'add-toner')));
$r->addRoute('api.devices.toners.delete', new R('api/v1/devices/:deviceId/toners/delete/:tonerId', array('module' => 'api', 'controller' => 'devices', 'action' => 'remove-toner')));

$r->addRoute('api.manufacturers',        new R('api/v1/manufacturers/:manufacturerId',        array('module' => 'api', 'controller' => 'manufacturers', 'action' => 'index', 'manufacturerId' => false)));
$r->addRoute('api.manufacturers.create', new R('api/v1/manufacturers/create',                 array('module' => 'api', 'controller' => 'manufacturers', 'action' => 'create')));
$r->addRoute('api.manufacturers.delete', new R('api/v1/manufacturers/:manufacturerId/delete', array('module' => 'api', 'controller' => 'manufacturers', 'action' => 'delete')));
$r->addRoute('api.manufacturers.save',   new R('api/v1/manufacturers/:manufacturerId/save',   array('module' => 'api', 'controller' => 'manufacturers', 'action' => 'update')));


//@formatter:on

// API for clients
//@formatter:off
$r->addRoute('api.clients',           new R('api/v1/clients/:clientId', array('module' => 'api', 'controller' => 'client', 'action' => 'index', 'clientId' => false)));
//@formatter:on

// API for countries
//@formatter:off
$r->addRoute('api.countries',           new R('api/v1/countries',              array('module' => 'api', 'controller' => 'country', 'action' => 'index')));
$r->addRoute('api.countries.country',   new R('api/v1/countries/:countryId',   array('module' => 'api', 'controller' => 'country', 'action' => 'index' )));
//@formatter:on

/**
 * View All Devices Route
 */
//@formatter:off
$r->addRoute('hardware-library.all-devices',                new R('hardware-library/all-devices',                    array('module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'index'                      )));

// TODO kmccully: blank page
$r->addRoute('hardware-library.all-devices.create',         new R('hardware-library/all-devices/create',             array('module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'create'                     )));
$r->addRoute('hardware-library.all-devices.edit',           new R('hardware-library/all-devices/edit/:id',           array('module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'edit'                       )));
$r->addRoute('hardware-library.all-devices.toners',         new R('hardware-library/all-devices/toners/:id',         array('module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'toners'                     )));

// TODO kmccully: getMasterDevice() on a non-object line 866 deviceSetup controller
$r->addRoute('hardware-library.all-devices.options',        new R('hardware-library/all-devices/options/:id',        array('module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'options',      'id' => false)));

// TODO kmccully: getMasterDevice() on a non-object line 1059 deviceSetup controller
$r->addRoute('hardware-library.all-devices.configurations', new R('hardware-library/all-devices/configurations/:id', array('module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'configurations'             )));
//@formatter:on

/**
 * Quotes - Option controller
 * Not sure where this goes/applies yet in the information structure
 */
//@formatter:off
$r->addRoute('quotes.options',        new R('quotes/options/:id/:page',        array('module' => 'quotegen', 'controller' => 'option', 'action' => 'index',  'id' => false, 'page' => 'options')));
$r->addRoute('quotes.options.create', new R('quotes/options/create/:id/:page', array('module' => 'quotegen', 'controller' => 'option', 'action' => 'create', 'id' => false, 'page' => 'options')));

// TODO kmccully: "function getCategories() on non-object" error
$r->addRoute('quotes.options.view',   new R('quotes/options/view/:id/:page',   array('module' => 'quotegen', 'controller' => 'option', 'action' => 'view',   'id' => false, 'page' => 'options')));

// TODO kmccully: toArray() on a non-object
$r->addRoute('quotes.options.edit',   new R('quotes/options/edit/:id/:page',   array('module' => 'quotegen', 'controller' => 'option', 'action' => 'edit',   'id' => false, 'page' => 'options')));
$r->addRoute('quotes.options.delete', new R('quotes/options/delete/:id/:page', array('module' => 'quotegen', 'controller' => 'option', 'action' => 'delete', 'id' => false, 'page' => 'options')));
//@formatter:on

/**
 * All Toners
 */
//@formatter:off
$r->addRoute('hardware-library.all-toners',       new R('hardware-library/all-toners',       array('module' => 'hardware-library', 'controller' => 'toner', 'action' => 'index'           )));
$r->addRoute('hardware-library.toners.load-form', new R('hardware-library/toners/load-form', array('module' => 'hardware-library', 'controller' => 'toner', 'action' => 'load-form'       )));
$r->addRoute('hardware-library.toners.save',      new R('hardware-library/toners/save',      array('module' => 'hardware-library', 'controller' => 'toner', 'action' => 'save'            )));
$r->addRoute('hardware-library.all-toners-list',  new R('hardware-library/all-toners-list',  array('module' => 'hardware-library', 'controller' => 'toner', 'action' => 'all-toners-list' )));
//@formatter:on

/**
 * All Options
 */
//@formatter:off
$r->addRoute('hardware-library.options',             new R('hardware-library/options',             array('module' => 'hardware-library', 'controller' => 'option',         'action' => 'option-list'  )));
$r->addRoute('hardware-library.options.load-form',   new R('hardware-library/options/load-form',   array('module' => 'hardware-library', 'controller' => 'option',         'action' => 'load-form'    )));
$r->addRoute('hardware-library.options.save',        new R('hardware-library/options/save',        array('module' => 'hardware-library', 'controller' => 'option',         'action' => 'save'         )));
$r->addRoute('hardware-library.options.delete',      new R('hardware-library/options/delete',      array('module' => 'hardware-library', 'controller' => 'option',         'action' => 'delete'       )));
$r->addRoute('hardware-library.options.option-list', new R('hardware-library/options/option-list', array('module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'options-list' )));
//@formatter:on

/**
 * Bulk Hardware/Pricing Updates Routes
 */
//@formatter:off
$r->addRoute('hardware-library.bulk-hardware-pricing-updates',                           new R('hardware-library/bulk-hardware-pricing-updates', array('module' => 'proposalgen', 'controller' => 'costs', 'action' => 'bulkdevicepricing')));
$r->addRoute('hardware-library.bulk-hardware-pricing-updates.export-pricing',            new R('hardware-library/export-pricing',                array('module' => 'proposalgen', 'controller' => 'costs', 'action' => 'export-pricing')));
$r->addRoute('hardware-library.bulk-hardware-pricing-updates.bulk-file-device-pricing',  new R('hardware-library/bulk-file-device-pricing',      array('module' => 'proposalgen', 'controller' => 'costs', 'action' => 'bulk-file-device-pricing')));
$r->addRoute('hardware-library.bulk-hardware-pricing-updates.bulk-file-device-features', new R('hardware-library/bulk-file-device-features',     array('module' => 'proposalgen', 'controller' => 'costs', 'action' => 'bulk-file-device-features')));
$r->addRoute('hardware-library.bulk-hardware-pricing-updates.bulk-file-toner-pricing',   new R('hardware-library/bulk-file-toner-pricing',       array('module' => 'proposalgen', 'controller' => 'costs', 'action' => 'bulk-file-toner-pricing')));
$r->addRoute('hardware-library.bulk-hardware-pricing-updates.bulk-file-toner-matchup',   new R('hardware-library/bulk-file-toner-matchup',       array('module' => 'proposalgen', 'controller' => 'costs', 'action' => 'bulk-file-toner-matchup')));
//@formatter:on

/**
 * Device Swaps Routes
 */
//@formatter:off
$r->addRoute('hardware-library.device-swaps', new R('hardware-library/device-swaps', array('module' => 'hardwareoptimization', 'controller' => 'deviceswaps', 'action' => 'index')));
//@formatter:on

/**
 * Manufacturers Routes
 */
//@formatter:off
$r->addRoute('hardware-library.manufacturers',        new R('hardware-library/manufacturers',            array('module' => 'hardware-library', 'controller' => 'manufacturer', 'action' => 'index'                )));
$r->addRoute('hardware-library.manufacturers.view',   new R('hardware-library/manufacturers/view/:id',   array('module' => 'hardware-library', 'controller' => 'manufacturer', 'action' => 'view',   'id' => false)));
$r->addRoute('hardware-library.manufacturers.create', new R('hardware-library/manufacturers/create',     array('module' => 'hardware-library', 'controller' => 'manufacturer', 'action' => 'create'               )));
$r->addRoute('hardware-library.manufacturers.edit',   new R('hardware-library/manufacturers/edit/:id',   array('module' => 'hardware-library', 'controller' => 'manufacturer', 'action' => 'edit',   'id' => false)));
$r->addRoute('hardware-library.manufacturers.delete', new R('hardware-library/manufacturers/delete/:id', array('module' => 'hardware-library', 'controller' => 'manufacturer', 'action' => 'delete', 'id' => false)));
//@formatter:on

/**
 * Manage Printer Matchups Routes
 */
//@formatter:off
$r->addRoute('hardware-library.managematchups', new R('hardware-library/manage-printer-matchups', array('module' => 'proposalgen', 'controller' => 'admin', 'action' => 'managematchups')));
//@formatter:on


// ***** Settings Routes ************************************************************************************************************************************ //

/**
 * Settings Routes
 */
//@formatter:off
$r->addRoute('settings', new R('settings', array('module' => 'preferences', 'controller' => 'index', 'action' => 'index')));
//@formatter:on

/**
 * Report Settings Routes
 */
//@formatter:off
$r->addRoute('report-settings', new R('client/settings', array('module' => 'preferences', 'controller' => 'client', 'action' => 'index')));
$r->addRoute('company.report-settings', new R('company/report-settings', array('module' => 'preferences', 'controller' => 'dealer', 'action' => 'index')));
//@formatter:on

/**
 * My Profile Routes
 */
//@formatter:off
$r->addRoute('profile', new R('settings/profile', array('module' => 'admin', 'controller' => 'user', 'action' => 'profile')));
//@formatter:on

/**
 *  My Settings Routes
 */
//@formatter:off

// TODO kmccully: no userAction in index controller in Preferences module yet
$r->addRoute('my-settings', new R('settings/my-settings', array('module' => 'preferences', 'controller' => 'index', 'action' => 'user')));
//@formatter:on

/**
 * User Settings Routes
 */
//@formatter:off

// TODO kmccully: no userAction in index controller (is in navigation.xml)
$r->addRoute('settings.user',                       new R('settings/user',                       array('module' => 'preferences', 'controller' => 'index',                'action' => 'user')));
$r->addRoute('settings.user.assessment',            new R('settings/user/assessment',            array('module' => 'preferences', 'controller' => 'proposal',             'action' => 'user')));
$r->addRoute('settings.user.hardware-quote',        new R('settings/user/hardware-quote',        array('module' => 'preferences', 'controller' => 'quote',                'action' => 'user')));
$r->addRoute('settings.user.health-check',          new R('settings/user/health-check',          array('module' => 'preferences', 'controller' => 'healthcheck',          'action' => 'user')));
$r->addRoute('settings.user.hardware-optimization', new R('settings/user/hardware-optimization', array('module' => 'preferences', 'controller' => 'hardwareoptimization', 'action' => 'user')));
//@formatter:on

/**
 * Dealer Settings Routes
 */
//@formatter:off

// TODO kmccully: no dealerAction in index controller (is in navigation.xml)
$r->addRoute('settings.dealer',                       new R('settings/dealer',                       array('module' => 'preferences', 'controller' => 'index',                'action' => 'dealer')));
$r->addRoute('settings.dealer.assessment',            new R('settings/dealer/assessment',            array('module' => 'preferences', 'controller' => 'proposal',             'action' => 'dealer')));
$r->addRoute('settings.dealer.hardware-quote',        new R('settings/dealer/hardware-quote',        array('module' => 'preferences', 'controller' => 'quote',                'action' => 'dealer')));
$r->addRoute('settings.dealer.health-check',          new R('settings/dealer/health-check',          array('module' => 'preferences', 'controller' => 'healthcheck',          'action' => 'dealer')));
$r->addRoute('settings.dealer.hardware-optimization', new R('settings/dealer/hardware-optimization', array('module' => 'preferences', 'controller' => 'hardwareoptimization', 'action' => 'dealer')));
//@formatter:on

/**
 * System Settings Routes
 */
//@formatter:off

// TODO kmccully: no systemAction in index controller (is in navigation.xml)
$r->addRoute('settings.system',                       new R('settings/system',                       array('module' => 'preferences', 'controller' => 'index',                'action' => 'system')));
$r->addRoute('settings.system.assessment',            new R('settings/system/assessment',            array('module' => 'preferences', 'controller' => 'proposal',             'action' => 'system')));
$r->addRoute('settings.system.hardware-quote',        new R('settings/system/hardware-quote',        array('module' => 'preferences', 'controller' => 'quote',                'action' => 'system')));
$r->addRoute('settings.system.health-check',          new R('settings/system/health-check',          array('module' => 'preferences', 'controller' => 'healthcheck',          'action' => 'system')));
$r->addRoute('settings.system.hardware-optimization', new R('settings/system/hardware-optimization', array('module' => 'preferences', 'controller' => 'hardwareoptimization', 'action' => 'system')));
//@formatter:on


// ***** System Administration Routes *********************************************************************************************************************** //

/**
 * System Administration Route
 */
//@formatter:off
$r->addRoute('admin', new R('admin', array('module' => 'admin', 'controller' => 'index', 'action' => 'index')));
//@formatter:on

/**
 * Dealer Onboarding Route
 */
//@formatter:off
$r->addRoute('admin.dealer-on-boarding', new R('admin/dealer-on-boarding', array('module' => 'admin', 'controller' => 'onboarding', 'action' => 'index')));
//@formatter:on

/**
 * Client Routes
 */
//@formatter:off
$r->addRoute('admin.clients',        new R('admin/clients',                array('module' => 'admin', 'controller' => 'client',  'action' => 'index'                      )));

// TODO kmccully: no viewAction in client controller (is in navigation.xml)
$r->addRoute('admin.clients.view',   new R('admin/clients/view/:clientId', array('module' => 'admin', 'controller' => 'client',  'action' => 'view',   'clientId' => false)));
$r->addRoute('admin.clients.create', new R('admin/clients/create',         array('module' => 'admin', 'controller' => 'client',  'action' => 'create'                     )));
$r->addRoute('admin.clients.edit',   new R('admin/clients/edit/:id',       array('module' => 'admin', 'controller' => 'client',  'action' => 'edit',   'id' => false      )));
$r->addRoute('admin.clients.delete', new R('admin/clients/delete/:id',     array('module' => 'admin', 'controller' => 'client',  'action' => 'delete', 'id' => false      )));
//@formatter:on

/**
 * User Routes
 */
//@formatter:off
$r->addRoute('admin.users',        new R('admin/users',            array('module' => 'admin', 'controller' => 'user', 'action' => 'index'                )));

// TODO kmccully: no viewAction in user controller (is in navigation.xml)
$r->addRoute('admin.users.view',   new R('admin/users/view/:id',   array('module' => 'admin', 'controller' => 'user', 'action' => 'view',  'id' => false )));
$r->addRoute('admin.users.create', new R('admin/users/create',     array('module' => 'admin', 'controller' => 'user', 'action' => 'create'               )));
$r->addRoute('admin.users.edit',   new R('admin/users/edit/:id',   array('module' => 'admin', 'controller' => 'user', 'action' => 'edit',  'id' => false )));
$r->addRoute('admin.users.delete', new R('admin/users/delete/:id', array('module' => 'admin', 'controller' => 'user', 'action' => 'delete','id' => false )));
//@formatter:on

/**
 * Dealer Routes
 */
//@formatter:off
$r->addRoute('admin.dealers',                     new R('admin/dealers',                          array('module' => 'admin', 'controller' => 'dealer', 'action' => 'index'                                                 )));
$r->addRoute('admin.dealers.view',                new R('admin/dealers/view/:id',                 array('module' => 'admin', 'controller' => 'dealer', 'action' => 'view',               'id'=> false                      )));
$r->addRoute('admin.dealers.create',              new R('admin/dealers/create',                   array('module' => 'admin', 'controller' => 'dealer', 'action' => 'create',                            'dealerId' => false)));
$r->addRoute('admin.dealers.create-user',         new R('admin/dealers/create-user/:dealerId',    array('module' => 'admin', 'controller' => 'user',   'action' => 'create'                                                )));
$r->addRoute('admin.dealers.edit',                new R('admin/dealers/edit/:id',                 array('module' => 'admin', 'controller' => 'dealer', 'action' => 'edit'                                                  )));
$r->addRoute('admin.dealers.delete',              new R('admin/dealers/delete/:id',               array('module' => 'admin', 'controller' => 'dealer', 'action' => 'delete'                                                )));
$r->addRoute('admin.dealers.users.edit',          new R('admin/dealers/users/edit/:id/:dealerId', array('module' => 'admin', 'controller' => 'user',   'action' => 'edit',               'id' => false, 'dealerId' => false)));
$r->addRoute('admin.dealers.edit-toner-vendors',  new R('admin/dealers/edit-toner-vendors/:id',   array('module' => 'admin', 'controller' => 'dealer', 'action' => 'edit-toner-vendors', 'id' => false                     )));
$r->addRoute('admin.dealers.edit-rms-providers',  new R('admin/dealers/edit-rms-providers/:id',   array('module' => 'admin', 'controller' => 'dealer', 'action' => 'edit-rms-providers', 'id' => false                     )));
//@formatter:on

/**
 * Toners Routes
 */
//@formatter:off
$r->addRoute('admin.fix-toners', new R('admin/fix-toners', array('module' => 'admin', 'controller' => 'fix', 'action' => 'toners')));
//@formatter:on

/**
 * Event Log Route
 */
//@formatter:off
$r->addRoute('admin.event-log',      new R('admin/event-log',      array('module' => 'admin', 'controller' => 'event_log', 'action' => 'index')));
$r->addRoute('admin.event-log.list', new R('admin/event-log/list', array('module' => 'admin', 'controller' => 'event_log', 'action' => 'get-event-logs')));
//$formatter:on


// ***** Dealer Management Routes *************************************************************************************************************************** //

/**
 * Dealer Management Route
 */
//@formatter:off
$r->addRoute('company', new R('company', array('module' => 'dealermanagement', 'controller' => 'index', 'action' => 'index')));
//@formatter:on

/**
 * Dealer Branding Route
 */
//@formatter:off
$r->addRoute('company.branding', new R('company/branding', array('module' => 'dealermanagement', 'controller' => 'branding', 'action' => 'index')));
//@formatter:on

/**
 * Dealer Clients Routes
 */
//@formatter:off
$r->addRoute('company.clients',               new R('company/clients',                array('module' => 'dealermanagement', 'controller' => 'client',           'action' => 'index'                )));
$r->addRoute('company.clients.create',        new R('company/clients/create',         array('module' => 'dealermanagement', 'controller' => 'client',           'action' => 'create'               )));
$r->addRoute('company.clients.edit',          new R('company/clients/edit/:id',       array('module' => 'dealermanagement', 'controller' => 'client',           'action' => 'edit',   'id' => false)));
$r->addRoute('company.clients.view',          new R('company/clients/view/:id',       array('module' => 'dealermanagement', 'controller' => 'client',           'action' => 'view',   'id' => false)));
$r->addRoute('company.clients.delete',        new R('company/clients/delete/:id',     array('module' => 'dealermanagement', 'controller' => 'client',           'action' => 'delete', 'id' => false)));
$r->addRoute('client.pricing',                new R('company/client-pricing',         array('module' => 'proposalgen',      'controller' => 'client-pricing',   'action' => 'index'                )));
$r->addRoute('client.pricing.pricing-upload', new R('company/pricing/pricing-upload', array('module' => 'proposalgen',      'controller' => 'client-pricing',   'action' => 'upload'               )));

// TODO kmccully: no controller in proposalgen called CustomerPricing yet
$r->addRoute('company.customer-pricing',      new R('company/customer-pricing',       array('module' => 'proposalgen',      'controller' => 'customer-pricing', 'action' => 'index'                )));
//@formatter:on

/**
 * Dealer Leasing Schema Routes
 */
//@formatter:off
$r->addRoute('company.leasing-schema',                     new R('company/leasing-schema',                                  array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'index'                                 )));
$r->addRoute('company.leasing-schema.view-provider',       new R('company/leasing-schema/view-provider',                    array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'index'                                 )));

// TODO kmccully: "leasingSchemaId not specified" error
$r->addRoute('company.leasing-schema.view',                new R('company/leasing-schema/view/:leasingSchemaId',            array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'view',     'leasingSchemaId'=> false   )));
$r->addRoute('company.leasing-schema.create',              new R('company/leasing-schema/create',                           array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'create'                                )));
$r->addRoute('company.leasing-schema.edit',                new R('company/leasing-schema/edit/:leasingSchemaId',            array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'edit'                                  )));
$r->addRoute('company.leasing-schema.delete',              new R('company/leasing-schema/delete/:leasingSchemaId',          array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'delete',   'leasingSchemaId'=> false   )));
$r->addRoute('company.leasing-schema.add-term',            new R('company/leasing-schema/add-term/:leasingSchemaId',        array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'addterm',  'leasingSchemaId'=> false   )));
$r->addRoute('company.leasing-schema.edit-term',           new R('company/leasing-schema/edit-term/:id/:leasingSchemaId',   array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'editterm', 'id' => false               )));
$r->addRoute('company.leasing-schema.delete-term',         new R('company/leasing-schema/delete-term/:id/:leasingSchemaId', array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'deleteterm'                            )));
$r->addRoute('company.leasing-schema.add-range',           new R('company/leasing-schema/add-range/:leasingSchemaId',       array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'addrange'                              )));
$r->addRoute('company.leasing-schema.edit-range',          new R('company/leasing-schema/edit-range/:id/:leasingSchemaId',  array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'editrange'                             )));
$r->addRoute('company.leasing-schema.delete-range',        new R('company/leasing-schema/delete-range/:id/:leasingSchemaId',array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'deleterange', 'leasingSchemaId'=> false)));
$r->addRoute('company.leasing-schema.clear-provider',      new R('company/leasing-schema/clear-provider/:leasingSchemaId',  array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'resetschema'                           )));
$r->addRoute('company.leasing-schema.import-leasing-rate', new R('company/leasing-schema/import/:leasingSchemaId',          array('module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'import',      'leasingSchemaId'=> false)));
//@formatter:on

/**
 * Dealer User-Management Routes
 */
//@formatter:off
$r->addRoute('company.users',        new R('company/users',            array('module' => 'dealermanagement', 'controller' => 'user', 'action' => 'index')));

//TODO kmccully: no viewAction in user controller (is in navigation.xml)
$r->addRoute('company.users.view',   new R('company/users/view',       array('module' => 'dealermanagement', 'controller' => 'user', 'action' => 'view')));
$r->addRoute('company.users.create', new R('company/users/create',     array('module' => 'dealermanagement', 'controller' => 'user', 'action' => 'create')));
$r->addRoute('company.users.edit',   new R('company/users/edit/:id',   array('module' => 'dealermanagement', 'controller' => 'user', 'action' => 'edit')));
$r->addRoute('company.users.delete', new R('company/users/delete/:id', array('module' => 'dealermanagement', 'controller' => 'user', 'action' => 'delete')));
//@formatter:on


// ***** Information Routes ************************************************************************************************************************************** //

/**
 * Application Log Route
 */
//@formatter:off
$r->addRoute('app.application-log', new R('application-log', array('module' => 'admin', 'controller' => 'log', 'action' => 'index')));
//@formatter:on

/**
 * About Route
 */
//@formatter:off
$r->addRoute('app.about', new R('about', array('module' => 'default', 'controller' => 'info', 'action' => 'about')));
//@formatter:on

/**
 * EULA Route
 */
//@formatter:off
$r->addRoute('app.eula', new R('eula', array('module' => 'default', 'controller' => 'info', 'action' => 'eula')));
//@formatter:on
