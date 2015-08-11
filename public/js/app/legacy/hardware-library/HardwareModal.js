define([
    'jquery',
    'underscore',
    'require',
    'app/Templates',
    'accounting',
    'jqgrid',
    'bootstrap.modal.manager',
    '../../components/Select2/Manufacturer'
], function ($, _, require, AssignTonersModal, Template, accounting)
{
    'use strict';
    var HardwareModal_InstanceIdCounter = 0;

    var HardwareModal = function (options)
    {
        /**
         * Each instance gets a unique id
         */
        HardwareModal_InstanceIdCounter++;
        this.id = HardwareModal_InstanceIdCounter;
        var hardwareModalInstance = this;


        var settings = _.extend({
            hardwareId      : 0,
            isAllowed     : false,
            onModalClose  : false
        }, _.pick(options, ['hardwareId', 'isAllowed', 'onModalClose']) || {});

        /**
         * Create Modal
         */
        var $modal = $(document.createElement('div'));
        $modal.addClass('modal fade');
        $('body').append($modal);

        /**
         * Class Members
         */
        this.$modal = $modal;
        this.hardwareId = settings.hardwareId;
        this.isAllowed = !(settings.isAllowed == 'undefined' || settings.isAllowed == 'false');
        this.isCreatingNewHardware = (this.hardwareId === 0);

        /**
         * Cleanup time!
         */
        if (_.isFunction(settings.onModalClose))
        {
            $modal.on('hide.bs.modal', settings.onModalClose);
        }

        // We want to destroy the modal once we're finished with it.
        $modal.on('hide.bs.modal', function ()
        {
            $modal.removeClass('fade');

            if (hardwareModalInstance.assignedTonersDataTable)
                hardwareModalInstance.assignedTonersDataTable.destroy();

            if (hardwareModalInstance.assignTonersModal && hardwareModalInstance.assignTonersModal.assignTonersDataTable)
                hardwareModalInstance.assignTonersModal.assignTonersDataTable.destroy();

            // Timeout was needed as the modal wasn't being destroyed
            window.setTimeout(function ()
            {
                $modal.removeData('bs.modal');
                $modal.remove();
            }, 1000);
        });
    };

    /**
     * A list of urls that the modal uses
     */
    HardwareModal.prototype.urls = {
        "loadForms"               : "/hardware-library/"+window.hardware_type+"/load-forms",
        "delete"                  : "/hardware-library/"+window.hardware_type+"/delete",
        "availableOptions"        : "/hardware-library/options",
        "sauron"                  : "/hardware-library/sauron",
    };

    HardwareModal.prototype.show = function ()
    {
        var hardwareModalInstance = this;
        hardwareModalInstance.updateTabs();
        var $modal = this.$modal;
        var windowWidth = $(window).width();
        var modalOptions = {
            backdrop: 'static'
        };


        if ($(window).width() > 960)
        {
            modalOptions.width = 960;
        }

        $modal.load(this.urls.loadForms,
            {
                hardwareId: this.hardwareId
            },
            function ()
            {
                $modal.modal(modalOptions);

                $modal.find(".js-yes-no-switch").bootstrapSwitch({
                    "onColor" : "success",
                    "offColor": "danger",
                    "onText"  : "Yes",
                    "offText" : "No"
                });

                $modal.find(".js-date-picker").datepicker({
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
        );
    };

    /**
     * Clears all the errors out of the forms
     */
    HardwareModal.prototype.clearErrors = function ()
    {
        this.$modal.find(".has-error").removeClass("has-error");
        this.$modal.find(".has-feedback").removeClass("has-feedback");
        this.$modal.find(".tabError").removeClass("tabError");
        this.$modal.find('.form-error-element').remove();
    };

    /**
     * Used to populate a form's values when pressing the jqgrid button create or edit
     * @param elementPrefix
     * @param formData
     */
    HardwareModal.prototype.populateForm = function (elementPrefix, formData)
    {
        var $modal = this.$modal;
        $.each(formData, function (key)
        {
            $modal.find("#" + elementPrefix + key).val(formData[key]);
        });
    };

    /**
     * Clears all element values within a form
     * @param formName
     */
    HardwareModal.prototype.clearForm = function (formName)
    {
        var elements = document.getElementById(formName).elements;
        $.each(elements, function (index, element)
        {
            element.value = '';
        });
    };

    /**
     * This handles the create or edit jqGrid buttons.
     * It outputs errors onto the forms if received from json
     * @param formName
     * @param shouldAssign
     */
    HardwareModal.prototype.createOrEdit = function (formName, shouldAssign)
    {
        var hardwareModalInstance = this;
        $.ajax({
            url     : this.urls.sauron,
            type    : "post",
            dataType: "json",
            data    : {
                "hardwareId": function ()
                {
                    return hardwareModalInstance.hardwareId;
                },
                "form"          : function ()
                {
                    return $("#" + formName).serialize()
                },
                "formName"      : function ()
                {
                    return formName;
                }
            },
            success : function (xhr)
            {
                /**
                 * Hide our modal (formName.length - 4) is to remove the word
                 * 'form' from the end of the formName, which happens to
                 * always be our jqGrid name!
                 */
                $('#' + formName.substr(0, formName.length - 4) + 'Modal').modal('hide');
                clearErrors();

                /**
                 * Reload our grid
                 */
                $("#" + formName.substr(0, formName.length - 4)).trigger('reloadGrid');

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
            }
        });
    };

    /**
     * Saves the modal
     * @param approve
     */
    HardwareModal.prototype.saveChanges = function (approve)
    {
        var hardwareModalInstance = this;
        $.ajax({
            url     : "/hardware-library/hardware/update",
            type    : "post",
            dataType: "json",
            data    : {
                hardwareId      : function ()
                {
                    return hardwareModalInstance.hardwareId;
                },
                manufacturerId      : $("#manufacturerId").val(),
                modelName           : $("#modelName").val(),
                approve             : approve,
                suppliesAndService  : function ()
                {
                    return $("#suppliesAndService").serialize();
                },
                hardwareAttributes    : $("#hardwareAttributes").serialize(),
                hardwareOptimization: $("#hardwareOptimization").serialize(),
                hardwareQuote       : $("#hardwareQuote").serialize(),
                hardwareImage         : $("#hardwareImage").serialize()
            },
            success : function (xhr)
            {
                hardwareModalInstance.hardwareId = xhr.hardwareId;
                hardwareModalInstance.clearErrors();

                hardwareModalInstance.updateTabs();

                hardwareModalInstance.displayAlert("success", "Successfully updated hardware");

                if (xhr.imageFile) {
                    $('#imageDiv').html('<a href="/img/hardware/'+xhr.imageFile+'" target="_blank" class="thumbnail"><img src="/img/hardware/'+xhr.imageFile+'" style="max-width:300px;max-height:300px"></a>');
                }

                var hardwareManagement = $("#hardwareManagement");
                hardwareManagement.trigger("saveSuccess", [hardwareModalInstance.hardwareId]);
                if (approve)
                {
                    hardwareManagement.trigger("approveSuccess", [hardwareModalInstance.hardwareId]);
                }

                $(hardwareModalInstance).trigger('DeviceModal.saved', [hardwareModalInstance.hardwareId])
            },
            error   : function (xhr)
            {
                hardwareModalInstance.clearErrors();

                var $launchDate = $("#launchDate");

                /**
                 * We need to destroy the launchDate date picker
                 */
                $launchDate.datepicker("destroy");

                var data = $.parseJSON(xhr.responseText);
                var errorMessage;
                if (data['error']['modelAndManufacturer'] != undefined)
                {
                    //Lets loop through and display the errors
                    $.each(data['error']['modelAndManufacturer']['errorMessages'], function (key, value)
                    {
                        var $formControl = hardwareModalInstance.$modal.find('input[name="' + key + '"]');
                        var $formControlParent = $formControl.parent();
                        var $formGroup = $formControlParent.parent();

                        if (!$formGroup.hasClass('has-feedback'))
                        {
                            $formGroup.addClass('has-feedback has-error');
                            $formControlParent.append('<span class="glyphicon glyphicon-remove form-control-feedback form-error-element"></span>');
                            $formControlParent.append("<span class='help-block form-error-element'>" + value + "</span>");
                        }
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


                        var $formControl = hardwareModalInstance.$modal.find('input[name="' + elementKey + '"]');
                        var $formControlParent = $formControl.parent();
                        var $formGroup = $formControlParent.parent();

                        if (!$formGroup.hasClass('has-feedback'))
                        {
                            $formGroup.addClass('has-feedback has-error');
                            $formControlParent.append('<span class="glyphicon glyphicon-remove form-control-feedback form-error-element"></span>');
                            $formControlParent.append("<span class='help-block form-error-element'>" + errorMessage + "</span>");
                        }
                    });
                });

                // We need to recreate the datepicker when we have an error!
                // Note we reselect using jQuery it because it gets deleted above somewhere (Lee thinks the "destroy" part of the datepicker but we don't know.
                $launchDate.datepicker({
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


                hardwareModalInstance.displayAlert("danger", "Please fix the errors before continuing");
            }
        });
    };

    /**
     * Displays a message at the top of the page.
     *
     * @param type The type of alert to show, success/alert/danger
     * @param message
     */
    HardwareModal.prototype.displayAlert = function (type, message)
    {
        $("#alertMessage")
            .attr("class", "alert alert-" + type)
            .html("<span>" + message + "</span>")
            .show();
    };

    /**
     * Handles updating the tabs
     */
    HardwareModal.prototype.updateTabs = function ()
    {
        var $hardwareOptimizationTab = $("#hardwareOptimizationTopTab");
        var $hardwareQuoteTab = $("#hardwareQuoteTopTab");
        var $availableOptionsTab = $("#availableOptionsTopTab");
        var $hardwareConfigurationsTab = $("#hardwareConfigurationsTopTab");

        if (this.hardwareId > 0)
        {
            var $isSellingElement = this.$modal.find('input[type="checkbox"][name="isSelling"]');
            var isSelling = false;
            if ($isSellingElement.length > 0)
            {
                isSelling = $isSellingElement[0].checked;
            }

            if ($hardwareOptimizationTab && isSelling)
            {
                $hardwareOptimizationTab.show();
            }
            else
            {
                $hardwareOptimizationTab.hide();
            }

            if ($hardwareQuoteTab)
            {
                $hardwareQuoteTab.show();
            }

            if ($availableOptionsTab)
            {
                if (isSelling)
                {
                    $availableOptionsTab.show();
                    $hardwareConfigurationsTab.show();
                    $("#hardwareConfigurations").trigger('reloadGrid');
                    $("#availableOptions").trigger('reloadGrid');
                }
                else
                {
                    $availableOptionsTab.hide();
                    $hardwareConfigurationsTab.hide();
                }
            }
        }
        else
        {
            $hardwareOptimizationTab.hide();
            $hardwareQuoteTab.hide();
            $availableOptionsTab.hide();
            $hardwareConfigurationsTab.hide();
        }
    };

    return HardwareModal;
});

function uploadDone (e, result) {
    var filename = result._response.result.filename;
    $('#imageDiv').html('<a href="/img/hardware/'+filename+'" target="_blank" class="thumbnail"><img src="/img/hardware/'+filename+'" style="max-width:300px;max-height:300px"></a>');
}
