(function ($) {
    'use strict';

    // Mostrar ou esconder a palavra-passe no login.
    $(document).on('click', '[data-toggle-password]', function () {
        var $input = $($(this).data('toggle-password'));
        var visible = $input.attr('type') === 'text';

        $input.attr('type', visible ? 'password' : 'text');
        $(this).find('i').toggleClass('fa-eye', visible).toggleClass('fa-eye-slash', !visible);
    });

    // Pesquisa por texto e filtro por estado nas listas (data-filter-table aponta para a tabela).
    $('[data-filter-table]').each(function () {
        var $box = $(this);
        var $rows = $($box.data('filter-table')).find('tbody tr');
        var $search = $box.find('[data-filter-search]');
        var $state = $box.find('[data-filter-state]');

        function apply() {
            var term = ($search.val() || '').toLowerCase();
            var state = $state.val() || '';

            $rows.each(function () {
                var $row = $(this);
                var matchesText = $row.text().toLowerCase().indexOf(term) !== -1;
                var matchesState = !state || String($row.data('state')) === state;

                $row.toggle(matchesText && matchesState);
            });
        }

        $search.on('input', apply);
        $state.on('change', apply);
    });
}(jQuery));
