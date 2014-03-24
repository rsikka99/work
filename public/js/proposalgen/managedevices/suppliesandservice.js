// When createMode is true, we need to store the assigned toners in this list because we don't add to the database until everything is valid

var tonersList = [];
//$('#grid1').jqGrid('clearGridData');
function loadSuppliesAndService()
{
    // We need to reset the array in case they had toners on the previously selected master device
    tonersList = [];

    var assignedTonersUrl = TMTW_BASEURL + 'proposalgen/managedevices/assigned-toner-list?masterDeviceId=' + masterDeviceId + "&tonersList=" + tonersList.join(",") + "&firstLoad=true";
    /**
     * Assigned Toners Grid
     */
    $('#assignedToners').jqGrid(
        {
            url         : assignedTonersUrl,
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
                    width : 30,
                    name  : 'isSystemDevice',
                    index : 'isSystemDevice',
                    label : 'isSystemDevice',
                    hidden: true
                },
                {
                    width : 30,
                    name  : 'deviceTonersIsSystemDevice',
                    index : 'deviceTonersIsSystemDevice',
                    label : 'deviceTonersIsSystemDevice',
                    hidden: true
                },
                {
                    width : 40,
                    name  : 'tonerColorId',
                    index : 'tonerColorId',
                    label : 'Color',
                    hidden: true
                },
                {
                    width   : 70,
                    name    : 'tonerColorIdModified',
                    index   : 'tonerColorId',
                    label   : 'Color',
                    sortable: true
                },
                {
                    width : 80,
                    name  : 'dealerSku',
                    index : 'dealerSku',
                    label : 'dealerSku',
                    hidden: true
                },
                {
                    width : 80,
                    name  : 'systemSku',
                    index : 'systemSku',
                    label : 'systemSku',
                    hidden: true
                },
                {
                    width   : 80,
                    name    : 'skuModified',
                    index   : 'dealerSku',
                    label   : '(' + dealerSkuName + ')<br/>OEM SKU',
                    sortable: true
                },
                {
                    width   : 233,
                    name    : 'manufacturer',
                    index   : 'manufacturer',
                    label   : 'Manufacturer',
                    sortable: true
                },
                {
                    width : 55,
                    name  : 'manufacturerId',
                    index : 'manufacturerId',
                    label : 'ManufacturerId',
                    hidden: true
                },
                {
                    width: 250,
                    name : 'device_list',
                    index: 'device_list',
                    label: 'Machine Compatibility'
                },
                {
                    width   : 60,
                    name    : 'yield',
                    index   : 'yield',
                    label   : 'Yield',
                    align   : 'right',
                    sortable: true
                },
                {
                    width : 120,
                    name  : 'dealerCost',
                    index : 'dealerCost',
                    label : 'dealerCost',
                    align : 'right',
                    hidden: true
                },
                {
                    width : 120,
                    name  : 'systemCost',
                    index : 'systemCost',
                    label : 'systemCost',
                    align : 'right',
                    hidden: true
                },
                {
                    width: 100,
                    name : 'costModified',
                    index: 'dealerCost',
                    label: 'Cost<br/>(System Cost)',
                    align: 'right'
                },
                {
                    width   : 80,
                    name    : 'action',
                    index   : 'action',
                    label   : 'Action',
                    align   : 'center',
                    sortable: false
                }
            ],
            jsonReader  : {
                repeatitems: false
            },
            height      : 'auto',
            toppager    : true,
            rowNum      : 50,
            gridComplete: function ()
            {
                tonersList = [];
                var grid = $(this).jqGrid();
                var ids = grid.getDataIDs();

                for (var i = 0; i < ids.length; i++)
                {
                    var currentRowId = ids[i];
                    var currentRow = grid.getRowData(currentRowId);

                    tonersList.push(currentRowId);
                    if (currentRow.dealerCost == '')
                    {
                        currentRow.costModified = "NA" + "</br><small>$" + currentRow.systemCost + "</small>";
                    }
                    else
                    {
                        currentRow.costModified = "$" + currentRow.dealerCost + "</br><small>$" + currentRow.systemCost + "</small>";
                    }

                    if (currentRow.dealerSku == '')
                    {
                        currentRow.skuModified = "NA" + "</br>" + currentRow.systemSku;
                    }
                    else
                    {
                        currentRow.skuModified = currentRow.dealerSku + "</br>" + currentRow.systemSku;
                    }

                    if (isAllowed || currentRow.isSystemDevice == false || currentRow.deviceTonersIsSystemDevice == false)
                    {
                        currentRow.action = '<input style="width 10px;" type="button" name="btnRemove' + currentRowId + '" id="btnRemove' + currentRowId + '" tag="Remove" value="' + 'Unassign' + '" class="btn"onclick="javascript: removeToner(' + currentRowId + ');" />';
                    }

                    //Change tonerColorId to images section
                    var image = '/img/tonerColor/Black.png';
                    var label = 'Black';

                    switch (currentRow.tonerColorId)
                    {
                        case "1":
                            image = '/img/tonercolors/Black.png';
                            label = "Black";
                            break;
                        case "2":
                            image = '/img/tonercolors/Cyan.png';
                            label = "Cyan";
                            break;
                        case "3":
                            image = '/img/tonercolors/Magenta.png';
                            label = "Magenta";
                            break;
                        case "4"://Yellow
                            image = '/img/tonercolors/Yellow.png';
                            label = "Yellow";
                            break;
                        case "5":
                            image = '/img/tonercolors/3color.png';
                            label = "3 Color";
                            break;
                        case "6":
                            image = '/img/tonercolors/4color.png';
                            label = "4 Color";
                            break;
                    }

                    /**
                     * This is the number of Devices to list before the view all toggle takes into effect
                     * @type {number}
                     */
                    var max = 1;

                    /**
                     * This is the final container that everything will be in
                     * @type {string}
                     */
                    var deviceListCollapsibleContainer = '<div id="assignedToners_outer_' + ids[i] + '" style="text-align: left; width: 200px;">';
                    var compatibleDevices = currentRow.device_list.split(";,");

                    // Loop through each device and add it to the container
                    for (var j = 0; j < compatibleDevices.length; j++)
                    {
                        var device = compatibleDevices[j];
                        if (j == max)
                        {
                            deviceListCollapsibleContainer += '<div id="assignedToners_inner_' + ids[i] + '" style="display: none;">';
                        }

                        deviceListCollapsibleContainer += device + '<br />';
                    }

                    if (compatibleDevices.length > max)
                    {
                        deviceListCollapsibleContainer += '</div>';
                        deviceListCollapsibleContainer += '<a id="assignedToners_view_link_' + ids[i] + '" href="javascript: void(0);" class="blue_link" onclick="javascript: view_device_list(\'assignedToners\',' + ids[i] + ');">View All...</a>';
                    }

                    currentRow.device_list = deviceListCollapsibleContainer;
                    currentRow.tonerColorIdModified = "<div style='text-align: center;padding-top:5px;'><img src=" + image + " width='24' /><br/>" + label + "</div>";

                    grid.setRowData(currentRowId, currentRow);

                }
            }
        });

    // Hide the top paging!
    $('#assignedToners_toppager_center').hide();

    var assignedTonersjQuery = jQuery("#assignedToners");
    assignedTonersjQuery.navGrid('#assignedToners_toppager', {edit: false, add: false, del: false, search: false, refresh: false});

    //Create New
    assignedTonersjQuery.navButtonAdd('#assignedToners_toppager', {
        caption      : "Create New",
        buttonicon   : "ui-icon-plus",
        onClickButton: function ()
        {
            clearForm("availableTonersForm");
            $('#availableTonersTitle').html("Add New Toner");
            var availableTonersDealerCost = document.getElementById("availableTonersdealerCost");
            var systemCostLabel = $("label[for='availableTonerssystemCost']");

            $("#availableTonerssaveAndApprove").closest("div.control-group").attr('style', 'display:none');
            $("#availableTonersFormBtn").attr('onClick', "javascript: createOrEdit('availableTonersForm',true);");
            if (!isSaveAndApproveAdmin)
            {
                availableTonersDealerCost.parentNode.parentNode.setAttribute("style", "display:none");
                systemCostLabel.text("Cost");
            }
            else
            {
                document.getElementById("availableTonersdealerCost").parentNode.parentNode.setAttribute("style", "display:inline-block");
                systemCostLabel.text("System Cost");
            }

            document.getElementById("availableTonersmanufacturerId").removeAttribute("disabled");
            document.getElementById("availableTonerstonerColorId").removeAttribute("disabled");
            document.getElementById("availableTonerssystemSku").removeAttribute("readonly");
            document.getElementById("availableTonersyield").removeAttribute("readonly");
            document.getElementById("availableTonerssystemCost").removeAttribute("readonly");

            $('#availableTonersModal').modal('show');
        },
        position     : "last"
    });

    //Edit
    assignedTonersjQuery.navButtonAdd('#assignedToners_toppager', {
        caption      : "Edit",
        buttonicon   : "ui-icon-pencil",
        onClickButton: function ()
        {
            $('#availableTonersId').val(jQuery("#assignedToners").jqGrid('getGridParam', 'selrow'));
            var rowdata = $("#assignedToners").jqGrid('getGridParam', 'selrow');

            if (rowdata)
            {
                var data = $("#assignedToners").jqGrid('getRowData', rowdata);
                clearForm("availableTonersForm");
                populateForm("availableToners", data);
                $('#availableTonersTitle').html("Edit Toner");
                document.getElementById("availableTonersdealerCost").parentNode.parentNode.setAttribute("style", "display:inline-block");
                var systemCostLabel = $("label[for='availableTonerssystemCost']");
                systemCostLabel.text("System Cost");

                // If it is not a system device or we are an admin
                if (data['isSystemDevice'] == 0 || isSaveAndApproveAdmin)
                {
                    document.getElementById("availableTonersmanufacturerId").removeAttribute("disabled");
                    document.getElementById("availableTonerstonerColorId").removeAttribute("disabled");
                    document.getElementById("availableTonerssystemSku").removeAttribute("readonly");
                    document.getElementById("availableTonersyield").removeAttribute("readonly");
                    document.getElementById("availableTonerssystemCost").removeAttribute("readonly");
                }
                else
                {
                    document.getElementById("availableTonersmanufacturerId").setAttribute("disabled", "disabled");
                    document.getElementById("availableTonerstonerColorId").setAttribute("disabled", "disabled");
                    document.getElementById("availableTonerssystemSku").setAttribute("readonly", "readonly");
                    document.getElementById("availableTonersyield").setAttribute("readonly", "readonly");
                    document.getElementById("availableTonerssystemCost").setAttribute("readonly", "readonly");
                }

                if ((data['isSystemDevice'] == 0 || data['deviceTonersIsSystemDevice'] == 0) && isSaveAndApproveAdmin)
                {
                    $("#availableTonerssaveAndApprove").closest("div.control-group").attr('style', 'display:block');
                }
                else
                {
                    $("#availableTonerssaveAndApprove").closest("div.control-group").attr('style', 'display:none')
                }

                $('#availableTonersModal').modal('show');
            }
            else
            {
                $("#alertMessageModal").modal().show()
            }
        },
        position     : "last"
    });

    //Delete
    assignedTonersjQuery.navButtonAdd('#assignedToners_toppager', {
        caption      : "Delete",
        buttonicon   : "ui-icon-trash",
        onClickButton: function ()
        {
            var selectedRow = jQuery("#assignedToners").jqGrid('getGridParam', 'selrow');

            if (selectedRow)
            {
                var data = $("#assignedToners").jqGrid('getRowData', selectedRow);
                // If it is not a system device or we are an admin
                if (data['isSystemDevice'] == 0 || isSaveAndApproveAdmin)
                {
                    $.ajax({
                        url     : TMTW_BASEURL + "proposalgen/admin/devicetonercount?tonerid=" + selectedRow,
                        type    : "post",
                        dataType: "json",
                        success : function (result)
                        {
                            // Store the tonerId and colorId of the toner we are deleting
                            $('#deleteId').val(selectedRow);
                            $('#deleteColorId').val(data.tonerColorId);

                            // If we do not need to replace anything!
                            if (result.total_count <= 0)
                            {

                                $('#deleteFormName').val('availableToners');
                                $('#deleteModal').modal('show');
                            }
                            else
                            {
                                $("#replacementTonersTotalCount").val(result.total_count);
                                $("#replacementTonersDeviceCount").val(result.device_count);
                                // We need to replace stuff =(
                                $("#affectedDevicesText").html("The " + result.total_count + " devices shown below will be affected by the deletion");
                                if (result.device_count > 0)
                                {
                                    $("#selectReplacementText").html("Select Required Replacement Toner");
                                }
                                else
                                {
                                    $("#selectReplacementText").html("Select Optional Replacement Toner");
                                }

                                $("#affectedReplacementToners").jqGrid().setGridParam({url: TMTW_BASEURL + 'proposalgen/managedevices/affected-replacement-toners-list?masterDeviceId=' + masterDeviceId + "&tonerId=" + selectedRow});
                                $("#replacementToners").jqGrid().setGridParam({url: TMTW_BASEURL + 'proposalgen/managedevices/available-toners-list?masterDeviceId=' + masterDeviceId + "&tonersList=" + tonersList.join(",") + "&tonerColorId=" + $("#deleteColorId").val()});

                                reloadReplacementTonersGrids();
                                showReplacementTonersModal();
                            }
                        }
                    });
                }
                else
                {
                    $("#alertCannotDelete").modal().show();
                }
            }
            else
            {
                $("#alertMessageModal").modal().show()
            }
        },
        position     : "last"
    });


    /**
     * Available Toners Grid
     */
    $('#availableToners').jqGrid(
        {
            url         : TMTW_BASEURL + 'proposalgen/managedevices/available-toners-list',
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
                    width : 30,
                    name  : 'isSystemDevice',
                    index : 'isSystemDevice',
                    label : 'isSystemDevice',
                    hidden: true
                },
                {
                    width : 30,
                    name  : 'deviceTonersIsSystemDevice',
                    index : 'deviceTonersIsSystemDevice',
                    label : 'deviceTonersIsSystemDevice',
                    hidden: true
                },
                {
                    width : 40,
                    name  : 'tonerColorId',
                    index : 'tonerColorId',
                    label : 'Color',
                    hidden: true
                },
                {
                    width   : 70,
                    name    : 'tonerColorIdModified',
                    index   : 'tonerColorId',
                    label   : 'Color',
                    sortable: true
                },
                {
                    width : 80,
                    name  : 'dealerSku',
                    index : 'dealerSku',
                    label : 'dealerSku',
                    hidden: true
                },
                {
                    width : 80,
                    name  : 'systemSku',
                    index : 'systemSku',
                    label : 'systemSku',
                    hidden: true
                },
                {
                    width: 80,
                    name : 'skuModified',
                    index: 'dealerSku',
                    label: '(' + dealerSkuName + ')<br/>OEM SKU'
                },
                {
                    width   : 213,
                    name    : 'manufacturer',
                    index   : 'manufacturer',
                    label   : 'Manufacturer',
                    hidden  : false,
                    sortable: true
                },
                {
                    width : 55,
                    name  : 'manufacturerId',
                    index : 'manufacturerId',
                    label : 'ManufacturerId',
                    hidden: true
                },
                {
                    width: 250,
                    name : 'device_list',
                    index: 'device_list',
                    label: 'Machine Compatibility'
                },
                {
                    width   : 60,
                    name    : 'yield',
                    index   : 'yield',
                    label   : 'Yield',
                    align   : 'right',
                    sortable: true
                },
                {
                    width : 120,
                    name  : 'dealerCost',
                    index : 'dealerCost',
                    label : 'dealerCost',
                    align : 'right',
                    hidden: true
                },
                {
                    width : 120,
                    name  : 'systemCost',
                    index : 'systemCost',
                    label : 'systemCost',
                    align : 'right',
                    hidden: true
                },
                {
                    width   : 100,
                    name    : 'costModified',
                    index   : 'dealerCost',
                    label   : 'Cost<br/>(System Cost)',
                    align   : 'right',
                    sortable: true
                },
                {
                    width   : 80,
                    name    : 'action',
                    index   : 'action',
                    label   : 'Action',
                    align   : 'center',
                    sortable: false
                }
            ],
            jsonReader  : {
                repeatitems: false
            },
            postData    : {
                filter            : function ()
                {
                    return $("#availableTonersFilter").val();
                },
                criteria          : function ()
                {
                    if ($("#availableTonersFilter").val() === 'tonerColorId')
                    {
                        return $('#availableTonersCriteriaList').val();
                    }
                    else
                    {
                        return $('#availableTonersCriteria').val();
                    }
                },
                manufacturerId    : function ()
                {
                    return $("#availableTonersSelectManufacturer").val();
                },
                tonerColorConfigId: function ()
                {
                    return $("#tonerConfigId").val();
                },
                tonersList        : function ()
                {
                    return tonersList.join(",");
                },
                masterDeviceId    : function ()
                {
                    return masterDeviceId;
                }
            },
            height      : 'auto',
            rowNum      : 10,
            rowList     : [ 10, 20, 30, 50 ],
            pager       : '#availableToners_Pager',
            toppager    : true,
            gridComplete: function ()
            {
                var grid = $(this).jqGrid();
                var ids = grid.getDataIDs();

                for (var i = 0; i < ids.length; i++)
                {
                    var currentRowId = ids[i];
                    var currentRow = grid.getRowData(currentRowId);
//                    //Change cost to include system
                    if (currentRow.dealerCost == '')
                    {
                        currentRow.costModified = "NA" + "</br><small>$" + currentRow.systemCost + "</small>";
                    }
                    else
                    {
                        currentRow.costModified = "$" + currentRow.dealerCost + "</br><small>$" + currentRow.systemCost + "</small>";
                    }
                    if (currentRow.dealerSku == '')
                    {
                        currentRow.skuModified = "NA" + "</br>" + currentRow.systemSku;
                    }
                    else
                    {
                        currentRow.skuModified = currentRow.dealerSku + "</br>" + currentRow.systemSku;
                    }
                    if (jQuery.inArray(currentRowId + "", tonersList) != -1)
                    {
                        currentRow.action = '<input style="width 10px;" type="button" name="btnAssign' + currentRowId + '" id="btnRemove' + currentRowId + '" value="' + 'Assigned' + '" class="btn" disabled="disabled" />';
                    }
                    else
                    {
                        currentRow.action = '<input style="width 10px;" type="button" name="btnAssign' + currentRowId + '" id="btnRemove' + currentRowId + '" value="' + 'Assign' + '" class="btn"onclick="javascript: assignToner(' + currentRowId + ');" />';
                    }

                    //Change tonerColorId to images section
                    var image = '/img/tonerColor/Black.png';
                    var label = 'Black';
                    switch (currentRow.tonerColorId)
                    {
                        case "1":
                            image = '/img/tonercolors/Black.png';
                            label = "Black";
                            break;
                        case "2":
                            image = '/img/tonercolors/Cyan.png';
                            label = "Cyan";
                            break;
                        case "3":
                            image = '/img/tonercolors/Magenta.png';
                            label = "Magenta";
                            break;
                        case "4"://Yellow
                            image = '/img/tonercolors/Yellow.png';
                            label = "Yellow";
                            break;
                        case "5":
                            image = '/img/tonercolors/3color.png';
                            label = "3 Color";
                            break;
                        case "6":
                            image = '/img/tonercolors/4color.png';
                            label = "4 Color";
                            break;
                    }
                    /**
                     * This is the number of Devices to list before the view all toggle takes into effect
                     * @type {number}
                     */
                    var max = 1;

                    /**
                     * This is the final container that everything will be in
                     * @type {string}
                     */
                    var deviceListCollapsibleContainer = '<div id="availableToners_outer_' + ids[i] + '" style="text-align: left; width: 200px;">';
                    var compatibleDevices = currentRow.device_list.split(";,");

                    // Loop through each device and add it to the container
                    for (var j = 0; j < compatibleDevices.length; j++)
                    {
                        var device = compatibleDevices[j];
                        if (j == max)
                        {
                            deviceListCollapsibleContainer += '<div id="availableToners_inner_' + ids[i] + '" style="display: none;">';
                        }

                        deviceListCollapsibleContainer += device + '<br />';
                    }

                    if (compatibleDevices.length > max)
                    {
                        deviceListCollapsibleContainer += '</div>';
                        deviceListCollapsibleContainer += '<a id="availableToners_view_link_' + ids[i] + '" href="javascript: void(0);" class="blue_link" onclick="javascript: view_device_list(\'availableToners\',' + ids[i] + ');">View All...</a>';
                    }

                    currentRow.device_list = deviceListCollapsibleContainer;
                    currentRow.tonerColorIdModified = "<div style='text-align: center;padding-top:5px;'><img src=" + image + " width='24' /><br/>" + label + "</div>";
                    grid.setRowData(currentRowId, currentRow);
                }
            }
        }
    );

    // Hide the top paging!
    $('#availableToners_toppager_center').hide();
    //Add to the top pager the create, edit and delete buttons
    var availableToners = jQuery("#availableToners");
    availableToners
        .navGrid('#availableToners_toppager', {edit: false, add: false, del: false, search: false, refresh: false});

    //Create New
    availableToners.navButtonAdd('#availableToners_toppager', {
        caption      : "Create New",
        buttonicon   : "ui-icon-plus",
        onClickButton: function ()
        {
            clearForm("availableTonersForm");
            $('#availableTonersTitle').html("Add New Toner");
            var availableTonersDealerCost = document.getElementById("availableTonersdealerCost");
            var systemCostLabel = $("label[for='availableTonerssystemCost']");

            $("#availableTonersFormBtn").attr('onClick', "javascript: createOrEdit('availableTonersForm',false);");
            $("#availableTonerssaveAndApprove").closest("div.control-group").attr('style', 'display:none');

            if (!isSaveAndApproveAdmin)
            {
                availableTonersDealerCost.parentNode.parentNode.setAttribute("style", "display:none");
                systemCostLabel.text("Cost");
            }
            else
            {
                document.getElementById("availableTonersdealerCost").parentNode.parentNode.setAttribute("style", "display:inline-block");
                systemCostLabel.text("System Cost");
            }

            document.getElementById("availableTonersmanufacturerId").removeAttribute("disabled");
            document.getElementById("availableTonerstonerColorId").removeAttribute("disabled");
            document.getElementById("availableTonerssystemSku").removeAttribute("readonly");
            document.getElementById("availableTonersyield").removeAttribute("readonly");
            document.getElementById("availableTonerssystemCost").removeAttribute("readonly");

            $('#availableTonersModal').modal('show');
        },
        position     : "last"
    });

    //Edit
    availableToners.navButtonAdd('#availableToners_toppager', {
        caption      : "Edit",
        buttonicon   : "ui-icon-pencil",
        onClickButton: function ()
        {
            $('#availableTonersId').val(jQuery("#availableToners").jqGrid('getGridParam', 'selrow'));
            var rowdata = $("#availableToners").jqGrid('getGridParam', 'selrow');

            if (rowdata)
            {
                var data = $("#availableToners").jqGrid('getRowData', rowdata);
                clearForm("availableTonersForm");
                populateForm("availableToners", data);
                $('#availableTonersTitle').html("Edit Toner");
                document.getElementById("availableTonersdealerCost").parentNode.parentNode.setAttribute("style", "display:inline-block");
                var systemCostLabel = $("label[for='availableTonerssystemCost']");
                systemCostLabel.text("System Cost");

                if (data['isSystemDevice'] == 0 && isSaveAndApproveAdmin)
                {
                    $("#availableTonerssaveAndApprove").closest("div.control-group").attr('style', 'display:block');
                }
                else
                {
                    $("#availableTonerssaveAndApprove").closest("div.control-group").attr('style', 'display:none');
                }

                // If it is not a system device or we are an admin
                if (data['isSystemDevice'] == 0 || isSaveAndApproveAdmin)
                {
                    document.getElementById("availableTonersmanufacturerId").removeAttribute("disabled");
                    document.getElementById("availableTonerstonerColorId").removeAttribute("disabled");
                    document.getElementById("availableTonerssystemSku").removeAttribute("readonly");
                    document.getElementById("availableTonersyield").removeAttribute("readonly");
                    document.getElementById("availableTonerssystemCost").removeAttribute("readonly");
                }
                else
                {
                    document.getElementById("availableTonersmanufacturerId").setAttribute("disabled", "disabled");
                    document.getElementById("availableTonerstonerColorId").setAttribute("disabled", "disabled");
                    document.getElementById("availableTonerssystemSku").setAttribute("readonly", "readonly");
                    document.getElementById("availableTonersyield").setAttribute("readonly", "readonly");
                    document.getElementById("availableTonerssystemCost").setAttribute("readonly", "readonly");
                }

                $('#availableTonersModal').modal('show');
            }
            else
            {
                $("#alertMessageModal").modal().show()
            }
        },
        position     : "last"
    });

    //Delete
    availableToners.navButtonAdd('#availableToners_toppager', {
        caption      : "Delete",
        buttonicon   : "ui-icon-trash",
        onClickButton: function ()
        {
            var selectedRow = jQuery("#availableToners").jqGrid('getGridParam', 'selrow');
            if (selectedRow)
            {
                var data = $("#availableToners").jqGrid('getRowData', selectedRow);

                // If it is not a system device or we are an admin
                if (data['isSystemDevice'] == 0 || isSaveAndApproveAdmin)
                {


                    $.ajax({
                        url     : TMTW_BASEURL + "proposalgen/admin/devicetonercount?tonerid=" + selectedRow,
                        type    : "post",
                        dataType: "json",
                        success : function (result)
                        {
                            // Store the tonerId and colorId of the toner we are deleting
                            $('#deleteId').val(selectedRow);

                            $('#deleteColorId').val(data.tonerColorId);
                            // If we do not need to replace anything!
                            if (result.total_count <= 0)
                            {
                                $('#deleteFormName').val('availableToners');
                                $('#deleteModal').modal('show');
                            }
                            else
                            {
                                $("#replacementTonersTotalCount").val(result.total_count);
                                $("#replacementTonersDeviceCount").val(result.device_count);
                                // We need to replace stuff =(
                                $("#affectedDevicesText").html("The " + result.total_count + " devices shown below will be affected by the deletion");

                                if (result.device_count > 0)
                                {
                                    $("#selectReplacementText").html("Select Required Replacement Toner");
                                }
                                else
                                {
                                    $("#selectReplacementText").html("Select Optional Replacement Toner");
                                }

                                $("#affectedReplacementToners").jqGrid().setGridParam({url: TMTW_BASEURL + 'proposalgen/managedevices/affected-replacement-toners-list?masterDeviceId=' + masterDeviceId + "&tonerId=" + selectedRow});
                                $("#replacementToners").jqGrid().setGridParam({url: TMTW_BASEURL + 'proposalgen/managedevices/available-toners-list?masterDeviceId=' + masterDeviceId + "&tonersList=" + tonersList.join(",") + "&tonerColorId=" + $("#deleteColorId").val()});

                                reloadReplacementTonersGrids();
                                showReplacementTonersModal();
                            }


                        },
                        error   : function ()
                        {

                        }
                    });
                }
                else
                {
                    $("#alertCannotDelete").modal().show();
                }
            }
            else
            {
                $("#alertMessageModal").modal().show()
            }
        },
        position     : "last"
    });


    loadReplacementGrids();
    var availableTonersSelectManufacturer = $("#availableTonersSelectManufacturer");
    /**
     * Available Toners Manufacturer search
     */
    availableTonersSelectManufacturer.select2({
        placeholder       : "Search for manufacturer",
        minimumInputLength: 1,
        ajax              : { // instead of writing the function to execute the request we use Select2's convenient helper
            url     : TMTW_BASEURL + 'proposalgen/managedevices/search-for-manufacturer',
            dataType: 'json',
            data    : function (term, page)
            {
                return {
                    manufacturerName: term, // search term
                    page_limit      : 10
                };
            },
            results : function (data, page)
            { // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to alter remote JSON data
                return {results: data};
            }
        },

        dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
        escapeMarkup    : function (m)
        {
            return m;
        } // we do not want to escape markup since we are displaying html in results
    });

    availableTonersSelectManufacturer.on("change", function (e)
    {
        reloadTonersGrids();
    });

    var leasedTonerYield = document.getElementById("leasedTonerYield");
    var leasedTonerControlGroupElement = leasedTonerYield.parentNode.parentNode;

    if (!document.getElementById("isLeased").checked)
    {
        leasedTonerControlGroupElement.setAttribute("style", "display:none");
    }

    $("#isLeased").on("change", function ()
    {
        if (document.getElementById("isLeased").checked)
        {
            leasedTonerControlGroupElement.setAttribute("style", "display:block");
        }
        else
        {
            leasedTonerControlGroupElement.setAttribute("style", "display:none");
        }
    });

    var availableTonersFilter = $("#availableTonersFilter");
    var availableTonersCriteria = $("#availableTonersCriteria");
    var availableTonersCriteriaList = $("#availableTonersCriteriaList");

    availableTonersFilter.change(function ()
    {
        if (availableTonersFilter.val() == 'tonerColorId')
        {
            availableTonersCriteriaList.css({"display": 'initial'});
            availableTonersCriteria.css({"display": 'none'});
        }
        else if (availableTonersFilter.val() == 'isSystemDevice')
        {
            availableTonersCriteriaList.css({"display": 'none'});
            availableTonersCriteria.css({"display": 'none'});
        }
        else
        {
            availableTonersCriteriaList.css({"display": 'none'});
            availableTonersCriteria.css({"display": 'initial'});
        }
    });

    $("#availableTonersForm").bind("createTonerSuccess", function (e, tonerId)
    {
        assignToner(tonerId);
    });
}
/**
 * Adds a toner to tonersList
 * This is used when creating a new Master Device
 * @param tonerId
 */
