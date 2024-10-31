<?php

use Automattic\WooCommerce\Utilities\OrderUtil;
/**
 * Function that check if the pickup form is enable on the checkout page.
 *
 * @return statement
 */
function pdfclw_cart_pickup_enable() {
    // Check if pickup setting is enable.
    $pdfclw_pickup_enable = get_option( 'pdfclw_pickup_enable', '' );
    if ( '1' !== $pdfclw_pickup_enable ) {
        return false;
    } else {
        if ( pdfclw_is_free() ) {
            return true;
        }
    }
    return false;
}

/**
 * Get the state.
 *
 * @since 1.5.0
 * @param string $country country code.
 * @param string $state state name/code.
 * @return string
 */
function pdfclw_states(  $country, $state  ) {
    $result = '';
    // Show state for USA.
    if ( 'US' === $country || 'United States (US)' === $country ) {
        $result = $state;
    } elseif ( 'CL' === $country || 'Chile' === $country ) {
        if ( in_array( 'comunas-de-chile-para-woocommerce', PDFCLW_PLUGINS, true ) ) {
            // Get chile states.
            if ( function_exists( 'comunas_de_chile' ) ) {
                $chile_states = comunas_de_chile( array() );
                if ( is_array( $chile_states ) ) {
                    if ( array_key_exists( 'CL', $chile_states ) ) {
                        if ( array_key_exists( $state, $chile_states['CL'] ) ) {
                            $result = $chile_states['CL'][$state];
                        }
                    }
                }
            }
        }
    }
    return $result;
}

/**
 * Store address.
 *
 * @since 1.0.0
 * @param string $format address format.
 * @return string
 */
function pdfclw_store_address(  $format  ) {
    // main store address.
    $store_address = get_option( 'woocommerce_store_address', '' );
    $store_address_2 = get_option( 'woocommerce_store_address_2', '' );
    $store_city = get_option( 'woocommerce_store_city', '' );
    $store_postcode = get_option( 'woocommerce_store_postcode', '' );
    $store_raw_country = get_option( 'woocommerce_default_country', '' );
    $split_country = explode( ':', $store_raw_country );
    if ( false === strpos( $store_raw_country, ':' ) ) {
        $store_country = $split_country[0];
        $store_state = '';
    } else {
        $store_country = $split_country[0];
        $store_state = $split_country[1];
    }
    if ( '' !== $store_country ) {
        $store_country = WC()->countries->countries[$store_country];
    }
    $array = array(
        'street_1' => $store_address,
        'street_2' => $store_address_2,
        'city'     => $store_city,
        'zip'      => $store_postcode,
        'country'  => $store_country,
        'state'    => $store_state,
    );
    return pdfclw_format_address( $format, $array );
}

/**
 * Funtion thea format the address.
 *
 * @param string $format format type.
 * @param array  $array address array.
 * @return statement
 */
