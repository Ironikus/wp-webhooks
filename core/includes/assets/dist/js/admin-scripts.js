(function( $ ) {
    'use strict';

    $( document ).on( "click", ".ironikus-refresh", function() {
        location.reload();
    });

    var accordion = (function(){

        var $accordion = $('.irnks-accordion');
        var $accordion_header = $accordion.find('.irnks-accordion-header');
        var $accordion_item = $('.irnks-accordion-item');

        // default settings
        var settings = {
            speed: 400,
            oneOpen: false
        };

        return {
            init: function($settings) {
                $accordion_header.on('click', function() {
                    accordion.toggle($(this));
                });

                $.extend(settings, $settings);

                if(settings.oneOpen && $('.irnks-accordion-item.active').length > 1) {
                    $('.irnks-accordion-item.active:not(:first)').removeClass('active');
                }

                $('.irnks-accordion-item.active').find('> .irnks-accordion-body').show();
            },
            toggle: function($this) {
                $this.closest('.irnks-accordion-item').toggleClass('active');
                $this.next().stop().slideToggle(settings.speed);
            }
        }
    })();

    $(document).ready(function(){
        accordion.init({ speed: 300, oneOpen: true });
    });

    $( ".ironikus-save" ).on( "click", function() {

        var $this = $( this );

        //Prevent from clicking again
        if( $( $this ).children( '.ironikus-loader' ).hasClass( 'active' ) ){
            return;
        }

        $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
        $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

        var webhook_id = $( $this ).attr( 'ironikus-webhook-trigger' );
        var $webhook_callback = $( $this ).attr( 'ironikus-webhook-callback' );
        var webhook_url_val = $( '#ironikus-webhook-url-' + webhook_id ).val();
        var webhook_current_url = $( '#ironikus-webhook-current-url' ).val();

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_add_webhook_trigger',
                webhook_url : webhook_url_val,
                webhook_group : webhook_id,
                webhook_callback : $webhook_callback,
                current_url : webhook_current_url,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                var $webhook = $.parseJSON( $response );

                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

                    if( $webhook['success'] != 'false' ){
                        $( $this ).css( { 'background': '#00a73f' } );

                        var $webhook_html = '<tr id="ironikus-webhook-id-' + $webhook['webhook'] + '"><td>';
                        $webhook_html += '<input class="ironikus-webhook-input" type="text" name="ironikus_wp_webhooks_pro_webhook_url" value="' + $webhook['webhook_url'] + '" readonly /><br>';
                        $webhook_html += '</td><td><div class="ironikus-element-actions">';
                        $webhook_html += '<span class="ironikus-delete" ironikus-delete="' + $webhook['webhook'] + '" ironikus-group="' + $webhook['webhook_group'] + '" >Delete</span><br>';
                        $webhook_html += '<span class="ironikus-refresh">Refresh for Settings</span>';

                        if( $webhook['webhook_callback'] != '' ){
                            $webhook_html += '<br><span class="ironikus-send-demo" ironikus-demo-data-callback="' + $webhook['webhook_callback'] + '" ironikus-webhook="' + $webhook['webhook'] + '" ironikus-group="' + $webhook['webhook_group'] + '" >Send demo</span>';
                        }

                        $webhook_html += '</div></td></tr>';

                        $( '.ironikus-webhook-table.ironikus-group-' + webhook_id + ' > tbody' ).append( $webhook_html );
                    } else {
                        $( $this ).css( { 'background': '#a70000' } );
                    }

                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '' } );
                }, 2700);
            },
            error: function( errorThrown ){
                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );
                    $( $this ).css( { 'background': '#a70000' } );
                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '' } );
                }, 2700);
            }
        } );

    });

    $( document ).on( "click", ".ironikus-delete", function() {

        if (confirm("Are you sure you want to delete this webhook?")){

            var $this = this;
            var $webhook = $( $this ).attr( 'ironikus-delete' );
            var $webhook_group = $( $this ).attr( 'ironikus-group' );

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_remove_webhook_trigger',
                    webhook : $webhook,
                    webhook_group : $webhook_group,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $webhook_response = $.parseJSON( $response );

                    if( $webhook_response['success'] != 'false' ){
                        $( '#ironikus-webhook-id-' + $webhook ).remove();
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });

        }

    });

    $( document ).on( "click", ".ironikus-send-demo", function() {
        var $this = this;
        var $webhook = $( $this ).attr( 'ironikus-webhook' );
        var $webhook_group = $( $this ).attr( 'ironikus-group' );
        var $webhook_callback = $( $this ).attr( 'ironikus-demo-data-callback' );

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_test_webhook_trigger',
                webhook : $webhook,
                webhook_group : $webhook_group,
                webhook_callback : $webhook_callback,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                var $webhook_response = $.parseJSON( $response );

                setTimeout(function(){

                    if( $webhook_response['success'] != 'false' ){
                        $( $this ).css( { 'color': '#00a73f' } );
                    } else {
                        $( $this ).css( { 'color': '#a70000' } );
                    }

                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'color': '' } );
                }, 2700);
            },
            error: function( errorThrown ){
                console.log(errorThrown);
            }
        } );
    });

    //New TB logic for trigger settings
    $( document ).on( "click", "#TB_ajaxContent .ironikus-submit-settings-form", function(e) {
        e.preventDefault();

        var $this = this;
        var $webhook = $( $this ).attr( 'webhook-id' );
        var $webhook_group = $( $this ).attr( 'webhook-group' );
        var $datastring = $("#ironikus-webhook-form-"+$webhook).serialize();

        //Prevent from clicking again
        if( $( $this ).children( '.ironikus-loader' ).hasClass( 'active' ) ){
            return;
        }

        $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
        $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

        $.ajax({
            url : ironikus.ajax_url,
            type : 'post',
            data : {
                action : 'ironikus_save_webhook_trigger_settings',
                webhook_id : $webhook,
                webhook_group : $webhook_group,
                trigger_settings : $datastring,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                //var $webhook_response = $.parseJSON( $response );

                //if( $webhook_response['success'] != 'false' ){
                //    $( '#ironikus-webhook-id-' + $webhook ).remove();
                //}

                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

                    $( $this ).css( { 'background': '#00a73f' } );
                }, 200);
                setTimeout(function(){
                    $( $this ).css( { 'background': '' } );
                }, 2700);
                console.log($response);
            },
            error: function( errorThrown ){
                console.log(errorThrown);
            }
        });

    });

})( jQuery );