function assignToner(tonerId)
{
    tonersList.push(tonerId);
    jQuery("#assignedToners").jqGrid().setGridParam({url: TMTW_BASEURL + 'proposalgen/managedevices/assigned-toner-list?masterDeviceId=' + masterDeviceId + "&tonersList=" + tonersList.join(",")});
    reloadTonersGrids();

}
/**
 * Removes a toner from tonersList
 * This is used when creating a new Master Device
 * @param tonerId
 */
function removeToner(tonerId)
{
    var index = jQuery.inArray(tonerId + "", tonersList);
    tonersList.splice(index, 1);
    jQuery("#assignedToners").jqGrid().setGridParam({url: TMTW_BASEURL + 'proposalgen/managedevices/assigned-toner-list?masterDeviceId=' + masterDeviceId + "&tonersList=" + tonersList.join(",")});
    reloadTonersGrids();
}

/**
 * Reloads assigned and unassigned toner jqgrids in Supplies & Service
 */
function reloadTonersGrids()
{
    var assignedToners = jQuery("#assignedToners").jqGrid();
    assignedToners.trigger("reloadGrid");
    var availableTonersGrid = jQuery("#availableToners");
    availableTonersGrid.trigger("reloadGrid");
}

/**
 * Reloads both jqgrids in Supplies & Service
 */
