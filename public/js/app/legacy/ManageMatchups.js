require(['jquery', 'jqgrid', 'bootstrap.typeahead'], function ($)
{
    $(document).ready(function ()
    {
        $("#grid_list").jqGrid({
            url         : TMTW_BASEURL + 'proposalgen/admin/matchuplist',
            datatype    : 'json',
            colModel    : [
//@formatter:off
{ width: 10,  name: 'rmsProviderId',         index: 'rmsProviderId',         label: 'Provider Id',         hidden: true    },
{ width: 35,  name: 'masterDeviceId',        index: 'masterDeviceId',        label: 'masterDeviceId',      hidden: true    },
{ width: 35,  name: 'modelName',             index: 'modelName',             label: 'modelName',           hidden: true    },
{ width: 35,  name: 'displayname',           index: 'displayname',           label: 'displayname',         hidden: true    },
{ width: 90,  name: 'rmsProviderName',       index: 'rmsProviderName',       label: 'Provider'                             },
{ width: 90,  name: 'rmsModelId',            index: 'rmsModelId',            label: 'Model Id',            align: 'center' },
{ width: 300, name: 'rmsProviderDeviceName', index: 'rmsProviderDeviceName', label: 'Provider Device Name'                 },
{ width: 350, name: 'masterDevice',          index: 'masterDevice',          label: 'Master Device'                        }
//@formatter:on
            ],
            height      : 'auto',
            jsonReader  : {
                repeatitems: false
            },
            rowNum      : 10,
            "rowList"   : [10, 15, 25, 50, 100],
            pager       : '#grid_pager',
            gridComplete: function ()
            {
                var grid = $("#grid_list").jqGrid();
                var ids = grid.getDataIDs();

                // Loop through the rows
                for (var i = 0; i < ids.length; i++)
                {
                    var rowData = grid.getRowData(ids[i]);

                    var rmsKey = rowData.rmsProviderId + "_" + rowData.rmsModelId;

                    // build hidden field for master_device_id
                    var devices_pf_id = document.getElementById("grid_list").rows[i + 1].cells[0].innerHTML;
                    var master_device_id = document.getElementById("grid_list").rows[i + 1].cells[1].innerHTML;

                    var mapped_to = rowData.modelName;
                    var mapped_to_id = rowData.master_device_id;
                    var mapped_to_manufacturer = rowData.displayname;
                    var mapped_to_deviceName = mapped_to_manufacturer + " " + mapped_to;

                    rowData.masterDevice = '';
                    rowData.masterDevice += '<input type="hidden" class="masterDeviceId" value="' + mapped_to_id + '" />';
                    rowData.masterDevice += '<input type="hidden" class="masterDeviceName" value="' + mapped_to_deviceName + '" />';
                    rowData.masterDevice += '<input type="hidden" class="manufacturerName" value="' + mapped_to_manufacturer + '" />';
                    rowData.masterDevice += '<input style="width: 100%" class="form-control text-left" type="text" name="txtMasterDevices' + rmsKey + '" id="txtMasterDevices' + rmsKey + '" size="55" class="autoCompleteDeviceName" value="' + mapped_to_deviceName + '" />';

                    grid.setRowData(ids[i], rowData);
                }

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
                                data.unshift({
                                    device_name: "Remove mapping",
                                    fullname   : "Remove mapping",
                                    id         : 0,
                                    modelName  : "Remove Mapping"
                                });
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
                        $(this).autocomplete('option', 'change').call(this);

                    },
                    open     : function ()
                    {
                        // Perform auto complete, and bold the match criteria to help the end user see what was matched.
                        var termTemplate = '<strong>%s</strong>';
                        var autocompleteData = $(this).data('uiAutocomplete');
                        autocompleteData.menu.element.find('a').each(function ()
                        {
                            var label = $(this);
                            if (label.text() == "Remove mapping")
                            {
                                label.html("<strong>Remove Mapping</strong>");
                            }
                            else
                            {
                                var regex = new RegExp(autocompleteData.term, "gi");
                                label.html(label.text().replace(regex, function (matched)
                                {
                                    return termTemplate.replace('%s', matched);
                                }));
                            }
                        });
                    },
                    change   : function ()
                    {
                        var parent = $(this).parent();
                        var textValue = $.trim(this.value);

                        // We stored our ids in our text box name as txtMasterDevices{ProviderId}_{ModelId}
                        var rmsUploadRowId = this.id.replace("txtMasterDevices", "");
                        var identifiers = rmsUploadRowId.split('_');
                        var rmsProviderId = identifiers[0];
                        var rmsModelId = identifiers[1];
                        var masterDeviceId = $.trim(parent.find("input.masterDeviceId")[0].value);
                        var deviceName = $.trim(parent.find("input.masterDeviceName")[0].value);

                        /*
                         * Populate the text field if the user was auto completing, or clear it out if they were deleting the text
                         */
                        if (textValue && masterDeviceId > 0)
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
            },
            editurl     : 'dummy.php'
        });

    });

    /**
     * Sets a device mapping to a new master device
     *
     * @param {Number} rmsProviderId The rms provider id
     * @param {Number} rmsModelId The rms model id
     * @param {Number} masterDeviceId The master device id. Can be 0 or false
     */
    function set_mapped(rmsProviderId, rmsModelId, masterDeviceId)
    {
        $.ajax({
            type       : "GET",
            contentType: "application/json; charset=utf-8",
            url        : TMTW_BASEURL + 'proposalgen/admin/set-mapped-to',
            data       : {
                "rmsProviderId" : rmsProviderId,
                "rmsModelId"    : rmsModelId,
                "masterDeviceId": masterDeviceId
            },
            complete   : function ()
            {
                $('#grid_list').trigger("reloadGrid");
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

        var $grid_list = $('#grid_list');
        $grid_list.setGridParam({
            url: TMTW_BASEURL + 'proposalgen/admin/matchuplist' + params
        });
        $grid_list.trigger("reloadGrid");
    }
});