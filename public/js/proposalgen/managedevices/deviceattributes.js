function loadDeviceAttributes()
{
    $("#launchDate").datepicker({
        dateFormat : 'yy-mm-dd',
        changeMonth: true,
        changeYear : true,
        yearRange  : '1980:+2',
        beforeShow : function (input)
        {
            $(input).css({
                "position": "relative",
                "z-index" : 999999
            });
        }
    });
}