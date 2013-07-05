// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function f()
{
    log.history = log.history || [];
    log.history.push(arguments);
    if (this.console)
    {
        var args = arguments, newarr;
        args.callee = args.callee.caller;
        newarr = [].slice.call(args);
        if (typeof console.log === 'object')
        {
            log.apply.call(console.log, console, newarr);
        }
        else
        {
            console.log.apply(console, newarr);
        }
    }
};

// make it safe to use console.log always
(function (a)
{
    function b()
    {
    }

    for (var c = "assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","), d; !!(d = c.pop());)
    {
        a[d] = a[d] || b;
    }
})
    (function ()
    {
        try
        {
            console.log();
            return window.console;
        }
        catch (a)
        {
            return (window.console = {});
        }
    }());


// place any jQuery/helper plugins in here, instead of separate, slower script files.

/*
 * jQuery timepicker addon
 * By: Trent Richardson [http://trentrichardson.com]
 * Version 1.0.0
 * Last Modified: 02/05/2012
 *
 * Copyright 2012 Trent Richardson
 * Dual licensed under the MIT and GPL licenses.
 * http://trentrichardson.com/Impromptu/GPL-LICENSE.txt
 * http://trentrichardson.com/Impromptu/MIT-LICENSE.txt
 *
 * HERES THE CSS:
 * .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
 * .ui-timepicker-div dl { text-align: left; }
 * .ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
 * .ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
 * .ui-timepicker-div td { font-size: 90%; }
 * .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
 */

(function ($)
{

// Prevent "Uncaught RangeError: Maximum call stack size exceeded"
    $.ui.timepicker = $.ui.timepicker || {};
    if ($.ui.timepicker.version)
    {
        return;
    }

    $.extend($.ui, { timepicker: { version: "1.0.0" } });

    /* Time picker manager.
     Use the singleton instance of this class, $.timepicker, to interact with the time picker.
     Settings for (groups of) time pickers are maintained in an instance object,
     allowing multiple different settings on the same page. */

    function Timepicker()
    {
        this.regional = []; // Available regional settings, indexed by language code
        this.regional[''] = { // Default regional settings
            currentText  : 'Now',
            closeText    : 'Done',
            ampm         : false,
            amNames      : ['AM', 'A'],
            pmNames      : ['PM', 'P'],
            timeFormat   : 'hh:mm tt',
            timeSuffix   : '',
            timeOnlyTitle: 'Choose Time',
            timeText     : 'Time',
            hourText     : 'Hour',
            minuteText   : 'Minute',
            secondText   : 'Second',
            millisecText : 'Millisecond',
            timezoneText : 'Time Zone'
        };
        this._defaults = { // Global defaults for all the datetime picker instances
            showButtonPanel : true,
            timeOnly        : false,
            showHour        : true,
            showMinute      : true,
            showSecond      : false,
            showMillisec    : false,
            showTimezone    : false,
            showTime        : true,
            stepHour        : 1,
            stepMinute      : 1,
            stepSecond      : 1,
            stepMillisec    : 1,
            hour            : 0,
            minute          : 0,
            second          : 0,
            millisec        : 0,
            timezone        : '+0000',
            hourMin         : 0,
            minuteMin       : 0,
            secondMin       : 0,
            millisecMin     : 0,
            hourMax         : 23,
            minuteMax       : 59,
            secondMax       : 59,
            millisecMax     : 999,
            minDateTime     : null,
            maxDateTime     : null,
            onSelect        : null,
            hourGrid        : 0,
            minuteGrid      : 0,
            secondGrid      : 0,
            millisecGrid    : 0,
            alwaysSetTime   : true,
            separator       : ' ',
            altFieldTimeOnly: true,
            showTimepicker  : true,
            timezoneIso8609 : false,
            timezoneList    : null,
            addSliderAccess : false,
            sliderAccessArgs: null
        };
        $.extend(this._defaults, this.regional['']);
    };

    $.extend(Timepicker.prototype, {
        $input              : null,
        $altInput           : null,
        $timeObj            : null,
        inst                : null,
        hour_slider         : null,
        minute_slider       : null,
        second_slider       : null,
        millisec_slider     : null,
        timezone_select     : null,
        hour                : 0,
        minute              : 0,
        second              : 0,
        millisec            : 0,
        timezone            : '+0000',
        hourMinOriginal     : null,
        minuteMinOriginal   : null,
        secondMinOriginal   : null,
        millisecMinOriginal : null,
        hourMaxOriginal     : null,
        minuteMaxOriginal   : null,
        secondMaxOriginal   : null,
        millisecMaxOriginal : null,
        ampm                : '',
        formattedDate       : '',
        formattedTime       : '',
        formattedDateTime   : '',
        timezoneList        : null,

        /* Override the default settings for all instances of the time picker.
         @param  settings  object - the new settings to use as defaults (anonymous object)
         @return the manager object */
        setDefaults         : function (settings)
        {
            extendRemove(this._defaults, settings || {});
            return this;
        },

        //########################################################################
        // Create a new Timepicker instance
        //########################################################################
        _newInst            : function ($input, o)
        {
            var tp_inst = new Timepicker(),
                inlineSettings = {};

            for (var attrName in this._defaults)
            {
                var attrValue = $input.attr('time:' + attrName);
                if (attrValue)
                {
                    try
                    {
                        inlineSettings[attrName] = eval(attrValue);
                    }
                    catch (err)
                    {
                        inlineSettings[attrName] = attrValue;
                    }
                }
            }
            tp_inst._defaults = $.extend({}, this._defaults, inlineSettings, o, {
                beforeShow       : function (input, dp_inst)
                {
                    if ($.isFunction(o.beforeShow))
                    {
                        return o.beforeShow(input, dp_inst, tp_inst);
                    }
                },
                onChangeMonthYear: function (year, month, dp_inst)
                {
                    // Update the time as well : this prevents the time from disappearing from the $input field.
                    tp_inst._updateDateTime(dp_inst);
                    if ($.isFunction(o.onChangeMonthYear))
                    {
                        o.onChangeMonthYear.call($input[0], year, month, dp_inst, tp_inst);
                    }
                },
                onClose          : function (dateText, dp_inst)
                {
                    if (tp_inst.timeDefined === true && $input.val() != '')
                    {
                        tp_inst._updateDateTime(dp_inst);
                    }
                    if ($.isFunction(o.onClose))
                    {
                        o.onClose.call($input[0], dateText, dp_inst, tp_inst);
                    }
                },
                timepicker       : tp_inst // add timepicker as a property of datepicker: $.datepicker._get(dp_inst, 'timepicker');
            });
            tp_inst.amNames = $.map(tp_inst._defaults.amNames, function (val)
            {
                return val.toUpperCase();
            });
            tp_inst.pmNames = $.map(tp_inst._defaults.pmNames, function (val)
            {
                return val.toUpperCase();
            });

            if (tp_inst._defaults.timezoneList === null)
            {
                var timezoneList = [];
                for (var i = -11; i <= 12; i++)
                {
                    timezoneList.push((i >= 0 ? '+' : '-') + ('0' + Math.abs(i).toString()).slice(-2) + '00');
                }
                if (tp_inst._defaults.timezoneIso8609)
                {
                    timezoneList = $.map(timezoneList, function (val)
                    {
                        return val == '+0000' ? 'Z' : (val.substring(0, 3) + ':' + val.substring(3));
                    });
                }
                tp_inst._defaults.timezoneList = timezoneList;
            }

            tp_inst.hour = tp_inst._defaults.hour;
            tp_inst.minute = tp_inst._defaults.minute;
            tp_inst.second = tp_inst._defaults.second;
            tp_inst.millisec = tp_inst._defaults.millisec;
            tp_inst.ampm = '';
            tp_inst.$input = $input;

            if (o.altField)
            {
                tp_inst.$altInput = $(o.altField)
                    .css({ cursor: 'pointer' })
                    .focus(function ()
                    {
                        $input.trigger("focus");
                    });
            }

            if (tp_inst._defaults.minDate == 0 || tp_inst._defaults.minDateTime == 0)
            {
                tp_inst._defaults.minDate = new Date();
            }
            if (tp_inst._defaults.maxDate == 0 || tp_inst._defaults.maxDateTime == 0)
            {
                tp_inst._defaults.maxDate = new Date();
            }

            // datepicker needs minDate/maxDate, timepicker needs minDateTime/maxDateTime..
            if (tp_inst._defaults.minDate !== undefined && tp_inst._defaults.minDate instanceof Date)
            {
                tp_inst._defaults.minDateTime = new Date(tp_inst._defaults.minDate.getTime());
            }
            if (tp_inst._defaults.minDateTime !== undefined && tp_inst._defaults.minDateTime instanceof Date)
            {
                tp_inst._defaults.minDate = new Date(tp_inst._defaults.minDateTime.getTime());
            }
            if (tp_inst._defaults.maxDate !== undefined && tp_inst._defaults.maxDate instanceof Date)
            {
                tp_inst._defaults.maxDateTime = new Date(tp_inst._defaults.maxDate.getTime());
            }
            if (tp_inst._defaults.maxDateTime !== undefined && tp_inst._defaults.maxDateTime instanceof Date)
            {
                tp_inst._defaults.maxDate = new Date(tp_inst._defaults.maxDateTime.getTime());
            }
            return tp_inst;
        },

        //########################################################################
        // add our sliders to the calendar
        //########################################################################
        _addTimePicker      : function (dp_inst)
        {
            var currDT = (this.$altInput && this._defaults.altFieldTimeOnly) ?
                this.$input.val() + ' ' + this.$altInput.val() :
                this.$input.val();

            this.timeDefined = this._parseTime(currDT);
            this._limitMinMaxDateTime(dp_inst, false);
            this._injectTimePicker();
        },

        //########################################################################
        // parse the time string from input value or _setTime
        //########################################################################
        _parseTime          : function (timeString, withDate)
        {
            var regstr = this._defaults.timeFormat.toString()
                    .replace(/h{1,2}/ig, '(\\d?\\d)')
                    .replace(/m{1,2}/ig, '(\\d?\\d)')
                    .replace(/s{1,2}/ig, '(\\d?\\d)')
                    .replace(/l{1}/ig, '(\\d?\\d?\\d)')
                    .replace(/t{1,2}/ig, this._getPatternAmpm())
                    .replace(/z{1}/ig, '(z|[-+]\\d\\d:?\\d\\d)?')
                    .replace(/\s/g, '\\s?') + this._defaults.timeSuffix + '$',
                order = this._getFormatPositions(),
                ampm = '',
                treg;

            if (!this.inst)
            {
                this.inst = $.datepicker._getInst(this.$input[0]);
            }

            if (withDate || !this._defaults.timeOnly)
            {
                // the time should come after x number of characters and a space.
                // x = at least the length of text specified by the date format
                var dp_dateFormat = $.datepicker._get(this.inst, 'dateFormat');
                // escape special regex characters in the seperator
                var specials = new RegExp("[.*+?|()\\[\\]{}\\\\]", "g");
                regstr = '^.{' + dp_dateFormat.length + ',}?' + this._defaults.separator.replace(specials, "\\$&") + regstr;
            }

            treg = timeString.match(new RegExp(regstr, 'i'));

            if (treg)
            {
                if (order.t !== -1)
                {
                    if (treg[order.t] === undefined || treg[order.t].length === 0)
                    {
                        ampm = '';
                        this.ampm = '';
                    }
                    else
                    {
                        ampm = $.inArray(treg[order.t].toUpperCase(), this.amNames) !== -1 ? 'AM' : 'PM';
                        this.ampm = this._defaults[ampm == 'AM' ? 'amNames' : 'pmNames'][0];
                    }
                }

                if (order.h !== -1)
                {
                    if (ampm == 'AM' && treg[order.h] == '12')
                    {
                        this.hour = 0;
                    } // 12am = 0 hour
                    else if (ampm == 'PM' && treg[order.h] != '12')
                    {
                        this.hour = (parseFloat(treg[order.h]) + 12).toFixed(0);
                    } // 12pm = 12 hour, any other pm = hour + 12
                    else
                    {
                        this.hour = Number(treg[order.h]);
                    }
                }

                if (order.m !== -1)
                {
                    this.minute = Number(treg[order.m]);
                }
                if (order.s !== -1)
                {
                    this.second = Number(treg[order.s]);
                }
                if (order.l !== -1)
                {
                    this.millisec = Number(treg[order.l]);
                }
                if (order.z !== -1 && treg[order.z] !== undefined)
                {
                    var tz = treg[order.z].toUpperCase();
                    switch (tz.length)
                    {
                        case 1:    // Z
                            tz = this._defaults.timezoneIso8609 ? 'Z' : '+0000';
                            break;
                        case 5:    // +hhmm
                            if (this._defaults.timezoneIso8609)
                            {
                                tz = tz.substring(1) == '0000'
                                    ? 'Z'
                                    : tz.substring(0, 3) + ':' + tz.substring(3);
                            }
                            break;
                        case 6:    // +hh:mm
                            if (!this._defaults.timezoneIso8609)
                            {
                                tz = tz == 'Z' || tz.substring(1) == '00:00'
                                    ? '+0000'
                                    : tz.replace(/:/, '');
                            }
                            else if (tz.substring(1) == '00:00')
                            {
                                tz = 'Z';
                            }
                            break;
                    }
                    this.timezone = tz;
                }

                return true;

            }
            return false;
        },

        //########################################################################
        // pattern for standard and localized AM/PM markers
        //########################################################################
        _getPatternAmpm     : function ()
        {
            var markers = [],
                o = this._defaults;
            if (o.amNames)
            {
                $.merge(markers, o.amNames);
            }
            if (o.pmNames)
            {
                $.merge(markers, o.pmNames);
            }
            markers = $.map(markers, function (val)
            {
                return val.replace(/[.*+?|()\[\]{}\\]/g, '\\$&');
            });
            return '(' + markers.join('|') + ')?';
        },

        //########################################################################
        // figure out position of time elements.. cause js cant do named captures
        //########################################################################
        _getFormatPositions : function ()
        {
            var finds = this._defaults.timeFormat.toLowerCase().match(/(h{1,2}|m{1,2}|s{1,2}|l{1}|t{1,2}|z)/g),
                orders = { h: -1, m: -1, s: -1, l: -1, t: -1, z: -1 };

            if (finds)
            {
                for (var i = 0; i < finds.length; i++)
                {
                    if (orders[finds[i].toString().charAt(0)] == -1)
                    {
                        orders[finds[i].toString().charAt(0)] = i + 1;
                    }
                }
            }

            return orders;
        },

        //########################################################################
        // generate and inject html for timepicker into ui datepicker
        //########################################################################
        _injectTimePicker   : function ()
        {
            var $dp = this.inst.dpDiv,
                o = this._defaults,
                tp_inst = this,
            // Added by Peter Medeiros:
            // - Figure out what the hour/minute/second max should be based on the step values.
            // - Example: if stepMinute is 15, then minMax is 45.
                hourMax = parseInt((o.hourMax - ((o.hourMax - o.hourMin) % o.stepHour)), 10),
                minMax = parseInt((o.minuteMax - ((o.minuteMax - o.minuteMin) % o.stepMinute)), 10),
                secMax = parseInt((o.secondMax - ((o.secondMax - o.secondMin) % o.stepSecond)), 10),
                millisecMax = parseInt((o.millisecMax - ((o.millisecMax - o.millisecMin) % o.stepMillisec)), 10),
                dp_id = this.inst.id.toString().replace(/([^A-Za-z0-9_])/g, '');

            // Prevent displaying twice
            //if ($dp.find("div#ui-timepicker-div-"+ dp_id).length === 0) {
            if ($dp.find("div#ui-timepicker-div-" + dp_id).length === 0 && o.showTimepicker)
            {
                var noDisplay = ' style="display:none;"',
                    html = '<div class="ui-timepicker-div" id="ui-timepicker-div-' + dp_id + '"><dl>' +
                        '<dt class="ui_tpicker_time_label" id="ui_tpicker_time_label_' + dp_id + '"' +
                        ((o.showTime) ? '' : noDisplay) + '>' + o.timeText + '</dt>' +
                        '<dd class="ui_tpicker_time" id="ui_tpicker_time_' + dp_id + '"' +
                        ((o.showTime) ? '' : noDisplay) + '></dd>' +
                        '<dt class="ui_tpicker_hour_label" id="ui_tpicker_hour_label_' + dp_id + '"' +
                        ((o.showHour) ? '' : noDisplay) + '>' + o.hourText + '</dt>',
                    hourGridSize = 0,
                    minuteGridSize = 0,
                    secondGridSize = 0,
                    millisecGridSize = 0,
                    size = null;

                // Hours
                html += '<dd class="ui_tpicker_hour"><div id="ui_tpicker_hour_' + dp_id + '"' +
                    ((o.showHour) ? '' : noDisplay) + '></div>';
                if (o.showHour && o.hourGrid > 0)
                {
                    html += '<div style="padding-left: 1px"><table class="ui-tpicker-grid-label"><tr>';

                    for (var h = o.hourMin; h <= hourMax; h += parseInt(o.hourGrid, 10))
                    {
                        hourGridSize++;
                        var tmph = (o.ampm && h > 12) ? h - 12 : h;
                        if (tmph < 10)
                        {
                            tmph = '0' + tmph;
                        }
                        if (o.ampm)
                        {
                            if (h == 0)
                            {
                                tmph = 12 + 'a';
                            }
                            else if (h < 12)
                            {
                                tmph += 'a';
                            }
                            else
                            {
                                tmph += 'p';
                            }
                        }
                        html += '<td>' + tmph + '</td>';
                    }

                    html += '</tr></table></div>';
                }
                html += '</dd>';

                // Minutes
                html += '<dt class="ui_tpicker_minute_label" id="ui_tpicker_minute_label_' + dp_id + '"' +
                    ((o.showMinute) ? '' : noDisplay) + '>' + o.minuteText + '</dt>' +
                    '<dd class="ui_tpicker_minute"><div id="ui_tpicker_minute_' + dp_id + '"' +
                    ((o.showMinute) ? '' : noDisplay) + '></div>';

                if (o.showMinute && o.minuteGrid > 0)
                {
                    html += '<div style="padding-left: 1px"><table class="ui-tpicker-grid-label"><tr>';

                    for (var m = o.minuteMin; m <= minMax; m += parseInt(o.minuteGrid, 10))
                    {
                        minuteGridSize++;
                        html += '<td>' + ((m < 10) ? '0' : '') + m + '</td>';
                    }

                    html += '</tr></table></div>';
                }
                html += '</dd>';

                // Seconds
                html += '<dt class="ui_tpicker_second_label" id="ui_tpicker_second_label_' + dp_id + '"' +
                    ((o.showSecond) ? '' : noDisplay) + '>' + o.secondText + '</dt>' +
                    '<dd class="ui_tpicker_second"><div id="ui_tpicker_second_' + dp_id + '"' +
                    ((o.showSecond) ? '' : noDisplay) + '></div>';

                if (o.showSecond && o.secondGrid > 0)
                {
                    html += '<div style="padding-left: 1px"><table><tr>';

                    for (var s = o.secondMin; s <= secMax; s += parseInt(o.secondGrid, 10))
                    {
                        secondGridSize++;
                        html += '<td>' + ((s < 10) ? '0' : '') + s + '</td>';
                    }

                    html += '</tr></table></div>';
                }
                html += '</dd>';

                // Milliseconds
                html += '<dt class="ui_tpicker_millisec_label" id="ui_tpicker_millisec_label_' + dp_id + '"' +
                    ((o.showMillisec) ? '' : noDisplay) + '>' + o.millisecText + '</dt>' +
                    '<dd class="ui_tpicker_millisec"><div id="ui_tpicker_millisec_' + dp_id + '"' +
                    ((o.showMillisec) ? '' : noDisplay) + '></div>';

                if (o.showMillisec && o.millisecGrid > 0)
                {
                    html += '<div style="padding-left: 1px"><table><tr>';

                    for (var l = o.millisecMin; l <= millisecMax; l += parseInt(o.millisecGrid, 10))
                    {
                        millisecGridSize++;
                        html += '<td>' + ((l < 10) ? '0' : '') + l + '</td>';
                    }

                    html += '</tr></table></div>';
                }
                html += '</dd>';

                // Timezone
                html += '<dt class="ui_tpicker_timezone_label" id="ui_tpicker_timezone_label_' + dp_id + '"' +
                    ((o.showTimezone) ? '' : noDisplay) + '>' + o.timezoneText + '</dt>';
                html += '<dd class="ui_tpicker_timezone" id="ui_tpicker_timezone_' + dp_id + '"' +
                    ((o.showTimezone) ? '' : noDisplay) + '></dd>';

                html += '</dl></div>';
                $tp = $(html);

                // if we only want time picker...
                if (o.timeOnly === true)
                {
                    $tp.prepend(
                        '<div class="ui-widget-header ui-helper-clearfix ui-corner-all">' +
                            '<div class="ui-datepicker-title">' + o.timeOnlyTitle + '</div>' +
                            '</div>');
                    $dp.find('.ui-datepicker-header, .ui-datepicker-calendar').hide();
                }

                this.hour_slider = $tp.find('#ui_tpicker_hour_' + dp_id).slider({
                    orientation: "horizontal",
                    value      : this.hour,
                    min        : o.hourMin,
                    max        : hourMax,
                    step       : o.stepHour,
                    slide      : function (event, ui)
                    {
                        tp_inst.hour_slider.slider("option", "value", ui.value);
                        tp_inst._onTimeChange();
                    }
                });


                // Updated by Peter Medeiros:
                // - Pass in Event and UI instance into slide function
                this.minute_slider = $tp.find('#ui_tpicker_minute_' + dp_id).slider({
                    orientation: "horizontal",
                    value      : this.minute,
                    min        : o.minuteMin,
                    max        : minMax,
                    step       : o.stepMinute,
                    slide      : function (event, ui)
                    {
                        tp_inst.minute_slider.slider("option", "value", ui.value);
                        tp_inst._onTimeChange();
                    }
                });

                this.second_slider = $tp.find('#ui_tpicker_second_' + dp_id).slider({
                    orientation: "horizontal",
                    value      : this.second,
                    min        : o.secondMin,
                    max        : secMax,
                    step       : o.stepSecond,
                    slide      : function (event, ui)
                    {
                        tp_inst.second_slider.slider("option", "value", ui.value);
                        tp_inst._onTimeChange();
                    }
                });

                this.millisec_slider = $tp.find('#ui_tpicker_millisec_' + dp_id).slider({
                    orientation: "horizontal",
                    value      : this.millisec,
                    min        : o.millisecMin,
                    max        : millisecMax,
                    step       : o.stepMillisec,
                    slide      : function (event, ui)
                    {
                        tp_inst.millisec_slider.slider("option", "value", ui.value);
                        tp_inst._onTimeChange();
                    }
                });

                this.timezone_select = $tp.find('#ui_tpicker_timezone_' + dp_id).append('<select></select>').find("select");
                $.fn.append.apply(this.timezone_select,
                    $.map(o.timezoneList, function (val, idx)
                    {
                        return $("<option />")
                            .val(typeof val == "object" ? val.value : val)
                            .text(typeof val == "object" ? val.label : val);
                    })
                );
                this.timezone_select.val((typeof this.timezone != "undefined" && this.timezone != null && this.timezone != "") ? this.timezone : o.timezone);
                this.timezone_select.change(function ()
                {
                    tp_inst._onTimeChange();
                });

                // Add grid functionality
                if (o.showHour && o.hourGrid > 0)
                {
                    size = 100 * hourGridSize * o.hourGrid / (hourMax - o.hourMin);

                    $tp.find(".ui_tpicker_hour table").css({
                        width         : size + "%",
                        marginLeft    : (size / (-2 * hourGridSize)) + "%",
                        borderCollapse: 'collapse'
                    }).find("td").each(function (index)
                        {
                            $(this).click(function ()
                            {
                                var h = $(this).html();
                                if (o.ampm)
                                {
                                    var ap = h.substring(2).toLowerCase(),
                                        aph = parseInt(h.substring(0, 2), 10);
                                    if (ap == 'a')
                                    {
                                        if (aph == 12)
                                        {
                                            h = 0;
                                        }
                                        else
                                        {
                                            h = aph;
                                        }
                                    }
                                    else if (aph == 12)
                                    {
                                        h = 12;
                                    }
                                    else
                                    {
                                        h = aph + 12;
                                    }
                                }
                                tp_inst.hour_slider.slider("option", "value", h);
                                tp_inst._onTimeChange();
                                tp_inst._onSelectHandler();
                            }).css({
                                    cursor   : 'pointer',
                                    width    : (100 / hourGridSize) + '%',
                                    textAlign: 'center',
                                    overflow : 'hidden'
                                });
                        });
                }

                if (o.showMinute && o.minuteGrid > 0)
                {
                    size = 100 * minuteGridSize * o.minuteGrid / (minMax - o.minuteMin);
                    $tp.find(".ui_tpicker_minute table").css({
                        width         : size + "%",
                        marginLeft    : (size / (-2 * minuteGridSize)) + "%",
                        borderCollapse: 'collapse'
                    }).find("td").each(function (index)
                        {
                            $(this).click(function ()
                            {
                                tp_inst.minute_slider.slider("option", "value", $(this).html());
                                tp_inst._onTimeChange();
                                tp_inst._onSelectHandler();
                            }).css({
                                    cursor   : 'pointer',
                                    width    : (100 / minuteGridSize) + '%',
                                    textAlign: 'center',
                                    overflow : 'hidden'
                                });
                        });
                }

                if (o.showSecond && o.secondGrid > 0)
                {
                    $tp.find(".ui_tpicker_second table").css({
                        width         : size + "%",
                        marginLeft    : (size / (-2 * secondGridSize)) + "%",
                        borderCollapse: 'collapse'
                    }).find("td").each(function (index)
                        {
                            $(this).click(function ()
                            {
                                tp_inst.second_slider.slider("option", "value", $(this).html());
                                tp_inst._onTimeChange();
                                tp_inst._onSelectHandler();
                            }).css({
                                    cursor   : 'pointer',
                                    width    : (100 / secondGridSize) + '%',
                                    textAlign: 'center',
                                    overflow : 'hidden'
                                });
                        });
                }

                if (o.showMillisec && o.millisecGrid > 0)
                {
                    $tp.find(".ui_tpicker_millisec table").css({
                        width         : size + "%",
                        marginLeft    : (size / (-2 * millisecGridSize)) + "%",
                        borderCollapse: 'collapse'
                    }).find("td").each(function (index)
                        {
                            $(this).click(function ()
                            {
                                tp_inst.millisec_slider.slider("option", "value", $(this).html());
                                tp_inst._onTimeChange();
                                tp_inst._onSelectHandler();
                            }).css({
                                    cursor   : 'pointer',
                                    width    : (100 / millisecGridSize) + '%',
                                    textAlign: 'center',
                                    overflow : 'hidden'
                                });
                        });
                }

                var $buttonPanel = $dp.find('.ui-datepicker-buttonpane');
                if ($buttonPanel.length)
                {
                    $buttonPanel.before($tp);
                }
                else
                {
                    $dp.append($tp);
                }

                this.$timeObj = $tp.find('#ui_tpicker_time_' + dp_id);

                if (this.inst !== null)
                {
                    var timeDefined = this.timeDefined;
                    this._onTimeChange();
                    this.timeDefined = timeDefined;
                }

                //Emulate datepicker onSelect behavior. Call on slidestop.
                var onSelectDelegate = function ()
                {
                    tp_inst._onSelectHandler();
                };
                this.hour_slider.bind('slidestop', onSelectDelegate);
                this.minute_slider.bind('slidestop', onSelectDelegate);
                this.second_slider.bind('slidestop', onSelectDelegate);
                this.millisec_slider.bind('slidestop', onSelectDelegate);

                // slideAccess integration: http://trentrichardson.com/2011/11/11/jquery-ui-sliders-and-touch-accessibility/
                if (this._defaults.addSliderAccess)
                {
                    var sliderAccessArgs = this._defaults.sliderAccessArgs;
                    setTimeout(function ()
                    { // fix for inline mode
                        if ($tp.find('.ui-slider-access').length == 0)
                        {
                            $tp.find('.ui-slider:visible').sliderAccess(sliderAccessArgs);

                            // fix any grids since sliders are shorter
                            var sliderAccessWidth = $tp.find('.ui-slider-access:eq(0)').outerWidth(true);
                            if (sliderAccessWidth)
                            {
                                $tp.find('table:visible').each(function ()
                                {
                                    var $g = $(this),
                                        oldWidth = $g.outerWidth(),
                                        oldMarginLeft = $g.css('marginLeft').toString().replace('%', ''),
                                        newWidth = oldWidth - sliderAccessWidth,
                                        newMarginLeft = ((oldMarginLeft * newWidth) / oldWidth) + '%';

                                    $g.css({ width: newWidth, marginLeft: newMarginLeft });
                                });
                            }
                        }
                    }, 0);
                }
                // end slideAccess integration

            }
        },

        //########################################################################
        // This function tries to limit the ability to go outside the
        // min/max date range
        //########################################################################
        _limitMinMaxDateTime: function (dp_inst, adjustSliders)
        {
            var o = this._defaults,
                dp_date = new Date(dp_inst.selectedYear, dp_inst.selectedMonth, dp_inst.selectedDay);

            if (!this._defaults.showTimepicker)
            {
                return;
            } // No time so nothing to check here

            if ($.datepicker._get(dp_inst, 'minDateTime') !== null && $.datepicker._get(dp_inst, 'minDateTime') !== undefined && dp_date)
            {
                var minDateTime = $.datepicker._get(dp_inst, 'minDateTime'),
                    minDateTimeDate = new Date(minDateTime.getFullYear(), minDateTime.getMonth(), minDateTime.getDate(), 0, 0, 0, 0);

                if (this.hourMinOriginal === null || this.minuteMinOriginal === null || this.secondMinOriginal === null || this.millisecMinOriginal === null)
                {
                    this.hourMinOriginal = o.hourMin;
                    this.minuteMinOriginal = o.minuteMin;
                    this.secondMinOriginal = o.secondMin;
                    this.millisecMinOriginal = o.millisecMin;
                }

                if (dp_inst.settings.timeOnly || minDateTimeDate.getTime() == dp_date.getTime())
                {
                    this._defaults.hourMin = minDateTime.getHours();
                    if (this.hour <= this._defaults.hourMin)
                    {
                        this.hour = this._defaults.hourMin;
                        this._defaults.minuteMin = minDateTime.getMinutes();
                        if (this.minute <= this._defaults.minuteMin)
                        {
                            this.minute = this._defaults.minuteMin;
                            this._defaults.secondMin = minDateTime.getSeconds();
                        }
                        else if (this.second <= this._defaults.secondMin)
                        {
                            this.second = this._defaults.secondMin;
                            this._defaults.millisecMin = minDateTime.getMilliseconds();
                        }
                        else
                        {
                            if (this.millisec < this._defaults.millisecMin)
                            {
                                this.millisec = this._defaults.millisecMin;
                            }
                            this._defaults.millisecMin = this.millisecMinOriginal;
                        }
                    }
                    else
                    {
                        this._defaults.minuteMin = this.minuteMinOriginal;
                        this._defaults.secondMin = this.secondMinOriginal;
                        this._defaults.millisecMin = this.millisecMinOriginal;
                    }
                }
                else
                {
                    this._defaults.hourMin = this.hourMinOriginal;
                    this._defaults.minuteMin = this.minuteMinOriginal;
                    this._defaults.secondMin = this.secondMinOriginal;
                    this._defaults.millisecMin = this.millisecMinOriginal;
                }
            }

            if ($.datepicker._get(dp_inst, 'maxDateTime') !== null && $.datepicker._get(dp_inst, 'maxDateTime') !== undefined && dp_date)
            {
                var maxDateTime = $.datepicker._get(dp_inst, 'maxDateTime'),
                    maxDateTimeDate = new Date(maxDateTime.getFullYear(), maxDateTime.getMonth(), maxDateTime.getDate(), 0, 0, 0, 0);

                if (this.hourMaxOriginal === null || this.minuteMaxOriginal === null || this.secondMaxOriginal === null)
                {
                    this.hourMaxOriginal = o.hourMax;
                    this.minuteMaxOriginal = o.minuteMax;
                    this.secondMaxOriginal = o.secondMax;
                    this.millisecMaxOriginal = o.millisecMax;
                }

                if (dp_inst.settings.timeOnly || maxDateTimeDate.getTime() == dp_date.getTime())
                {
                    this._defaults.hourMax = maxDateTime.getHours();
                    if (this.hour >= this._defaults.hourMax)
                    {
                        this.hour = this._defaults.hourMax;
                        this._defaults.minuteMax = maxDateTime.getMinutes();
                        if (this.minute >= this._defaults.minuteMax)
                        {
                            this.minute = this._defaults.minuteMax;
                            this._defaults.secondMax = maxDateTime.getSeconds();
                        }
                        else if (this.second >= this._defaults.secondMax)
                        {
                            this.second = this._defaults.secondMax;
                            this._defaults.millisecMax = maxDateTime.getMilliseconds();
                        }
                        else
                        {
                            if (this.millisec > this._defaults.millisecMax)
                            {
                                this.millisec = this._defaults.millisecMax;
                            }
                            this._defaults.millisecMax = this.millisecMaxOriginal;
                        }
                    }
                    else
                    {
                        this._defaults.minuteMax = this.minuteMaxOriginal;
                        this._defaults.secondMax = this.secondMaxOriginal;
                        this._defaults.millisecMax = this.millisecMaxOriginal;
                    }
                }
                else
                {
                    this._defaults.hourMax = this.hourMaxOriginal;
                    this._defaults.minuteMax = this.minuteMaxOriginal;
                    this._defaults.secondMax = this.secondMaxOriginal;
                    this._defaults.millisecMax = this.millisecMaxOriginal;
                }
            }

            if (adjustSliders !== undefined && adjustSliders === true)
            {
                var hourMax = parseInt((this._defaults.hourMax - ((this._defaults.hourMax - this._defaults.hourMin) % this._defaults.stepHour)), 10),
                    minMax = parseInt((this._defaults.minuteMax - ((this._defaults.minuteMax - this._defaults.minuteMin) % this._defaults.stepMinute)), 10),
                    secMax = parseInt((this._defaults.secondMax - ((this._defaults.secondMax - this._defaults.secondMin) % this._defaults.stepSecond)), 10),
                    millisecMax = parseInt((this._defaults.millisecMax - ((this._defaults.millisecMax - this._defaults.millisecMin) % this._defaults.stepMillisec)), 10);

                if (this.hour_slider)
                {
                    this.hour_slider.slider("option", { min: this._defaults.hourMin, max: hourMax }).slider('value', this.hour);
                }
                if (this.minute_slider)
                {
                    this.minute_slider.slider("option", { min: this._defaults.minuteMin, max: minMax }).slider('value', this.minute);
                }
                if (this.second_slider)
                {
                    this.second_slider.slider("option", { min: this._defaults.secondMin, max: secMax }).slider('value', this.second);
                }
                if (this.millisec_slider)
                {
                    this.millisec_slider.slider("option", { min: this._defaults.millisecMin, max: millisecMax }).slider('value', this.millisec);
                }
            }

        },


        //########################################################################
        // when a slider moves, set the internal time...
        // on time change is also called when the time is updated in the text field
        //########################################################################
        _onTimeChange       : function ()
        {
            var hour = (this.hour_slider) ? this.hour_slider.slider('value') : false,
                minute = (this.minute_slider) ? this.minute_slider.slider('value') : false,
                second = (this.second_slider) ? this.second_slider.slider('value') : false,
                millisec = (this.millisec_slider) ? this.millisec_slider.slider('value') : false,
                timezone = (this.timezone_select) ? this.timezone_select.val() : false,
                o = this._defaults;

            if (typeof(hour) == 'object')
            {
                hour = false;
            }
            if (typeof(minute) == 'object')
            {
                minute = false;
            }
            if (typeof(second) == 'object')
            {
                second = false;
            }
            if (typeof(millisec) == 'object')
            {
                millisec = false;
            }
            if (typeof(timezone) == 'object')
            {
                timezone = false;
            }

            if (hour !== false)
            {
                hour = parseInt(hour, 10);
            }
            if (minute !== false)
            {
                minute = parseInt(minute, 10);
            }
            if (second !== false)
            {
                second = parseInt(second, 10);
            }
            if (millisec !== false)
            {
                millisec = parseInt(millisec, 10);
            }

            var ampm = o[hour < 12 ? 'amNames' : 'pmNames'][0];

            // If the update was done in the input field, the input field should not be updated.
            // If the update was done using the sliders, update the input field.
            var hasChanged = (hour != this.hour || minute != this.minute
                || second != this.second || millisec != this.millisec
                || (this.ampm.length > 0
                && (hour < 12) != ($.inArray(this.ampm.toUpperCase(), this.amNames) !== -1))
                || timezone != this.timezone);

            if (hasChanged)
            {

                if (hour !== false)
                {
                    this.hour = hour;
                }
                if (minute !== false)
                {
                    this.minute = minute;
                }
                if (second !== false)
                {
                    this.second = second;
                }
                if (millisec !== false)
                {
                    this.millisec = millisec;
                }
                if (timezone !== false)
                {
                    this.timezone = timezone;
                }

                if (!this.inst)
                {
                    this.inst = $.datepicker._getInst(this.$input[0]);
                }

                this._limitMinMaxDateTime(this.inst, true);
            }
            if (o.ampm)
            {
                this.ampm = ampm;
            }

            //this._formatTime();
            this.formattedTime = $.datepicker.formatTime(this._defaults.timeFormat, this, this._defaults);
            if (this.$timeObj)
            {
                this.$timeObj.text(this.formattedTime + o.timeSuffix);
            }
            this.timeDefined = true;
            if (hasChanged)
            {
                this._updateDateTime();
            }
        },

        //########################################################################
        // call custom onSelect.
        // bind to sliders slidestop, and grid click.
        //########################################################################
        _onSelectHandler    : function ()
        {
            var onSelect = this._defaults.onSelect;
            var inputEl = this.$input ? this.$input[0] : null;
            if (onSelect && inputEl)
            {
                onSelect.apply(inputEl, [this.formattedDateTime, this]);
            }
        },

        //########################################################################
        // left for any backwards compatibility
        //########################################################################
        _formatTime         : function (time, format)
        {
            time = time || { hour: this.hour, minute: this.minute, second: this.second, millisec: this.millisec, ampm: this.ampm, timezone: this.timezone };
            var tmptime = (format || this._defaults.timeFormat).toString();

            tmptime = $.datepicker.formatTime(tmptime, time, this._defaults);

            if (arguments.length)
            {
                return tmptime;
            }
            else
            {
                this.formattedTime = tmptime;
            }
        },

        //########################################################################
        // update our input with the new date time..
        //########################################################################
        _updateDateTime     : function (dp_inst)
        {
            dp_inst = this.inst || dp_inst;
            var dt = $.datepicker._daylightSavingAdjust(new Date(dp_inst.selectedYear, dp_inst.selectedMonth, dp_inst.selectedDay)),
                dateFmt = $.datepicker._get(dp_inst, 'dateFormat'),
                formatCfg = $.datepicker._getFormatConfig(dp_inst),
                timeAvailable = dt !== null && this.timeDefined;
            this.formattedDate = $.datepicker.formatDate(dateFmt, (dt === null ? new Date() : dt), formatCfg);
            var formattedDateTime = this.formattedDate;
            if (dp_inst.lastVal !== undefined && (dp_inst.lastVal.length > 0 && this.$input.val().length === 0))
            {
                return;
            }

            if (this._defaults.timeOnly === true)
            {
                formattedDateTime = this.formattedTime;
            }
            else if (this._defaults.timeOnly !== true && (this._defaults.alwaysSetTime || timeAvailable))
            {
                formattedDateTime += this._defaults.separator + this.formattedTime + this._defaults.timeSuffix;
            }

            this.formattedDateTime = formattedDateTime;

            if (!this._defaults.showTimepicker)
            {
                this.$input.val(this.formattedDate);
            }
            else if (this.$altInput && this._defaults.altFieldTimeOnly === true)
            {
                this.$altInput.val(this.formattedTime);
                this.$input.val(this.formattedDate);
            }
            else if (this.$altInput)
            {
                this.$altInput.val(formattedDateTime);
                this.$input.val(formattedDateTime);
            }
            else
            {
                this.$input.val(formattedDateTime);
            }

            this.$input.trigger("change");
        }

    });

    $.fn.extend({
        //########################################################################
        // shorthand just to use timepicker..
        //########################################################################
        timepicker    : function (o)
        {
            o = o || {};
            var tmp_args = arguments;

            if (typeof o == 'object')
            {
                tmp_args[0] = $.extend(o, { timeOnly: true });
            }

            return $(this).each(function ()
            {
                $.fn.datetimepicker.apply($(this), tmp_args);
            });
        },

        //########################################################################
        // extend timepicker to datepicker
        //########################################################################
        datetimepicker: function (o)
        {
            o = o || {};
            tmp_args = arguments;

            if (typeof(o) == 'string')
            {
                if (o == 'getDate')
                {
                    return $.fn.datepicker.apply($(this[0]), tmp_args);
                }
                else
                {
                    return this.each(function ()
                    {
                        var $t = $(this);
                        $t.datepicker.apply($t, tmp_args);
                    });
                }
            }
            else
            {
                return this.each(function ()
                {
                    var $t = $(this);
                    $t.datepicker($.timepicker._newInst($t, o)._defaults);
                });
            }
        }
    });

//########################################################################
// format the time all pretty...
// format = string format of the time
// time = a {}, not a Date() for timezones
// options = essentially the regional[].. amNames, pmNames, ampm
//########################################################################
    $.datepicker.formatTime = function (format, time, options)
    {
        options = options || {};
        options = $.extend($.timepicker._defaults, options);
        time = $.extend({hour: 0, minute: 0, second: 0, millisec: 0, timezone: '+0000'}, time);

        var tmptime = format;
        var ampmName = options['amNames'][0];

        var hour = parseInt(time.hour, 10);
        if (options.ampm)
        {
            if (hour > 11)
            {
                ampmName = options['pmNames'][0];
                if (hour > 12)
                {
                    hour = hour % 12;
                }
            }
            if (hour === 0)
            {
                hour = 12;
            }
        }
        tmptime = tmptime.replace(/(?:hh?|mm?|ss?|[tT]{1,2}|[lz])/g, function (match)
        {
            switch (match.toLowerCase())
            {
                case 'hh':
                    return ('0' + hour).slice(-2);
                case 'h':
                    return hour;
                case 'mm':
                    return ('0' + time.minute).slice(-2);
                case 'm':
                    return time.minute;
                case 'ss':
                    return ('0' + time.second).slice(-2);
                case 's':
                    return time.second;
                case 'l':
                    return ('00' + time.millisec).slice(-3);
                case 'z':
                    return time.timezone;
                case 't':
                case 'tt':
                    if (options.ampm)
                    {
                        if (match.length == 1)
                        {
                            ampmName = ampmName.charAt(0);
                        }
                        return match.charAt(0) == 'T' ? ampmName.toUpperCase() : ampmName.toLowerCase();
                    }
                    return '';
            }
        });

        tmptime = $.trim(tmptime);
        return tmptime;
    };

//########################################################################
// the bad hack :/ override datepicker so it doesnt close on select
// inspired: http://stackoverflow.com/questions/1252512/jquery-datepicker-prevent-closing-picker-when-clicking-a-date/1762378#1762378
//########################################################################
    $.datepicker._base_selectDate = $.datepicker._selectDate;
    $.datepicker._selectDate = function (id, dateStr)
    {
        var inst = this._getInst($(id)[0]),
            tp_inst = this._get(inst, 'timepicker');

        if (tp_inst)
        {
            tp_inst._limitMinMaxDateTime(inst, true);
            inst.inline = inst.stay_open = true;
            //This way the onSelect handler called from calendarpicker get the full dateTime
            this._base_selectDate(id, dateStr);
            inst.inline = inst.stay_open = false;
            this._notifyChange(inst);
            this._updateDatepicker(inst);
        }
        else
        {
            this._base_selectDate(id, dateStr);
        }
    };

//#############################################################################################
// second bad hack :/ override datepicker so it triggers an event when changing the input field
// and does not redraw the datepicker on every selectDate event
//#############################################################################################
    $.datepicker._base_updateDatepicker = $.datepicker._updateDatepicker;
    $.datepicker._updateDatepicker = function (inst)
    {

        // don't popup the datepicker if there is another instance already opened
        var input = inst.input[0];
        if ($.datepicker._curInst &&
            $.datepicker._curInst != inst &&
            $.datepicker._datepickerShowing &&
            $.datepicker._lastInput != input)
        {
            return;
        }

        if (typeof(inst.stay_open) !== 'boolean' || inst.stay_open === false)
        {

            this._base_updateDatepicker(inst);

            // Reload the time control when changing something in the input text field.
            var tp_inst = this._get(inst, 'timepicker');
            if (tp_inst)
            {
                tp_inst._addTimePicker(inst);
            }
        }
    };

//#######################################################################################
// third bad hack :/ override datepicker so it allows spaces and colon in the input field
//#######################################################################################
    $.datepicker._base_doKeyPress = $.datepicker._doKeyPress;
    $.datepicker._doKeyPress = function (event)
    {
        var inst = $.datepicker._getInst(event.target),
            tp_inst = $.datepicker._get(inst, 'timepicker');

        if (tp_inst)
        {
            if ($.datepicker._get(inst, 'constrainInput'))
            {
                var ampm = tp_inst._defaults.ampm,
                    dateChars = $.datepicker._possibleChars($.datepicker._get(inst, 'dateFormat')),
                    datetimeChars = tp_inst._defaults.timeFormat.toString()
                        .replace(/[hms]/g, '')
                        .replace(/TT/g, ampm ? 'APM' : '')
                        .replace(/Tt/g, ampm ? 'AaPpMm' : '')
                        .replace(/tT/g, ampm ? 'AaPpMm' : '')
                        .replace(/T/g, ampm ? 'AP' : '')
                        .replace(/tt/g, ampm ? 'apm' : '')
                        .replace(/t/g, ampm ? 'ap' : '') +
                        " " +
                        tp_inst._defaults.separator +
                        tp_inst._defaults.timeSuffix +
                        (tp_inst._defaults.showTimezone ? tp_inst._defaults.timezoneList.join('') : '') +
                        (tp_inst._defaults.amNames.join('')) +
                        (tp_inst._defaults.pmNames.join('')) +
                        dateChars,
                    chr = String.fromCharCode(event.charCode === undefined ? event.keyCode : event.charCode);
                return event.ctrlKey || (chr < ' ' || !dateChars || datetimeChars.indexOf(chr) > -1);
            }
        }

        return $.datepicker._base_doKeyPress(event);
    };

//#######################################################################################
// Override key up event to sync manual input changes.
//#######################################################################################
    $.datepicker._base_doKeyUp = $.datepicker._doKeyUp;
    $.datepicker._doKeyUp = function (event)
    {
        var inst = $.datepicker._getInst(event.target),
            tp_inst = $.datepicker._get(inst, 'timepicker');

        if (tp_inst)
        {
            if (tp_inst._defaults.timeOnly && (inst.input.val() != inst.lastVal))
            {
                try
                {
                    $.datepicker._updateDatepicker(inst);
                }
                catch (err)
                {
                    $.datepicker.log(err);
                }
            }
        }

        return $.datepicker._base_doKeyUp(event);
    };

//#######################################################################################
// override "Today" button to also grab the time.
//#######################################################################################
    $.datepicker._base_gotoToday = $.datepicker._gotoToday;
    $.datepicker._gotoToday = function (id)
    {
        var inst = this._getInst($(id)[0]),
            $dp = inst.dpDiv;
        this._base_gotoToday(id);
        var now = new Date();
        var tp_inst = this._get(inst, 'timepicker');
        if (tp_inst && tp_inst._defaults.showTimezone && tp_inst.timezone_select)
        {
            var tzoffset = now.getTimezoneOffset(); // If +0100, returns -60
            var tzsign = tzoffset > 0 ? '-' : '+';
            tzoffset = Math.abs(tzoffset);
            var tzmin = tzoffset % 60;
            tzoffset = tzsign + ('0' + (tzoffset - tzmin) / 60).slice(-2) + ('0' + tzmin).slice(-2);
            if (tp_inst._defaults.timezoneIso8609)
            {
                tzoffset = tzoffset.substring(0, 3) + ':' + tzoffset.substring(3);
            }
            tp_inst.timezone_select.val(tzoffset);
        }
        this._setTime(inst, now);
        $('.ui-datepicker-today', $dp).click();
    };

//#######################################################################################
// Disable & enable the Time in the datetimepicker
//#######################################################################################
    $.datepicker._disableTimepickerDatepicker = function (target, date, withDate)
    {
        var inst = this._getInst(target),
            tp_inst = this._get(inst, 'timepicker');
        $(target).datepicker('getDate'); // Init selected[Year|Month|Day]
        if (tp_inst)
        {
            tp_inst._defaults.showTimepicker = false;
            tp_inst._updateDateTime(inst);
        }
    };

    $.datepicker._enableTimepickerDatepicker = function (target, date, withDate)
    {
        var inst = this._getInst(target),
            tp_inst = this._get(inst, 'timepicker');
        $(target).datepicker('getDate'); // Init selected[Year|Month|Day]
        if (tp_inst)
        {
            tp_inst._defaults.showTimepicker = true;
            tp_inst._addTimePicker(inst); // Could be disabled on page load
            tp_inst._updateDateTime(inst);
        }
    };

//#######################################################################################
// Create our own set time function
//#######################################################################################
    $.datepicker._setTime = function (inst, date)
    {
        var tp_inst = this._get(inst, 'timepicker');
        if (tp_inst)
        {
            var defaults = tp_inst._defaults,
            // calling _setTime with no date sets time to defaults
                hour = date ? date.getHours() : defaults.hour,
                minute = date ? date.getMinutes() : defaults.minute,
                second = date ? date.getSeconds() : defaults.second,
                millisec = date ? date.getMilliseconds() : defaults.millisec;

            //check if within min/max times..
            if ((hour < defaults.hourMin || hour > defaults.hourMax) || (minute < defaults.minuteMin || minute > defaults.minuteMax) || (second < defaults.secondMin || second > defaults.secondMax) || (millisec < defaults.millisecMin || millisec > defaults.millisecMax))
            {
                hour = defaults.hourMin;
                minute = defaults.minuteMin;
                second = defaults.secondMin;
                millisec = defaults.millisecMin;
            }

            tp_inst.hour = hour;
            tp_inst.minute = minute;
            tp_inst.second = second;
            tp_inst.millisec = millisec;

            if (tp_inst.hour_slider)
            {
                tp_inst.hour_slider.slider('value', hour);
            }
            if (tp_inst.minute_slider)
            {
                tp_inst.minute_slider.slider('value', minute);
            }
            if (tp_inst.second_slider)
            {
                tp_inst.second_slider.slider('value', second);
            }
            if (tp_inst.millisec_slider)
            {
                tp_inst.millisec_slider.slider('value', millisec);
            }

            tp_inst._onTimeChange();
            tp_inst._updateDateTime(inst);
        }
    };

//#######################################################################################
// Create new public method to set only time, callable as $().datepicker('setTime', date)
//#######################################################################################
    $.datepicker._setTimeDatepicker = function (target, date, withDate)
    {
        var inst = this._getInst(target),
            tp_inst = this._get(inst, 'timepicker');

        if (tp_inst)
        {
            this._setDateFromField(inst);
            var tp_date;
            if (date)
            {
                if (typeof date == "string")
                {
                    tp_inst._parseTime(date, withDate);
                    tp_date = new Date();
                    tp_date.setHours(tp_inst.hour, tp_inst.minute, tp_inst.second, tp_inst.millisec);
                }
                else
                {
                    tp_date = new Date(date.getTime());
                }
                if (tp_date.toString() == 'Invalid Date')
                {
                    tp_date = undefined;
                }
                this._setTime(inst, tp_date);
            }
        }

    };

//#######################################################################################
// override setDate() to allow setting time too within Date object
//#######################################################################################
    $.datepicker._base_setDateDatepicker = $.datepicker._setDateDatepicker;
    $.datepicker._setDateDatepicker = function (target, date)
    {
        var inst = this._getInst(target),
            tp_date = (date instanceof Date) ? new Date(date.getTime()) : date;

        this._updateDatepicker(inst);
        this._base_setDateDatepicker.apply(this, arguments);
        this._setTimeDatepicker(target, tp_date, true);
    };

//#######################################################################################
// override getDate() to allow getting time too within Date object
//#######################################################################################
    $.datepicker._base_getDateDatepicker = $.datepicker._getDateDatepicker;
    $.datepicker._getDateDatepicker = function (target, noDefault)
    {
        var inst = this._getInst(target),
            tp_inst = this._get(inst, 'timepicker');

        if (tp_inst)
        {
            this._setDateFromField(inst, noDefault);
            var date = this._getDate(inst);
            if (date && tp_inst._parseTime($(target).val(), tp_inst.timeOnly))
            {
                date.setHours(tp_inst.hour, tp_inst.minute, tp_inst.second, tp_inst.millisec);
            }
            return date;
        }
        return this._base_getDateDatepicker(target, noDefault);
    };

//#######################################################################################
// override parseDate() because UI 1.8.14 throws an error about "Extra characters"
// An option in datapicker to ignore extra format characters would be nicer.
//#######################################################################################
    $.datepicker._base_parseDate = $.datepicker.parseDate;
    $.datepicker.parseDate = function (format, value, settings)
    {
        var date;
        try
        {
            date = this._base_parseDate(format, value, settings);
        }
        catch (err)
        {
            if (err.indexOf(":") >= 0)
            {
                // Hack!  The error message ends with a colon, a space, and
                // the "extra" characters.  We rely on that instead of
                // attempting to perfectly reproduce the parsing algorithm.
                date = this._base_parseDate(format, value.substring(0, value.length - (err.length - err.indexOf(':') - 2)), settings);
            }
            else
            {
                // The underlying error was not related to the time
                throw err;
            }
        }
        return date;
    };

//#######################################################################################
// override formatDate to set date with time to the input
//#######################################################################################
    $.datepicker._base_formatDate = $.datepicker._formatDate;
    $.datepicker._formatDate = function (inst, day, month, year)
    {
        var tp_inst = this._get(inst, 'timepicker');
        if (tp_inst)
        {
            tp_inst._updateDateTime(inst);
            return tp_inst.$input.val();
        }
        return this._base_formatDate(inst);
    };

//#######################################################################################
// override options setter to add time to maxDate(Time) and minDate(Time). MaxDate
//#######################################################################################
    $.datepicker._base_optionDatepicker = $.datepicker._optionDatepicker;
    $.datepicker._optionDatepicker = function (target, name, value)
    {
        var inst = this._getInst(target),
            tp_inst = this._get(inst, 'timepicker');
        if (tp_inst)
        {
            var min = null, max = null, onselect = null;
            if (typeof name == 'string')
            { // if min/max was set with the string
                if (name === 'minDate' || name === 'minDateTime')
                {
                    min = value;
                }
                else if (name === 'maxDate' || name === 'maxDateTime')
                {
                    max = value;
                }
                else if (name === 'onSelect')
                {
                    onselect = value;
                }
            }
            else if (typeof name == 'object')
            { //if min/max was set with the JSON
                if (name.minDate)
                {
                    min = name.minDate;
                }
                else if (name.minDateTime)
                {
                    min = name.minDateTime;
                }
                else if (name.maxDate)
                {
                    max = name.maxDate;
                }
                else if (name.maxDateTime)
                {
                    max = name.maxDateTime;
                }
            }
            if (min)
            { //if min was set
                if (min == 0)
                {
                    min = new Date();
                }
                else
                {
                    min = new Date(min);
                }

                tp_inst._defaults.minDate = min;
                tp_inst._defaults.minDateTime = min;
            }
            else if (max)
            { //if max was set
                if (max == 0)
                {
                    max = new Date();
                }
                else
                {
                    max = new Date(max);
                }
                tp_inst._defaults.maxDate = max;
                tp_inst._defaults.maxDateTime = max;
            }
            else if (onselect)
            {
                tp_inst._defaults.onSelect = onselect;
            }
        }
        if (value === undefined)
        {
            return this._base_optionDatepicker(target, name);
        }
        return this._base_optionDatepicker(target, name, value);
    };

//#######################################################################################
// jQuery extend now ignores nulls!
//#######################################################################################
    function extendRemove(target, props)
    {
        $.extend(target, props);
        for (var name in props)
        {
            if (props[name] === null || props[name] === undefined)
            {
                target[name] = props[name];
            }
        }
        return target;
    };

    $.timepicker = new Timepicker(); // singleton instance
    $.timepicker.version = "1.0.0";

})(jQuery);


//  ----------------------------------------------------------------------------
//
//  bootstrap-typeahead.js
//
//  Twitter Bootstrap Typeahead Plugin
//  v1.2.2
//  https://github.com/tcrosen/twitter-bootstrap-typeahead
//
//
//  Author
//  ----------
//  Terry Rosen
//  tcrosen@gmail.com | @rerrify | github.com/tcrosen/
//
//
//  Description
//  ----------
//  Custom implementation of Twitter's Bootstrap Typeahead Plugin
//  http://twitter.github.com/bootstrap/javascript.html#typeahead
//
//
//  Requirements
//  ----------
//  jQuery 1.7+
//  Twitter Bootstrap 2.0+
//
//  ----------------------------------------------------------------------------

!
    function ($)
    {

        "use strict";

        //------------------------------------------------------------------
        //
        //  Constructor
        //
        var Typeahead = function (element, options)
        {
            this.$element = $(element);
            this.options = $.extend(true, {}, $.fn.typeahead.defaults, options);
            this.$menu = $(this.options.menu).appendTo('body');
            this.shown = false;

            // Method overrides
            this.eventSupported = this.options.eventSupported || this.eventSupported;
            this.grepper = this.options.grepper || this.grepper;
            this.highlighter = this.options.highlighter || this.highlighter;
            this.lookup = this.options.lookup || this.lookup;
            this.matcher = this.options.matcher || this.matcher;
            this.render = this.options.render || this.render;
            this.select = this.options.select || this.select;
            this.sorter = this.options.sorter || this.sorter;
            this.source = this.options.source || this.source;

            if (!this.source.length)
            {
                var ajax = this.options.ajax;

                if (typeof ajax === 'string')
                {
                    this.ajax = $.extend({}, $.fn.typeahead.defaults.ajax, { url: ajax });
                }
                else
                {
                    this.ajax = $.extend({}, $.fn.typeahead.defaults.ajax, ajax);
                }

                if (!this.ajax.url)
                {
                    this.ajax = null;
                }
            }

            this.listen();
        }

        Typeahead.prototype = {

            constructor        : Typeahead,

            //=============================================================================================================
            //
            //  Utils
            //
            //=============================================================================================================

            //------------------------------------------------------------------
            //
            //  Check if an event is supported by the browser eg. 'keypress'
            //  * This was included to handle the "exhaustive deprecation" of jQuery.browser in jQuery 1.8
            //
            eventSupported     : function (eventName)
            {
                var isSupported = (eventName in this.$element);

                if (!isSupported)
                {
                    this.$element.setAttribute(eventName, 'return;');
                    isSupported = typeof this.$element[eventName] === 'function';
                }

                return isSupported;
            },

            //=============================================================================================================
            //
            //  AJAX
            //
            //=============================================================================================================

            //------------------------------------------------------------------
            //
            //  Handle AJAX source
            //
            ajaxer             : function ()
            {
                var that = this,
                    query = that.$element.val();

                if (query === that.query)
                {
                    return that;
                }

                // Query changed
                that.query = query;

                // Cancel last timer if set
                if (that.ajax.timerId)
                {
                    clearTimeout(that.ajax.timerId);
                    that.ajax.timerId = null;
                }

                if (!query || query.length < that.ajax.triggerLength)
                {
                    // Cancel the ajax callback if in progress
                    if (that.ajax.xhr)
                    {
                        that.ajax.xhr.abort();
                        that.ajax.xhr = null;
                        that.ajaxToggleLoadClass(false);
                    }

                    return that.shown ? that.hide() : that;
                }

                // Query is good to send, set a timer
                that.ajax.timerId = setTimeout(function ()
                {
                    $.proxy(that.ajaxExecute(query), that)
                }, that.ajax.timeout);

                return that;
            },

            //------------------------------------------------------------------
            //
            //  Execute an AJAX request
            //
            ajaxExecute        : function (query)
            {
                this.ajaxToggleLoadClass(true);

                // Cancel last call if already in progress
                if (this.ajax.xhr)
                {
                    this.ajax.xhr.abort();
                }

                var params = this.ajax.preDispatch ? this.ajax.preDispatch(query) : { query: query };
                var jAjax = (this.ajax.method === "post") ? $.post : $.get;
                this.ajax.xhr = jAjax(this.ajax.url, params, $.proxy(this.ajaxLookup, this));
                this.ajax.timerId = null;
            },

            //------------------------------------------------------------------
            //
            //  Perform a lookup in the AJAX results
            //
            ajaxLookup         : function (data)
            {
                var items;

                this.ajaxToggleLoadClass(false);

                if (!this.ajax.xhr)
                {
                    return;
                }

                if (this.ajax.preProcess)
                {
                    data = this.ajax.preProcess(data);
                }

                // Save for selection retreival
                this.ajax.data = data;

                items = this.grepper(this.ajax.data);

                if (!items || !items.length)
                {
                    return this.shown ? this.hide() : this;
                }

                this.ajax.xhr = null;

                return this.render(items.slice(0, this.options.items)).show();
            },

            //------------------------------------------------------------------
            //
            //  Toggle the loading class
            //
            ajaxToggleLoadClass: function (enable)
            {
                if (!this.ajax.loadingClass)
                {
                    return;
                }
                this.$element.toggleClass(this.ajax.loadingClass, enable);
            },

            //=============================================================================================================
            //
            //  Data manipulation
            //
            //=============================================================================================================

            //------------------------------------------------------------------
            //
            //  Search source
            //
            lookup             : function (event)
            {
                var that = this,
                    items;

                if (that.ajax)
                {
                    that.ajaxer();
                }
                else
                {
                    that.query = that.$element.val();

                    if (!that.query)
                    {
                        return that.shown ? that.hide() : that;
                    }

                    items = that.grepper(that.source);

                    if (!items || !items.length)
                    {
                        return that.shown ? that.hide() : that;
                    }

                    return that.render(items.slice(0, that.options.items)).show();
                }
            },

            //------------------------------------------------------------------
            //
            //  Filters relevent results
            //
            grepper            : function (data)
            {
                var that = this,
                    items;

                if (data && data.length && !data[0].hasOwnProperty(that.options.display))
                {
                    return null;
                }

                items = $.grep(data, function (item)
                {
                    return that.matcher(item[that.options.display], item);
                });

                return this.sorter(items);
            },

            //------------------------------------------------------------------
            //
            //  Looks for a match in the source
            //
            matcher            : function (item)
            {
                return ~item.toLowerCase().indexOf(this.query.toLowerCase());
            },

            //------------------------------------------------------------------
            //
            //  Sorts the results
            //
            sorter             : function (items)
            {
                var that = this,
                    beginswith = [],
                    caseSensitive = [],
                    caseInsensitive = [],
                    item;

                while (item = items.shift())
                {
                    if (!item[that.options.display].toLowerCase().indexOf(this.query.toLowerCase()))
                    {
                        beginswith.push(item);
                    }
                    else if (~item[that.options.display].indexOf(this.query))
                    {
                        caseSensitive.push(item);
                    }
                    else
                    {
                        caseInsensitive.push(item);
                    }
                }

                return beginswith.concat(caseSensitive, caseInsensitive);
            },

            //=============================================================================================================
            //
            //  DOM manipulation
            //
            //=============================================================================================================

            //------------------------------------------------------------------
            //
            //  Shows the results list
            //
            show               : function ()
            {
                var pos = $.extend({}, this.$element.offset(), {
                    height: this.$element[0].offsetHeight
                });

                this.$menu.css({
                    top : pos.top + pos.height,
                    left: pos.left
                });

                this.$menu.show();
                this.shown = true;

                return this;
            },

            //------------------------------------------------------------------
            //
            //  Hides the results list
            //
            hide               : function ()
            {
                this.$menu.hide();
                this.shown = false;
                return this;
            },

            //------------------------------------------------------------------
            //
            //  Highlights the match(es) within the results
            //
            highlighter        : function (item)
            {
                var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&');
                return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match)
                {
                    return '<strong>' + match + '</strong>';
                });
            },

            //------------------------------------------------------------------
            //
            //  Renders the results list
            //
            render             : function (items)
            {
                var that = this;

                items = $(items).map(function (i, item)
                {
                    i = $(that.options.item).attr('data-value', item[that.options.val]);
                    i.find('a').html(that.highlighter(item[that.options.display], item));
                    return i[0];
                });

                items.first().addClass('active');
                this.$menu.html(items);
                return this;
            },

            //------------------------------------------------------------------
            //
            //  Item is selected
            //
            select             : function ()
            {
                var $selectedItem = this.$menu.find('.active');
                this.$element.val($selectedItem.text()).change();
                this.options.itemSelected($selectedItem, $selectedItem.attr('data-value'), $selectedItem.text());
                return this.hide();
            },

            //------------------------------------------------------------------
            //
            //  Selects the next result
            //
            next               : function (event)
            {
                var active = this.$menu.find('.active').removeClass('active');
                var next = active.next();

                if (!next.length)
                {
                    next = $(this.$menu.find('li')[0]);
                }

                next.addClass('active');
            },

            //------------------------------------------------------------------
            //
            //  Selects the previous result
            //
            prev               : function (event)
            {
                var active = this.$menu.find('.active').removeClass('active');
                var prev = active.prev();

                if (!prev.length)
                {
                    prev = this.$menu.find('li').last();
                }

                prev.addClass('active');
            },

            //=============================================================================================================
            //
            //  Events
            //
            //=============================================================================================================

            //------------------------------------------------------------------
            //
            //  Listens for user events
            //
            listen             : function ()
            {
                this.$element.on('blur', $.proxy(this.blur, this))
                    .on('keyup', $.proxy(this.keyup, this));

                if (this.eventSupported('keydown'))
                {
                    this.$element.on('keydown', $.proxy(this.keypress, this));
                }
                else
                {
                    this.$element.on('keypress', $.proxy(this.keypress, this));
                }

                this.$menu.on('click', $.proxy(this.click, this))
                    .on('mouseenter', 'li', $.proxy(this.mouseenter, this));
            },

            //------------------------------------------------------------------
            //
            //  Handles a key being raised up
            //
            keyup              : function (e)
            {
                e.stopPropagation();
                e.preventDefault();

                switch (e.keyCode)
                {
                    case 40:
                    // down arrow
                    case 38:
                        // up arrow
                        break;
                    case 9:
                    // tab
                    case 13:
                        // enter
                        if (!this.shown)
                        {
                            return;
                        }
                        this.select();
                        break;
                    case 27:
                        // escape
                        this.hide();
                        break;
                    default:
                        this.lookup();
                }
            },

            //------------------------------------------------------------------
            //
            //  Handles a key being pressed
            //
            keypress           : function (e)
            {
                e.stopPropagation();
                if (!this.shown)
                {
                    return;
                }

                switch (e.keyCode)
                {
                    case 9:
                    // tab
                    case 13:
                    // enter
                    case 27:
                        // escape
                        e.preventDefault();
                        break;
                    case 38:
                        // up arrow
                        e.preventDefault();
                        this.prev();
                        break;
                    case 40:
                        // down arrow
                        e.preventDefault();
                        this.next();
                        break;
                }
            },

            //------------------------------------------------------------------
            //
            //  Handles cursor exiting the textbox
            //
            blur               : function (e)
            {
                var that = this;
                e.stopPropagation();
                e.preventDefault();
                setTimeout(function ()
                {
                    if (!that.$menu.is(':focus'))
                    {
                        that.hide();
                    }
                }, 150)
            },

            //------------------------------------------------------------------
            //
            //  Handles clicking on the results list
            //
            click              : function (e)
            {
                e.stopPropagation();
                e.preventDefault();
                this.select();
            },

            //------------------------------------------------------------------
            //
            //  Handles the mouse entering the results list
            //
            mouseenter         : function (e)
            {
                this.$menu.find('.active').removeClass('active');
                $(e.currentTarget).addClass('active');
            }
        }

        //------------------------------------------------------------------
        //
        //  Plugin definition
        //
        $.fn.typeahead = function (option)
        {
            return this.each(function ()
            {
                var $this = $(this),
                    data = $this.data('typeahead'),
                    options = typeof option === 'object' && option;

                if (!data)
                {
                    $this.data('typeahead', (data = new Typeahead(this, options)));
                }

                if (typeof option === 'string')
                {
                    data[option]();
                }
            });
        }

        //------------------------------------------------------------------
        //
        //  Defaults
        //
        $.fn.typeahead.defaults = {
            source      : [],
            items       : 8,
            menu        : '<ul class="typeahead dropdown-menu"></ul>',
            item        : '<li><a href="#"></a></li>',
            display     : 'name',
            val         : 'id',
            itemSelected: function ()
            {
            },
            ajax        : {
                url          : null,
                timeout      : 300,
                method       : 'post',
                triggerLength: 3,
                loadingClass : null,
                displayField : null,
                preDispatch  : null,
                preProcess   : null
            }
        }

        $.fn.typeahead.Constructor = Typeahead;

        //------------------------------------------------------------------
        //
        //  DOM-ready call for the Data API (no-JS implementation)
        //
        //  Note: As of Bootstrap v2.0 this feature may be disabled using $('body').off('.data-api')
        //  More info here: https://github.com/twitter/bootstrap/tree/master/js
        //
        $(function ()
        {
            $('body').on('focus.typeahead.data-api', '[data-provide="typeahead"]', function (e)
            {
                var $this = $(this);

                if ($this.data('typeahead'))
                {
                    return;
                }

                e.preventDefault();
                $this.typeahead($this.data());
            })
        });

    }(window.jQuery);

