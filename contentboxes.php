<?php
/*
Plugin Name: Contentboxes
Plugin URI: http://www.horttcore.de/wordpress/contentboxes/
Description: This plugin will add some awesome cms functions to your site. Add posts to static pages on the fly.
Version: 1.1
Author: Ralf Hortt
Author URI: http://www.horttcore.de/
*/

//======================================
// Description: Displaying the Contentbox tab in editor view
function cb_add_box(){
	#add_meta_box('contentboxen', __('Contentboxes'), 'cb_meta_box', 'post');	
	add_meta_box('contentboxen', __('Contentboxes'), 'cb_meta_box', 'page');
}

//======================================
// @Description: 
// @Require: 
// @Optional: 
// @Return: 
function cb_administration_head(){
	if (preg_match('&post-new.php|page-new.php|page.php|post.php&',$_SERVER['REQUEST_URI'])) {?>
		<link rel="stylesheet" href="<?php bloginfo('url') ?>/<?php echo PLUGINDIR ?>/contentboxes/css/contentboxes.css" type="text/css" media="screen" title="no title" charset="utf-8">
		<!-- <script type="text/javascript" charset="utf-8" src="<?php bloginfo('url') ?>/wp-includes/js/jquery/ui.sortable.js"></script> -->
		<script type="text/javascript" charset="utf-8" src="<?php bloginfo('url') ?>/<?php echo PLUGINDIR ?>/contentboxes/js/contentboxes.js"></script>
		<?php
	}
}

//======================================
// Description: Template Tag for displaying the contentboxes
function wp_get_contentboxes($before="<ul class='contentboxes'>", $after="</ul>"){
global $post, $user_ID, $wpdb;
	$cbs = get_contentbox_ids($post->ID);
	
	$cb_ids = unserialize($cbs[0]);
	$conf = unserialize($cbs[1]);
	
	if ($cb_ids) {	
		$i = 0;
		echo $before;
		foreach($cb_ids as $box) {
			$sql = "SELECT ID, post_title, guid, post_excerpt, post_content FROM $wpdb->posts WHERE ID = '$box'";
			$row = $wpdb->get_row($sql);
			$edit = ($user_ID) ? ' <a class="bullet" href="'.get_bloginfo('url').'/wp-admin/post.php?action=edit&post='.$row->ID.'">Contentbox bearbeiten</a> ' : '';
			?>
			<li class="contentbox" id="contentbox-<?php echo $row->ID; ?>"><?php
				if (!preg_match('&hidetitle&',$conf[$i])) { ?>
					<div class="contentbox_header"><h2><?php if(preg_match('&linkto&',$conf[$i])) {echo "<a href='$row->guid'>";} echo $row->post_title; if(preg_match('&linkto&',$conf[$i])) {echo "</a>";}?></h2></div><?php 
				}
				if (!preg_match('&hidecontent&',$conf[$i])) {
					?><div class="contentbox_body"><?php
					echo apply_filters('the_content', $row->post_content);
					echo $edit;
					?></div><?php
				}
				?>
				<div class="contentbox_footer">&nbsp;</div>
			</li>
			<?php
			$i++;
		}
		echo $after;
	}
}

//======================================
// Description: Content of the meta box
function cb_meta_box(){
global $wpdb;

	if ($_GET['post']) {$cbs = get_contentbox_ids($_GET['post']);}
	
	$cb_ids = unserialize($cbs[0]);
	$cb_config = unserialize($cbs[1]);
	
	$act_cbs = array();
	
	?>
	<ul id="contentboxes" class="sortable">
		<?php
		if ($cb_ids) {
			$i = 0;
			
			foreach($cb_ids as $cb) {
				array_push($act_cbs, $cb);
				?>
			<li class="remove_cb" id="cb_act_<?php echo $cb; ?>">
				<span class="remove_box" onClick="remove_contentbox('<?php echo $cb; ?>');" title="<?php _e('Add Contentbox'); ?>">&nbsp;</span>
				<a href="<?php bloginfo('url') ?>/<?php echo PLUGINDIR ?>/contentboxes/contentbox_config.php?id=<?php echo $cb; ?>&amp;TB_iframe=true" class="thickbox"><?php echo cb_get_post_title($cb); ?></a>
				<input type="hidden" name="contentbox[]" id="cb_<?php echo $cb; ?>" value="<?php echo $cb; ?>" />
				<input type="hidden" name="contentboxconfig[]" id="cbc_<?php echo $cb; ?>" value="<?php echo $cb_config[$i]; ?>" />
			</li>
			<?php
			$i++;
			}
		} ?>
	</ul>
	<hr />
	
	<ul class="add_cb">
	<?php
	
	$sql = "SELECT * FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships ON object_id = ID WHERE term_taxonomy_id = '".cb_category()."' AND post_status = 'publish' ORDER BY p.post_excerpt, p.post_title, ID";
	$row = $wpdb->get_results($sql);
	
	foreach($row as $row) {
		$style = (in_array($row->ID, $act_cbs)) ? 'style="display: none;"' : '';
		$title = ($row->post_excerpt) ? $row->post_excerpt : $row->post_title;
		?>
		<li <?php echo $style ?> id="add_<?php echo $row->ID ?>" onClick="add_contentbox('<?php echo $row->ID ?>', '<?php echo $title ?>', '<?php bloginfo('url') ?>/<?php echo PLUGINDIR ?>/contentboxes/contentbox_config.php?id=<?php echo $row->ID; ?>');" class="add_cb">
			<a href="<?php bloginfo('url') ?>/<?php echo PLUGINDIR ?>/contentboxes/preview.php?id=<?php echo $row->ID; ?>" class="thickbox"><?php echo $title ?></a>
		</li>
		<?php
	} ?>
	</ul>
	<?php
}

