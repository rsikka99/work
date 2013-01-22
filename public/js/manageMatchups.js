$(document).ready(function ()
{
    // find center screen for modal popup
    var sTop = ($(window).height() / 2) - 100;
    var sLeft = ($(window).width() / 2) - 200;

    jQuery("#grid_list").jqGrid({
        url         : url_matchuplist,
        datatype    : 'json',
        colModel    : [
            { label: 'Provider Id', width: 35, name: 'rmsProviderId', index: 'rmsProviderId', hidden: true },
            { label: 'Provider', width: 50, name: 'rmsProviderName', index: 'rmsProviderName' },
            { label: 'Model Id', width: 50, name: 'rmsModelId', index: 'rmsModelId', align: 'center' },
            { label: 'Provider Device Name', width: 250, name: 'rmsProviderDeviceName', index: 'rmsProviderDeviceName' },
            { label: 'Master Device', width: 250, name: 'masterDevice', index: 'masterDevice' },
            { label: 'masterDeviceId', width: 35, name: 'masterDeviceId', index: 'masterDeviceId', hidden: true },
            { label: 'modelName', width: 35, name: 'modelName', index: 'modelName', hidden: true },
            { label: 'displayname', width: 35, name: 'displayname', index: 'displayname', hidden: true }
        ],
        width       : 765,
        height      : 'auto',
        jsonReader  : {
            repeatitems: false
        },
        rowNum      : 10,
        rowList     : [ 10, 20, 30 ],
        pager       : '#grid_pager',
        gridComplete: function ()
        {
            var grid = jQuery("#grid_list").jqGrid();
            var ids = grid.getDataIDs();

            // Loop through the rows
            for (var i = 0; i < ids.length; i++)
            {
                var rowData = grid.getRowData(ids[i]);

                var rmsKey = rowData.rmsProviderId + "_" + rowData.rmsModelId;

                // build hidden field for master_device_id
                var devices_pf_id = document.getElementById("grid_list").rows[i + 1].cells[0].innerHTML;
                var master_device_id = document.getElementById("grid_list").rows[i + 1].cells[1].innerHTML;

                hidden_devices_pf_id = "<input type='hidden' name='hdnDevicesPFID" + ids[i] + "' id='hdnDevicesPFID" + ids[i] + "' value='" + devices_pf_id + "' />";
                hidden_master_device_id = "<input type='hidden' name='hdnMasterDeviceID" + ids[i] + "' id='hdnMasterDeviceID" + ids[i] + "' value='" + master_device_id + "' />";

                var mapped_to = rowData.modelName;
                var mapped_to_id = rowData.master_device_id;
                var mapped_to_manufacturer = rowData.displayname;
                var mapped_to_deviceName = mapped_to_manufacturer + " " + mapped_to;

                rowData.masterDevice = '';
                rowData.masterDevice += '<input type="hidden" class="masterDeviceId" value="' + mapped_to_id + '" />';
                rowData.masterDevice += '<input type="hidden" class="masterDeviceName" value="' + mapped_to_deviceName + '" />';
                rowData.masterDevice += '<input type="hidden" class="manufacturerName" value="' + mapped_to_manufacturer + '" />';
                rowData.masterDevice += '<input type="text" name="txtMasterDevices' + rmsKey + '" id="txtMasterDevices' + rmsKey + '" size="55" class="autoCompleteDeviceName" value="' + mapped_to_deviceName + '" />';

                var result = grid.setRowData(ids[i], rowData);
            }

            $(".autoCompleteDeviceName").autocomplete({
                source   : function (request, response)
                {
                    $.ajax({
                        url     : TMTW_BASEURL + 'proposalgen/admin/getmodels',
                        dataType: "json",
                        data    : {
                            searchText: request.term
                        },
                        success : function (data)
                        {
                            response($.map(data, function (item)
                            {
                                return {
                                    value       : item.label,
                                    id          : item.value,
                                    label       : item.label,
                                    manufacturer: item.manufacturer
                                }
                            }))
                        }
                    })
                },
                minLength: 0,
                select   : function (event, ui)
                {
                    // TODO: Perform Mapping
                    // doMapping(rmsProviderId, rmsModelId, masterDeviceId);
                    $(this).parent().find("input.masterDeviceId")[0].value = ui.item.id;
                    $(this).parent().find("input.masterDeviceName")[0].value = ui.item.label;
                    $(this).parent().find("input.manufacturerName")[0].value = ui.item.manufacturer;

                },
                open     : function (event, ui)
                {
                    // Perform autocomplete, and bold the match critera to help the end user see what was matched.
                    var termTemplate = '<strong>%s</strong>';
                    var autocompleteData = $(this).data('autocomplete');
                    autocompleteData.menu.element.find('a').each(function ()
                    {
                        var label = $(this);
                        var regex = new RegExp(autocompleteData.term, "gi");
                        label.html(label.text().replace(regex, function (matched)
                        {
                            return termTemplate.replace('%s', matched);
                        }));
                    });
                },
                change   : function (event, ui)
                {
                    var parent = $(this).parent();
                    var textValue = $.trim(this.value);
                    // We stored our ids in our textbox name as txtMasterDevices{ProviderId}_{ModelId}
                    var rmsUploadRowId = this.id.replace("txtMasterDevices", "");
                    var identifiers = rmsUploadRowId.split('_');
                    var rmsProviderId = identifiers[0];
                    var rmsModelId = identifiers[1];
                    var masterDeviceId = $.trim(parent.find("input.masterDeviceId")[0].value);
                    var deviceName = $.trim(parent.find("input.masterDeviceName")[0].value);

                    /*
                     * Populate the text field if the user was auto completing, or clear it out if they were deleting the text
                     */
                    if (textValue)
                    {
                        // If the device id is not set, then we reset to blank
                        if (!masterDeviceId)
                        {
                            textValue = "";

                        }
                        else
                        {
                            // Set the name to the device name
                            textValue = deviceName;

                        }
                        this.value = textValue;
                        set_mapped(rmsProviderId, rmsModelId, masterDeviceId);
                    }
                    else
                    {
                        parent.find("input.masterDeviceId")[0].value = "";
                        parent.find("input.masterDeviceName")[0].value = "";
                        parent.find("input.manufacturerName")[0].value = "";
                        this.value = textValue;
                        set_mapped(rmsProviderId, rmsModelId, 0);
                    }
                }
            });


            $("#hdnIdArray").val(ids);
        },
        editurl     : 'dummy.php'
    });

    jQuery("#grid_list").jqGrid('navGrid', '#grid_pager', {
        add    : false,
        del    : false,
        edit   : false,
        refresh: false,
        search : false
    }, {
        closeAfterEdit: true,
        recreateForm  : true,
        closeOnEscape : true,
        width         : 400,
        top           : sTop,
        left          : sLeft
    }, {
        closeAfterAdd: true,
        recreateForm : true,
        closeOnEscape: true,
        width        : 400,
        top          : sTop,
        left         : sLeft
    }, {}, {}, {});

});

