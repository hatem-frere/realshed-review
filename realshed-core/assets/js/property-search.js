/**
 * property-search.js
 * Handles frontend search form interactions:
 *   - Price range slider (jQuery UI)
 *   - Area range slider (jQuery UI)
 *
 * Both sliders are scoped with $(this).closest() so they work
 * independently when multiple forms exist on the same page
 * (one per status tab: For Sale, For Rent, etc.)
 *
 * Path: wp-content/plugins/realshed-core/assets/js/property-search.js
 */
(function ($) {
    "use strict";

    $(document).ready(function () {

        // =========================================================
        // PRICE RANGE SLIDER
        // Target:   .price-range-slider    (one per status tab)
        // Display:  .property-amount       (cosmetic, no name attr)
        // Hidden:   .min-price-hidden      (URL param: s_price_min)
        //           .max-price-hidden      (URL param: s_price_max)
        //
        // Scoping:  $(this).closest('.price-range') ensures each
        //           slider only updates its own tab's fields.
        //           Without this, all tabs update simultaneously.
        // =========================================================
        if ( $(".price-range-slider").length ) {
            $(".price-range-slider").each(function () {

                var $priceContainer = $(this).closest(".price-range");

                // Read persisted values from hidden inputs (set by PHP on page load after a search)
                var persistedMin = parseInt( $priceContainer.find(".min-price-hidden").val() ) || 0;
                var persistedMax = parseInt( $priceContainer.find(".max-price-hidden").val() ) || 1000000;

                $(this).slider({
                    range:  true,
                    min:    0,
                    max:    1000000,
                    values: [ persistedMin, persistedMax ],
                    slide: function ( event, ui ) {
                        var $c = $(this).closest(".price-range");
                        $c.find(".property-amount").val( "$" + ui.values[0].toLocaleString() + " - $" + ui.values[1].toLocaleString() );
                        $c.find(".min-price-hidden").val( ui.values[0] );
                        $c.find(".max-price-hidden").val( ui.values[1] );
                    }
                });

                // Set display label to match persisted values on page load
                $priceContainer.find(".property-amount").val(
                    "$" + persistedMin.toLocaleString() + " - $" + persistedMax.toLocaleString()
                );
            });
        }

        // =========================================================
        // AREA RANGE SLIDER
        // Target:   .area-range-slider     (one per status tab)
        // Display:  .area-amount           (cosmetic, no name attr)
        // Hidden:   .min-area-hidden       (URL param: s_area_min)
        //           .max-area-hidden       (URL param: s_area_max)
        //
        // Handler:  _property_area_value meta, compare BETWEEN
        //           in class-property-search-handler.php
        // =========================================================
        if ( $(".area-range-slider").length ) {
            $(".area-range-slider").each(function () {

                var $areaContainer = $(this).closest(".area-range");

                var persistedMin = parseInt( $areaContainer.find(".min-area-hidden").val() ) || 0;
                var persistedMax = parseInt( $areaContainer.find(".max-area-hidden").val() ) || 7000;

                $(this).slider({
                    range:  true,
                    min:    0,
                    max:    7000,
                    values: [ persistedMin, persistedMax ],
                    slide: function ( event, ui ) {
                        var $c = $(this).closest(".area-range");
                        $c.find(".area-amount").val( ui.values[0].toLocaleString() + " - " + ui.values[1].toLocaleString() + " sq ft" );
                        $c.find(".min-area-hidden").val( ui.values[0] );
                        $c.find(".max-area-hidden").val( ui.values[1] );
                    }
                });

                $areaContainer.find(".area-amount").val(
                    persistedMin.toLocaleString() + " - " + persistedMax.toLocaleString() + " sq ft"
                );
            });
        }

        // NOTE: The tab-switching block that previously existed here has been removed.
        // It referenced #hero-property-status (does not exist) and data-status (not on buttons).
        // Tab switching is already handled by the tabs-box logic in script.js.

    });

})(jQuery);
