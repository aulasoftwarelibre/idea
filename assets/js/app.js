global.$ = global.jQuery = require('jquery');

require('../css/app.css');
require('semantic-ui-css/semantic.css');
require('../css/reponsive-semantic-ui.css');


require('semantic-ui-css');
import Clipboard from 'clipboard';

function actions(button, url) {
    button.addClass('loading');
    $.post(Routing.generate(url, {slug: button.data('slug')}), {})
        .done(function () {
            location.reload();
        })
        .fail(function () {
            button.removeClass('loading animated');
            button.addClass('red disabled');
            button.text('ERROR');
        });
}

$(document).ready(function () {
    $('.ui.sticky')
        .sticky({
            context: '#main'
        })
    ;

    $("a.close.idea").click(function () {
        actions($(this), 'idea_close');
    });
    $("a.open.idea").click(function () {
        actions($(this), 'idea_open');
    });
    $("a.approve.idea").click(function () {
        actions($(this), 'idea_approve');
    });
    $("a.reject.idea").click(function () {
        actions($(this), 'idea_reject');
    });
    $("a.join.idea").click(function () {
        actions($(this), 'idea_join');
    });
    $("a.leave.idea").click(function () {
        actions($(this), 'idea_leave');
    });

    $("#telegram-connect").click(function (action) {
        $('.ui.basic.modal')
            .modal({
                closable: false,
                onVisible: function () {
                    setTimeout(check_telegram_status, 1000)
                }
            })
            .modal('show')
        ;
    });
    function check_telegram_status() {
        $.getJSON(Routing.generate('profile_telegram_status'), function (data) {
            if (!data.active) {
                setTimeout(check_telegram_status, 1000)
                return
            }

            $('#telegram-connect').toggle('hidden');
            $('#telegram-disconnect').toggle('hidden');
            $('#telegram-username').toggle('hidden');
            $('#telegram-username>div>span').text("@" + data.username);
            $('.ui.basic.modal').modal('hide');
        });
    }

    $("#telegram-disconnect").click(function (action) {
        $.post(Routing.generate('profile_telegram_disconnect', {}), {})
            .done(function () {
                location.reload();
            });
    });

    // create sidebar and attach to menu open
    $('.ui.sidebar')
        .sidebar('attach events', '.toc.item')
    ;

    const clipboard = new Clipboard('.clipboard');

    clipboard
        .on('success', function (e) {
            $(e.trigger).attr('data-tooltip', 'Copiado');
            e.clearSelection();
            setTimeout(function () {
                $(e.trigger).attr('data-tooltip', 'Copiar');
            }, 2500)
        })
        .on('error', function (e) {
            $(e.trigger).attr('data-tooltip', 'No funciona en Safari');
            setTimeout(function () {
                $(e.trigger).attr('data-tooltip', 'Copiar');
            }, 2500)
        })

});
