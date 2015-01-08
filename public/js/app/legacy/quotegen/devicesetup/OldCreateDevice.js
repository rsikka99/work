require(['jquery', 'jqgrid'], function ($)
{
    $(document).ready(function ()
    {
        var toner_array = $("#toner_array").val();
        var master_device_id = $("#printer_model").val();

        /**
         * If we don't have a master device id then we are creating a new device
         * @type {boolean}
         */
        var createMode = (!master_device_id > 0);
        var assignedTonersUrl = TMTW_BASEURL + '/proposalgen/admin/devicetoners?list=' + toner_array.replace(/'/gi, '');
        if (createMode)
        {
            assignedTonersUrl += '&deviceid=' + 0;
        }
        else
        {
            assignedTonersUrl += '&deviceid=' + master_device_id;
        }

        jQuery("#availableToners").jqGrid({
            url         : TMTW_BASEURL + 'proposalgen/admin/tonerslist?deviceid=' + master_device_id,
            datatype    : 'json',
//        colNames    : ['Toner ID', 'SKU', 'Manufacturer', 'Type', 'Color', 'Yield', 'Price', 'MasterID', 'Added', 'Machine Compatibility', 'Action', 'Apply To Printer', 'Machine Compatibility'],
            colModel    : [
                //@formatter:off
                { width: 30, name: 'toner_id', index: 'toner_id', label: 'Toner Id', sorttype: 'int', hidden: true, editable: true, editoptions: {readonly: true, size: 12}},
                { width: 60, name: 'toner_SKU', index: 'toner_SKU', label: 'SKU', editable: true, editoptions: {size: 12, maxlength: 30}},
                { width: 120, name: 'manufacturer_name', index: 'manufacturerId', label: 'Manufacturer', editable: true, editoptions: {size: 20, maxlength: 30}},
                { width: 100, name: 'toner_color_name', index: 'toner_color_name', label: 'Color', editable: true, editoptions: {size: 12, maxlength: 30}},
                { width: 60, name: 'toner_yield', index: 'yield', label: 'Yield', editable: true, editoptions: {size: 10, maxlength: 4}, align: 'right'},
                { width: 60, name: 'toner_price', index: 'toner_price', label: 'Price', editable: true, editoptions: {size: 10, maxlength: 4}, align: 'right'},
                { width: 80, name: 'master_device_id', index: 'master_device_id', label: 'MasterID', hidden: true, editable: true, editoptions: {size: 12}},
                { width: 50, name: 'is_added', index: 'is_added', label: 'Added', hidden: true, editable: true, editoptions: {size: 12}},
                { width: 225, name: 'device_list', index: 'device_list', label: 'Machine Compatibility'},
                { width: 60, name: 'action', index: 'action', editable: false, align: 'center'},
                { width: 50, name: 'machine_compatibility', index: 'machine_compatibility', hidden: true},
                { width: 60, name: 'apply', index: 'apply', label: "Apply To Printer", hidden: true, edittype: 'checkbox', editable: true, align: 'center'}
                //@formatter:on
            ],
            width       : 940,
            height      : 500,
            "rowList"   : [10, 15, 25, 50, 100],
            rowNum      : 15,
            jsonReader  : {repeatitems: false},
            pager       : '#availableTonersPager',
            gridComplete: function ()
            {
                var grid = $(this).jqGrid();
                var ids = grid.getDataIDs();
                var toner_array = $("#toner_array").val();

                for (var i = 0; i < ids.length; i++)
                {
                    var currentRowId = ids[i];
                    var currentRow = grid.getRowData(currentRowId);
                    var isAdded = currentRow.is_added;


                    /**
                     * Add a button depending if the toner is added
                     */
                    if (toner_array.indexOf("'" + currentRowId + "'") != -1)
                    {
                        // Disabled Assign Button
                        currentRow.action = '<input type="button" name="btnAdd' + currentRowId + '" id="btnAdd' + currentRowId + '" tag="Add" value="Assign" class="btn" onclick="javascript: do_add(' + currentRow.toner_id + ');" disabled="disabled" />';
                    }
                    else
                    {
                        // Assign Button
                        currentRow.action = '<input type="button" name="btnAdd' + currentRowId + '" id="btnAdd' + currentRowId + '" tag="Add" value="Assign" class="btn" onclick="javascript: do_add(' + currentRowId + ');" />';
                    }

                    /**
                     * Not sure what this max variable is actually used for.
                     * @type {number}
                     */
                    var max = 1;

                    /**
                     * This is the final container that everything will be in
                     * @type {string}
                     */
                    var deviceListCollapsibleContainer = '<div id="outer_' + ids[i] + '" style="text-align: left; width: 200px;">';
                    var compatibleDevices = currentRow.device_list.split("; ");

                    // Loop through each device and add it to the container
                    for (var j = 0; j < compatibleDevices.length; j++)
                    {
                        device = compatibleDevices[j];
                        if (j == max)
                        {
                            deviceListCollapsibleContainer += '<div id="inner_' + ids[i] + '" style="display: none;">';
                        }
                        deviceListCollapsibleContainer += device + '<br />';
                        if (j > max && j == compatibleDevices.length - 1)
                        {
                            deviceListCollapsibleContainer += '</div>';
                            deviceListCollapsibleContainer += '<a id="view_link_' + ids[i] + '" href="javascript: void(0);" class="blue_link" onclick="javascript: view_device_list(\'\',' + ids[i] + ');">View All...</a>';
                        }
                    }
                    currentRow.device_list = deviceListCollapsibleContainer;

                    grid.setRowData(currentRowId, currentRow);
                }

            },
            editurl     : TMTW_BASEURL + '/proposalgen/admin/edittoner?deviceid=' + master_device_id
        });

        jQuery("#appliedToners").jqGrid({
            url         : assignedTonersUrl,
            datatype    : 'json',
            colNames    : ['Toner ID', 'SKU', 'Manufacturer', 'Type', 'Color', 'Yield', 'Price', 'MasterID', 'Added', 'Action'],
            colModel    : [
                //@formatter:off
                { width: 30, name: 'toner_id', index: 'toner_id', sorttype: 'int', hidden: true, editable: true, editoptions: {readonly: true, size: 12}, sortable: false},
                { width: 60, name: 'toner_sku', index: 'toner_sku', editable: true, editoptions: {size: 12, maxlength: 30}, sortable: false},
                { width: 120, name: 'manufacturer_id', index: 'toner_manufacturer', sortable: false},
                { width: 100, name: 'toner_color_id', index: 'toner_color_id', sortable: false},
                { width: 60, name: 'toner_yield', index: 'toner_yield', align: 'right', sortable: false},
                { width: 80, name: 'toner_price', index: 'toner_price', align: 'right', formatter: 'currency', formatoptions: {decimalPlaces: 2, defaultValue: '-'}, sortable: false},
                { width: 50, name: 'master_device_id', index: 'master_device_id', hidden: true, sortable: false},
                { width: 50, name: 'is_added', index: 'is_added', hidden: true, sortable: false},
                { width: 60, name: 'action', index: 'action', align: 'center', sortable: false}
                //@formatter:on
            ],
            width       : 940,
            height      : 'auto',
            gridComplete: function ()
            {
                var tonerArrayElement = $("#toner_array");
                var toner_array = tonerArrayElement.val();

                var populateTonerArray = (toner_array == '');

                var grid = $(this).jqGrid();
                var ids = grid.getDataIDs();

                for (var i = 0; i < ids.length; i++)
                {
                    var currentRowId = ids[i];
                    var currentRow = grid.getRowData(currentRowId);

                    currentRow.action = '<input type="button" name="btnRemove' + currentRowId + '" id="btnRemove' + currentRowId + '" tag="Remove" value="Remove" class="btn" onclick="javascript: do_remove(' + currentRowId + ');" />';

                    if (populateTonerArray)
                    {
                        if (toner_array != '')
                        {
                            toner_array = toner_array + ",";
                        }
                        toner_array = toner_array + "'" + currentRow.toner_id + "'";
                    }

                    grid.setRowData(currentRowId, currentRow);
                }
                tonerArrayElement.val(toner_array);
            }
        });

    });

    /**
     * Adds a toner to our list
     * @param tonerId
     */
    function do_add(tonerId)
    {
        $('#message_container').html('');


        var tonerArrayElement = $("#toner_array");
        var toner_array = tonerArrayElement.val();

        /**
         * We only need to add it if it doesn't already exist.
         */
        if (toner_array.indexOf("'" + tonerId + "'") == -1)
        {
            if (toner_array != '')
            {
                toner_array += ",";
            }
            toner_array = toner_array + "'" + tonerId + "'";
            tonerArrayElement.val(toner_array);

            update_applied();
            $("#btnAdd" + tonerId).attr('disabled', 'disabled');
        }
        else
        {
            show_message("Toner already exists.");
        }
    }

    /**
     * Handles updates to the assigned toners. Refreshes the jqGrid.
     */
    function update_applied()
    {
        var toner_array = $("#toner_array").val();

        var master_device_id = $("#printer_model").val();


        var appliedToners = $('#appliedToners');

        if (master_device_id > 0)
        {
            appliedToners.setGridParam({url: TMTW_BASEURL + '/proposalgen/admin/devicetoners?deviceid=' + master_device_id + '&list=' + toner_array.replace(/'/gi, '')});

        }
        else
        {
            appliedToners.setGridParam({url: TMTW_BASEURL + '/proposalgen/admin/devicetoners?list=' + toner_array.replace(/'/gi, '')});
        }

        appliedToners.trigger('reloadGrid');
    }

    /**
     * Removes a toner from the assigned list.
     * @param tonerId
     */
    function do_remove(tonerId)
    {
        $('#message_container').html('');

        if (confirm("Are you sure you want to remove this toner?"))
        {
            //update array
            var tonerArrayElement = $("#toner_array");
            var toner_array = tonerArrayElement.val();
            toner_array = toner_array.replace("'" + tonerId + "',", "").replace(",'" + tonerId + "'", "").replace("'" + tonerId + "'", "");

            tonerArrayElement.val(toner_array);

            update_applied();
            $("#btnAdd" + tonerId).removeAttr('disabled');
        }
    }

    /**
     * Handles toggling the collapsed "Machine Compatibility" Column
     * @param type
     * @param id
     */
    function view_device_list(type, id)
    {
        if (document.getElementById(type + 'inner_' + id).style.display == 'none')
        {
            document.getElementById(type + 'inner_' + id).style.display = 'block';
            document.getElementById(type + 'view_link_' + id).innerHTML = 'Collapse...';
        }
        else
        {
            document.getElementById(type + 'inner_' + id).style.display = 'none';
            document.getElementById(type + 'view_link_' + id).innerHTML = 'View All...';
        }
    }
});