function pdfclw_format_address(  $format, $array  ) {
    $address_1 = $array['street_1'];
    $address_2 = $array['street_2'];
    $city = $array['city'];
    $postcode = $array['zip'];
    $country = $array['country'];
    $state = $array['state'];
    if ( 'array' === $format ) {
        return $array;
    }
    if ( 'map_address' === $format ) {
        // Show state only for USA.
        if ( 'US' !== $array['country'] && 'United States (US)' !== $array['country'] ) {
            $state = '';
        }
        $address = $address_1 . ', ';
        $address .= $city;
        if ( !empty( $state ) || !empty( $postcode ) ) {
            $address .= ', ';
        }
        if ( !empty( $state ) ) {
            $address .= $state . ' ';
        }
        if ( !empty( $postcode ) ) {
            $address .= $postcode . ' ';
        }
        if ( !empty( $country ) ) {
            $address .= ' ' . $country;
        }
        $address = str_replace( '  ', ' ', trim( $address ) );
        $address = str_replace( ' ', '+', $address );
        return $address;
    }
    if ( 'address_line' === $format ) {
        // Show state only for USA.
        if ( 'US' !== $array['country'] && 'United States (US)' !== $array['country'] ) {
            $state = '';
        }
        $address = $address_1 . ', ';
        if ( !empty( $address_2 ) ) {
            $address .= $address_2 . ', ';
        }
        $address .= $city;
        if ( !empty( $state ) || !empty( $postcode ) ) {
            $address .= ', ';
        }
        if ( !empty( $state ) ) {
            $address .= $state . ' ';
        }
        if ( !empty( $postcode ) ) {
            $address .= $postcode . ' ';
        }
        if ( !empty( $country ) ) {
            $address .= ' ' . $country;
        }
        $address = str_replace( '  ', ' ', trim( $address ) );
        return $address;
    }
    if ( 'address' === $format ) {
        // Format address.
        // Show state only for USA.
        if ( 'US' !== $array['country'] && 'United States (US)' !== $array['country'] ) {
            $state = '';
        }
        $address = '';
        if ( !empty( $array['first_name'] ) ) {
            $first_name = $array['first_name'];
            $last_name = $array['last_name'];
            $address = $first_name . ' ' . $last_name . '<br>';
        }
        if ( !empty( $array['company'] ) ) {
            $address .= $array['company'] . '<br>';
        }
        $address .= $address_1;
        if ( !empty( $address_2 ) ) {
            $address .= ', ' . $address_2 . ' ';
        }
        $address .= '<br>' . $city;
        if ( !empty( $state ) || !empty( $postcode ) ) {
            $address .= ', ';
        }
        if ( !empty( $state ) ) {
            $address .= $state . ' ';
        }
        if ( !empty( $postcode ) ) {
            $address .= $postcode . ' ';
        }
        if ( !empty( $country ) ) {
            $address .= '<br>' . $country;
        }
        return $address;
    }
}

/**
 * Premium feature.
 *
 * @param string $value text.
 * @return html
 */
function pdfclw_admin_premium_feature(  $value  ) {
    $result = $value;
    if ( pdfclw_is_free() ) {
        $result = '<div class="pdfclw_premium_feature">
						<a class="pdfclw_star_button" href="#">
						<svg style="color:#ffc106" width=20 aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" class=" pdfclw_premium_icon svg-inline--fa fa-star fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><title>' . esc_attr__( 'Premium Feature', 'pdfclw' ) . '</title><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
						</a>
					  	<div class="pdfclw_premium_feature_note" style="display:none">
						  <a href="#" class="pdfclw_premium_close">
						  <svg aria-hidden="true"  width=10 focusable="false" data-prefix="fas" data-icon="times" class="svg-inline--fa fa-times fa-w-11" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg></a>
						  <h2>' . esc_html( __( 'Premium Feature', 'pdfclw' ) ) . '</h2>
						  <p>' . esc_html( __( 'You Discovered a Premium Feature!', 'pdfclw' ) ) . '</p>
						  <p>' . esc_html( __( 'Upgrading to Premium will unlock it.', 'pdfclw' ) ) . '</p>
						  <a target="_blank" href="https://powerfulwp.com/pickup-and-delivery-from-customer-locations-for-woocommerce#pricing" class="pdfclw_premium_buynow">' . esc_html( __( 'UNLOCK PREMIUM', 'pdfclw' ) ) . '</a>
						  </div>
					  </div>';
    }
    return $result;
}

/**
 * Check for free version
 *
 * @since 1.0.0
 * @return boolean
 */
function pdfclw_is_free() {
    if ( pdfclw_fs()->is__premium_only() && pdfclw_fs()->can_use_premium_code() ) {
        return false;
    } else {
        return true;
    }
}

/**
 * Determines whether HPOS is enabled.
 *
 * @return bool
 */
function pdfclw_is_hpos_enabled() : bool {
    if ( version_compare( get_option( 'woocommerce_version' ), '7.1.0' ) < 0 ) {
        return false;
    }
    if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
        return true;
    }
    return false;
}
