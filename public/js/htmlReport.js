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
     * Handles when a download button is clicked
     */
    $(".downloadButton").click(function (e)
    {
        var extension = "." + $(this).data('ext');

        var loadingMessage = $(this).data('loadingmessage');
        var extraDataContents = $(this).data('extra-parameter');

        if (loadingMessage == null || loadingMessage.length < 1)
        {
            loadingMessage = "Please be patient while we generate your document. This normally takes a minute or two.";
        }
        var cancelled = false;

        var extraData = {};

        if (extraDataContents != null || extraDataContents.length > 0)
        {

            extraData[extraDataContents] = $('#' + extraDataContents.toString()).val();
        }


        var xhr;

        e.preventDefault();
        $("<div id='loading' style='text-align: center'></div>").append("<p>" + loadingMessage + "</p>").append("<div class='AjaxLoadingIcon'></div>").dialog({
            height       : 200,
            width        : 450,
            modal        : true,
            draggable    : false,
            resizable    : false,
            title        : "Generating Document...",
            closeOnEscape: false,
            close        : function (event, ui)
            {
                if (xhr != null)
                {
                    cancelled = true;
                    xhr.abort();
                }
            }
        });

        xhr = $.ajax({
            url     : this.href,
            data    : extraData,
            success : function (data)
            {
                if (data.indexOf(extension) < 0 || data.length > 100)
                {
                    // Debug Info for extension:
                    //.append("<p>IndexOf Returned: " + data.indexOf(extension) + " for the extension " + extension +"</p>")
                    $("<div id='errorMessage'></div>").append(data).dialog({
                        height    : 300,
                        width     : 900,
                        modal     : true,
                        draggable : true,
                        resizeable: true,
                        title     : "Error",
                        buttons   : {
                            Ok: function ()
                            {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
                else
                {
                    window.location.href = data;
                }
            },
            error   : function (data)
            {
                if (!cancelled)
                {
                    if (data.responseJSON && data.responseJSON.error)
                    {
                        $("<div id='errorMessage'></div>").html(data.responseJSON.error).dialog({
                            height    : 500,
                            width     : 900,
                            modal     : true,
                            draggable : true,
                            resizeable: true,
                            title     : "Error"

                        });
                    }
                    else
                    {
                        $("<div id='errorMessage'></div>").html(data.responseText).dialog({
                            height    : 500,
                            width     : 900,
                            modal     : true,
                            draggable : true,
                            resizeable: true,
                            title     : "Error"

                        });
                    }
                }
            },
            complete: function (data)
            {
                $("#loading").remove();
                xhr = null;
                cancelled = false;
            }
        });
    });
});