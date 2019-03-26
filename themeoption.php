<?php
/**
 * RFIB theme options.
 * Author: Svetlana
 * Date: 20/12/18
 * Time: 8:02 PM
 */

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Theme General Settings',
		'menu_title'	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Theme Header Settings',
		'menu_title'	=> 'Header',
		'parent_slug'	=> 'theme-general-settings',
	));
	
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Theme Footer Settings',
		'menu_title'	=> 'Footer',
		'parent_slug'	=> 'theme-general-settings',
	));
	
}

/**
|-----custom Meta Box------------|
*/
function product_team_metaboxes() {
	add_meta_box(
		'product_team',
		'Team Members',
		'product_team_list_html',
		'rfib_products',
		'normal',
		'default'
	);
}
add_action('admin_menu', 'product_team_metaboxes');

/**------Products Team Custom Meta Box-------**/
//HTML
function product_team_list_html($post){

	wp_nonce_field( basename(__FILE__), 'mam_nonce' );

	$args = array(
        'post_type' => 'team',
        'order'     => 'ASC',
		'orderby' => 'title',
        'posts_per_page' => -1
    );

    $args_s = array(
        'post_type' => 'team',
        'posts_per_page' => -1
    );

    $the_query = new WP_Query($args);
    $s_query = new WP_Query($args_s);

    $key = 'product_team';
    $values = get_post_meta( $post->ID, $key, true);
    
    //var_dump($values);

    // HTML
    echo '<div id="team-post_type" class="teamdiv">';
    //echo '<input type="hidden" name="team_input[]" value="0" />';
    echo '<div class="half-member-section">';
    echo '<h3>Member List</h3>';
    echo '<div class="move-members button button-primary">Move >></div>';
    echo '<ul class="memeber-list">';
    if( $the_query->have_posts() ):
        while ($the_query->have_posts()):$the_query->the_post();
            $checked = "";
            $id = get_the_ID();
            if ( is_array( $values ) && !in_array( $id, $values ) ) {
	            //$checked = 'checked="checked"';
                $checked = null;
                $title = get_the_title();
                echo "<li id='{$id}'>";
                echo "<label><input type='checkbox' name='team_input[]' id='in-$id'".$checked." value='$id' /> $title </label><br />";
                echo "<span data-id='$id' class='member-remove dashicons dashicons-trash'></span>";
                echo "</li>";
	        }
            
        endwhile;
        wp_reset_postdata();
    endif;
    echo '</ul></div>'; 

    echo '<div class="half-member-section"><h3>Selected Member List</h3><ul class="selected-member-list">';

    foreach($values as $v ):$i++;
        $p = get_post($v);
        $checked = "";
        $id = $p->ID;
        $checked = 'checked="checked"';
        //$checked = null;
        $title = get_the_title($p->ID);
        echo "<li id='{$id}'>";
        echo "<label><input type='checkbox' name='team_input[]' id='in-$id'".$checked." value='$id' />$title</label><br />";
        echo "<span data-id='$id' class='member-remove dashicons dashicons-trash'></span>";
        echo "</li>";            
    endforeach;

    echo '</ul></div></div>';// end HTML
}

add_action( 'save_post', function( $post_id ) {
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'mam_nonce' ] ) && wp_verify_nonce( $_POST[ 'mam_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // If the checkbox was not empty, save it as array in post meta
    if ( ! empty( $_POST['team_input'] ) ) {
        update_post_meta( $post_id, 'product_team', $_POST['team_input'] );

    // Otherwise just delete it if its blank value.
    } else {
        delete_post_meta( $post_id, 'product_team' );
    }

});

/**------Overseas Team Custom Meta Box-------**/
function overseas_team_metaboxes() {
    add_meta_box(
        'overseas_team',
        'Team Members',
        'overseas_team_list_html',
        'overseas_operations',
        'normal',
        'default'
    );
}
add_action('admin_menu', 'overseas_team_metaboxes');

