/*global wc_country_select_params */
jQuery(function($) {

    // wc_country_select_params is required to continue, ensure the object exists
    if (typeof wc_country_select_params === 'undefined') {
        return false;
    }

    // Select2 Enhancement if it exists
    if ($().selectWoo) {


        var pdfclw_getEnhancedSelectFormatString = function() {
            return {
                'language': {
                    errorLoading: function() {
                        // Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
                        return wc_country_select_params.i18n_searching;
                    },
                    inputTooLong: function(args) {
                        var overChars = args.input.length - args.maximum;

                        if (1 === overChars) {
                            return wc_country_select_params.i18n_input_too_long_1;
                        }

                        return wc_country_select_params.i18n_input_too_long_n.replace('%qty%', overChars);
                    },
                    inputTooShort: function(args) {
                        var remainingChars = args.minimum - args.input.length;

                        if (1 === remainingChars) {
                            return wc_country_select_params.i18n_input_too_short_1;
                        }

                        return wc_country_select_params.i18n_input_too_short_n.replace('%qty%', remainingChars);
                    },
                    loadingMore: function() {
                        return wc_country_select_params.i18n_load_more;
                    },
                    maximumSelected: function(args) {
                        if (args.maximum === 1) {
                            return wc_country_select_params.i18n_selection_too_long_1;
                        }

                        return wc_country_select_params.i18n_selection_too_long_n.replace('%qty%', args.maximum);
                    },
                    noResults: function() {
                        return wc_country_select_params.i18n_no_matches;
                    },
                    searching: function() {
                        return wc_country_select_params.i18n_searching;
                    }
                }
            };
        };

        var pdfclw_wc_country_select_select2 = function() {
            $('.pickup_address select.country_select:visible, .pickup_address select.state_select:visible').each(function() {
                var $this = $(this);

                var pdfclw_select2_args = $.extend({
                    placeholder: $this.attr('data-placeholder') || $this.attr('placeholder') || '',
                    label: $this.attr('data-label') || null,
                    width: '100%'
                }, pdfclw_getEnhancedSelectFormatString());

                $(this)
                    .on('select2:select', function() {
                        $(this).trigger('focus'); // Maintain focus after select https://github.com/select2/select2/issues/4384
                    })
                    .selectWoo(pdfclw_select2_args);
            });
        };

        pdfclw_wc_country_select_select2();
        $("#pdfclw-pickup-form").hide();
        $(document.body).on('pdfclw_country_to_state_changed', function() {
            pdfclw_wc_country_select_select2();
        });
    }

    /* State/Country select boxes */
    var pdfclw_states_json = wc_country_select_params.countries.replace(/&quot;/g, '"'),
        pdfclw_states = JSON.parse(pdfclw_states_json),
        pdfclw_wrapper_selectors = '#pdfclw-pickup-form';


    $(document.body).on('change refresh', '.pickup_address select.country_to_state, .pickup_address input.country_to_state', function() {

        // Grab wrapping element to target only stateboxes in same 'group'
        var $pdfclw_wrapper = $(this).closest(pdfclw_wrapper_selectors);

        if (!$pdfclw_wrapper.length) {
            $pdfclw_wrapper = $(this).closest('.form-row').parent();
        }

        var pdfclw_country = $(this).val(),
            $pdfclw_statebox = $pdfclw_wrapper.find('#pickup_state'),
            $parent = $pdfclw_statebox.closest('.form-row'),
            input_name = $pdfclw_statebox.attr('name'),
            input_id = $pdfclw_statebox.attr('id'),
            input_classes = $pdfclw_statebox.attr('data-input-classes'),
            value = $pdfclw_statebox.val(),
            pdfclw_placeholder = $pdfclw_statebox.attr('placeholder') || $pdfclw_statebox.attr('data-placeholder') || '',
            $newstate;

        if (pdfclw_states[pdfclw_country]) {
            if ($.isEmptyObject(pdfclw_states[pdfclw_country])) {

                $newstate = $('<input type="hidden" />')
                    .prop('id', input_id)
                    .prop('name', input_name)
                    .prop('placeholder', pdfclw_placeholder)
                    .attr('data-input-classes', input_classes)
                    .addClass('hidden ' + input_classes);
                $parent.hide().find('.select2-container').remove();
                $pdfclw_statebox.replaceWith($newstate);
                $(document.body).trigger('pdfclw_country_to_state_changed', [pdfclw_country, $pdfclw_wrapper]);
            } else {
                var state = pdfclw_states[pdfclw_country],
                    $defaultOption = $('<option value=""></option>').text(wc_country_select_params.i18n_select_state_text);

                if (!pdfclw_placeholder) {
                    pdfclw_placeholder = wc_country_select_params.i18n_select_state_text;
                }

                $parent.show();

                if ($pdfclw_statebox.is('input')) {
                    $newstate = $('<select></select>')
                        .prop('id', input_id)
                        .prop('name', input_name)
                        .data('placeholder', pdfclw_placeholder)
                        .attr('data-input-classes', input_classes)
                        .addClass('state_select ' + input_classes);
                    $pdfclw_statebox.replaceWith($newstate);
                    $pdfclw_statebox = $pdfclw_wrapper.find('#pickup_state');
                }

                $pdfclw_statebox.empty().append($defaultOption);

                $.each(state, function(index) {
                    var $option = $('<option></option>')
                        .prop('value', index)
                        .text(state[index]);
                    $pdfclw_statebox.append($option);
                });

                $pdfclw_statebox.val(value).trigger('change');

                $(document.body).trigger('pdfclw_country_to_state_changed', [pdfclw_country, $pdfclw_wrapper]);
            }
        } else {
            if ($pdfclw_statebox.is('select, input[type="hidden"]')) {
                $newstate = $('<input type="text" />')
                    .prop('id', input_id)
                    .prop('name', input_name)
                    .prop('placeholder', pdfclw_placeholder)
                    .attr('data-input-classes', input_classes)
                    .addClass('input-text  ' + input_classes);
                $parent.show().find('.select2-container').remove();
                $pdfclw_statebox.replaceWith($newstate);
                $(document.body).trigger('pdfclw_country_to_state_changed', [pdfclw_country, $pdfclw_wrapper]);
            }
        }


    });

    $(document.body).on('pdfclw_address_i18n_ready', function() {
        // Init country selects with their default value once the page loads.
        $(pdfclw_wrapper_selectors).each(function() {
            var $pdfclw_country_input = $(this).find('#pickup_country');

            if (0 === $pdfclw_country_input.length || 0 === $pdfclw_country_input.val().length) {
                return;
            }

            $pdfclw_country_input.trigger('refresh');
        });
    });
});