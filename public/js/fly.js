/**
 * Created by kacpe on 17.07.2016.
 */

(function ($) {
    "use strict";

    var Fly = new function () {
        this.debug = true;

        this.log = function (message) {
            if (this.debug)
                console.log("DBG: " + message);
        };

        this.initialize = new function () {
            this.onReady = function () {
                Fly.log("onReady");
            };

            this.onLoad = function () {
                Fly.log("onLoad");
            };

            this.onResize = function () {
                Fly.log("onResize");
            };
        };
        this.Request = function (url, def_options) {
            this.options = {
                method: 'post',
                data: {},
                onSuccess: Function,
                onDone: Function,
                beforeSend: Function
            };
            $.extend(this.options, def_options || {});

            this.request = function (url) {
                url = url + "&ajax=" + fly["session_id"];
                Fly.log("Request FROM: " + url);
                var c_options = this.options;
                $.ajax({
                    method: c_options.method,
                    url: url,
                    data: c_options.data,
                    beforeSend: function () {
                        // TODO loading
                        c_options.beforeSend.call(c_options.beforeSend);
                    },
                    success: function (data) {
                        c_options.onSuccess.call(c_options.onSuccess, data);
                    }
                }).done(function (data) {
                    c_options.onDone.call(c_options.onDone, data);
                }).always(function () {
                    // TODO end loading
                });

            };
            this.request(url);
        };

        this.Redirect = function (url, def_options) {
            this.options = {
                method: 'post',
                title: '',
                data: {}
            };
            $.extend(this.options, def_options || {});
            this.request = function (url) {
                url = url + "&ajax=" + fly["session_id"];
                Fly.log("redirect to: " + url);
            };
            this.request(url);
        };

        this.Tools = new function () {
            this.Redirect = function (url) {
                var ua = navigator.userAgent.toLowerCase(),
                    isIE = ua.indexOf('msie') !== -1,
                    version = parseInt(ua.substr(4, 2), 10);

                if (isIE && version < 9) {
                    var link = document.createElement('a');
                    link.href = url;
                    document.body.appendChild(link);
                    link.click();
                }
                else
                    window.location.replace(url);
            };
            this.toHtmlEncoded = function (string) {
                return string.replace(/[\u0080-\uC350]/g, function (a) {
                    return '&#' + a.charCodeAt(0) + ';';
                });
            };
            this.isString = function (object) {
                return Object.prototype.toString.call(object) === '[object String]';
            };
            this.isArray = function (object) {
                return Object.prototype.toString.call(object) === '[object Array]';
            };
            this.stringify = function (object) {
                return JSON.stringify(object);
            };
        };

        this.Cookie = new function () {
            this.set = function (cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d.toUTCString();
                $(document).cookie = cname + "=" + cvalue + "; " + expires;
            };

            this.get = function (cname) {
                var name = cname + "=";
                var ca = $(document).cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') c = c.substring(1);
                    if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
                }
                return false;
            };

            this.delete = function (cname) {
                this.set(cname, "", -3600);
            };
        };
    };

    $(document).ready(Fly.initialize.onReady());
    $(window).load(Fly.initialize.onLoad());
    $(window).on('resize', Fly.initialize.onResize());
    $(document).delegate("a", "click", function (event) {
        event.preventDefault();
        Fly.Redirect($(this).attr("href"), {});
    });

    new Fly.Request(fly['board_url'] + "index.php?app=core&module=ajax_test", {
        onSuccess: function (data) {

        },
        onDone: function (data) {
            console.log(data);
        }
    });

})(jQuery);


