<?php
require_once("../../../wp-blog-header.php");


$sql = "SELECT * FROM $wpdb->posts WHERE ID = '$_GET[id]'";
$row = $wpdb->get_row($sql);

?>

<div id="content" class="widecolumn">	
	<div class="post" id="post-<?php echo $row->ID ?>">
		<h2><?php echo $row->post_title ?></h2>

		<div class="entry">
			<?php echo apply_filters('the_content',$row->post_content) ?>
		</div>
	</div>
</div>
