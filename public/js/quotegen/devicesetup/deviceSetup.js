$(function ()
{
    $("#devicesGrid").jqGrid(
        {
            url       : TMTW_BASEURL + 'quotegen/devicesetup/all-devices-list',
            datatype  : 'json',
            colModel  : [
                {
                    width   : 50,
                    name    : 'id',
                    index   : 'id',
                    hidden  : true,
                    label   : 'Master Device Ids',
                    title   : false,
                    sortable: false
                },
                {
                    width   : 460,
                    name    : 'modelName',
                    index   : 'modelName',
                    label   : 'Full Device Name',
                    hidden  : false,
                    title   : false,
                    sortable: true

                },
                {
                    width   : 150,
                    name    : 'oemSku',
                    index   : 'oemSku',
                    hidden  : false,
                    label   : 'OEM SKU',
                    title   : false,
                    sortable: false
                },
                {
                    width   : 150,
                    name    : 'dealerSku',
                    index   : 'dealerSku',
                    hidden  : false,
                    label   : 'Dealer Sku',
                    title   : false,
                    sortable: false
                },
                {
                    width   : 100,
                    name    : 'displayname',
                    index   : 'displayname',
                    hidden  : true,
                    label   : 'displayname',
                    title   : false,
                    sortable: false
                },
                {
                    width   : 120,
                    name    : 'action',
                    index   : 'action',
                    label   : 'Action',
                    title   : false,
                    sortable: false
                }
            ],
            jsonReader: {
                repeatitems: false
            },

            height      : 'auto',
            rowNum      : 15,
            mType       : "GET",
            postData    : {
                canSell       : function ()
                {
                    return document.getElementById('chkCanSell').checked; // or other method which read the value
                },
                unapproved      : function ()
                {
                    return document.getElementById('chkUnapproved').checked; // or other method which read the value
                },
                criteriaFilter: function ()
                {
                    return $("#criteria_filter").val(); // or other method which read the value
                },
                criteria      : function ()
                {
                    return $("#txtCriteria").val(); // or other method which read the value
                }
            },
            rowList     : [ 15, 30, 50, 100 ],
            pager       : '#devicesGridPager',
            gridComplete: function ()
            {
                // Get the grid object (cache in variable)
                var grid = $(this);
                var ids = grid.getDataIDs();
                for (var i = 0; i < ids.length; i++)
                {
                    var row = grid.getRowData(ids[i]);
                    row.modelName = row.displayname + ' ' + row.modelName;
                    if (row.oemSku != "")
                    {
                        row.action = '<input style="width:120px;" title="Edit Printer" class="btn btn-mini btn-warning" type="button" value="Edit" onclick="javascript: showMasterDeviceManagementModal(' + row.id + ',0, \'' + 'true' + '\');" />';
                    }
                    else
                    {
                        row.action = '<input style="width:120px;" title="Edit Printer" class="btn btn-mini btn-warning" type="button" value="Edit" onclick="javascript: showMasterDeviceManagementModal(' + row.id + ',0, \'' + 'true' + '\');" />';
//                        row.action = '<a href= "/quotegen/devicesetup/edit/id/' + row.id + '"><input style="width:120px;" title="Edit Printer" class="btn btn-mini btn-warning" type="button" value="Edit" onclick="javascript: showMasterDeviceManagementModal(\' + row.id + \');" /></a>';
                    }
                    grid.setRowData(ids[i], row);
                }
            }
        })


});
function updateGrid($type)
{
    if ($type === "search")
    {
        $("#devicesGrid").trigger("reloadGrid");
    }
    else
    {
        clearFilters();
    }
};
function clearFilters()
{
    $("#chkCanSell").removeAttr("checked");
    $("#chkUnapproved").removeAttr("checked");
    $("#txtCriteria").val("");
    updateGrid('search');
}
$("#masterDeviceManagement").bind("saveSuccess", function (e, myName, myValue)
{
    $("#devicesGrid").trigger("reloadGrid");
});
$("#masterDeviceManagement").bind("approveSuccess", function (e, masterDeviceId)
{
    $("#manageMasterDeviceModal").modal("hide");
});