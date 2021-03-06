<?php

class Kkd_Pff_Paystack_Admin {

	private $plugin_name;
	private $version;
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('admin_menu' , 'kkd_pff_paystack_add_settings_page');
		add_action( 'admin_init', 'kkd_pff_paystack_register_setting_page' );

		function kkd_pff_paystack_add_settings_page() {
			add_submenu_page('edit.php?post_type=paystack_form', 'Api Keys Settings', 'Api Keys Settings', 'edit_posts', basename(__FILE__), 'kkd_pff_paystack_setting_page');
		}
		function kkd_pff_paystack_register_setting_page() {
			register_setting( 'kkd-pff-paystack-settings-group', 'mode' );
			register_setting( 'kkd-pff-paystack-settings-group', 'tsk' );
			register_setting( 'kkd-pff-paystack-settings-group', 'tpk' );
			register_setting( 'kkd-pff-paystack-settings-group', 'lsk' );
			register_setting( 'kkd-pff-paystack-settings-group', 'lpk' );
		}
		function kkd_pff_paystack_txncheck($name,$txncharge){
			if ($name == $txncharge) {
				$result = "selected";
			}else{
				$result = "";
			}
			return $result;
		}
		function kkd_pff_paystack_setting_page() {
			?>
			 <h1>Paystack Forms API KEYS Settings!</h1>
			 <form method="post" action="options.php">
				    <?php settings_fields( 'kkd-pff-paystack-settings-group' ); do_settings_sections( 'kkd-pff-paystack-settings-group' ); ?>
				    <table class="form-table paystack_setting_page">
								<tr valign="top">
								<th scope="row">Mode</th>

								<td>
									<select class="form-control" name="mode" id="parent_id">
										<option value="live" <?php echo kkd_pff_paystack_txncheck('live',esc_attr( get_option('mode') )) ?>>Live Mode</option>
										<option value="test" <?php echo kkd_pff_paystack_txncheck('test',esc_attr( get_option('mode') )) ?>>Test Mode</option>
									</select>
								</tr>
								<tr valign="top">
				        <th scope="row">Test Secret Key</th>
				        <td>

								<input type="text" name="tsk" value="<?php echo esc_attr( get_option('tsk') ); ?>" /></td>
				        </tr>

				        <tr valign="top">
				        <th scope="row">Test Public Key</th>
				        <td><input type="text" name="tpk" value="<?php echo esc_attr( get_option('tpk') ); ?>" /></td>
				        </tr>

				        <tr valign="top">
				        <th scope="row">Live Secret Key</th>
				        <td><input type="text" name="lsk" value="<?php echo esc_attr( get_option('lsk') ); ?>" /></td>
				        </tr>
								<tr valign="top">
				        <th scope="row">Live Public Key</th>
				        <td><input type="text" name="lpk" value="<?php echo esc_attr( get_option('lpk') ); ?>" /></td>
				        </tr>
				    </table>

			    <?php submit_button(); ?>

			</form>
			 <?php
		}
		add_action( 'init', 'register_kkd_pff_paystack' );
		function register_kkd_pff_paystack() {

		    $labels = array(
		        'name' => _x( 'Paystack Forms', 'paystack_form' ),
		        'singular_name' => _x( 'Paystack Form', 'paystack_form' ),
		        'add_new' => _x( 'Add New', 'paystack_form' ),
		        'add_new_item' => _x( 'Add Paystack Form', 'paystack_form' ),
		        'edit_item' => _x( 'Edit Paystack Form', 'paystack_form' ),
		        'new_item' => _x( 'Paystack Form', 'paystack_form' ),
		        'view_item' => _x( 'View Paystack Form', 'paystack_form' ),
		        'search_items' => _x( 'Search Paystack Forms', 'paystack_form' ),
		        'not_found' => _x( 'No Paystack Forms found', 'paystack_form' ),
		        'not_found_in_trash' => _x( 'No Paystack Forms found in Trash', 'paystack_form' ),
		        'parent_item_colon' => _x( 'Parent Paystack Form:', 'paystack_form' ),
		        'menu_name' => _x( 'Paystack Forms', 'paystack_form' ),
		    );

		    $args = array(
		        'labels' => $labels,
		        'hierarchical' => true,
		        'description' => 'Paystack Forms filterable by genre',
		        'supports' => array( 'title', 'editor'),
		        'public' => true,
		        'show_ui' => true,
		        'show_in_menu' => true,
		        'menu_position' => 5,
		        'menu_icon' => plugins_url('../images/logo.png', __FILE__),
		        'show_in_nav_menus' => true,
		        'publicly_queryable' => true,
		        'exclude_from_search' => false,
		        'has_archive' => false,
		        'query_var' => true,
		        'can_export' => true,
		        'rewrite' => false,
		        'comments' => false,
		        'capability_type' => 'post'
		    );
		    register_post_type( 'paystack_form', $args );
		}
		add_filter('user_can_richedit', 'kkd_pff_paystack_disable_wyswyg');

