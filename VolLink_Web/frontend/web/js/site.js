(function () {
    'use strict';

    document.addEventListener('click', function (event) {
        // Mostrar ou esconder a palavra-passe.
        var toggle = event.target.closest('[data-toggle-password]');
        if (toggle) {
            var input = document.querySelector(toggle.getAttribute('data-toggle-password'));
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
            }
            return;
        }

        // Abrir um diálogo (<dialog>).
        var opener = event.target.closest('[data-dialog-open]');
        if (opener) {
            var dialog = document.getElementById(opener.getAttribute('data-dialog-open'));
            if (dialog && dialog.showModal) {
                dialog.showModal();
            }
            return;
        }

        // Fechar um diálogo pelo botão ou ao clicar fora dele.
        var closer = event.target.closest('[data-dialog-close]');
        if (closer) {
            closer.closest('dialog').close();
            return;
        }

        if (event.target.tagName === 'DIALOG') {
            event.target.close();
        }
    });

    // No registo, mostrar os campos do tipo de conta escolhido.
    document.addEventListener('change', function (event) {
        var radio = event.target;
        if (!radio.matches || !radio.matches('input[type="radio"][name$="[accountType]"]')) {
            return;
        }

        var form = radio.form;
        if (!form) {
            return;
        }

        form.querySelectorAll('[data-account-fields]').forEach(function (group) {
            group.hidden = group.getAttribute('data-account-fields') !== radio.value;
        });
    });
}());
