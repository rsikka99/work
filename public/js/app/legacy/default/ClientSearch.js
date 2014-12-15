require(['jquery', 'bootstrap.typeahead'], function ($)
{
    $(document).ready(function ()
    {
        /**
         * Handles searching for a client by company name
         */
        $('#searchClientByName').typeahead({
            ajax        : {
                url          : TMTW_BASEURL + "/index/search-for-client",
                triggerLength: 1

            },
            display     : 'companyName',
            itemSelected: function (item, val, text)
            {
                /**
                 * Do something here with val
                 */
                $('#hiddenSelectClientId').val(val);
                $('#searchForClientForm').submit();
            }
        });
    });
});