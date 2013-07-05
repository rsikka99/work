function loadAvailableOptions()
{
    /**
     * Available Options
     */
    $('#availableOptions').jqGrid(
        {
            url         : TMTW_BASEURL + "proposalgen/managedevices/option-list?masterDeviceId=" + masterDeviceId,
            datatype    : 'json',
            colModel    : [
                {
                    width : 30,
                    name  : 'id',
                    index : 'id',
                    label : 'Id',
                    hidden: true
                },
                {
                    width   : 80,
                    name    : 'oemSku',
                    index   : 'oemSku',
                    label   : 'OEM Sku',
                    sortable: true,
                    editable: true
                },
                {
                    width   : 100,
                    name    : 'dealerSku',
                    index   : 'dealerSku',
                    label   : 'Dealer Sku',
                    sortable: true,
                    editable: true
                },
                {
                    width   : 250,
                    name    : 'name',
                    index   : 'name',
                    label   : 'Option',
                    editable: true
                },
                {
                    width   : 280,
                    name    : 'description',
                    index   : 'description',
                    label   : 'Description',
                    editable: true
                },
                {
                    width   : 90,
                    name    : 'cost',
                    index   : 'cost',
                    label   : 'Price',
                    editable: true
                },
                {
                    width : 50,
                    name  : 'assigned',
                    index : 'assigned',
                    label : 'isAssigned',
                    hidden: true
                },
                {
                    width: 80,
                    name : 'action',
                    index: 'action',
                    label: 'Action',
                    align: 'center'
                }
            ],
            jsonReader  : {
                repeatitems: false
            },
            height      : 'auto',
            rowNum      : 15,
            rowList     : [ 15, 30, 50, 100 ],
            pager       : '#availableOptions_Pager',
            toppager    : true,
            postData    : {
                criteriaFilter: function ()
                {
                    return $("#optionFilterList").val();
                },
                criteria      : function ()
                {
                    return $("#optionCriteria").val();
                }
            },
            gridComplete: function ()
            {
                var grid = $(this).jqGrid();
                var ids = grid.getDataIDs();

                for (var i = 0; i < ids.length; i++)
                {
                    var currentRowId = ids[i];
                    var currentRow = grid.getRowData(currentRowId);
                    if (currentRow.assigned == 1)
                    {
                        currentRow.action = '<input style="width 10px;" type="button" name="btnAssignOption' + currentRowId + '" id="btnAssignOption' + currentRowId + '" value="' + 'Unassign' + '" class="btn btn-danger"onclick="javascript: assignOption(' + currentRowId + ');" />';
                    }
                    else
                    {
                        currentRow.action = '<input style="width 10px;" type="button" name="btnAssignOption' + currentRowId + '" id="btnAssignOption' + currentRowId + '" value="' + 'Assign' + '" class="btn btn-success"onclick="javascript: assignOption(' + currentRowId + ');" />';
                    }
                    grid.setRowData(currentRowId, currentRow);
                }
            }
        });
    // Hide the top paging!
    $('#availableOptions_toppager_center').hide();
    //Add to the top pager the create, edit and delete buttons
    jQuery("#availableOptions")
        .navGrid('#availableOptions_toppager', {edit: false, add: false, del: false, search: false, refresh: false})
        //Create New
        .navButtonAdd('#availableOptions_toppager', {
            caption      : "Create New",
            buttonicon   : "ui-icon-plus",
            onClickButton: function ()
            {
                clearForm("availableOptionsForm");
                $('#availableOptionsTitle').html("Add New Option");
                $('#availableOptionsModal').modal('show');
            },
            position     : "last"
        })
        //Edit
        .navButtonAdd('#availableOptions_toppager', {
            caption      : "Edit",
            buttonicon   : "ui-icon-pencil",
            onClickButton: function ()
            {
                $('#availableOptionsId').val(jQuery("#availableOptions").jqGrid('getGridParam', 'selrow'));
                var rowdata = $("#availableOptions").jqGrid('getGridParam', 'selrow');
                if (rowdata)
                {
                    var data = $("#availableOptions").jqGrid('getRowData', rowdata);
                    clearForm("availableOptionsForm");
                    populateForm("availableOptions", data);
                    $('#availableOptionsTitle').html("Edit Option");
                    $('#availableOptionsModal').modal('show');
                }
                else
                {
                    $("#alertMessageModal").modal().show()
                }
            },
            position     : "last"
        })
        //Delete
        .navButtonAdd('#availableOptions_toppager', {
            caption      : "Delete",
            buttonicon   : "ui-icon-trash",
            onClickButton: function ()
            {
                var selectedRow = jQuery("#availableOptions").jqGrid('getGridParam', 'selrow');
                if (selectedRow)
                {
                    $('#deleteId').val(selectedRow);
                    $('#deleteFormName').val('availableOptions');
                    $('#deleteModal').modal('show');
                }
                else
                {
                    $("#alertMessageModal").modal().show()
                }
            },
            position     : "last"
        });
};

// Hide the top paging!
function assignOption(optionId)
{
    var assignOptionButton = $("#btnAssignOption" + optionId);
    $.ajax({
        url     : TMTW_BASEURL + "proposalgen/managedevices/assign-available-option",
        type    : "post",
        dataType: "json",
        data    : {optionId: optionId, masterDeviceId: masterDeviceId},
        success : function ()
        {
            if (assignOptionButton.val() === "Assign")
            {
                assignOptionButton.removeClass("btn-success").addClass("btn-danger");
                assignOptionButton.val("Unassign");
            }
            else
            {
                assignOptionButton.removeClass("btn-danger").addClass("btn-success");
                assignOptionButton.val("Assign");
            }
        },
        error   : function ()
        {
        }
    });

};
function reloadAvailableOptions()
{
    jQuery("#availableOptions").jqGrid().trigger("reloadGrid");
};
function clearAvailableOptions()
{
    $("#optionFilterList").val('oemSku');
    $("#optionCriteria").val('');
    jQuery("#availableOptions").jqGrid().trigger("reloadGrid");
};