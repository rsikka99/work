define([
    'jquery',
    'underscore',
    'require',
    'app/Templates',
    'accounting',
    'jqgrid',
    'bootstrap.modal.manager'
], function ($, _, require, AssignTonersModal, Template, accounting)
{
    'use strict';
    var SkuModal_InstanceIdCounter = 0;

    var SkuModal = function (options)
    {
        /**
         * Each instance gets a unique id
         */
        SkuModal_InstanceIdCounter++;
        this.id = SkuModal_InstanceIdCounter;
        var modalInstance = this;


        var settings = _.extend({
            skuId      : 0,
            fromSupplier: '',
            categoryId : 0,
            isAllowed     : false,
            onModalClose  : false
        }, _.pick(options, ['skuId', 'fromSupplier', 'categoryId', 'isAllowed', 'onModalClose']) || {});

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
        this.skuId = settings.skuId;
        this.fromSupplier = settings.fromSupplier;
        this.categoryId = settings.categoryId;
        this.isAllowed = (settings.isAllowed == 'true' || settings.isAllowed == '1'); //!(settings.isAllowed == 'undefined' || settings.isAllowed == 'false');
        this.isCreating = (this.skuId === 0);

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
    SkuModal.prototype.urls = {
        "loadForms"               : "/hardware-library/sku/load-forms",
        "delete"                  : "/hardware-library/sku/delete"
    };

    SkuModal.prototype.show = function ()
    {
        var modalInstance = this;
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
                skuId: this.skuId,
                fromSupplier: this.fromSupplier,
                categoryId : this.categoryId
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

                $modal.find('.js-save-sku-modal').on('click', function ()
                {
                    modalInstance.saveChanges(false);
                });


            }
        );
    };

    /**
     * Clears all the errors out of the forms
     */
    SkuModal.prototype.clearErrors = function ()
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
    SkuModal.prototype.populateForm = function (elementPrefix, formData)
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
    SkuModal.prototype.clearForm = function (formName)
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
    SkuModal.prototype.createOrEdit = function (formName, shouldAssign)
    {
        var modalInstance = this;
        $.ajax({
            url     : this.urls.sauron,
            type    : "post",
            dataType: "json",
            data    : {
                "skuId": function ()
                {
                    return modalInstance.skuId;
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
    SkuModal.prototype.saveChanges = function (approve)
    {
        var modalInstance = this;
        $.ajax({
            url     : "/hardware-library/sku/update",
            type    : "post",
            dataType: "json",
            data    : {
                skuId      : this.skuId,
                categoryId      : $("#categoryId").val(),
                manufacturerId      : $("#manufacturerId").val(),
                modelName           : $("#modelName").val(),
                sku           : $("#sku").val(),
                weight           : $("#weight").val(),
                UPC           : $("#UPC").val(),
                skuAttributes    : $("#skuAttributes").serialize(),
                skuQuote       : $("#skuQuote").serialize(),
                skuImage         : $("#skuImage").serialize()
            },
            success : function (xhr)
            {
                modalInstance.skuId = xhr.skuId;
                modalInstance.clearErrors();

                modalInstance.displayAlert("success", "Successfully updated sku");

                $(window).trigger("skuSaveSuccess", modalInstance.skuId);
            },
            error   : function (xhr)
            {
                modalInstance.clearErrors();

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
                        var $formControl = modalInstance.$modal.find('input[name="' + key + '"], select[name="' + key + '"]');
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


                        var $formControl = modalInstance.$modal.find('input[name="' + elementKey + '"]');
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


                modalInstance.displayAlert("danger", "Please fix the errors before continuing");
            }
        });
    };

    /**
     * Displays a message at the top of the page.
     *
     * @param type The type of alert to show, success/alert/danger
     * @param message
     */
    SkuModal.prototype.displayAlert = function (type, message)
    {
        $("#alertMessage")
            .attr("class", "alert alert-" + type)
            .html("<span>" + message + "</span>")
            .show();
    };

    return SkuModal;
});

function uploadDone (e, result) {
    var filename = result._response.result.filename;
    $('#imageDiv').html('<a href="/img/sku/'+filename+'" target="_blank" class="thumbnail"><img src="/img/sku/'+filename+'" style="max-width:300px;max-height:300px"></a>');
}
