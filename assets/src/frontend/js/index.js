jQuery( document ).ready( function($) {
    jQuery( "body" ).on( 'click', 'div#view_list', function(e) {
        e.preventDefault();

        var target = $(this);

        var key_id = target.find( "span" ).attr( 'current-media-view' );

        var url = view_analytics_object.attachment_view_endpoint + key_id + '/';

        jQuery.ajax({
            type : "GET",
            url : url,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', view_analytics_object.nonce );
            },
            success: function(response) {

                var html = '';

                if ( $.isArray( response ) ) {
                    var media_view_user = wp.template( 'view-analytics-media-view-user' );
                    $.each( response, function( key, data ) {
                        html += media_view_user( data )
                    });
                }

                var message_modal   = $( '#view-analytics-view-confirmation-modal' );
                message_modal.find('.bb-action-popup-content ul').html( html );

                /**
                 * For BuddyPress and if not then BuddyBoss
                 */
                if( $( 'a.view-confirmation-modal-tigger' ).length ) {
		            var message_modal_trigger   = $( 'a.view-confirmation-modal-tigger' );

                    /**
                     * Show popup
                     */
                    message_modal_trigger.trigger( 'click' );
                } else {
                    message_modal.show();
                }
            }
        });
    });
 });