		function kkd_pff_paystack_add_view_payments($actions, $post){

		    if(get_post_type() === 'paystack_form'){
					unset($actions['view']);
					unset($actions['quick edit']);
		        $url = add_query_arg(
		            array(
		              'post_id' => $post->ID,
		              'action' => 'submissions',
		            )
		          );
		    $actions['export'] = '<a href="' . admin_url('admin.php?page=submissions&form='.$post->ID) . '" target="_blank" >View Payments</a>';
		    }
		    return $actions;
		}
		add_filter( 'page_row_actions', 'kkd_pff_paystack_add_view_payments', 10, 2 );


		function kkd_pff_paystack_remove_fullscreen( $qtInit ) {
				$qtInit['buttons'] = 'fullscreen';
				return $qtInit;
		}
		function kkd_pff_paystack_disable_wyswyg( $default ){
	    global $post_type, $_wp_theme_features;;


	    if ($post_type == 'paystack_form') {
	        echo "<style>#edit-slug-box,#message p > a{display:none;}</style>";
	      add_action("admin_print_footer_scripts", "kkd_pff_paystack_shortcode_button_script");
	      add_filter( 'user_can_richedit' , '__return_false', 50 );
	      add_action( 'wp_dashboard_setup', 'kkd_pff_paystack_remove_dashboard_widgets' );
				remove_action( 'media_buttons', 'media_buttons' );
				remove_meta_box( 'postimagediv','post','side' );
				add_filter('quicktags_settings', 'kkd_pff_paystack_remove_fullscreen');
			}

	    return $default;
	  }
	  function kkd_pff_paystack_remove_dashboard_widgets() {
	  	remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );   // Right Now
	  	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' ); // Recent Comments
	  	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );  // Incoming Links
	  	remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );   // Plugins
	  	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );  // Quick Press
	  	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );  // Recent Drafts
	  	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );   // WordPress blog
	  	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );   // Other WordPress News
	  	// use 'dashboard-network' as the second parameter to remove widgets from a network dashboard.
	  }
	  add_filter( 'manage_edit-paystack_form_columns', 'kkd_pff_paystack_edit_dashboard_header_columns' ) ;

	  function kkd_pff_paystack_edit_dashboard_header_columns( $columns ) {

	  	$columns = array(
	  		'cb' => '<input type="checkbox" />',
	  		'title' => __( 'Name' ),
				'shortcode' => __( 'Shortcode' ),
	  		'payments' => __( 'Payments' ),
	  		'date' => __( 'Date' )
	  	);

	  	return $columns;
	  }
		add_action( 'manage_paystack_form_posts_custom_column', 'kkd_pff_paystack_dashboard_table_data', 10, 2 );

		function kkd_pff_paystack_dashboard_table_data( $column, $post_id ) {
			global $post,$wpdb;
			$table = $wpdb->prefix . KKD_PFF_PAYSTACK_TABLE;

			switch( $column ) {
				case 'shortcode' :
					echo '<span class="shortcode">
					<input type="text" class="large-text code" value="[pff-paystack id=&quot;'.$post_id.'&quot;]"
					readonly="readonly" onfocus="this.select();"></span>';

					break;
				case 'payments':

						$count_query = 'select count(*) from '.$table.' WHERE post_id = "'.$post_id.'" AND paid = "1"';
						$num = $wpdb->get_var($count_query);

						echo '<a href="'.admin_url('admin.php?page=submissions&form='.$post_id) .'">'. $num.'</a>';
						break;
				default :
					break;
			}
		}
		add_filter( 'default_content', 'kkd_pff_paystack_editor_content', 10, 2 );

		function kkd_pff_paystack_editor_content( $content, $post ) {

		    switch( $post->post_type ) {
		        case 'paystack_form':
		            $content = '[text name="Phone Number"]';
		        break;
		        default:
		            $content = '';
		        break;
		    }

		    return $content;
		}
		/////
		function kkd_pff_paystack_editor_help_metabox( $post ) {

		    do_meta_boxes( null, 'custom-metabox-holder', $post );
		}
		add_action( 'edit_form_after_title', 'kkd_pff_paystack_editor_help_metabox' );
		function kkd_pff_paystack_editor_add_help_metabox() {

				add_meta_box(
					'awesome_metabox_id',
					'Help Section',
					'kkd_pff_paystack_editor_help_metabox_details',
					'paystack_form',
					'custom-metabox-holder'	//Look what we have here, a new context
				);

		}
		add_action( 'add_meta_boxes', 'kkd_pff_paystack_editor_add_help_metabox' );

		function kkd_pff_paystack_editor_help_metabox_details( $post ) {
			echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
	  	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

			?>
			<div class="awesome-meta-admin">
				Email and Full Name field is added automatically, no need to include that.<br /><br />
				To make an input field compulsory add <code> required="required" </code> to the shortcode <br /><br />
				It should look like this <code> [text name="Full Name" required="required" ]</code><br /><br />

				<b style="color:red;">Warning:</b> Using the file input field may cause data overload on your server.
				 Be sure you have enough server space before using it. You also have the ability to set file upload limits.

			</div>

		<?php
		}

		add_action( 'add_meta_boxes', 'kkd_pff_paystack_editor_add_extra_metaboxes' );
	  function kkd_pff_paystack_editor_add_extra_metaboxes() {

			add_meta_box('kkd_pff_paystack_editor_add_form_data', 'Extra Form Description', 'kkd_pff_paystack_editor_add_form_data', 'paystack_form', 'normal', 'default');
			add_meta_box('kkd_pff_paystack_editor_add_recur_data', 'Recurring Payment', 'kkd_pff_paystack_editor_add_recur_data', 'paystack_form', 'side', 'default');
			add_meta_box('kkd_pff_paystack_editor_add_email_data', 'Email Receipt Settings', 'kkd_pff_paystack_editor_add_email_data', 'paystack_form', 'normal', 'default');

	  }


	  function kkd_pff_paystack_editor_add_form_data() {
	  	global $post;

	  	// Noncename needed to verify where the data originated
	  	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
	  	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	  	// Get the location data if its already been entered
			$amount = get_post_meta($post->ID, '_amount', true);
	  	$paybtn = get_post_meta($post->ID, '_paybtn', true);
			$successmsg = get_post_meta($post->ID, '_successmsg', true);
			$txncharge = get_post_meta($post->ID, '_txncharge', true);
			$loggedin = get_post_meta($post->ID, '_loggedin', true);
			$currency = get_post_meta($post->ID, '_currency', true);
	    $filelimit = get_post_meta($post->ID, '_filelimit', true);

			if ($amount == "") {$amount = 0;}
			if ($filelimit == "") {$filelimit = 2;}
			if ($paybtn == "") {$paybtn = 'Pay';}
			if ($successmsg == "") {$successmsg = 'Thank you for paying!';}
			if ($currency == "") {$currency = 'NGN';}
			if ($txncharge == "") {$txncharge = 'merchant';}
	  	// Echo out the field
			echo '<p>Currency:</p>';
	  	echo '<input type="text" name="_currency" value="' . $currency  . '" class="widefat" />
			<small>We currently support only payments in Naira(NGN).</small>';
			echo '<p>Amount to be paid(Set 0 for customer input):</p>';
	  	echo '<input type="number" name="_amount" value="' . $amount  . '" class="widefat pf-number" />';
			echo '<p>Pay button Description:</p>';
	  	echo '<input type="text" name="_paybtn" value="' . $paybtn  . '" class="widefat" />';
			echo '<p>Transaction Charges:</p>';
			echo '<select class="form-control" name="_txncharge" id="parent_id" style="width:100%;">
							<option value="merchant"'.kkd_pff_paystack_txncheck('merchant',$txncharge).'>Merchant Pays(Include in fee)</option>
							<option value="customer" '.kkd_pff_paystack_txncheck('customer',$txncharge).'>Client Pays(Extra Fee added) - 1.55%+ NGN100 if above NGN2,500 </option>
						</select>';
			echo '<p>User logged In:</p>';
			echo '<select class="form-control" name="_loggedin" id="parent_id" style="width:100%;">
							<option value="no" '.kkd_pff_paystack_txncheck('no',$loggedin).'>User must not be logged in</option>
							<option value="yes"'.kkd_pff_paystack_txncheck('yes',$loggedin).'>User must be logged In</option>
						</select>';
	  	echo '<p>Success Message after Payment</p>';
	    echo '<textarea rows="3"  name="_successmsg"  class="widefat" >'.$successmsg.'</textarea>';
			echo '<p>File Upload Limit(MB):</p>';
	  	echo '<input ttype="number" name="_filelimit" value="' . $filelimit  . '" class="widefat  pf-number" />';

	  }
		function kkd_pff_paystack_editor_add_email_data() {
	  	global $post;

	  	// Noncename needed to verify where the data originated
	  	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
	  	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	  	// Get the location data if its already been entered
			$subject = get_post_meta($post->ID, '_subject', true);
	  	$heading = get_post_meta($post->ID, '_heading', true);
			$message = get_post_meta($post->ID, '_message', true);
			$sendreceipt = get_post_meta($post->ID, '_sendreceipt', true);

			if ($subject == "") {$subject = 'Thank you for your payment';}
			if ($sendreceipt == "") {$sendreceipt = 'yes';}
			if ($heading == "") {$heading = "We've received your payment";}
			if ($message == "") {$message = 'Your payment was received and we appreciate it.';}
	  	// Echo out the field
			echo '<p>Send Email Receipt:</p>';
			echo '<select class="form-control" name="_sendreceipt" id="parent_id" style="width:100%;">
							<option value="no" '.kkd_pff_paystack_txncheck('no',$sendreceipt).'>Don\'t send</option>
							<option value="yes" '.kkd_pff_paystack_txncheck('yes',$sendreceipt).'>Send</option>
						</select>';
			echo '<p>Email Subject:</p>';
	  	echo '<input type="text" name="_subject" value="' . $subject  . '" class="widefat" />';
			echo '<p>Email Heading:</p>';
	  	echo '<input type="text" name="_heading" value="' . $heading  . '" class="widefat" />';
			echo '<p>Email Body/Message:</p>';
	    echo '<textarea rows="6"  name="_message"  class="widefat" >'.$message.'</textarea>';

	  }
		function kkd_pff_paystack_editor_add_recur_data() {
	  	global $post;

	  	// Noncename needed to verify where the data originated
	  	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
	  	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	  	// Get the location data if its already been entered
			$recur = get_post_meta($post->ID, '_recur', true);
	  	$recurplan = get_post_meta($post->ID, '_recurplan', true);

			if ($recur == "") {$recur = 'no';}
			if ($recurplan == "") {$recurplan = '';}
			// Echo out the field
			echo '<p>Reccuring Payment:</p>';
			echo '<select class="form-control" name="_recur" style="width:100%;">
							<option value="no" '.kkd_pff_paystack_txncheck('no',$recur).'>None</option>
							<option value="optional" '.kkd_pff_paystack_txncheck('optional',$recur).'>Optional Recurring</option>
							<option value="plan" '.kkd_pff_paystack_txncheck('plan',$recur).'>Paystack Plan</option>
						</select>';
			echo '<p>Paystack Recur Plan code:</p>';
	  	echo '<input type="text" name="_recurplan" value="' . $recurplan  . '" class="widefat" />
				<small>Plan amount must match amount on extra form description.</small>';

	  }
		function kkd_pff_paystack_save_data($post_id, $post) {

			if ( !wp_verify_nonce( @$_POST['eventmeta_noncename'], plugin_basename(__FILE__) )) {
			return $post->ID;
			}

			// Is the user allowed to edit the post or page?
			if ( !current_user_can( 'edit_post', $post->ID )){
				return $post->ID;
			}

		  $form_meta['_amount'] = $_POST['_amount'];
			$form_meta['_paybtn'] = $_POST['_paybtn'];
			$form_meta['_currency'] = $_POST['_currency'];
			$form_meta['_successmsg'] = $_POST['_successmsg'];
			$form_meta['_txncharge'] = $_POST['_txncharge'];
			$form_meta['_loggedin'] = $_POST['_loggedin'];
			$form_meta['_filelimit'] = $_POST['_filelimit'];
			///
			$form_meta['_subject'] = $_POST['_subject'];
			$form_meta['_heading'] = $_POST['_heading'];
			$form_meta['_message'] = $_POST['_message'];
			$form_meta['_sendreceipt'] = $_POST['_sendreceipt'];
			///
			$form_meta['_recur'] = $_POST['_recur'];
			$form_meta['_recurplan'] = $_POST['_recurplan'];

			// Add values of $form_meta as custom fields

			foreach ($form_meta as $key => $value) { // Cycle through the $form_meta array!
				if( $post->post_type == 'revision' ) return; // Don't store custom data twice
				$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
				if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
					update_post_meta($post->ID, $key, $value);
				} else { // If the custom field doesn't have a value
					add_post_meta($post->ID, $key, $value);
				}
				if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
			}

		}
		add_action('save_post', 'kkd_pff_paystack_save_data', 1, 2);




	}

	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/paystack-forms-admin.css', array(), $this->version, 'all' );

	}
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/paystack-forms-admin.js', array( 'jquery' ), $this->version, false );

	}

}

