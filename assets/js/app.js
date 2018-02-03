global.$ = global.jQuery = require('jquery');

require('semantic-ui-css');
import Clipboard from 'clipboard';

$(document).ready(function () {
    $('.ui.sticky')
        .sticky({
            context: '#main'
        })
    ;

    $("a.close.idea").click(function (action) {
        var slug = $(this).data('slug');
        $.post(Routing.generate('idea_close', {slug: slug}), {})
            .done(function () {
                location.reload();
            });
    });
    $("a.open.idea").click(function (action) {
        var slug = $(this).data('slug');
        $.post(Routing.generate('idea_open', {slug: slug}), {})
            .done(function () {
                location.reload();
            });
    });
    $("a.approve.idea").click(function (action) {
        var slug = $(this).data('slug');
        $.post(Routing.generate('idea_approve', {slug: slug}), {})
            .done(function () {
                location.reload();
            });
    });
    $("a.reject.idea").click(function (action) {
        var slug = $(this).data('slug');
        $.post(Routing.generate('idea_reject', {slug: slug}), {})
            .done(function () {
                location.reload();
            });
    });
    $("a.join.idea").click(function (action) {
        var slug = $(this).data('slug');
        $.post(Routing.generate('idea_join', {slug: slug}), {})
            .done(function () {
                location.reload();
            });
    });
    $("a.leave.idea").click(function (action) {
        var slug = $(this).data('slug');
        $.post(Routing.generate('idea_leave', {slug: slug}), {})
            .done(function () {
                location.reload();
            });
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
    // fix menu when passed
    $('.masthead')
        .visibility({
            once: false,
            onBottomPassed: function() {
                $('.fixed.menu').transition('fade in');
            },
            onBottomPassedReverse: function() {
                $('.fixed.menu').transition('fade out');
            }
        })
    ;

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
