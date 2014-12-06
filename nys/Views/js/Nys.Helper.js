var nys;
if (typeof nys === "undefined")
    nys = {};

;
(function() {

    /**
	needed jquery extension...
    */
    $.urlParam = function(name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results == null) {
            return null;
        } else {
            return results[1] || 0;
        }
    }
    //https://stackoverflow.com/questions/881510/jquery-sorting-json-by-properties
    function sortJSON(data, key, way) {
        return data.sort(function(a, b) {
            var x = a[key];
            var y = b[key];
            if (way == 1) {
                return ((x < y) ? -1 : ((x > y) ? 1 : 0));
            }
            if (way == -1) {
                return ((x > y) ? -1 : ((x < y) ? 1 : 0));
            }
        });
    }

    function DisplaySpinner() {
        $(".entry").hide();
        //Hide the spinner on small screens
        $("#spinner").addClass("hidden-xs");
        var opts = {
            lines: 13, // The number of lines to draw
            length: 40, // The length of each line
            width: 10, // The line thickness
            radius: 60, // The radius of the inner circle
            corners: 0.8, // Corner roundness (0..1)
            rotate: 5, // The rotation offset
            direction: 1, // 1: clockwise, -1: counterclockwise
            color: '#000', // #rgb or #rrggbb or array of colors
            speed: 1, // Rounds per second
            trail: 100, // Afterglow percentage
            shadow: false, // Whether to render a shadow
            hwaccel: false, // Whether to use hardware acceleration
            className: 'spinner', // The CSS class to assign to the spinner
            zIndex: 2e9, // The z-index (defaults to 2000000000)
            // top: '50%', // Top position relative to parent
            //  left: '50%' // Left position relative to parent
        };
        var target = document.getElementById('spinner');
        var spinner = new Spinner(opts).spin(target);
    }

    function HideSpinner() {
        var target = document.getElementById('spinner');

        $(document).ready(function() {
            $("#spinner").html("");
            $(".entry").fadeIn();
        });
    }

    nys.sortJSON = sortJSON;
    nys.DisplaySpinner = DisplaySpinner;
    nys.HideSpinner = HideSpinner;
}());