/**
 * Autocomplete combobox
 */
(function ($)
{
    $.widget("ui.combobox", {
        _create: function ()
        {
            var self = this,
                select = this.element.hide(),
                selected = select.children(":selected"),
                value = selected.val() ? selected.text() : "";
            var input = this.input = $("<input size='25'>")
                .insertAfter(select)
                .val(value)
                .autocomplete({
                    delay    : 0,
                    minLength: 0,
                    source   : function (request, response)
                    {
                        var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
                        response(select.children("option").map(function ()
                        {
                            var text = $(this).text();
                            if (this.value && ( !request.term || matcher.test(text) ))
                            {
                                return {
                                    label : text.replace(
                                        new RegExp(
                                            "(?![^&;]+;)(?!<[^<>]*)(" +
                                                $.ui.autocomplete.escapeRegex(request.term) +
                                                ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                        ), "<strong>$1</strong>"),
                                    value : text,
                                    option: this
                                };
                            }
                        }));
                    },
                    select   : function (event, ui)
                    {
                        ui.item.option.selected = true;
                        self._trigger("selected", event, {
                            item: ui.item.option
                        });
                    },
                    change   : function (event, ui)
                    {
                        if (!ui.item)
                        {
                            var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex($(this).val()) + "$", "i"),
                                valid = false;
                            select.children("option").each(function ()
                            {
                                if ($(this).text().match(matcher))
                                {
                                    this.selected = valid = true;
                                    return false;
                                }
                            });
                            if (!valid)
                            {
                                // remove invalid value, as it didn't match anything
                                $(this).val("");
                                select.val("");
                                input.data("ui-autocomplete").term = "";
                                return false;
                            }
                        }
                    }
                })
                .addClass("ui-widget ui-widget-content ui-corner-left");

            input.data("ui-autocomplete")._renderItem = function (ul, item)
            {
                return $("<li></li>")
                    .data("ui-autocomplete-item", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
            };

            this.button = $("<button type='button'>&nbsp;</button>")
                .attr("tabIndex", -1)
                .attr("title", "Show All Items")
                .insertAfter(input)
                .button({
                    icons: {
                        primary: "ui-icon-triangle-1-s"
                    },
                    text : false
                })
                .removeClass("ui-corner-all")
                .addClass("ui-corner-right ui-button-icon")
                .click(function ()
                {
                    // close if already visible
                    if (input.autocomplete("widget").is(":visible"))
                    {
                        input.autocomplete("close");
                        return;
                    }

                    // work around a bug (likely same cause as #5265)
                    $(this).blur();

                    // pass empty string as value to search for, displaying all results
                    input.autocomplete("search", "");
                    input.focus();
                });
        },

        destroy: function ()
        {
            this.input.remove();
            this.button.remove();
            this.element.show();
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);

/*
 * jQuery UIx Multiselect 2.0
 *
 * Authors:
 *  Yanick Rochon (yanick.rochon[at]gmail[dot]com)
 *
 * Licensed under the MIT (MIT-LICENSE.txt) license.
 *
 * http://mind2soft.com/labs/jquery/multiselect/
 *
 *
 * Depends:
 * jQuery UI 1.8+
 *
 */

(function(d,i,g){var k=0;var l="";var b="group-";var h="multiselectChange";var f="multiselectSearch";d.widget("uix.multiselect",{options:{availableListPosition:"right",collapsableGroups:true,defaultGroupName:"",filterSelected:false,locale:"auto",moveEffect:null,moveEffectOptions:{},moveEffectSpeed:null,optionRenderer:false,optionGroupRenderer:false,searchField:"toggle",searchFilter:null,searchHeader:"available",selectionMode:"click,d&d",showDefaultGroupHeader:false,showEmptyGroups:false,splitRatio:0.55,sortable:false,sortMethod:null},_create:function(){var x=this;
    var u,s,y,w;var t,v;this.scope="multiselect"+(k++);this.optionGroupIndex=1;this._setLocale(this.options.locale);this.element.addClass("uix-multiselect-original");this._elementWrapper=d("<div></div>").addClass("uix-multiselect ui-widget").css({width:this.element.outerWidth(),height:this.element.outerHeight()}).append(d("<div></div>").addClass("multiselect-selected-list").append(d("<div></div>").addClass("ui-widget-header").append(v=d("<button></button>",{type:"button"}).addClass("uix-control-right").attr("data-localekey","deselectAll").attr("title",this._t("deselectAll")).button({icons:{primary:"ui-icon-arrowthickstop-1-e"},text:false}).click(function(z){z.preventDefault();
        z.stopPropagation();x.optionCache.setSelectedAll(false);return false})).append(u=d("<div></div>").addClass("header-text"))).append(s=d("<div></div>").addClass("uix-list-container ui-widget-content")))["right,top".indexOf(this.options.availableListPosition)>=0?"prepend":"append"](d("<div></div>").addClass("multiselect-available-list").append(d("<div></div>").addClass("ui-widget-header").append(t=d("<button></button>",{type:"button"}).addClass("uix-control-right").attr("data-localekey","selectAll").attr("title",this._t("selectAll")).button({icons:{primary:"ui-icon-arrowthickstop-1-w"},text:false}).click(function(z){z.preventDefault();
            z.stopPropagation();x.optionCache.setSelectedAll(true);return false})).append(y=d("<div></div>").addClass("header-text"))).append(w=d("<div></div>").addClass("uix-list-container ui-widget-content"))).insertAfter(this.element);this._buttons={selectAll:t,deselectAll:v};
    this._headers={selected:u,available:y};this._lists={selected:s.attr("id",this.scope+"_selListContent"),available:w.attr("id",this.scope+"_avListContent")};this.optionCache=new a(this);this._searchDelayed=new o(this,{delay:500});this._initSearchable();this._applyListDroppable();
    this.refresh()},refresh:function(s){this._resize();n(function(){this.optionCache.cleanup();var y,w=this.element[0].childNodes;for(var x=0,u=w.length;x<u;x++){y=w[x];if(y.nodeType===1){if(y.tagName.toUpperCase()==="OPTGROUP"){var A=d(y).data("option-group")||(b+(this.optionGroupIndex++));
    var z=y.childNodes;this.optionCache.prepareGroup(d(y),A);for(var v=0,t=z.length;v<t;v++){y=z[v];if(y.nodeType===1){this.optionCache.prepareOption(d(y),A)}}}else{this.optionCache.prepareOption(d(y))}}}this.optionCache.reIndex();if(this._searchField&&this._searchField.is(":visible")){this._search(null,true)
}if(s){s()}},10,this)},search:function(s){if(typeof s!="object"){s={showInput:true,text:s}}if((s.toggleInput!=false)&&!this._searchField.is(":visible")){this._buttons.search.trigger("click")}this._search(s.text,!!s.silent)},locale:function(s){if(s===g){return this.options.locale
}else{this._setLocale(s);this._updateControls();this._updateHeaders()}},_destroy:function(){this.optionCache.reset(true);this._lists.selected.empty().remove();this._lists.available.empty().remove();this._elementWrapper.empty().remove();delete this.optionCache;
    delete this._searchDelayed;delete this._lists;delete this._elementWrapper;this.element.removeClass("uix-multiselect-original")},_initSearchable:function(){var u=("toggle"===this.options.searchField);var t=this.options.searchHeader;if(u){var s=this;this._buttons.search=d("<button></button",{type:"button"}).addClass("uix-control-right").attr("data-localekey","search").attr("title",this._t("search")).button({icons:{primary:"ui-icon-search"},text:false}).click(function(w){w.preventDefault();
    w.stopPropagation();if(s._searchField.is(":visible")){var v=d(this);s._headers[t].css("visibility","visible").fadeTo("fast",1);s._searchField.hide("slide",{direction:"right"},200,function(){v.removeClass("ui-corner-right ui-state-active").addClass("ui-corner-all")
    });s._searchDelayed.cancelLastRequest();s.optionCache.filter("")}else{s._headers[t].fadeTo("fast",0.1,function(){d(this).css("visibility","hidden")});d(this).removeClass("ui-corner-all").addClass("ui-corner-right ui-state-active");s._searchField.show("slide",{direction:"right"},200,function(){d(this).focus()
    });s._search()}return false}).insertBefore(this._headers[t])}if(this.options.searchField){if(!u){this._headers[t].hide()}this._searchField=d('<input type="text" />').addClass("uix-search ui-widget-content ui-corner-"+(u?"left":"all"))[u?"hide":"show"]().insertBefore(this._headers[t]).focus(function(){d(this).select()
}).on("keydown keypress",function(v){if(v.keyCode==13){v.preventDefault();v.stopPropagation();return false}}).keyup(d.proxy(this._searchDelayed.request,this._searchDelayed))}},_applyListDroppable:function(){if(this.options.selectionMode.indexOf("d&d")==-1){return
}var s=this.optionCache;var t=this.scope;var w=function(x){return s._elements[x.data("element-index")]};var v=function(y,x){y.droppable({accept:function(z){var A=w(z);return A&&(A.selected!=x)},activeClass:"ui-state-highlight",scope:t,drop:function(z,A){A.draggable.removeClass("ui-state-disabled");
    A.helper.remove();s.setSelected(w(A.draggable),x)}})};v(this._lists.selected,true);v(this._lists.available,false);if(this.options.sortable){var u=this;this._lists.selected.sortable({appendTo:"parent",axis:"y",containment:d(".multiselect-selected-list",this._elementWrapper),items:".multiselect-element-wrapper",handle:".group-element",revert:true,stop:d.proxy(function(x,y){var z;
    d(".multiselect-element-wrapper",u._lists.selected).each(function(){var A=u.optionCache._groups.get(d(this).data("option-group"));if(!z){u.element.append(A.groupElement)}else{A.groupElement.insertAfter(z.groupElement)}z=A})},this)})}},_search:function(t,s){if(this._searchField.is(":visible")){if(typeof t==="string"){this._searchField.val(t)
}else{t=this._searchField.val()}}this.optionCache.filter(t,s)},_setLocale:function(s){if(s=="auto"){s=navigator.userLanguage||navigator.language||navigator.browserLanguage||navigator.systemLanguage||""}if(!d.uix.multiselect.i18n[s]){s=""}this.options.locale=s
},_t:function(t,s,u){return r({locale:this.options.locale,key:t,plural:s,data:u})},_updateControls:function(){var s=this;d(".uix-control-left,.uix-control-right",this._elementWrapper).each(function(){d(this).attr("title",s._t(d(this).attr("data-localekey")))
})},_updateHeaders:function(){var s,u=this.optionCache.getSelectionInfo();this._headers.selected.text(s=this._t("itemsSelected",u.selected.total,{count:u.selected.total})).parent().attr("title",this.options.filterSelected?this._t("itemsSelected",u.selected.count,{count:u.selected.count})+", "+this._t("itemsFiltered",u.selected.filtered,{count:u.selected.filtered}):s);
    this._headers.available.text(this._t("itemsAvailable",u.available.total,{count:u.available.total})).parent().attr("title",this._t("itemsAvailable",u.available.count,{count:u.available.count})+", "+this._t("itemsFiltered",u.available.filtered,{count:u.available.filtered}))
},_resize:function(){var B=this.options.availableListPosition.toLowerCase();var w=("left,right".indexOf(B)>=0)?"Width":"Height";var v=("left,right".indexOf(B)>=0)?"Height":"Width";var F=this.element["outer"+w]()*this.options.splitRatio;var C=this.element["outer"+w]()-F;
    var s=(v==="Width")?F:this.element.outerHeight();var D=(v==="Width")?C:this.element.outerHeight();var E=("left,right".indexOf(B)>=0)?"left":"top";var u=("left,top".indexOf(B)>=0);var t=("toggle"===this.options.searchField);var x="ui-corner-tl ui-corner-tr ui-corner-bl ui-corner-br ui-corner-top";
    var A=(v==="Width")?(u?"":"ui-corner-top"):(u?"ui-corner-tr":"ui-corner-tl");var z=(v==="Width")?(u?"ui-corner-top":""):(u?"ui-corner-tl":"ui-corner-tr");this._elementWrapper.find(".multiselect-available-list")[w.toLowerCase()](C).css(E,u?0:F)[v.toLowerCase()](this.element["outer"+v]()+1);
    this._elementWrapper.find(".multiselect-selected-list")[w.toLowerCase()](F).css(E,u?C:0)[v.toLowerCase()](this.element["outer"+v]()+1);this._buttons.selectAll.button("option","icons",{primary:q(B,"ui-icon-arrowthickstop-1-",false)});this._buttons.deselectAll.button("option","icons",{primary:q(B,"ui-icon-arrowthickstop-1-",true)});
    this._headers.available.parent().removeClass(x).addClass(z);this._headers.selected.parent().removeClass(x).addClass(A);if(!t){var y=Math.max(this._headers.selected.parent().height(),this._headers.available.parent().height());this._headers.available.parent().height(y);
        this._headers.selected.parent().height(y)}if(this._searchField){this._searchField.width((w==="Width"?C:this.element.width())-(t?52:26))}this._lists.available.height(D-this._headers.available.parent().outerHeight()-2);this._lists.selected.height(s-this._headers.selected.parent().outerHeight()-2)
},_triggerUIEvent:function(t,u){var s;if(typeof t==="string"){s=t;t=d.Event(t)}else{s=t.type}this.element.trigger(t,u);return !t.isDefaultPrevented()},_setOption:function(s,t){switch(s){}if(typeof(this._superApply)=="function"){this._superApply(arguments)}else{d.Widget.prototype._setOption.apply(this,arguments)
}}});var m={standard:function(t,s){if(t>s){return 1}if(t<s){return -1}return 0},natural:function e(K,J){var F=/(^-?[0-9]+(\.?[0-9]*)[df]?e?[0-9]?$|^0x[0-9a-f]+$|[0-9]+)/gi,w=/(^[ ]*|[ ]*$)/g,L=/(^([\w ]+,?[\w ]+)?[\w ]+,?[\w ]+\d+:\d+(:\d+)?[\w ]?|^\d{1,4}[\/\-]\d{1,4}[\/\-]\d{1,4}|^\w+, \w+ \d+, \d{4})/,v=/^0x[0-9a-f]+$/i,t=/^0/,H=function(x){return e.insensitive&&(""+x).toLowerCase()||""+x
},C=H(K).replace(w,"")||"",A=H(J).replace(w,"")||"",u=C.replace(F,"\0$1\0").replace(/\0$/,"").replace(/^\0/,"").split("\0"),E=A.replace(F,"\0$1\0").replace(/\0$/,"").replace(/^\0/,"").split("\0"),z=parseInt(C.match(v))||(u.length!=1&&C.match(L)&&Date.parse(C)),G=parseInt(A.match(v))||z&&A.match(L)&&Date.parse(A)||null,I,s;
    if(G){if(z<G){return -1}else{if(z>G){return 1}}}for(var D=0,B=Math.max(u.length,E.length);D<B;D++){I=!(u[D]||"").match(t)&&parseFloat(u[D])||u[D]||0;s=!(E[D]||"").match(t)&&parseFloat(E[D])||E[D]||0;if(isNaN(I)!==isNaN(s)){return(isNaN(I))?1:-1}else{if(typeof I!==typeof s){I+="";
        s+=""}}if(I<s){return -1}if(I>s){return 1}}return 0}};var c=["n","e","s","w"];var j=["bottom","left","top","right"];var q=function(u,t,s){return t+c[(d.inArray(u.toLowerCase(),j)+(s?2:0))%4]};var n=function(v,u,s){var t=Array.prototype.slice.call(arguments,3);
    return setTimeout(function(){v.apply(s||i,t)},u)};var o=function(t,s){this._widget=t;this._options=s;this._lastSearchValue=null};o.prototype={request:function(){if(this._widget._searchField.val()==this._lastSearchValue){return}this.cancelLastRequest();this._timeout=n(function(){this._timeout=null;
    this._lastSearchValue=this._widget._searchField.val();this._widget._search()},this._options.delay,this)},cancelLastRequest:function(){if(this._timeout){clearTimeout(this._timeout)}}};var p=function(u){var v=[];var t={};var s=u;this.setComparator=function(w){s=w;
    return this};this.clear=function(){v=[];t={};return this};this.containsKey=function(w){return !!t[w]};this.get=function(w){return t[w]};this.put=function(w,x){if(!t[w]){if(s){v.splice((function(){var z=0,C=v.length;var B=-1,D=0;while(z<C){B=parseInt((z+C)/2);
    var A=t[v[B]].groupElement;var y=x.groupElement;D=s(A?A.attr("label"):l,y?y.attr("label"):l);if(D<0){z=B+1}else{if(D>0){C=B}else{return B}}}return z})(),0,w)}else{v.push(w)}}t[w]=x;return this};this.remove=function(w){delete t[w];return v.splice(v.indexOf(w),1)
};this.each=function(z){var x=Array.prototype.slice.call(arguments,1);x.splice(0,0,null,null);for(var y=0,w=v.length;y<w;y++){x[0]=v[y];x[1]=t[v[y]];z.apply(x[1],x)}return this}};var a=function(s){this._widget=s;this._listContainers={selected:d("<div></div>").appendTo(this._widget._lists.selected),available:d("<div></div>").appendTo(this._widget._lists.available)};
    this._elements=[];this._groups=new p();this._moveEffect={fn:s.options.moveEffect,options:s.options.moveEffectOptions,speed:s.options.moveEffectSpeed};this._selectionMode=this._widget.options.selectionMode.indexOf("dblclick")>-1?"dblclick":this._widget.options.selectionMode.indexOf("click")>-1?"click":false;
    this.reset()};a.Options={batchCount:200,batchDelay:50};a.prototype={_createGroupElement:function(y,G,v){var A=this;var E;var u=function(){if(!E){E=A._groups.get(G)}return E};var z=function(){return y?y.attr("label"):A._widget.options.defaultGroupName};var t=d("<span></span>").addClass("label").text(z()+" (0)").attr("title",z()+" (0)");
    var x=function(){var I=u()[v?"selected":"available"];I.listElement[(!v&&(I.count||A._widget.options.showEmptyGroups))||(I.count&&((E.optionGroup!=l)||A._widget.options.showDefaultGroupHeader))?"show":"hide"]();var H=z()+" ("+I.count+")";t.text(H).attr("title",H)
    };var C=d("<div></div>").addClass("ui-widget-header ui-priority-secondary group-element").append(d("<button></button>",{type:"button"}).addClass("uix-control-right").attr("data-localekey",(v?"de":"")+"selectAllGroup").attr("title",this._widget._t((v?"de":"")+"selectAllGroup")).button({icons:{primary:q(this._widget.options.availableListPosition,"ui-icon-arrowstop-1-",v)},text:false}).click(function(M){M.preventDefault();
        M.stopPropagation();var L=u()[v?"selected":"available"];if(E.count>0){var I=[];A._bufferedMode(true);for(var J=E.startIndex,H=E.startIndex+E.count,K;J<H;J++){K=A._elements[J];if(!K.filtered&&!K.selected!=v){A.setSelected(K,!v,true);I.push(K.optionElement[0])
        }}A._updateGroupElements(E);A._widget._updateHeaders();A._bufferedMode(false);A._widget._triggerUIEvent(h,{optionElements:I,selected:!v})}return false})).append(t);var s,B=(y)?y.attr("data-group-icon"):null;if(this._widget.options.collapsableGroups){var D=(y)?y.attr("data-collapse-icon"):null,F=(D)?"ui-icon "+D:"ui-icon ui-icon-triangle-1-s";
        var w=d("<span></span>").addClass("ui-icon collapse-handle").attr("data-localekey","collapseGroup").attr("title",this._widget._t("collapseGroup")).addClass(F).mousedown(function(H){H.stopPropagation()}).click(function(H){H.preventDefault();H.stopPropagation();
            s(y);return false}).prependTo(C.addClass("group-element-collapsable"));s=function(M){var L=u()[v?"selected":"available"],J=(M)?M.attr("data-collapse-icon"):null,H=(M)?M.attr("data-expand-icon"):null,K=(J)?"ui-icon "+J:"ui-icon ui-icon-triangle-1-s",I=(H)?"ui-icon "+H:"ui-icon ui-icon-triangle-1-e";
            L.collapsed=!L.collapsed;L.listContainer.slideToggle();w.removeClass(L.collapsed?K:I).addClass(L.collapsed?I:K)}}else{if(B){d("<span></span>").addClass("collapse-handle "+B).css("cursor","default").prependTo(C.addClass("group-element-collapsable"))}}return d("<div></div>").data("fnUpdateCount",x).data("fnToggle",s||d.noop).append(C)
},_createGroupContainerElement:function(x,w,t){var u=this;var v=d("<div></div>");var s;if(this._widget.options.sortable&&t){v.sortable({tolerance:"pointer",appendTo:this._widget._elementWrapper,connectWith:this._widget._lists.available.attr("id"),scope:this._widget.scope,helper:"clone",receive:function(y,z){var A=u._elements[s=z.item.data("element-index")];
    A.selected=true;A.optionElement.prop("selected",true);A.listElement.removeClass("ui-state-active")},stop:function(y,z){var A;if(s){A=u._elements[s];s=g;z.item.replaceWith(A.listElement.addClass("ui-state-highlight option-selected"));u._widget._updateHeaders();
    u._widget._triggerUIEvent(h,{optionElements:[A.optionElement[0]],selected:true})}else{A=u._elements[z.item.data("element-index")];if(A&&!A.selected){u._bufferedMode(true);u._appendToList(A);u._bufferedMode(false)}}if(A){u._reorderSelected(A.optionGroup)}},revert:true})
}if(this._selectionMode){d(v).on(this._selectionMode,"div.option-element",function(){var y=u._elements[d(this).data("element-index")];y.listElement.removeClass("ui-state-hover");u.setSelected(y,!t)})}return v},_createElement:function(t,x){var w=this._widget.options.optionRenderer?this._widget.options.optionRenderer(t,x):d("<div></div>").text(t.text());
    var s=t.attr("data-option-icon");var v=d("<div></div>").append(w).addClass("ui-state-default option-element").attr("unselectable","on").data("element-index",-1).hover(function(){if(t.prop("selected")){d(this).removeClass("ui-state-highlight")}d(this).addClass("ui-state-hover")
    },function(){d(this).removeClass("ui-state-hover");if(t.prop("selected")){d(this).addClass("ui-state-highlight")}});if(this._widget.options.selectionMode.indexOf("d&d")>-1){var u=this;v.draggable({addClasses:false,cancel:(this._widget.options.sortable?".option-selected, ":"")+".ui-state-disabled",appendTo:this._widget._elementWrapper,scope:this._widget.scope,start:function(y,z){d(this).addClass("ui-state-disabled ui-state-active");
        z.helper.width(d(this).width()).height(d(this).height())},stop:function(y,z){d(this).removeClass("ui-state-disabled ui-state-active")},helper:"clone",revert:"invalid",zIndex:99999,disabled:t.prop("disabled")});if(t.prop("disabled")){v.addClass("ui-state-disabled")
    }if(this._widget.options.sortable){v.draggable("option","connectToSortable",this._groups.get(x)["selected"].listContainer)}}else{if(t.prop("disabled")){v[(t.prop("disabled")?"add":"remove")+"Class"]("ui-state-disabled")}}if(s){v.addClass("grouped-option").prepend(d("<span></span>").addClass("ui-icon "+s))
    }return v},_isOptionCollapsed:function(s){return this._groups.get(s.optionGroup)[s.selected?"selected":"available"].collapsed},_updateGroupElements:function(u){if(u){u.selected.count=0;u.available.count=0;for(var t=u.startIndex,s=u.startIndex+u.count;t<s;t++){u[this._elements[t].selected?"selected":"available"].count++
}u.selected.listElement.data("fnUpdateCount")();u.available.listElement.data("fnUpdateCount")()}else{this._groups.each(function(v,x,w){w._updateGroupElements(x)},this)}},_appendToList:function(t){var w=this;var v=this._groups.get(t.optionGroup);var x=v[t.selected?"selected":"available"];
    if((t.optionGroup!=this._widget.options.defaultGroupName)||this._widget.options.showDefaultGroupHeader){x.listElement.show()}if(x.collapsed){x.listElement.data("fnToggle")()}else{x.listContainer.show()}var s=t.index-1;while((s>=v.startIndex)&&(this._elements[s].selected!=t.selected)){s--
    }if(s<v.startIndex){x.listContainer.prepend(t.listElement)}else{var u=this._elements[s].listElement;if(u.parent().hasClass("ui-effects-wrapper")){u=u.parent()}t.listElement.insertAfter(u)}t.listElement[(t.selected?"add":"remove")+"Class"]("ui-state-highlight option-selected");
    if((t.selected||!t.filtered)&&!this._isOptionCollapsed(t)&&this._moveEffect&&this._moveEffect.fn){t.listElement.hide().show(this._moveEffect.fn,this._moveEffect.options,this._moveEffect.speed)}else{if(t.filtered){t.listElement.hide()}}},_reorderSelected:function(w){var v=this._elements;
    var u=this._groups.get(w);var t=u.groupElement?u.groupElement:this._widget.element;var s;d(".option-element",u.selected.listContainer).each(function(){var x=v[d(this).data("element-index")].optionElement;if(!s){t.prepend(x)}else{x.insertAfter(s)}s=x})},_bufferedMode:function(s){if(s){this._oldMoveEffect=this._moveEffect;
    this._moveEffect=null;this._widget._lists.selected.data("scrollTop",this._widget._lists.selected.scrollTop());this._widget._lists.available.data("scrollTop",this._widget._lists.available.scrollTop());this._listContainers.selected.detach();this._listContainers.available.detach()
}else{this._widget._lists.selected.append(this._listContainers.selected).scrollTop(this._widget._lists.selected.data("scrollTop")||0);this._widget._lists.available.append(this._listContainers.available).scrollTop(this._widget._lists.available.data("scrollTop")||0);
    this._moveEffect=this._oldMoveEffect;delete this._oldMoveEffect}},reset:function(u){this._groups.clear();this._listContainers.selected.empty();this._listContainers.available.empty();if(u){for(var t=0,v=this._elements,s=v.length;t<s;t++){v[t].optionElement.removeData("element-index")
}delete this._elements;delete this._groups;delete this._listContainers}else{this._elements=[];this.prepareGroup();this._groups.setComparator(this.getComparator())}},cleanup:function(){var w=this._widget.element[0];var v=[];this._groups.each(function(y,x){if(x.groupElement&&!d.contains(w,x.groupElement[0])){v.push(y)
}});for(var t=0,u;t<this._elements.length;t++){u=this._elements[t];if(!d.contains(w,u.optionElement[0])||(d.inArray(u.optionGroup,v)>-1)){this._elements.splice(t--,1)[0].listElement.remove()}}for(var t=0,s=v.length;t<s;t++){this._groups.remove(v[t])}this.prepareGroup()
},getComparator:function(){return this._widget.options.sortMethod?typeof this._widget.options.sortMethod=="function"?this._widget.options.sortMethod:m[this._widget.options.sortMethod]:null},prepareGroup:function(t,s){s=s||l;if(!this._groups.containsKey(s)){this._groups.put(s,{startIndex:-1,count:0,selected:{collapsed:false,count:0,listElement:this._createGroupElement(t,s,true),listContainer:this._createGroupContainerElement(t,s,true)},available:{collapsed:false,count:0,listElement:this._createGroupElement(t,s,false),listContainer:this._createGroupContainerElement(t,s,false)},groupElement:t,optionGroup:s})
}},prepareOption:function(s,u){var t;if(s.data("element-index")===g){u=u||l;this._elements.push(t={index:-1,selected:false,filtered:false,listElement:this._createElement(s,u),optionElement:s,optionGroup:u})}else{this._elements[s.data("element-index")].listElement[(s.prop("disabled")?"add":"remove")+"Class"]("ui-state-disabled")
}},reIndex:function(){var t=this.getComparator();if(t){var v=this._groups;this._elements.sort(function(z,y){var C=v.get(z.optionGroup).groupElement;var B=v.get(y.optionGroup).groupElement;var A=t(C?C.attr("label"):l,B?B.attr("label"):l);if(A!=0){return A}else{return t(z.optionElement.text(),y.optionElement.text())
}})}this._bufferedMode(true);this._groups.each(function(C,A,z,y){if(!A.available.listContainer.parents(".multiselect-element-wrapper").length){if(A.groupElement){A.groupElement.data("option-group",C)}var B=d("<div></div>").addClass("multiselect-element-wrapper").data("option-group",C);
    var D=d("<div></div>").addClass("multiselect-element-wrapper").data("option-group",C);B.append(A.selected.listElement.hide());if(C!=l||(C==l&&y)){D.append(A.available.listElement.show())}B.append(A.selected.listContainer);D.append(A.available.listContainer);
    z.selected.append(B);z.available.append(D)}A.count=0},this._listContainers,this._widget.options.showDefaultGroupHeader);for(var u=0,w,x,s=this._elements.length;u<s;u++){w=this._elements[u];x=this._groups.get(w.optionGroup);if(x.startIndex==-1||x.startIndex>=u){x.startIndex=u;
    x.count=1}else{x.count++}w.listElement.data("element-index",w.index=u);if(w.optionElement.data("element-index")==g||w.selected!=w.optionElement.prop("selected")){w.selected=w.optionElement.prop("selected");w.optionElement.data("element-index",u);this._appendToList(w)
}}this._updateGroupElements();this._widget._updateHeaders();this._groups.each(function(A,y,z){z._reorderSelected(A)},this);this._bufferedMode(false)},filter:function(t,x){if(t&&!x){var y={term:t};if(this._widget._triggerUIEvent(f,y)){t=y.term}else{return}}this._bufferedMode(true);
    var s=this._widget.options.filterSelected;var z=this._widget.options.searchFilter||function(C,B){return B.innerHTML.toLowerCase().indexOf(C)>-1};t=(this._widget.options.searchPreFilter||function(B){return B?(B+"").toLowerCase():false})(t);for(var u=0,A,v=this._elements.length,w;
                                                                                                                                                                                                                                                       u<v;u++){A=this._elements[u];w=!(!t||z(t,A.optionElement[0]));if((!A.selected||s)&&(A.filtered!=w)){A.listElement[w?"hide":"show"]();A.filtered=w}else{if(A.selected){A.filtered=false}}}this._widget._updateHeaders();this._bufferedMode(false)},getSelectionInfo:function(){var v={selected:{total:0,count:0,filtered:0},available:{total:0,count:0,filtered:0}};
    for(var t=0,s=this._elements.length;t<s;t++){var u=this._elements[t];v[u.selected?"selected":"available"][u.filtered?"filtered":"count"]++;v[u.selected?"selected":"available"].total++}return v},setSelected:function(u,t,s){if(u.optionElement.attr("disabled")&&t){return
}u.optionElement.prop("selected",u.selected=t);this._appendToList(u);if(!s){if(this._widget.options.sortable&&t){this._reorderSelected(u.optionGroup)}this._updateGroupElements(this._groups.get(u.optionGroup));this._widget._updateHeaders();this._widget._triggerUIEvent(h,{optionElements:[u.optionElement[0]],selected:t})
}},setSelectedAll:function(x){var t=[];var w={};this._bufferedMode(true);for(var u=0,v,s=this._elements.length;u<s;u++){v=this._elements[u];if(!((v.selected==x)||(v.optionElement.attr("disabled")||(x&&(v.filtered||v.selected))))){this.setSelected(v,x,true);
    t.push(v.optionElement[0]);w[v.optionGroup]=true}}if(this._widget.options.sortable&&x){var y=this;d.each(w,function(z){y._reorderSelected(z)})}this._updateGroupElements();this._widget._updateHeaders();this._bufferedMode(false);this._widget._triggerUIEvent(h,{optionElements:t,selected:x})
}};function r(y){var s=d.uix.multiselect.i18n[y.locale]?y.locale:"";var x=d.uix.multiselect.i18n[s];var u=y.plural||0;var w=y.data||{};var v;if(u===2&&x[y.key+"_plural_two"]){v=x[y.key+"_plural_two"]}else{if((u===2||u===3)&&x[y.key+"_plural_few"]){v=x[y.key+"_plural_few"]
}else{if(u>1&&x[y.key+"_plural"]){v=x[y.key+"_plural"]}else{if(u===0&&x[y.key+"_nil"]){v=x[y.key+"_nil"]}else{v=x[y.key]||""}}}}return v.replace(/\{([^\}]+)\}/g,function(t,z){return w[z]})}d.uix.multiselect.i18n={"":{itemsSelected_nil:"no selected option",itemsSelected:"{count} selected option",itemsSelected_plural:"{count} selected options",itemsAvailable_nil:"no item available",itemsAvailable:"{count} available option",itemsAvailable_plural:"{count} available options",itemsFiltered_nil:"no option filtered",itemsFiltered:"{count} option filtered",itemsFiltered_plural:"{count} options filtered",selectAll:"Select All",deselectAll:"Deselect All",search:"Search Options",collapseGroup:"Collapse Group",expandGroup:"Expand Group",selectAllGroup:"Select All Group",deselectAllGroup:"Deselect All Group"}}
})(jQuery,window);
