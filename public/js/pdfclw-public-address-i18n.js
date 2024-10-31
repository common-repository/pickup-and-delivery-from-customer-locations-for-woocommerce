/*global wc_address_i18n_params */
jQuery(function($) {

    // wc_address_i18n_params is required to continue, ensure the object exists
    if (typeof wc_address_i18n_params === 'undefined') {
        return false;
    }

    var locale_json = wc_address_i18n_params.locale.replace(/&quot;/g, '"'),
        locale = JSON.parse(locale_json);


    function pdfclw_field_is_required(field, is_required) {

        if (is_required) {

            field.find('label .optional').remove();



            field.addClass('validate-required');

            if (field.find('label .required').length === 0) {

                // field.find('label').append('<span class="3required">**************</span>');
                field.find('label').append('<span class="required" title="' + wc_address_i18n_params.i18n_required_text + '">*</span>');
            }
        } else {
            field.find('label .required').remove();
            field.removeClass('validate-required woocommerce-invalid woocommerce-invalid-required-field');

            if (field.find('label .optional').length === 0) {

                field.find('label').append('&nbsp;<span class="optional">(' + wc_address_i18n_params.i18n_optional_text + ')</span>');
            }
        }
    }

    // Handle pickup checkbox
    $(document.body).on("change", '#pickup-from-different-address-checkbox', function() {
        if (this.checked) {
            $("#pdfclw-pickup-form").show();
        } else {
            $("#pdfclw-pickup-form").hide();
        }
    });

    $(document.body).on("change", "#pickup-from-option-checkbox", function() {
        if ($(this).is(":checked")) {
            $("#pickup-from-different-address-checkbox_wrap").show();
            $("#pickup_location_exist").val("1");
        } else {
            $("#pickup-from-different-address-checkbox_wrap").hide();
            $("#pickup-from-different-address-checkbox").prop('checked', false);
            $("#pickup_location_exist").val("0");
        }
    });

    // Handle locale
    $(document.body)
        .on('pdfclw_country_to_state_changed', function(event, country, wrapper) {

            var thisform = wrapper,
                thislocale;

            if (typeof locale[country] !== 'undefined') {
                thislocale = locale[country];
            } else {
                thislocale = locale['default'];
            }

            var $postcodefield = thisform.find('#pickup_postcode_field'),
                $cityfield = thisform.find('#pickup_city_field'),
                $statefield = thisform.find('#pickup_state_field');

            if (!$postcodefield.attr('data-o_class')) {
                $postcodefield.attr('data-o_class', $postcodefield.attr('class'));
                $cityfield.attr('data-o_class', $cityfield.attr('class'));
                $statefield.attr('data-o_class', $statefield.attr('class'));
            }

            var locale_fields = JSON.parse(wc_address_i18n_params.locale_fields);

            $.each(locale_fields, function(key, value) {

                value = value.replace('#billing_', '#pickup_');

                if (value.indexOf('#pickup_') !== -1) {


                    var field = thisform.find(value),
                        fieldLocale = $.extend(true, {}, locale['default'][key], thislocale[key]);

                    // Labels.

                    if (typeof fieldLocale.label !== 'undefined') {
                        field.find('label').html(fieldLocale.label);

                    }

                    // Placeholders.
                    if (typeof fieldLocale.placeholder !== 'undefined') {
                        field.find(':input').attr('placeholder', fieldLocale.placeholder);
                        field.find(':input').attr('data-placeholder', fieldLocale.placeholder);
                        field.find('.select2-selection__placeholder').text(fieldLocale.placeholder);
                    }

                    // Use the i18n label as a placeholder if there is no label element and no i18n placeholder.
                    if (
                        typeof fieldLocale.placeholder === 'undefined' &&
                        typeof fieldLocale.label !== 'undefined' &&
                        !field.find('label').length
                    ) {
                        field.find(':input').attr('placeholder', fieldLocale.label);
                        field.find(':input').attr('data-placeholder', fieldLocale.label);
                        field.find('.select2-selection__placeholder').text(fieldLocale.label);
                    }


                    // Required.
                    if (typeof fieldLocale.required !== 'undefined') {
                        pdfclw_field_is_required(field, fieldLocale.required);
                    } else {
                        pdfclw_field_is_required(field, false);
                    }

                    // Priority.
                    if (typeof fieldLocale.priority !== 'undefined') {
                        field.data('priority', fieldLocale.priority);
                    }

                    // Hidden fields.
                    if ('state' !== key) {
                        if (typeof fieldLocale.hidden !== 'undefined' && true === fieldLocale.hidden) {
                            field.hide().find(':input').val('');
                        } else {
                            field.show();
                        }
                    }

                    // Class changes.
                    if (Array.isArray(fieldLocale.class)) {
                        field.removeClass('form-row-first form-row-last form-row-wide');
                        field.addClass(fieldLocale.class.join(' '));
                    }
                }
            });

            var fieldsets = $('.pickup_address');

            fieldsets.each(function(index, fieldset) {
                var rows = $(fieldset).find('.form-row');
                var wrapper = rows.first().parent();

                // Before sorting, ensure all fields have a priority for bW compatibility.
                var last_priority = 0;

                rows.each(function() {
                    if (!$(this).data('priority')) {
                        $(this).data('priority', last_priority + 1);
                    }
                    last_priority = $(this).data('priority');
                });

                // Sort the fields.
                rows.sort(function(a, b) {
                    var asort = parseInt($(a).data('priority'), 10),
                        bsort = parseInt($(b).data('priority'), 10);

                    if (asort > bsort) {
                        return 1;
                    }
                    if (asort < bsort) {
                        return -1;
                    }
                    return 0;
                });

                rows.detach().appendTo(wrapper);
            });
        })
        .trigger('pdfclw_address_i18n_ready');
});