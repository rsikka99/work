require([
    'jquery',
    'jquery.ui',
    'bootstrap',
    'bootstrap.switch',
    '/js/app/components/Select2/Country.js'
], function ($)
{
    //@formatter:off
    /**
     * Shortcut for logging as well as history of logs.
     * paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
     *
     * usage: log('inside coolFunc', this, arguments);
     */
    window.log=function(){log.history=log.history||[];log.history.push(arguments);if(this.console){console.log(Array.prototype.slice.call(arguments))}};

    /**
     * Make it safe to use console.log (always).
     * http://www.sitepoint.com/safe-console-log/
     */
    (function(a){function b(){}for(var c="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),d;!!(d=c.pop());){a[d]=a[d]||b;}})
    (function(){try{console.log();return window.console;}catch(a){return (window.console={});}}());
    //@formatter:on

    $(function ()
    {
        if ($.fn.button.noConflict != undefined)
        {
            $.fn.button.noConflict();
        }

        $('.js-select-country').selectCountry();

        /*
         * Enable dropdown menus
         */
        $('.dropdown-toggle').dropdown();

        $('.js-tooltip').tooltip({
            "html": true
        });

        $('.js-input-tooltip').tooltip({
            "html"   : true,
            "trigger": 'focus'
        });

        $('.js-popover').popover();

        /*
         * Enable hover popovers
         */
        $('.hasPopover').popover();

        $(".js-yes-no-switch").bootstrapSwitch({
            "onColor" : "success",
            "offColor": "danger",
            "onText"  : "Yes",
            "offText" : "No"
        });

        $(".js-enabled-switch").bootstrapSwitch({
            "onColor" : "success",
            "offColor": "danger",
            "onText"  : "Enabled",
            "offText" : "Disabled"
        });

        $(".js-date-picker").datepicker({
            "showOtherMonths"  : true,
            "selectOtherMonths": true,
            "dateFormat": 'yy-mm-dd'
        });

        $(document).on('click', '.js-delete-confirmation', function (event)
        {
            // TODO lrobert: Make this a nice modal popup component
            if ('confirm' in window)
            {
                return window.confirm('Are you sure you want to delete this?');
            }

            return true;
        });

    });
});