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
                width: 310,
                label: 'Device Name'
            },
            {
                name         : 'monochromeCpp',
                index        : 'monochromeCpp',
                label        : 'Monochrome CPP*',
                width        : 120,
                align        : 'right',
                sortable     : false,
                formatter    : 'currency',
                formatoptions: {decimalSeparator: ".", decimalPlaces: 4, prefix: "$ "}
            },
            {
                name         : 'colorCpp',
                index        : 'colorCpp',
                label        : 'Color CPP*',
                width        : 120,
                align        : 'right',
                formatter    : 'currency',
                formatoptions: {decimalSeparator: ".", decimalPlaces: 4, prefix: "$ "},
                sortable     : false
            },
            {
                name    : 'minimumPageCount',
                index   : 'minimumPageCount',
                label   : 'Min Page Count',
                width   : 120,
                align   : 'right',
                sortable: true,
                editable: true
            },
            {
                name    : 'maximumPageCount',
                index   : 'maximumPageCount',
                label   : 'Max Page Count',
                width   : 120,
                align   : 'right',
                sortable: true,
                editable: true
            },
            {
                name  : 'deviceType',
                index : 'deviceType',
                label : 'Device Type',
                align : 'right',
                hidden: true
            },
            {
                width   : 85,
                name    : 'action',
                index   : 'action',
                label   : 'Action',
                title   : false,
                sortable: false,
                align   : 'center'
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
        // When we click a row, we will allow the user to edit that device
//        onSelectRow : function (rowid)
//        {
//            var grid = $(this);
//            var myCellData = grid.getRowData(rowid);
//            $(".modal").modal("show");
//            $('#masterDeviceId').select2('data', { id: myCellData.id, text: myCellData.device_name});
//            $('#minimumPageCount').val(myCellData.minimumPageCount);
//            $('#maximumPageCount').val(myCellData.maximumPageCount);
//        },
        gridComplete: function ()
        {
            // Get the grid object (cache in variable)
            var grid = $(this);
            var ids = grid.getDataIDs();

            for (var i = 0; i < ids.length; i++)
            {
                // Get the data so we can use and manipulate it.
                var row = grid.getRowData(ids[i]);

                row.action = '<input style="width:75px;" title="Delete Device" class="btn btn-small btn-danger deleteDevice" type="button"  data-device-instance-ids="' + row.id + '" value="Delete" />';
                grid.setRowData(ids[i], row);
            }
        }
    });

    // Setup auto complete for our text box
    $("#masterDeviceId").select2({
        placeholder       : "Search for a device",
        minimumInputLength: 1,
        ajax              : {
            // Instead of writing the function to execute the request we use select2's convenient helper
            url     : TMTW_BASEURL + "proposalgen/admin/search-for-device",
            dataType: 'json',
            data    : function (term, page)
            {
                // onlyQuoteDevices will only return devices that are quote devices
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

    // Trigger is used for the create new button, displays the modal.
    $("#trigger").click(function ()
    {
        $("#deviceAdd").modal("show");
        $('#masterDeviceId').select2('data', {text: "Begin typing to search"});
        $('#minimumPageCount').val(0);
        $('#maximumPageCount').val(0);
    });


    /**
     * Adding/Editing of the unknown device
     */
    $(document).on("click", "#deleteDeviceBtn", function ()
    {
        $('#deviceInstanceId').val($("#deleteDeviceBtn").val());

        $.ajax({
            url    : TMTW_BASEURL + "hardwareoptimization/deviceswaps/delete-device",
            type   : "post",
            data   : $("#deleteDeviceForm").serialize(),
            success: function ()
            {
                $("#deviceSwapsTable").jqGrid().trigger('reloadGrid');
                $("#deleteModal").modal('hide');
            },
            error  : function (data)
            {
//                $("#login-error").show();
            }
        });
    });

    /**
     * Adding/Editing of the unknown device
     */
    $(document).on("click", "#cancelDeviceBtn", function ()
    {
        $("#deleteModal").modal("hide");
    });

    $(document).on("click", ".deleteDevice", function ()
    {
        $("#deleteDeviceBtn").val($(this).data("device-instance-ids"));
        $("#deleteModal").modal("show");
    });

    // Save button on the modal will trigger a json response to save the data
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
            error  : function (data)
            {
                $("#login-error").show();
            }
        });
    });

    // When modal hides function is triggered, clear preivious messages
    $('.modal').on('hidden', function ()
    {
        $("#login-error").hide();
    })
});