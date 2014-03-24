$("#leasingSchemaId").change(function ()
{
    //add new options depending on selection
    $.ajax({
        beforeSend: function ()
        {
            // disable form elements while loading new info
            $("#leasingSchemaId").attr('disabled', 'disabled');
        },
        url       : '/quotegen/quote_profitability/leasingdetails?schemaId=' + $("#leasingSchemaId").val(),
        success   : function (data)
        {
            //empty out leasingSchemaId
            $("#leasingSchemaTermId").html('');
            for (var i = 0; i < data.length; i++)
            {
                var listItems = "";
                for (var i = 0; i < data.length; i++)
                {
                    listItems += "<option value=" + data[i][0] + ">" + data[i][1] + "</option>";
                }
                $("#leasingSchemaTermId").append(listItems);
            }
        },
        error     : function ()
        {

        },
        complete  : function ()
        {
            //enable form elements after they are filled
            $("#leasingSchemaId").removeAttr('disabled');
        }
    });
});