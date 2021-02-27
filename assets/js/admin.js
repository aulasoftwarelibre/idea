import flatpickr from "flatpickr";
import { Spanish } from "flatpickr/dist/l10n/es.js"

$(document).ready(function () {
    //flatpickr("[type=datetime-local]");
    $("[type=datetime-local]").flatpickr({
        enableTime: true,
        time_24hr: true,
        locale: Spanish
    });
});

require('../css/admin.css');
require('flatpickr/dist/flatpickr.css')

