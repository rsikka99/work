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
        viewrecords : true,
        jsonReader  : { repeatitems: false },
        grouping    : true,
        groupingView: {
            groupField     : ['deviceType'],
            groupColumnShow: [false],
            groupOrder     : ['asc']
        },
        caption     : "Device Swaps",
        gridComplete: function ()
        {
            // Get the grid object (cache in variable)
            var grid = $(this);
            var ids = grid.getDataIDs();

            for (var i = 0; i < ids.length; i++)
            {
                // Get the data so we can use and manipulate it.
                var row = grid.getRowData(ids[i]);

                // Add an edit button and a delete button for each device swap
                row.action = '<button type="button" title="Edit" class="btn btn-warning btn-mini editDeviceAction" style="margin:2px;" ><i class="icon-pencil icon-pencil"></i></button>';
                row.action += '<button type="button" title="Delete" class="btn btn-danger btn-mini deleteDeviceAction" data-device-instance-ids="' + row.id + '" ><i class="icon-trash icon-white"></i></button>';

                grid.setRowData(ids[i], row);
            }
        }
    });

    jQuery("#deviceReasonTable").jqGrid({
        url         : TMTW_BASEURL + 'hardwareoptimization/deviceswaps/device-reason-list',
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
                name  : 'reasonCategoryId',
                index : 'reasonCategoryId',
                hidden: true
            },
            {
                name  : 'rawIsDefault',
                index : 'rawIsDefault',
                hidden: true
            },
            {
                name : 'reason',
                index: 'reason',
                width: 352,
                label: 'Reason'
            },
            {
                name : 'reasonCategory',
                index: 'reasonCategory',
                width: 352,
                label: 'Reason Category'
            },
            {
                name : 'isDefault',
                index: 'isDefault',
                width: 100,
                label: 'Is Default'
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
        viewrecords : true,
        jsonReader  : { repeatitems: false },
        caption     : "Device Swap Reason",
        gridComplete: function ()
        {
            // Get the grid object (cache in variable)
            var grid = $(this);
            var ids = grid.getDataIDs();

            for (var i = 0; i < ids.length; i++)
            {
                // Get the data so we can use and manipulate it.
                var row = grid.getRowData(ids[i]);

                // Add an edit button and a delete button for each device swap
                row.action = '<button type="button" title="Edit" class="btn btn-warning btn-mini editSwapReasonAction" style="margin:2px;" ><i class="icon-pencil icon-pencil"></i></button>';
                row.action += '<button type="button" title="Delete" class="btn btn-danger btn-mini deleteSwapReasonAction" data-device-instance-ids="' + row.id + '" ><i class="icon-trash icon-white"></i></button>';

                grid.setRowData(ids[i], row);
            }
        }
    });

    // Setup auto complete for our text box
    var masterDeviceSelect = $("#masterDeviceId");
    masterDeviceSelect.select2({
        placeholder       : "Search for a device",
        minimumInputLength: 1,
        ajax              : {
            // Instead of writing the function to execute the request we use select2's convenient helper
            url     : TMTW_BASEURL + "proposalgen/admin/search-for-device",
            dataType: 'json',
            data    : function (term)
            {
                // onlyQuoteDevices will only return devices that are quote devices
                return {
                    searchTerm      : term, // search term
                    onlyQuoteDevices: true,
                    page_limit      : 10
                };
            },
            results : function (data)
            {
                // Parse the results into the format expected by select2.
                // Since we are using custom formatting functions we do not need to alter remote JSON data
                var newData = $.map(data, function (device)
                {
                    device.text = device.device_name;
                    return device;
                });

                return {results: newData};
            }
        }
    });

    // When we select a master device we want to set printer type in the text box
    masterDeviceSelect
        .on("change", function (e)
        {
            $('#deviceType').val($(this).select2('data').deviceType);
        })


    // Trigger is used for the create new button, displays the modal.
    $("#createNewBtn").click(function ()
    {
        $("#deviceAddModal").modal("show");
        $('#masterDeviceId').select2('data', {text: "Begin typing to search"});
        $('#minimumPageCount').val(0);
        $('#maximumPageCount').val(0);
    });

    // Persist the jqGrid row id to the hidden form element inside the model
    $(document).on("click", ".editDeviceAction", function ()
    {
        $("#deviceAddModal").modal("show");

        // Get the data from the selected row
        var row_data = getJqGridRow(jQuery("#deviceSwapsTable"));

        $('#masterDeviceId').select2('data', { id: row_data.id, text: row_data.device_name});
        $('#minimumPageCount').val(row_data.minimumPageCount);
        $('#maximumPageCount').val(row_data.maximumPageCount);
        $('#deviceType').val(row_data.deviceType);
    });

    // Persist the jqGrid row id to the hidden form element inside the model
    $(document).on("click", ".editSwapReasonAction", function ()
    {
        $("#reasonAddModal").modal("show");
        // Get the data from the selected row
        var row_data = getJqGridRow(jQuery("#deviceReasonTable"));
        // Set the hidden element in the modal for the id
        $("#deviceSwapReasonId").val(row_data.id);

        // Populate the data for the row selected
        $('#reason').val(row_data.reason);
        $('#reasonCategory').val(row_data.reasonCategoryId);
        $('#isDefault').prop('checked', !!((row_data.rawIsDefault === '1' )));
    });

    // Cancel the deletion process
    $(document).on("click", ".cancelBtn", function ()
    {
        $("#deleteModal").modal("hide");
        $("#deviceAddModal").modal("hide");
        $("#reasonAddModal").modal("hide");
        $("#deleteReasonModal").modal("hide");
    });

    // Persist the jqGrid row id to the hidden form element inside the model
    $(document).on("click", ".deleteDeviceAction", function ()
    {
        $("#deleteModal").modal("show");
    });

    $(document).on("click", ".deleteSwapReasonAction", function ()
    {
        $('#device-swap-error-message').hide();
        $("#deleteReasonModal").modal("show");
    });

    // Persist the jqGrid row id to the hidden form element inside the model
    $(document).on("click", "#createReasonBtn", function ()
    {
        // Clear out any fields that have been filled
        $("#deviceSwapReasonId").val('');
        $("#reasonCategory").val(1);
        $("#reason").val('');
        $("#isDefault").attr('checked', false);

        $("#reasonAddModal").modal("show");
    });

    // Save button on the modal will trigger a json response to save the data
    $(document).on("click", "#saveDevice", function ()
    {
        $.ajax({
            url    : TMTW_BASEURL + "hardwareoptimization/deviceswaps/update-device",
            type   : "post",
            data   : $("#deviceSwap").serialize(),
            success: function ()
            {
                $("#deviceSwapsTable").jqGrid().trigger('reloadGrid');
                $("#deviceAddModal").modal('hide');
            },
            error  : function (xhr)
            {
                // Show the error message
                var errorMessageElement = $("#save-error");

                try
                {
                    // a try/catch is recommended as the error handler
                    // could occur in many events and there might not be
                    // a JSON response from the server

                    var json = $.parseJSON(xhr.responseText);
                    errorMessageElement.html('<ol>');

                    for (var i = 0; i < json.error.length; i++)
                    {
                        errorMessageElement.append('<li>' + json.error[i] + '</li>');
                    }

                    errorMessageElement.append('</ol>');
                }
                catch (e)
                {
                    console.log('Something bad happened.');
                }

                errorMessageElement.show();
            }
        });
    });

    // Save button on the modal will trigger a json response to save the data
    $(document).on("click", "#saveDeviceReason", function ()
    {
        $.ajax({
            url    : TMTW_BASEURL + "hardwareoptimization/deviceswaps/update-device-reason",
            type   : "post",
            data   : $("#deviceSwapReason").serialize(),
            success: function ()
            {
                $("#deviceReasonTable").jqGrid().trigger('reloadGrid');
                $("#reasonAddModal").modal('hide');
            },
            error  : function (xhr)
            {
                // Show the error message
                var errorMessageElement = $("#save-error");

                try
                {
                    // a try/catch is recommended as the error handler
                    // could occur in many events and there might not be
                    // a JSON response from the server

                    var json = $.parseJSON(xhr.responseText);
                    errorMessageElement.html('<ol>');

                    for (var i = 0; i < json.error.length; i++)
                    {
                        errorMessageElement.append('<li>' + json.error[i] + '</li>');
                    }

                    errorMessageElement.append('</ol>');
                }
                catch (e)
                {
                    console.log('Something bad happened.');
                }

                errorMessageElement.show();
            }
        });

    });

    $(document).on("click", "#deleteDeviceBtn", function ()
    {
        // Get the jqGrid row that we have selected when we clicked the button
        var row_data = getJqGridRow(jQuery("#deviceSwapsTable"));

        $.ajax({
            url     : TMTW_BASEURL + "hardwareoptimization/deviceswaps/delete-device",
            dataType: 'json',
            data    : {
                deviceInstanceId: row_data.id
            },
            success : function ()
            {
                $("#deviceSwapsTable").jqGrid().trigger('reloadGrid');
                $("#deleteModal").modal('hide');
            }
        });
    });

    $(document).on("click", "#deleteSwapBtn", function ()
    {
        // Get the jqGrid row that we have selected when we clicked the button
        var row_data = getJqGridRow(jQuery("#deviceReasonTable"));

        $.ajax({
            url     : TMTW_BASEURL + "hardwareoptimization/deviceswaps/delete-reason",
            dataType: 'json',
            data    : {
                reasonId: row_data.id
            },
            success : function ()
            {
                $("#deviceReasonTable").jqGrid().trigger('reloadGrid');
                $("#deleteReasonModal").modal('hide');
            },
            error : function(data)
            {
                var json = $.parseJSON(data.responseText);
                var errorMessageElement = $('#device-swap-error-message');
                errorMessageElement.html('<ol>');
                errorMessageElement.append('<li>' + json.error + '</li>');
                errorMessageElement.append('</ol>');
                errorMessageElement.show();
            }
        });
    });


    // When modal hides function is triggered, clear previous messages
    $('.modal').on('hidden', function ()
    {
        var errorMessageElement = $("#save-error");
        errorMessageElement.hide();
        $('#deviceType').val("");
    })
});

/**
 * Gets row of jqGrid data for the grid provided.
 *
 * @param jqGrid jqGrid
 * @returns {*}
 */
function getJqGridRow(jqGrid)
{
    return jqGrid.getRowData(jqGrid.jqGrid('getGridParam', 'selrow'));
}