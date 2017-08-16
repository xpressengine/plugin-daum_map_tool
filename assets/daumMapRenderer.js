(function ($) {
    var scripts = document.getElementsByTagName('script');
    var lastScript = scripts[scripts.length-1];
    var scriptName = lastScript.src;

    var _jsLoad = function(targetDoc, src, load, error) {
        var el = targetDoc.createElement('script');

        el.src = src;
        el.async = true;

        if(load) {
            el.onload = load;
        }

        if(error) {
            el.onerror = error;
        }

        targetDoc.head.appendChild(el);
    };

    var getParam = function (name) {
        var qs = scriptName.replace(/^[^\?]+\??/, '');

        return (function (query) {
            var params = {};

            if (!query) {
                return name ? null : {};
            }

            var pairs = query.split(/[;&]/);
            for (var i = 0; i < pairs.length; i++) {
                var KeyVal = pairs[i].split('=');
                if (!KeyVal || KeyVal.length != 2) {
                    continue;
                }
                var key = unescape(KeyVal[0]);
                var val = unescape(KeyVal[1]);
                val = val.replace(/\+/g, ' ');
                params[key] = val;
            }
            return name ? params[name] : params;
        })(qs);
    };
    
    $.fn['daumMapRender'] = function (options) {
        var options = options || {},
            win = options.win || window,
            callback = options.callback || function () {},
            $tar = this instanceof jQuery ? this : $(this);

        var render = function (tar, win, callback) {
            var toolData = JSON.parse($(tar).attr('xe-tool-data').replace(/'/g, '"'));
            var lat = toolData.lat;
            var lng = toolData.lng;
            var text = toolData.text;
            var zoom = toolData.zoom || 10;

            var map = new daum.maps.Map(tar, {
                center: new daum.maps.LatLng(lat, lng),
                zoom: zoom
            });

            var myLatLng = new daum.maps.LatLng(lat, lng);
            var marker = new daum.maps.Marker({
                position: myLatLng,
                map: map
            });

            var infowindow = new daum.maps.InfoWindow({
                content: text
            });

            infowindow.open(map, marker);

            callback(tar);
        };

        var act = function () {
            $tar.each(function () {
                render(this, win, callback);
            });
        };

        if(win.daum && win.daum.maps) {
            act();
        } else {
            _jsLoad(win.document, '//dapi.kakao.com/v2/maps/sdk.js?appkey=' + getParam('key'), act);
        }
    }
})(jQuery);
