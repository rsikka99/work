require(['jquery', 'bootstrap'], function ($)
{
    /**
     * surveyForm.js takes care of handling all our UX for the assessment survey
     */
    $(document).ready(function ()
    {
        $('body').scrollspy({
            target: '#surveyNavbar'
        });

        var $surveyForm = $('.js-survey-form');

        var $tonerCost = $('[name="toner_cost"]');
        var $laborCost = $('[name="labor_cost"]');
        var $numberOfMonthlyOrders = $('[name="numb_monthlyOrders"]');
        var $itHours = $('[name="itHours"]');
        var $monthlyBreakdowns = $('[name="monthlyBreakdown"]');

        var onChange = function (event)
        {
            if ($('[name="toner_cost_radio"]:checked').val() == 'exact')
            {
                $tonerCost.removeAttr('disabled');
            }
            else
            {
                $tonerCost.attr('disabled', 'disabled');
            }

            if ($('[name="labor_cost_radio"]:checked').val() == 'exact')
            {
                $laborCost.removeAttr('disabled');
            }
            else
            {
                $laborCost.attr('disabled', 'disabled');
            }

            if ($('[name="inkTonerOrderRadio"]:checked').val() == 'Times per month')
            {
                $numberOfMonthlyOrders.removeAttr('disabled');
            }
            else
            {
                $numberOfMonthlyOrders.attr('disabled', 'disabled');
            }

            if ($('[name="itHoursRadio"]:checked').val() == 'exact')
            {
                $itHours.removeAttr('disabled');
            }
            else
            {
                $itHours.attr('disabled', 'disabled');
            }

            if ($('[name="monthlyBreakdownRadio"]:checked').val() == 'exact')
            {
                $monthlyBreakdowns.removeAttr('disabled');
            }
            else
            {
                $monthlyBreakdowns.attr('disabled', 'disabled');
            }
        };


        $surveyForm.find('input').on('change', onChange);
        onChange();
    });
});