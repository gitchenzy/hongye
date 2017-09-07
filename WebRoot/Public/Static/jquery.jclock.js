(function ($) {
    $.fn.jclock = function (options) {
        $.fn.jclock.timerID = null;
        $.fn.jclock.running = false;
        $.fn.jclock.el = null;
        var version = '0.1.0';

        // options
        var opts = $.extend({}, $.fn.jclock.defaults, options);

        return this.each(function () {
            $this = $(this);
            $.fn.jclock.el = $this;

            var o = $.meta ? $.extend({}, opts, $this.data()) : opts;

            $.fn.jclock.timeNotation = o.timeNotation;
            $.fn.jclock.am_pm = o.am_pm;
            $.fn.jclock.utc = o.utc;

            $this.css({
                fontFamily: o.fontFamily,
                fontSize: o.fontSize,
                backgroundColor: o.background,
                color: o.foreground
            });

            $.fn.jclock.startClock();

        });
    };

    $.fn.jclock.startClock = function () {
        $.fn.jclock.stopClock();
        $.fn.jclock.displayTime();
    }
    $.fn.jclock.stopClock = function () {
        if ($.fn.jclock.running) {
            clearTimeout(timerID);
        }
        $.fn.jclock.running = false;
    }
    $.fn.jclock.displayTime = function (el) {
        var time = $.fn.jclock.getTime();
        $.fn.jclock.el.html(time);
        timerID = setTimeout("$.fn.jclock.displayTime()", 1000);
    }
    $.fn.jclock.getTime = function () {
        var now = new Date();
        var hours, minutes, seconds;

        if ($.fn.jclock.utc == true) {
            hours = now.getUTCHours();
            minutes = now.getUTCMinutes();
            seconds = now.getUTCSeconds();
        } else {
            hours = now.getHours();
            minutes = now.getMinutes();
            seconds = now.getSeconds();
        }

        if ($.fn.jclock.timeNotation == '12h') {
            hours = ((hours > 12) ? hours - 12 : hours);
        } else {
            hours = ((hours < 10) ? "0" : "") + hours;
        }

        minutes = ((minutes < 10) ? "0" : "") + minutes;
        seconds = ((seconds < 10) ? "0" : "") + seconds;

        var timeNow = hours + ":" + minutes + ":" + seconds;
        if (($.fn.jclock.timeNotation == '12h') && ($.fn.jclock.am_pm == true)) {
            timeNow += (hours >= 12) ? " P.M." : " A.M."
        }

        return timeNow;
    };

    // plugin defaults
    $.fn.jclock.defaults = {
        timeNotation: '24h',
        am_pm: false,
        utc: false,
        fontFamily: '',
        fontSize: '',
        foreground: '',
        background: ''
    };
})(jQuery);