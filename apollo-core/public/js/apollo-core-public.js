/**
 * Apollo Core Public Scripts
 *
 * @package Apollo_Core
 * @since 6.0.0
 */

(function($) {
    'use strict';

    window.ApolloCore = {
        version: apolloCore.version,

        init: function() {
            console.log('Apollo Core Public v' + this.version + ' loaded');
        },

        api: {
            health: function() {
                return $.ajax({
                    url: apolloCore.restUrl + 'health',
                    method: 'GET',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', apolloCore.nonce);
                    }
                });
            }
        }
    };

    $(document).ready(function() {
        ApolloCore.init();
    });

})(jQuery);