//======================================
// Description: Saving the relation between contentbox and post
// Require: $_POST['ID']
function cb_meta_save(){
global $wpdb;
	if ($_POST['ID']) { $id = $_POST['ID'];}
	else{$id = get_next_post_id();}
	$sql = "DELETE FROM $wpdb->postmeta WHERE post_id = '$id' AND meta_key = 'contentbox'";
	$wpdb->query($sql);
	if ($_POST['contentbox']) {
		$contentbox = serialize(array(serialize($_POST['contentbox']), serialize($_POST['contentboxconfig'])));
		$sql = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) VALUES ('$id', 'contentbox', '$contentbox')";
		$wpdb->query($sql);
	}
}

//======================================
// Description: Returns an array with the contentbox relations
function get_contentbox_ids($post_id){
global $wpdb;
	$cb_ids = array();
	$sql = "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'contentbox' AND post_id = '$post_id'";
	$meta = $wpdb->get_var($sql);
	
	$cb_ids = unserialize($meta);	
	
	return $cb_ids;
}

//======================================
// Description: Returns the Contentboxes category ID
function cb_category($return = 'single'){
	$id = is_term('Contentbox', 'category');
	$return = ($return == 'single') ? $id['term_id'] : array($id['term_id']);
	return $return;
}

//======================================
// Description: Remove contentboxes from the loop
// @Require: query object
function cb_remove_from_loop($query){
	$query->query_vars['category__not_in'] = cb_category('array');
	return $query;
}

//======================================
// Description: Removes Contentbox category from the category list
function cb_remove_from_category($subject){
	$pattern = '&<li class="cat-item cat-item-'.cb_category().'">.*Contentbox.*li>&isU';
	$subject = preg_replace($pattern, '',$subject);
	return $subject;
}

//======================================
// Description: Removes Contentbox category from the category dropdownbox
function cb_remove_from_dropdown($select){
	$select = str_replace('<option value="'.cb_category().'">Contentbox</option>','',$select);
	return $select;
}

//======================================
// Description: Removes Contentbox posts from adjacent
function cb_remove_from_adjacent_join($join){
global $wpdb;
	$join = "INNER JOIN $wpdb->term_relationships AS r ON r.object_id = p.ID";
	return $join;
}

//======================================
// Description: Removes Contentbox posts from adjacent
function cb_remove_from_adjacent_where($where){
global $wpdb;
	$where.= "and r.term_taxonomy_id != '".cb_category()."'"; 
	return $where;
}

//======================================
// Description: This runs when plugin is activated
function cb_install() {
global $wpdb;
	if (!is_term('Contentbox')) wp_create_category('Contentbox');
}

//======================================
// Description: This runs when the plugin is deactivated
function cb_deinstall(){
global $wpdb;
	wp_delete_category(cb_category());
	
	$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key = 'contentbox'";
	$wpdb->query($sql);
}

//======================================
// @Description: 
// @Require: 
// @Return: 
function cb_get_post_title($id){
global $wpdb;
	$sql = "SELECT post_title, post_excerpt FROM $wpdb->posts WHERE ID = '$id'";
	$row = $wpdb->get_row($sql);
	$title = ($row->post_excerpt) ? $row->post_excerpt : $row->post_title;
	return $title;
}

//======================================
// Description: Returns the next post ID
function get_next_post_id(){
global $wpdb;
	$sql = "SHOW TABLE STATUS LIKE '$wpdb->posts'";
	$row = $wpdb->get_row($sql);
	return $row->Auto_increment;
}

//====================================== WP HOOKS
register_activation_hook(__FILE__, 'cb_install');
#register_deactivation_hook(__FILE__, 'cb_deinstall');

add_action('admin_head', 'cb_administration_head');
add_action('admin_menu', 'cb_add_box');
add_action('save_post', 'cb_meta_save');

if (!is_admin()) add_filter('wp_list_categories', 'cb_remove_from_category');
if (!is_admin()) add_filter('wp_dropdown_cats', 'cb_remove_from_dropdown');
add_filter('get_next_post_where', 'cb_remove_from_adjacent_where');
add_filter('get_previous_post_where', 'cb_remove_from_adjacent_where');
add_filter('get_next_post_join', 'cb_remove_from_adjacent_join');
add_filter('get_previous_post_join', 'cb_remove_from_adjacent_join');
if (empty($_GET['p']) && empty($_GET['s']) && !is_admin() && empty($_GET['page_id'])) add_filter('pre_get_posts','cb_remove_from_loop');




?>