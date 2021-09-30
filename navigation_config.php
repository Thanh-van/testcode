<?php

/**
 * create menu_custom_admin
 */
function Menu_API()
{
    add_menu_page(
        'API_con',
        'Config',
        'manage_options',
        'Config_api',
        'Migrate_show_post',
        '',
        '20'
    );
}


/**
 * Form data API
 */
function Migrate_show_post()
{
    ?>
        <div class="wrap config_option">
            <div class="Campaign_title">
                <h1 class="wp-heading-inline">Setting config</h1>
                
                <?php 
                edit_config();
                ?>
            </div>
            <table class="wp-list-table widefat fixed striped table-view-list pages">
                <thead>
                    <tr>
                        <td style="width: 20%;">
                            <span class="title-config">Title</span>
                        </td>
                        <td class="manage-column column-author" style="width: 60%">
                            <span class="config-value">Value</span>
                        </td>
                        <td style="width: 20%;">
                            <span class="config-action">Action</span>
                        </td>
                    </tr>
                </thead>
                <tbody id="the-list">
                    <?php 
                        load_option();
                    ?>
                </tbody>
            </table>
            <div class="connect_api">
                <?php Connect_site(); ?>
            </div>
        </div>
    <?php
}

/**
 * Load option config
 */
function load_option()
{
    $key=array("url_site","key");
    foreach ($key as $item)
    {
        $check = get_option($item,false);
        if ($check != false ){
            ?>
            <tr>
                <td>
                    <p><?= $item ?></p>
                </td>
                <td>
                    <p>
                    <?= $check ?>
                    </p>
                </td>
                <td>
                    <a href="?page=Config_api&edit=<?= $item ?>">edit</a>
                </td>
            </tr>
            <?php   
        }   
    }
}

/**
 * Form edit 
 */
function edit_config()
{
    if (isset($_GET['edit']) ){
        ?>
            <div class="edit_config">
                <form action="" method="POST">
                    <input type="text" name="title" value="<?= $_GET['edit'] ?>" hidden />
                    <input type="text" name="values" value="<?= get_option($_GET['edit']) ?>" required/>
                    <input type="submit" class="button action" name="edit" value="Edit">
                </form>
            
            </div>
        <?php
    }
}

/**
 * Form connection site
 */
function Connect_site()
{
    include 'shortcode/Curl_api.php';
    print_r( get_db());
}
add_action('admin_menu', 'Menu_API');