/**------Products Team Custom Meta Box-------**/
//HTML
function overseas_team_list_html($post){

    wp_nonce_field( basename(__FILE__), 'm_nonce' );

    $args = array(
        'post_type' => 'team',
        'order'     => 'DESC',
		'orderby' => 'title',
        'posts_per_page' => -1
    );
    $the_query = new WP_Query($args);

    $key = 'overseas_team';
    $values = get_post_meta( $post->ID, $key, true);
    
    //var_dump($values);

    // HTML
    echo '<div id="team-post_type" class="teamdiv">';
    //echo '<input type="hidden" name="oversea_team_input[]" value="0" />';
    echo '<ul>';
    if( $the_query->have_posts() ):
        while ($the_query->have_posts()):$the_query->the_post();
            $checked = "";
            $id = get_the_ID();
            if ( is_array( $values ) && in_array( $id, $values ) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = null;
            }
            $title = get_the_title();
            echo "<li id='{$id}'>";
            echo "<label><input type='checkbox' name='oversea_team_input[]' id='in-$id'".$checked." value='$id' /> $title </label><br />";
            echo "</li>";
        endwhile;
    endif;
    echo '</ul></div>'; // end HTML
}

add_action( 'save_post', function( $post_id ) {
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'm_nonce' ] ) && wp_verify_nonce( $_POST[ 'm_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // If the checkbox was not empty, save it as array in post meta
    if ( ! empty( $_POST['oversea_team_input'] ) ) {
        update_post_meta( $post_id, 'overseas_team', $_POST['oversea_team_input'] );

    // Otherwise just delete it if its blank value.
    } else {
        delete_post_meta( $post_id, 'overseas_team' );
    }

});


/**------Related Products Custom Meta Box-------**/
function related_products_metaboxes() {
    add_meta_box(
        'related_products',
        'Choose Related Products',
        'related_products_list_html',
        'rfib_products',
        'normal',
        'default'
    );
}
add_action('admin_menu', 'related_products_metaboxes');

/**------Products Custom Meta Box-------**/
//HTML
function related_products_list_html($post){

    wp_nonce_field( basename(__FILE__), 'r_nonce' );

    $args = array(
        'post_type' => 'rfib_products',
        'order'     => 'ASC',
		'orderby' => 'title',
        'posts_per_page' => -1
    );
    $the_query = new WP_Query($args);

    $key = 'related_products';
    $values = get_post_meta( $post->ID, $key, true);
    
    //var_dump($values);

    // HTML
    echo '<div id="team-post_type" class="teamdiv">';
    //echo '<input type="hidden" name="oversea_team_input[]" value="0" />';
    echo '<ul>';
    if( $the_query->have_posts() ):
        while ($the_query->have_posts()):$the_query->the_post();
            $checked = "";
            $id = get_the_ID();
            if ( is_array( $values ) && in_array( $id, $values ) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = null;
            }
            $title = get_the_title();
            echo "<li id='{$id}'>";
            echo "<label><input type='checkbox' name='related_products[]' id='in-$id'".$checked." value='$id' /> $title </label><br />";
            echo "</li>";
        endwhile;
    endif;
    echo '</ul></div>'; // end HTML
}

add_action( 'save_post', function( $post_id ) {
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'r_nonce' ] ) && wp_verify_nonce( $_POST[ 'r_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // If the checkbox was not empty, save it as array in post meta
    if ( ! empty( $_POST['related_products'] ) ) {
        update_post_meta( $post_id, 'related_products', $_POST['related_products'] );

    // Otherwise just delete it if its blank value.
    } else {
        delete_post_meta( $post_id, 'related_products' );
    }

});


/**------Team Products Custom Meta Box-------**/
function team_products_metaboxes() {
    add_meta_box(
        'team_products',
        'Choose Products',
        'team_products_list_html',
        'team',
        'normal',
        'default'
    );
}
add_action('admin_menu', 'team_products_metaboxes');

