require(['jquery', 'bootstrap'], function ($)
{
    $(document).ready(function ()
    {
        // Set up info popups
        $('i').popover();

        // Toggle the filter field
        $("#criteria_filter").change(function ()
        {
            var url = '';

            //clear searches
            $("#txtCriteria").val('');
            $("#cboCriteria").val('');

            switch (this.value)
            {
                case "manufacturer":
                    $("#text_criteria").hide();
                    $("#list_criteria").show();
                    break;
                case "sku":
                    $("#list_criteria").hide();
                    $("#text_criteria").show();
                    break;
                default:
                    break;
            }
        });

        // Set up filter values
        if (isManufacturerSearch)
        {
            $("#list_criteria").show();
            $("#text_criteria").hide();
            $("#cboCriteria").val(manufacturerValue);
        }
        else
        {
            $("#list_criteria").hide();
            $("#text_criteria").show();
            $("#txtCriteria").val(textSearchValue);
        }

    });

    function update_leased()
    {
        if ($('#isLeased').is(":checked"))
        {
            $('#leasedTonerYield').removeAttr('disabled');
        }
        else
        {
            $('#leasedTonerYield').attr('disabled', 'disabled');
        }
    }


    $(isLeased).change(function ()
    {
        update_leased();
    });
    update_leased();

    function removeA(arr)
    {
        var what, a = arguments, L = a.length, ax;
        while (L > 1 && arr.length)
        {
            what = a[--L];
            while ((ax = arr.indexOf(what)) != -1)
            {
                arr.splice(ax, 1);
            }
        }
        return arr;
    }

    function update_toner(id)
    {
        var ids = $("#hdnToners").val();
        var idsArray = ids.split(',');

        if (jQuery.inArray(id.toString(), idsArray) > -1)
        {
            // remove from array
            idsArray.splice(jQuery.inArray(id.toString(), idsArray), 1);

            // toggle button to assign
            $("#btnAssign" + id).attr('class', 'btn btn-success btn-xs');
            $("#btnAssign" + id).html('<i class="glyphicon glyphicon-plus-sign icon-white"></i> Assign');

        }
        else
        {
            // add to array
            if (ids.length > 0)
            {
                idsArray.push(id);
            }
            else
            {
                idsArray = id;
            }

            // toggle button to remove
            $("#btnAssign" + id).attr('class', 'btn btn-danger btn-xs');
            $("#btnAssign" + id).html('<i class="glyphicon glyphicon-minus-sign icon-white"></i> Unassign');
        }
        $("#hdnToners").val(idsArray.toString());
    }
});