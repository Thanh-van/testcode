<?php
/**
 * Check data validity
 *
 * @return array
 */
function get_db()
{
    $url = get_option("url_site", false) . "?rest_route=/code/key/";
    $key = get_option("key", false);
    $response = wp_remote_get($url . $key, array(
        'sslverify' => false
    ));
    if (!is_wp_error($response))
    {
        $response = wp_remote_retrieve_body($response);
        $response = json_decode($response, true);
        return $response['data']['status'];
    }
}