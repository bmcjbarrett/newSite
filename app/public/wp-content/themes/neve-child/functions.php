<?php


/*
// Disable Gutenberg on the back end. https://www.themeum.com/disable-gutenberg-editor-easily/
add_filter( 'use_block_editor_for_post', '__return_false' );

// Disable Gutenberg for widgets.
add_filter( 'use_widgets_block_editor', '__return_false' );

add_action( 'wp_enqueue_scripts', function() {
    // Remove CSS on the front end.
    wp_dequeue_style( 'wp-block-library' );

    // Remove Gutenberg theme.
    wp_dequeue_style( 'wp-block-library-theme' );

    // Remove inline global CSS on the front end.
    wp_dequeue_style( 'global-styles' );
}, 20 );

*/
/*
function suppress_all_admin_notices() {
    global $wp_filter;
    if (isset($wp_filter['admin_notices'])) {
        unset($wp_filter['admin_notices']);
    }
    if (isset($wp_filter['all_admin_notices'])) {
        unset($wp_filter['all_admin_notices']);
    }
}
add_action('admin_print_scripts', 'suppress_all_admin_notices');
*/
//
//Templates and Page IDs without editor
//
//
function ea_disable_editor( $id = false ) {

	$excluded_templates = array(
		'page-roastersignup.php'
		
	);

	$excluded_ids = array(
		// get_option( 'page_on_front' )
	);

	if( empty( $id ) )
		return false;

	$id = intval( $id );
	$template = get_page_template_slug( $id );

	return in_array( $id, $excluded_ids ) || in_array( $template, $excluded_templates );
}


// Disable Gutenberg by template

function ea_disable_gutenberg( $can_edit, $post_type ) {

	if( ! ( is_admin() && !empty( $_GET['post'] ) ) )
		return $can_edit;

	if( ea_disable_editor( $_GET['post'] ) )
		$can_edit = false;

	return $can_edit;

}
add_filter( 'gutenberg_can_edit_post_type', 'ea_disable_gutenberg', 10, 2 );
add_filter( 'use_block_editor_for_post_type', 'ea_disable_gutenberg', 10, 2 );

//
//AUTHOR PROFILE EDITS
//

//restric author roles
/*
function posts_for_current_author($query) {
        global $pagenow;
  
    if( 'edit.php' != $pagenow || !$query->is_admin )
        return $query;
  
    if( !current_user_can( 'manage_options' ) ) {
       global $user_ID;
       $query->set('author', $user_ID );
     }
     return $query;
}
add_filter('pre_get_posts', 'posts_for_current_author');

//remove roles from existance
function remove_selected_roles() {
    $roles_to_remove = ['subscriber', 'contributor','wpseo_editor', 'editor','wpseo_manager'];

    foreach ($roles_to_remove as $role) {
        remove_role($role);
    }
}
add_action('init', 'remove_selected_roles');
*/

//remove dashboard from author level 
function remove_menu_authorLevel() {
    if (current_user_can('author')) { 
        remove_menu_page('index.php'); // Removes the dashboard menu item for authors
    }
}
add_action('admin_init', 'remove_menu_authorLevel');




// author profile screen edit 

/*
add_action('admin_footer-profile.php', 'remove_profile_fields');
function remove_profile_fields() {
    if(is_admin() && current_user_can('author')) { // Check if the current user has the 'Author' role
        ?>
         <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Remove the sections titled "Name", "Contact Info", and "About Yourself"
                jQuery(' h2:contains("Personal Options"),   h2:contains("Contact Info"),   h2:contains("About Yourself")').each(function() {
                    // Remove the next form-table and the h2 itself
                    $(this).next('.form-table').remove();
                    $(this).remove();
                });
            });
        </script>
        <?php
    }
}
*/
//  adjust capabilities to author roles

/*
function assign_roaster_capabilities() {
    // Get the author role object
    $role = get_role('author');

    // Add capabilities for Roasters CPT
   // $role->add_cap('edit_roaster');
   // $role->add_cap('edit_roasters');
  //  $role->add_cap('edit_others_roaster');
  //  $role->remove_cap('publish_roaster');
  //  $role->add_cap('read_roaster');
  //  $role->add_cap('delete_roaster');
  //  $role->add_cap('delete_roasters');
  //  $role->add_cap('delete_published_roasters');
  //  $role->add_cap('delete_others_roasters');
}

// Assign capabilities on theme activation or plugin activation
add_action('init', 'assign_roaster_capabilities');
*/