add_action( 'admin_menu', 'kkd_pff_paystack_register_newpage' );
function kkd_pff_paystack_register_newpage(){
		add_menu_page('paystack', 'paystack', 'administrator','submissions', 'kkd_pff_paystack_payment_submissions');
		remove_menu_page('submissions');
}
function kkd_pff_paystack_payment_submissions(){
	$id = $_GET['form'];
	$obj = get_post($id);
	if ($obj->post_type == 'paystack_form') {
		 $amount = get_post_meta($id,'_amount',true);
		 $thankyou = get_post_meta($id,'_successmsg',true);
		 $paybtn = get_post_meta($id,'_paybtn',true);
		 $loggedin = get_post_meta($id,'_loggedin',true);
		 $txncharge = get_post_meta($id,'_txncharge',true);

		 echo "<h1>".$obj->post_title." Payments</h1>";
		 $exampleListTable = new Kkd_Pff_Paystack_Payments_List_Table();
		 $exampleListTable->prepare_items();
		 ?>
		 <div class="wrap">
				 <div id="icon-users" class="icon32"></div>
				 <?php $exampleListTable->display(); ?>
		 </div>
		 <?php

	}
}
class Kkd_Pff_Paystack_Wp_List_Table{
    public function __construct(){
        add_action( 'admin_menu', array($this, 'add_menu_example_list_table_page' ));
    }
		public function add_menu_example_list_table_page(){
        add_menu_page( '', '', 'manage_options', 'example-list-table.php', array($this, 'list_table_page') );
    }
		public function list_table_page(){
        $exampleListTable = new Example_List_Table();
				$exampleListTable->prepare_items($data);
        ?>
					<div class="wrap">
              <div id="icon-users" class="icon32"></div>
              <?php $exampleListTable->display(); ?>
          </div>
        <?php
    }
}


