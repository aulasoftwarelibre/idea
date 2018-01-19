global.$ = global.jQuery = require('jquery');

require('semantic-ui-css');

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
});
