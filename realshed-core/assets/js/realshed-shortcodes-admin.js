
/**
 * Realshed Admin Shortcode Logic
 * Handles the "Shortcuts" button in the Classic Editor
 */


// jQuery(document).ready(function ($) {
//     "use strict";

//     // Debugging: This will pop up an alert as soon as the page loads.
//     // If you don't see this alert, the file is NOT enqueued correctly.
//     console.log("Realshed Admin JS Loaded");

//     $(document).on('click', '#realshed-shortcode-trigger', function (e) {
//         e.preventDefault();
//         e.stopPropagation();

//         console.log("Button clicked!"); // Check your F12 console for this

//         var $dropdown = $('#realshed-shortcode-dropdown');

//         if ($dropdown.length === 0) {
//             alert("Error: Dropdown HTML not found in footer!");
//             return;
//         }

//         // Calculate position relative to button
//         var offset = $(this).offset();
//         $dropdown.css({
//             'top': (offset.top + $(this).outerHeight() + 5) + 'px',
//             'left': offset.left + 'px',
//             'position': 'absolute',
//             'display': ($dropdown.is(':visible') ? 'none' : 'block'),
//             'z-index': '999999'
//         });
//     });

//     // Close when clicking outside
//     $(document).on('click', function (e) {
//         if (!$(e.target).closest('#realshed-shortcode-trigger, #realshed-shortcode-dropdown').length) {
//             $('#realshed-shortcode-dropdown').hide();
//         }
//     });

//     // Insert logic
//     $(document).on('click', '.realshed-insert-shortcode', function (e) {
//         e.preventDefault();
//         var sc = $(this).data('shortcode');
//         if (window.wp && window.wp.media && window.wp.media.editor) {
//             window.wp.media.editor.insert(sc);
//         } else {
//             window.send_to_editor(sc);
//         }
//         $('#realshed-shortcode-dropdown').hide();
//     });
// });

// ==================================================================

jQuery(document).ready(function($) {
    "use strict";

    // Toggle only - CSS handles the "sticky" position now
    $(document).on('click', '#realshed-shortcode-trigger', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#realshed-shortcode-dropdown').toggle();
    });

    // Insert shortcode
    $(document).on('click', '.realshed-insert-shortcode', function(e) {
        e.preventDefault();
        var sc = $(this).data('shortcode');
        if (window.wp && window.wp.media && window.wp.media.editor) {
            window.wp.media.editor.insert(sc);
        } else {
            window.send_to_editor(sc);
        }
        $('#realshed-shortcode-dropdown').hide();
    });

    // Close if user clicks anywhere else
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.realshed-shortcode-container').length) {
            $('#realshed-shortcode-dropdown').hide();
        }
    });
});
