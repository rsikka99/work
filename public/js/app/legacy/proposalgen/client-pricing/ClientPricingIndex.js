require(['jquery'], function ($)
{
    $(function ()
    {
        $("#clientToners").jqGrid(
            {
                url         : TMTW_BASEURL + 'proposalgen/client-pricing/client-toners-list',
                datatype    : 'json',
                sortname    : 'dateOrdered',
                sortorder   : 'DESC',
                colModel    : [
                    //@formatter:off
                    { width: 50, name: 'id', index: 'id', hidden: true, label: 'ID', title: false, sortable: false},
                    { width: 50, name: 'tonerId', index: 'tonerId', hidden: true, label: 'Toner Id', title: false, sortable: false},
                    { width: 110, name: 'orderNumber', index: 'orderNumber', hidden: false, label: 'Order Number', title: false, sortable: true},
                    { width: 90, name: 'oemSku', index: 'oemSku', hidden: false, label: 'OEM SKU', title: false, sortable: true},
                    { width: 90, name: 'dealerSku', index: 'dealerSku', hidden: false, label: dealerSkuName, title: false, sortable: true},
                    { width: 90, name: 'clientSku', index: 'clientSku', hidden: true, label: 'Client SKU', title: false, sortable: true},
                    { width: 55, name: 'cost', index: 'cost', hidden: false, label: 'Client Cost', formatter: 'currency', formatoptions: {decimalPlaces: 2, defaultValue: '-'}, align: 'right', title: false, sortable: true},
                    { width: 50, name: 'quantity', index: 'quantity', hidden: false, formatter: 'integer', align: 'right', label: 'Qty', title: false, sortable: true},
                    { width: 110, name: 'dateOrdered', index: 'dateOrdered', hidden: false, label: 'Date Ordered', align: 'right', formatter: 'date', formatoptions: {newformat: "M d, Y"}, title: false, sortable: true},
                    { width: 90, name: 'replacementOemSku', index: 'replacementOemSku', hidden: false, label: 'New OEM SKU', title: false, sortable: true},
                    { width: 90, name: 'replacementDealerSku', index: 'replacementDealerSku', hidden: false, label: 'New ' + dealerSkuName, title: false, sortable: true},
                    { width: 70, name: 'replacementCost', index: 'replacementCost', hidden: false, label: 'New Cost', formatter: 'currency', formatoptions: {decimalPlaces: 2, defaultValue: '-'}, align: 'right', title: false, sortable: true},
                    { width: 70, name: 'replacementSavings', index: 'replacementSavings', hidden: false, label: 'Savings', formatter: 'currency', formatoptions: {decimalPlaces: 2, defaultValue: '-'}, align: 'right', title: false, sortable: true},
                    { width: 160, name: 'dateShipped', index: 'dateShipped', hidden: true, label: 'Date Shipped', title: false, sortable: true},
                    { width: 160, name: 'dateReconciled', index: 'dateReconciled', hidden: true, label: 'Date Reconciled', title: false, sortable: true},
                    { width: 160, name: 'replacementTonerId', index: 'replacementTonerId', hidden: true, label: 'Replacement Toner Id', title: false, sortable: false},
                    { width: 61, name: 'action', index: 'action', hidden: false, label: 'Action', title: false, sortable: false}
                    //@formatter:on
                ],
                jsonReader  : {
                    repeatitems: false
                },
                postData    : {
                    filter  : function ()
                    {
                        return $("#tonersFilter").val();
                    },
                    criteria: function ()
                    {
                        return $('#tonersCriteria').val();
                    }
                },
                height      : 'auto',
                rowNum      : 30,
                rowList     : [15, 30, 50, 100, 500],
                pager       : '#clientToners_Pager',
                gridComplete: function ()
                {
                    var grid = $(this).jqGrid();
                    var ids = grid.getDataIDs();

                    for (var i = 0; i < ids.length; i++)
                    {
                        var currentRowId = ids[i];
                        var currentRow = grid.getRowData(currentRowId);
                        currentRow.action = '<div class="btn-group"><button class="btn btn-sm btn-warning" type="button" onclick="javascript: editClientPricing(' + currentRowId + ')" ><i class="icon-pencil"></i></button>';
                        currentRow.action += '<button class="btn btn-sm btn-danger" type="button" onclick="javascript: deleteClientPricing(' + currentRowId + ')"><i class="icon-trash"></i></button></div>';
                        grid.setRowData(currentRowId, currentRow);
                    }
                }
            });
    });


    /**
     * Reloads assigned and unassigned toner jqgrids in Supplies & Service
     */
    function reloadClientToners()
    {
        var clientToners = jQuery("#clientToners");
        clientToners.trigger("reloadGrid");
    }

    function editClientPricing(id)
    {
        var rowdata = $("#clientToners").jqGrid('getRowData', id);
        if (rowdata)
        {
            clearErrors();
            clearTonersForm();
            $.ajax({
                url     : TMTW_BASEURL + "proposalgen/client-pricing/client-toner-replacements",
                type    : "post",
                dataType: "json",
                data    : {
                    tonerId: rowdata.tonerId
                },
                success : function (xhr)
                {
                    $("#replacementTonerId").empty();
                    $('#replacementTonerId').append($('<option>', {
                        value: 0,
                        text : 'Do Not Replace'
                    }));
                    $.each(xhr, function (index, value)
                    {
                        if (index != 'error')
                        {
                            $('#replacementTonerId').append($('<option>', {
                                value: index,
                                text : value
                            }));
                        }
                    });
                    $('#replacementTonerId').val((rowdata.replacementTonerId) ? rowdata.replacementTonerId : 0);
                },
                error   : function (xhr)
                {
                    $("#replacementTonerId").empty();
                    $('#replacementTonerId').append($('<option>', {
                        value: 0,
                        text : ''
                    }));
                }
            });
            $('#clientTonersId').val((rowdata.tonerId));
            populateTonersForm(rowdata);
            $('#clientTonersModal').modal('show');
        }
    }

    function deleteClientPricing(id)
    {
        var rowdata = $("#clientToners").jqGrid('getRowData', id);
        if (rowdata)
        {
            $("#deleteClientTonerOrderId").val(rowdata.id);
            $('#alertDeleteModal').modal('show');
        }
    }

    $(document).on("click", "#tonersSearch", function ()
    {
        reloadClientToners();
    });

    $(document).on("click", "#deleteAllClientPricing", function ()
    {
        $('#alertDeleteAllModal').modal('show');
    });

    $(document).on("click", "#tonersReset", function ()
    {
        $("#tonersFilter").val('sku');

        var clientTonersCriteria = $("#tonersCriteria");
        clientTonersCriteria.val('');
        reloadClientToners();
    });

    /**
     * Used to populate a form's values when pressing the jqgrid button create or edit
     * @param formData
     */
    function populateTonersForm(formData)
    {
        $.each(formData, function (key)
        {
            $("#" + key).val(formData[key]);
        });
    }

    function deleteAllClientPricing()
    {
        $.ajax({
            url     : TMTW_BASEURL + "proposalgen/client-pricing/delete-all-client-pricing",
            type    : "post",
            dataType: "json",
            success : function (xhr)
            {
                reloadClientToners();
            }
        });
    }

    /**
     * Clears all element values within the toners form
     */
    function clearTonersForm()
    {
        var elements = document.getElementById("clientTonersForm").elements;
        $.each(elements, function (key)
        {
            elements[key].value = '';
        });
    }

    function saveClientToners()
    {
        $.ajax({
            url     : TMTW_BASEURL + "proposalgen/client-pricing/save-client-pricing",
            type    : "post",
            dataType: "json",
            data    : {
                id                : $("#id").val(),
                tonerId           : $("#tonerId").val(),
                clientSku         : $("#clientSku").val(),
                cost              : $("#cost").val(),
                replacementTonerId: $("#replacementTonerId").val()
            },
            success : function (xhr)
            {
                $('#clientTonersModal').modal('hide');
                reloadClientToners();
            },
            error   : function (xhr)
            {
                clearErrors();
                var data = $.parseJSON(xhr.responseText);
                $.each(data.error, function (elementKey)
                {
                    var errorMessage = data.error[elementKey];
                    var element = document.getElementById(elementKey);

                    // We need to change the attribute value, to the new value or else it will revert to the last correct value, Which we do not want
                    element.setAttribute("value", element.value);
                    var controls = element.parentNode;
                    parent = controls.parentNode;
                    parent.className = "control-group error ";
                    controls.innerHTML = controls.innerHTML + "<span class='help-inline'>" + errorMessage + "</span>";
                });
            }
        });
    }

    /*
     * Clears all the errors out of the forms
     */
    function clearErrors()
    {
        $(".error").removeClass("error");
        $('.help-inline').remove();
    }

    function deleteClientToner()
    {
        $.ajax({
            url     : TMTW_BASEURL + "proposalgen/client-pricing/delete-client-pricing",
            type    : "post",
            dataType: "json",
            data    : {
                deleteClientTonerOrderId: $("#deleteClientTonerOrderId").val()
            },
            success : function (xhr)
            {
                reloadClientToners();
            }
        });

    }
});