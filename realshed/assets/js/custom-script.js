/**
 * Realshed Custom Frontend Logic
 * Handles Search Tabs, Sliders, UI Sync, Card Height Equalizing, and AJAX Filtering
 */
(function ($) {
    "use strict";

    // ============================================================
    // 1. EXISTING FUNCTIONALITY (search tabs, sliders, drawer)
    // ============================================================
    $(document).ready(function () {

        // SEARCH TABS SYNC (BUY/RENT)
        $(document).on('click', '.tab-btn', function (e) {
            var $this = $(this);
            var statusValue = $this.text().trim().toLowerCase();

            $this.closest('.tab-buttons').find('.tab-btn').removeClass('active-btn');
            $this.addClass('active-btn');

            $('#tab_status_field, input[name="s_status"]').val(statusValue);

            var targetTab = $this.attr('data-tab');
            if (targetTab) {
                var $tabsBox = $this.closest('.tabs-box');
                $tabsBox.find('.tabs-content .tab').fadeOut(0).removeClass('active-tab');
                $(targetTab).fadeIn(300).addClass('active-tab');
            }
        });

        // ADVANCED SEARCH DRAWER (Toggle)
        $(document).on('click', '.search__toggler, .nav-toggler', function (e) {
            e.preventDefault();
            $(this).closest(".search-field").find(".switch_btn_one").addClass("active");
            $(this).closest('.inner-box').find('.more-filter').slideToggle(500);
        });

        $(document).on('click', '.switch_btn_one .close-btn', function () {
            $(this).closest(".switch_btn_one").removeClass("active");
        });

        // PRICE RANGE SLIDER SYNC
        if ($('.price-range-slider').length) {
            $(".price-range-slider").on("slide", function (event, ui) {
                var $container = $(this).closest('.range-slider, .price-filter, .inner-box');
                $container.find(".min-price-hidden").val(ui.values[0]);
                $container.find(".max-price-hidden").val(ui.values[1]);
            });
        }

        // AREA RANGE SLIDER SYNC
        if ($('.area-range-slider').length) {
            $(".area-range-slider").on("slide", function (event, ui) {
                var $container = $(this).closest('.area-range');
                $container.find(".min-area-hidden").val(ui.values[0]);
                $container.find(".max-area-hidden").val(ui.values[1]);
            });
        }

    }); // end document.ready

    // ============================================================
    // 2. HEIGHT EQUALIZER FOR GRID CARDS (WORKING VERSION)
    // ============================================================

    function equalizeCardHeights() {
        $('.deals-grid-content .row').each(function () {
            var maxHeight = 0;
            var $cards = $(this).find('.feature-block-one');
            $cards.css('height', '');
            $cards.each(function () {
                var h = $(this).outerHeight();
                if (h > maxHeight) maxHeight = h;
            });
            if (maxHeight > 0) {
                $cards.css('height', maxHeight + 'px');
            }
        });
    }

    function equalizeInnerBoxHeights() {
        $('.deals-grid-content .row').each(function () {
            var maxHeight = 0;
            var $innerBoxes = $(this).find('.feature-block-one .inner-box');
            $innerBoxes.css('height', '');
            $innerBoxes.each(function () {
                var h = $(this).outerHeight();
                if (h > maxHeight) maxHeight = h;
            });
            if (maxHeight > 0) {
                $innerBoxes.css('height', maxHeight + 'px');
            }
        });
    }

    function alignCardButtons() {
        $('.deals-grid-content .row .feature-block-one .inner-box').css({
            'display': 'flex',
            'flex-direction': 'column'
        });
        $('.deals-grid-content .row .feature-block-one .inner-box .lower-content').css({
            'display': 'flex',
            'flex-direction': 'column',
            'flex': '1'
        });
        $('.deals-grid-content .row .feature-block-one .inner-box .lower-content .other-info-box').css({
            'margin-top': 'auto'
        });
    }

    function runGridEqualizers() {
        equalizeCardHeights();
        equalizeInnerBoxHeights();
        alignCardButtons();
    }

    $(window).on('load', function () {
        runGridEqualizers();
    });
    $(window).on('resize', function () {
        runGridEqualizers();
    });
    $(document).on('click', '.grid-view', function () {
        setTimeout(function () {
            runGridEqualizers();
        }, 100);
    });
    $(document).on('click', '.list-view', function () {
        $('.feature-block-one, .feature-block-one .inner-box').css('height', '');
    });

    // ============================================================
    // 3. AJAX FILTERING FOR PROPERTIES (NO PAGE RELOAD)
    // ============================================================

    function getFilterParams() {
        var params = { realshed_search: 1 };
        var $form = $('.realshed-main-filter');

        // Main form selects and hidden inputs
        $form.find('select, input[type="hidden"]').each(function () {
            var name = $(this).attr('name');
            var val = $(this).val();
            if (name && val !== undefined && val !== '') {
                params[name] = val;
            }
        });

        // Status (from links)
        var $activeStatus = $('.ajax-status-links a.current');
        if ($activeStatus.length && $activeStatus.data('status')) {
            params.s_status = $activeStatus.data('status');
        }

        // Amenities (checkboxes)
        var amenities = [];
        $('.ajax-amenities-list input[type="checkbox"]:checked').each(function () {
            amenities.push($(this).val());
        });
        if (amenities.length) {
            params.s_amenities = amenities;
        }

        // Sort order (from shorting bar)
        var $sortSelect = $('#sort-form select[name="s_sort"]');
        if ($sortSelect.length && $sortSelect.val()) {
            params.s_sort = $sortSelect.val();
        }

        return params;
    }

    function loadProperties(filters) {
        var $container = $('.property-content-side');
        if (!$container.length) return;

        if (typeof realshedAjax === 'undefined' || !realshedAjax.ajaxurl || !realshedAjax.nonce) {
            console.error('Realshed AJAX settings are missing.');
            return;
        }

        $container.addClass('loading').css('opacity', '0.5');
        $.ajax({
            url: realshedAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'realshed_ajax_filter',
                nonce: realshedAjax.nonce,
                filters: filters
            },
            success: function (response) {
                if (response.success && response.data.html) {
                    $container.html(response.data.html);
                    // Re-run height equalizer after new content loads
                    runGridEqualizers();
                    $(window).trigger('resize');
                } else {
                    console.error('AJAX filter error', response);
                }
            },
            complete: function () {
                $container.css('opacity', '').removeClass('loading');
            }
        });
    }

    function debounce(func, wait) {
        var timeout;
        return function () {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                func.apply(context, args);
            }, wait);
        };
    }

    var debouncedFilter = debounce(function () {
        var filters = getFilterParams();
        loadProperties(filters);
    }, 300);

    // Trigger on any change in main form
    $(document).on('change', '.realshed-main-filter select, .realshed-main-filter input[type="hidden"]', function () {
        debouncedFilter();
    });

    // Sliders: when user stops sliding
    $(document).on('mouseup', '.price-range-slider, .area-range-slider', function () {
        debouncedFilter();
    });

    // Status links
    $(document).on('click', '.ajax-status-links a', function (e) {
        e.preventDefault();
        var $this = $(this);
        $('.ajax-status-links a').removeClass('current');
        $this.addClass('current');
        debouncedFilter();
    });

    // Amenities checkboxes
    $(document).on('change', '.ajax-amenities-list input[type="checkbox"]', function () {
        debouncedFilter();
    });

    // Reset button
    $(document).on('click', '.reset-filters-btn', function (e) {
        e.preventDefault();
        $('.realshed-main-filter select').val('');
        $('.realshed-main-filter input[type="hidden"]').val('');
        $('.ajax-status-links a').removeClass('current');
        $('.ajax-amenities-list input[type="checkbox"]').prop('checked', false);
        debouncedFilter();
    });

    // Sort select in shorting bar
    $(document).on('change', '#sort-form select[name="s_sort"]', function () {
        debouncedFilter();
    });

})(jQuery);
