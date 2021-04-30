global.$ = global.jQuery = require('jquery');

require('../css/app.css');
require('fomantic-ui-css/semantic.css')
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
    $('.ui.sidebar')
        .sidebar('attach events', '.toc.item')
    ;
    $('.ui.search')
        .search({
            apiSettings: {
                url: Routing.generate("api_ideas_get") + "?q={query}"
            },
            fields: {
                results : 'items',
                title   : 'title',
                url     : 'url',
            },
            minCharacters : 3,
            ignoreDiacritics: true
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

    $('.ui.form').form();

    $('.dropdown')
        .dropdown({
            on: 'hover'
        })
    ;


    $('#idea_form')
        .form({
            on: 'blur',
            inline: true,
            fields: {
                idea_title: {
                    identifier: 'idea_title',
                    rules: [{
                        type: 'empty',
                        prompt: 'Indica un título'
                    }, {
                        type: 'minLength[10]',
                        prompt: '{name} debe tener al menos {ruleValue} caracteres'
                    }, {
                        type: 'maxLength[255]',
                        prompt: '{name} no debe tener más de {ruleValue} caracteres'
                    }]
                },
                idea_group: {
                    identifier: 'idea_group',
                    rules: [{
                        type: 'empty',
                        prompt: 'Indica un grupo'
                    }]
                }
            }
        })
    ;
});
