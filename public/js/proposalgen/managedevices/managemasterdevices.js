/**
 * When we save successfully we fire a custom event called saveSuccess on the main masterDeviceManagement field
 * If you want to catch this event put this in your javascript, Most likely used to refresh the JQGrid that you are editing from
 $("#masterDeviceManagement").bind("saveSuccess", function(e, masterDeviceId)
 {
      // Do Stuff
 });
 */

var masterDeviceId = 0;
var isAllowed = false;

function showMasterDeviceManagementModal(newMasterDeviceId, rmsUploadRowId, isAllowedToEdit)
{
    isAllowed = !(isAllowedToEdit == 'undefined' || isAllowedToEdit == 'false');

    masterDeviceId = newMasterDeviceId;
    var $screenWidth = ($(this).width());


    $('#masterDeviceManagement').load(TMTW_BASEURL + 'proposalgen/managedevices/managemasterdevices?masterDeviceId=' + masterDeviceId + '&rmsUploadRowId=' + rmsUploadRowId, '', function ()
    {
        setupModalPosition('manageMasterDeviceModal', $screenWidth);
        loadSuppliesAndService();
        loadDeviceAttributes();
        loadAvailableOptions();
        loadHardwareQuote();
        loadHardwareConfigurations();
        updateTabs();
        $(".switch").bootstrapSwitch();
    });
}


/**
 * This is the delete button for all deletion buttons on jqgrid.
 */
$(document).on("click", "#delete", function ()
{
    var id = $("#deleteId").val();
    var formName = $("#deleteFormName").val();
    $.ajax({
        url     : TMTW_BASEURL + "proposalgen/managedevices/delete?masterDeviceId=" + masterDeviceId + "&id=" + id + "&formName=" + formName,
        type    : "post",
        dataType: "json",
        success : function ()
        {
            clearErrors();
            //Reload our form
            $("#" + formName).trigger('reloadGrid');
        },
        error   : function (xhr)
        {

        }
    });
});

$(document).on("click", "#btnSaveChanges", function ()
{
    saveChanges(false);
});

$(document).on("click", "#btnSaveAndApprove", function ()
{
    saveChanges(true);
});


/**
 * This handles the create or edit jqgrid buttons.
 * It outputs errors onto the forms if received from json
 * @param formName
 * @param shouldAssign
 */
function createOrEdit(formName, shouldAssign)
{
    $.ajax({
        url     : TMTW_BASEURL + "proposalgen/managedevices/sauron?masterDeviceId=" + masterDeviceId,
        type    : "post",
        dataType: "json",
        data    : {
            form    : $("#" + formName).serialize(),
            formName: formName
        },
        success : function (xhr)
        {
            // Hide our modal (formName.length - 4) is to remove the word 'form' from the end of the formName, which happens to always be our jqgrid name!
            $('#' + formName.substr(0, formName.length - 4) + 'Modal').modal('hide');
            clearErrors();

            // Reload our grid
            $("#" + formName.substr(0, formName.length - 4)).trigger('reloadGrid');

            if (formName.substr(0, formName.length - 4) == 'availableToners')
            {
                $("#assignedToners").trigger('reloadGrid');
            }

            $("#availableTonersForm").trigger("createTonerSuccess", xhr.id);
        },
        error   : function (xhr)
        {
            clearErrors();
            try
            {
                var data = $.parseJSON(xhr.responseText);
                $.each(data.error, function (formKey)
                {
                    $.each(data.error[formKey]['errorMessages'], function (key, value)
                    {
                        var element = document.getElementById(key);//
                        var parent = element.parentNode.parentNode;
                        parent.className = "control-group error";
                        parent.innerHTML = parent.innerHTML + "<span class='help-inline'>" + value + "</span>";
                    });
                });
            }
            catch (e)
            {
                console.log(e);
            }

//            errorMessageElement.show();
        }
    });
}

/**
 * Clears all the errors out of the forms
 */
function clearErrors()
{
    $(".error").removeClass("error");
    $(".tabError").removeClass("tabError");
    $('.help-inline').remove();
}

/**
 * Used to populate a form's values when pressing the jqgrid button create or edit
 * @param elementPrefix
 * @param formData
 */
function populateForm(elementPrefix, formData)
{
    $.each(formData, function (key)
    {
        $("#" + elementPrefix + key).val(formData[key]);
    });
}

/**
 * Clears all element values within a form
 * @param formName
 */
function clearForm(formName)
{
    var elements = document.getElementById(formName).elements;
    $.each(elements, function (key)
    {
        elements[key].value = '';
    });
}

/**
 * This shows or hides the tabs depending on form data
 */
