var $document = jQuery(document);
$document.ready(function ()
{
    /**
     * FIXME lrobert: Ok this list of elements is getting a bit crazy
     */
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
    var $message_container = jQuery("#message_container");


    var setupManufacturerList = function (url)
    {
        $.ajax({
            type    : "POST",
            url     : url,
            dataType: "json",
            success : function (data)
            {
                $cbo_criteria.empty();
                for (var i = 0; i < data.rows.length; i++)
                {
                    $cbo_criteria.append(jQuery("<option></option>").val(data.rows[i].cell[0]).text(data.rows[i].cell[1]));
                }
            },
            error   : function ()
            {
                $details_error.html("Error returning criteria list.");
            },
            complete: function ()
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

        var filter, criteria;

        if (action == 'search')
        {
            criteria = ($cbo_criteria.is(":visible")) ? $cbo_criteria.val() : $txt_criteria.val();
            filter = $criteria_filter.val();
        }
        else if (action == 'clear')
        {
            $criteria_filter.attr('selectedIndex', 2);
            $list_criteria.hide();
            $text_criteria.show();
            $txt_criteria.val('');
            $criteria_filter.change();
        }

        // Refresh the grid
        if ($pricing_filter.val() == 'toner')
        {
            $toners_table.hide();
            $toners_list.setGridParam({
                url     : TMTW_BASEURL + '/proposalgen/admin/tonerslist',
                postData: {
                    'filter'  : filter,
                    'criteria': criteria
                },
                page    : $hdn_page.val()
            });
            $toners_list.trigger("reloadGrid");


            setTimeout(function ()
            {
                $toners_table.show()
            }, 200);
        }
        else
        {
            $devices_table.hide();
            $devices_list.setGridParam({
                url     : TMTW_BASEURL + '/proposalgen/admin/masterdeviceslist',
                postData: {
                    'type'    : $pricing_filter.val(),
                    'filter'  : filter,
                    'criteria': criteria
                },
                page    : $hdn_page.val()
            });
            $devices_list.trigger('reloadGrid');

            setTimeout(function ()
            {
                $devices_table.show();
            }, 200);
        }
    };

    var apply_percentage = function ()
    {

        var sign = jQuery("#cboSign").val();
        var percentage = parseFloat($text_update.val());
        var decimalPlacesToShow = 4;

        // Get our jqGrid
        var jqGridSelector = ($pricing_filter.val() == 'toner') ? $toners_list : $devices_list;

        if (percentage > 0)
        {
            var ids = jqGridSelector.jqGrid('getGridParam', 'selarrrow');
            var jqGridRow = '';

            for (var i = 0; i < ids.length; i++)
            {
                jqGridRow = jqGridSelector.getRowData(ids[i]);
                if ($pricing_filter.val() == 'printer')
                {
                    /**
                     * Master Device Parts & Labor
                     */
                    var laborCostPerPageElement = jQuery("#laborCostPerPage" + jqGridRow.masterID);
                    var partsCostPerPageElement = jQuery("#partsCostPerPage" + jqGridRow.masterID);

                    var oldLaborCostPerPage = (jqGridRow.labor_cost_per_page_dealer > 0) ? jqGridRow.labor_cost_per_page_dealer : default_labor;
                    var oldPartsCostPerPage = (jqGridRow.parts_cost_per_page_dealer > 0) ? jqGridRow.parts_cost_per_page_dealer : default_parts;

                    if (sign == "-")
                    {
                        // Subtract
                        laborCostPerPageElement.val((oldLaborCostPerPage - (oldLaborCostPerPage * (percentage / 100))).toFixed(decimalPlacesToShow));
                        partsCostPerPageElement.val((oldPartsCostPerPage - (oldPartsCostPerPage * (percentage / 100))).toFixed(decimalPlacesToShow));
                    }
                    else
                    {
                        // Add
                        laborCostPerPageElement.val(((oldLaborCostPerPage * ((percentage / 100) + 1))).toFixed(decimalPlacesToShow));
                        partsCostPerPageElement.val(((oldPartsCostPerPage * ((percentage / 100) + 1))).toFixed(decimalPlacesToShow));
                    }
                }
                else
                {
                    /**
                     * Toner Pricing
                     */
                    var tonerPriceElement = jQuery("#txtTonerPrice" + jqGridRow.toner_id);
                    var tonerPrice = 0;
                    var newTonerPrice = 0;

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
                        newTonerPrice = tonerPrice - (tonerPrice * (percentage / 100));
                        tonerPriceElement.val(newTonerPrice.toFixed(decimalPlacesToShow));
                    }
                    else
                    {
                        newTonerPrice = (tonerPrice * ((percentage / 100) + 1));
                        tonerPriceElement.val(newTonerPrice.toFixed(decimalPlacesToShow));
                    }
                }
            }

            $message_container.empty();
        }
        else
        {
            $message_container.html("<div class='alert'>Please enter a percentage.</div>");
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
                width : 100,
                name  : 'labor_cost_per_page_dealer',
                index : 'labor_cost_per_page_dealer',
                label : 'Labor CPP',
                hidden: true
            },
            {
                width : 100,
                name  : 'parts_cost_per_page_dealer',
                index : 'parts_cost_per_page_dealer',
                label : 'Parts CPP',
                hidden: true
            },
            {
                width   : 100,
                name    : 'pretty_labor_cost_per_page_dealer',
                index   : 'pretty_labor_cost_per_page_dealer',
                label   : 'Labor CPP',
                align   : 'right',
                sorttype: 'int'
            },
            {
                width   : 100,
                name    : 'pretty_parts_cost_per_page_dealer',
                index   : 'pretty_parts_cost_per_page_dealer',
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
            // Update hdnPage
            $hdn_page.val($devices_list.jqGrid('getGridParam', 'page'));
        },
        gridComplete: function ()
        {
            var $grid = jQuery(this).jqGrid();
            var ids = $grid.getDataIDs();

            for (var i = 0; i < ids.length; i++)
            {
                var row = $grid.getRowData(ids[i]);

                // Reformat
                row.pretty_labor_cost_per_page_dealer = (parseFloat(row.labor_cost_per_page_dealer) > 0.0) ? "$ " + row.labor_cost_per_page_dealer : "$ -";
                row.pretty_parts_cost_per_page_dealer = (parseFloat(row.parts_cost_per_page_dealer) > 0.0) ? "$ " + row.parts_cost_per_page_dealer : "$ -";

                // Add text boxes to edit1
                row.new_labor_cost_per_page = "$ <input type='text' id='laborCostPerPage" + row.masterID + "' name='laborCostPerPage" + row.masterID + "' class='input-small text-right int-only' maxlength='12' />";
                row.new_parts_cost_per_page = "$ <input type='text' id='partsCostPerPage" + row.masterID + "' name='partsCostPerPage" + row.masterID + "' class='input-small text-right int-only' maxlength='12' />";
                $grid.setRowData(ids[i], row);
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
            // We keep our current page in a hidden page for when we repopulate
            $hdn_page.val($toners_list.jqGrid('getGridParam', 'page'));
        },
        gridComplete: function ()
        {
            var $grid = jQuery(this).jqGrid();
            var ids = $grid.getDataIDs();
            var maxMachinesToShow = 2;

            for (var i = 0; i < ids.length; i++)
            {
                var row = $grid.getRowData(ids[i]);

                row.new_toner_price = "$ <input type='text' name='txtTonerPrice" + row.toner_id + "' id='txtTonerPrice" + row.toner_id + "' class='input-mini int-only' maxlength='8' />";
                row.new_dealer_sku = "<input type='text' name='txtNewDealerSku" + row.toner_id + "' id='txtNewDealerSku" + row.toner_id + "' class='input-small' maxlength='255' />";

                var $output = jQuery('<div class="outer">');

                var pieces = row.device_list.split("; " + "");
                var firstLoopLength = (pieces.length > maxMachinesToShow) ? maxMachinesToShow : pieces.length;
                for (var j = 0; j < firstLoopLength; j++)
                {
                    $output.append(pieces[j]);
                    $output.append("<br>");
                }

                if (pieces.length > maxMachinesToShow)
                {
                    var $inner = jQuery('<div class="inner"></div>').hide();
                    for (j = maxMachinesToShow; j < pieces.length; j++)
                    {
                        $inner.append(pieces[j]);
                        $inner.append("<br>");
                    }
                    $output.append($inner);
                    $output.append('<a class="view-all-machine-compatibility">View All...</a>');
                }


                row.device_list = $output.html();

                $grid.setRowData(ids[i], row);
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
        if ($pricing_filter.val() == 'toner')
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
            setupManufacturerList(TMTW_BASEURL + '/proposalgen/admin/filterlistitems?list=man');

        }
    });

    $criteria_filter.change(function ()
    {
        var url = '';

        //clear searches
        $txt_criteria.val('');
        $cbo_criteria.html('');

        switch (this.value)
        {
            case "manufacturerId":
                url = TMTW_BASEURL + '/proposalgen/admin/filterlistitems?list=man';
                break;
            case "type_name":
                url = TMTW_BASEURL + '/proposalgen/admin/filterlistitems?list=type';
                break;
            case "toner_color_name":
                url = TMTW_BASEURL + '/proposalgen/admin/filterlistitems?list=color';
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
            $list_criteria.hide();
            $text_criteria.show();
        }

    });

    jQuery("#btnSearch").click(function ()
    {
        repop_page = 1;
        $hdn_page.val(1);
        $message_container.html('');
        setTimeout(function ()
        {
            update_grid('search');
        }, 300);
    });

    jQuery("#btnClearSearch").click(function ()
    {
        repop_page = 1;
        $hdn_page.val(1);
        $message_container.html('');
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

    $document.on('keypress', 'input.float-only', function (event)
    {
        return numbersonly(this, event, true);
    });

    $document.on('keypress', 'input.int-only', function (event)
    {
        return numbersonly(this, event, false);
    });

    /**
     * Function #1
     * Toggle display machine compatibility
     */
    $document.on('click', 'a.view-all-machine-compatibility', function (event)
    {
        event.preventDefault();
        var $link = jQuery(this);
        var $inner = $link.siblings(".inner");
        $inner.toggle();
        var linkText = ($inner.is(":visible")) ? "Collapse..." : "View All...";
        $link.text(linkText);
    });

    $criteria_filter.change();
});