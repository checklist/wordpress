<?php

/*
  Plugin Name: Checklist
  Plugin URI: https://checklist.com/
  Description: Turn any list in your blog to a beautiful interactive checklist. Print, Use, Share, Download to Mobile and more.
  Version: 1.1.4
  Author: checklist
  Author URI: https://checklist.com
  License: GPLv3
  Text Domain: checklist-com
  Domain Path: /languages
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

if (!defined('CHECKLIST_ROOT_PATH')) define('CHECKLIST_ROOT_PATH', dirname(__FILE__));

class WP_Checklist {

  // Constructor
    function __construct() {

        add_action( 'admin_menu', array( $this, 'wpa_add_menu' ));
        add_action( 'admin_init', array( $this, 'checklist_com_admin_init'));

        // localization
        add_action( 'init', array( $this, 'plugin_load_textdomain' ) );

        // styles
        add_action( 'admin_enqueue_scripts', array( $this, 'wpa_scripts') );
        add_action( 'enqueue_scripts', array( $this, 'wpf_scripts') );

        // Editor Buttons
        add_filter( 'mce_external_plugins', array( $this, 'wpa_add_buttons' ) );
        add_filter( 'mce_buttons', array( $this, 'wpa_register_buttons' ) );
    }

    /**
    * checklist-buttons ShortCode
    */
    public static function register_checklist_buttons_shortcode($atts){
        $atts = shortcode_atts (
            array (
                'save'       => '',
                'print'       => '',
            ), $atts );

        // get a counter or set a new if does not exist
        static $counter = -1;
        $counter++;

        wp_enqueue_style( 'checklist', plugins_url('css/checklist.css', __FILE__));

        // get the default style from the plugin settings
        $settings = (array) get_option( 'checklist_settings' );

        // source
        $siteUrl = get_home_url();
        $parseUrl = parse_url($siteUrl);
        $host = $parseUrl['host'];
        $source = '&utm_source='.$host.'&utm_medium=referral&utm_campaign=wordpress';

        $saveButton = "";
        $printButton = "";

        // save button
        if ($atts["save"]){
            $saveDefaultText = isset($settings['saveDefaultText']) ? $settings['saveDefaultText'] : esc_html__( 'Save', 'checklist-com' );
            $saveTextColor = isset($settings['saveTextColor']) ? $settings['saveTextColor'] : '#FFFFFF';
            $saveBackgroundColor = isset($settings['saveBackgroundColor']) ? $settings['saveBackgroundColor'] : '#FF5722';
            $saveStyle = 'color:'.$saveTextColor.'; background-color:'.$saveBackgroundColor.';';
            $saveButton = '<a href="https://checklist.com" onclick="window.open(\'https://api.checklist.com/\'+\'save-list?id=checklist-id-'.$counter.$source.'&url='.get_permalink().'\', \'_blank\');return false;" style="'.$saveStyle.'" class="checklist-button" title="Checklist"><img src=\''.plugins_url('images/checklist-icon.php', __FILE__).'?fill='.substr($saveTextColor,1).'\' width="16" height="16" class="svg checklist-image"/> '.$atts["save"].'</a>';
        }

        // print button
        if ($atts["print"]){
            $printTextColor = isset($settings['printTextColor']) ? $settings['printTextColor'] : '#FFFFFF';
            $printBackgroundColor = isset($settings['printBackgroundColor']) ? $settings['printBackgroundColor'] : '#2196F3';
            $printStyle = 'color:'.$printTextColor.'; background-color:'.$printBackgroundColor.';';
            $printButton = '<a href="https://checklist.com" onclick="window.open(\'https://api.checklist.com/\'+\'print?id=checklist-id-'.$counter.$source.'&url='.get_permalink().'\', \'_blank\');return false;" style="'.$printStyle.'" class="checklist-button" title="Printable Checklists"><img src=\''.plugins_url('images/ic_print_white_24px.php', __FILE__).'?fill='.substr($printTextColor,1).'\' width="16" height="16" class="checklist-image"/> '.$atts["print"].'</a>';
        }
        
        return '
            <div class="checklist-buttons" id="checklist-id-'.$counter.'">
                '.$saveButton.$printButton.'
            </div>
        ';
    }

    /**
    * checklist-box ShortCode
    */
    public static function register_checklist_box_shortcode($atts, $content=null) {

        if ($content==null){
            return '';
        }

        $atts = shortcode_atts (
            array (
                'title'       => '',
                'extraurl'       => '',
                'extratitle'       => '',
            ), $atts );

        // get a counter or set a new if does not exist
        static $counter = -1;
        $counter++;

        wp_enqueue_script('checklist', plugins_url('js/checklist.js', __FILE__), array('jquery'),'', true);
        wp_enqueue_style( 'checklist', plugins_url('css/checklist.css', __FILE__));

        // get the default style from the plugin settings
        $settings = (array) get_option( 'checklist_settings' );

        // save button
        $saveDefaultText = isset($settings['saveDefaultText']) ? $settings['saveDefaultText'] : esc_html__( 'Save', 'checklist-com' );
        $saveTextColor = isset($settings['saveTextColor']) ? $settings['saveTextColor'] : '#FFFFFF';
        $saveBackgroundColor = isset($settings['saveBackgroundColor']) ? $settings['saveBackgroundColor'] : '#FF5722';
        $saveStyle = 'color:'.$saveTextColor.'; background-color:'.$saveBackgroundColor.';';

        // print button
        $printTextColor = isset($settings['printTextColor']) ? $settings['printTextColor'] : '#FFFFFF';
        $printBackgroundColor = isset($settings['printBackgroundColor']) ? $settings['printBackgroundColor'] : '#2196F3';
        $printStyle = 'color:'.$printTextColor.'; background-color:'.$printBackgroundColor.';';

        $extraTextColor = isset($settings['extraTextColor']) ? $settings['extraTextColor'] : '#FFFFFF';
        $extraBackgroundColor = isset($settings['extraBackgroundColor']) ? $settings['extraBackgroundColor'] : '#2196F3';
        $extraStyle = 'color:'.$extraTextColor.'; background-color:'.$extraBackgroundColor.';';
        $extraButton = '';
        if (strlen($atts['extraurl'])>0 && strlen($atts['extratitle'])>0){
            $extraButton = '<a href="'.$atts['extraurl'].'" style="'.$extraStyle.'" class="checklist-button" target="_blank" rel="nofollow">'.$atts['extratitle'].'</a>';
        }

        // border
        $borderColor = isset($settings['borderColor']) ? $settings['borderColor'] : '#03A9F4';  
        $borderStyle = isset($settings['borderStyle']) ? $settings['borderStyle'] : 'dashed';
        $style = 'border-style:'.$borderStyle.'; border-color:'.$borderColor.'; padding:20px;';

        // title
        $title = $atts["title"];
        if (strlen($title)>0){
            $title = '<h2 class="checklist-title">'.$title.'</h2>';
        }

        // powered by
        $poweredBy = isset($settings['poweredBy']) ? $settings['poweredBy'] : 0;
        $powered = "";
        if ($poweredBy == 1){
            $powered = '<div class="checklist-powered">Powered By <a href="https://checklist.com" target="_blank">Checklist</a></div>';
        }

        // source
        $siteUrl = get_home_url();
        $parseUrl = parse_url($siteUrl);
        $host = $parseUrl['host'];
        $source = '&utm_source='.$host.'&utm_medium=referral&utm_campaign=wordpress';

        return '
            <div id="checklist-id-'.$counter.'" class="checklist-box" style="'.$style.'">
                '.$title.'
                <div class="">
                    <a href="https://checklist.com" onclick="window.open(\'https://api.checklist.com/\'+\'customize?id=checklist-id-'.$counter.$source.'&url='.get_permalink().'\', \'_blank\');return false;" style="'.$saveStyle.'" class="checklist-button" title="Checklist"><img src=\''.plugins_url('images/checklist-icon.php', __FILE__).'?fill='.substr($saveTextColor,1).'\' width="16" height="16" class="svg checklist-image"/> '.$saveDefaultText.'</a>
                    <a href="https://checklist.com" onclick="window.open(\'https://api.checklist.com/\'+\'print?id=checklist-id-'.$counter.$source.'&url='.get_permalink().'\', \'_blank\');return false;" style="'.$printStyle.'" class="checklist-button" title="Printable Checklists"><img src=\''.plugins_url('images/ic_print_white_24px.php', __FILE__).'?fill='.substr($printTextColor,1).'\' width="16" height="16" class="checklist-image"/> '.esc_html__( 'Print', 'checklist-com' ).'</a>
                    '.$extraButton.'
                </div>
                '.do_shortcode($content).'
                '.$powered.'
            </div>
            ';
    }

    function plugin_load_textdomain() {
        load_plugin_textdomain( 'checklist-com', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
    }

    /*
      * Actions perform at loading of admin menu
      */
    public function wpa_add_menu() {

        add_menu_page( 
            'Checklist',                // The value used to populate the browser's title bar when the menu page is active
            'Checklist',                // The text of the menu in the administrator's sidebar
            'manage_options',           // What roles are able to access the menu
            'checklist_settings',            // The ID used to bind submenu items to this menu 
            array(                      // The callback function used to render this menu
                          $this,
                         'wpa_page_file_path'
                        ), 
            plugins_url('images/icon-white-16.png', __FILE__), // the icon
            123                        // the position (anything above 100 is good)
        );

        add_submenu_page( 
            'checklist_com',             // The ID of the top-level menu page to which this submenu item belongs
            'Checklist Settings',   // The value used to populate the browser's title bar when the menu page is active
            'Settings',                 // The label of this submenu item displayed in the menu
            'manage_options',            // What roles are able to access this submenu item
            'checklist_settings',        // The ID used to represent this submenu item
            array(                       // The callback function used to render the options for this submenu item
                          $this,
                         'wpa_page_file_path'
                        ), 
            plugins_url('images/icon-white-16.png', __FILE__)
        );
       
    }

    /*
     * Actions perform on loading of menu pages
     */
    public static function wpa_page_file_path() {

        $screen = get_current_screen();
        if ( strpos( $screen->base, 'checklist_settings' ) !== false ) {
            include( CHECKLIST_ROOT_PATH . '/includes/checklist-settings.php' );
        } 
        else {
            include( dirname(__FILE__) . '/includes/checklist-dashboard.php' );
        }
    }

    /**
    * Styling: loading stylesheets for the plugin admin.
    */
    public function wpa_scripts( $page ) {

        wp_enqueue_style( 'wp-checklist-admin', plugins_url('css/checklist-admin.css', __FILE__));

        wp_enqueue_style( 'wp-color-picker' ); 
        wp_enqueue_script( 'wp-checklist-js',  plugins_url('js/checklist-admin.js', __FILE__), array('wp-color-picker'), null, true);	
        add_action ( 'after_wp_tiny_mce', array( $this, 'wpa_checklist_tinymce_extra_vars' )) ;
        
    }

    public function wpa_add_buttons($plugin_array){
        $plugin_array['checklist'] = plugins_url( '/js/tinymce-plugin.js',__FILE__ );
        return $plugin_array;    
    }

    public function wpa_register_buttons( $buttons ) {
        array_push( $buttons, 'checklistMenu'); 
        return $buttons;
    }

    public function wpa_checklist_tinymce_extra_vars(){

        $settings = (array) get_option( 'checklist_settings' );
        $saveDefaultText = isset($settings['saveDefaultText']) ? $settings['saveDefaultText'] : esc_html__( 'Save', 'checklist-com' );

        ?>

		<script type="text/javascript">
			var checklist_obj = <?php echo json_encode(
				array(
                    'saveDefault' => $saveDefaultText,
                    'printDefault' =>  'Print',
                    'checklistButtons' => __( 'Save & Print Buttons', 'checklist-com' ),
					'checklistBox' => __( 'Interactive Checklist Box', 'checklist-com' ),
	                'checklistTitle' => __( 'The Title for your Checklist', 'checklist-com' ),
                    'title' => __( 'Title', 'checklist-com' ),
                    'optional' => __( 'Optional', 'checklist-com' ),
                    'extraOptional' => __( 'Optional EXTRA button. Leave empty if not needed', 'checklist-com' ),
                    'extraTitle' => __( 'Extra Title', 'checklist-com' ),
                    'extraUrl' => __( 'Extra URL', 'checklist-com' ),
                    'extraUrlPlaceholder' => __( 'Optional. Format: http://example.com/page', 'checklist-com' ),
				)
				);
			?>;
		</script><?php
    }

    /**
    * Styling: loading stylesheets for the plugin front-end.
    */
    public function wpf_scripts( $page ) {
        wp_enqueue_style( 'wp-checklist', plugins_url('css/checklist.css', __FILE__));
    }

    /**
    * Admin pages 
    */
    public function checklist_com_settings_save_section_callback() {
        echo "<p>".esc_html__( 'Select the default text for the Save button. You can also set the button\'s text and background colors.', 'checklist-com' )."</p>";
    }

    public function checklist_com_settings_print_section_callback() {
        echo "<p>".esc_html__( 'Select the colors for the Print button.', 'checklist-com' )."</p>";
    }

    public function checklist_com_settings_extra_section_callback() {
        echo "<p>".esc_html__( 'The EXTRA button allows you to link to additional info or even further monetize your list.', 'checklist-com' )."</p>";
    }

    public function checklist_com_settings_border_section_callback() {
        echo "<p>".esc_html__( 'Define the default settings for your Checlist box border.', 'checklist-com' )."</p>";
    }

    public function checklist_com_settings_powered_section_callback() {
        echo "<p>".esc_html__( 'Play nice and let your users know in advance that the interactive Checklists are powered by Checklist.com.', 'checklist-com' )."</p>";
    }

    public function checklist_com_settings_text_callback($args) {
        extract( $args );
        echo '<div class="wrap">';
        echo "<input type='text' name='$name' value='$value' />";
        echo (!empty($desc))?' <span class="description">'.$desc.'</span>':'';
        echo "</div>";
    }

    public function checklist_com_settings_color_picker_callback($args) {
        extract( $args );
        echo '<div class="wrap">';
        echo '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" class="checklist-admin-color-picker" style="width:70px;"/>';
        echo (!empty($desc))?' <span class="description checklist-admin-color-description">'.$desc.'</span>':'';
        echo '</div>';
    }

    public function checklist_com_settings_checkbox_callback($args) {
        extract( $args );
        echo '<div class="wrap">';
        echo '<input name="'.$name.'" type="checkbox" value="1" '.checked( "1", $value, false ).' />';
        echo (!empty($desc))?' <span class="description">'.$desc.'</span>':'';
        echo '</div>';
    }

    public function checklist_com_settings_border_style_callback($args) {
        extract( $args );
        $options = array( 'none', 'dotted', 'dashed', 'solid', 'double', 'groove', 'ridge', 'inset', 'outset' );
        echo '<div class="wrap">';
        echo "<select class='post_form' name='".$name."' value='true'>";
		for( $i=0; $i<count($options); $i++ ) {
            echo '<option '
             . ( $value == $options[$i] ? 'selected="selected"' : '' ) . '>' 
             . $options[$i] 
             . '</option>';
        }
		echo "</select>";
        echo (!empty($desc))?' <span class="description">'.$desc.'</span>':'';
        echo '</div>';
    }

    public function checklist_com_admin_init(){

        // If the theme options don't exist, create them.
	    if( false == get_option( 'checklist_settings' ) ) {	
            // error_log("checklist_settings options");
	    	add_option( 'checklist_settings' );
	    }

        $settings = (array) get_option( 'checklist_settings' );

	    // First, we register a section. This is necessary since all future options must belong to a 
	    add_settings_section(
	    	'checklist_settings_save',			        // ID used to identify this section and with which to register options
            esc_html__( 'Save Button', 'checklist-com' ),	 // Title to be displayed on the administration page
            array( $this, 'checklist_com_settings_save_section_callback'),	// Callback used to render the description of the section
            'checklist_settings'		                // Page on which to add this section of options
        );
        
        $saveDefaultText = isset($settings['saveDefaultText']) ? $settings['saveDefaultText'] : 'Save';
        add_settings_field(	
            'saveDefaultText',				                    // ID used to identify the field throughout the theme		
            esc_html__( 'Default Text', 'checklist-com' ),							        // The label to the left of the option interface element
            array( $this, 'checklist_com_settings_text_callback'),  // The name of the function responsible for rendering the option interface	
            'checklist_settings',                       // The page on which this option will be displayed
            'checklist_settings_save',                  // The section it belongs to
            array(                                      // args to be passed to call back function
                'name' => 'checklist_settings[saveDefaultText]' ,
                'value' => $saveDefaultText,
                'desc' => esc_html__( 'This is the default text for the Save button. You can set it to anything you like.', 'checklist-com' )
            )                                        
        );

        $saveTextColor = isset($settings['saveTextColor']) ? $settings['saveTextColor'] : '#FFFFFF';
        add_settings_field(	
            'saveTextColor',				                    // ID used to identify the field throughout the theme		
            esc_html__( 'Text Color', 'checklist-com' ),							        // The label to the left of the option interface element
            array( $this, 'checklist_com_settings_color_picker_callback'),  // The name of the function responsible for rendering the option interface	
            'checklist_settings',                       // The page on which this option will be displayed
            'checklist_settings_save',                  // The section it belongs to
            array(                                      // args to be passed to call back function
                'name' => 'checklist_settings[saveTextColor]' ,
                'value' => $saveTextColor,
                'id' => 'checklist-picker-saveTextColor',
                'desc' => esc_html__( 'This is the text color of the Save button', 'checklist-com' )
            )                                        
        );

        $saveBackgroundColor = isset($settings['saveBackgroundColor']) ? $settings['saveBackgroundColor'] : '#FF5722';
        add_settings_field(	
            'saveBackgroundColor',				                    // ID used to identify the field throughout the theme		
            esc_html__( 'Background Color', 'checklist-com' ),							        // The label to the left of the option interface element
            array( $this, 'checklist_com_settings_color_picker_callback'),  // The name of the function responsible for rendering the option interface	
            'checklist_settings',                       // The page on which this option will be displayed
            'checklist_settings_save',                  // The section it belongs to
            array(                                      // args to be passed to call back function
                'name' => 'checklist_settings[saveBackgroundColor]' ,
                'value' => $saveBackgroundColor,
                'id' => 'checklist-picker-saveBackgroundColor',
                'desc' => esc_html__( 'This is the background color of the Save button', 'checklist-com' )
            )                                        
        );

	    add_settings_section(
	    	'checklist_settings_print',			        // ID used to identify this section and with which to register options
            esc_html__( 'Print Button', 'checklist-com' ),					        // Title to be displayed on the administration page
            array( $this, 'checklist_com_settings_print_section_callback'),	// Callback used to render the description of the section
            'checklist_settings'		                // Page on which to add this section of options
        );
        
        $printTextColor = isset($settings['printTextColor']) ? $settings['printTextColor'] : '#FFFFFF';
        add_settings_field(	
            'printTextColor',				                    // ID used to identify the field throughout the theme		
            esc_html__( 'Text Color', 'checklist-com' ),							        // The label to the left of the option interface element
            array( $this, 'checklist_com_settings_color_picker_callback'),  // The name of the function responsible for rendering the option interface	
            'checklist_settings',                       // The page on which this option will be displayed
            'checklist_settings_print',                  // The section it belongs to
            array(                                      // args to be passed to call back function
                'name' => 'checklist_settings[printTextColor]' ,
                'value' => $printTextColor,
                'id' => 'checklist-picker-printTextColor',
                'desc' => esc_html__( 'This is the text color of the Print button', 'checklist-com' )
            )                                        
        );

        $printBackgroundColor = isset($settings['printBackgroundColor']) ? $settings['printBackgroundColor'] : '#2196F3';
        add_settings_field(	
            'printBackgroundColor',				                    // ID used to identify the field throughout the theme		
            esc_html__( 'Background Color', 'checklist-com' ),							        // The label to the left of the option interface element
            array( $this, 'checklist_com_settings_color_picker_callback'),  // The name of the function responsible for rendering the option interface	
            'checklist_settings',                       // The page on which this option will be displayed
            'checklist_settings_print',                  // The section it belongs to
            array(                                      // args to be passed to call back function
                'name' => 'checklist_settings[printBackgroundColor]' ,
                'value' => $printBackgroundColor,
                'id' => 'checklist-picker-printBackgroundColor',
                'desc' => esc_html__( 'This is the background color of the Print button', 'checklist-com' )
            )                                        
        );

        add_settings_section(
	    	'checklist_settings_extra',			        // ID used to identify this section and with which to register options
            esc_html__( 'Extra Button', 'checklist-com' ),					        // Title to be displayed on the administration page
            array( $this, 'checklist_com_settings_extra_section_callback'),	// Callback used to render the description of the section
            'checklist_settings'		                // Page on which to add this section of options
        );
        
        $extraTextColor = isset($settings['extraTextColor']) ? $settings['extraTextColor'] : '#FFFFFF';
        add_settings_field(	
            'extraTextColor',				                    // ID used to identify the field throughout the theme		
            esc_html__( 'Text Color', 'checklist-com' ),							        // The label to the left of the option interface element
            array( $this, 'checklist_com_settings_color_picker_callback'),  // The name of the function responsible for rendering the option interface	
            'checklist_settings',                       // The page on which this option will be displayed
            'checklist_settings_extra',                  // The section it belongs to
            array(                                      // args to be passed to call back function
                'name' => 'checklist_settings[extraTextColor]' ,
                'value' => $extraTextColor,
                'id' => 'checklist-picker-extraTextColor',
                'desc' => esc_html__( 'This is the text color of the extra button', 'checklist-com' )
            )                                        
        );

        $extraBackgroundColor = isset($settings['extraBackgroundColor']) ? $settings['extraBackgroundColor'] : '#2196F3';
        add_settings_field(	
            'extraBackgroundColor',				                    // ID used to identify the field throughout the theme		
            esc_html__( 'Background Color', 'checklist-com' ),							        // The label to the left of the option interface element
            array( $this, 'checklist_com_settings_color_picker_callback'),  // The name of the function responsible for rendering the option interface	
            'checklist_settings',                       // The page on which this option will be displayed
            'checklist_settings_extra',                  // The section it belongs to
            array(                                      // args to be passed to call back function
                'name' => 'checklist_settings[extraBackgroundColor]' ,
                'value' => $extraBackgroundColor,
                'id' => 'checklist-picker-extraBackgroundColor',
                'desc' => esc_html__( 'This is the background color of the extra button', 'checklist-com' )
            )                                        
        );

        add_settings_section(
	    	'checklist_settings_border',			        // ID used to identify this section and with which to register options
            esc_html__( 'Border', 'checklist-com' ),					        // Title to be displayed on the administration page
            array( $this, 'checklist_com_settings_border_section_callback'),	// Callback used to render the description of the section
            'checklist_settings'		                // Page on which to add this section of options
        );
        
        $borderColor = isset($settings['borderColor']) ? $settings['borderColor'] : '#03A9F4';
        add_settings_field(	
            'borderColor',				                    // ID used to identify the field throughout the theme		
            esc_html__( 'Border Color', 'checklist-com' ),							        // The label to the left of the option interface element
            array( $this, 'checklist_com_settings_color_picker_callback'),  // The name of the function responsible for rendering the option interface	
            'checklist_settings',                       // The page on which this option will be displayed
            'checklist_settings_border',                  // The section it belongs to
            array(                                      // args to be passed to call back function
                'name' => 'checklist_settings[borderColor]' ,
                'value' => $borderColor,
                'id' => 'checklist-picker-borderColor',
                'desc' => esc_html__( 'This is the color of border around the checklist', 'checklist-com' )
            )                                        
        );

        $borderStyle = isset($settings['borderStyle']) ? $settings['borderStyle'] : 'dashed';
        add_settings_field(	
            'borderStyle',				                    // ID used to identify the field throughout the theme		
            esc_html__( 'Border Style', 'checklist-com' ),							        // The label to the left of the option interface element
            array( $this, 'checklist_com_settings_border_style_callback'),  // The name of the function responsible for rendering the option interface	
            'checklist_settings',                       // The page on which this option will be displayed
            'checklist_settings_border',                  // The section it belongs to
            array(                                      // args to be passed to call back function
                'name' => 'checklist_settings[borderStyle]' ,
                'value' => $borderStyle,
                'desc' => esc_html__( 'This is the style of the border around the checklist', 'checklist-com' )
            )                                        
        );

        add_settings_section(
	    	'checklist_settings_powered',			        // ID used to identify this section and with which to register options
            'Powered By',					        // Title to be displayed on the administration page
            array( $this, 'checklist_com_settings_powered_section_callback'),	// Callback used to render the description of the section
            'checklist_settings'		                // Page on which to add this section of options
        );
        
        $poweredBy = isset($settings['poweredBy']) ? $settings['poweredBy'] : 0;
        add_settings_field(	
            'poweredBy',				                    // ID used to identify the field throughout the theme		
            'Powered By',							        // The label to the left of the option interface element
            array( $this, 'checklist_com_settings_checkbox_callback'),  // The name of the function responsible for rendering the option interface	
            'checklist_settings',                       // The page on which this option will be displayed
            'checklist_settings_powered',                  // The section it belongs to
            array(                                      // args to be passed to call back function
                'name' => 'checklist_settings[poweredBy]' ,
                'value' => $poweredBy,
                'desc' => esc_html__( 'Let people know the Checklists are powered by Checklist.com', 'checklist-com' )
            )                                        
        );


        // Finally, we register the fields with WordPress
        register_setting(
            'checklist_group',
            'checklist_settings'
        );
    }

    /*
     * Actions perform on activation of plugin
     */
    public static function wpa_install() {
    }

    /*
     * Actions perform on de-activation of plugin
     */
    public static function wpa_uninstall() {
        // delete any settings we have made
        unregister_setting(
            'checklist_group',
            'checklist_settings'
        );
    }
}
new WP_Checklist();

register_activation_hook( __FILE__, array( 'WP_Checklist', 'wpa_install' ) );
register_deactivation_hook( __FILE__, array( 'WP_Checklist', 'wpa_uninstall' ) );

add_shortcode('checklist-box', array('WP_Checklist', 'register_checklist_box_shortcode') );
add_shortcode('checklist-buttons', array('WP_Checklist', 'register_checklist_buttons_shortcode') );
?>