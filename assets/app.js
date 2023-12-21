/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
import {read} from "@popperjs/core";
import "/node_modules/flag-icons/css/flag-icons.min.css";

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

let mercureUrl = document.getElementById('mercure-url');

if (mercureUrl !== null) {
    let currentTurnStatus = $('#current-turn-status').val();
    const u = JSON.parse(mercureUrl.textContent);

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
}

const playerId = parseInt($('#player-id').val());

const lobbyFunction = function (data) {
    if (data.status === 'in_progress') {
        window.location = $('#game-url').val()

        return;
    }

    $('#number-of-rounds').val(data.numberOfRounds);
    $('#deck').val(data.deckId);

    if (data.readyToStart) {
        $('#start-button').removeClass('btn-secondary').addClass('btn-primary').removeAttr('disabled');
    } else {
        $('#start-button').removeClass('btn-primary').addClass('btn-secondary').attr('disabled', 'disabled');
    }

    let html = '<tbody>';

    if (!Array.isArray(data.users)) {
        html += handleUserList(data.users[1]);
    } else {
        for (const user of data.users) {
            html += handleUserList(user);
        }
    }

    html += '</tbody>';

    $('#players-table').html(html);
}

const handleUserList = function (user) {
    const readyCheck = `&nbsp;<i class="bi-check"></i>`;
    const hostStar = `&nbsp;<i class="bi-star"></i>`;

    const player = user.player;

    let playerRow = `<tr id="player-${player.id}"><td>${user.nickname ?? user.name}`;

    if (player.host) {
        playerRow += hostStar;

        if (player.id === playerId) {
            $('#deck').removeAttr('disabled').removeAttr('readonly');
            $('#number-of-rounds').removeAttr('disabled').removeAttr('readonly');

            $('#start-button').css('display', 'inherit');
        } else {
            $('#start-button').css('display', 'none');
        }
    }

    if (player.ready) {
        playerRow += readyCheck;
    }

    playerRow += `</td></tr>`;

    return playerRow;
}

const gameFunction = function (data) {
    let currentTurnStatus = $('#current-turn-status').val();

    if (data.turnStatus !== currentTurnStatus) {
        location.reload();
    }

    let allReady = true;

    for (let player of data.players) {
        let playerTag = $(`#player-${player.id}`);

        if (player.ready || player.played) {
            playerTag.css('background-color', 'rgba(255, 255, 255, 0.2').html(
                `${player.name} <i class="bi bi-check"></i><br><i class="fs-6">${player.points} points</i>`
            )
        }

        if (!player.ready) {
            allReady = false;
        }
    }

    if (allReady) {
        location.reload();
    }
}


$('#game-slug').on('click', function(e){
    navigator.clipboard
        .writeText(e.target.value)
        .then(function() {
            let toastElement = document.getElementById('copy-toast');
            bootstrap.Toast.getOrCreateInstance(toastElement).show();
        });
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
    );
});

let blankCardMap = [];

