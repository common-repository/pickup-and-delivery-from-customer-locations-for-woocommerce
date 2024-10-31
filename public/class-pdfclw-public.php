<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://powerfulwp.com
 * @since      1.0.0
 *
 * @package    Pdfclw
 * @subpackage Pdfclw/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Pdfclw
 * @subpackage Pdfclw/public
 * @author     powerfulwp <apowerfulwp@gmail.com>
 */
class Pdfclw_Public {
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name       The name of the plugin.
     * @param      string $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Pdfclw_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Pdfclw_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if ( is_checkout() ) {
            wp_enqueue_style(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'css/pdfclw-public.css',
                array(),
                $this->version,
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Pdfclw_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Pdfclw_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if ( is_checkout() ) {
            wp_enqueue_script(
                $this->plugin_name . '-country',
                plugin_dir_url( __FILE__ ) . 'js/pdfclw-public-country-select.js',
                array('jquery'),
                $this->version,
                true
            );
            wp_enqueue_script(
                $this->plugin_name . '-address',
                plugin_dir_url( __FILE__ ) . 'js/pdfclw-public-address-i18n.js',
                array('jquery'),
                $this->version,
                true
            );
        }
    }

    /**
     * Checkout pickup form.
     *
     * @param object $checkout checkout.
     * @return void
     */
    public function checkout_pickup_form( $checkout ) {
        if ( pdfclw_cart_pickup_enable() ) {
            $checkout = new WC_Checkout();
            $pdfclw_different_address = get_option( 'pdfclw_different_address' );
            $different_address_allow = ( '1' === $pdfclw_different_address || false === $pdfclw_different_address ? true : false );
            ?>
				</div>
				<div class="pickup_address woocommerce-Address-title">

				

				<?php 
            // Show pickup option checkbox.
            if ( '1' !== get_option( 'pdfclw_pickup_mandatory', '' ) ) {
                ?>
							<h3 id="pickup-from-different-address">
								<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
									<input type="hidden" name="pickup_location_exist" id="pickup_location_exist" value="0">
									<input id="pickup-from-option-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"  type="checkbox" name="pickup-from-option-checkbox" value="1" />
									<span><?php 
                esc_html_e( 'Do you need pickup?', 'pdfclw' );
                ?></span>
								</label>
								<?php 
                if ( $different_address_allow ) {
                    ?>
								<div style="display:none" id="pickup-from-different-address-checkbox_wrap">
									<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
										<input id="pickup-from-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"  type="checkbox" name="pickup_from_different_address" value="1" />
										<span><?php 
                    esc_html_e( 'PickUp from a different address?', 'pdfclw' );
                    ?></span>
									</label>
								</div>
								<?php 
                }
                ?>
							</h3>
						<?php 
            } else {
                ?>
							<input type="hidden" name="pickup_location_exist" value="1">
							<?php 
                if ( $different_address_allow ) {
                    ?>
							<h3 id="pickup-from-different-address">
								<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
									<input id="pickup-from-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"  type="checkbox" name="pickup_from_different_address" value="1" />
									<span><?php 
                    esc_html_e( 'PickUp from a different address?', 'pdfclw' );
                    ?></span>
								</label>
							</h3>
							<?php 
                }
                ?>
						<?php 
            }
            if ( $different_address_allow ) {
                ?>
				<div id="pdfclw-pickup-form">
					<?php 
                do_action( 'pdfclw_before_checkout_pickup_form', $checkout );
                ?>

				<div class="pickup-fields-wrapper" id="pickup-fields-wrapper"  >
					<?php 
                // Fields are based on billing/shipping country. Grab those values but ensure they are valid for the store before using.
                $pickup_country = $checkout->get_value( 'pickup_country' );
                $pickup_country = ( empty( $pickup_country ) ? $checkout->get_value( 'billing_country' ) : $pickup_country );
                $pickup_country = ( empty( $pickup_country ) ? WC()->countries->get_base_country() : $pickup_country );
                $allowed_countries = WC()->countries->get_allowed_countries();
                if ( !array_key_exists( $pickup_country, $allowed_countries ) ) {
                    $pickup_country = current( array_keys( $allowed_countries ) );
                }
                $form_fields = WC()->countries->get_address_fields( $pickup_country, 'pickup_' );
                foreach ( $form_fields as $field_type => $fields ) {
                    // Add accessibility labels to fields that have placeholders.
                    foreach ( $fields as $single_field_type => $field ) {
                        if ( empty( $field['label'] ) && !empty( $field['placeholder'] ) ) {
                            $form_fields[$field_type][$single_field_type]['label'] = $field['placeholder'];
                            $form_fields[$field_type][$single_field_type]['label_class'] = array('screen-reader-text');
                        }
                    }
                }
                foreach ( $form_fields as $key => $field ) {
                    if ( 'pickup_country' === $key ) {
                        $value = $pickup_country;
                    } else {
                        $value = $checkout->get_value( $key );
                    }
                    woocommerce_form_field( $key, $field, $value );
                }
                ?>

				 

			</div>
					<?php 
                do_action( 'pdfclw_after_checkout_pickup_form', $checkout );
                ?>
			</div>
					<?php 
            }
        }
    }

    /**
     * Checkout pickup validation.
     *
     * @param object $data data.
     * @param object $errors erorrs.
     * @return void
     */
    public function pickup_checkout_validation( $data, $errors ) {
        // Check if pickup location exist in the checkout.
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if ( !empty( $_POST['pickup_location_exist'] ) ) {
            if ( '1' === sanitize_text_field( wp_unslash( $_POST['pickup_location_exist'] ) ) ) {
                // Check if pickup location is diffrent from the billing address.
                if ( !empty( $_POST['pickup_from_different_address'] ) ) {
                    if ( '1' === sanitize_text_field( wp_unslash( $_POST['pickup_from_different_address'] ) ) ) {
                        $pickup_first_name = ( isset( $_POST['pickup_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_first_name'] ) ) : '' );
                        $pickup_last_name = ( isset( $_POST['pickup_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_last_name'] ) ) : '' );
                        $pickup_country = ( isset( $_POST['pickup_country'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_country'] ) ) : '' );
                        $pickup_address_1 = ( isset( $_POST['pickup_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_address_1'] ) ) : '' );
                        $pickup_city = ( isset( $_POST['pickup_city'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_city'] ) ) : '' );
                        $pickup_state = ( isset( $_POST['pickup_state'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_state'] ) ) : '' );
                        $pickup_postcode = ( isset( $_POST['pickup_postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_postcode'] ) ) : '' );
                        if ( '' === $pickup_first_name ) {
                            wc_add_notice( sprintf( __( '%s is a required field.', 'pdfclw' ), '<strong>' . __( 'Pickup First name', 'pdfclw' ) . '</strong>' ), 'error' );
                        }
                        if ( '' === $pickup_last_name ) {
                            wc_add_notice( sprintf( __( '%s is a required field.', 'pdfclw' ), '<strong>' . __( 'Pickup Last name', 'pdfclw' ) . '</strong>' ), 'error' );
                        }
                        if ( '' === $pickup_country ) {
                            wc_add_notice( sprintf( __( '%s is a required field.', 'pdfclw' ), '<strong>' . __( 'Pickup Country', 'pdfclw' ) . '</strong>' ), 'error' );
                        }
                        if ( '' !== $pickup_country && !WC()->countries->country_exists( $pickup_country ) ) {
                            wc_add_notice( sprintf( __( '%s is not a valid country code.', 'pdfclw' ), '<strong>' . __( 'Pickup Country', 'pdfclw' ) . '</strong>' ), 'error' );
                        }
                        if ( '' === $pickup_address_1 ) {
                            wc_add_notice( sprintf( __( '%s is a required field.', 'pdfclw' ), '<strong>' . __( 'Pickup Street address', 'pdfclw' ) . '</strong>' ), 'error' );
                        }
                        if ( '' === $pickup_city ) {
                            wc_add_notice( sprintf( __( '%s is a required field.', 'pdfclw' ), '<strong>' . __( 'Pickup City', 'pdfclw' ) . '</strong>' ), 'error' );
                        }
                        if ( '' === $pickup_postcode ) {
                            wc_add_notice( sprintf( __( '%s is a required field.', 'pdfclw' ), '<strong>' . __( 'Pickup Zip', 'pdfclw' ) . '</strong>' ), 'error' );
                        }
                        if ( '' !== $pickup_postcode ) {
                            $pickup_postcode = wc_format_postcode( $pickup_postcode, $pickup_country );
                            if ( !WC_Validation::is_postcode( $pickup_postcode, $pickup_country ) ) {
                                switch ( $pickup_country ) {
                                    case 'IE':
                                        /* translators: %1$s: field name, %2$s finder.eircode.ie URL */
                                        $postcode_validation_notice = sprintf( __( '%1$s is not valid. You can look up the correct Eircode <a target="_blank" href="%2$s">here</a>.', 'pdfclw' ), '<strong>' . __( 'Pickup Zip', 'pdfclw' ) . '</strong>', 'https://finder.eircode.ie' );
                                        break;
                                    default:
                                        /* translators: %s: field name */
                                        $postcode_validation_notice = sprintf( __( '%s is not a valid postcode / ZIP.', 'pdfclw' ), '<strong>' . __( 'Pickup Zip', 'pdfclw' ) . '</strong>' );
                                }
                                wc_add_notice( $postcode_validation_notice, 'error' );
                            }
                        }
                        if ( '' !== $pickup_state && '' !== $pickup_country ) {
                            $valid_states = WC()->countries->get_states( $pickup_country );
                            if ( !empty( $valid_states ) && is_array( $valid_states ) && count( $valid_states ) > 0 ) {
                                $valid_state_values = array_map( 'wc_strtoupper', array_flip( array_map( 'wc_strtoupper', $valid_states ) ) );
                                $pickup_state = wc_strtoupper( $pickup_state );
                                if ( isset( $valid_state_values[$pickup_state] ) ) {
                                    // With this part we consider state value to be valid as well, convert it to the state key for the valid_states check below.
                                    $pickup_state = $valid_state_values[$pickup_state];
                                }
                                if ( !in_array( $pickup_state, $valid_state_values, true ) ) {
                                    /* translators: 1: state field 2: valid states */
                                    wc_add_notice( sprintf( __( '%1$s is not valid. Please enter one of the following: %2$s', 'pdfclw' ), '<strong>' . __( 'Pickup State', 'pdfclw' ) . '</strong>', implode( ', ', $valid_states ) ), 'error' );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Checkout function.
     *
     * @param number $order_id order number.
     * @return void
     */
    public function update_checkout_fields( $order_id ) {
        // Check if pickup location exist in the checkout.
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if ( !empty( $_POST['pickup_location_exist'] ) ) {
            if ( '1' === sanitize_text_field( wp_unslash( $_POST['pickup_location_exist'] ) ) ) {
                $order = wc_get_order( $order_id );
                $pickup_company = ( isset( $_POST['billing_company'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_company'] ) ) : '' );
                $pickup_first_name = ( isset( $_POST['billing_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_first_name'] ) ) : '' );
                $pickup_last_name = ( isset( $_POST['billing_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_last_name'] ) ) : '' );
                $pickup_country = ( isset( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : '' );
                $pickup_address_1 = ( isset( $_POST['billing_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_1'] ) ) : '' );
                $pickup_address_2 = ( isset( $_POST['billing_address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_2'] ) ) : '' );
                $pickup_city = ( isset( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '' );
                $pickup_state = ( isset( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '' );
                $pickup_postcode = ( isset( $_POST['billing_postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_postcode'] ) ) : '' );
                // Check if pickup location is diffrent from the billing address.
                if ( !empty( $_POST['pickup_from_different_address'] ) ) {
                    if ( '1' === sanitize_text_field( wp_unslash( $_POST['pickup_from_different_address'] ) ) ) {
                        $pickup_company = ( isset( $_POST['pickup_company'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_company'] ) ) : '' );
                        $pickup_first_name = ( isset( $_POST['pickup_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_first_name'] ) ) : '' );
                        $pickup_last_name = ( isset( $_POST['pickup_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_last_name'] ) ) : '' );
                        $pickup_country = ( isset( $_POST['pickup_country'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_country'] ) ) : '' );
                        $pickup_address_1 = ( isset( $_POST['pickup_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_address_1'] ) ) : '' );
                        $pickup_address_2 = ( isset( $_POST['pickup_address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_address_2'] ) ) : '' );
                        $pickup_city = ( isset( $_POST['pickup_city'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_city'] ) ) : '' );
                        $pickup_state = ( isset( $_POST['pickup_state'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_state'] ) ) : '' );
                        $pickup_postcode = ( isset( $_POST['pickup_postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_postcode'] ) ) : '' );
                    }
                }
                // Update order.
                $order->update_meta_data( '_pdfclw_pickup_company', $pickup_company );
                $order->update_meta_data( '_pdfclw_pickup_first_name', $pickup_first_name );
                $order->update_meta_data( '_pdfclw_pickup_last_name', $pickup_last_name );
                $order->update_meta_data( '_pdfclw_pickup_address_1', $pickup_address_1 );
                $order->update_meta_data( '_pdfclw_pickup_address_2', $pickup_address_2 );
                $order->update_meta_data( '_pdfclw_pickup_city', $pickup_city );
                $order->update_meta_data( '_pdfclw_pickup_postcode', $pickup_postcode );
                $order->update_meta_data( '_pdfclw_pickup_country', $pickup_country );
                $order->update_meta_data( '_pdfclw_pickup_state', $pickup_state );
                $order->save();
                $orders = new Pdfclw_Order();
                $order = wc_get_order( $order_id );
                $orders->new_order( $order_id, $order );
            }
        }
    }

    /**
     * Email function.
     *
     * @param object $order order object.
     * @param string $sent_to_admin send email.
     * @param string $plain_text text.
     * @param string $email email.
     * @return void
     */
    public function pickup_on_emails(
        $order,
        $sent_to_admin,
        $plain_text,
        $email
    ) {
        $pickup = new Pdfclw_Order();
        $address = $pickup->order_pickup_loction(
            '',
            'address',
            $order,
            0
        );
        if ( '' !== $address ) {
            echo '<p class="pdfclw_order_pickup"><strong>' . esc_html( __( 'Pickup:', 'pdfclw' ) ) . '</strong><br>' . wp_kses_post( $address );
        }
    }

    /**
     * Thank you function.
     *
     * @param number $order_id order number.
     * @return void
     */
    public function pickup_on_thankyou( $order_id ) {
        $order = wc_get_order( $order_id );
        $pickup = new Pdfclw_Order();
        $address = $pickup->order_pickup_loction(
            '',
            'address',
            $order,
            0
        );
        if ( '' !== $address ) {
            echo '<p class="pdfclw_order_pickup"><strong>' . esc_html( __( 'Pickup:', 'pdfclw' ) ) . '</strong><br>' . wp_kses_post( $address );
        }
    }

    /**
     * Details on view order.
     *
     * @param object $order order object.
     * @param string $sent_to_admin send email.
     * @param string $plain_text text.
     * @param string $email email.
     * @return void
     */
    public function pickup_on_details_after_order_table(
        $order,
        $sent_to_admin = '',
        $plain_text = '',
        $email = ''
    ) {
        if ( is_wc_endpoint_url( 'view-order' ) ) {
            $pickup = new Pdfclw_Order();
            $address = $pickup->order_pickup_loction(
                '',
                'address',
                $order,
                0
            );
            if ( '' !== $address ) {
                echo '<p class="pdfclw_order_pickup"><strong>' . esc_html( __( 'Pickup:', 'pdfclw' ) ) . '</strong><br>' . wp_kses_post( $address );
            }
        }
    }

    /**
     * Get the checkout pickup address filter for drsfwp plugin.
     *
     * @param string $address Current pickup address.
     * @param object $package Checkout package.
     * @param string $posted_data Checkout posted data.
     * @return string
     */
    public function get_checkout_pickup_address( $address, $package, $posted_data ) {
        return $address;
    }

}