if( ! class_exists( 'WP_List_Table' ) ) {
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
function format_data($data){
	$new = json_decode($data);
	$text = '';
	if (array_key_exists("0", $new)) {
		foreach ($new as $key => $item) {
			if ($item->type == 'text') {
				$text.= '<b>'.$item->display_name."</b> :".$item->value."<br />";
			}else{
				$text.= '<b>'.$item->display_name."</b> : <a target='_blank' href='".$item->value."'>link</a><br />";
			}

		}
	}else{
		$text = '';
		if (count($new) > 0) {
			foreach ($new as $key => $item) {
				$text.= '<b>'.$key."</b> :".$item."<br />";
			}
		}
	}
	//
	return $text;
}

class Kkd_Pff_Paystack_Payments_List_Table extends WP_List_Table{
   public function prepare_items(){
		 	$post_id = $_GET['form'];
			$currency = get_post_meta($post_id,'_currency',true);

			global $wpdb;

		  $table = $wpdb->prefix.KKD_PFF_PAYSTACK_TABLE;
			$data = array();
			$alldbdata = $wpdb->get_results("SELECT * FROM $table WHERE (post_id = '".$post_id."' AND paid = '1')");

			foreach ($alldbdata as $key => $dbdata) {
				$newkey = $key+1;
				$data[] = array(
							'id'  => $newkey,
							'email' => '<a href="mailto:'.$dbdata->email.'">'.$dbdata->email.'</a>',
		          'amount' => $currency.'<b>'.number_format($dbdata->amount).'</b>',
		          'txn_code' => $dbdata->txn_code,
		          'metadata' => format_data($dbdata->metadata),
		          'date'  => $dbdata->created_at
				);
			}

       $columns = $this->get_columns();
       $hidden = $this->get_hidden_columns();
       $sortable = $this->get_sortable_columns();
       usort( $data, array( &$this, 'sort_data' ) );
       $perPage = 20;
       $currentPage = $this->get_pagenum();
       $totalItems = count($data);
       $this->set_pagination_args( array(
           'total_items' => $totalItems,
           'per_page'    => $perPage
       ) );
       $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
       $this->_column_headers = array($columns, $hidden, $sortable);
       $this->items = $data;
   }
	 public function get_columns(){
       $columns = array(
           'id'  => '#',
           'email' => 'Email',
           'amount' => 'Amount',
           'txn_code' => 'Txn Code',
           'metadata' => 'Data',
           'date'  => 'Date'
       );
       return $columns;
   }
   /**
    * Define which columns are hidden
    *
    * @return Array
    */
   public function get_hidden_columns(){
       return array();
   }
  	public function get_sortable_columns(){
       return array('email' => array('email', false),'date' => array('date', false),'amount' => array('amount', false));
   	}
   /**
    * Get the table data
    *
    * @return Array
    */
   private function table_data($data){

       return $data;
   }
   /**
    * Define what data to show on each column of the table
    *
    * @param  Array $item        Data
    * @param  String $column_name - Current column name
    *
    * @return Mixed
    */
   public function column_default( $item, $column_name ){
       switch( $column_name ) {
           case 'id':
           case 'email':
           case 'amount':
           case 'txn_code':
           case 'metadata':
           case 'date':
               return $item[ $column_name ];
           default:
               return print_r( $item, true ) ;
       }
   }

     /**
    * Allows you to sort the data by the variables set in the $_GET
    *
    * @return Mixed
    */
   private function sort_data( $a, $b ){
       $orderby = 'date';
       $order = 'asc';
       if(!empty($_GET['orderby'])){
           $orderby = $_GET['orderby'];
       }
       if(!empty($_GET['order'])){
           $order = $_GET['order'];
       }
       $result = strcmp( $a[$orderby], $b[$orderby] );
       if($order === 'asc'){
           return $result;
       }
       return -$result;
   }
}

?>
