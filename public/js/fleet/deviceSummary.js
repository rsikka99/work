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
                    width   : 150
                },
                {
                    align   : 'right',
                    name    : 'ampv', index: 'ampv', label: 'AMPV',
                    sortable: false,
                    width   : 40
                },
                {
                    align   : 'center',
                    name    : 'isLeased', index: 'isLeased', label: 'Ownership',
                    sortable: false,
                    width   : 40
                },
                {
                    align   : 'center',
                    name    : 'isExcluded', index: 'isExcluded', label: 'Excluded',
                    sortable: false,
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

                    var checked = "";
                    if (row.isExcluded == 1)
                    {
                        checked = 'checked="checked"';
                    }
                    row.isExcluded = '<input type="checkbox" class="toggleDeviceInstanceExcludedButton" data-device-instance-id="' + row.id + '" ' + checked + ' />';

                    // Put our new data back into the grid
                    summaryGrid.setRowData(ids[i], row);
                }
            }
        }
    );

    /**
     * Toggles whether or not a device is excluded
     */
    $(document).on("click", ".toggleDeviceInstanceExcludedButton", function (eventObject)
    {
        // TODO Code Toggle Device Instance Exclusion
        if ($(this).is(':checked'))
        {
            $.ajax({
                url     : TMTW_BASEURL + '/proposalgen/fleet/toggle-excluded-flag',
                dataType: 'json',
                data    : {
                    deviceInstanceId: $(this).data("device-instance-id"),
                    isExcluded      : true
                }
            });
        }
        else
        {
            $.ajax({
                url     : TMTW_BASEURL + '/proposalgen/fleet/toggle-excluded-flag',
                dataType: 'json',
                data    : {
                    deviceInstanceId: $(this).data("device-instance-id"),
                    isExcluded      : false
                }
            });
        }
    });

});