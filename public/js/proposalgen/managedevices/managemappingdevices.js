if (form_mode == '')
{
    form_mode = 'edit';
}

(function ($)
{
    $.widget("ui.combobox", { _create: function ()
    {
        var self = this,
            select = this.element.hide(),
            selected = select.children(":selected"),
            value = selected.val() ? selected.text() : "";
        var input = this.input = $("<input size='23'>")
            .insertAfter(select)
            .val(value)
            .autocomplete({
                delay    : 0,
                minLength: 0,
                source   : function (request, response)
                {
                    var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
                    response(select.children("option").map(function ()
                    {
                        var text = $(this).text();
                        if (this.value && ( !request.term || matcher.test(text) ))
                        {
                            return {
                                label : text.replace(
                                    new RegExp(
                                        "(?![^&;]+;)(?!<[^<>]*)(" +
                                            $.ui.autocomplete.escapeRegex(request.term) +
                                            ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                    ), "<strong>$1</strong>"),
                                value : text,
                                option: this
                            };
                        }
                    }));
                },
                select   : function (event, ui)
                {
                    ui.item.option.selected = true;
                    self._trigger("selected", event, {
                        item: ui.item.option
                    });
                },
                change   : function (event, ui)
                {
                    if (!ui.item)
                    {
                        var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex($(this).val()) + "$", "i"),
                            valid = false;
                        select.children("option").each(function ()
                        {
                            if ($(this).text().match(matcher))
                            {
                                this.selected = valid = true;
                                return false;
                            }
                        });
                        if (!valid)
                        {
                            // remove invalid value, as it didn't match anything
                            $(this).val("");
                            select.val("");
                            input.data("autocomplete").term = "";
                            return false;
                        }
                    }
                }
            })
            .addClass("ui-widget ui-widget-content ui-corner-left");

        input.data("autocomplete")._renderItem = function (ul, item)
        {
            return $("<li></li>")
                .data("item.autocomplete", item)
                .append("<a>" + item.label + "</a>")
                .appendTo(ul);
        };

        this.button = $("<button type='button'>&nbsp;</button>")
            .attr("tabIndex", -1)
            .attr("title", "Show All Items")
            .insertAfter(input)
            .button({
                icons: {
                    primary: "ui-icon-triangle-1-s"
                },
                text : false
            })
            .removeClass("ui-corner-all")
            .addClass("ui-corner-right ui-button-icon")
            .click(function ()
            {
                // close if already visible
                if (input.autocomplete("widget").is(":visible"))
                {
                    input.autocomplete("close");
                    return;
                }

                // work around a bug (likely same cause as #5265)
                $(this).blur();

                // pass empty string as value to search for, displaying all results
                input.autocomplete("search", "");
                input.focus();
            });
    },

        destroy: function ()
        {
            this.input.remove();
            this.button.remove();
            this.element.show();
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);

$(document).ready(function ()
{
    $("#launch_date").datepicker({ dateFormat: 'mm/dd/yy', changeMonth: true, changeYear: true});
    //find center screen for modal popup
    var master_device_id = $("#printer_model").val();
    var lastsel;

    jQuery("#applied_toners_list").jqGrid({
        url         : TMTW_BASEURL + '/proposalgen/admin/devicetoners?deviceid=' + master_device_id,
        datatype    : 'json',
        colNames    : ['Toner ID', 'SKU', 'Manufacturer', 'Type', 'Color', 'Yield', 'Price', 'MasterID', 'Added', 'Action'],
        colModel    : [
            {width: 30, name: 'toner_id', index: 'toner_id', sorttype: 'int', hidden: true, editable: true, editoptions: {readonly: true, size: 12}, sortable: false},
            {width: 60, name: 'toner_sku', index: 'toner_sku', editable: true, editoptions: {size: 12, maxlength: 30}, sortable: false},
            {width: 120, name: 'manufacturer_id', index: 'toner_manufacturer', sortable: false},
            {width: 100, name: 'toner_color_id', index: 'toner_color_id', sortable: false},
            {width: 60, name: 'toner_yield', index: 'toner_yield', align: 'right', sortable: false},
            {width: 80, name: 'toner_price', index: 'toner_price', align: 'right', formatter: 'currency', formatoptions: {prefix: "$", thousandsSeparator: ","}, sortable: false},
            {width: 50, name: 'master_device_id', index: 'master_device_id', hidden: true, sortable: false},
            {width: 50, name: 'is_added', index: 'is_added', hidden: true, sortable: false},
            {width: 60, name: 'action', index: 'action', align: 'center', sortable: false}
        ],
        width       : 940,
        height      : 'auto',
        gridComplete: function ()
        {
            var toner_array = '';
            var ids = jQuery("#applied_toners_list").jqGrid('getDataIDs');
            for (var i = 0; i < ids.length; i++)
            {
                var cur_row = ids[i];
                var is_added = document.getElementById("applied_toners_list").rows[i + 1].cells[8].innerHTML;
                remove_button = '<input type="button" name="btnRemove' + cur_row + '" id="btnRemove' + cur_row + '" tag="Remove" value="Remove" class="btn" onclick="javascript: do_remove(' + cur_row + ');" />';
                jQuery("#applied_toners_list").jqGrid('setRowData', ids[i], {action: remove_button});

                if (toner_array != '')
                {
                    toner_array = toner_array + ",";
                }
                toner_array = toner_array + "'" + cur_row + "'";
            }
            $("#toner_array").val(toner_array);
        }
    });

    jQuery("#applied_toners_list").jqGrid('navGrid', '#applied_toners_pager',
        {add: false, del: false, edit: false, refresh: false, search: false},
        {closeAfterEdit: false, recreateForm: true, reloadAfterSubmit: true, closeOnEscape: true, width: 400,
            afterSubmit: function (response, postdata)
            {
                if (response.responseText)
                {
                    $("#FormError > td", "#editcntapplied_toners_list").html(response.responseText).show();
                    $("#FormError").css('display', 'table-row');
                    return false;
                }
                return true;
            }
        },
        {closeAfterAdd : false, recreateForm: true, reloadAfterSubmit: true, closeOnEscape: true, width: 400,
            afterSubmit: function (response, postdata)
            {
                if (response.responseText)
                {
                    $("#FormError > td", "#editcntapplied_toners_list").html(response.responseText).show();
                    $("#FormError").css('display', 'table-row');
                    return false;
                }
                return true;
            }
        },
        {},
        {},
        {}
    );

    jQuery("#available_toners_list").jqGrid({
        url         : TMTW_BASEURL + '/proposalgen/admin/tonerslist?deviceid=' + master_device_id,
        datatype    : 'json',
        colNames    : ['Toner ID', 'SKU', 'Manufacturer', 'Type', 'Color', 'Yield', 'Price', 'MasterID', 'Added', 'Action', 'Apply To Printer', 'Machine Compabibility'],
        colModel    : [
            {tag: 0, width: 30, name: 'toner_id', index: 'toner_id', sorttype: 'int', hidden: true, editable: true, editoptions: {readonly: true, size: 12}},
            {tag: 1, width: 60, name: 'toner_sku', index: 'toner_sku', editable: true, editoptions: {size: 12, maxlength: 30}},
            {tag: 2, width: 120, name: 'manufacturer_id', index: 'toner_manufacturer', edittype: 'select', editable: true, editoptions: {value: manufacturerList}},
            {tag: 4, width: 100, name: 'toner_color_id', index: 'tonerColorId', edittype: 'select', editable: true, editoptions: {value: colorList}},
            {tag: 5, width: 60, name: 'toner_yield', index: 'yield', editable: true, editoptions: {size: 10, maxlength: 5}, align: 'right', sorttype: 'int'},
            {tag: 6, width: 80, name: 'toner_price', index: 'toner_price', editable: true, editoptions: {size: 10, maxlength: 8}, align: 'right', formatter: 'currency', formatoptions: {prefix: "$", thousandsSeparator: ","}, sorttype: 'int'},
            {tag: 7, width: 50, name: 'master_device_id', index: 'master_device_id', hidden: true, editable: true, editoptions: {size: 12}},
            {tag: 8, width: 50, name: 'is_added', index: 'is_added', editable: true, hidden: true, editoptions: {size: 12}},
            {tag: 9, width: 60, name: 'action', index: 'action', editable: false, align: 'center'},
            {tag: 10, width: 50, name: 'machine_compatibility', index: 'machine_compatibility', hidden: true},
            {tag: 11, width: 60, name: 'apply', index: 'apply', hidden: true, edittype: 'checkbox', editable: true, align: 'center'}
        ],
        width       : 940,
        height      : 150,
        rowNum      : 10,
        rowList     : [10, 20, 30],
        pager       : '#available_toners_pager',
        gridComplete: function ()
        {
            var ids = jQuery("#available_toners_list").jqGrid('getDataIDs');
            var toner_array = $("#toner_array").val();

            for (var i = 0; i < ids.length; i++)
            {
                var cur_row = ids[i];
                var is_added = document.getElementById("available_toners_list").rows[i + 1].cells[8].innerHTML;

                add_button = '<input type="button" name="btnAdd' + cur_row + '" id="btnAdd' + cur_row + '" tag="Add" value="Assign" class="btn" onclick="javascript: do_add(' + cur_row + ');" />';
                disabled_button = '<input type="button" name="btnAdd' + cur_row + '" id="btnAdd' + cur_row + '" tag="Add" value="Assign" class="btn" onclick="javascript: do_add(' + cur_row + ');" disabled="disabled" />';

                if (toner_array.indexOf("'" + cur_row + "'") != -1)
                {
                    jQuery("#available_toners_list").jqGrid('setRowData', ids[i], {action: disabled_button});
                }
                else
                {
                    jQuery("#available_toners_list").jqGrid('setRowData', ids[i], {action: add_button});
                }
            }
        },
        editurl     : TMTW_BASEURL + '/proposalgen/admin/edittoner?deviceid=' + master_device_id
    });

    jQuery("#available_toners_list").jqGrid('navGrid', '#available_toners_pager',
        {add: true, del: true, edit: true, refresh: false, search: false},
        {closeAfterEdit   : false, recreateForm: true, reloadAfterSubmit: true, closeOnEscape: true, width: 400, caption: 'Edit Toner',
            beforeShowForm: function ()
            {
                sTop = $(document).height() - ($(document).height() / 2);
                sLeft = ($(window).width() / 2) - 200;
                $("#editmodavailable_toners_list").css('top', sTop);
                $("#editmodavailable_toners_list").css('left', sLeft);
                $("#toner_color_id").attr('disabled', 'disabled');
            },
            afterSubmit   : function (response, postdata)
            {
                if (response.responseText)
                {
                    $("#FormError > td", "#editcntavailable_toners_list").html(response.responseText).show();
                    $("#FormError").css('display', 'table-row');
                    return false;
                }
                return true;
            },
            afterComplete : function ()
            {
                update_applied();
            }
        },
        {closeAfterAdd    : false, recreateForm: true, reloadAfterSubmit: true, closeOnEscape: true, width: 400, caption: 'Add Toner',
            beforeShowForm: function ()
            {
                sTop = $(document).height() - ($(document).height() / 2);
                sLeft = ($(window).width() / 2) - 200;
                $("#editmodavailable_toners_list").css('top', sTop);
                $("#editmodavailable_toners_list").css('left', sLeft);
                $("#toner_color_id").removeAttr('disabled');
            },
            afterSubmit   : function (response, postdata)
            {
                if (response.responseText)
                {
                    $("#FormError > td", "#editcntavailable_toners_list").html(response.responseText).show();
                    $("#FormError").css('display', 'table-row');
                    return false;
                }
                return true;
            },
            afterComplete : function ()
            {
                update_applied();
            }
        },
        {closeAfterAdd    : false, recreateForm: true, reloadAfterSubmit: true, closeOnEscape: true,
            beforeShowForm: function ()
            {
                //close dialog
                $("#delmodavailable_toners_list").remove();

                var grid = $("#available_toners_list");
                var rowid = grid.jqGrid('getGridParam', 'selrow');

                // check device toner count for color
                var url = TMTW_BASEURL + '/proposalgen/admin/devicetonercount?tonerid=' + rowid;
                $.ajax({
                    url    : url,
                    success: function (data)
                    {
                        var obj = jQuery.parseJSON(data);
                        var total_devices = obj.total_count;
                        var num_devices = obj.device_count;
                        if (num_devices > 0 || total_devices > 0)
                        {
                            lightbox('show');
                            //get selected row id (toner_id)
                            $("#replace_toner_id").val(rowid);
                            //return list of replacement toners for current toner
                            update_replacement();
                        }
                        else
                        {
                            //use default dialog
                            $('#replace_toner_id').val(rowid);
                            $('#replace_mode').val('no_replace');
                            if (confirm("Are you sure you want to delete this toner?"))
                            {
                                do_action('replace_toner');
                            }
                        }
                    },
                    error  : function ()
                    {
                        $('#message_container').html("Error checking report count.");
                    }
                });
            },
            afterComplete : function (response)
            {
                if (response.responseText)
                {
                    $("#modal_message_container").html(response.responseText).show();
                }
            }
        },
        {},
        {}
    );

    jQuery("#replacement_toners_list").jqGrid({
        url         : TMTW_BASEURL + '/proposalgen/admin/replacementtoners?tonerid=0',
        datatype    : 'json',
        colNames    : ['Toner ID', 'SKU', 'Manufacturer', 'Type', 'Color', 'Yield', 'Price', 'Num', 'Total'],
        colModel    : [
            {width: 30, name: 'toner_id', index: 'toner_id', sorttype: 'int', hidden: true, editable: true, editoptions: {readonly: true, size: 12}},
            {width: 60, name: 'toner_sku', index: 'toner_sku', editable: true, editoptions: {size: 12, maxlength: 30}},
            {width: 120, name: 'manufacturer_id', index: 'manufacturer_id'},
            {width: 100, name: 'toner_color_id', index: 'toner_color_id'},
            {width: 60, name: 'toner_yield', index: 'toner_yield', align: 'right'},
            {width: 80, name: 'toner_price', index: 'toner_price', align: 'right', formatter: 'currency', formatoptions: {prefix: "$", thousandsSeparator: ","}},
            {width: 50, name: 'num_devices', index: 'num_devices', hidden: true},
            {width: 50, name: 'total_devices', index: 'total_devices', hidden: true}
        ],
        width       : 500,
        height      : 'auto',
        rowNum      : 10,
        rowList     : [10, 20, 30],
        pager       : '#replacement_toners_pager',
        onSelectRow : function (id, status)
        {
            if (status == false)
            {
                jQuery("#replacement_toners_list").jqGrid('resetSelection');
                id = 0;
            }
            $("#with_toner_id").val(id);
        },
        gridComplete: function ()
        {
            //update statement
            if (document.getElementById("replacement_toners_list").rows[1] !== undefined)
            {
                num_devices = document.getElementById("replacement_toners_list").rows[1].cells[7].innerHTML;
                total_devices = document.getElementById("replacement_toners_list").rows[1].cells[8].innerHTML;
            }
            else
            {
                num_devices = 0;
                total_devices = 0;
            }
            if (num_devices == 0 && total_devices == 0)
            {
                //should never hit this as it's handled by the confirm box instead
                $('#replacement_toners_container').hide();
                $('#replace_all_container').hide();
                $('#replacement_filter_container').hide();
                $('#replace_mode').val('no_replace');
                $('#replacement_title').html('Confirm Toner Deletion');
                $('#replacement_toners_statement').html('Are you sure you want to delete this toner?');
            }
            else if (num_devices == 0)
            {
                $('#replacement_toners_container').show();
                $('#replace_all_container').hide();
                $('#replacement_filter_container').show();
                $('#replace_mode').val('optional_replace');
                $('#replacement_title').html('Confirm Toner Deletion');
                $('#replacement_toners_statement').html('This toner is being used by ' + total_devices + ' device(s). If you wish to assign a replacement toner to apply to all ' + total_devices + ' device(s), please select it from the list below.');
            }
            else
            {
                $('#replacement_toners_container').show();
                $('#replace_all_container').show();
                $('#replacement_filter_container').show();
                $('#replace_mode').val('require_replace');
                $('#replacement_title').html('Select Replacement Toner');
                $('#replacement_toners_statement').html(num_devices + ' of the ' + total_devices + ' device(s) using this toner require a replacement toner to be assigned. Please select a replacement toner from the list below.');
            }
        }
    });

    jQuery("#replacement_toners_list").jqGrid('navGrid', '#replacement_toners_pager',
        {add: false, del: false, edit: false, refresh: false, search: false},
        {},
        {},
        {},
        {}
    );

    $("#manufacturer_id").change(function (event)
    {
        $('#applied_toners_list').clearGridData();
        var manufacturerid = $("#manufacturer_id").val();
        var printer_model_id = $("#printer_model").val();
        var url = TMTW_BASEURL + '/proposalgen/admin/printermodels?manufacturerid=' + manufacturerid;

        $.ajax({
            type       : "POST",
            contentType: "application/json; charset=utf-8",
            url        : url,
            dataType   : "json",
            success    : function (models)
            {
                //empty out printer_model
                $("#printer_model").html('');
                var listItems = "";
                var selected = selectedPrinterModel;
                listItems += "<option value=''></option>";
                for (var i = 0; i < models.rows.length; i++)
                {
                    selected_code = "";
                    if (selected && selected == models.rows[i].cell[0])
                    {
                        selected_code = " selected ";
                    }
                    listItems += "<option value='" + models.rows[i].cell[0] + "'" + selected_code + ">" + models.rows[i].cell[1] + "</option>";
                }
                $("#printer_model").html(listItems);
            },
            error      : function ()
            {
                $('#message_container').html("ERROR IN PRINTERMODELS");
            },
            complete   : function ()
            {
                if (manufacturerid > 0)
                {
                    $("#add_link").show();
                    if (adminEdit == 1)
                    {
                        $("#add_link").text('');
                        $("#edit_man_link").text('');
                    }
                    else
                    {
                        $("#printer_model").removeAttr('disabled');
                    }
                }
                else
                {
                    if ($("#edit_link").css('display') != 'none')
                    {
                        add_printer(false);
                        if (repop != 1)
                        {
                            empty_form();
                            disable_form();
                            $("#add_link").hide();
                        }
                    }
                }
                $("#printer_model").change();
            }
        });
    });

    $("#printer_model").change(function (event)
    {
        $('#applied_toners_list').clearGridData();

        var master_device_id = $("#printer_model").val();
        if (form_mode == "add")
        {
            master_device_id = 0;
        }

        //GET DEVICE DETAILS
        $.ajax({
            beforeSend: function ()
            {
                disable_form();
            },
            url       : TMTW_BASEURL + '/proposalgen/admin/devicedetails?deviceid=' + master_device_id,
            success   : function (data)
            {
                if (form_mode != 'add' && repop != 1)
                {
                    $("#launch_date").val(data.launch_date);
                    $("#device_price").val(data.device_price);
                    $("#toner_config_id").val(data.toner_config_id);
                    $("#is_copier").attr('checked', data.is_copier);
                    $("#is_scanner").attr('checked', data.is_scanner);
                    $("#reportsTonerLevels").attr('checked', data.reportsTonerLevels);
                    $("#is_fax").attr('checked', data.is_fax);
                    $("#is_duplex").attr('checked', data.is_duplex);
                    $("#watts_power_normal").val(data.watts_power_normal);
                    $("#watts_power_idle").val(data.watts_power_idle);
                    $("#toner_array").val(data.toner_array);
                    $("#is_leased").attr('checked', data.is_leased);
                    $("#leased_toner_yield").val(data.leased_toner_yield);
                    $("#ppm_black").val(data.ppm_black);
                    $("#ppm_color").val(data.ppm_color);
                    $("#duty_cycle").val(data.duty_cycle);
                }
            },
            error     : function ()
            {
                $('#message_container').html("ERROR IN DEVICEDETAILS");
            },
            complete  : function ()
            {
                if ($("#printer_model").val() > 0 || $("#form_mode").val() == "add")
                {
                    enable_form();
                }

                //update instruction
                $("#toner_config_id").change();

                toggle_leased($("#is_leased").is(":checked"));

                //update display
                update_label();
                update_grids();

                //reset repop flag after load
                repop = 0;

                //finished reloading form after delete... reset to edit
                if (form_mode == 'delete')
                {
                    form_mode = 'edit';
                }
            }
        });
    });

    $("#toner_config_id").change(function (event)
    {
        $("#toner_instruction").show();
        switch (this.value)
        {
            case '1':
                $("#toner_instruction_config").html('one Black Toner');
                break;
            case '2':
                $("#toner_instruction_config").html('one Black, one Yellow, one Magenta and one Cyan Toner');
                break;
            case '3':
                $("#toner_instruction_config").html('one Black and one 3 Color Toner');
                break;
            case '4':
                $("#toner_instruction_config").html('one 4 Color Toner');
                break;
            default:
                $("#toner_instruction").hide();
                break;
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
            case "manufacturer_name":
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
        else
        {
            $("#list_criteria").hide();
            $("#text_criteria").show();
        }
    });

    $("#replacement_criteria_filter").change(function ()
    {
        var url = '';

        //clear searches
        $("#replacement_txtCriteria").val('');
        $("#replacement_cboCriteria").html('');

        switch (this.value)
        {
            case "manufacturer_name":
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
                    $("#replacement_cboCriteria").html(options);
                },
                error      : function ()
                {
                    $('#details_error').html("Error returning criteria list.");
                },
                complete   : function ()
                {
                    $("#replacement_text_criteria").hide();
                    $("#replacement_list_criteria").show();
                }
            });

        }
        else
        {
            $("#replacement_list_criteria").hide();
            $("#replacement_text_criteria").show();
        }
    });

    if (form_mode == 'add')
    {
        $("#printer_model-label").hide();
        $("#printer_model-element").hide();
        $("#new_printer-label").show();
        $("#new_printer-element").show();
    }
    else if (form_mode == 'delete')
    {
        $("#manufacturer_id").change();
        $("#new_printer-label").hide();
        $("#new_printer-element").hide();
    }
    else
    {
        if (repop != 1)
        {
            disable_form();
            $("#add_link").hide();
        }
    }

    if ($("#printer_model").val() == '')
    {
        $("#btnRefresh").attr('disabled', 'disabled');
        $("#btnDelete").attr('disabled', 'disabled');
    }

    if (form_mode == 'edit')
    {
        $("#printer_model-label").show();
        $("#printer_model-element").show();
        $("#new_printer-label").hide();
        $("#new_printer-element").hide();
    }

    // If a system administrator is editing a device, lock the model information.
    if (adminEdit == 1)
    {
        toggle_leased($('#is_leased').is(':checked'));
        $("#manufacturer_id").change();
        $("#manufacturer_id").attr('disabled', 'disabled');
        $("#printer_model").attr('disabled', 'disabled');

    }
    else if (repop == 1)
    {
        toggle_leased($('#is_leased').is(':checked'));
        $("#manufacturer_id").change();
    }
    else
    {
        empty_form();
    }

    //SET DEFAULTS
    $("#leasd_toner_yield-label").hide();
    $("#leasd_toner_yield-element").hide();

    $("#is_replacement_device-label").hide();
    $("#is_replacement_device-element").hide();

    $("#modal_message_container").hide();
});

function update_label()
{
    var manufacturer = $("#manufacturer_id option:selected").text();
    var model = $("#printer_model option:selected").text();
    if (form_mode != 'add' && $("#printer_model").val() > 0)
    {
        $("#devicename").html('Toner Assigned to ' + manufacturer + ' ' + model + ':');
    }
    else
    {
        $("#devicename").html('Toner Assignment:');
    }
}

function add_printer(mode)
{
    if (repop != 1 && $("#manufacturer_id").val() > 0)
    {
        empty_form();
    }

    if (mode == true)
    {
        form_mode = 'add';
        $("#printer_model-label").hide();
        $("#printer_model-element").hide();
        $("#new_printer-label").show();
        $("#new_printer-element").show();
    }
    else
    {
        if (form_mode != 'delete')
        {
            form_mode = 'edit';
        }
        $("#printer_model-label").show();
        $("#printer_model-element").show();
        $("#new_printer-label").hide();
        $("#new_printer-element").hide();
    }

    $("#form_mode").val(form_mode);
    $("#printer_model").change();
}

function empty_form()
{
    if (form_mode != 'delete' && repop != 1)
    {
        clear_validation();
    }

    $("#toner_array").val("'0'");
    $("#new_printer").val('');
    $("#launch_date").val('');
    $("#device_price").val('');
    $("#toner_config_id").val('');
    $("#is_copier").removeAttr('checked');
    $("#is_scanner").removeAttr('checked');
    $("#reportsTonerLevels").removeAttr('checked');
    $("#is_fax").removeAttr('checked');
    $("#is_duplex").removeAttr('checked');
    $("#ppm_black").val('');
    $("#ppm_color").val('');
    $("#duty_cycle").val('');
    $("#watts_power_normal").val('');
    $("#watts_power_idle").val('');
    $("#is_leased").removeAttr('checked');
    $("#leased_toner_yield").val('');
    $("#partsCostPerPage").val('');
    $("#laborCostPerPage").val('');
    $("#btnSave").attr('disabled', 'disabled');
    $("#btnRefresh").attr('disabled', 'disabled');
    $("#btnDelete").attr('disabled', 'disabled');

    toggle_leased(false);
    //update_applied();
}

function disable_form()
{
    if ($("#manufacturer_id").val() > 0)
    {
        if (adminEdit == 1)
        {
            $("#add_link").text('');
            $("#edit_man_link").text('');
        }
        else
        {
            $("#printer_model").removeAttr('disabled');
        }
    }
    else
    {
        //$("#printer_model").attr('disabled','disabled');
    }

    $("#launch_date").attr('disabled', 'disabled');
    $("#device_price").attr('disabled', 'disabled');
    $("#toner_config_id").attr('disabled', 'disabled');
    $("#is_copier").attr('disabled', 'disabled');
    $("#is_scanner").attr('disabled', 'disabled');
    $("#reportsTonerLevels").attr('disabled', 'disabled');
    $("#is_fax").attr('disabled', 'disabled');
    $("#is_duplex").attr('disabled', 'disabled');
    $("#ppm_black").attr('disabled', 'disabled');
    $("#ppm_color").attr('disabled', 'disabled');
    $("#duty_cycle").attr('disabled', 'disabled');
    $("#watts_power_normal").attr('disabled', 'disabled');
    $("#watts_power_idle").attr('disabled', 'disabled');
    $("#is_leased").attr('disabled', 'disabled');
    $("#leased_toner_yield").attr('disabled', 'disabled');
    $("#partsCostPerPage").attr('disabled', 'disabled');
    $("#laborCostPerPage").attr('disabled', 'disabled');
    $("#btnSave").attr('disabled', 'disabled');
    $("#btnRefresh").attr('disabled', 'disabled');
    $("#btnDelete").attr('disabled', 'disabled');
}

function enable_form()
{
    if ($("#manufacturer_id").val() > 0)
    {
        if (adminEdit == 1)
        {
            $("#add_link").text('');
            $("#edit_man_link").text('');
        }
        else
        {
            $("#printer_model").removeAttr('disabled');
        }
    }
    else
    {
        //$("#printer_model").attr('disabled','disabled');
    }

    $("#launch_date").removeAttr('disabled');
    $("#device_price").removeAttr('disabled');
    $("#toner_config_id").removeAttr('disabled');
    $("#is_copier").removeAttr('disabled');
    $("#is_scanner").removeAttr('disabled');
    $("#reportsTonerLevels").removeAttr('disabled');
    $("#is_fax").removeAttr('disabled');
    $("#is_duplex").removeAttr('disabled');
    $("#ppm_black").removeAttr('disabled');
    $("#ppm_color").removeAttr('disabled');
    $("#duty_cycle").removeAttr('disabled');
    $("#watts_power_normal").removeAttr('disabled');
    $("#watts_power_idle").removeAttr('disabled');
    $("#is_leased").removeAttr('disabled');
    $("#leased_toner_yield").removeAttr('disabled');
    $("#partsCostPerPage").removeAttr('disabled');
    $("#laborCostPerPage").removeAttr('disabled');
    $("#btnSave").removeAttr('disabled');
    if ($("#printer_model").val() > 0)
    {
        $("#btnRefresh").removeAttr('disabled');
        $("#btnDelete").removeAttr('disabled');
    }
}

function clear_validation()
{
    //$('ul li').remove();
    $("#message_container").html('');
    $("#modal_message_container").html('').hide();
}

function show_message(message)
{
    $("#modal_message_container").html('').hide();
    $("#message_container").html(message).show();
}

function do_add(id)
{
    $('#message_container').html('');

    var isvalid = true;
    var toner_id = id;
    var toner_sku = $("#toner_sku").val();
    var manufacturer_id = $("#new_manufacturer_id").val();
    var toner_color_id = $("#toner_color_id").val();
    var toner_yield = $("#toner_yield").val();
    var toner_price = $("#toner_price").val();

    //update array
    var toner_array = $("#toner_array").val();

    //check to see if toner exists
    if (toner_array.indexOf("'" + id + "'") == -1)
    {
        if (toner_array != '')
        {
            toner_array += ",";
        }
        toner_array = toner_array + "'" + id + "'";
        $("#toner_array").val(toner_array);
        update_applied();
        $("#btnAdd" + id).attr('disabled', 'disabled');
    }
    else
    {
        show_message("Toner already exists.");
    }
}

function do_remove(id)
{
    $('#message_container').html('');

    if (confirm("Are you sure you want to remove this toner?"))
    {
        //update array
        var toner_array = $("#toner_array").val();
        toner_array = toner_array.replace("'" + id + "',", "").replace(",'" + id + "'", "").replace("'" + id + "'", "");
        if (toner_array == '')
        {
            toner_array = '0';
        }
        $("#toner_array").val(toner_array);
        update_applied();
        $("#btnAdd" + id).removeAttr('disabled');
    }
}

function do_action(inAction)
{
    if (inAction == 'save')
    {
        $("#save_flag").val('save');
        if (adminEdit == 1)
        {
            $("#manufacturer_id").removeAttr('disabled');
            $("#printer_model").removeAttr('disabled');
        }
        $("#device_form").submit();

    }
    else if (inAction == 'done')
    {
        document.location.href = TMTW_BASEURL + '/proposalgen/fleet/mapping/rmsUploadId/' + rmsUploadId;

    }
    else if (inAction == 'manufacturer')
    {
        $("#form_mode").val(inAction);
        $("#device_form").attr('action', '../admin/manufacturers');
        $("#device_form").submit();

    }
    else if (inAction == 'replace_toner')
    {
        //update hidden fields to pass to action
        var replace_mode = $("#replace_mode").val();
        var replace_toner_id = $("#replace_toner_id").val();
        var with_toner_id = $("#with_toner_id").val();
        var apply_all = $("#chkAllToners").is(":checked");

        if (replace_mode == 'require_replace' && (with_toner_id == "null" || with_toner_id <= 0))
        {
            $("#replacement_message").html("You must select a replacement toner.");

        }
        else
        {
            lightbox('hide');
            $("#replacement_message").html("");
            var url = TMTW_BASEURL + '/proposalgen/admin/replacetoner?replace_mode=' + replace_mode + '&replace_toner_id=' + replace_toner_id + '&with_toner_id=' + with_toner_id + '&chkAlLToners=' + apply_all;
            $.ajax({
                url     : url,
                success : function (data)
                {
                    $('#message_container').html(data);
                },
                error   : function ()
                {
                    $('#message_container').html("Error deleting toners.");
                },
                complete: function ()
                {
                    toner_array = $("#toner_array").val();
                    toner_array = toner_array.replace("'" + replace_toner_id + "'", "'" + with_toner_id + "'");
                    $("#toner_array").val(toner_array);
                    update_grids();
                }
            });
        }
    }
}

function update_grids()
{
    update_applied();
    update_available('clear');
}

function update_applied()
{
    var toner_array = $("#toner_array").val();
    var master_device_id = $("#printer_model").val();

    if (form_mode == 'add')
    {
        master_device_id = 0;
    }

    $('#applied_toners_list').setGridParam({url: TMTW_BASEURL + '/proposalgen/admin/devicetoners?deviceid=' + master_device_id + '&list=' + toner_array.replace(/'/gi, '')});
    $('#applied_toners_list').trigger('reloadGrid');
}

function update_available(action)
{
    var params = '&filter=&criteria=';
    if (action == 'search')
    {
        if ($("#cboCriteria").is(":visible"))
        {
            criteria = $("#cboCriteria option:selected").text();
        }
        else
        {
            criteria = $("#txtCriteria").val();
        }
        params = '&filter=' + $("#criteria_filter").val() + '&criteria=' + criteria;
    }
    else if (action == 'clear')
    {
        $("#criteria_filter").attr('selectedIndex', 0);
        $("#list_criteria").hide();
        $("#text_criteria").show();
        $("#cboCriteria").html('');
        $("#txtCriteria").val('');
    }

    var master_device_id = $("#printer_model").val();
    $('#available_toners_list').setGridParam({url: TMTW_BASEURL + '/proposalgen/admin/tonerslist?deviceid=' + master_device_id + params});
    $('#available_toners_list').setGridParam({editurl: TMTW_BASEURL + '/proposalgen/admin/edittoner?deviceid=' + master_device_id});
    colModel:[
        {width: 30, name: 'toner_id', index: 'toner_id', sorttype: 'int', hidden: true, editable: true, editoptions: {readonly: true, size: 12}},
        {width: 60, name: 'toner_sku', index: 'toner_sku', editable: true, editoptions: {size: 12, maxlength: 30}},
        {width: 120, name: 'manufacturer_id', index: 'manufacturer_id', edittype: 'select', editable: true, editoptions: {value: manufacturerList}},
        {width: 100, name: 'toner_color_id', index: 'toner_color_id', edittype: 'select', editable: true, editoptions: {value: colorList}},
        {width: 60, name: 'toner_yield', index: 'toner_yield', editable: true, editoptions: {size: 10, maxlength: 4}, align: 'right'},
        {width: 80, name: 'toner_price', index: 'toner_price', editable: true, editoptions: {size: 10, maxlength: 8}, align: 'right', formatter: 'currency', formatoptions: {prefix: "$", thousandsSeparator: ","}},
        {width: 50, name: 'master_device_id', index: 'master_device_id', hidden: true, editable: true, editoptions: {size: 12}},
        {width: 50, name: 'is_added', index: 'is_added', editable: true, hidden: true, editoptions: {size: 12}},
        {width: 60, name: 'action', index: 'action', editable: false, align: 'center'},
        {width: 60, name: 'apply', index: 'apply', hidden: true, edittype: 'checkbox', editable: true, align: 'center'}
    ],
        $('#available_toners_list').trigger('reloadGrid');
}

function update_replacement(action)
{
    var params = '&filter=&criteria=';
    if (action == 'search')
    {
        if ($("#replacement_cboCriteria").is(":visible"))
        {
            criteria = $("#replacement_cboCriteria option:selected").text();
        }
        else
        {
            criteria = $("#replacement_txtCriteria").val();
        }
        params = '&filter=' + $("#replacement_criteria_filter").val() + '&criteria=' + criteria;
    }
    else if (action == 'clear')
    {
        $("#replacement_criteria_filter").attr('selectedIndex', 0);
        $("#replacement_list_criteria").hide();
        $("#replacement_text_criteria").show();
        $("#replacement_cboCriteria").html('');
        $("#replacement_txtCriteria").val('');
    }

    var grid = $("#available_toners_list");
    var toner_id = grid.jqGrid('getGridParam', 'selrow');
    $('#replacement_toners_list').setGridParam({url: TMTW_BASEURL + '/proposalgen/admin/replacementtoners?tonerid=' + toner_id + params});
    $('#replacement_toners_list').trigger('reloadGrid');
}

function toggle_leased(mode)
{
    if (mode)
    {
        $("#leased_toner_yield-label").show();
        $("#leased_toner_yield-element").show();
    }
    else
    {
        $("#leased_toner_yield-label").hide();
        $("#leased_toner_yield-element").hide();
    }
}

function lightbox(mode)
{
    if (mode == 'show')
    {
        $("html, body").animate({scrollTop: 0}, 'slow');
        $("body").css("overflow", "hidden");
        $("#lightbox, #lightbox-panel").fadeIn(300);
    }
    else if (mode == 'hide')
    {
        $("#lightbox, #lightbox-panel").fadeOut(300);
        $("body").css("overflow", "auto");
    }
}