function reloadReplacementTonersGrids()
{
    jQuery("#affectedReplacementToners").jqGrid().trigger("reloadGrid");
    jQuery("#replacementToners").jqGrid().trigger("reloadGrid");
}

/**
 * Handles toggling the collapsed "Machine Compatibility" Column within the jqgrid
 * @param type
 * @param id
 */
function view_device_list(type, id)
{
    if (document.getElementById(type + '_inner_' + id).style.display == 'none')
    {
        document.getElementById(type + '_inner_' + id).style.display = 'block';
        document.getElementById(type + '_view_link_' + id).innerHTML = 'Collapse...';
    }
    else
    {
        document.getElementById(type + '_inner_' + id).style.display = 'none';
        document.getElementById(type + '_view_link_' + id).innerHTML = 'View All...';
    }
}

/**
 * Makes the assign toners modal show
 */
function showAssignTonersModal()
{
    reloadTonersGrids();
    var screenWidth = ($(this).width());
    var assignTonersModal = $('#assignTonersModal');
    assignTonersModal.modal('show');
    assignTonersModal.css('width', 960);
    assignTonersModal.css('left', function ()
    {
        var addedLeft = 280;
        var contentWidth = 960;
        var left = screenWidth - contentWidth;
        if (left < 0)
        {
            left = 0;
        }
        left = left / 2;
        left += addedLeft;
        return left;
    });
}