$(document).ready(
    function () {
        const blankCardCount = $('#blank-card-count').val() ?? 0;

        for (let i = 0; i < blankCardCount; i++) {
            blankCardMap[i] = null;
        }

        $('.game-card-link').on('click', function (e) {
            let cardId = $(this).data('card-id');
            let cardText = $(this).data('card-text');

            for (let element of blankCardMap) {
                if (element !== null && element.id === cardId) {
                    return;
                }
            }

            for (let i = 0; i < blankCardCount; i++) {
                if (blankCardMap[i] === null) {
                    blankCardMap[i] = {
                        id: cardId,
                        text: cardText
                    }

                    $(`#blank-card-${i+1}-body`).text(cardText);
                    $(`#blank-card-${i+1}`).addClass('white-card').removeClass('blank-card').addClass('blank-card-clickable');

                    $(`#white-card-${cardId}`).addClass('used-white-card');

                    updateListeners();

                    let hasNullValue = false;
                    for (let element of blankCardMap) {
                        if (element === null) {
                            hasNullValue = true;

                            break;
                        }
                    }

                    if (!hasNullValue) {
                        $('#submit-button').removeAttr('disabled').removeClass('btn-secondary').addClass('btn-primary');
                    } else {
                        $('#submit-button').attr('disabled', 'disabled');
                    }

                    $('#reset-button').removeAttr('disabled');

                    return;
                }
            }
        });

        $('#reset-button').click(
            function () {
                if ($(this).attr('disabled')) {
                    return;
                }

                for (let i = 0; i < blankCardCount; i++) {
                    $(`#blank-card-${i+1}`).removeClass('blank-card-clickable').addClass('blank-card').removeClass('white-card');
                    $(`#blank-card-${i+1}-body`).text('');
                    $(`#white-card-${blankCardMap[i].id}`).removeClass('used-white-card');
                    blankCardMap[i] = null;
                }

                $('#submit-button').attr('disabled', 'disabled').removeClass('btn-primary').addClass('btn-secondary');
                $('#reset-button').attr('disabled', 'disabled');
            }
        );

        $('#submit-button').click(
            function () {
                if ($(this).attr('disabled')) {
                    return;
                }

                let ids = [];

                for (let i = 0; i < blankCardCount; i++) {
                    ids[i] = blankCardMap[i].id;
                }

                fetch(
                    $('#submit-url').val(),
                    {
                        method: 'POST',
                        body: JSON.stringify(
                            {
                                ids: ids,
                            }
                        ),
                        headers: {
                            "Content-type": "application/json; charset=UTF-8"
                        }
                    }
                )
            }
        )
    }
);

function updateListeners() {
    $('.blank-card-clickable').on('click', function() {
        let blankCardId = parseInt($(this).data('id'));

        $(this).removeClass('blank-card-clickable').addClass('blank-card').removeClass('white-card');
        $(`#blank-card-${blankCardId}-body`).text('');

        $(`#white-card-${blankCardMap[blankCardId-1].id}`).removeClass('used-white-card');

        blankCardMap[blankCardId-1] = null;

        $('#submit-button').attr('disabled', 'disabled').removeClass('btn-primary').addClass('btn-secondary');

        let hasValue = false;
        for (let element of blankCardMap) {
            if (element !== null) {
                hasValue = true;

                break;
            }
        }

        if (!hasValue) {
            $('#reset-button').attr('disabled', 'disabled');
        }
    });
}

$(document).ready(function() {
    let selectedPlay = null;

    $('.black-card-link').click(
        function () {
            $(this).addClass('black-card-link-clicked');
            if (selectedPlay !== null) {
                $(`#play-${selectedPlay}`).removeClass('black-card-link-clicked');
            }
            if (selectedPlay === $(this).data('play-id')) {
                selectedPlay = null;
            } else {
                selectedPlay = $(this).data('play-id');
            }

            if (selectedPlay !== null) {
                $('#play-submit-button').removeClass('btn-secondary').addClass('btn-primary').removeAttr('disabled');
            } else {
                $('#play-submit-button').removeClass('btn-primary').addClass('btn-secondary').attr('disabled', 'disabled');
            }
        }
    );

    $('#play-submit-button').click(
        function() {
            if (selectedPlay === null) {
                return;
            }

            fetch(
                $('#submit-winner-url').val(),
                {
                    method: 'POST',
                    body: JSON.stringify(
                        {
                            play: selectedPlay
                        }
                    ),
                    headers: {
                        "Content-type": "application/json; charset=UTF-8"
                    }
                }
            )
        }
    )

    $('#recap-ready-button').click(
        function() {
            fetch(
                $('#ready-url').val(),
                {
                    method: "POST",
                }
            ).then(
                function() {
                    $(`#recap-ready-button`).removeClass('btn-primary').addClass('btn-secondary').attr('disabled', 'disabled');
                }
            )
        }
    )
});