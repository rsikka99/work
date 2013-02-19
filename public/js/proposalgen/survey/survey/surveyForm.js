/**
 * surveyForm.js takes care of handling all our UX for the assessment survey
 */
$(document).ready(function ()
{
    $('body').scrollspy({
        target: '#surveyNavbar'
    });


    //set default
    if ($("#toner_cost_radio-guess").is(':checked'))
    {
        $("#toner_cost").attr('disabled', 'disabled');
        $("#toner_cost").val('');
    }

    $("#toner_cost_radio-guess").click(function ()
    {
        $("#toner_cost").attr('disabled', 'disabled');
        $("#toner_cost").val('');
    });

    $("#toner_cost_radio-exact").click(function ()
    {
        $("#toner_cost").removeAttr('disabled');
    });

    //set default
    if ($("#labor_cost_radio-guess").is(':checked'))
    {
        $("#labor_cost").attr('disabled', 'disabled');
        $("#labor_cost").val('');
    }

    $("#labor_cost_radio-guess").click(function ()
    {
        $("#labor_cost").attr('disabled', 'disabled');
        $("#labor_cost").val('');
    });

    $("#labor_cost_radio-exact").click(function ()
    {
        $("#labor_cost").removeAttr('disabled');
    });


    //set default
    if (!$("#inkTonerOrderRadio-Timespermonth").is(':checked'))
    {
        $("#numb_monthlyOrders").val('');
        $("#numb_monthlyOrders").attr('disabled', 'disabled');
    }

    //add option click events
    $("#inkTonerOrderRadio-Daily").click(function ()
    {
        $("#numb_monthlyOrders").val('');
        $("#numb_monthlyOrders").attr('disabled', 'disabled');
    });
    $("#inkTonerOrderRadio-Weekly").click(function ()
    {
        $("#numb_monthlyOrders").val('');
        $("#numb_monthlyOrders").attr('disabled', 'disabled');
    });
    $("#inkTonerOrderRadio-Timespermonth").click(function ()
    {
        $("#numb_monthlyOrders").removeAttr('disabled');
    });

    //set default
    if ($("#itHoursRadio-guess").is(':checked'))
    {
        $("#itHours").attr('disabled', 'disabled');
        $("#itHours").val('');
    }

    $("#itHoursRadio-guess").click(function ()
    {
        $("#itHours").attr('disabled', 'disabled');
        $("#itHours").val('');
    });

    $("#itHoursRadio-Iknowtheexactamount").click(function ()
    {
        $("#itHours").removeAttr('disabled');
    });

    //set default
    if ($("#monthlyBreakdownRadio-guess").is(':checked'))
    {
        $("#monthlyBreakdown").attr('disabled', 'disabled');
        $("#monthlyBreakdown").val('');
    }

    $("#monthlyBreakdownRadio-guess").click(function ()
    {
        $("#monthlyBreakdown").attr('disabled', 'disabled');
        $("#monthlyBreakdown").val('');
    });

    $("#monthlyBreakdownRadio-exact").click(function ()
    {
        $("#monthlyBreakdown").removeAttr('disabled');
    });

});