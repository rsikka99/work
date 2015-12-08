<?php
/* @var $r Zend_Controller_Router_Rewrite */
$r = Zend_Controller_Front::getInstance()->getRouter();
use \Zend_Controller_Router_Route as R;

/**
 * Auth Routes
 */

$r->addRoute('auth.login',                       new R('login',                         ['module' => 'default', 'controller' => 'auth', 'action' => 'login'                  ]));
$r->addRoute('auth.logout',                      new R('logout',                        ['module' => 'default', 'controller' => 'auth', 'action' => 'logout'                 ]));
$r->addRoute('auth.forgot-password',             new R('forgot-password',               ['module' => 'default', 'controller' => 'auth', 'action' => 'forgot-password'        ]));
$r->addRoute('auth.forgot-password.reset',       new R('forgot-password/reset/:verify', ['module' => 'default', 'controller' => 'auth', 'action' => 'forgot-password-reset'  ]));
$r->addRoute('auth.login.change-password',       new R('login/change-password',         ['module' => 'default', 'controller' => 'auth', 'action' => 'changepassword'         ]));
$r->addRoute('auth.login.reset-password',        new R('login/reset-password',  ['module' => 'default', 'controller' => 'auth', 'action' => 'reset-password']));


// ***** Home Menu Routes *********************************************************************************************************************************** //


/**
 * Client Routes
 * deprecated, was moved to Dealermanagement_ClientController
 */

#$r->addRoute('clients',                          new R('clients/view-all',          ['module' => 'default', 'controller' => 'index', 'action' => 'view-all-clients']));
#$r->addRoute('clients.create-clients-dashboard', new R('clients/create-new',        ['module' => 'default', 'controller' => 'index', 'action' => 'create-client']));
#$r->addRoute('client.edit-clients-dashboard',    new R('client',                    ['module' => 'default', 'controller' => 'index', 'action' => 'edit-client']));
#$r->addRoute('clients.search-for-client',        new R('clients/search-for-client', ['module' => 'default', 'controller' => 'index', 'action' => 'search-for-client']));


// ***** Report Routes ************************************************************************************************************************************** //

/**
 * RMS Upload Routes
 */

$r->addRoute('rms-upload',                            new R('rms-uploads/:rmsUploadId',                            ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'index',                'rmsUploadId' => false]));
$r->addRoute('rms-upload.mapping',                    new R('rms-uploads/mapping/:rmsUploadId',                    ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'mapping',              'rmsUploadId' => false]));
$r->addRoute('rms-upload.mapping.list',               new R('rms-uploads/mapping/list',                            ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'device-mapping-list'                         ]));
$r->addRoute('rms-upload.mapping.set-mapped-to',      new R('rms-uploads/mapping/set-mapped-to',                   ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'set-mapped-to'                               ]));

#$r->addRoute('rms-upload.mapping.admin-edit-mapping', new R('rms-uploads/mapping/admin-edit-mapping',              ['module' => 'proposalgen', 'controller' => 'managedevices', 'action' => 'managemappingdevices', 'rmsUploadId' => false]));
#$r->addRoute('rms-upload.mapping.user-edit-mapping',  new R('rms-uploads/mapping/user-edit-mapping/:rmsUploadId',  ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'edit-unknown-device',  'rmsUploadId' => false]));

$r->addRoute('rms-upload.summary',                    new R('rms-uploads/summary/:rmsUploadId',                    ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'summary',              'rmsUploadId' => false]));
$r->addRoute('rms-upload.summary.device-list',        new R('rms-uploads/summary/device-list',                     ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'device-summary-list',                        ]));
$r->addRoute('rms-upload.excluded-list',              new R('rms-uploads/excluded-list',                           ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'excluded-list',                              ]));
//missing routes: toggle-excluded-flag, toggle-leased-flag, toggle-managed-flag, device-instance-details

$r->addRoute('rms-upload.realtime',                   new R('rms-uploads/realtime',                                ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'realtime']));

/**
 * Home Route
 */

