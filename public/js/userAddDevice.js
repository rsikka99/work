$(document).ready(function ()
{
    var blackTonerFieldset = $("#fieldset-blackTonerFieldset");
    var cyanTonerFieldset = $("#fieldset-cyanTonerFieldset");
    var magentaTonerFieldset = $("#fieldset-magentaTonerFieldset");
    var yellowTonerFieldset = $("#fieldset-yellowTonerFieldset");
    var threeColorTonerFieldset = $("#fieldset-threeColorTonerFieldset");
    var fourColorTonerFieldset = $("#fieldset-fourColorTonerFieldset");


    var isLeasedCheckbox = $("#is_leased");
    $("#mps_launch_date").datepicker({ dateFormat: 'mm/dd/yy', changeMonth: true, changeYear: true});

    $("#tonerConfigId").change(function ()
    {
        var tonerConfigId = $("#tonerConfigId").val();

        switch (tonerConfigId)
        {
            case "1":
                // Black Only
                blackTonerFieldset.show();
                cyanTonerFieldset.hide();
                magentaTonerFieldset.hide();
                yellowTonerFieldset.hide();
                threeColorTonerFieldset.hide();
                fourColorTonerFieldset.hide();

                break;
            case "2":
                // 3 Color - Separated
                blackTonerFieldset.show();
                cyanTonerFieldset.show();
                magentaTonerFieldset.show();
                yellowTonerFieldset.show();
                threeColorTonerFieldset.hide();
                fourColorTonerFieldset.hide();
                break;
            case "3":
                // 3 Color - Combined
                blackTonerFieldset.show();
                cyanTonerFieldset.hide();
                magentaTonerFieldset.hide();
                yellowTonerFieldset.hide();
                threeColorTonerFieldset.show();
                fourColorTonerFieldset.hide();
                break;
            case "4":
                // 4 Color - Combined
                blackTonerFieldset.hide();
                cyanTonerFieldset.hide();
                magentaTonerFieldset.hide();
                yellowTonerFieldset.hide();
                threeColorTonerFieldset.hide();
                fourColorTonerFieldset.show();

                break;
        }
    });

    $("#tonerConfigId").change();

    isLeasedCheckbox.change(function ()
    {
        $("#tonerConfigId").change();
    });
});