/**
 * Sets a device mapping to a new master device
 *
 * @param rmsUploadRowId
 *            The upload row id
 * @param masterDeviceId
 *            The master device id. Can be 0 or false
 */
function set_mapped(rmsProviderId, rmsModelId, masterDeviceId)
{
    $.ajax({
        type       : "GET",
        contentType: "application/json; charset=utf-8",
        url        : TMTW_BASEURL + 'proposalgen/admin/setmappedto',
        data       : {
            "rmsProviderId" : rmsProviderId,
            "rmsModelId"    : rmsModelId,
            "masterDeviceId": masterDeviceId
        },
        error      : function ()
        {
            $('#message_container').html("Error setting mapped device!");
        }
    });
}

/**
 * Updates the grid to be filtered by a certain criteria
 *
 * @param action
 */
function update_grid(action)
{
    var params = '?filter=&criteria=';
    if (action == 'search')
    {
        params = '?filter=' + $("#criteria_filter").val() + '&criteria=' + $("#txtCriteria").val();
    }
    else if (action == 'clear')
    {
        $("#criteria_filter").attr('selectedIndex', 0);
        $("#txtCriteria").val('');
    }

    $('#grid_list').setGridParam({
        url: TMTW_BASEURL + 'proposalgen/admin/matchuplist' + params
    });
    $('#grid_list').trigger("reloadGrid");
}

/**
 * Performs an action
 *
 * @param inAction
 */
function do_action(inAction)
{
    if (inAction == 'save')
    {
        $("#matchups").submit();

    }
    else if (inAction == 'done')
    {
        if ($("#ticket_id").val() > 0)
        {
            document.location.href = TMTW_BASEURL + 'proposalgen/ticket/ticketdetails?id=' + $("#ticket_id").val();
        }
        else
        {
            document.location.href = TMTW_BASEURL + 'proposalgen/admin';
        }
    }
}