function shortcode_roaster_loop() {
    ob_start(); // Start output buffering 
    include 'roastlist.php'; // Adjust the path to your PHP file
    $content = ob_get_clean(); // Store the buffer in a variable and clean the buffer
    return $content; // Return the content as the result of the shortcode
}

add_shortcode('roasterQuery', 'shortcode_roaster_loop');
//[roasterQuery] 


//https://weichie.com/blog/wordpress-filter-posts-with-ajax/
add_action('wp_footer', 'wpshout_action_example'); 
function wpshout_action_example() { 
    echo  "  
<script  src='https://code.jquery.com/jquery-3.7.1.min.js'  integrity='sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo='  crossorigin='anonymous'></script><script>

$('.cat-list_item').on('click', function() {
  $('.cat-list_item').removeClass('active');
  $(this).addClass('active');

var dataToSend ={
      action: 'filter_projects',
      category: $(this).data('slug'),
};
 	console.log('Data being sent:', dataToSend);


	//$('#zipID').change(function(){
		  $.ajax({
			type: 'POST',
			url: 'https://chicagolandofficecleaning.com/wp-admin/admin-ajax.php',
			dataType: 'html',
			data: dataToSend, 

			success: function(res) {
			  $('.project-tiles').html(res);

			},
			 error: function (xhr, ajaxOptions, thrownError) {
				//console.log(xhr.status);

			  }
		  })

	//});  
});

</script>
	
	"; //end echo
} //end function

//filter
function filter_projects() {
	
  $catSlug = $_POST['category'];



$args =[
	'post_type' => 'roasters',
	 'posts_per_page' => -1,
];
if($catSlug){
	$args =[ 'tax_query' => [ // This should be an array of arrays
            [
                'taxonomy' => 'zipcode_location', // Make sure taxonomy name is correct. It should be 'zipcode_locations' if that is what is registered in WordPress.
                'field'    => 'slug',
                'terms'    => $catSlug,
            ],
		  ],
		
	];
	
}
	
	$ajaxposts = new WP_Query ($args);
	
	  $response = '';

  if($ajaxposts->have_posts()) {
    while($ajaxposts->have_posts()) : $ajaxposts->the_post();
     //  ob_start(); // Start output buffering
            get_template_part('project-list-item');
          //  $response .= ob_get_clean(); // Store the output and clear buffer
	  //$response .=  include('project-list-item.php'); //get_template_part('project-list-item.php');
    endwhile;
  } else {
 $response =  $catSlug . 'empty ';
	
  }

  echo $response;
  exit;
}
add_action('wp_ajax_filter_projects', 'filter_projects');
add_action('wp_ajax_nopriv_filter_projects', 'filter_projects');



///
//USER META STUFF
//https://justintadlock.com/archives/2009/09/10/adding-and-using-custom-user-profile-fields
//
//

//
/*
add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) { ?>

	<h3>Extra  information</h3>

	<table class="form-table">

		<tr>
			<th><label for="twitter">YourData</label></th>

			<td>
				<input type="text" name="YourData" id="YourData" value="<?php echo esc_attr( get_the_author_meta( 'YourData', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter YourData .</span>
			</td>
		</tr>

	</table>




<?php }
//save user profile info  https://justintadlock.com/archives/2009/09/10/adding-and-using-custom-user-profile-fields 

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	// Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. 
	update_usermeta( $user_id, 'YourData', $_POST['YourData'] );
}



//show that users posts they made in the admin area at the bottom 

add_action('show_user_profile', 'display_user_posts_on_profile');
add_action('edit_user_profile', 'display_user_posts_on_profile');
function display_user_posts_on_profile() {
    // Check if on a profile page and if the user has the right capability to view posts
    if (is_admin() && current_user_can('edit_posts')) {
        $user_id = get_current_user_id(); // Get current user ID
        $args = array(
            'post_type' =>'roasters',
			'author' => $user_id,
            'posts_per_page' => 10  // Adjust the number of posts as needed
        );
        $user_posts = new WP_Query($args);
        
        if ($user_posts->have_posts()) {
            echo '<h3>Recent Posts:</h3>';
            echo '<ul>';
            while ($user_posts->have_posts()) {
                $user_posts->the_post();
                echo '<li><a href="' . get_permalink() . '?query" target="_blank">' . get_the_title() . '</a></li>';
            }
            echo '</ul>';
            wp_reset_postdata();
        } else {
            echo '<p>No posts found.</p>';
        }
    }
}


*/

?>