jQuery(document).ready(function ()
{
    var $devices_list = jQuery("#devices_list");
    var $devices_table = jQuery("#devices_table");
    var $toners_list = jQuery("#toners_list");
    var $toners_table = jQuery("#toners_table");
    var $pricing_filter = jQuery("#pricing_filter");
    var $criteria_filter = jQuery("#criteria_filter");
    var $text_criteria = jQuery("#text_criteria");
    var $txt_criteria = jQuery("#txtCriteria");
    var $list_criteria = jQuery("#list_criteria");
    var $cbo_criteria = jQuery("#cboCriteria");
    var $default_price = jQuery("#default_price");
    var $details_error = jQuery('#details_error');
    var $hdn_page = jQuery("#hdnPage");
    var $button_update = jQuery("#btnUpdate");
    var $button_apply = jQuery("#btnApply");
    var $text_update = jQuery("#txtUpdate");


    var setupManufacturerList = function (url)
    {
        $.ajax({
            type       : "POST",
            url        : url,
            contentType: "application/json; charset=utf-8",
            dataType   : "json",
            success    : function (data)
            {
                var obj = data;
                if (typeof(data) == 'string')
                {
                    obj = jQuery.parseJSON(data);
                }
                else
                {
                    var options = '';
                }
                for (var i = 0; i < data.rows.length; i++)
                {
                    list = (data.rows[i].cell);
                    options += '<option value="' + list[0] + '">' + list[1] + '</option>';
                }
                $cbo_criteria.html(options);
            },
            error      : function ()
            {
                $details_error.html("Error returning criteria list.");
            },
            complete   : function ()
            {
                $text_criteria.hide();
                $list_criteria.show();
            }
        });
    };

    //*********************************************************************
    //* UPDATE GRIDS
    //*********************************************************************
    var update_grid = function (action)
    {
        $devices_table.hide();
        $toners_table.hide();

        //update column headers
        if ($pricing_filter.val() == 'printer')
        {
            $default_price.show();
            $devices_list.jqGrid('setLabel', 'device_price', 'Printer Price');
            $devices_list.jqGrid('setLabel', 'new_price', 'New Price');
        }
        else if ($pricing_filter.val() == 'toner')
        {
            $default_price.hide();
        }

        var params = '?filter=&criteria=';
        if (action == 'search')
        {
            if ($cbo_criteria.is(":visible"))
            {
                criteria = jQuery("#cboCriteria").val();
            }
            else
            {
                criteria = jQuery("#txtCriteria").val();
            }

            params = '?filter=' + $criteria_filter.val() + '&criteria=' + criteria;
        }
        else if (action == 'clear')
        {
            $criteria_filter.attr('selectedIndex', 2);
            jQuery("#list_criteria").hide();
            jQuery("#text_criteria").show();
            jQuery("#txtCriteria").val('');
            $criteria_filter.change();
        }

        // Refresh the grid
        if ($pricing_filter.val() == 'toner')
        {
            $toners_list.setGridParam({
                url : TMTW_BASEURL + '/proposalgen/admin/tonerslist' + params,
                page: $hdn_page.val()
            });
            $toners_list.trigger("reloadGrid");


            setTimeout(function ()
            {
                $toners_table.show()
            }, 200);
        }
        else
        {
            $devices_list.setGridParam({url: TMTW_BASEURL + '/proposalgen/admin/masterdeviceslist' + params + '&type=' + $pricing_filter.val(), page: $hdn_page.val()});
            $devices_list.trigger('reloadGrid');

            setTimeout(function ()
            {
                $devices_table.show();
            }, 200);
        }
    };

    var apply_percentage = function ()
    {
        var new_price = 0;
        var default_labor = '';
        var default_parts = '';
        var sign = jQuery("#cboSign").val();
        var percentage = jQuery("#txtUpdate").val();
        var decimals = 4;

        // Get our jqGrid
        var jqGridSelector = '';
        if ($pricing_filter.val() == 'toner')
        {
            jqGridSelector = $toners_list;
        }
        else
        {
            jqGridSelector = $devices_list;
        }

        if (percentage > 0)
        {
            var ids = jqGridSelector.jqGrid('getGridParam', 'selarrrow');
            var jqGridRow = '';

            for (var i = 0; i < ids.length; i++)
            {
                var cur_row = ids[i];
                if ($pricing_filter.val() == 'printer')
                {
                    jqGridRow = jqGridSelector.getRowData(cur_row);
                    var masterId = jqGridRow.masterID;


                    var laborCostPerPageElement = jQuery("#laborCostPerPage" + masterId);
                    var partsCostPerPageElement = jQuery("#partsCostPerPage" + masterId);

                    var old_price_labor = jQuery("#hdnDevicePriceLabor" + masterId).val();
                    var old_price_parts = jQuery("#hdnDevicePriceParts" + masterId).val();

                    if (old_price_labor == 0)
                    {
                        old_price_labor = default_labor;
                    }

                    if (old_price_parts == 0)
                    {
                        old_price_parts = default_parts;
                    }

                    if (sign == "-")
                    {
                        // Labor
                        laborCostPerPageElement.val((old_price_labor - (old_price_labor * (percentage / 100))).toFixed(decimals));

                        // Parts
                        partsCostPerPageElement.val((old_price_parts - (old_price_parts * (percentage / 100))).toFixed(decimals));
                    }
                    else
                    {
                        // Labor
                        laborCostPerPageElement.val(((old_price_labor * ((percentage / 100) + 1))).toFixed(decimals));

                        // Parts
                        partsCostPerPageElement.val(((old_price_parts * ((percentage / 100) + 1))).toFixed(decimals));
                    }
                }
                else
                {
                    jqGridRow = jqGridSelector.getRowData(cur_row);
                    var tonerPriceElement = jQuery("#txtTonerPrice" + jqGridRow.toner_id);

                    var tonerPrice = 0;

                    if (parseFloat(jqGridRow.toner_dealer_price) > 0)
                    {
                        tonerPrice = jqGridRow.toner_dealer_price;
                    }
                    else
                    {
                        tonerPrice = jqGridRow.toner_price;
                    }

                    if (sign == "-")
                    {
                        new_price = tonerPrice - (tonerPrice * (percentage / 100));
                        tonerPriceElement.val(new_price.toFixed(decimals));
                    }
                    else
                    {
                        new_price = (tonerPrice * ((percentage / 100) + 1));
                        tonerPriceElement.val(new_price.toFixed(decimals));
                    }
                }
            }
        }
        else
        {
            jQuery("#message_container").html("Please enter a percentage.");
        }
    };

    /**
     * Handles the save button
     */
    var do_update = function ()
    {
        document.getElementById("hdnMode").value = 'update';
        document.getElementById("bulk").submit();
    };

    var view_device_list = function (type, id)
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
    };

    //*********************************************************************
    //* DEVICES GRID
    //*********************************************************************
    $devices_list.jqGrid({
        url         : TMTW_BASEURL + '/proposalgen/admin/masterdeviceslist?type=printers&compid=1',
        datatype    : 'json',
        colModel    : [
            {
                width : 30,
                name  : 'masterID',
                index : 'masterId',
                label : "Master Device Id",
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
            $hdn_page.val($devices_list.jqGrid('getGridParam', 'page'));
        },
        gridComplete: function ()
        {
            var ids = $devices_list.jqGrid('getDataIDs');
            var grid = jQuery(this).jqGrid();
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
                $devices_list.jqGrid('setRowData', ids[i], {new_labor_cost_per_page: hidden_price_element_labor + new_labor_cost_per_page_element});
                $devices_list.jqGrid('setRowData', ids[i], {new_parts_cost_per_page: hidden_price_element_parts + new_parts_cost_per_page_element});
            }
        },
        editurl     : 'dummy.php'
    });

    $devices_list.jqGrid('navGrid', '#devices_pager',
        {add: false, del: false, edit: false, refresh: false, search: false}
    );

    //*********************************************************************
    //* TONERS GRID
    //*********************************************************************
    $toners_list.jqGrid({
        url     : TMTW_BASEURL + '/proposalgen/admin/tonerslist',
        datatype: 'json',

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
                width      : 150,
                name       : 'manufacturer_name',
                index      : 'toner_manufacturer',
                label      : 'Manufacturer',
                editable   : true,
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
                width      : 80,
                name       : 'toner_SKU',
                index      : 'toner_SKU',
                label      : 'OEM SKU',
                editable   : true,
                editoptions: {size: 12, maxlength: 30}
            },
            {
                width      : 90,
                name       : 'dealer_sku',
                index      : 'dealerSku',
                label      : 'Dealer Sku',
                sortable   : true,
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
            $hdn_page.val($toners_list.jqGrid('getGridParam', 'page'));
        },
        gridComplete: function ()
        {
            var ids = $toners_list.jqGrid('getDataIDs');
            var grid = jQuery(this).jqGrid();
            for (var i = 0; i < ids.length; i++)
            {
                var cur_row = ids[i];
                var cur_price = document.getElementById("toners_list").rows[i + 1].cells[7].innerHTML.replace("$", "").replace(/,/gi, "").replace(/ /gi, "");
                hidden_price_element = "<input type='hidden' name='hdnTonerPrice" + grid.getRowData(i + 1).toner_id + "' id='hdnTonerPrice" + grid.getRowData(i + 1).toner_id + "' value='" + cur_price + "' class='span1' maxlength='8' />";
                new_price_element = "$ <input type='text' name='txtTonerPrice" + grid.getRowData(i + 1).toner_id + "' id='txtTonerPrice" + grid.getRowData(i + 1).toner_id + "' class='span1' maxlength='8' style='text-align:right;width:60px' onkeypress='javascript: return numbersonly(this, event);' />";
                new_dealer_sku_element = "<input type='text' name='txtNewDealerSku" + grid.getRowData(i + 1).toner_id + "' id='txtNewDealerSku" + grid.getRowData(i + 1).toner_id + "' class='span1' maxlength='8' style='text-align:right;width:100px';' />";
                $toners_list.jqGrid('setRowData', ids[i], {new_dealer_sku: new_dealer_sku_element});
                $toners_list.jqGrid('setRowData', ids[i], {new_toner_price: hidden_price_element + new_price_element});

                var min = 4;
                var max = 2;
                var output = '';
                device_list = document.getElementById("toners_list").rows[i + 1].cells[13].innerHTML;
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
                $toners_list.jqGrid('setRowData', ids[i], {device_list: output});
            }
        },
        editurl     : 'dummy.php'
    });

    $toners_list.jqGrid('navGrid', '#toners_pager',
        {add: false, del: false, edit: false, refresh: false, search: false}
    );

    $pricing_filter.change(function ()
    {
        //reset page to 1
        repop_page = 1;
        $hdn_page.val(1);
        //remove all options
        $criteria_filter.empty();

        //add new options depending on selection
        if (jQuery('#pricing_filter').val() == 'toner')
        {
            $criteria_filter.append(jQuery('<option></option>').val('machine_compatibility').html('Machine Compatibility'));
            $criteria_filter.append(jQuery('<option></option>').val('toner_sku').html('SKU'));
            $criteria_filter.append(jQuery('<option></option>').val('manufacturerId').html('Manufacturer'));

            $text_criteria.show();
            $list_criteria.hide();
        }
        else
        {
            $criteria_filter.append(jQuery('<option></option>').val('manufacturerId').html('Manufacturer'));
            $criteria_filter.append(jQuery('<option></option>').val('modelName').html('Printer Model'));
            setupManufacturerList(TMTW_BASEURL + '/proposalgen/admin/filterlistitems' + '?list=man');

        }
    });

    $criteria_filter.change(function ()
    {
        var url = '';

        //clear searches
        jQuery("#txtCriteria").val('');
        jQuery("#cboCriteria").html('');

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
            jQuery("#list_criteria").hide();
            jQuery("#text_criteria").show();
        }

    });

    jQuery("#btnSearch").click(function ()
    {
        repop_page = 1;
        $hdn_page.val(1);
        jQuery("#message_container").html('');
        setTimeout(function ()
        {
            update_grid('search');
        }, 300);
    });

    jQuery("#btnClearSearch").click(function ()
    {
        repop_page = 1;
        $hdn_page.val(1);
        jQuery("#message_container").html('');
        setTimeout(function ()
        {
            update_grid('clear');
        }, 300);
    });

    //*********************************************************************
    //* Set Defaults
    //*********************************************************************
    $toners_table.hide();

    if (repop == 1)
    {
        $devices_table.hide();

        if (refilter == 1)
        {
            setTimeout(function ()
            {
                update_grid('search');
            }, 300);
            refilter = 0;
        }
        else
        {
            setTimeout(function ()
            {
                update_grid();
            }, 300);
            $pricing_filter.change();
        }
    }
    else
    {
        $default_price.show();
    }

    if (repop_page != '')
    {
        $hdn_page.val(repop_page);
    }


    /**
     * Pricing Filter Dropdown handler
     */
    $pricing_filter.on('change', function ()
    {
        update_grid('pricing');
    });

    /**
     * Page save button handler
     */
    $button_update.on('click', function ()
    {
        do_update();
    });

    /**
     * Page save button handler
     */
    $button_apply.on('click', function ()
    {
        apply_percentage();
    });

    $text_update.on('keypress', function (event)
    {
        numbersonly(this, event, true);
    });

    $criteria_filter.change();
});