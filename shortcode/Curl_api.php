<?php

    /**
     * Check data validity
     * 
     * @param null
     * 
     * @return 
     */
    function get_db()
    {
      
            $url = get_option("url_site", false);
            $key = get_option("key", false);
            $url .= "?rest_route=/code/key/";
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

    

