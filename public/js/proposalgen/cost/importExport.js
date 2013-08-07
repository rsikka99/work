// Setup auto complete for our text box
var manufacturerSelect = $("#manufacturers");
manufacturerSelect.select2({
    placeholder       : "Export By Manufacturer",
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
        {
            var allArray = [];
            allArray['id']= 0;
            allArray['text']= "All Manufactures";
            data.push(allArray);

            return {results: data};
        }
    }
});

function do_action(action)
{
    if(action == 'import')
    {
        // Gets the closest form element and submit that form.
        $("#importLink").closest('form').submit();
    }
    else if(action == 'export')
    {
        var manufacturerElement = $("#manufacturers");
        // Do we have a manufacturer element
        if(manufacturerElement.length > 0)
        {
            document.location.href = "exportpricing?type="+$("#exportLink").data().type+"&manufacturer="+manufacturerElement.val()
        }
        else
        {
            document.location.href = "exportpricing?type="+$("#exportLink").data().type;
        }
    }
    else if(action == 'cancel')
    {
        document.location.href = '<?php echo $this->baseUrl('/admin/'); ?>';
    }
}