/**------Products Custom Meta Box-------**/
//HTML
function team_products_list_html($post){

    wp_nonce_field( basename(__FILE__), 't_nonce' );

    $args = array(
        'post_type' => 'rfib_products',
        'order'     => 'ASC',
        'posts_per_page' => -1
    );
    $the_query = new WP_Query($args);

    $key = 'team_products';
    $values = get_post_meta( $post->ID, $key, true);

    //products specialisam
    $taxonomy = 'specialism';
    $terms = get_terms($taxonomy,array('orderby' => 'ID', 'order' => 'ASC')); //get terms taxonomy
    
    //var_dump($values);

    // HTML
    echo '<div id="team-post_type" class="teamdiv">';
    //echo '<input type="hidden" name="oversea_team_input[]" value="0" />';
    echo '<ul>';
    if( $the_query->have_posts() ):
        while ($the_query->have_posts()):$the_query->the_post();
            $checked = "";
            $id = get_the_title();
            $slug = get_post_field( 'post_name', get_the_ID() );
            if ( is_array( $values ) && in_array( $slug, $values ) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = null;
            }
            $title = get_the_title();
            echo "<li id='{$id}'>";
            echo "<label><input type='checkbox' name='team_products[]' id='in-$slug'".$checked." value='$slug' /> $title </label><br />";
            echo "</li>";
        endwhile;
    endif;
    echo "<li style='border-bottom:1px solid #e2e2e2;'></li>";
    foreach ($terms as $term) {
        $term_name = $term->name;
        $term_slug = $term->slug;
        if ( is_array( $values ) && in_array( $term_slug, $values ) ) {
            $checked = 'checked="checked"';
        } else {
            $checked = null;
        }

        echo "<li id='{$term->ID}'>";
        echo "<label><input type='checkbox' name='team_products[]' id='in-$term_slug'".$checked." value='$term_slug' /> $term_name </label><br />";
        echo "</li>";
    }
    echo '</ul></div>'; // end HTML
}

add_action( 'save_post', function( $post_id ) {
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 't_nonce' ] ) && wp_verify_nonce( $_POST[ 't_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // If the checkbox was not empty, save it as array in post meta
    if ( ! empty( $_POST['team_products'] ) ) {
        update_post_meta( $post_id, 'team_products', $_POST['team_products'] );

    // Otherwise just delete it if its blank value.
    } else {
        delete_post_meta( $post_id, 'team_products' );
    }

});

/*---------------------------
//Management Board Custom Meta Box
------------*/
function team_management_board_metabox(){
    add_meta_box(
        'team_management_board',
        'Management Board',
        'team_management_board_html',
        'team',
        'side',
        'default'
    );
}
add_action('admin_menu', 'team_management_board_metabox');

function team_management_board_html($post){
    wp_nonce_field( basename(__FILE__), 't_nonce' );
    $key = 'team_management_board';
    $value = get_post_meta($post->ID,$key,true);
?>
    <input type="checkbox" name="team_management_board" <?php if( $value == true ) { ?>checked="checked"<?php } ?> />  Management Board.
<?php
}

add_action( 'save_post', function( $post_id ) {
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 't_nonce' ] ) && wp_verify_nonce( $_POST[ 't_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // If the checkbox was not empty, save it as array in post meta
    if ( ! empty( $_POST['team_management_board'] ) ) {
        update_post_meta( $post_id, 'team_management_board', $_POST['team_management_board'] );

    // Otherwise just delete it if its blank value.
    } else {
        delete_post_meta( $post_id, 'team_management_board' );
    }

});



/**------Visual Editor Disable---------**/
add_action('init', 'init_remove_support');
function init_remove_support(){
    $post_type = 'team';
    remove_post_type_support( $post_type, 'editor');
}
add_action('init', 'init_rp_remove_support');
function init_rp_remove_support(){
    $post_type = 'rfib_products';
    remove_post_type_support( $post_type, 'editor');
}


/*-----sub menu adding ----------*/
function change_submenu_class($menu) {  
  $menu = preg_replace('/ class="sub-menu"/','/ class="sub-menu" /',$menu);  
  return $menu;  
}  
add_filter('wp_nav_menu','change_submenu_class');  
