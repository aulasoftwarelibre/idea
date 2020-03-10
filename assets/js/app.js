global.$ = global.jQuery = require('jquery');

require('../css/app.css');
require('fomantic-ui-css/semantic.css')
require('../css/reponsive-semantic-ui.css');
require('fomantic-ui-css/semantic');

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
});
