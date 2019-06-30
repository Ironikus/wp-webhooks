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

    $( ".ironikus-action-save" ).on( "click", function() {

        var $this = $( this );
        var $webhook_slug = $( '#ironikus-webhook-action-name' ).val();

        if( ! $webhook_slug ){
            return;
        }

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
                action : 'ironikus_add_webhook_action',
                webhook_slug : $webhook_slug,
                ironikus_nonce: ironikus.ajax_nonce
            },
            success : function( $response ) {
                var $webhook = $.parseJSON( $response );

                console.log($webhook);

                setTimeout(function(){
                    $( $this ).children( '.ironikus-save-text' ).toggleClass( 'active' );
                    $( $this ).children( '.ironikus-loader' ).toggleClass( 'active' );

                    if( $webhook['success'] != 'false' && $webhook['success'] != false ){
                        $( $this ).css( { 'background': '#00a73f' } );

                        var $webhook_html = '<tr id="webhook-action-' + $webhook['webhook'] + '"><td>' + $webhook['webhook'] + '</td>';
                        $webhook_html += '<td>';
                        $webhook_html += '<input class="ironikus-webhook-input" type="text" name="ironikus_wp_webhooks_pro_webhook_url" value="' + $webhook['webhook_url'] + '" readonly /><br>';
                        $webhook_html += '</td>';
                        $webhook_html += '<td>';
                        $webhook_html += '<div class="ironikus-element-actions">';

                        $webhook_html += '<p class="ironikus-delete-action" ironikus-webhook-slug="' + $webhook['webhook'] + '">' + $webhook['webhook_action_delete_name'] + '</p>';
                        $webhook_html += '<span class="ironikus-refresh">Refresh for Settings</span>';

                        $webhook_html += '</div>';
                        $webhook_html += '</td>';
                        $webhook_html += '</tr>';

                        $( '.ironikus-webhook-table.ironikus-webhook-action-table > tbody' ).append( $webhook_html );
                        $( '#ironikus-webhook-action-name' ).val( '' );
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

    $( document ).on( "click", ".ironikus-delete-action", function() {

        if (confirm("Are you sure you want to delete this webhook?")){

            var $this = this;
            var $webhook = $( $this ).attr( 'ironikus-webhook-slug' );

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_remove_webhook_action',
                    webhook : $webhook,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $webhook_response = $.parseJSON( $response );

                    console.log( $response );

                    if( $webhook_response['success'] != 'false' ){
                        $( '#webhook-action-' + $webhook ).remove();
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

                console.log( $webhook_response );

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
    //New TB logic for trigger settings
    $( document ).on( "click", "#TB_ajaxContent .ironikus-actions-submit-data-mapping-form", function(e) {
        e.preventDefault();

        var $this = this;
        var $webhook = $( $this ).attr( 'webhook-id' );
        var $datastring = $("#ironikus-webhook-action-form-"+$webhook).serialize();

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
                action : 'ironikus_save_webhook_action_settings',
                webhook_id : $webhook,
                action_settings : $datastring,
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

    //Choose template file
    $( document ).on( "change", "#wpwhpro-data-mapping-template-select", function(e) {

        //Prevent from clicking again
        if( $( "#wpwhpro-data-mapper-template-loader-img" ).hasClass( 'active' ) ){
            return;
        }

        $( "#wpwhpro-data-mapper-template-loader-img" ).addClass( 'active' );
        
        var $this = this;
        var $data_mapping_id = $( $this ).val();
        var $wrapper_html = '';

        if( $data_mapping_id && $data_mapping_id !== 'empty' ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_load_data_mapping_data',
                    data_mapping_id : $data_mapping_id,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $mapping_response = $.parseJSON( $response );
                    var $mapping_html = '';
                    console.log($mapping_response);

                    $( "#wpwhpro-data-mapper-template-loader-img" ).removeClass( 'active' );

                    //Add logic for delete and save button
                    $( "#wpwhpro-delete-template-button" ).addClass( 'active' ).attr( 'wpwhpro-mapping-id', $data_mapping_id );
                    $( "#wpwhpro-save-template-button" ).addClass( 'active' ).attr( 'wpwhpro-mapping-id', $data_mapping_id );
    
                    if( $mapping_response['success'] === 'true' || $mapping_response['success'] === true ){
                        
                        $mapping_html = create_data_mapping_table( $mapping_response['data']['template'], $mapping_response );

                        $( '#wpwhpro-data-mapping-wrapper' ).html( $mapping_html );
                        reload_sortable();
                    }
                },
                error: function( errorThrown ){
                    $( "#wpwhpro-data-mapper-template-loader-img" ).removeClass( 'active' );
                    console.log(errorThrown);
                }
            });
        } else {
            $( "#wpwhpro-data-mapper-template-loader-img" ).removeClass( 'active' );
            $( "#wpwhpro-delete-template-button" ).removeClass( 'active' );
            $( "#wpwhpro-save-template-button" ).removeClass( 'active' );

            $wrapper_html += '<div class="wpwhpro-empty">';
            $wrapper_html += 'Please choose a template first.';
            $wrapper_html += '</div>';

            $( '#wpwhpro-data-mapping-wrapper' ).html( $wrapper_html );
        }

    });

    function create_data_mapping_table( $data, $args ){

        var $html_table = '';
        var $html_action = '';
        var $json_obj = $.parseJSON( $data );

        $html_table += '<div id="wpwhpro-data-editor">';

        if ( ! $.isEmptyObject( $json_obj ) ) {

            $.each( $json_obj, function( index, value ) {
                $html_table += get_table_single_row_layout( value );
            });

        } else {
            $html_table += '<div class="wpwhpro-empty">';
            $html_table += $args['text']['add_first_row_text'];
            $html_table += '</div>';
        }

        $html_table += '</div>';

        $html_action += '<div class="wpwhpro-data-mapping-actions">';
        $html_action += '<div class="wpwhpro-add-row-button-text wpwhpro-button btn-blue">' + $args['text']['add_button_text'] + '</div>';
        $html_action += '';
        $html_action += '';
        $html_action += '</div>';

        //Map settings
        $html_table += $html_action;

        return $html_table;

    }

    function get_table_single_row_layout( $data ){
        var $html = '';
        var $new_key_placeholder = 'Add new key';
        $html += '<div class="single-data-row">';

        //Add sortable button
        $html += '<div alt="f182" class="data-delete-icon dashicons dashicons-trash"></div>';
        $html += '<div alt="f545" class="data-move-icon dashicons dashicons-move"></div>';

        if( $data === 'empty' ){

            //setup new key
            $html += '<div class="data-new-key-wrapper">';
            $html += '<input class="data-new-key" name="data-new-key" placeholder="' + $new_key_placeholder + '" />';
            $html += '</div>';

            //Setup connector
            $html += '<div class="data-connector">' + get_connector() + '</div>';

            //setup current data keys
            $html += '<ul class="data-income-keys"></ul>';

        } else {

            //setup new key
            $html += '<div class="data-new-key-wrapper">';
            $html += '<input class="data-new-key" name="data-new-key" value="' + $data.new_key + '" placeholder="' + $new_key_placeholder + '" />';
            $html += '</div>';

            //Setup connector
            $html += '<div class="data-connector">' + get_connector() + '</div>';

            //setup current data keys
            $html += '<ul class="data-income-keys">';

            $.each( $data.singles, function( index, value ) {
                $html += get_single_key_html( value );
            });

            $html += '';
            $html += '</ul>';

        }

        //add new data key button @todo - translate text
        $html += '<div class="wpwhpro-add-key-button-text wpwhpro-button btn-blue">' + 'Add Key' + '</div>';

        $html += '</div>';

        return $html;

    }

    function get_connector(){
        return '<?xml version="1.0" encoding="UTF-8"?>' +
        '<!DOCTYPE svg  PUBLIC \'-//W3C//DTD SVG 1.1//EN\'  \'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\'>' + 
        '<svg version="1.1" viewBox="0 0 640 640" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">' +
        '<defs>' +
        '<path id="a" d="m34.98 270.03h428.25c-80.77-80.95-125.65-125.93-134.62-134.92-6.6-6.59-10.21-15.52-10.21-24.88 0-9.37 3.61-18.23 10.21-24.83 2.09-2.1 18.85-18.86 20.94-20.96 6.59-6.59 15.37-10.23 24.73-10.23 9.37 0 18.15 3.62 24.74 10.2 23.07 23.08 207.7 207.69 230.78 230.76 6.61 6.62 10.23 15.43 10.2 24.8 0.03 9.42-3.59 18.23-10.2 24.84-23.08 23.07-207.71 207.7-230.78 230.78-6.59 6.58-15.37 10.2-24.74 10.2-9.36 0-18.14-3.63-24.73-10.2-2.09-2.1-18.85-18.87-20.94-20.96-6.6-6.58-10.21-15.36-10.21-24.73 0-9.36 3.61-17.68 10.21-24.26 9.07-9.05 54.46-54.27 136.14-135.68h-429.25c-19.29 0-35.5-16.63-35.5-35.9v-29.65c0-19.27 16.69-34.6 35.98-34.6-0.14 0.03-0.47 0.1-1 0.22z"/>' +
        '</defs>' +
        '<use fill="#000000" xlink:href="#a"/>' +
        '<use fill-opacity="0" stroke="#000000" stroke-opacity="0" xlink:href="#a"/>' +
        '</svg>';
    }

    function reload_sortable(){
        $( ".data-income-keys" ).sortable({connectWith: ".wpwhpro-single-row", delay: 150});
        $( "#wpwhpro-data-editor" ).sortable({connectWith: ".single-data-row", delay: 150});
    }

    function get_add_new_html(){
        var $html = '';

        $html += '<div class="wpwhpro-data-mapping-add-template-wrapper">';
        $html += '<input id="wpwhpro-data-mapping-add-template-name" type="text" />';
        $html += '<div class="wpwhpro-add-template-button wpwhpro-button btn-blue">' + 'Add Template' + '</div>';
        $html += '';
        $html += '';
        $html += '</div>';

        return $html;
    }

    function reload_wnd(){
        window.location = window.location.href;
        location.reload();
    }

    function get_single_key_html( $value ){
        var $html = '';
        var $map_key_placeholder = 'Add old key';

        $html += '<li class="wpwhpro-single-row">';

        if( $value == 'empty' ){
            $html += '<input class="wpwhpro-single-row-input" name="data-income-key" placeholder="' + $map_key_placeholder + '" />';
        } else {
            $html += '<input class="wpwhpro-single-row-input" name="data-income-key" value="' + $value + '" placeholder="' + $map_key_placeholder + '" />';
        }

        $html += '<div class="wpwhpro-single-row-key-actions">';
        $html += '<div class="wpwhpro-delete-single-row-key"><div alt="f182" class="dashicons dashicons-trash"></div></div>';
        $html += '<div class="wpwhpro-move-single-row-key"><div alt="f545" class="dashicons dashicons-move"></div></div>';
        $html += '</div>';
        $html += '</li>';

        return $html;
    }

    function create_template_json(){
        var $data = $('#wpwhpro-data-editor .data-new-key').map(function() {
            return {
                new_key: $(this).val(),
                singles: $(this).parent().parent().find('.wpwhpro-single-row-input').map(function() {
                    return $(this).val();
                }).get()
            };
        }).get();
        
        return $data;
    }

    $(document).ready(function(){
        var $html = '';

        $html += get_add_new_html();
        $html += '<div id="wpwhpro-save-template-button" class="wpwhpro-button btn-blue">' + 'Save Template' + '</div>';
        $html += '<div id="wpwhpro-delete-template-button" class="wpwhpro-button btn-red">' + 'Delete Template' + '</div>';

        $( '#wpwhpro-data-mapping-actions' ).html( $html );
    });

    // Json editor logic
    $( document ).on( "click", ".wpwhpro-add-row-button-text", function() {
        var $this = this;

        //Clear empty text ares
        if( $("#wpwhpro-data-editor .wpwhpro-empty").length ){
            $( '#wpwhpro-data-editor' ).html( '' );
        }

        var $single_row = get_table_single_row_layout( 'empty' );

        $( '#wpwhpro-data-editor' ).append( $single_row );
        
        reload_sortable();
    });

    // delete single key logic
    $( document ).on( "click", ".wpwhpro-delete-single-row-key", function() {
        var $this = this;
        $( $this ).parent().parent(".wpwhpro-single-row").remove();
    });

    // delete single key logic
    $( document ).on( "click", ".data-delete-icon", function() {
        if (confirm("Are you sure you want to delete this row?")){
            var $this = this;
            $( $this ).parent(".single-data-row").remove();
        }
    });

    // Json editor logic
    $( document ).on( "click", ".wpwhpro-add-key-button-text", function() {
        var $this = this;
        var $html = get_single_key_html( 'empty' );

        $( $this ).prev( ".data-income-keys" ).append( $html );
        
        reload_sortable();
    });

    // Delete Template logic
    $( document ).on( "click", "#wpwhpro-save-template-button", function() {

        var $this = this;
        var $data_mapping_id = $( $this ).attr( 'wpwhpro-mapping-id' );
        var $template_json = create_template_json();

        console.log($data_mapping_id);
        console.log($template_json);

        if( $data_mapping_id ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_save_data_mapping_template',
                    data_mapping_id : $data_mapping_id,
                    data_mapping_json : $template_json,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $saving_response = $.parseJSON( $response );
    
                    if( $saving_response['success'] === 'true' || $saving_response['success'] === true ){
                        
                        setTimeout(function(){
                            $( $this ).css( { 'background': '#00a73f' } );
                        }, 200);
                        setTimeout(function(){
                            $( $this ).css( { 'background': '' } );
                        }, 2700);
                        
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });
        }

    });

    // Delete Template logic
    $( document ).on( "click", "#wpwhpro-delete-template-button", function() {

        var $this = this;
        var $data_mapping_id = $( $this ).attr( 'wpwhpro-mapping-id' );
        var $wrapper_html = '';

        if( $data_mapping_id && $data_mapping_id !== 'empty' && confirm( "Are you sure you want to delete this template?" ) ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_delete_data_mapping_template',
                    data_mapping_id : $data_mapping_id,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $deleting_response = $.parseJSON( $response );
    
                    if( $deleting_response['success'] === 'true' || $deleting_response['success'] === true ){
                        
                        $( "#wpwhpro-delete-template-button" ).removeClass( 'active' );
                        $( "#wpwhpro-save-template-button" ).removeClass( 'active' );
                        $("#wpwhpro-data-mapping-template-select option[value='" + $data_mapping_id + "']").remove();

                        $wrapper_html += '<div class="wpwhpro-empty">';
                        $wrapper_html += 'Please choose a template first.';
                        $wrapper_html += '</div>';

                        $( '#wpwhpro-data-mapping-wrapper' ).html( $wrapper_html );
                        
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });
        }

    });

    // Delete Template logic
    $( document ).on( "click", ".wpwhpro-add-template-button", function() {

        var $this = this;
        var $data_mapping_name = $( "#wpwhpro-data-mapping-add-template-name" ).val();

        if( $data_mapping_name ){

            $.ajax({
                url : ironikus.ajax_url,
                type : 'post',
                data : {
                    action : 'ironikus_add_data_mapping_template',
                    data_mapping_name : $data_mapping_name,
                    ironikus_nonce: ironikus.ajax_nonce
                },
                success : function( $response ) {
                    var $add_response = $.parseJSON( $response );
    
                    if( $add_response['success'] === 'true' || $add_response['success'] === true ){
                        
                        if( confirm( 'Reload required. Want to reload now?' ) ){
                            reload_wnd();
                        }
                        
                    }
                },
                error: function( errorThrown ){
                    console.log(errorThrown);
                }
            });
        }

    });

    $( document ).on({
        mouseover: function () {
            var $this = this;
            $( $this ).find( ".wpwhpro-single-row-key-actions" ).addClass( 'active' );
        },
        mouseleave: function () {
            var $this = this;
            $( $this ).find( ".wpwhpro-single-row-key-actions" ).removeClass( "active" );
        }
    }, ".wpwhpro-single-row");

    //Log logic
     $( document ).on( "click", ".log-element", function() {
        var $this = this;
        var $log_id = $( $this ).attr( 'wpwhpro-log-id' );
        var $log_content = $( "#wpwhpro-log-content-"+$log_id ).html();
        var $log_json = $( "#wpwhpro-log-json-"+$log_id ).text();

        $( "#wpwhpro-log-content" ).html( $log_content );
        $('#wpwhpro-log-json').jsonBrowse( $.parseJSON( $log_json ) );

     });

})( jQuery );

/**
 * jQuery json-viewer
 * @author: Kevin Olson <acidjazz@gmail.com>
 */
(function($){

  /**
   * Check if arg is either an array with at least 1 element, or a dict with at least 1 key
   * @return boolean
   */
  function isCollapsable(arg) {
    return arg instanceof Object && Object.keys(arg).length > 0;
  }

  /**
   * Check if a string represents a valid url
   * @return boolean
   */
  function isUrl(string) {
     var regexp = /^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
     return regexp.test(string);
  }

  /**
   * Transform a json object into html representation
   * @return string
   */
  function json2html(json, options) {
    html = '';
    if (typeof json === 'string') {
      // Escape tags
      json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
      if (isUrl(json))
        html += '<a href="' + json + '" class="json-string">' + json + '</a>';
      else
        html += '<span class="json-string">"' + json + '"</span>';
    }
    else if (typeof json === 'number') {
      html += '<span class="json-literal">' + json + '</span>';
    }
    else if (typeof json === 'boolean') {
      html += '<span class="json-literal">' + json + '</span>';
    }
    else if (json === null) {
      html += '<span class="json-literal">null</span>';
    }
    else if (json instanceof Array) {
      if (json.length > 0) {
        html += '[<ol class="json-array">';
        for (var i = 0; i < json.length; ++i) {
          html += '<li>'
          // Add toggle button if item is collapsable
          if (isCollapsable(json[i])) {
            html += '<a href class="json-toggle"></a>';
          }
          html += json2html(json[i], options);
          // Add comma if item is not last
          if (i < json.length - 1) {
            html += ',';
          }
          html += '</li>';
        }
        html += '</ol>]';
      }
      else {
        html += '[]';
      }
    }
    else if (typeof json === 'object') {
      var key_count = Object.keys(json).length;
      if (key_count > 0) {
        html += '{<ul class="json-dict">';
        for (var key in json) {
          if (json.hasOwnProperty(key)) {
            html += '<li>';
            var keyRepr = options.withQuotes ?
              '<span class="json-string">"' + key + '"</span>' : key;
            // Add toggle button if item is collapsable
            if (isCollapsable(json[key])) {
              html += '<a href class="json-toggle">' + keyRepr + '</a>';
            }
            else {
              html += keyRepr;
            }
            html += ': ' + json2html(json[key], options);
            // Add comma if item is not last
            if (--key_count > 0)
              html += ',';
            html += '</li>';
          }
        }
        html += '</ul>}';
      }
      else {
        html += '{}';
      }
    }
    return html;
  }

  /**
   * jQuery plugin method
   * @param json: a javascript object
   * @param options: an optional options hash
   */
  $.fn.jsonBrowse = function(json, options) {
    options = options || {};

    // jQuery chaining
    return this.each(function() {

      // Transform to HTML
      var html = json2html(json, options)
      if (isCollapsable(json))
        html = '<a href class="json-toggle"></a>' + html;

      // Insert HTML in target DOM element
      $(this).html(html);

      // Bind click on toggle buttons
      $(this).off('click');
      $(this).on('click', 'a.json-toggle', function() {
        var target = $(this).toggleClass('collapsed').siblings('ul.json-dict, ol.json-array');
        target.toggle();
        if (target.is(':visible')) {
          target.siblings('.json-placeholder').remove();
        }
        else {
          var count = target.children('li').length;
          var placeholder = count + (count > 1 ? ' items' : ' item');
          target.after('<a href class="json-placeholder">' + placeholder + '</a>');
        }
        return false;
      });

      // Simulate click on toggle button when placeholder is clicked
      $(this).on('click', 'a.json-placeholder', function() {
        $(this).siblings('a.json-toggle').click();
        return false;
      });

      if (options.collapsed == true) {
        // Trigger click to collapse all nodes
        $(this).find('a.json-toggle').click();
      }
    });
  };
})(jQuery);
