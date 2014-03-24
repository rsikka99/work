/**
 * show_mapped holds the visibility state of the bottom jqgrid
 */
var show_mapped = false;

$(function ()
{
    /***********************************************************************************************************************************************************
     * UNMAPPED GRID
     **********************************************************************************************************************************************************/
    var summaryGrid = $("#summaryGrid").jqGrid(
        {
            url         : TMTW_BASEURL + 'proposalgen/fleet/device-summary-list',
            postData    : {
                rmsUploadId: rmsUploadId
            },
            datatype    : 'json',
            colModel    : [
                {
                    hidden  : true,
                    name    : 'id', index: 'id', label: 'Id',
                    sortable: false,
                    width   : 10
                },
                {
                    name    : 'deviceName', index: 'deviceName', label: 'Device Name',
                    sortable: false,
                    width   : 150
                },
                {
                    name    : 'mappedToDeviceName', index: 'mappedToDeviceName', label: 'Mapped To Device Name',
                    sortable: false,
                    width   : 110
                },
                {
                    align   : 'right',
                    name    : 'reportsTonerLevels', index: 'reportsTonerLevels', label: 'Reports Toner Levels',
                    sortable: false,
                    width   : 40
                },
                {
                    align   : 'right',
                    name    : 'isCapableOfReportingTonerLevels', index: 'isCapableOfReportingTonerLevels', label: 'Capable of Reporting Toner Levels',
                    sortable: false,
                    width   : 40
                },
                {
                    align   : 'right',
                    name    : 'ampv', index: 'ampv', label: 'AMPV',
                    sortable: false,
                    width   : 40
                },
                {
                    align   : 'center',
                    name    : 'isLeased', index: 'isLeased', label: 'Leased',
                    sortable: false,
                    width   : 40
                },
                {
                    align   : 'center',
                    name    : 'isManaged', index: 'isManaged', label: 'Managed',
                    sortable: false,
                    width   : 40
                },
                {
                    align   : 'center',
                    name    : 'isExcluded', index: 'isExcluded', label: 'Excluded',
                    sortable: false,
                    width   : 35
                },
                {
                    align   : 'center',
                    name    : 'compatibleWithJitProgram', index: 'compatibleWithJitProgram', label: 'Compatible with ' + jit + ' Program',
                    sortable: false,
                    width   : 40
                },
                {
                    name    : 'validToners', index: 'validToners', label: 'Valid Toners',
                    sortable: false,
                    hidden  : true,
                    width   : 35
                }
            ],
            jsonReader  : {
                repeatitems: false
            },
            width       : 900,
            height      : 'auto',
            rowNum      : 15,
            rowList     : [ 15, 30, 50, 100 ],
            pager       : '#summaryGridPager',
            gridComplete: function ()
            {
                // Get the grid object (cache in variable)
                var ids = summaryGrid.getDataIDs();

                for (var i = 0; i < ids.length; i++)
                {
                    // Get the data so we can use and manipualte it.
                    var row = summaryGrid.getRowData(ids[i]);

                    row.mappedToDeviceName = "<a style='text-decoration: underline;' href='javascript:void(0);' class='viewDeviceDetails' data-device-instance-id='" + row.id + "'>" + row.mappedToDeviceName + "</a>";

                    var excludedChecked = "";
                    if (row.isExcluded == 1)
                    {
                        excludedChecked = 'checked="checked"';
                    }
                    row.isExcluded = '<input type="checkbox" class="toggleDeviceInstanceExcludedButton" data-device-instance-id="' + row.id + '" ' + excludedChecked + ' />';

                    var isLeased = "";
                    if (row.isLeased == 1)
                    {
                        isLeased = 'checked="checked"';
                    }

                    var isManaged = "";
                    if (row.isManaged == 1)
                    {
                        isManaged = 'checked="checked"';
                    }

                    var validToners = '';
                    if (row.validToners == false)
                    {
                        validToners = 'disabled="disabled"';
                    }

                    var isCompatibleWithJit = "";
                    if (row.compatibleWithJitProgram == 1)
                    {
                        isCompatibleWithJit = 'checked="checked"';
                    }

                    row.isLeased = '<input type="checkbox" class="toggleLeasedButton" data-device-instance-id="' + row.id + '" ' + isLeased + " " + validToners + ' />';
                    row.isManaged = '<input type="checkbox" class="toggleManagedButton" data-device-instance-id="' + row.id + '" ' + isManaged + ' />';
                    row.compatibleWithJitProgram = '<input type="checkbox" class="toggleJitCompatibilityButton" data-device-instance-id="' + row.id + '" ' + isCompatibleWithJit + ' />';

                    // Put our new data back into the grid
                    summaryGrid.setRowData(ids[i], row);
                }
            }
        }
    );

    $(document).on("click", ".viewDeviceDetails", function ()
    {
        $.ajax({
            url     : '/proposalgen/fleet/device-instance-details',
            dataType: 'json',
            data    : {
                rmsUploadId     : rmsUploadId,
                deviceInstanceId: $(this).data("device-instance-id")
            },
            success : function (data)
            {
                /**
                 * Populate the dialog
                 */
                var deviceDetailsContainer = $('#deviceDetailsContainer');

                $('#deviceInstanceInfoTabs').find('a:first').tab('show');

                $('#deviceDetails_launchDate').empty().html(data.masterDevice.launchDate + " (" + data.masterDevice.age + "y)");
                $('#deviceDetails_isCopier').empty().html((data.masterDevice.isCopier) ? 'YES' : 'NO');
                $('#deviceDetails_isDuplex').empty().html((data.masterDevice.isDuplex) ? 'YES' : 'NO');
                $('#deviceDetails_isFax').empty().html((data.masterDevice.isFax) ? 'YES' : 'NO');
                $('#deviceDetails_isA3').empty().html((data.masterDevice.isA3) ? 'YES' : 'NO');
                $('#deviceDetails_reportsToners').empty().html((data.masterDevice.reportsTonerLevels == 1) ? 'YES' : 'NO');
                $('#deviceDetails_ppmBlack').empty().html(data.masterDevice.ppmBlack);
                $('#deviceDetails_ppmColor').empty().html(data.masterDevice.ppmColor);
                $('#deviceDetails_wattsPowerNormal').empty().html(data.masterDevice.wattsPowerNormal);
                $('#deviceDetails_wattsPowerIdle').empty().html(data.masterDevice.wattsPowerIdle);
                $('#deviceDetails_cost').empty().html(data.masterDevice.cost);
                $('#deviceDetails_tonerConfigName').empty().html(data.masterDevice.tonerConfigName);
                $('#deviceDetails_isLeased').empty().html((data.masterDevice.isLeased) ? 'YES' : 'NO');
                $('#deviceDetails_compatibleWithJit').empty().html((data.masterDevice.compatibleWithJit) ? 'YES' : 'NO');

                $('#pageCoverageMonochrome').empty().html(data.pageCoverage.monochrome);
                $('#pageCoverageCyan').empty().html(data.pageCoverage.cyan);
                $('#pageCoverageMagenta').empty().html(data.pageCoverage.magenta);
                $('#pageCoverageYellow').empty().html(data.pageCoverage.yellow);

                var metersTBody = $('#deviceDetails_meters');
                metersTBody.empty();
                metersTBody.append(
                    $('<tr></tr>')
                        .append($('<td></td>').html('Life Pages'))
                        .append($('<td></td>').html(data.meters.life))
                );

                metersTBody.append(
                    $('<tr></tr>')
                        .append($('<td></td>').html('Maximum Recommended Life Volume'))
                        .append($('<td></td>').html(data.meters.maxLife))
                );

                metersTBody.append(
                    $('<tr></tr>')
                        .append($('<td></td>').html('Monochrome Pages'))
                        .append($('<td></td>').html(data.pageCounts.monochrome))
                );

                if (data.masterDevice.isColor)
                {
                    metersTBody.append(
                        $('<tr></tr>')
                            .append($('<td></td>').html('Color Pages'))
                            .append($('<td></td>').html(data.pageCounts.color))
                    );
                }

                if (data.masterDevice.isA3)
                {
                    metersTBody.append(
                        $('<tr></tr>')
                            .append($('<td></td>').html('A3 Pages'))
                            .append($('<td></td>').html(data.pageCounts.a3Combined))
                    );
                }


                var tonerTBody = $('#deviceDetails_toners');
                tonerTBody.empty();

                if (data.masterDevice.isLeased)
                {
                    var tr = $('<tr></tr>');
                    tr.append($('<td></td>').html('N/A'));
                    tr.append($('<td></td>').html('N/A'));
                    tr.append($('<td></td>').html('N/A'));
                    tr.append($('<td></td>').html(data.masterDevice.leasedTonerYield));
                    tr.append($('<td></td>').html('N/A'));
                    tonerTBody.append(tr);
                }
                else
                {
                    $.each(data.masterDevice.toners, function (key, toner)
                    {
                        var tr = $('<tr></tr>');
                        tr.append($('<td></td>').html(toner.sku));
                        tr.append($('<td></td>').html(toner.manufacturer.fullname));
                        tr.append($('<td></td>').html(toner.tonerColorName));
                        tr.append($('<td></td>').html(toner.yield));
                        tr.append($('<td></td>').html(toner.cost));
                        tonerTBody.append(tr);
                    });
                }


                /**
                 * Show the dialog
                 */
                deviceDetailsContainer.dialog({
                    modal  : true,
                    title  : data.masterDevice.manufacturer.fullname + " " + data.masterDevice.modelName,
                    width  : 800,
                    buttons: {
                        Ok: function ()
                        {
                            $(this).dialog("close");
                        }
                    },
                    open   : function ()
                    {
                        $('.ui-widget-overlay').bind('click', function ()
                        {
                            deviceDetailsContainer.dialog('close');
                        })
                    }
                });

                // Remove Focus on the first Tab
                deviceDetailsContainer.find('a').blur();
                /**
                 * End of the dialog
                 */
            },
            error   : function ()
            {
                alert("There was an error retrieving device information.");
            }
        });

    });

    /**
     * Toggles whether or not a device is excluded
     */
    $(document).on("click", ".toggleDeviceInstanceExcludedButton", function (eventObject)
    {
        var checkbox = $(this);
        $.ajax({
            url     : TMTW_BASEURL + '/proposalgen/fleet/toggle-excluded-flag',
            dataType: 'json',
            data    : {
                rmsUploadId     : rmsUploadId,
                deviceInstanceId: checkbox.data("device-instance-id"),
                isExcluded      : (checkbox.is(':checked')) ? true : false
            },
            error   : function (jqXHR)
            {
                var data = $.parseJSON(jqXHR.responseText);
                if (checkbox.is(':checked'))
                {
                    checkbox.removeAttr('checked');
                }
                else
                {
                    checkbox.attr('checked', 'checked');
                }
                var popup = $('#excludeDeviceErrorModal');
                if (popup.length < 1)
                {
                    popup = $("<div class='modal' id='excludeDeviceErrorModal'><div class='modal-header'>Error Excluding Device<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button></div><div class='modal-body'></div><div class='modal-footer'><button type='button' data-dismiss='modal' class='btn btn-primary'>OK</button></div></div>");
                    $('body').append(popup);
                }

                popup.find('.modal-body').html(data.message);

                popup.modal({
                    hidden: function ()
                    {
                        $('body').remove(popup);
                    }
                });
            }
        });
    });

    /**
     * Toggles whether or not a device is Compatible With JIT
     */
    $(document).on("click", ".toggleJitCompatibilityButton", function (eventObject)
    {
        var checkbox = $(this);
        $.ajax({
            url     : TMTW_BASEURL + '/proposalgen/fleet/toggle-jit-flag',
            dataType: 'json',
            data    : {
                rmsUploadId             : rmsUploadId,
                deviceInstanceId        : checkbox.data("device-instance-id"),
                compatibleWithJitProgram: (checkbox.is(':checked')) ? true : false
            },
            error   : function (jqXHR)
            {
                var data = $.parseJSON(jqXHR.responseText);
                if (checkbox.is(':checked'))
                {
                    checkbox.removeAttr('checked');
                }
                else
                {
                    checkbox.attr('checked', 'checked');
                }
            }
        });
    });

    /**
     * Toggles whether or not a device is leased
     */
    $(document).on("click", ".toggleLeasedButton", function (eventObject)
    {
        var checkbox = $(this);
        $.ajax({
            url     : TMTW_BASEURL + '/proposalgen/fleet/toggle-leased-flag',
            dataType: 'json',
            data    : {
                rmsUploadId     : rmsUploadId,
                deviceInstanceId: checkbox.data("device-instance-id"),
                isLeased        : (checkbox.is(':checked')) ? true : false
            },
            error   : function (jqXHR)
            {
                // This should never happen unless they edit the html and remove the disabled flag
                alert("Device does not have valid toners");
                var data = $.parseJSON(jqXHR.responseText);
                if (checkbox.is(':checked'))
                {
                    checkbox.prop('checked', false);
                }
                else
                {
                    checkbox.prop('checked', true);
                }
            }
        });
    });

    /**
     * Toggles whether or not a device is managed
     */
    $(document).on("click", ".toggleManagedButton", function (eventObject)
    {
        var checkbox = $(this);
        $.ajax({
            url     : TMTW_BASEURL + '/proposalgen/fleet/toggle-managed-flag',
            dataType: 'json',
            data    : {
                rmsUploadId     : rmsUploadId,
                deviceInstanceId: checkbox.data("device-instance-id"),
                isManaged       : (checkbox.is(':checked')) ? true : false
            }
        });
    });

});