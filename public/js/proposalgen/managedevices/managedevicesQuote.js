$(document).ready(function ()
{
    var master_device_id = $("#printer_model").val();

    jQuery("#availableToners").jqGrid({
        url         : TMTW_BASEURL + 'proposalgen/admin/tonerslist?deviceid=' + master_device_id,
        datatype    : 'json',
        colNames    : ['Toner ID', 'SKU', 'Manufacturer', 'Type', 'Color', 'Yield', 'Price', 'MasterID', 'Added','Machine Compatibility', 'Action', 'Apply To Printer', 'Machine Compabibility'],
        colModel    : [
            {tag: 0, width: 30, name: 'toner_id', index: 'toner_id', sorttype: 'int', hidden: true, editable: true, editoptions: {readonly: true, size: 12}},
            {tag: 1, width: 60, name: 'toner_sku', index: 'toner_sku', editable: true, editoptions: {size: 12, maxlength: 30}},
            {tag: 2, width: 120, name: 'manufacturer_id', index: 'toner_manufacturer'},
            {tag: 3, width: 120, name: 'part_type_id', index: 'part_type_id'},
            {tag: 4, width: 100, name: 'toner_color_id', index: 'tonerColorId'},
            {tag: 5, width: 60, name: 'toner_yield', index: 'yield'},
            {tag: 6, width: 80, name: 'toner_price', index: 'toner_price'},
            {tag: 7, width: 50, name: 'master_device_id', index: 'master_device_id', hidden: true},
            {tag: 8, width: 50, name: 'is_added', index: 'is_added', hidden: true},
            {tag: 9, width:225, name:'device_list', index:'device_list'},
            {tag: 10, width: 60, name: 'action', index: 'action', editable: false, align: 'center'},
            {tag: 11, width: 50, name: 'machine_compatibility', index: 'machine_compatibility', hidden: true},
            {tag: 12, width: 60, name: 'apply', index: 'apply', hidden: true, edittype: 'checkbox', editable: true, align: 'center'}

        ],
        width       : 940,
        height      : 500,
        rowNum      : 15,
        rowList     : [15, 35, 50],
        pager       : '#availableTonersPager',
        gridComplete: function ()
        {
            var ids = $(this).jqGrid('getDataIDs');
            var toner_array = $("#toner_array").val();

            for (var i = 0; i < ids.length; i++)
            {
                var cur_row = ids[i];
                var is_added = document.getElementById("availableToners").rows[i+1].cells[9].innerHTML;

                add_button = '<input type="button" name="btnAdd' + cur_row + '" id="btnAdd' + cur_row + '" tag="Add" value="Assign" class="btn" onclick="javascript: do_add(' + cur_row + ');" />';
                disabled_button = '<input type="button" name="btnAdd' + cur_row + '" id="btnAdd' + cur_row + '" tag="Add" value="Assign" class="btn" onclick="javascript: do_add(' + cur_row + ');" disabled="disabled" />';

                if (toner_array.indexOf("'" + cur_row + "'") != -1)
                {
                    jQuery("#availableToners").jqGrid('setRowData', ids[i], {action: disabled_button});
                }
                else
                {
                    jQuery("#availableToners").jqGrid('setRowData', ids[i], {action: add_button});
                }

                var min = 4;
                var max = 1;
                var output = '';
                device_list = document.getElementById("availableToners").rows[i+1].cells[9].innerHTML;
                var pieces = device_list.split("; ");
                output += '<div id="outer_'+ids[i]+'" style="text-align: left; width: 200px;">';
                for(var j=0; j < pieces.length; j++) {
                    device = pieces[j];
                    if(j == max) {
                        output += '<div id="inner_'+ids[i]+'" style="display: none;">';
                    }
                    output += device + '<br />';
                    if(j > max && j == pieces.length - 1) {
                        output += '</div>';
                        output += '<a id="view_link_'+ids[i]+'" href="javascript: void(0);" class="blue_link" onclick="javascript: view_device_list(\'\','+ids[i]+');">View All...</a>';
                    }
                }

                jQuery("#availableToners").jqGrid('setRowData',ids[i],{device_list:output});
            }

        },
        editurl     : TMTW_BASEURL + '/proposalgen/admin/edittoner?deviceid=' + master_device_id
    });

    jQuery("#appliedToners").jqGrid({
        url         : TMTW_BASEURL + '/proposalgen/admin/devicetoners?deviceid=' + master_device_id,
        datatype    : 'json',
        colNames    : ['Toner ID', 'SKU', 'Manufacturer', 'Type', 'Color', 'Yield', 'Price', 'MasterID', 'Added', 'Action'],
        colModel    : [
            {width: 30, name: 'toner_id', index: 'toner_id', sorttype: 'int', hidden: true, editable: true, editoptions: {readonly: true, size: 12}, sortable: false},
            {width: 60, name: 'toner_sku', index: 'toner_sku', editable: true, editoptions: {size: 12, maxlength: 30}, sortable: false},
            {width: 120, name: 'manufacturer_id', index: 'toner_manufacturer', sortable: false},
            {width: 120, name: 'part_type_id', index: 'part_type_id', sortable: false},
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
            var toner_array = $("#toner_array").val();
            var ids = $(this).jqGrid('getDataIDs');
            for (var i = 0; i < ids.length; i++)
            {
                var cur_row = ids[i];
                var is_added = document.getElementById("appliedToners").rows[i + 1].cells[8].innerHTML;

                remove_button = '<input type="button" name="btnRemove' + cur_row + '" id="btnRemove' + cur_row + '" tag="Remove" value="Remove" class="btn" onclick="javascript: do_remove(' + cur_row + ');" />';
                jQuery("#appliedToners").jqGrid('setRowData', ids[i], {action: remove_button});

                if (toner_array != '')
                {
                    toner_array = toner_array + ",";
                }
                toner_array = toner_array + "'" + cur_row + "'";

            }
            $("#toner_array").val(toner_array);
        }
    });

});

function do_add(id)
{
    $('#message_container').html('');

    var isvalid = true;
    var toner_id = id;
    var toner_sku = $("#toner_sku").val();
    var part_type_id = $("#part_type_id").val();
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

function update_applied()
{
    var toner_array = $("#toner_array").val();
    var master_device_id = $("#printer_model").val();
    var appliedToners = $('#appliedToners');

    appliedToners.setGridParam({url: TMTW_BASEURL + '/proposalgen/admin/devicetoners?deviceid=' + master_device_id + '&list=' + toner_array.replace(/'/gi, '')});
    appliedToners.trigger('reloadGrid');
}

function do_remove(id)
{
    $('#message_container').html('');
    var tonerArrayElement = $("#toner_array");

    if (confirm("Are you sure you want to remove this toner?"))
    {
        //update array
        var toner_array = tonerArrayElement.val();
        toner_array = toner_array.replace("'" + id + "',", "").replace(",'" + id + "'", "").replace("'" + id + "'", "");
        if (toner_array == '')
        {
            toner_array = '0';
        }
        tonerArrayElement.val(toner_array);
        update_applied();
        $("#btnAdd" + id).removeAttr('disabled');
    }
}

function view_device_list(type,id) {
    if(document.getElementById(type+'inner_'+id).style.display == 'none') {
        document.getElementById(type+'inner_'+id).style.display = 'block';
        document.getElementById(type+'view_link_'+id).innerHTML = 'Collapse...';
    } else {
        document.getElementById(type+'inner_'+id).style.display = 'none';
        document.getElementById(type+'view_link_'+id).innerHTML = 'View All...';
    }
}