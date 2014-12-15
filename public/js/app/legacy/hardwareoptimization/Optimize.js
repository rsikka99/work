require(['jquery', 'jqgrid', 'select2', 'bootstrap', 'bootstrap.modal.manager'], function ($)
{
    $(function ()
    {
        var ajaxRequestsActiveCounter = 0;

        $("#deviceInstanceInformationModal").hide();

        var $replacementDeviceTable = $("#replacementDeviceTable");

        var differenceClass = function (oldValue, newValue, increaseIsGood)
        {
            var result = 'no-change';
            if (newValue > oldValue)
            {
                result = (increaseIsGood) ? 'positive-change-up' : 'negative-change-up';
            }
            else if (newValue < oldValue)
            {
                result = (increaseIsGood) ? 'negative-change-down' : 'positive-change-down';
            }

            return result;
        };

        $replacementDeviceTable.jqGrid({
            url         : TMTW_BASEURL + 'hardwareoptimization/index/device-list',
            datatype    : 'json',
            colModel    : [

//@formatter:off
{ label: 'More Details',   name: 'info',             index: 'info',             align: 'center', width: 50,  sortable: false },
{ label: 'Device',         name: 'device',           index: 'device',           align: 'left',   width: 148, sortable: false },
{ label: 'Mono AMPV',      name: 'monoAmpv',         index: 'monoAmpv',         align: 'right',  width: 60,  sortable: false, formatter: 'number',   formatoptions: { decimalPlaces: 0, defaultValue: '-' } },
{ label: 'Color AMPV',     name: 'colorAmpv',        index: 'colorAmpv',        align: 'right',  width: 60,  sortable: false, formatter: 'number',   formatoptions: { decimalPlaces: 0, defaultValue: '-' } },
{ label: 'Mono CPP',       name: 'monoCpp',          index: 'monoCpp',          align: 'right',  width: 60,  sortable: false, formatter: 'currency', formatoptions: { decimalPlaces: 4, defaultValue: '-' } },
{ label: 'Color CPP',      name: 'colorCpp',         index: 'colorCpp',         align: 'right',  width: 60,  sortable: false, formatter: 'currency', formatoptions: { decimalPlaces: 4, defaultValue: '-' } },
{ label: 'Monthly Cost',   name: 'monthlyCost',      index: 'monthlyCost',      align: 'right',  width: 50,  sortable: false, formatter: 'currency' },
{ label: 'Action',         name: 'action',           index: 'action',           align: 'right',  width: 225, sortable: false },
{ label: 'Cost Delta',     name: 'costDelta',        index: 'costDelta',        align: 'right',  width: 50,  sortable: false, formatter: 'currency' },
{ label: 'Change Logic',   name: 'reason',           index: 'reason',           align: 'left',   width: 250 },

{ label: 'ID',             name: 'deviceInstanceId', index: 'deviceInstanceId', align: 'left',   width: 25,  hidden: true },
{ label: 'Raw Mono CPP',   name: 'rawMonoCpp',       index: 'rawMonoCpp',       align: 'right',  width: 50,  hidden: true },
{ label: 'Raw Color CPP',  name: 'rawColorCpp',      index: 'rawColorCpp',      align: 'right',  width: 50,  hidden: true },
{ label: 'Raw Cost Delta', name: 'rawCostDelta',     index: 'costDelta',        align: 'right',  width: 50,  hidden: true },
{ label: 'Is Color',       name: 'isColor',          index: 'isColor',          align: 'left',   width: 25,  hidden: true }
//@formatter:on
            ],
            height      : 900,
            width       : 940,
            shrinkToFit : false,
            jsonReader  : {repeatitems: false},
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
                    var button = "<button type='button' class='btn btn-inverse btn-xs'><i class='fa fa-fw fa-search'></i></button>";
                    grid.setCell(ids[i], 'info', button);

                    if (row.rawMonoCpp > targetCostPerPageMono)
                    {
                        grid.setCell(ids[i], 'monoCpp', '', 'danger');
                    }
                    else if (row.rawMonoCpp > targetCostPerPageMonoThreshold)
                    {
                        grid.setCell(ids[i], 'monoCpp', '', 'warning');
                    }
                    else
                    {
                        grid.setCell(ids[i], 'monoCpp', '', 'success');
                    }

                    if (row.isColor == 1)
                    {
                        if (row.rawColorCpp > targetCostPerPageColor)
                        {
                            grid.setCell(ids[i], 'colorCpp', '', 'danger');
                        }
                        else if (row.rawColorCpp > targetCostPerPageColorThreshold)
                        {
                            grid.setCell(ids[i], 'colorCpp', '', 'warning');
                        }
                    }

                    if (row.rawCostDelta < 0)
                    {
                        grid.setCell(ids[i], 'costDelta', '', 'negativeCostDelta');
                    }
                    else if (row.rawCostDelta > 0)
                    {
                        grid.setCell(ids[i], 'costDelta', '', 'positiveCostDelta');
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
                            /**
                             * @param data {{
                         *    deviceInstance   : {
                         *        deviceName           : string,
                         *        serialNumber         : string,
                         *        ipAddress            : string,
                         *        location             : string,
                         *        age                  : number,
                         *        costPerPageMonochrome: number,
                         *        monoAmpv             : number,
                         *        costPerPageColor     : number,
                         *        colorAmpv            : number,
                         *        lifeUsage            : number,
                         *        lifePageCount        : number,
                         *        maxLifePageCount     : number,
                         *        isCopy               : boolean,
                         *        isFax                : boolean,
                         *        isColor              : boolean
                         *    },
                         *    hasReplacement   : boolean,
                         *    replacementDevice: {
                         *        reason               : string,
                         *        deviceName           : string,
                         *        age                  : number,
                         *        costPerPageMonochrome: number,
                         *        costPerPageColor     : number,
                         *        maxLifePageCount     : number,
                         *        isCopy               : boolean,
                         *        isFax                : boolean,
                         *        isColor              : boolean
                         *    }
                         *}}
                             */

                            var divTag = $("#deviceInstanceInformationModal");
                            var $table = $("#deviceInstanceInformationModalTable");
                            var $replacementTable = $("#replacementInformationModalTable");
                            var $replacementReason = $("#replacementReason");

                            $replacementReason.empty().hide();
                            $table.empty();
                            $replacementTable.empty();

                            $table.append($("<tr></tr>").append("<td colspan='2'><strong>Current Device</strong></td>"));
                            $table.append($("<tr></tr>").append("<td colspan='2'><em>" + data.deviceInstance.deviceName + "</em></td>"));
                            $table.append($("<tr></tr>").append("<td>Serial Number</td><td>" + data.deviceInstance.serialNumber + "</td>"));
                            $table.append($("<tr></tr>").append("<td>IP Address</td><td>" + data.deviceInstance.ipAddress + "</td>"));
                            $table.append($("<tr></tr>").append("<td>Location</td><td>" + data.deviceInstance.location + "</td>"));
                            $table.append($("<tr></tr>").append("<td>Age</td><td>" + data.deviceInstance.age + "y</td>"));
                            $table.append($("<tr></tr>").append("<td>Mono CPP</td><td>" + data.deviceInstance.costPerPageMonochrome + "</td>"));
                            $table.append($("<tr></tr>").append("<td>AMPV - Mono</td><td>" + data.deviceInstance.monoAmpv + "</td>"));

                            if (data.deviceInstance.isColor == 1)
                            {
                                $table.append($("<tr></tr>").append("<td>Color CPP</td><td>" + data.deviceInstance.costPerPageColor + "</td>"));
                                $table.append($("<tr></tr>").append("<td>AMPV - Color</td><td>" + data.deviceInstance.colorAmpv + "</td>"));
                            }

                            $table.append($("<tr></tr>").append("<td>Life Usage</td> <td>" + data.deviceInstance.lifeUsage + "</td> "));
                            $table.append($("<tr></tr>").append("<td>Life Page Count</td> <td>" + data.deviceInstance.lifePageCount + "</td> "));
                            $table.append($("<tr></tr>").append("<td>Max. Life Page Count</td> <td>" + data.deviceInstance.maxLifePageCount + "</td> "));

                            if (data.deviceInstance.isCopy)
                            {
                                $table.append($("<tr></tr>").append("<td>Copier</td><td>Yes</td>"));
                            }
                            else
                            {
                                $table.append($("<tr></tr>").append("<td>Copier</td><td>No</td>"));
                            }

                            if (data.deviceInstance.isFax)
                            {
                                $table.append($("<tr></tr>").append("<td>Fax</td><td>Yes</td>"));
                            }
                            else
                            {
                                $table.append($("<tr></tr>").append("<td>Fax</td><td>No</td>"));
                            }

                            $table.append($("<tr></tr>").append("<td>PPM - Mono</td> <td>" + data.deviceInstance.ppmBlack + "</td>"));

                            if (data.deviceInstance.isColor == 1)
                            {
                                $table.append($("<tr></tr>").append("<td>PPM - Color</td><td>" + data.deviceInstance.ppmColor + "</td>"));
                            }

                            $table.append($("<tr></tr>").append("<td>Coverage - Mono</td><td>" + data.deviceInstance.pageCoverageMonochrome + "</td>"));

                            if (data.deviceInstance.isColor == 1)
                            {
                                $table.append($("<tr></tr>").append("<td>Coverage - Cyan</td><td>" + data.deviceInstance.pageCoverageCyan + "</td>"));
                                $table.append($("<tr></tr>").append("<td>Coverage - Magenta</td><td>" + data.deviceInstance.pageCoverageMagenta + "</td>"));
                                $table.append($("<tr></tr>").append("<td>Coverage - Yellow</td><td>" + data.deviceInstance.pageCoverageYellow + "</td>"));
                            }

                            // Replacement Device Information
                            if (data.hasReplacement == 1)
                            {
                                $replacementReason.append("<strong>Reason For Replacement: </strong>");
                                $replacementReason.append(data.replacementDevice.reason).show();
                                var blankRow = "<tr><td colspan='2'>&nbsp;</td></tr>";
                                $replacementTable.show();

                                $replacementTable.append($("<tr></tr>").append("<td colspan='2'><strong>New Device</strong></td>"));
                                $replacementTable.append($("<tr></tr>").append("<td colspan='2'><em>" + data.replacementDevice.deviceName + "</em></td>"));


                                // Blank row to match device rows up
                                $replacementTable.append($(blankRow));
                                // Blank row to match device rows up
                                $replacementTable.append($(blankRow));

                                $replacementTable.append($("<tr></tr>").append("<td>Age</td><td>" + data.replacementDevice.age + "y</td>"));

                                $replacementTable.append($("<tr></tr>").append("<td>Mono CPP</td><td>" + data.replacementDevice.costPerPageMonochrome + "</td>"));


                                // Blank row to match device rows up
                                $replacementTable.append($(blankRow));


                                if (data.replacementDevice.isColor == 1)
                                {
                                    $replacementTable.append($("<tr></tr>").append("<td>Color CPP</td><td>" + data.replacementDevice.costPerPageColor + "</td>"));

                                    // Blank row to match device rows up
                                    $replacementTable.append($(blankRow));
                                }

                                $replacementTable.append($("<tr></tr>").append("<td>Max. Life Page Count</td> <td>" + data.replacementDevice.maxLifePageCount + "</td> "));

                                // Blank row to match device rows up
                                $replacementTable.append($(blankRow));

                                if (data.replacementDevice.isCopy)
                                {
                                    $replacementTable.append($("<tr></tr>").append("<td>Copier</td><td>Yes</td>"));
                                }
                                else
                                {
                                    $replacementTable.append($("<tr></tr>").append("<td>Copier</td><td>No</td>"));
                                }

                                if (data.replacementDevice.isFax)
                                {
                                    $replacementTable.append($("<tr></tr>").append("<td>Fax</td><td>Yes</td>"));
                                }
                                else
                                {
                                    $replacementTable.append($("<tr></tr>").append("<td>Fax</td><td>No</td>"));
                                }

                                $replacementTable.append($("<tr></tr>").append("<td>PPM - Mono</td> <td>" + data.replacementDevice.ppmBlack + "</td>"));

                                if (data.replacementDevice.isColor == 1)
                                {
                                    $replacementTable.append($("<tr></tr>").append("<td>PPM - Color</td><td>" + data.replacementDevice.ppmColor + "</td>"));
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
                                    height   : 450,
                                    width    : myWidth,
                                    resizable: false
                                }).dialog('open', {position: [e.pageX + 5, e.pageY + 5]});
                        }
                    });
                }
            }
        });


        $(window).bind('resize', function ()
        {
            $replacementDeviceTable.setGridWidth($replacementDeviceTable.closest('.replacementDevicesTableWrapper').width(), true);
        }).trigger('resize');

        // Hide the loading div at the beginning
        $('#optimizationTable').load(TMTW_BASEURL + 'hardwareoptimization/index/summary-table', '', function ()
        {
            $('#loadingDiv').hide();
        });


        var clearSummaryTableClasses = function (elements)
        {
            $.each(elements, function (index, $element)
            {
                $element.removeClass('no-change');
                $element.removeClass('positive-change-up');
                $element.removeClass('positive-change-down');
                $element.removeClass('negative-change-up');
                $element.removeClass('negative-change-down');
            });
        };

        $replacementDeviceTable.on('change', 'select', function ()
        {
            var $thisSelectElement = $(this);
            var elementId = $thisSelectElement.attr("id");
            var deviceInstanceId = $thisSelectElement.data('device-instance-id');

            // See if it's a reason or it's a device element that has been changed
            if (elementId.search("deviceInstanceReason") == -1)
            {
                var replacementDeviceId = $thisSelectElement.val();

                // Get the jqGrid and the id of the row we changed
                var rowId = $thisSelectElement.closest('tr').attr('id');

                $.ajax({
                    url       : TMTW_BASEURL + 'hardwareoptimization/index/update-replacement-device',
                    dataType  : 'json',
                    data      : {
                        deviceInstanceId   : deviceInstanceId,
                        replacementDeviceId: replacementDeviceId
                    },
                    beforeSend: function ()
                    {
                        $('#loadingDiv').show();
                        $replacementDeviceTable.setCell(rowId, 'costDelta', 'Loading...');
                        ajaxRequestsActiveCounter++;
                    },
                    complete  : function ()
                    {
                        ajaxRequestsActiveCounter--;
                        if (ajaxRequestsActiveCounter < 1)
                        {
                            $('#loadingDiv').hide();
                        }
                    },
                    success   : function (data)
                    {
                        /**
                         * @param data {{
                     *    summary                : {
                     *        current  : {
                     *            monochromeCpp       : number,
                     *            monochromePageVolume: number,
                     *            colorCpp            : number,
                     *            colorPageVolume     : number,
                     *            totalCost           : number,
                     *            totalRevenue        : number,
                     *            marginDollar        : number,
                     *            marginPercent       : number
                     *        },
                     *        optimized: {
                     *            monochromeCpp       : number,
                     *            monochromePageVolume: number,
                     *            colorCpp            : number,
                     *            colorPageVolume     : number,
                     *            totalCost           : number,
                     *            totalRevenue        : number,
                     *            marginDollar        : number,
                     *            marginPercent       : number
                     *        },
                     *    },
                     *    costDelta              : number,
                     *    replaceReason          : string,
                     *    deviceActionCount: {
                     *        keep   : number,
                     *        retire : number,
                     *        replace: number,
                     *        upgrade: number,
                     *        dnr    : number,
                     *        total  : number,
                     *    }
                     * }}
                         */

                        try
                        {
                            var $monochromeCpp = $('#monochromeCpp'),
                                $monochromePageVolume = $('#monochromePageVolume'),
                                $colorCpp = $('#colorCpp'),
                                $colorPageVolume = $('#colorPageVolume'),
                                $totalCost = $('#totalCost'),
                                $totalRevenue = $('#totalRevenue'),
                                $marginDollar = $('#marginDollar'),
                                $marginPercent = $('#marginPercent'),
                                $numberOfCostOptimizedDevices = $('#numberOfCostOptimizedDevices'),
                                $numberOfColorOptimizedDevices = $('#numberOfColorOptimizedDevices'),
                                $numberOfRetireDevices = $('#numberOfRetireDevices'),
                                $numberOfDoNotRepairDevices = $('#numberOfDoNotRepairDevices'),
                                $numberOfKeepDevices = $('#numberOfKeepDevices'),
                                $numberOfDevices = $('#numberOfDevices');

                            var summaryTableElements = [$monochromeCpp, $monochromePageVolume, $colorCpp, $colorPageVolume, $totalCost, $totalRevenue, $marginDollar, $marginPercent];
                            clearSummaryTableClasses(summaryTableElements);

                            var costDeltaColumnIndex = getColumnSrcIndexByName($replacementDeviceTable, 'costDelta');
                            var costDeltaTr = $replacementDeviceTable[0].rows.namedItem(rowId);
                            var costDeltaTd = costDeltaTr.cells[costDeltaColumnIndex];
                            $(costDeltaTd).removeClass("positiveCostDelta negativeCostDelta");

                            $replacementDeviceTable.setCell(rowId, 'reason', data.replaceReason);

                            var costDeltaClass = '';
                            if (data.costDelta > 0)
                            {
                                costDeltaClass = 'positiveCostDelta';

                            }
                            else if (data.costDelta < 0)
                            {
                                costDeltaClass = 'negativeCostDelta';
                            }

                            $replacementDeviceTable.setCell(rowId, 'costDelta', data.costDelta, costDeltaClass);

                            // Update the calculation
                            $monochromeCpp.html(numeral(data.summary.optimized.monochromeCpp).format(format.costPerPage));
                            $monochromeCpp.addClass(differenceClass(data.summary.current.monochromeCpp, data.summary.optimized.monochromeCpp, false));

                            $monochromePageVolume.html(numeral(data.summary.optimized.monochromePageVolume).format(format.pageVolume));
                            $monochromePageVolume.addClass(differenceClass(data.summary.current.monochromePageVolume, data.summary.optimized.monochromePageVolume, false));

                            $colorCpp.html(numeral(data.summary.optimized.colorCpp).format(format.costPerPage));
                            $colorCpp.addClass(differenceClass(data.summary.current.colorCpp, data.summary.optimized.colorCpp, false));

                            $colorPageVolume.html(numeral(data.summary.optimized.colorPageVolume).format(format.pageVolume));
                            $colorPageVolume.addClass(differenceClass(data.summary.current.colorPageVolume, data.summary.optimized.colorPageVolume, true));

                            $totalCost.html(numeral(data.summary.optimized.totalCost).format(format.currency));
                            $totalCost.addClass(differenceClass(data.summary.current.totalCost, data.summary.optimized.totalCost, false));

                            $totalRevenue.html(numeral(data.summary.optimized.totalRevenue).format(format.currency));
                            $totalRevenue.addClass(differenceClass(data.summary.current.totalRevenue, data.summary.optimized.totalRevenue, true));

                            $marginDollar.html(numeral(data.summary.optimized.marginDollar).format(format.currency));
                            $marginDollar.addClass(differenceClass(data.summary.current.marginDollar, data.summary.optimized.marginDollar, true));

                            $marginPercent.html(numeral(data.summary.optimized.marginPercent).format(format.marginPercent));
                            $marginPercent.addClass(differenceClass(data.summary.current.marginPercent, data.summary.optimized.marginPercent, true));

                            $numberOfCostOptimizedDevices.html(numeral(data.deviceActionCount.replace).format(format.number));
                            $numberOfColorOptimizedDevices.html(numeral(data.deviceActionCount.upgrade).format(format.number));
                            $numberOfRetireDevices.html(numeral(data.deviceActionCount.retire).format(format.number));
                            $numberOfDoNotRepairDevices.html(numeral(data.deviceActionCount.dnr).format(format.number));
                            $numberOfKeepDevices.html(numeral(data.deviceActionCount.keep).format(format.number));
                            $numberOfDevices.html(numeral(data.deviceActionCount.total).format(format.number));
                        }
                        catch (e)
                        {
                            console.log(e);
                        }
                    }
                });
            }
            else
            {
                var replacementReasonId = $(this).val();
                $.ajax({
                    url     : TMTW_BASEURL + 'hardwareoptimization/index/update-device-swap-reason',
                    dataType: 'json',
                    data    : {
                        deviceInstanceId   : deviceInstanceId,
                        replacementReasonId: replacementReasonId
                    },
                    success : function (data)
                    {

                    }
                });
            }
        });
    });
});