$r->addRoute('app.dashboard',                     new R('/',                                            ['module' => 'default', 'controller' => 'index', 'action' => 'index'             ]));
$r->addRoute('app.dashboard.change-client',       new R('/clients/change',                              ['module' => 'default', 'controller' => 'index', 'action' => 'change-client'     ]));
#$r->addRoute('app.dashboard.new-client',          new R('/clients/new',                                 ['module' => 'default', 'controller' => 'index', 'action' => 'create-client'    ]));
$r->addRoute('app.dashboard.change-upload',       new R('/rms-uploads/change',                          ['module' => 'default', 'controller' => 'index', 'action' => 'change-upload'     ]));
$r->addRoute('app.dashboard.delete-rms-upload',   new R('/rms-uploads/delete/:rmsUploadId',             ['module' => 'default', 'controller' => 'index', 'action' => 'delete-rms-upload' ]));
$r->addRoute('app.dashboard.select-client',       new R('/select-client',                               ['module' => 'default', 'controller' => 'index', 'action' => 'select-client'     ]));
$r->addRoute('app.dashboard.select-upload',       new R('/select-upload',                               ['module' => 'default', 'controller' => 'index', 'action' => 'select-upload'     ]));
$r->addRoute('app.dashboard.delete-assessment',   new R('/delete-assessment/:assessmentId',             ['module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     ]));
$r->addRoute('app.dashboard.delete-optimization', new R('/delete-optimization/:hardwareOptimizationId', ['module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     ]));
$r->addRoute('app.dashboard.delete-healthcheck',  new R('/delete-healthcheck/:healthcheckId',           ['module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     ]));
$r->addRoute('app.dashboard.delete-quote',        new R('/delete-quote/:quoteId',                       ['module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     ]));
$r->addRoute('app.dashboard.no-clients',          new R('/first-client',                                ['module' => 'default', 'controller' => 'index', 'action' => 'no-clients'        ]));
$r->addRoute('app.dashboard.no-uploads',          new R('/first-upload',                                ['module' => 'default', 'controller' => 'index', 'action' => 'no-uploads'        ]));


/**
 * Assessment Routes
 */

$r->addRoute('assessment',                                  new R('assessment',                                  ['module' => 'assessment', 'controller' => 'index',                         'action' => 'index']));
#$r->addRoute('assessment.select-upload',                    new R('assessment/select-upload',                    ['module' => 'assessment', 'controller' => 'index',                         'action' => 'select-upload']));
$r->addRoute('assessment.survey',                           new R('assessment/survey',                           ['module' => 'assessment', 'controller' => 'index',                         'action' => 'survey']));
$r->addRoute('assessment.settings',                         new R('assessment/settings',                         ['module' => 'assessment', 'controller' => 'index',                         'action' => 'settings']));
$r->addRoute('assessment.report-index',                     new R('assessment/report-index',                     ['module' => 'assessment', 'controller' => 'report_index',                  'action' => 'index']));
$r->addRoute('assessment.report-assessment',                new R('assessment/report-assessment',                ['module' => 'assessment', 'controller' => 'report_assessment',             'action' => 'index']));
$r->addRoute('assessment.report-cost-analysis',             new R('assessment/report-cost-analysis',             ['module' => 'assessment', 'controller' => 'report_costanalysis',           'action' => 'index']));
$r->addRoute('assessment.report-gross-margin',              new R('assessment/report-gross-margin',              ['module' => 'assessment', 'controller' => 'report_grossmargin',            'action' => 'index']));
$r->addRoute('assessment.report-toner-vendor-gross-margin', new R('assessment/report-toner-vendor-gross-margin', ['module' => 'assessment', 'controller' => 'report_tonervendorgrossmargin', 'action' => 'index']));
$r->addRoute('assessment.report-jit-supply-and-toner-sku',  new R('assessment/report-jit-supply-and-toner-sku',  ['module' => 'assessment', 'controller' => 'report_toners',                 'action' => 'index']));
$r->addRoute('assessment.report-old-device-list',           new R('assessment/report-old-device-list',           ['module' => 'assessment', 'controller' => 'report_olddevicelist',          'action' => 'index']));
$r->addRoute('assessment.report-printing-device-list',      new R('assessment/report-printing-device-list',      ['module' => 'assessment', 'controller' => 'report_printingdevicelist',     'action' => 'index']));
$r->addRoute('assessment.report-solution',                  new R('assessment/report-solution',                  ['module' => 'assessment', 'controller' => 'report_solution',               'action' => 'index']));
$r->addRoute('assessment.report-lease-buy-back',            new R('assessment/report-lease-buyback',             ['module' => 'assessment', 'controller' => 'report_leasebuyback',           'action' => 'index']));
$r->addRoute('assessment.report-fleet-attributes',          new R('assessment/report-fleet-attributes',          ['module' => 'assessment', 'controller' => 'report_fleetattributes',        'action' => 'index']));
$r->addRoute('assessment.report-utilization',               new R('assessment/report-utilization',               ['module' => 'assessment', 'controller' => 'report_utilization',            'action' => 'index']));

/**
 * Hardware Optimization Routes
 */

$r->addRoute('hardwareoptimization',                              new R('hardwareoptimization',                              ['module' => 'hardwareoptimization', 'controller' => 'index',                        'action' => 'index']));
#$r->addRoute('hardwareoptimization.select-upload',                new R('hardwareoptimization/select-upload',                ['module' => 'hardwareoptimization', 'controller' => 'index',                        'action' => 'select-upload']));
$r->addRoute('hardwareoptimization.settings',                     new R('hardwareoptimization/settings',                     ['module' => 'hardwareoptimization', 'controller' => 'index',                        'action' => 'settings']));
$r->addRoute('hardwareoptimization.optimization',                 new R('hardwareoptimization/optimization',                 ['module' => 'hardwareoptimization', 'controller' => 'index',                        'action' => 'optimize']));
$r->addRoute('hardwareoptimization.report-index',                 new R('hardwareoptimization/report-index',                 ['module' => 'hardwareoptimization', 'controller' => 'report_index',                 'action' => 'index']));
$r->addRoute('hardwareoptimization.report-customer-optimization', new R('hardwareoptimization/report-customer-optimization', ['module' => 'hardwareoptimization', 'controller' => 'report_customer_optimization', 'action' => 'index']));
$r->addRoute('hardwareoptimization.report-dealer-optimization',   new R('hardwareoptimization/report-dealer-optimization',   ['module' => 'hardwareoptimization', 'controller' => 'report_dealer_optimization',   'action' => 'index']));

/**
 * Healthcheck Routes
 */

$r->addRoute('healthcheck',                new R('healthcheck',                            ['module' => 'healthcheck', 'controller' => 'index',                      'action' => 'index']));
#$r->addRoute('healthcheck.select-upload',  new R('healthcheck/select-upload',              ['module' => 'healthcheck', 'controller' => 'index',                      'action' => 'select-upload']));
$r->addRoute('healthcheck.settings',       new R('healthcheck/settings',                   ['module' => 'healthcheck', 'controller' => 'index',                      'action' => 'settings']));
$r->addRoute('healthcheck.report',         new R('healthcheck/report-healthcheck',         ['module' => 'healthcheck', 'controller' => 'report_healthcheck',         'action' => 'index']));
$r->addRoute('healthcheck.report-printiq', new R('healthcheck/printiq-report-healthcheck', ['module' => 'healthcheck', 'controller' => 'report_printiq_healthcheck', 'action' => 'index']));
$r->addRoute('healthcheck.report.quadrant',new R('healthcheck/report-healthcheck-quadrant',['module' => 'healthcheck', 'controller' => 'report_healthcheck',         'action' => 'quadrant']));
$r->addRoute('healthcheck.device-age',     new R('healthcheck/report-device-age',          ['module' => 'healthcheck', 'controller' => 'report_healthcheck',         'action' => 'age']));


//$r->addRoute('quotes.index',          new R('quotes/index/:clientId',          ['module' => 'quotegen', 'controller' => 'index', 'action' => 'index',          'clientId' => false]));
//$r->addRoute('quotes.existing-quote', new R('quotes/existing-quote/:clientId', ['module' => 'quotegen', 'controller' => 'index', 'action' => 'existing-quote', 'clientId' => false]));
//$r->addRoute('quotes.create-client',  new R('quotes/create-client',            ['module' => 'quotegen', 'controller' => 'index', 'action' => 'create-client'                      ]));


/**
 * Quote Menu Routes
 *
 * TODO kmccully: Create-quotes needs nav bar options to be non-selectable, breaks
 */

$r->addRoute('quotes',                                 new R('quotes/:quoteId',                                                                     ['module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'index',                                                           ]));
$r->addRoute('quotes.add-hardware',                    new R('quotes/:quoteId/add-hardware',                                                        ['module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'index',                                                           ]));
$r->addRoute('quotes.add-hardware.edit',               new R('quotes/add-hardware/edit-device/:id/:quoteId',                                        ['module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'edit-quote-device',              'id' => false,                   ]));
$r->addRoute('quotes.add-hardware.edit.add-options',   new R('quotes/add-hardware/edit-device/add-options/:id/:quoteId',                            ['module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'add-options-to-quote-device',    'id' => false,                   ]));
$r->addRoute('quotes.add-hardware.delete',             new R('quotes/add-hardware/delete/:id/:quoteId',                                             ['module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'delete-quote-device'                                              ]));
$r->addRoute('quotes.sync-all-device-configurations',  new R('quotes/sync-all-device-configurations/:quoteId',                                      ['module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'sync-all-device-configurations', 'id' => false                    ]));
$r->addRoute('quotes.sync-device-configurations',      new R('quotes/sync-device-configurations/:id/:quoteId',                                      ['module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'sync-device-configuration'                                        ]));
$r->addRoute('quotes.use-configuration',               new R('quotes/use-configuration/:deviceId/:configurationId',                                 ['module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'use-configuration'                                                ]));
$r->addRoute('quotes.delete-option-from-quote-device', new R('quotes/delete-option-from-quote-device/:quoteId/:quoteDeviceId/:quoteDeviceOptionId', ['module' => 'quotegen', 'controller' => 'quote_devices',       'action' => 'delete-option-from-quote-device'                                  ]));
$r->addRoute('quotes.group-devices',                   new R('quotes/:quoteId/group-devices',                                                       ['module' => 'quotegen', 'controller' => 'quote_groups',        'action' => 'index',                                                           ]));
$r->addRoute('quotes.manage-pages',                    new R('quotes/:quoteId/manage-pages',                                                        ['module' => 'quotegen', 'controller' => 'quote_pages',         'action' => 'index',                                                           ]));
$r->addRoute('quotes.hardware-financing',              new R('quotes/:quoteId/hardware-financing',                                                  ['module' => 'quotegen', 'controller' => 'quote_profitability', 'action' => 'index'                                                            ]));
$r->addRoute('quotes.reports',                         new R('quotes/:quoteId/reports',                                                             ['module' => 'quotegen', 'controller' => 'quote_reports',       'action' => 'index',                                                           ]));
$r->addRoute('quotes.reports.purchase',                new R('quotes/:quoteId/reports/purchase/:format',                                            ['module' => 'quotegen', 'controller' => 'quote_reports',       'action' => 'purchase-quote'                                                   ]));
$r->addRoute('quotes.reports.lease',                   new R('quotes/:quoteId/reports/lease/:format',                                               ['module' => 'quotegen', 'controller' => 'quote_reports',       'action' => 'lease-quote'                                                      ]));
$r->addRoute('quotes.reports.order-list',              new R('quotes/:quoteId/reports/order-list/:format',                                          ['module' => 'quotegen', 'controller' => 'quote_reports',       'action' => 'order-list'                                                       ]));
$r->addRoute('quotes.reports.contract',                new R('quotes/:quoteId/reports/contract/:format',                                            ['module' => 'quotegen', 'controller' => 'quote_reports',       'action' => 'contract'                                                         ]));

/**
 * Hardware Quotes - Option Categories - Not in navigation.xml
 */

$r->addRoute('quotes.category-options',        new R('quotes/categories',                   ['module' => 'quotegen', 'controller' => 'category', 'action' => 'index'                ]));
$r->addRoute('quotes.category-options.view',   new R('quotes/categories/view/:id',          ['module' => 'quotegen', 'controller' => 'category', 'action' => 'view',   'id' => false]));
$r->addRoute('quotes.category-options.create', new R('quotes/category-options/create',      ['module' => 'quotegen', 'controller' => 'category', 'action' => 'create'               ]));
$r->addRoute('quotes.category-options.edit',   new R('quotes/category-options/edit/:id',    ['module' => 'quotegen', 'controller' => 'category', 'action' => 'edit',   'id' => false]));
$r->addRoute('quotes.category-options.delete', new R('quotes/category-options/delete/:id',  ['module' => 'quotegen', 'controller' => 'category', 'action' => 'delete', 'id' => false]));

/**
 * Hardware Quotes - Configurations - Not in navigation.xml
 */

$r->addRoute('quotes.configurations',             new R('quotes/configurations',                                ['module' => 'quotegen', 'controller' => 'configuration', 'action' => 'index'                                                         ]));

// TODO kmccully: no viewAction in configuration controller (not used anywhere)
$r->addRoute('quotes.configurations.view',        new R('quotes/configurations/view',                           ['module' => 'quotegen', 'controller' => 'configuration', 'action' => 'view'                                                          ]));
$r->addRoute('quotes.configurations.create',      new R('quotes/configurations/create',                         ['module' => 'quotegen', 'controller' => 'configuration', 'action' => 'create'                                                        ]));
$r->addRoute('quotes.configurations.create.id',   new R('quotes/configurations/create/:id/:page',               ['module' => 'quotegen', 'controller' => 'configuration', 'action' => 'create', 'id'              => false, 'page' => 'configurations']));
$r->addRoute('quotes.configurations.edit',        new R('quotes/configurations/edit/:configurationid',          ['module' => 'quotegen', 'controller' => 'configuration', 'action' => 'edit',   'configurationid' => false                            ]));
$r->addRoute('quotes.configurations.delete',      new R('quotes/configurations/delete/:configurationid',        ['module' => 'quotegen', 'controller' => 'configuration', 'action' => 'delete', 'configurationid' => false                            ]));
$r->addRoute('quotes.configurations.edit.page',   new R('quotes/configurations/edit/:configurationid/:page',    ['module' => 'quotegen', 'controller' => 'configuration', 'action' => 'edit',   'configurationid' => false, 'page' => 'configurations']));
$r->addRoute('quotes.configurations.delete.page', new R('quotes/configurations/delete/:configurationid/:page',  ['module' => 'quotegen', 'controller' => 'configuration', 'action' => 'delete', 'configurationid' => false, 'page' => 'configurations']));

/**
 * Hardware Quotes - Devices - Not in navigation.xml
 */

$r->addRoute('quotes.devices',               new R('quotes/devices',                               ['module' => 'quotegen', 'controller' => 'device', 'action' => 'index'                                           ]));

// TODO kmccully: "function getMasterDevice() on a non-object" error
$r->addRoute('quotes.devices.view',          new R('quotes/devices/view/:id',                      ['module' => 'quotegen', 'controller' => 'device', 'action' => 'view'                                            ]));

// TODO kmccully: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'md.manufacturer_id' in 'order clause'
$r->addRoute('quotes.devices.create',        new R('quotes/devices/create',                        ['module' => 'quotegen', 'controller' => 'device', 'action' => 'create'                                          ]));

// TODO kmccully: "error selecting device to edit
$r->addRoute('quotes.devices.edit',          new R('quotes/devices/edit/:id',                      ['module' => 'quotegen', 'controller' => 'device', 'action' => 'edit',         'id' => false                     ]));

// TODO kmccully: "error selecting device to delete
$r->addRoute('quotes.devices.delete',        new R('quotes/devices/delete/:id',                    ['module' => 'quotegen', 'controller' => 'device', 'action' => 'delete',       'id' => false                     ]));

// TODO kmccully: "select a device to edit first"
$r->addRoute('quotes.devices.delete-option', new R('quotes/devices/delete-options/:id/:optionId',  ['module' => 'quotegen', 'controller' => 'device', 'action' => 'deleteoption', 'id' => false, 'optionId' => false]));

// TODO kmccully: toArray() on a non-object
$r->addRoute('quotes.devices.add-options',   new R('quotes/devices/add-options/:optionId',         ['module' => 'quotegen', 'controller' => 'device', 'action' => 'addoptions',                  'optionId' => false]));

/**
 * Hardware Quotes - Device Configurations - Not in navigation.xml
 */

$r->addRoute('quotes.device-configurations',               new R('quotes/device-configurations',                             ['module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'index']));
$r->addRoute('quotes.device-configurations.view',          new R('quotes/device-configurations/view/:id',                    ['module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'view']));

// TODO kmccully: Error saving device-configuration
$r->addRoute('quotes.device-configurations.create',        new R('quotes/device-configurations/create',                      ['module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'create']));
$r->addRoute('quotes.device-configurations.edit',          new R('quotes/device-configurations/edit/:id',                    ['module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'edit']));
$r->addRoute('quotes.device-configurations.add-option',    new R('quotes/device-configurations/add-option/:id',              ['module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'addoption']));
$r->addRoute('quotes.device-configurations.delete',        new R('quotes/device-configurations/delete/:id',                  ['module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'delete']));
$r->addRoute('quotes.device-configurations.delete-option', new R('quotes/device-configurations/delete-option/:id/:optionId', ['module' => 'quotegen', 'controller' => 'deviceconfiguration', 'action' => 'deleteoption']));


// ***** Hardware Library Routes **************************************************************************************************************************** //

/**
 * Device Management Routes
 */

$r->addRoute('hardware-library.devices',                       new R('hardware-library/devices',                       ['module' => 'hardware-library', 'controller' => 'index',          'action' => 'index'                ]));
$r->addRoute('hardware-library.devices',                       new R('hardware-library/devices/search',                ['module' => 'proposalgen',      'controller' => 'admin',          'action' => 'search-for-device'    ]));
$r->addRoute('hardware-library.devices.grid-list',             new R('hardware-library/devices/grid-list',             ['module' => 'hardware-library', 'controller' => 'devices',        'action' => 'grid-list'            ]));
$r->addRoute('hardware-library.devices.load-forms',            new R('hardware-library/devices/load-forms',            ['module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'manage-master-devices']));
$r->addRoute('hardware-library.devices.update-master-device',  new R('hardware-library/devices/update-master-device',  ['module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'update-master-device' ]));
$r->addRoute('hardware-library.devices.delete',                new R('hardware-library/devices/delete-jqgrid',         ['module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'delete'               ]));
$r->addRoute('hardware-library.devices.image',                 new R('hardware-library/devices/image/:id',             ['module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'image'               ]));
$r->addRoute('hardware-library.devices.delete',                new R('hardware-library/devices/delete',                ['module' => 'quotegen',         'controller' => 'devicesetup',    'action' => 'delete'               ]));
$r->addRoute('hardware-library.devices.toner-list',            new R('hardware-library/devices/toner-list',            ['module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'assigned-toner-list'  ]));
$r->addRoute('hardware-library.devices.toners',                new R('hardware-library/devices/toners',                ['module' => 'hardware-library', 'controller' => 'devices',        'action' => 'add-toner'            ]));
$r->addRoute('hardware-library.devices.toners.remove',         new R('hardware-library/devices/toners/remove',         ['module' => 'hardware-library', 'controller' => 'devices',        'action' => 'remove-toner'         ]));
$r->addRoute('hardware-library.devices.available-toners-list', new R('hardware-library/devices/available-toners-list', ['module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'available-toners-list']));

$r->addRoute('hardware-library.toners.colors-for-configuration', new R('hardware-library/toners/colors-for-configuration', ['module' => 'hardware-library', 'controller' => 'toner', 'action' => 'colors-for-configuration']));

$r->addRoute('hardware-library.configurations.list',        new R('hardware-library/configurations/list',        ['module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'hardware-configuration-list']));
$r->addRoute('hardware-library.configurations.reload-form', new R('hardware-library/configurations/reload-form', ['module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'reload-hardware-configurations-form']));

// FIXME lrobert: Fix this damn function to be a properly separated api
$r->addRoute('hardware-library.sauron', new R('hardware-library/sauron', ['module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'sauron']));

$r->addRoute('api.devices',           new R('api/v1/devices/:deviceId',        ['module' => 'api', 'controller' => 'devices', 'action' => 'index', 'deviceId' => false]));
$r->addRoute('api.devices.grid-list', new R('api/v1/devices/grid-list',        ['module' => 'api', 'controller' => 'devices', 'action' => 'grid-list']));
#$r->addRoute('api.devices.create',    new R('api/v1/devices/create',           ['module' => 'api', 'controller' => 'devices', 'action' => 'create']));
#$r->addRoute('api.devices.delete',    new R('api/v1/devices/:deviceId/delete', ['module' => 'api', 'controller' => 'devices', 'action' => 'delete']));
#$r->addRoute('api.devices.save',      new R('api/v1/devices/:deviceId/save',   ['module' => 'api', 'controller' => 'devices', 'action' => 'save']));

#$r->addRoute('api.devices.toners',        new R('api/v1/devices/:deviceId/toners/:tonerId',        ['module' => 'api', 'controller' => 'devices', 'action' => 'view-toners', 'tonerId' => false]));
#$r->addRoute('api.devices.toners.create', new R('api/v1/devices/:deviceId/toners/create',          ['module' => 'api', 'controller' => 'devices', 'action' => 'add-toner']));
#$r->addRoute('api.devices.toners.delete', new R('api/v1/devices/:deviceId/toners/delete/:tonerId', ['module' => 'api', 'controller' => 'devices', 'action' => 'remove-toner']));

#$r->addRoute('api.manufacturers',        new R('api/v1/manufacturers/:manufacturerId',        ['module' => 'api', 'controller' => 'manufacturers', 'action' => 'index', 'manufacturerId' => false]));
#$r->addRoute('api.manufacturers.create', new R('api/v1/manufacturers/create',                 ['module' => 'api', 'controller' => 'manufacturers', 'action' => 'create']));
#$r->addRoute('api.manufacturers.delete', new R('api/v1/manufacturers/:manufacturerId/delete', ['module' => 'api', 'controller' => 'manufacturers', 'action' => 'delete']));
#$r->addRoute('api.manufacturers.save',   new R('api/v1/manufacturers/:manufacturerId/save',   ['module' => 'api', 'controller' => 'manufacturers', 'action' => 'update']));

$r->addRoute('api.computers',           new R('api/v1/computers/:deviceId',        ['module' => 'api', 'controller' => 'computers', 'action' => 'index', 'deviceId' => false]));
$r->addRoute('api.computers.grid-list', new R('api/v1/computers/grid-list',        ['module' => 'api', 'controller' => 'computers', 'action' => 'grid-list']));

$r->addRoute('api.peripherals',           new R('api/v1/peripherals/:deviceId',        ['module' => 'api', 'controller' => 'peripherals', 'action' => 'index', 'deviceId' => false]));
$r->addRoute('api.peripherals.grid-list', new R('api/v1/peripherals/grid-list',        ['module' => 'api', 'controller' => 'peripherals', 'action' => 'grid-list']));

$r->addRoute('api.rms',                     new R('api/v1/rms/:rmsId',                   ['module' => 'api', 'controller' => 'rms', 'action' => 'index', 'rmsId' => false]));
$r->addRoute('api.rms.printaudit-push',     new R('api/v1/rms/printaudit/:clientId',     ['module' => 'api', 'controller' => 'rms', 'action' => 'printaudit-push', 'clientId' => false]));

// API for clients

$r->addRoute('api.clients',           new R('api/v1/clients/:clientId', ['module' => 'api', 'controller' => 'client', 'action' => 'index', 'clientId' => false]));

// API for countries

$r->addRoute('api.countries',           new R('api/v1/countries',              ['module' => 'api', 'controller' => 'country', 'action' => 'index']));
$r->addRoute('api.countries.country',   new R('api/v1/countries/:countryId',   ['module' => 'api', 'controller' => 'country', 'action' => 'index' ]));

/**
 * View All Devices Route
 */

$r->addRoute('hardware-library.all-devices',                new R('hardware-library/all-devices',                    ['module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'index'                      ]));
$r->addRoute('hardware-library.computers',                  new R('hardware-library/computers',                      ['module' => 'hardware-library', 'controller' => 'computers', 'action' => 'index'                      ]));
$r->addRoute('hardware-library.peripherals',                new R('hardware-library/peripherals',                    ['module' => 'hardware-library', 'controller' => 'peripherals', 'action' => 'index'                      ]));

// TODO kmccully: blank page
$r->addRoute('hardware-library.all-devices.create',         new R('hardware-library/all-devices/create',             ['module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'create'                     ]));
$r->addRoute('hardware-library.all-devices.edit',           new R('hardware-library/all-devices/edit/:id',           ['module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'edit'                       ]));
$r->addRoute('hardware-library.all-devices.toners',         new R('hardware-library/all-devices/toners/:id',         ['module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'toners'                     ]));

// TODO kmccully: getMasterDevice() on a non-object line 866 deviceSetup controller
$r->addRoute('hardware-library.all-devices.options',        new R('hardware-library/all-devices/options/:id',        ['module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'options',      'id' => false]));

// TODO kmccully: getMasterDevice() on a non-object line 1059 deviceSetup controller
$r->addRoute('hardware-library.all-devices.configurations', new R('hardware-library/all-devices/configurations/:id', ['module' => 'quotegen', 'controller' => 'devicesetup', 'action' => 'configurations'             ]));

/**
 * Quotes - Option controller
 * Not sure where this goes/applies yet in the information structure
 */

$r->addRoute('quotes.options',        new R('quotes/options/:id/:page',        ['module' => 'quotegen', 'controller' => 'option', 'action' => 'index',  'id' => false, 'page' => 'options']));
$r->addRoute('quotes.options.create', new R('quotes/options/create/:id/:page', ['module' => 'quotegen', 'controller' => 'option', 'action' => 'create', 'id' => false, 'page' => 'options']));

// TODO kmccully: "function getCategories() on non-object" error
$r->addRoute('quotes.options.view',   new R('quotes/options/view/:id/:page',   ['module' => 'quotegen', 'controller' => 'option', 'action' => 'view',   'id' => false, 'page' => 'options']));

// TODO kmccully: toArray() on a non-object
$r->addRoute('quotes.options.edit',   new R('quotes/options/edit/:id/:page',   ['module' => 'quotegen', 'controller' => 'option', 'action' => 'edit',   'id' => false, 'page' => 'options']));
$r->addRoute('quotes.options.delete', new R('quotes/options/delete/:id/:page', ['module' => 'quotegen', 'controller' => 'option', 'action' => 'delete', 'id' => false, 'page' => 'options']));

/**
 * All Toners
 */

$r->addRoute('hardware-library.all-toners',       new R('hardware-library/all-toners',       ['module' => 'hardware-library', 'controller' => 'toner', 'action' => 'index'           ]));
$r->addRoute('hardware-library.toners.load-form', new R('hardware-library/toners/load-form', ['module' => 'hardware-library', 'controller' => 'toner', 'action' => 'load-form'       ]));
$r->addRoute('hardware-library.toners.save',      new R('hardware-library/toners/save',      ['module' => 'hardware-library', 'controller' => 'toner', 'action' => 'save'            ]));
$r->addRoute('hardware-library.all-toners-list',  new R('hardware-library/all-toners-list',  ['module' => 'hardware-library', 'controller' => 'toner', 'action' => 'all-toners-list' ]));
$r->addRoute('hardware-library.toners.image',     new R('hardware-library/toners/image/:id', ['module' => 'hardware-library', 'controller' => 'toner', 'action' => 'image'           ]));

/**
 * All Options
 */

$r->addRoute('hardware-library.options',             new R('hardware-library/options',             ['module' => 'hardware-library', 'controller' => 'option',         'action' => 'option-list'  ]));
$r->addRoute('hardware-library.options.load-form',   new R('hardware-library/options/load-form',   ['module' => 'hardware-library', 'controller' => 'option',         'action' => 'load-form'    ]));
$r->addRoute('hardware-library.options.save',        new R('hardware-library/options/save',        ['module' => 'hardware-library', 'controller' => 'option',         'action' => 'save'         ]));
$r->addRoute('hardware-library.options.delete',      new R('hardware-library/options/delete',      ['module' => 'hardware-library', 'controller' => 'option',         'action' => 'delete'       ]));
$r->addRoute('hardware-library.options.option-list', new R('hardware-library/options/option-list', ['module' => 'hardware-library', 'controller' => 'manage-devices', 'action' => 'options-list' ]));

/**
 * Bulk Hardware/Pricing Updates Routes
 */

$r->addRoute('hardware-library.bulk-hardware-pricing-updates',                           new R('hardware-library/bulk-hardware-pricing-updates', ['module' => 'proposalgen', 'controller' => 'costs', 'action' => 'bulkdevicepricing']));
$r->addRoute('hardware-library.bulk-hardware-pricing-updates.export-pricing',            new R('hardware-library/export-pricing',                ['module' => 'proposalgen', 'controller' => 'costs', 'action' => 'export-pricing']));
$r->addRoute('hardware-library.bulk-hardware-pricing-updates.bulk-file-device-pricing',  new R('hardware-library/bulk-file-device-pricing',      ['module' => 'proposalgen', 'controller' => 'costs', 'action' => 'bulk-file-device-pricing']));
$r->addRoute('hardware-library.bulk-hardware-pricing-updates.bulk-file-device-features', new R('hardware-library/bulk-file-device-features',     ['module' => 'proposalgen', 'controller' => 'costs', 'action' => 'bulk-file-device-features']));
$r->addRoute('hardware-library.bulk-hardware-pricing-updates.bulk-file-toner-pricing',   new R('hardware-library/bulk-file-toner-pricing',       ['module' => 'proposalgen', 'controller' => 'costs', 'action' => 'bulk-file-toner-pricing']));
$r->addRoute('hardware-library.bulk-hardware-pricing-updates.bulk-file-toner-matchup',   new R('hardware-library/bulk-file-toner-matchup',       ['module' => 'proposalgen', 'controller' => 'costs', 'action' => 'bulk-file-toner-matchup']));

/**
 * Device Swaps Routes
 */

$r->addRoute('hardware-library.device-swaps', new R('hardware-library/device-swaps', ['module' => 'hardwareoptimization', 'controller' => 'deviceswaps', 'action' => 'index']));

/**
 * Manufacturers Routes
 */

$r->addRoute('hardware-library.manufacturers',        new R('hardware-library/manufacturers',            ['module' => 'hardware-library', 'controller' => 'manufacturer', 'action' => 'index'                ]));
$r->addRoute('hardware-library.manufacturers.view',   new R('hardware-library/manufacturers/view/:id',   ['module' => 'hardware-library', 'controller' => 'manufacturer', 'action' => 'view',   'id' => false]));
$r->addRoute('hardware-library.manufacturers.create', new R('hardware-library/manufacturers/create',     ['module' => 'hardware-library', 'controller' => 'manufacturer', 'action' => 'create'               ]));
$r->addRoute('hardware-library.manufacturers.edit',   new R('hardware-library/manufacturers/edit/:id',   ['module' => 'hardware-library', 'controller' => 'manufacturer', 'action' => 'edit',   'id' => false]));
$r->addRoute('hardware-library.manufacturers.delete', new R('hardware-library/manufacturers/delete/:id', ['module' => 'hardware-library', 'controller' => 'manufacturer', 'action' => 'delete', 'id' => false]));

/**
 * Manage Printer Matchups Routes
 */

$r->addRoute('hardware-library.managematchups', new R('hardware-library/manage-printer-matchups', ['module' => 'proposalgen', 'controller' => 'admin', 'action' => 'managematchups']));


// ***** Settings Routes ************************************************************************************************************************************ //

/**
 * Settings Routes
 */

$r->addRoute('settings', new R('settings', ['module' => 'preferences', 'controller' => 'index', 'action' => 'index']));

/**
 * Report Settings Routes
 */

$r->addRoute('report-settings',         new R('client/settings',         ['module' => 'preferences', 'controller' => 'client', 'action' => 'index']));
$r->addRoute('company.report-settings', new R('company/report-settings', ['module' => 'preferences', 'controller' => 'dealer', 'action' => 'index']));
$r->addRoute('company.shop-settings',   new R('company/shop-settings',   ['module' => 'preferences', 'controller' => 'dealer', 'action' => 'shop']));

/**
 * My Profile Routes
 */

$r->addRoute('profile', new R('settings/profile', ['module' => 'admin', 'controller' => 'user', 'action' => 'profile']));

/**
 *  My Settings Routes
 */


// TODO kmccully: no userAction in index controller in Preferences module yet
$r->addRoute('my-settings', new R('settings/my-settings', ['module' => 'preferences', 'controller' => 'index', 'action' => 'user']));

/**
 * User Settings Routes
 */


// TODO kmccully: no userAction in index controller (is in navigation.xml)
$r->addRoute('settings.user',                       new R('settings/user',                       ['module' => 'preferences', 'controller' => 'index',                'action' => 'user']));
$r->addRoute('settings.user.assessment',            new R('settings/user/assessment',            ['module' => 'preferences', 'controller' => 'proposal',             'action' => 'user']));
$r->addRoute('settings.user.hardware-quote',        new R('settings/user/hardware-quote',        ['module' => 'preferences', 'controller' => 'quote',                'action' => 'user']));
$r->addRoute('settings.user.health-check',          new R('settings/user/health-check',          ['module' => 'preferences', 'controller' => 'healthcheck',          'action' => 'user']));
$r->addRoute('settings.user.hardware-optimization', new R('settings/user/hardware-optimization', ['module' => 'preferences', 'controller' => 'hardwareoptimization', 'action' => 'user']));

/**
 * Dealer Settings Routes
 */


// TODO kmccully: no dealerAction in index controller (is in navigation.xml)
$r->addRoute('settings.dealer',                       new R('settings/dealer',                       ['module' => 'preferences', 'controller' => 'index',                'action' => 'dealer']));
$r->addRoute('settings.dealer.assessment',            new R('settings/dealer/assessment',            ['module' => 'preferences', 'controller' => 'proposal',             'action' => 'dealer']));
$r->addRoute('settings.dealer.hardware-quote',        new R('settings/dealer/hardware-quote',        ['module' => 'preferences', 'controller' => 'quote',                'action' => 'dealer']));
$r->addRoute('settings.dealer.health-check',          new R('settings/dealer/health-check',          ['module' => 'preferences', 'controller' => 'healthcheck',          'action' => 'dealer']));
$r->addRoute('settings.dealer.hardware-optimization', new R('settings/dealer/hardware-optimization', ['module' => 'preferences', 'controller' => 'hardwareoptimization', 'action' => 'dealer']));

/**
 * System Settings Routes
 */


// TODO kmccully: no systemAction in index controller (is in navigation.xml)
$r->addRoute('settings.system',                       new R('settings/system',                       ['module' => 'preferences', 'controller' => 'index',                'action' => 'system']));
$r->addRoute('settings.system.assessment',            new R('settings/system/assessment',            ['module' => 'preferences', 'controller' => 'proposal',             'action' => 'system']));
$r->addRoute('settings.system.hardware-quote',        new R('settings/system/hardware-quote',        ['module' => 'preferences', 'controller' => 'quote',                'action' => 'system']));
$r->addRoute('settings.system.health-check',          new R('settings/system/health-check',          ['module' => 'preferences', 'controller' => 'healthcheck',          'action' => 'system']));
$r->addRoute('settings.system.hardware-optimization', new R('settings/system/hardware-optimization', ['module' => 'preferences', 'controller' => 'hardwareoptimization', 'action' => 'system']));


// ***** System Administration Routes *********************************************************************************************************************** //

/**
 * System Administration Route
 */

$r->addRoute('admin', new R('admin', ['module' => 'admin', 'controller' => 'index', 'action' => 'index']));

/**
 * Dealer Onboarding Route
 */

$r->addRoute('admin.dealer-on-boarding', new R('admin/dealer-on-boarding', ['module' => 'admin', 'controller' => 'onboarding', 'action' => 'index']));

/**
 * Client Routes
 */

$r->addRoute('admin.clients',        new R('admin/clients',                ['module' => 'admin', 'controller' => 'client',  'action' => 'index'                      ]));

// TODO kmccully: no viewAction in client controller (is in navigation.xml)
$r->addRoute('admin.clients.view',   new R('admin/clients/view/:clientId', ['module' => 'admin', 'controller' => 'client',  'action' => 'view',   'clientId' => false]));
$r->addRoute('admin.clients.create', new R('admin/clients/create',         ['module' => 'admin', 'controller' => 'client',  'action' => 'create'                     ]));
$r->addRoute('admin.clients.edit',   new R('admin/clients/edit/:id',       ['module' => 'admin', 'controller' => 'client',  'action' => 'edit',   'id' => false      ]));
$r->addRoute('admin.clients.delete', new R('admin/clients/delete/:id',     ['module' => 'admin', 'controller' => 'client',  'action' => 'delete', 'id' => false      ]));

/**
 * User Routes
 */

$r->addRoute('admin.users',        new R('admin/users',            ['module' => 'admin', 'controller' => 'user', 'action' => 'index'                ]));

// TODO kmccully: no viewAction in user controller (is in navigation.xml)
$r->addRoute('admin.users.view',   new R('admin/users/view/:id',   ['module' => 'admin', 'controller' => 'user', 'action' => 'view',  'id' => false ]));
$r->addRoute('admin.users.create', new R('admin/users/create',     ['module' => 'admin', 'controller' => 'user', 'action' => 'create'               ]));
$r->addRoute('admin.users.edit',   new R('admin/users/edit/:id',   ['module' => 'admin', 'controller' => 'user', 'action' => 'edit',  'id' => false ]));
$r->addRoute('admin.users.delete', new R('admin/users/delete/:id', ['module' => 'admin', 'controller' => 'user', 'action' => 'delete','id' => false ]));

/**
 * Dealer Routes
 */

$r->addRoute('admin.dealers',                     new R('admin/dealers',                          ['module' => 'admin', 'controller' => 'dealer', 'action' => 'index'                                                 ]));
$r->addRoute('admin.dealers.view',                new R('admin/dealers/view/:id',                 ['module' => 'admin', 'controller' => 'dealer', 'action' => 'view',               'id'=> false                      ]));
$r->addRoute('admin.dealers.create',              new R('admin/dealers/create',                   ['module' => 'admin', 'controller' => 'dealer', 'action' => 'create',                            'dealerId' => false]));
$r->addRoute('admin.dealers.create-user',         new R('admin/dealers/create-user/:dealerId',    ['module' => 'admin', 'controller' => 'user',   'action' => 'create'                                                ]));
$r->addRoute('admin.dealers.edit',                new R('admin/dealers/edit/:id',                 ['module' => 'admin', 'controller' => 'dealer', 'action' => 'edit'                                                  ]));
$r->addRoute('admin.dealers.delete',              new R('admin/dealers/delete/:id',               ['module' => 'admin', 'controller' => 'dealer', 'action' => 'delete'                                                ]));
$r->addRoute('admin.dealers.users.edit',          new R('admin/dealers/users/edit/:id/:dealerId', ['module' => 'admin', 'controller' => 'user',   'action' => 'edit',               'id' => false, 'dealerId' => false]));
$r->addRoute('admin.dealers.edit-toner-vendors',  new R('admin/dealers/edit-toner-vendors/:id',   ['module' => 'admin', 'controller' => 'dealer', 'action' => 'edit-toner-vendors', 'id' => false                     ]));
$r->addRoute('admin.dealers.edit-rms-providers',  new R('admin/dealers/edit-rms-providers/:id',   ['module' => 'admin', 'controller' => 'dealer', 'action' => 'edit-rms-providers', 'id' => false                     ]));

/**
 * Toners Routes
 */

$r->addRoute('admin.fix-toners', new R('admin/fix-toners', ['module' => 'admin', 'controller' => 'fix', 'action' => 'toners']));

/**
 * Event Log Route
 */

$r->addRoute('admin.event-log',      new R('admin/event-log',      ['module' => 'admin', 'controller' => 'event_log', 'action' => 'index']));
$r->addRoute('admin.event-log.list', new R('admin/event-log/list', ['module' => 'admin', 'controller' => 'event_log', 'action' => 'get-event-logs']));
//$formatter:on


// ***** Dealer Management Routes *************************************************************************************************************************** //

/**
 * Dealer Management Route
 */

$r->addRoute('company', new R('company', ['module' => 'dealermanagement', 'controller' => 'index', 'action' => 'index']));

/**
 * Dealer Branding Route
 */

$r->addRoute('company.branding', new R('company/branding', ['module' => 'dealermanagement', 'controller' => 'branding', 'action' => 'index']));

/**
 * Dealer Clients Routes
 */

$r->addRoute('company.clients',               new R('company/clients',                ['module' => 'dealermanagement', 'controller' => 'client',           'action' => 'index'                ]));
$r->addRoute('company.clients.create',        new R('company/clients/create',         ['module' => 'dealermanagement', 'controller' => 'client',           'action' => 'create'               ]));
$r->addRoute('company.clients.edit',          new R('company/clients/edit/:id',       ['module' => 'dealermanagement', 'controller' => 'client',           'action' => 'edit',   'id' => false]));
$r->addRoute('company.clients.view',          new R('company/clients/view/:id',       ['module' => 'dealermanagement', 'controller' => 'client',           'action' => 'view',   'id' => false]));
$r->addRoute('company.clients.delete',        new R('company/clients/delete/:id',     ['module' => 'dealermanagement', 'controller' => 'client',           'action' => 'delete', 'id' => false]));
$r->addRoute('client.pricing',                new R('company/client-pricing',         ['module' => 'proposalgen',      'controller' => 'client-pricing',   'action' => 'index'                ]));
$r->addRoute('client.pricing.pricing-upload', new R('company/pricing/pricing-upload', ['module' => 'proposalgen',      'controller' => 'client-pricing',   'action' => 'upload'               ]));

// TODO kmccully: no controller in proposalgen called CustomerPricing yet
$r->addRoute('company.customer-pricing',      new R('company/customer-pricing',       ['module' => 'proposalgen',      'controller' => 'customer-pricing', 'action' => 'index'                ]));

/**
 * Dealer Leasing Schema Routes
 */

$r->addRoute('company.leasing-schema',                     new R('company/leasing-schema',                                  ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'index'                                 ]));
$r->addRoute('company.leasing-schema.view-provider',       new R('company/leasing-schema/view-provider',                    ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'index'                                 ]));

// TODO kmccully: "leasingSchemaId not specified" error
$r->addRoute('company.leasing-schema.view',                new R('company/leasing-schema/view/:leasingSchemaId',            ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'view',     'leasingSchemaId'=> false   ]));
$r->addRoute('company.leasing-schema.create',              new R('company/leasing-schema/create',                           ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'create'                                ]));
$r->addRoute('company.leasing-schema.edit',                new R('company/leasing-schema/edit/:leasingSchemaId',            ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'edit'                                  ]));
$r->addRoute('company.leasing-schema.delete',              new R('company/leasing-schema/delete/:leasingSchemaId',          ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'delete',   'leasingSchemaId'=> false   ]));
$r->addRoute('company.leasing-schema.add-term',            new R('company/leasing-schema/add-term/:leasingSchemaId',        ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'addterm',  'leasingSchemaId'=> false   ]));
$r->addRoute('company.leasing-schema.edit-term',           new R('company/leasing-schema/edit-term/:id/:leasingSchemaId',   ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'editterm', 'id' => false               ]));
$r->addRoute('company.leasing-schema.delete-term',         new R('company/leasing-schema/delete-term/:id/:leasingSchemaId', ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'deleteterm'                            ]));
$r->addRoute('company.leasing-schema.add-range',           new R('company/leasing-schema/add-range/:leasingSchemaId',       ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'addrange'                              ]));
$r->addRoute('company.leasing-schema.edit-range',          new R('company/leasing-schema/edit-range/:id/:leasingSchemaId',  ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'editrange'                             ]));
$r->addRoute('company.leasing-schema.delete-range',        new R('company/leasing-schema/delete-range/:id/:leasingSchemaId',['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'deleterange', 'leasingSchemaId'=> false]));
$r->addRoute('company.leasing-schema.clear-provider',      new R('company/leasing-schema/clear-provider/:leasingSchemaId',  ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'resetschema'                           ]));
$r->addRoute('company.leasing-schema.import-leasing-rate', new R('company/leasing-schema/import/:leasingSchemaId',          ['module' => 'dealermanagement', 'controller' => 'leasingschema', 'action' => 'import',      'leasingSchemaId'=> false]));

/**
 * Dealer User-Management Routes
 */

$r->addRoute('company.users',        new R('company/users',            ['module' => 'dealermanagement', 'controller' => 'user', 'action' => 'index']));

//TODO kmccully: no viewAction in user controller (is in navigation.xml)
$r->addRoute('company.users.view',   new R('company/users/view',       ['module' => 'dealermanagement', 'controller' => 'user', 'action' => 'view']));
$r->addRoute('company.users.create', new R('company/users/create',     ['module' => 'dealermanagement', 'controller' => 'user', 'action' => 'create']));
$r->addRoute('company.users.edit',   new R('company/users/edit/:id',   ['module' => 'dealermanagement', 'controller' => 'user', 'action' => 'edit']));
$r->addRoute('company.users.delete', new R('company/users/delete/:id', ['module' => 'dealermanagement', 'controller' => 'user', 'action' => 'delete']));

/**
 * Webhooks
 */

$r->addRoute('webhook.shopify.order', new R('webhooks/shopify/order/:dealerId', ['module' => 'default', 'controller' => 'webhook', 'action' => 'shopify-order']));



// ***** Information Routes ************************************************************************************************************************************** //

/**
 * Application Log Route
 */

$r->addRoute('app.application-log', new R('application-log', ['module' => 'admin', 'controller' => 'log', 'action' => 'index']));

/**
 * About Route
 */

$r->addRoute('app.about', new R('about', ['module' => 'default', 'controller' => 'info', 'action' => 'about']));

/**
 * EULA Route
 */

$r->addRoute('app.eula', new R('eula', ['module' => 'default', 'controller' => 'info', 'action' => 'eula']));
