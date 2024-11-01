<?php
/*
Plugin Name: WP-Follow5
Plugin URI: http://immmmm.com/wp-follow5.html
Description: WordPress the new post will be automatically sent to the Follow5.com // 将WordPress新日志自动发送至 Follow5.com
Version: 1.0.0
Author: 林木木
Author URI: http://immmmm.com
*/
add_action('admin_menu', 'f5_page');
function f5_page (){
	if ( count($_POST) > 0 && isset($_POST['f5_settings']) ){
		$options = array ('1','2','3');
		foreach ( $options as $opt ){
			delete_option ( 'f5_'.$opt, $_POST[$opt] );
			add_option ( 'f5_'.$opt, $_POST[$opt] );	
		}
	}
	add_options_page('WP-Follow5', 'WP-Follow5', 8, basename(__FILE__), 'f5_settings');
}

function f5_settings() {?>
<style>
	.wrap,.wrap h2,textarea,em{font-family:'Century Gothic','Microsoft YaHei',Verdana;}
	.wrap{margin:0 auto;width:411px;}
	fieldset{border:1px solid #aaa;padding-bottom:20px;margin-top:10px;-webkit-box-shadow:rgba(0,0,0,.2) 0px 0px 5px;-moz-box-shadow:rgba(0,0,0,.2) 0px 0px 5px;box-shadow:rgba(0,0,0,.2) 0px 0px 5px;}
	legend{margin-left:5px;padding:0 5px;color:#2481C6;background:#F9F9F9;font-size:21px;}
	.form-table th{width:148px;}
	.form-table input{width:188px;}
	input[type="text"],input[type="password"]{font-size:11px;border:1px solid #aaa;background:none;-moz-box-shadow:rgba(0,0,0,.2) 1px 1px 2px inset;box-shadow:rgba(0,0,0,.2) 1px 1px 2px inset;-webkit-transition:all .4s ease-out;-moz-transition:all .4s ease-out;}
	input:focus{-moz-box-shadow:rgba(0,0,0,.2) 0px 0px 8px;box-shadow:rgba(0,0,0,.2) 0px 0px 8px;outline:none;}
</style>
<div class="wrap">
<h2>WP-Follow5</h2>
<form method="post" action="">
<fieldset>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="1">Follow5帐户：</label></th>
				<td>
					<input name="1" type="text" id="1" value="<?php echo get_option('f5_1');?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="2">Follow5密码：</label></th>
				<td>
					<input name="2" type="password" id="2" value="<?php echo get_option('f5_2'); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="3">API KEY（选填）：</label></th>
				<td>
					<input name="3" type="text" id="3" value="<?php echo get_option('f5_3'); ?>" />
				</td>
			</tr>
		</table>
	</fieldset>

	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="保存设置" />
		<input type="hidden" name="f5_settings" value="save" style="display:none;" />
	</p>

</form>
</div>
<?php
}

function update_follow5($status){
	if(get_option('f5_1')!='')
		$username = get_option('f5_1');
	if(get_option('f5_2')!='')
		$password = get_option('f5_2');
	if(get_option('f5_3')!=''){
		$api_key = get_option('f5_3');
	}else{
		$api_key= '314FF701BC626965C9E9A17AAF8F3A71';
	}
	$api_url = 'http://api.follow5.com/api/statuses/update.xml?api_key='.$api_key.'';
	$body = array( 'status' => $status, 'source' => 'FollowWP');
	$headers = array( 'Authorization' => 'Basic '.base64_encode("$username:$password") );
	$request = new WP_Http;   
	$result = $request->request( $api_url , array( 'method' => 'POST', 'body' => $body, 'headers' => $headers ) );
}
add_action('publish_post', 'publish_post_2_follow5', 0);

function publish_post_2_follow5($post_ID){
	$follow5 = get_post_meta($post_ID, 'follow5', true);
	if($follow5) return;
	$status = get_the_title($post_ID).' '.get_permalink($post_ID);
	update_follow5($status);
	add_post_meta($post_ID, 'follow5', 'true', true);
}

?>