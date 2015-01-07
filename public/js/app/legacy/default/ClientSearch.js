require([
    'jquery',
    'moment',
    'bootstrap.typeahead',
    'datatables',
    'datatables.bootstrap',
    'datatables.responsive',
    'datatables.tabletools'
], function ($, moment)
{
    $(function ()
    {
        $.fn.dataTable.TableTools.buttons.create_new = $.extend(
            true,
            $.fn.dataTable.TableTools.buttonBase,
            {
                "sNewLine"   : "<br>",
                "sButtonText": "Create new",
                "action"     : function ()
                {
                    console.log('You must set an action for this button');
                },
                "fnClick"    : function (button, conf)
                {
                    conf.action();
                }
            }
        );

        var actionRenderer = function (data, type, full, meta)
        {
            return '<button class="btn btn-primary btn-xs btn-block" name="selectClient" value="' + data.id + '">Select</button>';
        };

        var timeAgoRenderer = function (data, type, full, meta)
        {
            if (data)
            {
                // Assumes MYSQL time being returned
                var time = moment(data, "YYYY-MM-DD hh:mm:ss");
                return '<i class="fa fa-fw fa-clock-o js-tooltip btn-link" title="' + time.format('LLL') + '"></i> ' + time.fromNow();
            }
            return '';
        };

        $('.js-select-client-table').DataTable({
            "processing": true,
            "serverSide": true,
            "paging"    : true,
            "autoWidth" : false,
            "searching" : true,
            "ordering"  : true,
            //"dom"       : '<"row"<"col-md-4"i><"col-md-4"T><"col-md-4"f>>rt<"bottom"lp><"clear">',
            "dom"       : '<"row"<"col-sm-4"i><"col-sm-4"><"col-sm-4"f>>rt<"bottom"lp><"clear">',
            "tableTools": {
                "aButtons": [
                    {
                        "sExtends"   : "create_new",
                        "sButtonText": "Create New",
                        "action"     : function ()
                        {
                            document.location.href = "/clients/create-new";
                        }
                    }
                ]
            },
            "ajax"      : {
                "url": "/api/v1/clients"
            },
            "columns"   : [
                {"data": "companyName"},
                {"data": "employeeCount"},
                {
                    "data"  : "dateViewed",
                    "render": timeAgoRenderer
                },
                {
                    "orderable"     : false,
                    "data"          : null,
                    "defaultContent": '',
                    "render"        : actionRenderer,
                    "width"         : 50
                }
            ],
            "order"     : [[2, 'desc']]
        });
    });
});