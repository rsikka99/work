$('#eventLogGrid').jqGrid(
    {
        url       : TMTW_BASEURL + 'admin/event_log/get-event-logs',
        datatype  : 'json',
        colModel  : [
            {
                width : 50,
                name  : 'id',
                index : 'id',
                label : 'Id',
                hidden: true
            },
            {
                width: 70,
                name : 'ipAddress',
                index: 'ipAddress',
                label: 'IP Address'
            },
            {
                width: 150,
                name : 'email',
                index: 'email',
                label: 'Email'
            },
            {
                width: 150,
                name : 'name',
                index: 'name',
                label: 'Type'
            },
            {
                width: 263,
                name : 'description',
                index: 'description',
                label: 'Description'
            },
            {
                width: 150,
                name : 'message',
                index: 'message',
                label: 'Message'
            },
            {
                width    : 130,
                name     : 'timestamp',
                index    : 'timestamp',
                label    : 'Timestamp',
                formatter: 'date'
            }
        ],
        jsonReader: {
            repeatitems: false
        },
        postData  : {
            email: function ()
            {
                return $("#emailCriteria").val();
            },
            type : function ()
            {
                return $('#typeCriteria').val();
            }
        },
        height    : 'auto',
        rowNum    : 20,
        rowList   : [ 20, 30, 50, 100, 500 ],
        pager     : '#eventLogPager'
    }
);
var emailCriteria = $("#emailCriteria");

emailCriteria.on("change", function (e)
{
    reloadGrid();
});

$("#typeCriteria").on("change", function (e)
{
    reloadGrid();
});


/**
 * Available Toners Manufacturer search
 */
emailCriteria.select2({
    placeholder       : "Search for Email",
    minimumInputLength: 1,
    ajax              : {
        // instead of writing the function to execute the request we use Select2's convenient helper
        url     : TMTW_BASEURL + 'admin/event_log/search-for-email',
        dataType: 'json',
        data    : function (term, page)
        {
            return {
                emailName : term, // search term
                page_limit: 10
            };
        },
        results : function (data, page)
        {
            // parse the results into the format expected by Select2.
            // since we are using custom formatting functions we do not need to alter remote JSON data
            return {results: data};
        }
    },

    dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
    escapeMarkup    : function (m)
    {
        return m;
    } // we do not want to escape markup since we are displaying html in results
});

emailCriteria.on("change", function (e)
{
    reloadGrid();
});

$(document).on("click", "#emailCriteriaReset", function ()
{
    $("#emailCriteria").select2("data", null);
});
function reloadGrid()
{
    jQuery("#eventLogGrid").jqGrid().trigger("reloadGrid");
}