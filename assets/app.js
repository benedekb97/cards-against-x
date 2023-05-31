/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

const $ = require('jquery');

require('bootstrap');

$(document).ready(
    function () {
        $('[data-toggle="popover"]').popover();

        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    }
)

$('.login-back-button').click(
    function () {
        history.back();
    }
)

const u = JSON.parse(document.getElementById('mercure-url').textContent);

const es = new EventSource(
    u,
    {
        withCredentials: true
    }
);

es.onmessage = e => {
    const data = JSON.parse(e.data);

    const url = data.url;

    if (url.includes('lobby')) {
        lobbyFunction(data);

        return;
    }

    if (url.includes('game')) {
        gameFunction(data);
    }
}

const lobbyFunction = function (data) {
    console.log(data);
}

const gameFunction = function (data) {

}
