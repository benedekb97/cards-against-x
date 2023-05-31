/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
import {read} from "@popperjs/core";

const $ = require('jquery');

const bootstrap = require('bootstrap');

$(document).ready(
    function () {
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        let toastElList = [].slice.call(document.querySelectorAll('.toast'));
        let toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl);
        });
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

const playerId = parseInt($('#player-id').val());

const lobbyFunction = function (data) {
    const readyCheck = `&nbsp;<i class="bi-check"></i>`;
    const hostStar = `&nbsp;<i class="bi-star"></i>`;

    $('#number-of-rounds').val(data.numberOfRounds);
    $('#deck').val(data.deckId);

    if (data.readyToStart) {
        $('#start-button').removeClass('btn-secondary').addClass('btn-primary').removeAttr('disabled');
    } else {
        $('#start-button').removeClass('btn-primary').addClass('btn-secondary').attr('disabled', 'disabled');
    }

    data.users.forEach(
        function (user) {
            const player = user.player;

            let playerRow = `<td>${user.nickname ?? user.name}`;

            if (player.host) {
                playerRow += hostStar;

                if (player.id === playerId) {
                    $('#deck').removeAttr('disabled').removeAttr('readonly');
                    $('#number-of-rounds').removeAttr('disabled').removeAttr('readonly');

                    $('#start-button').css('display', 'block');
                } else {
                    $('#start-button').css('display', 'none');
                }
            }

            if (player.ready) {
                playerRow += readyCheck;
            }

            playerRow += `</td>`;

            $(`#player-${player.id}`).html(playerRow);
        }
    )
}

const gameFunction = function (data) {

}
$('#game-slug').on('click', function(e){
    navigator.clipboard.writeText(e.target.value);

    let toastElement = document.getElementById('copy-toast');
    let myToast = bootstrap.Toast.getOrCreateInstance(toastElement).show();
});

const updateLobby = function () {
    fetch(
        $('#update-lobby-route').val(),
        {
            method: "POST",
            body: JSON.stringify(
                {
                    deckId: parseInt($('#deck').val()),
                    numberOfRounds: parseInt($('#number-of-rounds').val())
                }
            ),
            headers: {
                "Content-type": "application/json; charset=UTF-8"
            }
        }
    )
        .then((response) => response.json())
        .then((json) => console.log(json));
}

$('#number-of-rounds').on('change', updateLobby);
$('#deck').on('change', updateLobby);

const readyButton = $('#ready-button');
readyButton.on('click', function () {
    if (readyButton.hasClass('btn-primary')) {
        readyButton.removeClass('btn-primary');
        readyButton.addClass('btn-secondary');
        readyButton.text('Not ready');
    } else {
        readyButton.removeClass('btn-secondary');
        readyButton.addClass('btn-primary');
        readyButton.text('Ready');
    }

    fetch(
        $('#ready-route').val(),
        {
            method: 'POST'
        }
    ).then(
        (response) => response.json()
    ).then((json) => console.log(json));
});