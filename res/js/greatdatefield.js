/**
 * Converts Arabic-Indic and Persian digits to 0-9 while typing.
 * Listens on the document, so it also covers forms loaded later by ajax.
 */
(function () {
    'use strict';

    var DIGITS = /[\u0660-\u0669\u06F0-\u06F9]/g;

    function normalize(input) {
        if (!input.matches || !input.matches('input[data-greatdate-part]')) {
            return;
        }

        var value = input.value;
        var converted = value.replace(DIGITS, function (d) {
            return String(d.charCodeAt(0) % 16);
        });
        if (converted === value) {
            return;
        }

        // Each digit is replaced by one character, so the caret position is unchanged
        var focused = document.activeElement === input;
        var start = input.selectionStart;
        var end = input.selectionEnd;
        input.value = converted;
        if (focused) {
            input.setSelectionRange(start, end);
        }
    }

    document.addEventListener('input', function (e) {
        // Leave the text alone while an input method is still composing it
        if (!e.isComposing) {
            normalize(e.target);
        }
    });

    document.addEventListener('compositionend', function (e) {
        normalize(e.target);
    });
})();
