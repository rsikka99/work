define([
    'jquery',
    'underscore',
    'require',
    './AssignTonersModal',
    'app/Templates',
    'accounting',
    'jqgrid',
    'bootstrap.modal.manager',
    '../../../components/Select2/Manufacturer'
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


        var settings = _.extend({
            rmsUploadRowId: 0,
            deviceId      : 0,
            isAllowed     : false,
            onModalClose  : false
        }, _.pick(options, ['rmsUploadRowId', 'deviceId', 'isAllowed', 'onModalClose']) || {});

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
        this.rmsUploadRowId = settings.rmsUploadRowId;
        this.deviceId = settings.deviceId;
        this.isAllowed = !(settings.isAllowed == 'undefined' || settings.isAllowed == 'false');
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

            if (deviceModalInstance.assignedTonersDataTable)
                deviceModalInstance.assignedTonersDataTable.destroy();

            if (deviceModalInstance.assignTonersModal && deviceModalInstance.assignTonersModal.assignTonersDataTable)
                deviceModalInstance.assignTonersModal.assignTonersDataTable.destroy();

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

        $modal.on('DeviceModal.unassign-toner', function (event, tonerId, $element)
        {
            deviceModalInstance.unassignToner(tonerId);
            $element.removeClass('btn-danger js-unassign-toner').addClass('btn-success js-assign-toner').text('Assign');
        });

        $modal.on('DeviceModal.assign-toner', function (event, tonerId, $element)
        {
            deviceModalInstance.assignToner(tonerId);
            $element.removeClass('btn-success js-assign-toner').addClass('btn-danger js-unassign-toner').text('Unassign');
        });

        $modal.load(this.urls.loadForms,
            {
                masterDeviceId: this.deviceId,
                rmsUploadRowId: this.rmsUploadRowId
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

                /**
                 * Available Toners Manufacturer search
                 */
                $modal.find('#manufacturerId').selectManufacturer();

                require(['app/components/jqGrid/DeviceModal/AssignedTonersGrid'], function (AssignedTonersGrid)
                {
                    deviceModalInstance.$assignedTonersGrid = new AssignedTonersGrid(deviceModalInstance.$modal.find('.js-assigned-toners-grid'), {
                        "deviceId"     : function ()
                        {
                            return deviceModalInstance.deviceId;
                        },
                        "tonerConfigId": function ()
                        {
                            return deviceModalInstance.$modal.find('select[name="tonerConfigId"]').val();
                        },
                        "isAllowed"    : deviceModalInstance.isAllowed,
                        "url"          : deviceModalInstance.urls.assignedToners
                    });

                    deviceModalInstance.loadAvailableOptions();

                    // FIXME lrobert: Implement favorite configurations
                    //deviceModalInstance.loadFavoriteConfigurations();

                    var $assignTonersButton = jQuery('.js-assign-toners-button');
                    deviceModalInstance.assignTonersModal = new AssignTonersModal({
                        "deviceModalInstance": deviceModalInstance,
                        "assignTonersModal"  : $modal.find('.js-assign-toners-modal')
                    });

                    $assignTonersButton.on('click', function ()
                    {
                        deviceModalInstance.assignTonersModal.show();
                    });

                    deviceModalInstance.updateTabs();

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
                "tonerIds"          : function ()
                {
                    return deviceModalInstance.$assignedTonersGrid.getTonerList().join(",");
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

                if (xhr.imageFile) {
                    $('#imageDiv').html('<a href="/img/devices/'+xhr.imageFile+'" target="_blank" class="thumbnail"><img src="/img/devices/'+xhr.imageFile+'" style="max-width:300px;max-height:300px"></a>');
                }

                var masterDeviceManagement = $("#masterDeviceManagement");
                masterDeviceManagement.trigger("saveSuccess", [deviceModalInstance.deviceId]);
                if (approve)
                {
                    masterDeviceManagement.trigger("approveSuccess", [deviceModalInstance.deviceId]);
                }

                $(deviceModalInstance).trigger('DeviceModal.saved', [deviceModalInstance.deviceId])
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

    /**
     * Event Handler to add  a toner id to the tonerList variable.
     *
     * Requires deviceModalInstance to be bound to the event data
     *
     * @param event
     */
    DeviceModal.prototype.assignTonerButtonHandler = function (event)
    {
        var $element = $(this);
        var tonerId = $element.data('toner-id');
        event.data.deviceModalInstance.$modal.trigger('DeviceModal.assign-toner', [tonerId, $element]);
    };

    /**
     * Event Handler to remove a toner id to the tonerList variable.
     *
     * Requires deviceModalInstance to be bound to the event data
     *
     * @param event
     */
    DeviceModal.prototype.unassignTonerButtonHandler = function (event)
    {
        var $element = $(this);
        var tonerId = $element.data('toner-id');
        event.data.deviceModalInstance.$modal.trigger('DeviceModal.unassign-toner', [tonerId, $element]);
    };

    /**
     * Adds a toner to the list
     *
     * @param tonerId
     */
    DeviceModal.prototype.assignToner = function (tonerId)
    {
        this.$assignedTonersGrid.addToner(parseInt(tonerId));
    };

    /**
     * Removes a toner from the list
     *
     * @param tonerId
     */
    DeviceModal.prototype.unassignToner = function (tonerId)
    {
        this.$assignedTonersGrid.removeToner(tonerId);
    };

    /**
     * Reloads the assigned and available toner grids
     */
    DeviceModal.prototype.reloadTonersGrids = function ()
    {
        this.$assignedTonersGrid.reloadGrid();
        this.assignTonersModal.$assignTonersGrid.trigger('reloadGrid');
    };

    DeviceModal.prototype.loadFavoriteConfigurations = function ()
    {
        var deviceModalInstance = this;

        var $hardwareConfigurationsTable = $('#hardwareConfigurations');
        var $hardwareConfigurationsForm = $('#hardwareConfigurationsForm');

        /**
         * Hardware Configurations
         */
        $hardwareConfigurationsTable.jqGrid(
            {
                url         : deviceModalInstance.urls.deviceConfigurationList,
                datatype    : 'json',
                postData    : {
                    "masterDeviceId": function ()
                    {
                        return deviceModalInstance.deviceId;
                    }
                },
                colModel    : [
//@formatter:off
{ width: 130, name: 'id',          index: 'id',          label: 'ID',                 hidden: true                   },
{ width: 268, name: 'name',        index: 'name',        label: 'Configuration Name', sortable: true, editable: true },
{ width: 630, name: 'description', index: 'description', label: 'Description',        sortable: true, editable: true }
//@formatter:on
                ],
                jsonReader  : {
                    repeatitems: false
                },
                height      : 'auto',
                rowNum      : 15,
                pager       : '#hardwareConfigurations_Pager',
                toppager    : true,
                gridComplete: function ()
                {
                }
            }
        );

        /**
         * Hide the top paging
         */
        $('#hardwareConfigurations_toppager_center').hide();

        /**
         * Add to the top pager the create, edit and delete buttons
         */
        $hardwareConfigurationsTable
            .navGrid('#hardwareConfigurations_toppager', {
                edit   : false,
                add    : false,
                del    : false,
                search : false,
                refresh: false
            })
        /**
         * Create New button
         */
            .navButtonAdd('#hardwareConfigurations_toppager', {
                caption      : "Create New",
                buttonicon   : "ui-icon-plus",
                onClickButton: function ()
                {
                    $hardwareConfigurationsForm.load(deviceModalInstance.urls.reloadHardwareConfigForm,
                        {
                            "masterDeviceId": function ()
                            {
                                return deviceModalInstance.deviceId;
                            }
                        },
                        function ()
                        {
                            deviceModalInstance.clearForm("hardwareConfigurationsForm");
                        }
                    );
                    $('#hardwareConfigurationsTitle').html("Add New Configuration");
                    $("#hardwareConfigurationsModal").modal('show');
                },
                position     : "last"
            })
        /**
         * Edit Button
         */
            .navButtonAdd('#hardwareConfigurations_toppager', {
                caption      : "Edit",
                buttonicon   : "ui-icon-pencil",
                onClickButton: function ()
                {
                    var selectedRow = $hardwareConfigurationsTable.jqGrid('getGridParam', 'selrow');
                    if (selectedRow)
                    {
                        $hardwareConfigurationsForm.load(deviceModalInstance.urls.reloadHardwareConfigForm,
                            {
                                "id"            : function ()
                                {
                                    return selectedRow;
                                },
                                "masterDeviceId": function ()
                                {
                                    return deviceModalInstance.deviceId;
                                }
                            },
                            function ()
                            {
                                $('#hardwareConfigurationsid').val(selectedRow);
                            }
                        );
                        $('#hardwareConfigurationsTitle').html("Edit Configuration");
                        $('#hardwareConfigurationsModal').modal('show');
                    }
                    else
                    {
                        $("#alertMessageModal").modal().show()
                    }
                },
                position     : "last"
            })
        /**
         * Delete Button
         */
            .navButtonAdd('#hardwareConfigurations_toppager', {
                caption      : "Delete",
                buttonicon   : "ui-icon-trash",
                onClickButton: function ()
                {
                    var selectedRow = $hardwareConfigurationsTable.jqGrid('getGridParam', 'selrow');
                    if (selectedRow)
                    {
                        $('#deleteId').val(selectedRow);
                        $('#deleteFormName').val('hardwareConfigurations');
                        $('#deleteModal').modal('show');
                    }
                    else
                    {
                        $("#alertMessageModal").modal().show()
                    }
                },
                position     : "last"
            });
    };


    return DeviceModal;
});

function uploadDone (e, result) {
    var filename = result._response.result.filename;
    $('#imageDiv').html('<a href="/img/devices/'+filename+'" target="_blank" class="thumbnail"><img src="/img/devices/'+filename+'" style="max-width:300px;max-height:300px"></a>');
}
