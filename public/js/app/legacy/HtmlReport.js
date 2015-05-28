require(['jquery', 'app/plugins/DownloadManager', 'bootstrap'], function ($, require)
{
    $(document).ready(function ()
    {
        if ($("#reportNavbar").length > 0)
        {
            $('body').scrollspy({
                target: '#reportNavbar'
            });
        }

        /**
         * Handles the view report button
         */
        $("#viewReportButton").click(function (e)
        {
            window.open($("#availableReports")[0].value);
        });

        /**
         * Handles displaying a nice downloading popup
         */
        $('.downloadButton').downloadManager();
    });
});
