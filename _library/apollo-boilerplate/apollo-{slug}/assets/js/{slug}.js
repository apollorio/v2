/**
 * Apollo {Name} - Main JavaScript
 *
 * @package Apollo\{Namespace}
 */

(function($) {
    'use strict';

    // Configuration from WordPress
    const CONFIG = window.apollo{Namespace}Config || {
        ajaxUrl: '/wp-admin/admin-ajax.php',
        restUrl: '/wp-json/apollo/v1/',
        nonce: ''
    };

    // Initialize when DOM ready
    $(document).ready(function() {
        init();
    });

    /**
     * Initialize plugin JavaScript
     */
    function init() {
        // Add your initialization code here
    }

})(jQuery);
