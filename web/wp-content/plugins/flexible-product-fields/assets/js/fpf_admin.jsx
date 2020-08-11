import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import FPF_Settings_Container from './components/fpf_settings_container.jsx';
import FPF_Fields_Container from './components/fpf_fields_container.jsx';

document.addEventListener('DOMContentLoaded', function () {

    if ( typeof fpf_settings != 'undefined' ) {
        jQuery('form').keydown(function(e) {
            if(e.which == 13) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
        ReactDOM.render(
            < FPF_Settings_Container />,
            document.getElementById('fpf_settings_container')
        );
        ReactDOM.render(
            < FPF_Fields_Container />,
            document.getElementById('fpf_fields_container')
        );
        var fpf_saved = false;

        /**
         * Save Flexible Product Fields fields configuration.
         *
         * @param action_button
         */
        function save_fpf_fields( action_button ) {
            var action_button_id = jQuery(action_button).attr('id');

            jQuery(action_button).parent().find('.spinner').addClass('is-active');
            jQuery(action_button).addClass('disabled');

            fpf_settings.post_title.value = document.getElementById('title').value;
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState === 4) {
                    jQuery(action_button).removeClass('disabled');
                    jQuery(action_button).parent().find('.spinner').removeClass('is-active');
                    var response = JSON.parse(xmlhttp.responseText);
                    if (xmlhttp.status === 200) {
                        if (response.code === 'ok') {
                            fpf_saved = true;
                            document.getElementById(action_button_id).click();
                        } else {
                            alert(response.message);
                        }
                    } else {
                        alert(fpf_admin.save_error + xmlhttp.status);
                    }
                }
            };
            xmlhttp.open('POST', fpf_admin.rest_url + 'flexible_product_fields/v1/fields/' + fpf_settings.post_id.value, true);
            xmlhttp.setRequestHeader('Content-type', 'application/json');
            xmlhttp.setRequestHeader('x-wp-nonce', fpf_admin.rest_nonce);
            xmlhttp.send(JSON.stringify(fpf_settings));
        }

        jQuery('#publish,#save_post').click(function (e) {
            if (fpf_saved) {
                return;
            }
            e.preventDefault();

            jQuery('div.fpf-field-object.closed div.fpf-field-title-row').trigger('click');

            if ( document.getElementById('post').reportValidity() ) {
                save_fpf_fields( this );
            }
        })

    }

});