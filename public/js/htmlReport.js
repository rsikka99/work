$(document).ready(function ()
{
    if ($("#reportNavbar").length > 0)
    {
        $('body').scrollspy({
            target: '#reportNavbar'
        });
    }

    /**
     * The report navigation drop down
     */
    $("#reportNavigator").change(function (e)
    {
        window.location.href = this.value;
    });

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