/**
 * Makes the replacement toners modal show
 */
function showReplacementTonersModal()
{
    var screenWidth = ($(this).width());
    var replacementTonersModal = $('#replacementTonersModal');
    replacementTonersModal.modal('show');
    replacementTonersModal.css('width', 960);
    replacementTonersModal.css('left', function ()
    {
        var addedLeft = 280;
        var contentWidth = 960;
        var left = screenWidth - contentWidth;
        if (left < 0)
        {
            left = 0;
        }
        left = left / 2;
        left += addedLeft;
        return left;
    });
}
/**
 * deletes a toner and replaces it if needed
 */
function replaceToner()
{
    // The total amount of devices using this toner
    var totalCount = $("#replacementTonersTotalCount").val();
    // The amount of devices that use this toner as the last of their color
    var deviceCount = $("#replacementTonersDeviceCount").val();
    // This is the toner we are deleting
    var replaceId = $("#deleteId").val();
    // This is the replacement toner we selected
    var withId = jQuery("#replacementToners").jqGrid('getGridParam', 'selrow');
    // optional_replace is when the the toner isn't the last of its color, we don't need to choose a replacement toner, but you can
    // require_replace is when it's last toner of that color is the one we are deleting, we need a replacement toner
    var replaceMode = ((deviceCount > 0) ? 'require_replace' : 'optional_replace');
    // Apply to all devices using this toner
    var applyAll = document.getElementById("replacementTonersApplyToAll").checked;

    // If we require a replacement device and a replacement is not selected then
    if (!withId && deviceCount > 0)
    {
        $("#alertMessageModal").modal().show()
    }
    else
    {
        $.ajax({
            url     : TMTW_BASEURL + 'proposalgen/admin/replacetoner?replace_mode=' + replaceMode + '&replace_toner_id=' + replaceId + '&with_toner_id=' + withId + '&chkAllToners=' + applyAll,
            type    : "post",
            dataType: "json",
            success : function ()
            {
                $("#replacementTonersModal").modal('hide');
                reloadTonersGrids();
            },
            error   : function (xhr)
            {
            }
        });
    }
}

