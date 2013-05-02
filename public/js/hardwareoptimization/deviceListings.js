$(function ()
{
    // Setup autocomplete for our textbox

    $("#deviceName").select2({
        placeholder       : "Search for a device",
        minimumInputLength: 1,
        ajax              : { // instead of writing the function to execute the request we use select2's convenient helper
            url     : TMTW_BASEURL + "proposalgen/admin/search-for-device",
            dataType: 'json',
            data    : function (term, page)
            {
                return {
                    searchTerm: term, // search term
                    page_limit: 10
                };
            },
            results : function (data, page)
            { // parse the results into the format expected by select2.
                // Since we are using custom formatting functions we do not need to alter remote JSON data
                var newData = $.map(data, function (device)
                {
                    device.text = device.device_name;
                    return device;
                })
                return {results: newData};
            }
        }
    });
});