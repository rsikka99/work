/**
 * Created with JetBrains PhpStorm.
 * User: triehl
 * Date: 09/04/13
 * Time: 10:32 AM
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function ()
{
    //find center screen for modal popup
    var sTop = ($(window).height() / 2) - 100;
    var sLeft = ($(window).width() / 2) - 200;

    //*********************************************************************
    //* DEVICES GRID
    //*********************************************************************
    jQuery("#devices_list").jqGrid({
        url         : TMTW_BASEURL + '/proposalgen/admin/masterdeviceslist' + '?type=printers&compid=1',
        datatype    : 'json',
        colModel    : [
            {
                width : 30,
                name  : 'masterID',
                index : 'masterId',
                label : "MasterId",
                hidden: true
            },
            {
                width: 150,
                name : 'manufacturerId',
                index: 'manufacturerId',
                label: 'Manufacturer'
            },
            {
                width: 250,
                name : 'printer_model',
                index: 'printer_model',
                label: 'Printer Model'
            },
            {
                width   : 100, name: 'labor_cost_per_page_dealer',
                index   : 'labor_cost_per_page_dealer',
                label   : 'Labor CPP',
                align   : 'right',
                sorttype: 'int'
            },
            {
                width   : 100,
                name    : 'parts_cost_per_page_dealer',
                index   : 'parts_cost_per_page_dealer',
                label   : 'Parts CPP',
                align   : 'right',
                sorttype: 'int'
            },
            {
                width: 150,
                name : 'new_labor_cost_per_page',
                index: 'new_labor_cost_per_page',
                label: 'New Labor CPP',
                align: 'right'
            },
            {
                width: 150,
                name : 'new_parts_cost_per_page',
                index: 'new_parts_cost_per_page',
                label: 'New Parts CPP',
                align: 'right'
            }
        ],
        height      : 'auto',
        width       : 940,
        rowNum      : 10,
        jsonReader  : {
            repeatitems: false
        },
        multiselect : true,
        rowList     : [10, 20, 30],
        pager       : '#devices_pager',
        onPaging    : function ()
        {
            //update hdnPage
            $("#hdnPage").val(jQuery("#devices_list").jqGrid('getGridParam', 'page'));
        },
        gridComplete: function ()
        {
            var ids = jQuery("#devices_list").jqGrid('getDataIDs');
            var grid = $(this).jqGrid();
            for (var i = 0; i < ids.length; i++)
            {
                var cur_row = ids[i];
                var laborCostPerPage = document.getElementById("devices_list").rows[i + 1].cells[4].innerHTML.replace("$", "").replace(/,/gi, "").replace(/ /gi, "");
                if (laborCostPerPage == 0)
                {
                    document.getElementById("devices_list").rows[i + 1].cells[4].innerHTML = "$ -";
                }
                else
                {
                    document.getElementById("devices_list").rows[i + 1].cells[4].innerHTML = "$ " + laborCostPerPage;
                }

                var partsCostPerPage = document.getElementById("devices_list").rows[i + 1].cells[5].innerHTML.replace("$", "").replace(/,/gi, "").replace(/ /gi, "");
                if (partsCostPerPage == 0)
                {
                    document.getElementById("devices_list").rows[i + 1].cells[5].innerHTML = "$ -";
                }
                else
                {
                    document.getElementById("devices_list").rows[i + 1].cells[5].innerHTML = "$ " + partsCostPerPage;
                }

                hidden_price_element_parts = "<input type='hidden' id='hdnDevicePriceParts" + grid.getRowData(i + 1).masterID + "' name='hdnDevicePriceParts" + grid.getRowData(i + 1).masterID + "' value='" + partsCostPerPage + "' class='span1' maxlength='8' />";
                hidden_price_element_labor = "<input type='hidden' id='hdnDevicePriceLabor" + grid.getRowData(i + 1).masterID + "' name='hdnDevicePriceLabor" + grid.getRowData(i + 1).masterID + "' value='" + laborCostPerPage + "' class='span1' maxlength='8' />";
                new_labor_cost_per_page_element = "$ <input type='text' id='laborCostPerPage" + grid.getRowData(i + 1).masterID + "' name='laborCostPerPage" + grid.getRowData(i + 1).masterID + "' class='span1' maxlength='12' style='text-align:right;width:70px' onkeypress='javascript: return numbersonly(this, event);' />";
                new_parts_cost_per_page_element = "$ <input type='text' id='partsCostPerPage" + grid.getRowData(i + 1).masterID + "' name='partsCostPerPage" + grid.getRowData(i + 1).masterID + "' class='span1' maxlength='12' style='text-align:right;width:70px' onkeypress='javascript: return numbersonly(this, event);' />";
                jQuery("#devices_list").jqGrid('setRowData', ids[i], {new_labor_cost_per_page: hidden_price_element_labor + new_labor_cost_per_page_element});
                jQuery("#devices_list").jqGrid('setRowData', ids[i], {new_parts_cost_per_page: hidden_price_element_parts + new_parts_cost_per_page_element});
            }

            $("#default_price").show();

            if (repop_array != '')
            {
                repop_form('Device');
            }
        },
        editurl     : 'dummy.php'
    });

    jQuery("#devices_list").jqGrid('navGrid', '#devices_pager',
        {add: false, del: false, edit: false, refresh: false, search: false},
        {closeAfterEdit: true, recreateForm: true, closeOnEscape: true, width: 400, top: sTop, left: sLeft},
        {closeAfterAdd: true, recreateForm: true, closeOnEscape: true, width: 400, top: sTop, left: sLeft},
        {},
        {},
        {}
    );

    //*********************************************************************
    //* TONERS GRID
    //*********************************************************************
    jQuery("#toners_list").jqGrid({
        url         : TMTW_BASEURL + '/proposalgen/admin/tonerslist',
        datatype    : 'json',
        //colNames    : ['Toner ID', 'SKU', 'Manufacturer', 'Type', 'Color', 'Yield', 'Default Price', 'Price', 'New Price', 'MasterID', 'Added', 'Machine Compatibility'],
        colModel    : [
            {
                width      : 60,
                name       : 'toner_id',
                index      : 'toner_id',
                label      : 'Toner Id',
                sorttype   : 'int',
                hidden     : true,
                editable   : true,
                editoptions: {readonly: true, size: 12}
            },
            {
                width      : 80,
                name       : 'toner_SKU',
                index      : 'toner_SKU',
                label      : 'SKU',
                editable   : true,
                editoptions: {size: 12, maxlength: 30}
            },
            {
                width      : 150,
                name       : 'manufacturer_name',
                index      : 'toner_manufacturer',
                label      : 'Manufacturer',
                editable   : true,
                editoptions: {size: 20, maxlength: 30}
            },
            {
                width      : 50,
                name       : 'part_type_id',
                index      : 'part_type_id',
                label      : 'Type',
                editable   : true,
                sortable : false,
                editoptions: {size: 20, maxlength: 30}
            },
            {
                width      : 60,
                name       : 'toner_color_name',
                index      : 'toner_color_name',
                label      : 'Color',
                editable   : true,
                editoptions: {size: 12, maxlength: 30}
            },
            {
                width      : 50,
                name       : 'toner_yield',
                index      : 'yield',
                label      : 'Yield',
                editable   : true,
                editoptions: {size: 10, maxlength: 4},
                align      : 'right'
            },
            {
                width      : 90,
                name       : 'dealer_sku',
                index      : 'dealerSku',
                label      : 'Dealer Sku',
                sortable : true,
                editable   : true,
                editoptions: {size: 10, maxlength: 4},
                align      : 'right'
            },
            {
                width: 120,
                name : 'new_dealer_sku',
                index: 'new_dealer_sku',
                label: 'New Dealer Sku',
                align: 'right'
            },
            {
                width        : 100,
                name         : 'toner_price',
                index        : 'toner_dealer_price',
                label        : 'System Cost',
                editable     : true,
                editoptions  : {size: 10, maxlength: 8},
                formatter    : 'currency',
                align        : 'right',
                formatoptions: {prefix: "$", thousandsSeparator: ","},
                sorttype     : 'int'
            },
            {
                width        : 60,
                name         : 'toner_dealer_price',
                index        : 'toner_price',
                label        : 'Cost',
                editable     : true,
                editoptions  : {size: 10, maxlength: 8},
                formatter    : 'currency',
                align        : 'right',
                formatoptions: {prefix: "$", thousandsSeparator: ","},
                sorttype     : 'int'
            },
            {
                width: 100,
                name : 'new_toner_price',
                index: 'new_toner_price',
                label: 'New Price',
                align: 'right'
            },
            {
                width      : 50,
                name       : 'master_device_id',
                index      : 'master_device_id',
                label      : 'MasterID',
                hidden     : true,
                editable   : true,
                editoptions: {size: 12}
            },
            {
                width      : 50,
                name       : 'is_added',
                index      : 'is_added',
                label      : 'Added',
                hidden     : true,
                editable   : true,
                editoptions: {size: 12}
            },
            {
                width: 225,
                name : 'device_list',
                index: 'device_list',
                label: 'Machine Compatibility'
            }
        ],
        hidegrid    : false,
        shrinkToFit : false,
        width       : 940,
        height      : 'auto',
        multiselect : true,
        jsonReader  : {
            repeatitems: false
        },
        rowNum      : 10,
        rowList     : [10, 20, 30],
        pager       : '#toners_pager',
        onPaging    : function ()
        {
            //update hdnPage
            $("#hdnPage").val(jQuery("#toners_list").jqGrid('getGridParam', 'page'));
        },
        gridComplete: function ()
        {
            var ids = jQuery("#toners_list").jqGrid('getDataIDs');
            var grid = $(this).jqGrid();
            for (var i = 0; i < ids.length; i++)
            {
                var cur_row = ids[i];
                var cur_price = document.getElementById("toners_list").rows[i + 1].cells[7].innerHTML.replace("$", "").replace(/,/gi, "").replace(/ /gi, "");
                hidden_price_element = "<input type='hidden' name='hdnTonerPrice" + grid.getRowData(i+1).toner_id + "' id='hdnTonerPrice" + grid.getRowData(i+1).toner_id + "' value='" + cur_price + "' class='span1' maxlength='8' />";
                new_price_element = "$ <input type='text' name='txtTonerPrice" + grid.getRowData(i+1).toner_id + "' id='txtTonerPrice" + grid.getRowData(i+1).toner_id + "' class='span1' maxlength='8' style='text-align:right;width:60px' onkeypress='javascript: return numbersonly(this, event);' />";
                new_dealer_sku_element = "<input type='text' name='txtNewDealerSku" + grid.getRowData(i+1).toner_id + "' id='txtNewDealerSku" + grid.getRowData(i+1).toner_id + "' class='span1' maxlength='8' style='text-align:right;width:100px' onkeypress='javascript: return numbersonly(this, event);' />";
                jQuery("#toners_list").jqGrid('setRowData', ids[i], {new_dealer_sku: new_dealer_sku_element});
                jQuery("#toners_list").jqGrid('setRowData', ids[i], {new_toner_price: hidden_price_element + new_price_element});

                var min = 4;
                var max = 2;
                var output = '';
                device_list = document.getElementById("toners_list").rows[i + 1].cells[14].innerHTML;
                var pieces = device_list.split("; ");
                output += '<div id="outer_' + ids[i] + '" style="text-align: left; width: 200px;">';
                for (var j = 0; j < pieces.length; j++)
                {
                    device = pieces[j];
                    if (j == max)
                    {
                        output += '<div id="inner_' + ids[i] + '" style="display: none;">';
                    }
                    output += device + '<br />';
                    if (j > max && j == pieces.length - 1)
                    {
                        doubleQuotes = "''";
                        output += '</div>';
                        output += '<a id="view_link_' + ids[i] + '" href="javascript: void(0);" class="blue_link" onclick="javascript: view_device_list(\'\',' + ids[i] + ');">View All...</a>';
                    }
                }
                output += '</div>';
                jQuery("#toners_list").jqGrid('setRowData', ids[i], {device_list: output});

                $("#default_price").show();

                if (repop_array != '')
                {
                    repop_form('Toner');
                }
            }
        },
        editurl     : 'dummy.php'
    });

    jQuery("#toners_list").jqGrid('navGrid', '#toners_pager',
        {add: false, del: false, edit: false, refresh: false, search: false},
        {closeAfterEdit: true, recreateForm: true, closeOnEscape: true, width: 400, top: sTop, left: sLeft},
        {closeAfterAdd: true, recreateForm: true, closeOnEscape: true, width: 400, top: sTop, left: sLeft},
        {},
        {},
        {}
    );

    $("#pricing_filter").change(function ()
    {
        //reset page to 1
        repop_page = 1;
        $("#hdnPage").val(1);
        //remove all options
        $("#criteria_filter >option").remove();

        //add new options depending on selection
        if ($('#pricing_filter').val() == 'toner')
        {
            $("#criteria_filter").append($('<option></option>').val('machine_compatibility').html('Machine Compatibility'));
            $("#criteria_filter").append($('<option></option>').val('toner_sku').html('SKU'));
            $("#criteria_filter").append($('<option></option>').val('manufacturerId').html('Manufacturer'));
            $("#text_criteria").show();
            $("#list_criteria").hide();
        }
        else
        {
            $("#criteria_filter").append($('<option></option>').val('manufacturerId').html('Manufacturer'));
            $("#criteria_filter").append($('<option></option>').val('modelName').html('Printer Model'));
            setupManufacturerList(TMTW_BASEURL + '/proposalgen/admin/filterlistitems' + '?list=man');

        }
    });

    $("#criteria_filter").change(function ()
    {
        var url = '';

        //clear searches
        $("#txtCriteria").val('');
        $("#cboCriteria").html('');

        switch (this.value)
        {
            case "manufacturerId":
                url = TMTW_BASEURL + '/proposalgen/admin/filterlistitems' + '?list=man';
                break;
            case "type_name":
                url = TMTW_BASEURL + '/proposalgen/admin/filterlistitems' + '?list=type';
                break;
            case "toner_color_name":
                url = TMTW_BASEURL + '/proposalgen/admin/filterlistitems' + '?list=color';
                break;
            default:
                break;
        }

        if (url != '')
        {
            setupManufacturerList(url)

        }
        else
        {
            $("#list_criteria").hide();
            $("#text_criteria").show();
        }

    });

    $("#btnSearch").click(function ()
    {
        repop_page = 1;
        repop_array = '';
        $("#hdnPage").val(1);
        $("#message_container").html('');
        setTimeout("update_grid('search')", 300);
    });

    $("#btnClearSearch").click(function ()
    {
        repop_page = 1;
        repop_array = '';
        $("#hdnPage").val(1);
        $("#message_container").html('');
        setTimeout("update_grid('clear')", 300);
    });

    //*********************************************************************
    //* Set Defaults
    //*********************************************************************
    $('#toners_table').hide();

    if (repop == 1)
    {
        $('#devices_table').hide();
        if (refilter == 1)
        {
            setTimeout("update_grid('search')", 300);
            refilter = 0;
        }
        else
        {
            setTimeout("update_grid()", 300);
            $("#pricing_filter").change();
        }
    }

    if (repop_page != '')
    {
        $("#hdnPage").val(repop_page);
    }

    $('#criteria_filter').change();

});
function setupManufacturerList(url)
{
    $.ajax({
        type       : "POST",
        url        : url,
        contentType: "application/json; charset=utf-8",
        dataType   : "json",
        success    : function (data)
        {
            var obj = jQuery.parseJSON(data);
            var options = '';
            for (var i = 0; i < data.rows.length; i++)
            {
                list = (data.rows[i].cell);
                options += '<option value="' + list[0] + '">' + list[1] + '</option>';
            }
            $("#cboCriteria").html(options);
        },
        error      : function ()
        {
            $('#details_error').html("Error returning criteria list.");
        },
        complete   : function ()
        {
            $("#text_criteria").hide();
            $("#list_criteria").show();
        }
    });
}
function repop_form(grid)
{
    repop_items = repop_array.split(',');
    for (i = 0; i < repop_items.length; i++)
    {
        repop_item = repop_items[i].split(':');
        repop_id = repop_item[0];
        repop_value = repop_item[1];
        $("#txt" + grid + "Price" + repop_id).val(repop_value);
    }
}

//*********************************************************************
//* UPDATE GRIDS
//*********************************************************************
function update_grid(action)
{
    $('#devices_table').hide();
    $('#toners_table').hide();

    //update column headers
    if ($('#pricing_filter').val() == 'printer')
    {
        $("#default_price").html('Default Labor: $' + default_labor_formated + ' Default Parts: $' + default_parts_formated);
        $("#devices_list").jqGrid('setLabel', 'device_price', 'Printer Price');
        $("#devices_list").jqGrid('setLabel', 'new_price', 'New Price');
    }
    else if ($('#pricing_filter').val() == 'toner')
    {
        default_price = 0;
        $("#default_price").html('');
        //do nothing
    }
    $("#default_price").show();

    var params = '?filter=&criteria=';
    if (action == 'search')
    {
        if ($("#cboCriteria").is(":visible"))
        {
            criteria = $("#cboCriteria").val();
        }
        else
        {
            criteria = $("#txtCriteria").val();
        }
        <!--            criteria = $("#cboCriteria").val();-->
        params = '?filter=' + $("#criteria_filter").val() + '&criteria=' + criteria;
    }
    else if (action == 'clear')
    {
        $("#criteria_filter").attr('selectedIndex', 2);
        $("#list_criteria").hide();
        $("#text_criteria").show();
        //$("#cboCriteria").html('');
        $("#txtCriteria").val('');
        $("#criteria_filter").change();
    }
    //refresh the grid
    if ($('#pricing_filter').val() == 'toner')
    {
        $('#toners_list').setGridParam({url: TMTW_BASEURL + '/proposalgen/admin/tonerslist' + params, page: $("#hdnPage").val()});
        $('#toners_list').trigger("reloadGrid");
        setTimeout("$('#toners_table').show()", 200);
    }
    else
    {
        $('#devices_list').setGridParam({url: TMTW_BASEURL + '/proposalgen/admin/masterdeviceslist' + params + '&type=' + $('#pricing_filter').val(), page: $("#hdnPage").val()});
        $('#devices_list').trigger('reloadGrid');
        setTimeout("$('#devices_table').show()", 200);
    }
}

function apply_percentage()
{
    var id = 0;
    var old_price = 0;
    var new_price = 0;
    var sign = $("#cboSign").val();
    var percentage = $("#txtUpdate").val();
    var decimals = 2;

    //find decimal places
    if ($('#pricing_filter').val() != 'Toner Costs')
    {
        decimals = 4;
    }

    //master or dealer?
    var grid = '';
    var company = '';

    //toner or device?
    var list = 'devices';
    var type = 'Device';
    if ($('#pricing_filter').val() == 'toner')
    {
        list = 'toners';
        type = 'Toner';
    }

    if (percentage > 0)
    {
        var ids = jQuery("#" + grid + list + "_list").jqGrid('getDataIDs');
        for (var i = 0; i < ids.length; i++)
        {
            var cur_row = ids[i];
            var old_price = $("#hdn" + company + type + "Price" + cur_row).val();
            var old_price_labor = $("#hdn" + type + "PriceLabor" + cur_row).val();
            var old_price_parts = $("#hdn" + type + "PriceParts" + cur_row).val();
            if (old_price_labor == 0)
            {
                old_price_labor = default_labor;
            }
            if (old_price_parts == 0)
            {
                old_price_parts = default_parts;
            }
            if (old_price == 0)
            {
                old_price = default_price;
            }
            if ($("#jqg_" + grid + list + "_list_" + cur_row).is(':checked'))
            {
                if (type == 'Device')
                {
                    if (sign == "-")
                    {
                        //Labor
                        new_price = old_price_labor - (old_price_labor * (percentage / 100));
                        $("#laborCostPerPage" + cur_row).css('color', 'black');
                        $("#laborCostPerPage" + cur_row).val(new_price.toFixed(decimals));
                        //Parts
                        new_price = old_price_parts - (old_price_parts * (percentage / 100));
                        $("#partsCostPerPage" + cur_row).css('color', 'black');
                        $("#partsCostPerPage" + cur_row).val(new_price.toFixed(decimals));
                    }
                    else
                    {
                        new_price = (old_price_labor * ((percentage / 100) + 1));
                        $("#laborCostPerPage" + cur_row).css('color', 'black');
                        $("#laborCostPerPage" + cur_row).val(new_price.toFixed(decimals));

                        new_price = (old_price_parts * ((percentage / 100) + 1));
                        $("#partsCostPerPage" + cur_row).css('color', 'black');
                        $("#partsCostPerPage" + cur_row).val(new_price.toFixed(decimals));
                    }
                }
                else
                {
                    if (sign == "-")
                    {
                        new_price = old_price - (old_price * (percentage / 100));
                        $("#txt" + company + type + "Price" + cur_row).css('color', 'black');
                        $("#txt" + company + type + "Price" + cur_row).val(new_price.toFixed(decimals));
                    }
                    else
                    {
                        new_price = (old_price * ((percentage / 100) + 1));
                        $("#txt" + company + type + "Price" + cur_row).css('color', 'black');
                        $("#txt" + company + type + "Price" + cur_row).val(new_price.toFixed(decimals));
                    }

                }

            }

        }
    }
    else
    {
        $("#message_container").html("Please enter a percentage.");

    }
}

function do_action(action)
{
    if (action == 'file')
    {
        document.getElementById("hdnMode").action = "pricing";
        document.getElementById("bulk").action = "bulkfilepricing";
        document.getElementById("bulk").submit();
    }
}

function do_update()
{
    document.getElementById("hdnMode").value = 'update';
    document.getElementById("bulk").submit();
}

function view_device_list(type, id)
{
    if (document.getElementById(type + 'inner_' + id).style.display == 'none')
    {
        document.getElementById(type + 'inner_' + id).style.display = 'block';
        document.getElementById(type + 'view_link_' + id).innerHTML = 'Collapse...';
    }
    else
    {
        document.getElementById(type + 'inner_' + id).style.display = 'none';
        document.getElementById(type + 'view_link_' + id).innerHTML = 'View All...';
    }
}