function updateTabs()
{
    var hardwareOptimizationTab = $("#hardwareOptimizationTopTab");
    var hardwareQuoteTab = $("#hardwareQuoteTopTab");
    var availableOptionsTab = $("#availableOptionsTopTab");
    var hardwareConfigurationsTab = $("#hardwareConfigurationsTopTab");
    if (masterDeviceId > 0)
    {
        var isSelling = document.getElementById("isSelling").checked;
        if (hardwareOptimizationTab && isSelling)
        {
            hardwareOptimizationTab.show();
        }
        else
        {
            hardwareOptimizationTab.hide();
        }
        if (hardwareQuoteTab)
        {
            hardwareQuoteTab.show();
        }
        if (availableOptionsTab)
        {
            var isSellingElement = document.getElementById("isSelling");
            if (isSellingElement)
            {
                if (isSelling)
                {
                    availableOptionsTab.show();
                    hardwareConfigurationsTab.show();
                    $("#hardwareConfigurations").trigger('reloadGrid');
                    $("#availableOptions").trigger('reloadGrid');
                }
                else
                {
                    availableOptionsTab.hide();
                    hardwareConfigurationsTab.hide();
                }
            }
        }
    }
    else
    {
        hardwareOptimizationTab.hide();
        hardwareQuoteTab.hide();
        availableOptionsTab.hide();
        hardwareConfigurationsTab.hide();
    }
}

/**
 *
 * @param modelName
 * @param screenWidth
 */
function setupModalPosition(modelName, screenWidth)
{
    var currentModal = $('#' + modelName);
    currentModal.modal('show');
    currentModal.css('width', 960);
    currentModal.css('left', function ()
    {
        var $addedLeft = 280;
        var $contentWidth = 960;
        var $left = screenWidth - $contentWidth;
        if ($left < 0)
        {
            $left = 0;
        }
        $left = $left / 2;
        $left += $addedLeft;
        return $left;
    });
}

/**
 * Displays a message at the top of the page,
 * @param type The type of alert to show, success/alert/danger
 * @param message
 */
function displayAlert(type, message)
{
    $("#alertMessage").attr("class", "alert alert-" + type).html("<span>" + message + "</span>").show();
}

function saveChanges(approve)
{
    $.ajax({
        url     : TMTW_BASEURL + "proposalgen/managedevices/update-master-device",
        type    : "post",
        dataType: "json",
        data    : {
            masterDeviceId      : masterDeviceId,
            manufacturerId      : $("#manufacturerId").val(),
            modelName           : $("#modelName").val(),
            approve             : approve,
            suppliesAndService  : $("#suppliesAndService").serialize() + "&tonersList=" + tonersList.join(","),
            deviceAttributes    : $("#deviceAttributes").serialize(),
            hardwareOptimization: $("#hardwareOptimization").serialize(),
            hardwareQuote       : $("#hardwareQuote").serialize()
        },
        success : function (xhr)
        {
            // Lets update our master device id, this tells updateTabs that we should show more tabs
            masterDeviceId = xhr.masterDeviceId;
            clearErrors();

            // Shows or hides the Available Options and Hardware Configuration tabs depending on if it is a quote device
            updateTabs();

            // This calls our custom event called saveSuccess
            displayAlert("success", "Successfully updated device");

            var masterDeviceManagement = $("#masterDeviceManagement");
            masterDeviceManagement.trigger("saveSuccess", [masterDeviceId]);
            if (approve)
            {
                masterDeviceManagement.trigger("approveSuccess", [masterDeviceId]);
            }
        },
        error   : function (xhr)
        {
            clearErrors();
            var $launchDate = $("#launchDate");
            // We need to destroy the launchDate datepicker
            $launchDate.datepicker("destroy");

            var data = $.parseJSON(xhr.responseText);
            var errorMessage;
            if (data['error']['modelAndManufacturer'] != undefined)
            {
                //Lets loop through and display the errors
                $.each(data['error']['modelAndManufacturer']['errorMessages'], function (key, value)
                {
                    var parent = document.getElementById(key).parentNode;
                    parent.className = "control-group error ";
                    parent.innerHTML = parent.innerHTML + "<span class='help-inline'>" + value + "</span>";
                });
                // Remove the key so its not used in the form validation below
                delete data['error']['modelAndManufacturer'];
            }

            $.each(data.error, function (formKey)
            {
                var formTab = document.getElementById(formKey + "TopTab");

                formTab.className = "tabError";

                $.each(data.error[formKey]['errorMessages'], function (elementKey)
                {
                    errorMessage = data.error[formKey]['errorMessages'][elementKey];

                    // We need to change the attribute value, to the new value or else it will revert to the last correct value, Which we do not want
                    var element = document.getElementById(elementKey);
                    element.setAttribute("value", element.value);

                    var parent = element.parentNode.parentNode;
                    parent.className = "control-group error ";
                    parent.innerHTML = parent.innerHTML + "<span class='help-inline'>" + errorMessage + "</span>";
                });
            });

            // We need to recreate the datepicker when we have an error!
            // Note we reselect using jQuery it because it gets deleted above somewhere (Lee thinks the "destroy" part of the datepicker but we don't know.
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


            displayAlert("danger", "Please fix the errors before continuing");
        }
    });
}

window.onresize = function ()
{
    if ($("#manageMasterDeviceModal").is(':visible'))
    {
        setupModalPosition("manageMasterDeviceModal", ($(this).width()));
    }
};