/**
 * Created by kacpe on 17.07.2016.
 */

var Fly = Fly || {};

(function ($) {
    "use strict";

    Fly.onReady = function () {
        Fly.initialize.init();
    };

    Fly.onLoad = function () {
        Fly.initialize.preload();
    };

    Fly.onResize = function () {

    };

    $(document).ready(Fly.onReady);
    $(window).load(Fly.onLoad);
    $(window).on('resize', Fly.onResize);

    Fly.initialize = {
        init: function () {
            //Fly.Request("test.html");
        },
        preload: function () {

        }
    };

    Fly.Request = function (url, def_options) {
        var options = {
            method: 'post'
        };
        $.extend(options, def_options || {});
        request(url);

        function request(url) {
            // TODO
            /*$.ajax({
             type: options.method,
             url: url,
             data: options.data,
             beforeSend: options.beforeSend,
             success: options.onSuccess
             });*/
        }
    };

    Fly.Redirect = function (url, def_options) {
        var options = {
            method: 'post',
            title: '',
            data: {}
        };
        $.extend(options, def_options || {});
        redirect(url);

        function redirect(url) {
            // TODO
        }
    };

    Fly.Tools = {
        Redirect: function (url) {
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
        },
        toHtmlEncoded: function (string) {
            return string.replace(/[\u0080-\uC350]/g, function (a) {
                return '&#' + a.charCodeAt(0) + ';';
            });
        },
        isString: function (object) {
            return Object.prototype.toString.call(object) === '[object String]';
        },
        isArray: function (object) {
            return Object.prototype.toString.call(object) === '[object Array]';
        },
        stringify: function (object) {
            return JSON.stringify(object);
        }/*,
         toHTML: function (object) {
         return object && object.toHTML ? object.toHTML() : String.interpret(object);
         }*/
    };

    /*Object.extend(String, {
     interpret: function (value) {
     return value == null ? '' : String(value);
     }
     });*/

    Fly.Cookie = {
        set: function (cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toUTCString();
            $(document).cookie = cname + "=" + cvalue + "; " + expires;
        },
        get: function (cname) {
            var name = cname + "=";
            var ca = $(document).cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1);
                if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
            }
            return false;
        },
        delete: function (cname) {
            this.set(cname, "", -3600);
        }
    };
})(jQuery);


