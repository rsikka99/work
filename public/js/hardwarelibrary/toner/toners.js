$(function ()
{
    isAllowed = true;
    masterDeviceId = 1;
    var tonersList = [];
    var tonerConfigId = 1;

    /**
     * Available Toners Grid
     */
    $('#availableToners').jqGrid(
        {
            url         : TMTW_BASEURL + 'hardwarelibrary/toner/all-toners-list',
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
                    width : 120,
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
                    width: 120,
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
                    width: 300,
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
                }
            ],
            jsonReader  : {
                repeatitems: false
            },
            postData    : {
                filter            : function ()
                {
                    return $("#tonersFilter").val();
                },
                criteria          : function ()
                {
                    if ($("#tonersFilter").val() === 'tonerColorId')
                    {
                        return $('#tonersCriteriaList').val();
                    }
                    else
                    {
                        return $('#tonersCriteria').val();
                    }
                },
                manufacturerId    : function ()
                {
                    return $("#tonersSelectManufacturer").val();
                },
                tonerColorConfigId: function ()
                {
                    return $("#tonerConfigId").val();
                }
            },
            height      : 'auto',
            rowNum      : 10,
            rowList     : [ 10, 20, 30, 50 ],
            pager       : '#availableTonersPager',
            toppager    : true,
            gridComplete: function ()
            {
                var grid = $(this).jqGrid();
                var ids = grid.getDataIDs();

                for (var i = 0; i < ids.length; i++)
                {
                    var currentRowId = ids[i];
                    var currentRow = grid.getRowData(currentRowId);

                    // Change cost to include system
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
                     * This is the number of Devices to list before the view all toggle takes into effect
                     * @type {number}
                     */
                    var max = 1;

                    /**
                     * This is the final container that everything will be in
                     * @type {string}
                     */
                    var deviceListCollapsibleContainer = '<div id="toners_outer_' + ids[i] + '" style="text-align: left; width: 200px;">';
                    var compatibleDevices = currentRow.device_list.split(";,");

                    // Loop through each device and add it to the container
                    for (var j = 0; j < compatibleDevices.length; j++)
                    {
                        var device = compatibleDevices[j];
                        if (j == max)
                        {
                            deviceListCollapsibleContainer += '<div id="toners_inner_' + ids[i] + '" style="display: none;">';
                        }

                        deviceListCollapsibleContainer += device + '<br />';
                    }

                    if (compatibleDevices.length > max)
                    {
                        deviceListCollapsibleContainer += '</div>';
                        deviceListCollapsibleContainer += '<a id="toners_view_link_' + ids[i] + '" href="javascript: void(0);" class="blue_link" onclick="javascript: view_device_list(\'toners\',' + ids[i] + ');">View All...</a>';
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
    var availableTonersjQuery = jQuery("#availableToners");
    availableTonersjQuery.navGrid('#availableToners_toppager', {edit: false, add: false, del: false, search: false, refresh: false});

    //Create New
    availableTonersjQuery.navButtonAdd('#availableToners_toppager', {
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

            $('#tonersModal').modal('show');
        },
        position     : "last"
    });

    //Edit
    availableTonersjQuery.navButtonAdd('#availableToners_toppager', {
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

                if ((data['isSystemDevice'] == 0) && isSaveAndApproveAdmin)
                {
                    $("#availableTonerssaveAndApprove").closest("div.control-group").attr('style', 'display:block');
                }
                else
                {
                    $("#availableTonerssaveAndApprove").closest("div.control-group").attr('style', 'display:none')
                }

                $('#tonersModal').modal('show');
            }
            else
            {
                $("#alertMessageModal").modal().show()
            }
        },
        position     : "last"
    });

    //Delete
    availableTonersjQuery.navButtonAdd('#availableToners_toppager', {
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
                            $('#deleteId').val(data['id']);
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

    var tonersSelectManufacturer = $("#tonersSelectManufacturer");

    /**
     * Available Toners Manufacturer search
     */
    tonersSelectManufacturer.select2({
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
        dropdownCssClass  : "bigdrop", // apply css that makes the dropdown taller
        escapeMarkup      : function (m)
        {
            return m;
        } // we do not want to escape markup since we are displaying html in results
    });

    tonersSelectManufacturer.on("change", function (e)
    {
        reloadAvailableToners();
    });

    $("#availableTonersForm").bind("createTonerSuccess", function (e, masterDeviceId)
    {
        $("#tonersModal").modal('hide');
    });

    loadReplacementGrids();
});

/**
 * Reloads assigned and unassigned toner jqgrids in Supplies & Service
 */
function reloadAvailableToners()
{
    var availableToners = jQuery("#availableToners");
    availableToners.trigger("reloadGrid");
}

$(document).on("click", "#tonersClearManSearch", function ()
{
    $("#tonersSelectManufacturer").select2("data", null);
});

$(document).on("click", "#tonersSearch", function ()
{
    reloadAvailableToners();
});

$(document).on("click", "#tonersReset", function ()
{
    $("#tonersFilter").val('dealerSku');
    var tonersCriteria = $("#tonersCriteria");
    tonersCriteria.val('');
    tonersCriteria.css({"display": 'initial'});
    $("#tonersCriteriaList").css({"display": 'none'});

    var availableTonersCriteria = $("#availableTonersCriteria");
    $("#availableTonersFilter").val('dealerSku');
    availableTonersCriteria.val('');
    $("#availableTonersCriteriaList").css({"display": 'none'});
    availableTonersCriteria.css({"display": 'initial'});
    reloadAvailableToners();
});

var tonersFilter = $("#tonersFilter");
var tonersCriteria = $("#tonersCriteria");
var tonersCriteriaList = $("#tonersCriteriaList");
tonersFilter.change(function ()
{
    if (tonersFilter.val() == 'tonerColorId')
    {
        tonersCriteriaList.css({"display": 'initial'});
        tonersCriteria.css({"display": 'none'});
    }
    else if (tonersFilter.val() == 'isSystemDevice')
    {
        tonersCriteriaList.css({"display": 'none'});
        tonersCriteria.css({"display": 'none'});
    }
    else
    {
        tonersCriteriaList.css({"display": 'none'});
        tonersCriteria.css({"display": 'initial'});
    }
});
