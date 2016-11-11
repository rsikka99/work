define([
    'jquery',
    'underscore',
    'require',
    './AssignTonersModal',
    'app/Templates',
    'accounting',
    'jqgrid',
    'bootstrap.modal.manager'
], function ($, _, require, AssignTonersModal, Template, accounting)
{
    'use strict';
    var DeviceModal_InstanceIdCounter = 0;

    var DeviceModal = function (options)
    {
        /**
         * Each instance gets a unique id
         */
        DeviceModal_InstanceIdCounter++;
        this.id = DeviceModal_InstanceIdCounter;
        var deviceModalInstance = this;
        window.deviceModalInstance = this;

        var settings = _.extend({
            supplies: [],
            rmsUploadRowId: 0,
            rmsDeviceInstanceId: 0,
            deviceId      : 0,
            isAllowed     : false,
            onModalClose  : false
        }, _.pick(options, ['supplies','rmsUploadRowId', 'rmsDeviceInstanceId', 'deviceId', 'isAllowed', 'onModalClose']) || {});

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
        this.supplies = settings.supplies;
        this.rmsUploadRowId = settings.rmsUploadRowId;
        this.rmsDeviceInstanceId = settings.rmsDeviceInstanceId;
        this.deviceId = settings.deviceId;
        this.isAllowed = (settings.isAllowed == 'true' || settings.isAllowed == '1'); //!(settings.isAllowed == 'undefined' || settings.isAllowed == 'false');
        this.isCreatingNewDevice = (this.deviceId === 0);

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
    DeviceModal.prototype.urls = {
        "loadForms"               : "/hardware-library/devices/load-forms",
        "delete"                  : "/hardware-library/devices/delete",
        "assignedToners"          : "/hardware-library/devices/toner-list",
        "availableOptions"        : "/hardware-library/options",
        "sauron"                  : "/hardware-library/sauron",
        "deviceConfigurationList" : "/hardware-library/configurations/list",
        "reloadHardwareConfigForm": "/hardware-library/configurations/reload-form"
    };

    DeviceModal.prototype.show = function ()
    {
        var deviceModalInstance = this;
        deviceModalInstance.updateTabs();
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
                masterDeviceId: this.deviceId,
                rmsUploadRowId: this.rmsUploadRowId,
                rmsDeviceInstanceId: this.rmsDeviceInstanceId
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

                deviceModalInstance.updateSupplies();
                deviceModalInstance.updateTabs();
                deviceModalInstance.loadAvailableOptions();

                $modal.find('#search-supply').on('change', function() {
                    deviceModalInstance.supplies.push($modal.find('#search-supply').val());
                    deviceModalInstance.updateSupplies();
                });

                $modal.find('.js-save-device-modal').on('click', function ()
                {
                    deviceModalInstance.saveChanges(false);
                });

                $modal.find('.js-save-and-approve-device-modal').on('click', function ()
                {
                    deviceModalInstance.saveChanges(true);
                });

                var $leasedTonerControlGroupElement = $modal.find('input[name="leasedTonerYield"]').parent('.form-group');
                var $isLeased = $modal.find('input[type="checkbox"][name="isLeased"]');

                if ($isLeased.prop('checked'))
                {
                    $leasedTonerControlGroupElement.show();
                }
                else
                {
                    $leasedTonerControlGroupElement.hide();
                }

                $isLeased.on("change", function ()
                {
                    if ($isLeased.prop('checked'))
                    {
                        $leasedTonerControlGroupElement.show();
                    }
                    else
                    {
                        $leasedTonerControlGroupElement.hide();
                    }
                });

            }
        );
    };

    /**
     * Clears all the errors out of the forms
     */
    DeviceModal.prototype.clearErrors = function ()
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
    DeviceModal.prototype.populateForm = function (elementPrefix, formData)
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
    DeviceModal.prototype.clearForm = function (formName)
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
    DeviceModal.prototype.createOrEdit = function (formName, shouldAssign)
    {
        var deviceModalInstance = this;
        $.ajax({
            url     : this.urls.sauron,
            type    : "post",
            dataType: "json",
            data    : {
                "masterDeviceId": function ()
                {
                    return deviceModalInstance.deviceId;
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
    DeviceModal.prototype.saveChanges = function (approve)
    {
        var deviceModalInstance = this;
        $.ajax({
            url     : "/hardware-library/devices/update-master-device",
            type    : "post",
            dataType: "json",
            data    : {
                masterDeviceId      : function ()
                {
                    return deviceModalInstance.deviceId;
                },
                manufacturerId      : $("#manufacturerId").val(),
                modelName           : $("#modelName").val(),
                sku                 : $("#sku").val(),
                weight              : $("#weight").val(),
                UPC                 : $("#UPC").val(),
                tech                : $("#tech").val(),
                "tonerIds"          : function ()
                {
                    return deviceModalInstance.supplies.join(",");
                },
                approve             : approve,
                suppliesAndService  : function ()
                {
                    return $("#suppliesAndService").serialize();
                },
                deviceAttributes    : $("#deviceAttributes").serialize(),
                hardwareOptimization: $("#hardwareOptimization").serialize(),
                hardwareQuote       : $("#hardwareQuote").serialize(),
                deviceImage         : $("#deviceImage").serialize()
            },
            success : function (xhr)
            {
                // Lets update our master device id, this tells updateTabs that we should show more tabs
                deviceModalInstance.deviceId = xhr.masterDeviceId;
                deviceModalInstance.clearErrors();

                // Shows or hides the Available Options and Hardware Configuration tabs depending on if it is a quote device
                deviceModalInstance.updateTabs();

                // This calls our custom event called saveSuccess
                deviceModalInstance.displayAlert("success", "Successfully updated device");

                var masterDeviceManagement = $("#masterDeviceManagement");
                masterDeviceManagement.trigger("saveSuccess", [deviceModalInstance.deviceId]);
                if (approve)
                {
                    masterDeviceManagement.trigger("approveSuccess", [deviceModalInstance.deviceId]);
                }

                $(deviceModalInstance).trigger('DeviceModal.saved', [deviceModalInstance.deviceId])

                if (xhr.history) {
                    var str='';
                    $.each(xhr.history, function(i,e) {
                        str+= '<tr><td>'+ e.date +'</td><td>'+ e.user +'</td><td>'+ e.action +'</td></tr>';
                    });
                    $('#historyTab tbody').html(str);
                }

            },
            error   : function (xhr)
            {
                deviceModalInstance.clearErrors();

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
                        var $formControl = deviceModalInstance.$modal.find('input[name="' + key + '"]');
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


                        var $formControl = deviceModalInstance.$modal.find('input[name="' + elementKey + '"]');
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


                deviceModalInstance.displayAlert("danger", "Please fix the errors before continuing");
            }
        });
    };

    /**
     * Displays a message at the top of the page.
     *
     * @param type The type of alert to show, success/alert/danger
     * @param message
     */
    DeviceModal.prototype.displayAlert = function (type, message)
    {
        $("#alertMessage")
            .attr("class", "alert alert-" + type)
            .html("<span>" + message + "</span>")
            .show();
    };

    DeviceModal.prototype.updateSupplies = function() {
        var mfgId = $("#manufacturerId").val();
        var modalInstance = this;
        $.post('/hardware-library/manage-devices/supplies', {supplies: modalInstance.supplies, deviceId: this.deviceId, mfgId: mfgId}, function(r) {
            modalInstance.supplies = r.supplies;
            $.each(['main','other','compatible'], function (i,n) {
                var tr='';
                $.each(r[n], function(j,line) {
                    tr+='<tr>';
                    $.each(line, function(k,cell) {
                        tr+='<td>'+cell+'</td>';
                    });
                    tr+='</tr>';
                });
                $('#'+n+'-supplies-table tbody').html(tr);
            });
            $('.edit-supply').click(function() {
                var id = $(this).attr('data-id');
                modalInstance.editSupply(id);
            });
            $('.edit-device').click(function() {
                var id = $(this).attr('data-id');
                modalInstance.editDevice(id);
            });
            $('.unassign-supply').click(function() {
                if (!window.confirm('Unassign this supply?')) return;
                var id = $(this).attr('data-id');
                var new_supplies = [];
                $(modalInstance.supplies).each(function(i,e) {
                    if (e!=id) new_supplies.push(e);
                });
                modalInstance.supplies = new_supplies;
                modalInstance.updateSupplies();
            });
            $('.addon-supply').click(function() {
                var $el = $(this);
                $el.text('working...');
                var id = $(this).attr('data-id');
                $.post('/hardware-library/manage-devices/addon-supply', { id:id, deviceId:modalInstance.deviceId }, function(r) {
                    $el.remove();
                    loadAddons();
                }, 'json');
            });
        }, 'json');
    };

    /**
     * Handles updating the tabs
     */
    DeviceModal.prototype.updateTabs = function ()
    {
        var $hardwareOptimizationTab = $("#hardwareOptimizationTopTab");
        var $hardwareQuoteTab = $("#hardwareQuoteTopTab");
        var $availableOptionsTab = $("#availableOptionsTopTab");
        var $hardwareConfigurationsTab = $("#hardwareConfigurationsTopTab");

        if (this.deviceId > 0)
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

    DeviceModal.prototype.loadAvailableOptions = function ()
    {
        var that = this;
        require(['./OptionService', 'app/components/jqGrid/DeviceModal/AvailableOptionsGrid'], function (OptionService)
        {
            var deviceModalInstance = that;

            var $availableOptions = $('.js-available-options-grid');
            var $availableOptionFilterSku = $('.js-filter-options-sku');
            var $availableOptionFilterText = $('.js-filter-options-text');
            var $availableOptionFilterAssigned = $('.js-filter-options-assigned');
            var $resetFilterButton = $('.js-reset-filter');

            $resetFilterButton.on("click",function (e)
            {
                $availableOptionFilterSku.val('');
                $availableOptionFilterText.val('');
                $availableOptionFilterAssigned.val('');
                $availableOptions.availableOptionGrid('reloadGrid');
            });

            var $availableOptionsModal = $('#available-options-gridModal');

            $availableOptions.availableOptionGrid({
                "deviceId"        : function ()
                {
                    return deviceModalInstance.deviceId;
                },
                "filterOptionText": function ()
                {
                    return $availableOptionFilterText.val();
                },
                "filterOptionSku" : function ()
                {
                    return $availableOptionFilterSku.val();
                },
                "filterAssigned" : function () {
                    return $availableOptionFilterAssigned.val();
                },
                url               : TMTW_BASEURL + "hardware-library/options"
            });

            /**
             * Setup type watch on toner sku so that we automatically
             * filter when someone types in a sku.
             */
            var typeWatchOptions = {
                callback     : function (value)
                {
                    $availableOptions.availableOptionGrid('reloadGrid');
                },
                wait         : 750,
                highlight    : true,
                captureLength: 0
            };

            $availableOptionFilterSku.typeWatch(typeWatchOptions);
            $availableOptionFilterText.typeWatch(typeWatchOptions);

            $availableOptionFilterAssigned.change(function() {
                $availableOptions.availableOptionGrid('reloadGrid');
            });

            $availableOptions.on('assign-option', {"deviceModalInstance": deviceModalInstance}, function (event, optionId, $button)
            {
                var deviceModalInstance = event.data.deviceModalInstance;
                OptionService.assignOptionToDevice(optionId, deviceModalInstance.deviceId).then(function (value)
                {
                    if ($button.hasClass('js-assign-option'))
                    {
                        $button.removeClass("btn-success js-assign-option").addClass("btn-danger js-unassign-option").html("Unassign");
                    }

                }, function ()
                {
                    // Do nothing on error?
                });

            });

            $availableOptions.on('unassign-option', {"deviceModalInstance": deviceModalInstance}, function (event, optionId, $button)
            {
                var deviceModalInstance = event.data.deviceModalInstance;
                OptionService.assignOptionToDevice(optionId, deviceModalInstance.deviceId).then(function (value)
                {
                    if ($button.hasClass('js-unassign-option'))
                    {
                        $button.removeClass("btn-danger js-unassign-option").addClass("btn-success js-assign-option").val("Assign").text('Assign');
                    }

                }, function ()
                {
                    // Do nothing on error?
                });
            });
        });
    };

    DeviceModal.prototype.editSupply = function(tonerId) {

        var deviceModalInstance = this;

        require([
            'app/legacy/hardware-library/manage-devices/TonerForm'
        ], function (TonerForm)
        {
            var tonerForm = new TonerForm({
                "tonerId"      : tonerId
            });

            $(tonerForm).on('toner-form.saved', function (event, tonerId)
            {
                deviceModalInstance.updateSupplies();
            });

            tonerForm.show();
        });
    };

    DeviceModal.prototype.editDevice = function(deviceId) {

        var deviceModalInstance = this;
        var isAllowed = deviceModalInstance.isAllowed;

        deviceModalInstance.$modal.modal('hide');
        window.setTimeout(function() {
            var newForm = new DeviceModal({
                isAllowed    : isAllowed,
                deviceId      : deviceId
            });
            newForm.show();
        }, 150);
    };

    return DeviceModal;
});