$(document).on("click", "#availableTonersClearManSearch", function ()
{
    $("#availableTonersSelectManufacturer").select2("data", null);
});

$(document).on("click", "#availableTonersSearch", function ()
{
    reloadTonersGrids();
});

$(document).on("click", "#availableTonersReset", function ()
{
    var availableTonersCriteria = $("#availableTonersCriteria");
    $("#availableTonersFilter").val('dealerSku');
    availableTonersCriteria.val('');
    $("#availableTonersCriteriaList").css({"display": 'none'});
    availableTonersCriteria.css({"display": 'initial'});
    reloadTonersGrids();
});

$(document).on("click", "#replacementTonersClearManSearch", function ()
{
    $("#replacementTonersSelectManufacturer").select2("data", null);
});

$(document).on("click", "#replacementTonersSearch", function ()
{
    reloadReplacementTonersGrids();
});

$(document).on("click", "#replacementTonersReset", function ()
{
    $("#replacementTonersFilter").val('dealerSku');
    $("#replacementTonersCriteria").val('');
    reloadReplacementTonersGrids();
});

function tonerSaveAndApprove()
{
    document.getElementById('availableTonerssaveAndApproveHdn').value = 1;
    createOrEdit('availableTonersForm', false);
    document.getElementById('availableTonerssaveAndApproveHdn').value = 0;
}

