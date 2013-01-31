/**
 * show_mapped holds the visibility state of the bottom jqgrid
 */
var show_mapped = false;

$(function ()
{

    /***********************************************************************************************************************************************************
     * UNMAPPED GRID
     **********************************************************************************************************************************************************/
    jQuery("#mappingGrid").jqGrid(
        {
            url         : TMTW_BASEURL + 'proposalgen/fleet/devicemappinglist',
            datatype    : 'json',
            colModel    : [
                {
                    width   : 10,
                    name    : 'deviceInstanceIds',
                    index   : 'deviceInstanceIds',
                    hidden  : true,
                    label   : 'Device Instance Ids',
                    title   : false,
                    sortable: false
                },
                {
                    width   : 50,
                    name    : 'isMapped',
                    index   : 'isMapped',
                    label   : 'Is Mapped',
                    hidden  : true,
                    title   : false,
                    sortable: false,
                    align   : 'center'
                },
                {
                    width   : 10,
                    name    : 'rmsModelId',
                    index   : 'rmsModelId',
                    hidden  : true,
                    label   : 'RMS Model Number',
                    title   : false,
                    sortable: false
                },
                {
                    width   : 10,
                    name    : 'rmsProviderId',
                    index   : 'rmsProviderId',
                    hidden  : true,
                    label   : 'RMS Provider',
                    title   : false,
                    sortable: false
                },
                {
                    width   : 40,
                    name    : 'deviceCount',
                    index   : 'deviceCount',
                    label   : 'Count',
                    title   : false,
                    sortable: true,
                    align   : 'center',
                    sorttype: 'int'
                },
                {
                    width   : 150,
                    name    : 'manufacturer',
                    index   : 'manufacturer',
                    label   : 'Device Manufacturer',
                    title   : false,
                    sortable: true
                },
                {
                    width   : 150,
                    name    : 'modelName',
                    index   : 'modleName',
                    label   : 'Device Name',
                    title   : false,
                    sortable: true
                },
                {
                    width   : 50,
                    name    : 'masterDeviceId',
                    index   : 'masterDeviceId',
                    label   : 'Mapped To ID',
                    hidden  : true,
                    sortable: true
                },
                {
                    width   : 10,
                    name    : 'mappedModelName',
                    index   : 'mappedModelName',
                    label   : 'Mapped Model Name',
                    hidden  : true,
                    sortable: false
                },
                {
                    width   : 10,
                    name    : 'mappedManufacturer',
                    index   : 'mappedManufacturer',
                    label   : 'Mapped Manufacturer',
                    hidden  : true,
                    sortable: false
                },
                {
                    width   : 250,
                    name    : 'mapToMasterDevice',
                    index   : 'mapToMasterDevice',
                    label   : 'Select Master Device',
                    sortable: false,
                    align   : 'center'
                },
                {
                    width   : 10,
                    name    : 'useUserData',
                    index   : 'useUserData',
                    label   : 'Using User Data',
                    hidden  : true,
                    title   : false,
                    sortable: false
                },
                {
                    width   : 80,
                    name    : 'action',
                    index   : 'action',
                    label   : 'Action',
                    title   : false,
                    sortable: false,
                    align   : 'center'
                }
            ],
            jsonReader  : {
                repeatitems: false
            },
            width       : 900,
            height      : 'auto',
            rowNum      : 15,
            rowList     : [ 15, 30, 50, 100 ],
            pager       : '#mappingGridPager',
            gridComplete: function ()
            {

                // Get the grid object (cache in variable)
                var grid = $(this);
                var ids = grid.getDataIDs();

                for (var i = 0; i < ids.length; i++)
                {
                    // Get the data so we can use and manipualte it.
                    var row = grid.getRowData(ids[i]);

                    // This is what toggles the 'master printer
                    // name' field between the auto complete text
                    // box and the 'Click to Remove' text
                    if (row.useUserData == 1)
                    {
                        // Display message instead of dropdown
                        row.mapToMasterDevice = '&nbsp;New Printer Added (<a href="javascript: void(0);" class="removeUnknownDeviceButton" data-device-instance-ids="' + row.deviceInstanceIds + '">Click to Remove</a>)';
                        row.action = '<input style="width:35px;" title="Edit Printer" class="addEditUnknownDeviceButton" type="button" data-device-instance-ids="' + row.deviceInstanceIds + '" value="Edit" />';

                    }
                    else
                    {
                        var master_device_dropdown = '';
                        master_device_dropdown += '<input type="hidden" name="deviceInstanceIds" class="deviceInstanceIds" value="' + row.deviceInstanceIds + '" />';
                        master_device_dropdown += '<input type="hidden" name="masterDeviceId" class="masterDeviceId" value="' + row.masterDeviceId + '" />';
                        master_device_dropdown += '<input type="hidden" name="modelName" class="masterDeviceName" value="' + row.mappedModelName + '" />';
                        master_device_dropdown += '<input type="hidden" name="manufacturer" class="manufacturerName" value="' + row.mappedManufacturer + '" />';
                        master_device_dropdown += '<input type="text"   name="masterDeviceName" style="width: 97%" class="autoCompleteDeviceName" value="' + row.mappedManufacturer + ' ' + row.mappedModelName + '" />';

                        row.mapToMasterDevice = master_device_dropdown;
                        if (row.isMapped == 1)
                        {
                            row.action = 'Mapped';
                        }
                        else
                        {
                            row.action = '<input title="Create New Device" type="button" class="addEditUnknownDeviceButton" data-device-instance-ids="' + row.deviceInstanceIds + '" value="Create New" />';
                        }
                    }

                    // Put our new data back into the grid
                    grid.setRowData(ids[i], row);

                    // Setup autocomplete for our textbox
                    $(".autoCompleteDeviceName").autocomplete({
                        source   : function (request, response)
                        {
                            $.ajax({
                                url     : TMTW_BASEURL + "proposalgen/admin/search-for-device",
                                dataType: "json",
                                data    : {
                                    searchTerm: request.term
                                },
                                success : function (data)
                                {
                                    response($.map(data, function (item)
                                    {
                                        return {
                                            value       : item.device_name,
                                            id          : item.id,
                                            label       : item.device_name,
                                            manufacturer: item.fullName
                                        };
                                    }));
                                }
                            });
                        },
                        minLength: 0,
                        select   : function (event, ui)
                        {
                            $(this).parent().find("input.masterDeviceId")[0].value = ui.item.id;
                            $(this).parent().find("input.masterDeviceName")[0].value = ui.item.label;
                            $(this).parent().find("input.manufacturerName")[0].value = ui.item.manufacturer;
                        },
                        open     : function (event, ui)
                        {
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
                            var rmsUploadRowId = this.id.replace("txtMasterDevices", "");
                            var masterDeviceId = $.trim(parent.find("input.masterDeviceId")[0].value);
                            var deviceName = $.trim(parent.find("input.masterDeviceName")[0].value);
                            var deviceInstanceIds = $(this).parent().find("input.deviceInstanceIds")[0].value;

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
                                set_mapped(deviceInstanceIds, masterDeviceId);
                            }
                            else
                            {
                                parent.find("input.masterDeviceId")[0].value = "";
                                parent.find("input.masterDeviceName")[0].value = "";
                                parent.find("input.manufacturerName")[0].value = "";
                                this.value = textValue;
                                set_mapped(deviceInstanceIds, 0);
                            }

                        }
                    });
                }
            }
        }
    );

    jQuery("#grid_list").jqGrid('navGrid', '#mappingGridPager', {
        add    : false,
        del    : false,
        edit   : false,
        refresh: false,
        search : false
    }, {}, {}, {}, {}, {});


    /**
     * Adding/Editing of the unknown device
     */
    $(document).on("click", ".addEditUnknownDeviceButton", function() {
        $("#unknownDeviceDeviceInstanceIds").val($(this).data("device-instance-ids"));
        $("#addUnknownDeviceForm").submit();
    });

    /**
     * Removal of the unknown device
     */
    $(document).on("click", ".removeUnknownDeviceButton", function() {
        var deviceInstanceIds = $(this).data("device-instance-ids");
        $.ajax({
            url     : TMTW_BASEURL + "/proposalgen/fleet/remove-unknown-device",
            dataType: 'json',
            data    : {'deviceInstanceIds': deviceInstanceIds},
            success : function (data)
            {
                jQuery("#mappingGrid").trigger("reloadGrid");
            },
            error   : function (data)
            {
                alert("There was an error removing the device.");
            }

        });
    });

});


function set_mapped(deviceInstanceIds, masterDeviceId)
{
    $.ajax({
        url     : TMTW_BASEURL + '/proposalgen/fleet/set-mapped-to',
        dataType: 'json',
        data    : {
            'deviceInstanceIds': deviceInstanceIds,
            'masterDeviceId'   : masterDeviceId
        },
        complete: function (data)
        {
            console.log("Ajax set mapped to complete.");
            console.log(data);
        }
    });
}