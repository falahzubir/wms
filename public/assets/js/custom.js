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
setTimeout(function() {
    document.querySelector('.alert-autoclose').style.display = 'none';
}, 3000);
