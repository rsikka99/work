//The build will inline common dependencies into this file.

//For any third party dependencies, like jQuery, place them in the lib folder.

//Configure loading modules from the lib directory,
//except for 'app' ones, which are in a sibling
//directory.
requirejs.config({
    'baseUrl': '/js',
    'paths'  : {
        'accounting'             : 'lib/accounting/accounting.min',
        'backbone'               : 'lib/backbone/backbone-min',
        'bluebird'               : 'lib/bluebird/bluebird',
        'bootstrap'              : 'lib/bootstrap/bootstrap.min',
        'bootstrap.modal'        : 'lib/bootstrap/bootstrap-modal',
        'bootstrap.modal.manager': 'lib/bootstrap/bootstrap-modalmanager',
        'bootstrap.switch'       : 'lib/bootstrap/bootstrap-switch.min',
        'bootstrap.typeahead'    : 'lib/bootstrap/bootstrap-typeahead',
        'datatables'             : 'lib/jquery/dataTables/jquery.dataTables',
        'datatables.bootstrap'   : 'lib/jquery/dataTables/jquery.dataTables.bootstrap',
        'datatables.responsive'  : 'lib/jquery/dataTables/jquery.dataTables.responsive',
        'datatables.tabletools'  : 'lib/jquery/dataTables/jquery.dataTables.tableTools.min',
        'jqgrid'                 : 'lib/jqGrid/jquery.jqGrid.min',
        'jqgridlocale'           : 'lib/jqGrid/i18n/grid.locale-en',
        'jquery'                 : 'lib/jquery/jquery-1.11.1.min',
        'jquery.typewatch'       : 'lib/jquery/jquery-typewatch',
        'jquery.ui'              : 'lib/jquery-ui/jquery-ui-1.10.3.custom.min',
        'jquery.ui.autocomplete' : 'lib/jquery-ui/jquery-ui-autocomplete',
        'jquery.ui.multiselect'  : 'lib/jquery-ui/jquery-ui-multiselect',
        'jquery.ui.datepicker'   : 'lib/jquery-ui/jquery-ui-datepicker',
        'moment'                 : 'lib/moment/moment.min',
        'moment.timezone'        : 'lib/moment/moment-timezone.min',
        'numeral'                : 'lib/numeral/numeral.min',
        'riot'                   : 'lib/riot/riot.min',
        'select2'                : 'lib/select2/select2.min',
        'underscore'             : 'lib/underscore/underscore.min',
        'uri'                    : 'lib/jqGrid/jquery.jqGrid.min'
    },
    'shim'   : {
        'backbone'               : {
            'deps'   : ['jquery', 'underscore'],
            'exports': 'Backbone'
        },
        'bootstrap'              : ['jquery', 'jquery.ui'],
        'bootstrap.modal'        : ['bootstrap'],
        'bootstrap.modal.manager': ['bootstrap', 'bootstrap.modal'],
        'bootstrap.switch'       : ['bootstrap'],
        'bootstrap.typeahead'    : ['bootstrap'],
        'underscore'             : {
            'exports': '_'
        },
        'jqgrid'                 : ['jquery.ui', 'jqgridlocale'],
        'jqgridlocale'           : ['jquery.ui'],
        'datatables'             : ['jquery'],
        'datatables.bootstrap'   : ['datatables', 'bootstrap'],
        'jquery.typewatch'       : ['jquery'],
        'jquery'                 : {
            'exports': 'jQuery'
        },
        'jquery.ui'              : {
            'deps': ['jquery'],
            'init': function ($)
            {
                // handle jQuery plugin naming conflict between jQuery UI and Bootstrap
                $.widget.bridge('uibutton', $.ui.button);
                $.widget.bridge('uitooltip', $.ui.tooltip);

                return this;
            }
        },
        'select2'                : ['jquery']
    }
});

require(['./main']);