function loadReplacementGrids()
{
    /**
     * Affected Toners Grid
     */
    $('#affectedReplacementToners').jqGrid(
        {
            url         : TMTW_BASEURL + 'proposalgen/managedevices/affected-replacement-toners-list?masterDeviceId=' + masterDeviceId + "&tonersList=" + tonersList.join(","),
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
                    width: 250,
                    name : 'deviceName',
                    index: 'deviceName',
                    label: 'Device Name'
                },
                {
                    width   : 80,
                    name    : 'tonerColorId',
                    index   : 'tonerColorId',
                    label   : 'Color',
                    sortable: true,
                    hidden  : true
                },
                {
                    width   : 100,
                    name    : 'tonerColorIdModified',
                    index   : 'tonerColorId',
                    label   : 'Color',
                    sortable: true
                },
                {
                    width : 80,
                    name  : 'dealerSku',
                    index : 'dealerSku',
                    label : 'dealerSku',
                    hidden: true
                },
                {
                    width : 80,
                    name  : 'systemSku',
                    index : 'systemSku',
                    label : 'systemSku',
                    hidden: true
                },
                {
                    width: 300,
                    name : 'skuModified',
                    index: 'sku',
                    label: '(' + dealerSkuName + ')<br/>OEM SKU'
                },
                {
                    width   : 295,
                    name    : 'yield',
                    index   : 'yield',
                    label   : 'Yield',
                    align   : 'right',
                    editable: true
                },
                {
                    width : 120,
                    name  : 'dealerCost',
                    index : 'dealerCost',
                    label : 'dealerCost',
                    align : 'right',
                    hidden: true
                },
                {
                    width : 120,
                    name  : 'systemCost',
                    index : 'systemCost',
                    label : 'systemCost',
                    align : 'right',
                    hidden: true
                },
                {
                    width: 200,
                    name : 'costModified',
                    index: 'cost',
                    label: 'Cost<br/>(System Cost)',
                    align: 'right'
                }
            ],
            grouping    : true,
            groupingView: {
                groupField     : ['deviceName'],
                groupColumnShow: [false],
                groupCollapse  : true
            },
            jsonReader  : {
                repeatitems: false
            },
            height      : 'auto',
            rowNum      : 50,
            gridComplete: function ()
            {
                var grid = $(this).jqGrid();
                var ids = grid.getDataIDs();

                for (var i = 0; i < ids.length; i++)
                {
                    var currentRowId = ids[i];
                    var currentRow = grid.getRowData(currentRowId);
//                    //Change cost to include system
                    if (currentRow.dealerCost == '')
                    {
                        currentRow.costModified = "NA" + "</br><small>$" + currentRow.systemCost + "</small>";
                    }
                    else
                    {
                        currentRow.costModified = "$" + currentRow.dealerCost + "</br><small>$" + currentRow.systemCost + "</small>";
                    }
                    if (currentRow.dealerSku == '')
                    {
                        currentRow.skuModified = "NA" + "</br>" + currentRow.systemSku;
                    }
                    else
                    {
                        currentRow.skuModified = currentRow.dealerSku + "</br>" + currentRow.systemSku;
                    }
                    //Change tonerColorId to images section
                    var image = '/img/tonerColor/Black.png';
                    var label = 'Black';
                    switch (currentRow.tonerColorId)
                    {
                        case "1":
                            image = '/img/tonercolors/Black.png';
                            label = "Black";
                            break;
                        case "2":
                            image = '/img/tonercolors/Cyan.png';
                            label = "Cyan";
                            break;
                        case "3":
                            image = '/img/tonercolors/Magenta.png';
                            label = "Magenta";
                            break;
                        case "4"://Yellow
                            image = '/img/tonercolors/Yellow.png';
                            label = "Yellow";
                            break;
                        case "5":
                            image = '/img/tonercolors/3color.png';
                            label = "3 Color";
                            break;
                        case "6":
                            image = '/img/tonercolors/4color.png';
                            label = "4 Color";
                            break;
                    }
                    currentRow.tonerColorIdModified = "<div style='text-align: center;padding-top:5px;'><img src=" + image + " width='24' /><br/>" + label + "</div>";
                    grid.setRowData(currentRowId, currentRow);
                }

            }
        }
    );

    /**
     * Replacement Toners Grid
     */
    $('#replacementToners').jqGrid(
        {
            url         : TMTW_BASEURL + 'proposalgen/managedevices/available-toners-list?masterDeviceId=' + masterDeviceId + "&tonersList=" + tonersList.join(",") + "&tonerColorId=" + $("#deleteColorId").val(),
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
                    width   : 40,
                    name    : 'tonerColorId',
                    index   : 'tonerColorId',
                    label   : 'Color',
                    sortable: true,
                    hidden  : true
                },
                {
                    width   : 70,
                    name    : 'tonerColorIdModified',
                    index   : 'tonerColorId',
                    label   : 'Color',
                    sortable: true
                },
                {
                    width : 80,
                    name  : 'dealerSku',
                    index : 'dealerSku',
                    label : 'dealerSku',
                    hidden: true
                },
                {
                    width : 80,
                    name  : 'systemSku',
                    index : 'systemSku',
                    label : 'systemSku',
                    hidden: true
                },
                {
                    width: 80,
                    name : 'skuModified',
                    index: 'sku',
                    label: '(' + dealerSkuName + ')<br/>OEM SKU'
                },
                {
                    width : 220,
                    name  : 'manufacturer',
                    index : 'manufacturer',
                    label : 'Manufacturer',
                    hidden: false
                },
                {
                    width : 55,
                    name  : 'manufacturerId',
                    index : 'manufacturerId',
                    label : 'ManufacturerId',
                    hidden: true
                },
                {
                    width: 300,
                    name : 'device_list',
                    index: 'device_list',
                    label: 'Machine Compatibility'
                },
                {
                    width: 118,
                    name : 'yield',
                    index: 'yield',
                    label: 'Yield',
                    align: 'right'
                },
                {
                    width : 118,
                    name  : 'dealerCost',
                    index : 'dealerCost',
                    label : 'dealerCost',
                    align : 'right',
                    hidden: true
                },
                {
                    width : 120,
                    name  : 'systemCost',
                    index : 'systemCost',
                    label : 'systemCost',
                    align : 'right',
                    hidden: true
                },
                {
                    width: 100,
                    name : 'costModified',
                    index: 'cost',
                    label: 'Cost<br/>(System Cost)',
                    align: 'right'
                }
            ],
            postData    : {
                criteria      : function ()
                {
                    return $('#replacementTonersCriteria').val();
                },
                filter        : function ()
                {
                    return $("#replacementTonersFilter").val();
                },
                manufacturerId: function ()
                {
                    return $("#replacementTonersSelectManufacturer").val();
                }
            },
            jsonReader  : {
                repeatitems: false
            },
            height      : 'auto',
            rowNum      : 10,
            rowList     : [ 10, 20, 30, 50 ],
            pager       : '#replacementToners_Pager',
            gridComplete: function ()
            {
                var grid = $(this).jqGrid();
                var ids = grid.getDataIDs();

                for (var i = 0; i < ids.length; i++)
                {
                    var currentRowId = ids[i];
                    var currentRow = grid.getRowData(currentRowId);
//                    //Change cost to include system
                    if (currentRow.dealerCost == '')
                    {
                        currentRow.costModified = "NA" + "</br><small>$" + currentRow.systemCost + "</small>";
                    }
                    else
                    {
                        currentRow.costModified = "$" + currentRow.dealerCost + "</br><small>$" + currentRow.systemCost + "</small>";
                    }
                    if (currentRow.dealerSku == '')
                    {
                        currentRow.skuModified = "NA" + "</br>" + currentRow.systemSku;
                    }
                    else
                    {
                        currentRow.skuModified = currentRow.dealerSku + "</br>" + currentRow.systemSku;
                    }
                    //Change tonerColorId to images section
                    var image = '/img/tonerColor/Black.png';
                    var label = 'Black';
                    switch (currentRow.tonerColorId)
                    {
                        case "1":
                            image = '/img/tonercolors/Black.png';
                            label = "Black";
                            break;
                        case "2":
                            image = '/img/tonercolors/Cyan.png';
                            label = "Cyan";
                            break;
                        case "3":
                            image = '/img/tonercolors/Magenta.png';
                            label = "Magenta";
                            break;
                        case "4"://Yellow
                            image = '/img/tonercolors/Yellow.png';
                            label = "Yellow";
                            break;
                        case "5":
                            image = '/img/tonercolors/3color.png';
                            label = "3 Color";
                            break;
                        case "6":
                            image = '/img/tonercolors/4color.png';
                            label = "4 Color";
                            break;
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
                    var deviceListCollapsibleContainer = '<div id="replacementToners_outer_' + ids[i] + '" style="text-align: left; width: 200px;">';
                    var compatibleDevices = currentRow.device_list.split(";,");

                    // Loop through each device and add it to the container
                    for (var j = 0; j < compatibleDevices.length; j++)
                    {
                        var device = compatibleDevices[j];
                        if (j == max)
                        {
                            deviceListCollapsibleContainer += '<div id="replacementToners_inner_' + ids[i] + '" style="display: none;">';
                        }
                        deviceListCollapsibleContainer += device + '<br />';
                        if (j > max && j == compatibleDevices.length - 1)
                        {
                            deviceListCollapsibleContainer += '</div>';
                            deviceListCollapsibleContainer += '<a id="replacementToners_view_link_' + ids[i] + '" href="javascript: void(0);" class="blue_link" onclick="javascript: view_device_list(\'replacementToners\',' + ids[i] + ');">View All...</a>';
                        }
                    }
                    currentRow.device_list = deviceListCollapsibleContainer;
                    currentRow.tonerColorIdModified = "<div style='text-align: center;padding-top:5px;'><img src=" + image + " width='24' /><br/>" + label + "</div>";
                    grid.setRowData(currentRowId, currentRow);
                }
            }
        }
    );
// Hide the top paging!
    $('#replacementToners_toppager_center').hide();

    var replaceTonersSelectManufacturer = $("#replacementTonersSelectManufacturer");

    /**
     * Available Toners Manufacturer search
     */
    replaceTonersSelectManufacturer.select2({
        placeholder       : "Search for manufacturer",
        minimumInputLength: 1,
        ajax              : {
            // instead of writing the function to execute the request we use Select2's convenient helper
            url     : TMTW_BASEURL + 'proposalgen/managedevices/search-for-manufacturer',
            dataType: 'json',
            data    : function (term, page)
            {
                return {
                    manufacturerName: term, // search term
                    page_limit      : 10
                };
            },
            results : function (data, page)
            {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to alter remote JSON data
                return {results: data};
            }
        },

        dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
        escapeMarkup    : function (m)
        {
            return m;
        } // we do not want to escape markup since we are displaying html in results
    });

    replaceTonersSelectManufacturer.on("change", function (e)
    {
        reloadReplacementTonersGrids();
    });
}
