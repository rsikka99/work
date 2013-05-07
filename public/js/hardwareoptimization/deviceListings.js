$(function ()
{
    var lastsel2;

    jQuery("#deviceSwapsTable").jqGrid({
        url         : TMTW_BASEURL + 'hardwareoptimization/deviceswaps/device-swap-list',
        datatype    : 'json',
        width       : '100%',
        height      : '100%',
        rowNum      : 30,
        rowList     : [10, 20, 30],
        colModel    : [
            {
                name    : 'id',
                label   : 'id',
                index   : 'id',
                sortable: false,
                hidden  : true
            },
            {
                name : 'device_name',
                index: 'device_name',
                label: 'Device Name',
                width: 340
            },
            {
                name     : 'monochromeCpp',
                index    : 'monochromeCpp',
                label    : 'Monochrome CPP*',
                width    : 120,
                align    : "right",
                sortable : false,
                formatter: "number"
            },
            {
                name    : 'colorCpp',
                index   : 'colorCpp',
                label   : 'Color CPP*',
                width   : 120,
                align   : "right",
                sortable: false
            },
            {
                name    : 'minimumPageCount',
                index   : 'minimumPageCount',
                label   : 'Min Page Count',
                width   : 150,
                align   : "right",
                sortable: true,
                editable: true
            },
            {
                name    : 'maximumPageCount',
                index   : 'maximumPageCount',
                label   : 'Max Page Count',
                width   : 150,
                align   : "right",
                sortable: true,
                editable: true
            },
            {
                name  : 'deviceType',
                index : 'deviceType',
                label : 'Device Type',
                width : 140,
                align : "right",
                hidden: true
            }
        ],
        pager       : "#pager",
        viewrecords : true,
        jsonReader  : { repeatitems: false },
        grouping    : true,
        groupingView: {
            groupField     : ['deviceType'],
            groupColumnShow: [false],
            groupOrder     : ['asc']
        },
        caption     : "Device Swaps",
        onSelectRow : function (rowid)
        {
            var grid = $(this);
            var myCellData = grid.getRowData(rowid);
            $(".modal").modal("show");
            $('#masterDeviceId').select2('data', { id: myCellData.id, text: myCellData.device_name});
            $('#minimumPageCount').val(myCellData.minimumPageCount);
            $('#maximumPageCount').val(myCellData.maximumPageCount);
        }
    });

    // Setup autocomplete for our textbox
    $("#masterDeviceId").select2({
        placeholder       : "Search for a device",
        minimumInputLength: 1,
        ajax              : {
            // Instead of writing the function to execute the request we use select2's convenient helper
            url     : TMTW_BASEURL + "proposalgen/admin/search-for-device",
            dataType: 'json',
            data    : function (term, page)
            {
                return {
                    searchTerm      : term, // search term
                    onlyQuoteDevices: true,
                    page_limit      : 10
                };
            },
            results : function (data, page)
            {
                // Parse the results into the format expected by select2.
                // Since we are using custom formatting functions we do not need to alter remote JSON data
                var newData = $.map(data, function (device)
                {
                    device.text = device.device_name;
                    return device;
                })
                return {results: newData};
            }
        }
    });

    $("#trigger").click(function ()
    {
        $(".modal").modal("show");
        $('#masterDeviceId').select2('data', {text: "Begin typing to search"});
        $('#minimumPageCount').val(0);
        $('#maximumPageCount').val(0);
    });

    $("#saveTest").click(function ()
    {
        $.ajax({
            url    : TMTW_BASEURL + "hardwareoptimization/deviceswaps/update-device",
            type   : "post",
            data   : $("#deviceSwap").serialize(),
            success: function ()
            {
                $("#deviceSwapsTable").jqGrid().trigger('reloadGrid');
                $(".modal").modal('hide');
            },
            error  : function ()
            {
                console.log('test');
            }
        });
    });
});