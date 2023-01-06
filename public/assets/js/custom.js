// select all checkboxes
function toggleCheckboxes(source, cls) {
    checkboxes = document.querySelectorAll(`.${cls}`);
    for (var i = 0, n = checkboxes.length; i < n; i++) {
        checkboxes[i].checked = source.checked;
    }
}
