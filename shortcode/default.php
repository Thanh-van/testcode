<?php 
/**
 * Create config filed
 */
function add_option_custom()
{
    $key = array("url_site","key");
    foreach ($key as $item)
    {
        $check = get_option($item);
        if ($check == null) {
            add_option($item,"defaul");
        }
    }
}
add_action('init', 'add_option_custom');

/**
 * Edit value option config
 */
function edit_option()
{
    if ( isset($_GET['page']) && isset($_POST['edit']) && $_GET['page'] === "Config_api")
    {
        $check = get_option($_POST['title'],false);
        if ($check != false)
        {
            update_option($_POST['title'],$_POST['values']);
            header('Location: ?page=Config_api');
        }
    }
}
add_action('init', 'edit_option');