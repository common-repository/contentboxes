<?php
require_once("../../../wp-blog-header.php");
require_once('./contentboxes.php');

$sql = "SELECT * FROM $wpdb->posts WHERE ID = '$_GET[id]'";
$row = $wpdb->get_row($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="coverage" content="Worldwide" />
	<meta name="robots" content="index,follow" />
	<meta name="revisit-after" content="1 Day" />
	<meta name="content-language" content="de" />
	<meta name="Language" content="German, de, deutsch" />
	<meta name="page-topic" content="" />
	<title>Configure Contentbox</title>
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<link rel="stylesheet" href="./css/contentboxes.css" type="text/css" />
	<script type="text/javascript" src="<?php bloginfo('url') ?>/wp-includes/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="./js/jquery-ui-personalized-1.5.2.min.js"></script>
	<script type="text/javascript" src="./js/contentboxes.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			/*= Get config ======================================*/
			conf = parent.jQuery(<?php echo "'#cbc_".$_GET['id']."'"; ?>).val();
			if (conf) {
				if (conf.search('hidetitle') != '-1') {jQuery('#hidetitle').attr('checked','checked'); jQuery('.cb_title').toggle();}
				if (conf.search('hidecontent') != '-1') {jQuery('#hidecontent').attr('checked','checked'); jQuery('.cb_content').toggle();}
				if (conf.search('linkto') != '-1') {jQuery('#linkto').attr('checked','checked');}
				if (conf.search('removeimages') != '-1') {jQuery('#removeimages').attr('checked','checked'); jQuery('.cb_content img').toggle();}
			}
			/*= Set Config ======================================*/
			jQuery('span.button').click(function(){
				if (document.getElementById('hidetitle').checked == true) { title = jQuery('#hidetitle').val();} else {title = '';}
				if (document.getElementById('hidecontent').checked == true) { content = jQuery('#hidecontent').val();} else {content = '';}
				if (document.getElementById('linkto').checked == true) { link = jQuery('#linkto').val();} else {link = '';}
				if (document.getElementById('removeimages').checked == true) { img = jQuery('#removeimages').val();} else {img = '';}
				conf = title + ',' + content + ',' + link + ',' + img;
				parent.jQuery(<?php echo "'#cbc_".$_GET['id']."'"; ?>).val(conf);
			});
		});
	</script>
</head>
<body id="contentbox_config">
	<!--
	<form method="post">
		<h1 class="headline"><?php _e('Configure Contentbox'); ?></h1>

		<table class="form-table">
			<tr>
				<th><label for="hidetitle"><?php _e('Hide Title'); ?></label></th>
				<td><input name="hidetitle" id="hidetitle" value="hidetitle" type="checkbox" onChange="jQuery('.cb_title').toggle();" /></td>
			</tr>
			<tr>
				<th><label for="hidecontent"><?php _e('Hide Post Content'); ?></label></label></th>
				<td><input name="hidecontent" id="hidecontent" value="hidecontent" type="checkbox" onChange="jQuery('.cb_content').toggle();"  /></td>
			</tr>
			<tr>
				<th><label for="linkto"><?php _e('Link to full post'); ?></label></th>
				<td><input name="linkto" id="linkto" value="linkto" type="checkbox" /></td>
			</tr>
			
			<tr>
				<th><label for="removeimages"><?php _e('Remove Images'); ?></label></th>
				<td><input name="removeimages" id="removeimages" value="removeimages" type="checkbox" onChange="jQuery('.cb_content img').toggle();" /></td>
			</tr>
			
		</table>
		
		<p class="submit"><span class="button"><?php _e('Save'); ?></span></p>
		
	</form>
	-->
	<div id="cb_preview">
		<h1 class="headline"><?php _e('Preview'); ?></h1>

		<ul id="contentboxes">
			<li class="contentbox" id="contentbox-<?php echo $row->ID; ?>">
				<h3 class="cb_title"><?php echo $row->post_title;?></h3>
				<div class="cb_content">
				<?php
				echo $content = ($row->post_excerpt) ? apply_filters('the_content', $row->post_excerpt) : apply_filters('the_content', $row->post_content);
				?>
				</div>
			</li>
		</ul>
	</div>
</body>
</html>




