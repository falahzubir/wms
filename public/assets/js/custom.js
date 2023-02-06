// select all checkboxes
function toggleCheckboxes(source, cls) {
    checkboxes = document.querySelectorAll(`.${cls}`);
    for (var i = 0, n = checkboxes.length; i < n; i++) {
        checkboxes[i].checked = source.checked;
    }
}

function linkTrack(num) {
    TrackButton.track({
        tracking_no: num
    });
}

//auto close alert span js
if (document.getElementsByClassName('alert-autoclose').length > 0) {
    setTimeout(function() {
        document.querySelector('.alert-autoclose').style.display = 'none';
    }, 3000);
}

function queryStringToJSON(qs) {
    qs = qs || location.search.slice(1);

    var pairs = qs.split('&');
    var result = {};
    pairs.forEach(function(p) {
        var pair = p.split('=');
        var key = pair[0];
        var value = decodeURIComponent(pair[1] || '');

        if( result[key] ) {
            if( Object.prototype.toString.call( result[key] ) === '[object Array]' ) {
                result[key].push( value );
            } else {
                result[key] = [ result[key], value ];
            }
        } else {
            result[key] = value;
        }
    });

    return JSON.parse(JSON.stringify(result));
};

function not_ready() {
    // sweet alert
    Swal.fire({
        title: 'Not Ready!',
        text: "This feature still in progress.",
        icon: 'warning',
        confirmButtonText: 'OK'
    })
}
