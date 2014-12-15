require(['jquery', 'select2', 'app/components/Select2/Manufacturer'], function ($)
{
    // Setup auto complete for our text box
    var $manufacturerSelect = $("#manufacturers");
    $manufacturerSelect.selectManufacturer({
        'placeholder': 'Export By Manufacturer'
    });

    function do_action(action)
    {
        if (action == 'import')
        {
            // Gets the closest form element and submit that form.
            $("#importLink").closest('form').submit();
        }
        else if (action == 'export')
        {
            var manufacturerElement = $("#manufacturers");
            // Do we have a manufacturer element
            if (manufacturerElement.length > 0)
            {
                document.location.href = "exportpricing?type=" + $("#exportLink").data().type + "&manufacturer=" + manufacturerElement.val()
            }
            else
            {
                document.location.href = "exportpricing?type=" + $("#exportLink").data().type;
            }
        }
        else if (action == 'cancel')
        {
            document.location.href = '<?php echo $this->baseUrl(' / admin / '); ?>';
        }
    }
});