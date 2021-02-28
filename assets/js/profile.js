global.$ = global.jQuery = require('jquery');

$(document).ready(function () {

    $('select')
        .dropdown({fullTextSearch: true})
    ;
    $('#clear-degree').click(function () {
        $('#profile_degree').dropdown('clear')
    })

    $.fn.form.settings.rules.collective = function (value, required) {
        var collective = $('.ui.form').form('get value', 'profile[collective]');

        return collective !== required || value;
    };


    $('#register-profile.ui.form')
        .form({
            on: 'blur',
            inline : true,
            fields: {
                register_alias: {
                    identifier: 'register_alias',
                    rules: [{
                        type: 'empty',
                        prompt: 'Indica un alias'
                    },
                        {
                            type: 'regExp',
                            value: /^[\w\d_]{3,16}$/i,
                            prompt: 'El formato no es correcto'
                        }]
                },
                register_firstname: {
                    identifier: 'register_firstname',
                    rules: [{
                        type: 'empty',
                        prompt: 'Indica tu nombre'
                    }]
                },
                register_lastname: {
                    identifier: 'register_lastname',
                    rules: [{
                        type: 'empty',
                        prompt: 'Indica tus apellidos'
                    }]
                },
                register_collective: {
                    identifier: 'register_collective',
                    rules: [{
                        type: 'empty',
                        prompt: 'Indica tu colectivo'
                    }]
                },
                register_degree: {
                    identifier: 'register_degree',
                    depends: 'register_collective',
                    rules: [{
                        type: 'collective[student]',
                        prompt: 'Si eres alumno indica tus estudios'
                    }]
                },
                register_year: {
                    identifier: 'register_year',
                    depends: 'register_collective',
                    rules: [{
                        type: 'collective[student]',
                        prompt: 'Indica el año de ingreso a tus estudios actuales'
                    }]
                },
                register_terms: {
                    identifier: 'register_terms',
                    rules: [{
                        type: 'checked',
                        prompt: 'Debes aceptar las condiciones para continuar'
                    }]
                }
            }
        })
    ;

    $('#edit-profile.ui.form')
        .form({
            on: 'blur',
            inline: true,
            fields: {
                profile_alias: {
                    identifier: 'profile_alias',
                    rules: [{
                        type: 'empty',
                        prompt: 'Indica un alias'
                    },
                        {
                            type: 'regExp',
                            value: /^[\w\d_]{3,16}$/i,
                            prompt: 'El formato no es correcto'
                        }]
                },
                profile_firstname: {
                    identifier: 'profile_firstname',
                    rules: [{
                        type: 'empty',
                        prompt: 'Indica tu nombre'
                    }]
                },
                profile_lastname: {
                    identifier: 'profile_lastname',
                    rules: [{
                        type: 'empty',
                        prompt: 'Indica tus apellidos'
                    }]
                },
                profile_collective: {
                    identifier: 'profile_collective',
                    rules: [{
                        type: 'empty',
                        prompt: 'Indica tu colectivo'
                    }]
                },
                profile_degree: {
                    identifier: 'profile_degree',
                    depends: 'profile_collective',
                    rules: [{
                        type: 'collective[student]',
                        prompt: 'Si eres alumno indica tus estudios'
                    }]
                },
                profile_year: {
                    identifier: 'profile_year',
                    depends: 'profile_collective',
                    rules: [{
                        type: 'collective[student]',
                        prompt: 'Indica el año de ingreso a tus estudios actuales'
                    }]
                }
            }
        })
    ;

    $('.remove.modal')
        .modal('attach events', '#remove', 'show')
    ;

    $('input[name=iamsure]').on('input', function (e) {
        const button = $('.remove.modal button');
        const message = $(this).val()

        if (message === "Estoy seguro") {
            button.removeClass('disabled');
            return;
        }

        if (button.hasClass('disabled')) {
            return;
        }

        button.addClass('disabled');
    })

});
