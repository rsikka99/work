require(['jquery', 'jquery.ui.multiselect'], function ($)
{
    $(function ()
    {
        $('.tonerMultiselect').multiselect({
            selectionMode        : 'click',
            availableListPosition: 'left',
            sortable             : true

        });
    });
});