<?php

// Copies woocommerce orders and users over from source to target.
// I use this on my local machine - loading both db's up there side by side
// could easily adjust the connect strings to connect elsewhere if needed.
// will change order ids

// My use case for this is when I've got a staging/test version of a site with new posts/products/pages etc, that needs
// to go live without the loss of any orders placed on the site site since we copied it to the staging site.

// names of source and target dbs
define('NEW_DB', 'target');
define('OLD_DB', 'source');

// if you wanted to connect somewhere else, do it here
$new_conn = mysqli_connect('127.0.0.1', 'root', '');
$old_conn = mysqli_connect('127.0.0.1', 'root', '');

mysqli_select_db($new_conn, NEW_DB);
mysqli_select_db($old_conn, OLD_DB);

// completely replace user and usermeta (orders are related to usermeta - this simplifies things)
$cmd = 'mysqldump -h 127.0.0.1 -u root %s %s | mysql -h 127.0.0.1 -u root %s';
exec(sprintf($cmd, OLD_DB, 'wp_users', NEW_DB));
exec(sprintf($cmd, OLD_DB, 'wp_usermeta', NEW_DB));

// template for inserting the shop_order posts
$post_sql_template = "INSERT INTO `wp_posts` (
            `post_author`,
            `post_date`,
            `post_date_gmt`,
            `post_content`,
            `post_title`,
            `post_excerpt`,
            `post_status`,
            `comment_status`,
            `ping_status`,
            `post_password`,
            `post_name`,
            `to_ping`,
            `pinged`,
            `post_modified`,
            `post_modified_gmt`,
            `post_content_filtered`,
            `post_parent`,
            `guid`,
            `menu_order`,
            `post_type`,
            `post_mime_type`,
            `comment_count`
        ) VALUES (
            %s, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %s, '%s', %s, '%s', '%s', %s);";
print_r($post_sql_template);
// template for inserting the order_items
$item_sql_template = "INSERT INTO `wp_woocommerce_order_items` (`order_item_name`, `order_item_type`, `order_id`) VALUES ('%s', '%s', '%s')";

// If you want to remove all orders in the target db, and replace with those from source, uncomment this block.
// I don't usually do this as leaving it commented, orders common to both systems will be skipped and so
// retain their original ids

// $sql = "DELETE FROM wp_woocommerce_order_itemmeta";
// mysqli_query($new_conn, $sql);
// $sql = "DELETE FROM wp_woocommerce_order_items";
// mysqli_query($new_conn, $sql);
// $sql = "DELETE FROM wp_posts WHERE post_type = 'shop_order'";
// mysqli_query($new_conn, $sql);

$sql = "SELECT * FROM wp_posts WHERE post_type = 'shop_order' ";

$order_res = mysqli_query($old_conn, $sql);

while ($row = mysqli_fetch_assoc($order_res)) {

    $old_id = $row['ID'];

    // check to see if a shop_order post with this id exists - if so, skip
    // (see note on deleting above - this won't happen if that block is uncommented)
    $sql = "SELECT * FROM wp_posts WHERE post_type = 'shop_order' AND ID = " . $old_id;
    $count_res = mysqli_query($new_conn, $sql);
    if (mysqli_num_rows($count_res)) {
        echo "Skipping " . $old_id . "..." . PHP_EOL;
        continue;
    }
    echo "Processing " . $old_id . "..." . PHP_EOL;

    // add the post
    $post_sql = sprintf($post_sql_template,
        $row['post_author'],
        $row['post_date'],
        $row['post_date_gmt'],
        $row['post_content'],
        $row['post_title'],
        $row['post_excerpt'],
        $row['post_status'],
        $row['comment_status'],
        $row['ping_status'],
        $row['post_password'],
        $row['post_name'],
        $row['to_ping'],
        $row['pinged'],
        $row['post_modified'],
        $row['post_modified_gmt'],
        $row['post_content_filtered'],
        $row['post_parent'],
        $row['guid'],
        $row['menu_order'],
        $row['post_type'],
        $row['post_mime_type'],
        $row['comment_count']
    );

    $insert_post_res = mysqli_query($new_conn, $post_sql);
    $new_id = mysqli_insert_id($new_conn);

    // and the postmeta
    $sql = "INSERT INTO " . NEW_DB . ".wp_postmeta (post_id, meta_key, meta_value) SELECT " . $new_id . ", meta_key, meta_value FROM " . OLD_DB . ".wp_postmeta old WHERE old.post_id = " . $old_id;
    mysqli_query($new_conn, $sql);

    // and term relationships (stores order status)
    $sql = "INSERT INTO " . NEW_DB . ".wp_term_relationships (object_id, term_taxonomy_id, term_order) SELECT " . $new_id . ", term_taxonomy_id, term_order FROM " . OLD_DB . ".wp_term_relationships old WHERE old.object_id = " . $old_id;
    mysqli_query($new_conn, $sql);

    // and the comments - which store order nots
    $sql = "INSERT INTO " . NEW_DB . ".wp_comments (`comment_post_ID`, `comment_author`, `comment_author_email`, `comment_author_url`, `comment_author_IP`, `comment_date`, `comment_date_gmt`, `comment_content`, `comment_karma`, `comment_approved`, `comment_agent`, `comment_type`, `comment_parent`, `user_id`)
            SELECT $new_id, `comment_author`, `comment_author_email`, `comment_author_url`, `comment_author_IP`, `comment_date`, `comment_date_gmt`, `comment_content`, `comment_karma`, `comment_approved`, `comment_agent`, `comment_type`, `comment_parent`, `user_id` FROM " . OLD_DB . ".wp_comments old WHERE old.comment_post_ID = $old_id";
    mysqli_query($new_conn, $sql);

    // and then order items and order item meta
    $sql = sprintf("SELECT * FROM wp_woocommerce_order_items WHERE order_id = %s", $old_id);
    $item_res = mysqli_query($old_conn, $sql);
    while ($item_row = mysqli_fetch_assoc($item_res)) {
        $old_item_id = $item_row['order_item_id'];

        $item_sql = sprintf($item_sql_template,
            $item_row['order_item_name'],
            $item_row['order_item_type'],
            $new_id
        );
        mysqli_query($new_conn, $item_sql);
        $new_item_id = mysqli_insert_id($new_conn);
        $sql = "INSERT INTO " . NEW_DB . ".wp_woocommerce_order_itemmeta (order_item_id, meta_key, meta_value) SELECT " . $new_item_id . ", meta_key, meta_value FROM " . OLD_DB . ".wp_woocommerce_order_itemmeta old WHERE old.order_item_id = " . $old_item_id;
        mysqli_query($new_conn, $sql);

    }

}
