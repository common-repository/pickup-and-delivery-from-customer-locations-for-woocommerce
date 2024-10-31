(function($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    $(document).ready(function() {



        $("body").on("click", ".pdfclw_premium_close", function() {
            $(this).parent().hide();
            return false;
        });
        $("body").on("click", ".pdfclw_star_button", function() {
            if ($(this).next().is(":visible")) {
                $(this).next().hide();
            } else {
                $(".pdfclw_premium_feature_note").hide();
                $(this).next().show();
            }
            return false;
        });
    });

})(jQuery);