
if (document.querySelector('#start-date')) {
    let start = document.querySelector('#start-date');
    let end = document.querySelector('#end-date');
    document.querySelector('#btn-check-today').onclick = function() {
        start.value = moment().format('YYYY-MM-DD');
        end.value = moment().format('YYYY-MM-DD');
    }
    document.querySelector('#btn-check-yesterday').onclick = function() {
        start.value = moment().subtract(1, 'days').format('YYYY-MM-DD');
        end.value = moment().subtract(1, 'days').format('YYYY-MM-DD');
    }
    document.querySelector('#btn-check-this-month').onclick = function() {
        start.value = moment().startOf('month').format('YYYY-MM-DD');
        end.value = moment().endOf('month').format('YYYY-MM-DD');
    }
    document.querySelector('#btn-check-last-month').onclick = function() {
        start.value = moment().subtract(1, 'months').startOf('month').format('YYYY-MM-DD');
        end.value = moment().subtract(1, 'months').endOf('month').format('YYYY-MM-DD');
    }
    document.querySelector('#btn-check-overall').onclick = function() {
        start.value = '';
        end.value = '';
    }
}

// select all checkboxes
function toggleCheckboxes(source, cls) {
    checkboxes = document.querySelectorAll(`.${cls}`);
    for (var i = 0, n = checkboxes.length; i < n; i++) {
        checkboxes[i].checked = source.checked;
    }
    checkedOrder = []; //reset array
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

let notification_list = document.querySelector('#notification-list');
fetch('/notifications')
    .then(response => response.json())
    .then(data => {
        document.querySelectorAll('.notification-count').forEach(function (length) {
            length.innerHTML = data.length;
        });
        data.forEach(function (item) {
            notification_list.innerHTML += `<li class="message-item">
            <a href="${item.url}">
                <div>
                    <p>${item.message}</p>
                </div>
            </a>
            </li>
            <li>
            <hr class="dropdown-divider">
            </li>`;
        });
    })
    // .catch(error => {
    //     console.error(error);
    // }
// );
