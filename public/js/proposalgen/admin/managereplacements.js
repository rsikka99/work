if (form_mode == '')
{
    form_mode = 'edit';
}

$(document).ready(function ()
{

    //find center screen for modal popup
    var sTop = ($(window).height() / 2) - 100;
    var sLeft = ($(window).width() / 2) - 200;

    (function ($)
    {
        $.widget("ui.combobox", {
            _create: function ()
            {
                var self = this,
                    select = this.element.hide(),
                    selected = select.children(":selected"),
                    value = selected.val() ? selected.text() : "";
                var input = this.input = $("<input size='25'>")
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

    jQuery("#grid_list").jqGrid({
        url         : TMTW_BASEURL + '/proposalgen/admin/replacementprinterslist',
        datatype    : 'json',
        colNames    : ['manufacturer_id', 'master_devcie_id', 'Printer Model', 'Replacement Category', 'Action'],
        colModel    : [
            {width: 30, name: 'manufacturer_id', index: 'manufacturer_id', align: 'center', title: false, hidden: true},
            {width: 30, name: 'master_device_id', index: 'master_device_id', align: 'center', title: false, hidden: true},
            {width: 250, name: 'printer_model', index: 'printer_model', title: false},
            {width: 100, name: 'replacement_category', index: 'replacement_category', title: false},
            {width: 40, name: 'action', index: 'action', align: 'center', title: false}
        ],
        multiselect : true,
        width       : 940,
        height      : 'auto',
        gridComplete: function ()
        {
            var ids = jQuery("#grid_list").jqGrid('getDataIDs');
            for (var i = 0; i < ids.length; i++)
            {
                var cur_row = ids[i];

                //add hidden replacement category fields
                var replacement_category = document.getElementById("grid_list").rows[i + 1].cells[4].innerHTML;
                replacement_category_field = '<input type="hidden" name="replacement_category_' + cur_row + '" id="replacement_category_' + cur_row + '" value="' + replacement_category + '" />';
                jQuery("#grid_list").jqGrid('setRowData', ids[i], {replacement_category: replacement_category + replacement_category_field});

                //add edit button
                var manufacturer_id = document.getElementById("grid_list").rows[i + 1].cells[1].innerHTML;
                var master_device_id = document.getElementById("grid_list").rows[i + 1].cells[2].innerHTML;
                edit_button = '<input type="button" name="btnEdit' + cur_row + '" id="btnEdit' + cur_row + '" tag="Edit" value="Edit" onclick="javascript: do_action(\'edit\',' + manufacturer_id + ',' + master_device_id + ');" class="btn" />';
                jQuery("#grid_list").jqGrid('setRowData', ids[i], {action: edit_button});
            }
            $("#hdnIds").val(ids);
        },
        editurl     : 'dummy.php'
    });

    jQuery("#grid_list").jqGrid('navGrid', '#grid_pager',
        {add: false, del: false, edit: true, refresh: false, search: false},
        {closeAfterEdit: true, recreateForm: true, closeOnEscape: true, width: 400, top: sTop, left: sLeft},
        {closeAfterAdd: true, recreateForm: true, closeOnEscape: true, width: 400, top: sTop, left: sLeft},
        {},
        {},
        {}
    );

    $("#manufacturer_id").change(function (event)
    {
        var manufacturerid = $("#manufacturer_id").val();
        var url = TMTW_BASEURL + '/proposalgen/admin/printermodels?manufacturerid=' + manufacturerid;

        $.ajax({
            type       : "POST",
            contentType: "application/json; charset=utf-8",
            url        : url,
            dataType   : "json",
            success    : function (models)
            {
                //empty out printer_model
                var printerModel = $("#printer_model");
                printerModel.html('');

                var listItems = "";
                var selected = selectedPrinterModel;

                for (var i = 0; i < models.rows.length; i++)
                {
                    selected_code = "";
                    if (selected && selected == models.rows[i].cell[0])
                    {
                        selected_code = " selected ";
                    }
                    listItems += "<option value='" + models.rows[i].cell[0] + "'" + selected_code + ">" + models.rows[i].cell[1] + "</option>";
                }
                printerModel.html(listItems);
                printerModel.combobox("destroy");
                printerModel.combobox({
                    selected: function (event, ui)
                    {
                        $("#printer_model").change();
                    }
                });

            },
            error      : function ()
            {
                $('#message_container').html("Error returning printer models.");
            },
            complete   : function ()
            {
            }
        });

    });

    $("#printer_model").change(function (event)
    {
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
            url       : TMTW_BASEURL + '/proposalgen/admin/replacementdetails?deviceid=' + master_device_id,
            dataType: 'JSON',
            success   : function (data)
            {
                $("#hdnOriginalCategory").val(data.replacement_category);
                $("#replacement_category").val(data.replacement_category);
                $("#print_speed").val(data.print_speed);
                $("#resolution").val(data.resolution);
                $("#monthly_rate").val(data.monthly_rate);
            },
            error     : function ()
            {
                $('#message_container').html("Error returning replacement printer details.");
            },
            complete  : function ()
            {
                enable_form();
            }

        });

    });

    $(function ()
    {
        $("#manufacturer_id").combobox({
            selected: function (event, ui)
            {
                $("#manufacturer_id").change();
            }
        });
        $("#printer_model").combobox({
            selected: function (event, ui)
            {
                $("#printer_model").change();
            }
        });
    });

    if (repop == true)
    {
        setTimeout("do_action('edit', man_id, mas_id)", 100);
    }

});

function empty_form()
{
    clear_validation();
    $("#replacement_category").val('');
    $("#print_speed").val('');
    $("#resolution").val('');
    $("#monthly_rate").val('');
    repop = false;
}

function disable_form()
{
    $("#replacement_category").attr('disabled', 'disabled');
    $("#print_speed").attr('disabled', 'disabled');
    $("#resolution").attr('disabled', 'disabled');
    $("#monthly_rate").attr('disabled', 'disabled');
}

function enable_form()
{
    $("#replacement_category").removeAttr('disabled');
    $("#print_speed").removeAttr('disabled');
    $("#resolution").removeAttr('disabled');
    $("#monthly_rate").removeAttr('disabled');
    $("#btnSave").removeAttr('disabled');
}

function clear_validation()
{
    $('ul.error li').remove();
    $("#message_container").html('');
    $("#modal_message_container").html('');
}

function update_grid(id)
{
    $('#grid_list').setGridParam({url: TMTW_BASEURL + '/proposalgen/admin/replacementprinterslist'});
    $('#grid_list').trigger("reloadGrid");
}

function do_action(inAction, man_id, mas_id)
{
    if (inAction == 'add')
    {
        empty_form();

        $("#manufacturer_id").val('');
        $("#manufacturer_id").combobox({
            selected: function (event, ui)
            {
                $("#manufacturer_id").change();
            }
        });
        $("#manufacturer_id").change();
        $("#manufacturer_html").html('');

        setTimeout(function ()
        {
            $("#printer_model").val('');
            $("#printer_model").combobox({
                selected: function (event, ui)
                {
                    $("#printer_model").change();
                }
            });
            $("#printer_model").change();
            $("#printer_model_html").html('');

            $("#manufacturer_id").removeAttr('disabled');
            $("#printer_model").removeAttr('disabled');
        }, 200);

        form_mode = "add";
        $("#form_mode").val('add');
        $("#hdnManId").val('');
        $("#hdnMasId").val('');
        $("#replacement_id").val('');
        $("#modal_title").html('Add Replacement Printer');
        $("#lightbox, #lightbox-panel").fadeIn(300);

    }
    else if (inAction == 'edit')
    {
        if (repop != true)
        {
            empty_form();
        }
        $("#manufacturer_id").val(man_id);
        $("#manufacturer_id").combobox("destroy");
        $("#manufacturer_id").change();
        $("#manufacturer_id").hide();
        $("#manufacturer_html").html($("#manufacturer_id option:selected").text());

        setTimeout(function ()
        {
            $("#printer_model").val(mas_id);
            $("#printer_model").combobox("destroy");
            $("#printer_model").change();
            $("#printer_model").hide();
            $("#printer_model_html").html($("#printer_model option:selected").text());

            //$("#manufacturer_id").attr('disabled','disabled');
            //$("#printer_model").attr('disabled','disabled');
        }, 300);

        form_mode = "edit";
        $("#form_mode").val('edit');
        $("#hdnManId").val(man_id);
        $("#hdnMasId").val(mas_id);
        $("#replacement_id").val(mas_id);
        $("#modal_title").html('Edit Replacement Printer');
        $("#lightbox, #lightbox-panel").fadeIn(300);

    }
    else if (inAction == 'delete')
    {
        if (confirm("Are you sure you want to delete the selected printers?"))
        {
            form_mode = "delete";
            $("#form_mode").val('delete');
            $("#replacements").submit();
        }

    }
    else if (inAction == 'close')
    {
        empty_form();
        $("#lightbox, #lightbox-panel").fadeOut(300);

    }
    else if (inAction == 'save')
    {
        $("#manufacturer_id").removeAttr('disabled');
        $("#printer_model").removeAttr('disabled');

        //clear all existing errors
        $("#replacement_category-error").remove();
        $("#print_speed-error").remove();
        $("#resolution-error").remove();
        $("#paper_capacity-error").remove();
        $("#cpp_above-error").remove();
        $("#monthly_rate-error").remove();
        $("#modal_message_container").html('');

        //get data
        var data = '';
        data += '?hdnManId=' + $("#hdnManId").val();
        data += '&hdnMasId=' + $("#hdnMasId").val();
        data += '&hdnIds=' + $("#hdnIds").val();
        data += '&manufacturer_id=' + $("#manufacturer_id").val();
        data += '&printer_model=' + $("#printer_model").val();
        data += '&hdnOriginalCategory=' + escape($("#hdnOriginalCategory").val());
        data += '&replacement_category=' + escape($("#replacement_category").val());
        data += '&print_speed=' + $("#print_speed").val();
        data += '&resolution=' + $("#resolution").val();
        data += '&monthly_rate=' + $("#monthly_rate").val();
        data += '&form_mode=' + $("#form_mode").val();

        url = TMTW_BASEURL + '/proposalgen/admin/savereplacementprinter' + data;
        $.ajax({
            type       : "POST",
            contentType: "application/json; charset=utf-8",
            url        : url,
            data       : data,
            error      : function ()
            {
                $('#message_container').html("Error setting mapped device!");
            },
            success    : function (data)
            {
                temp = data.split(',');
                if (temp.length == 1)
                {
                    $("#modal_message_container").html(temp[0]);
                }
                else
                {
                    $("#modal_message_container").html('<p>Please review the error below.</p>');
                    $("#" + temp[0] + "-element").html($("#" + temp[0] + "-element").html() + "<ul id='" + temp[0] + "-error' class='error'><li>" + temp[1] + "</li></ul>");
                }
                $('#grid_list').trigger("reloadGrid");
                //$("#lightbox, #lightbox-panel").fadeOut(300);
            }
        });

    }
    else if (inAction == 'done')
    {
        document.location.href = TMTW_BASEURL + '/admin';
    }
}
