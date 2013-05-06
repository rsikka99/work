$(function ()
{
    jQuery("#deviceSwapsTable").jqGrid({
        url         : TMTW_BASEURL + 'hardwareoptimization/deviceswaps/device-swap-list',
        datatype    : 'json',
        height      : 'auto',
        width       : "100%",
        rowNum      : 30,
        rowList     : [10, 20, 30],
        colModel    : [
            {
                name    : 'id',
                label   : 'id',
                index   : 'id',
                width   : 60,
                sortable: false,
                hidden  : true
            },
            {
                name : 'fullname',
                index: 'fullname',
                label: 'Manufacturer',
                width: 90
            },
            {
                name    : 'device_name',
                index   : 'device_name',
                label   : 'Device Name',
                width   : 100,
                editable: true
            },
            {
                name     : 'monochromeCpp',
                index    : 'monochromeCpp',
                label    : 'Monochrome CPP',
                width    : 80,
                align    : "right",
                sortable : false,
                formatter: "number",
                editable : true
            },
            {
                name    : 'colorCpp',
                index   : 'colorCpp',
                label   : 'Color CPP',
                width   : 80,
                align   : "right",
                sortable: false,
                editable: true
            },
            {
                name    : 'minimumPageCount',
                index   : 'minimumPageCount',
                label   : 'Min Page Count',
                width   : 80,
                align   : "right",
                sortable: false
            },
            {
                name    : 'maximumPageCount',
                index   : 'maximumPageCount',
                label   : 'Max Page Count',
                width   : 390,
                align   : "right",
                sortable: false
            },
            {
                name  : 'deviceType',
                index : 'deviceType',
                label : 'Device Type',
                width : 346,
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
            groupOrder     : [' ']
        },
        caption     : "Device Swaps"
    });

    // Setup autocomplete for our textbox
    $("#masterDeviceId").select2({
        placeholder       : "Search for a device",
        minimumInputLength: 1,
        ajax              : { // instead of writing the function to execute the request we use select2's convenient helper
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
            { // parse the results into the format expected by select2.
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
    });
});