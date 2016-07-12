require([
    'jquery',
    'require',
    'app/Templates',
    'accounting',
    'jquery.typewatch',
    'app/components/Select2/TonerColor',
    'bootstrap.modal.manager'
], function ($, require, Template, accounting)
{
    if (window.currencySymbol) {
        accounting.settings.currency.symbol = window.currencySymbol;
    }

    $(window).scroll(function() {
        var loadMore = $('#load-more');
        if (loadMore.length==1) {
            if (loadMore.hasClass('loading')) return;
            var t = $(window).scrollTop();
            var h = $(window).height();
            var n = loadMore.position().top;
            if (t+h>n) {
                loadMore.addClass('loading').html('<p class="text-center"><img src="/img/spinner_32.gif"> loading...</p>');
                var offset = loadMore.attr('data-offset');
                var data = $('#filter-form').serialize();
                $.post('/hardware-library/toner/infinite?offset='+offset, data, function(response) {
                    loadMore.replaceWith(response);
                }, 'html');
            }
        }
    });

    $(function ()
    {
        var $filterManufacturer = $(".js-filter-manufacturer");
        var $filterTonerColor = $(".js-filter-toner-color");
        var $filterTonerSku = $(".js-filter-toner-sku");
        var $resetFilterButton = $(".js-reset-filter");

        /**
         * Toner Color Filter
         */
        $filterTonerColor.selectTonerColor({
            placeholder: "All available colors"
        });

        $resetFilterButton.on("click", function ()
        {
            $filterManufacturer.select2("val", '');
            $filterTonerColor.select2("val", '');
            $filterTonerSku.val('');
            reloadResults();
        });

        $filterManufacturer.on("change", function ()
        {
            reloadResults();
        });

        $filterTonerColor.on("change", function ()
        {
            reloadResults();
        });

        $filterTonerSku.on("change", function ()
        {
            reloadResults();
        });

        /**
         * Setup type watch on toner sku so that we automatically
         * filter when someone types in a sku.
         */
        $filterTonerSku.typeWatch({
            callback     : function (value)
            {
                reloadResults();
            },
            wait         : 750
        });

    });
});



function reloadResults() {
    $('#result-panel').html('<p class="text-center"><img src="/img/spinner_32.gif"> loading...</p>');
    var data = $('#filter-form').serialize();
    $.post('/hardware-library/toner/infinite', data, function(response) {
        $('#result-panel').html(response);
    }, 'html');
}

function editRow(id) {
    require(['app/legacy/hardware-library/manage-devices/TonerForm'], function (TonerForm)
    {
        var tonerForm = new TonerForm({
            isAllowed: isSaveAndApproveAdmin,
            tonerId  : id
        });

        $(tonerForm).on('toner-form.saved', function (event, tonerId)
        {
            $('#toner-'+id).load('/hardware-library/toner/infinite?reload='+id);
        });

        tonerForm.show();
    });
}
