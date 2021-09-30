<?php
  /**
     * Add API Check id at site1 and key at site2
     *
     * 
     * @param string $key
     * 
     * @return 
     * 
     */
  add_action('rest_api_init', function ()
  {
      register_rest_route('code', '/key/(?P<id>[a-zA-Z0-9-]+)', array(
          'methods' => 'GET',
          'callback' => 'get_or_insert'
      ));
  });
  /**
     * Check id at site1 and key at site2
     * 
     * @param $data
     * 
     * @return WP_Error
     */
  function get_or_insert($data)
  {
      if ($data['id'] === get_option('key')) 
        return new WP_Error('key is authorized', __('The key is authorized') , array(
          'status' => 200
      ));
      else 
        return new WP_Error('key is not authorized', __('The key is not authorized') , array(
          'status' => 401
      ));
  }

    /**
     * Add API to get data from site2
     * 
     * @param null
     * 
     * @return 
     */

add_action( 'rest_api_init', function () {
  register_rest_route('code','/repon',[
    'methods' => 'POST',
    'callback' => 'code_repon'
   ] );
} ); 


/**
     * Data returned for site 1 and redirect site thank you
     * 
     * @param json $request
     * 
     * @return WP_Error
     */

function code_repon(WP_REST_Request $request){
    $response = json_decode($request->get_body(), true);
    $order_id = $response['order_id'];
    $order = new WC_Order($order_id);
    $order->update_status('completed');
    $status = $order->get_status();
    get_status($status);
    log_history($request->get_body());     
}


   /**
     * Save return values ​​ and incoming requests
     * 
     * @param json $respon
     * 
     * @return write to file
     */

function log_history($respon){

    $file = plugin_dir_path( __FILE__ ) . '/errors.log'; 
    $open = fopen( $file, "a" ); 
    $ban = date("d:m:Y, g:i a").PHP_EOL.
            "data: ".$respon.PHP_EOL.
            "-------------------------------------------------------------------------------------".PHP_EOL;
    $write = fputs( $open, $ban ); 
    fclose( $open );
} 