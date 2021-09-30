<?php

/**
     * Add hook send data to site2, set session and redirect checkout site2
     * 
     * @param int $order_id
     * 
     * @return redirect checkout site2
     */
add_action('woocommerce_thankyou', 'enroll_student', 10, 1);
function enroll_student($order_id)
{
    $order = wc_get_order($order_id);
    if (check_key() == true) {
        echo "site ok";
        WC()->session->set( 'woocommerce_thankyou', "true" );

    
        // Connect to site 2 and get status
        $response = connect_to_site_2($order_id);
        

        // Check data and redirect to site 2
        $data = json_decode($response['body'], true);
        if ( is_wp_error( $response ) ) {
            add_action('woocommerce_after_checkout_validation', 'wrong_data_message');
        } else {
            if ($data['data']['status'] == 200) {
                echo "site 2 ok";
                redirect_to_site_2($data);
            } else {
                echo "site 2 Fail";
                $order->update_status( 'failed' );
            }
        }   
    } else {
        echo "die site 1";
        $order->update_status( 'failed' );
        add_action('woocommerce_after_checkout_validation', 'message_fail_to_connect');
    }
    

}

// /**
//  * Hide shipping in cart page.
//  * 
//  *  @param int $needs_shipping
//  * 
//  * @return int
//  */
// add_filter( 'woocommerce_cart_needs_shipping', 'filter_cart_needs_shipping_callback' );
// function filter_cart_needs_shipping_callback( $needs_shipping ){
//     $needs_shipping = false;
//     return $needs_shipping;
// }

/**
 * Check session and remove action hook thankyou.
 */
add_action( 'init', function(){
    if( !is_admin() ) {
        $a = WC()->session->get( 'woocommerce_thankyou',null);
        if($a != null){
            remove_action( 'woocommerce_thankyou', 'enroll_student', 10, 1 ); 
            WC()->session->__unset( 'woocommerce_thankyou' );
        }
    }
} );


 /**
 * Get status and update status site thank you
 * 
 * @param int $order_id
 * 
 * @return
 */
add_action('woocommerce_thankyou', function($order_id){
    $order = new WC_Order($order_id);
    $status = $order->get_status();
    echo "<li>Status: <strong>" . $status . "</strong></li>";
});


/**
 * Data for post to site 2
 * 
 * @param int $order_id
 * 
 * @return array
 * 
 */

function order_data($order_id) {
    $order = wc_get_order($order_id);
    $key_order = $order->get_order_key();

    // The Order data
    $order_data = $order->get_data(); 
    foreach ($order->get_items() as $item_key => $item):
        $item_name = $item->get_name();
        $order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
        $site_url = get_site_url();
    endforeach;
    $total = $order_data['total'];
   
    //    Data body for posting
    $body = [
        "amount" => (int)$total,
        "orderId" => (string)$order_id,
        "saleTimestamp" => (int)$order_date_created,
        "storeGuid" => $_SERVER['HTTP_REFERER'].'&order-received='.$order_id.'&key='.$key_order.'',
        "storeName"=>   get_bloginfo(),
        "storeIP"=>GetIP(),
        "billing"=>$order_data['billing'],
    ];

    return $body;
}


function GetIP()
{
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
    {
        if (array_key_exists($key, $_SERVER) === true)
        {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip)
            {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
                {
                    return $ip;
                }
            }
        }
    }
}

/**
 * Check connecting key between site 1 and site 2
 * 
 * @param int $key
 * 
 * @return bool
 */

function check_key() {
    include_once 'Curl_api.php';
    if (get_db() == 200) {
        return true;
    }
    return false;
}


/**
 * For connecting site 2
 *
 * @param [int] $order_id
 * 
 * @return array
 * 
 */
function connect_to_site_2($order_id) {
    $url = get_option("url_site", false) . "?rest_route=/code/data";

    $body = order_data($order_id);

    return wp_remote_post( $url, [
        'sslverify' => false,
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => json_encode($body),
        'cookies' => array()
    ]
);
}


/**
 *  For redirecting site 2
 *
 * @param [json] $response
 * @param [array] $order
 * @return void
 */
function redirect_to_site_2($data) {
    $filename = $data['message'];
    $key = get_option('key', false);
    $url_redirect = get_option('url_site') . "?action=";
    $url = $url_redirect.$filename.'&key='.$key;
    wp_redirect($url); 
}


/**
 * Connection Error Message
 */
function message_fail_to_connect() {
    wc_add_notice( __( "Connection Error!", 'woocommerce' ), 'error' );
}

/**
 * Posting Data Message
 */
function wrong_data_message() {
    wc_add_notice( __( "Data was wrong!", 'woocommerce' ), 'error' );
}