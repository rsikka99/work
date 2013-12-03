$(function ()
{
//    var ajaxArray = [];
    var ajaxCounter = 0;

    $("#deviceInstanceInformationModal").hide();
    var getColumnSrcIndexByName = function (grid, columnName)
    {
        var cm = grid.jqGrid('getGridParam', 'colModel'),
            i = 0, index = 0, l = cm.length, cmName;
        while (i < l)
        {
            cmName = cm[i].name;
            i++;
            if (cmName === columnName)
            {
                return index;
            }
            else if (cmName !== 'rn' && cmName !== 'cb' && cmName !== 'subgrid')
            {
                index++;
            }
        }
        return -1;
    };

    $("#replacementDeviceTable").jqGrid({
        url         : TMTW_BASEURL + 'memjetoptimization/index/device-list',
        datatype    : 'json',
        colModel    : [
            { label: 'Device', name: 'device', index: 'device', align: 'left', width: 130, frozen: true, sortable: false },
            { label: 'Mono AMPV', name: 'monoAmpv', index: 'monoAmpv', align: 'right', width: 50, sortable: false },
            { label: 'Color AMPV', name: 'colorAmpv', index: 'colorAmpv', align: 'right', width: 50, sortable: false },
            { label: 'Estimated Mono AMPV', name: 'estimatedMonoAmpv', index: 'estimatedMonoAmpv', align: 'right', width: 65, sortable: false },
            { label: 'Estimated Color AMPV', name: 'estimatedColorAmpv', index: 'estimatedColorAmpv', align: 'right', width: 65, sortable: false },
            { label: 'Mono CPP', name: 'monoCpp', index: 'monoCpp', align: 'right', width: 50, sortable: false },
            { label: 'Raw Mono CPP', name: 'rawMonoCpp', index: 'rawMonoCpp', align: 'right', width: 50, hidden: true },
            { label: 'Color CPP', name: 'colorCpp', index: 'colorCpp', align: 'right', width: 50, sortable: false },
            { label: 'Raw Color CPP', name: 'rawColorCpp', index: 'rawColorCpp', align: 'right', width: 50, hidden: true },
            { label: 'Monthly Cost', name: 'monthlyCost', index: 'monthlyCost', align: 'right', width: 65, sortable: false },
            { label: 'Action', name: 'action', index: 'action', align: 'right', width: 125, sortable: false },
            { label: 'Cost Delta', name: 'costDelta', index: 'costDelta', align: 'right', width: 50, sortable: false },
            { label: 'Raw Cost Delta', name: 'rawCostDelta', index: 'costDelta', align: 'right', width: 50, hidden: true },
            { label: 'More Details', name: 'info', index: 'info', align: 'center', width: 50, sortable: false },
            { label: 'ID', name: 'deviceInstanceId', index: 'deviceInstanceId', align: 'left', width: 25, hidden: true},
            { label: 'Is Color', name: 'isColor', index: 'isColor', align: 'left', width: 25, hidden: true },
            { label: 'Reason', name: 'reason', index: 'reason', align: 'left', width: 200 }
        ],
        height      : 900,
        width       : 940,
        shrinkToFit : false,
        jsonReader  : { repeatitems: false },
        caption     : "Purchased Devices",
        rowNum      : 25,
        rowList     : [25, 50, 100],
        pager       : '#replacementDevicePager',
        loadui      : "block",
        gridComplete: function ()
        {
            var grid = $(this);
            var ids = grid.getDataIDs();
            for (var i = 0; i < ids.length; i++)
            {
                // Get the data so we can use and manipulate it.
                var row = grid.getRowData(ids[i]);
                var button = "<button type='button' class='btn btn-inverse btn-mini'><i class='icon-search icon-white'></i></button>";
                grid.setCell(ids[i], 'info', button);

                if (row.rawMonoCpp > targetCostPerPageMono)
                {
                    grid.setCell(ids[i], 'monoCpp', '', 'dangerThreshhold');
                }
                else if (row.rawMonoCpp > targetCostPerPageMonoThreshold)
                {
                    grid.setCell(ids[i], 'monoCpp', '', 'warningThreshhold');
                }

                if (row.isColor == 1)
                {
                    if (row.rawColorCpp > targetCostPerPageColor)
                    {
                        grid.setCell(ids[i], 'colorCpp', '', 'dangerThreshhold');
                    }
                    else if (row.rawColorCpp > targetCostPerPageColorThreshold)
                    {
                        grid.setCell(ids[i], 'colorCpp', '', 'warningThreshhold');
                    }
                }

                if (row.rawCostDelta < 0)
                {
                    grid.setCell(ids[i], 'costDelta', '', {'background-color': 'red', 'font-weight': 'bold', 'color': 'white'});
                }
            }
        },
        onCellSelect: function (rowid, iCol, cellcontent, e)
        {
            var grid = $(this);
            var nameColumn = getColumnSrcIndexByName(grid, 'info');
            if (iCol == nameColumn)
            {
                var row = grid.getRowData(rowid);
                var deviceInstanceId = row.deviceInstanceId;

                $.ajax({
                    data    : {deviceInstanceId: deviceInstanceId},
                    type    : 'POST',
                    dataType: 'json',
                    url     : deviceListUrl,
                    success : function (data)
                    {
                        var divTag = $("#deviceInstanceInformationModal");
                        var $table = $("#deviceInstanceInformationModalTable");
                        var $replacementTable = $("#replacementInformationModalTable");
                        var $replacementReason = $("#replacementReason");

                        $replacementReason.empty().hide();
                        $table.empty();
                        $replacementTable.empty();

                        $row = $("<tr></tr>");
                        $row.append("<td colspan='2'><strong>Current Device</strong></td>");
                        $table.append($row);

                        $row = $("<tr></tr>");
                        $row.append("<td colspan='2'><em>" + data.deviceInstance.deviceName + "</em></td>");
                        $table.append($row);

                        $row = $("<tr></tr>");
                        $row.append("<td>Serial Number</td><td>" + data.deviceInstance.serialNumber + "</td>");
                        $table.append($row);

                        $row = $("<tr></tr>");
                        $row.append("<td>IP Address</td><td>" + data.deviceInstance.ipAddress + "</td>");
                        $table.append($row);

                        $row = $("<tr></tr>");
                        $row.append("<td>Mono CPP</td><td>" + data.deviceInstance.costPerPageMonochrome + "</td>");
                        $table.append($row);

                        $row = $("<tr></tr>");
                        $row.append("<td>AMPV - Mono</td><td>" + data.deviceInstance.monoAmpv + "</td>");
                        $table.append($row);

                        if (data.deviceInstance.isColor == 1)
                        {
                            $row = $("<tr></tr>");
                            $row.append("<td>Color CPP</td><td>" + data.deviceInstance.costPerPageColor + "</td>");
                            $table.append($row);

                            $row = $("<tr></tr>");
                            $row.append("<td>AMPV - Color</td><td>" + data.deviceInstance.colorAmpv + "</td>");
                            $table.append($row);
                        }

                        $row = $("<tr></tr>");
                        $row.append("<td>Life Page Count</td> <td>" + data.deviceInstance.lifePageCount + "</td> ");
                        $table.append($row);
                        $row = $("<tr></tr>");

                        $row = $("<tr></tr>");
                        if (data.deviceInstance.isCopy)
                        {
                            $row.append("<td>Copier</td><td>Yes</td>");
                        }
                        else
                        {
                            $row.append("<td>Copier</td><td>No</td>");
                        }
                        $table.append($row);

                        $row = $("<tr></tr>");
                        if (data.deviceInstance.isFax)
                        {
                            $row.append("<td>Fax</td><td>Yes</td>");
                        }
                        else
                        {
                            $row.append("<td>Fax</td><td>No</td>");
                        }
                        $table.append($row);

                        $row = $("<tr></tr>");
                        $row.append("<td>PPM - Mono</td> <td>" + data.deviceInstance.ppmBlack + "</td>");
                        $table.append($row);

                        if (data.deviceInstance.isColor == 1)
                        {
                            $row = $("<tr></tr>");
                            $row.append("<td>PPM - Color</td><td>" + data.deviceInstance.ppmColor + "</td>");
                            $table.append($row);
                        }
                        // Replacement Device Information
                        if (data.hasReplacement == 1)
                        {
                            $replacementReason.append("<strong>Reason For Replacement: </strong>");
                            $replacementReason.append(data.replacementDevice.reason).show();
                            var blankRow = "<tr><td colspan='2'>&nbsp;</td></tr>";
                            $replacementTable.show();

                            $row = $("<tr></tr>");
                            $row.append("<td colspan='2'><strong>New Device</strong></td>");
                            $replacementTable.append($row);

                            $row = $("<tr></tr>");
                            $row.append("<td colspan='2'><em>" + data.replacementDevice.deviceName + "</em></td>");
                            $replacementTable.append($row);

                            // Blank row to match device rows up
                            $replacementTable.append($(blankRow));
                            // Blank row to match device rows up
                            $replacementTable.append($(blankRow));

                            $row = $("<tr></tr>");
                            $row.append("<td>Mono CPP</td><td>" + data.replacementDevice.costPerPageMonochrome + "</td>");
                            $replacementTable.append($row);

                            // Blank row to match device rows up
                            $replacementTable.append($(blankRow));

                            if (data.replacementDevice.isColor == 1)
                            {
                                $row = $("<tr></tr>");
                                $row.append("<td>Color CPP</td><td>" + data.replacementDevice.costPerPageColor + "</td>");
                                $replacementTable.append($row);

                                // Blank row to match device rows up
                                $replacementTable.append($(blankRow));
                            }

                            // Blank row to match device rows up
                            $replacementTable.append($(blankRow));

                            // Blank row to match device rows up
                            $replacementTable.append($(blankRow));


                            $row = $("<tr></tr>");
                            if (data.replacementDevice.isCopy)
                            {
                                $row.append("<td>Copier</td><td>Yes</td>");
                            }
                            else
                            {
                                $row.append("<td>Copier</td><td>No</td>");
                            }
                            $replacementTable.append($row);
                            $row = $("<tr></tr>");
                            if (data.replacementDevice.isFax)
                            {
                                $row.append("<td>Fax</td><td>Yes</td>");
                            }
                            else
                            {
                                $row.append("<td>Fax</td><td>No</td>");
                            }
                            $replacementTable.append($row);

                            $row = $("<tr></tr>");
                            $row.append("<td>PPM - Mono</td> <td>" + data.replacementDevice.ppmBlack + "</td>");
                            $replacementTable.append($row);

                            if (data.replacementDevice.isColor == 1)
                            {
                                $row = $("<tr></tr>");
                                $row.append("<td>PPM - Color</td><td>" + data.replacementDevice.ppmColor + "</td>");
                                $replacementTable.append($row);
                            }
                        }
                        else
                        {
                            $replacementTable.hide();
                        }

                        var myWidth = (data.hasReplacement == 1) ? 650 : 350;

                        divTag.dialog(
                            {
                                modal    : true,
                                title    : "Device Information",
                                height   : 375,
                                width    : myWidth,
                                resizable: false
                            }).dialog('open', { position: [e.pageX + 5, e.pageY + 5]});
                    }
                });
            }
        }
    });
    jQuery("#replacementDeviceTable").jqGrid('setFrozenColumns');

    // Hide the loading div at the beginning
    $('#optimizationTable').load(TMTW_BASEURL + 'memjetoptimization/index/summary-table', '', function ()
    {
        $('#loadingDiv').hide();
    });

    $(document).on('change', 'select',
        function ()
        {
            var elementId = $(this).attr("id");

            // See if it's a reason or it's a device element that has been changed
            if (elementId.search("deviceInstanceReason") == -1)
            {
                var replacementDeviceId = $(this).val();

                // Get the jqGrid and the id of the row we changed
                var grid = jQuery("#replacementDeviceTable");
                var rowId = $(this).closest('tr').attr('id');

                $.ajax({
                    url       : TMTW_BASEURL + 'memjetoptimization/index/update-replacement-device',
                    dataType  : 'json',
                    data      : {
                        deviceInstanceId   : elementId,
                        replacementDeviceId: replacementDeviceId
                    },
                    beforeSend: function ()
                    {
                        $('#loadingDiv').show();
                        grid.setCell(rowId, 'costDelta', 'Loading...');
                        grid.setCell(rowId, 'estimatedMonoAmpv', 'Loading...');
                        grid.setCell(rowId, 'estimatedColorAmpv', 'Loading...');
                        ajaxCounter++;
                    },
                    complete  : function ()
                    {
                        ajaxCounter--;
                        if (ajaxCounter < 1)
                        {
                            $('#loadingDiv').hide();
                        }
                    },
                    success   : function (data)
                    {
                        grid.setCell(rowId, 'reason', data.device.replaceReason);
                        grid.setCell(rowId, 'rawCostDelta', data.device.rawCostDelta);
                        grid.setCell(rowId, 'costDelta', data.device.costDelta, (data.device.rawCostDelta >= 0) ? {'background-color': 'white', 'font-weight': 'normal', 'color': 'black'} : {'background-color': 'red', 'font-weight': 'bold', 'color': 'white'});

                        grid.setCell(rowId, 'estimatedMonoAmpv', data.device.estimatedMonoAmpv);
                        grid.setCell(rowId, 'estimatedColorAmpv', data.device.estimatedColorAmpv);

                        // Update the calculation
                        $('#monochromeCpp').html(data.summary.monochromeCpp);
                        $('#colorCpp').html(data.summary.colorCpp);
                        $('#totalRevenue').html(data.summary.totalRevenue);
                        $('#monoVolume').html(data.summary.monoVolume);
                        $('#monoVolumePercent').html(data.summary.monoVolumePercent);
                        $('#colorVolume').html(data.summary.colorVolume);
                        $('#colorVolumePercent').html(data.summary.colorVolumePercent);

                        $('#totalCost').html(data.summary.totalCost);
                        $('#marginDollar').html(data.summary.marginDollar);
                        $('#marginPercent').html(data.summary.marginPercent);
                        $('#grossMarginDelta').html(data.summary.grossMarginDelta);
                        $('#grossMarginDeltaTitle').html((data.summary.grossMarginDeltaIsPositive) ? "Gross Margin Increase" : "Gross Margin Decrease");
                        $('#customerCostDelta').html(data.summary.customerCostDelta);
                        $('#customerCostDeltaTitle').html((data.summary.customerCostDeltaIsPositive) ? "Customer Cost Increase" : "Customer Cost Decrease");
                        $('#numberOfDevicesReplaced').html(data.summary.numberOfDevicesReplaced);
                    }
                });
            }
            else
            {

                var replacementReasonId = $(this).val();
                $.ajax({
                    url     : TMTW_BASEURL + 'memjetoptimization/index/update-device-swap-reason',
                    dataType: 'json',
                    data    : {
                        deviceInstanceId   : elementId,
                        replacementReasonId: replacementReasonId
                    },
                    success : function (data)
                    {

                    }
                });
            }
        });
});
