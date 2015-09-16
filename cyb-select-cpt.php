<?php
/**
* Plugin Name: Cyb Select CPT
* Plugin URI: http://www.cybernaute.ch/plugins/cyb-select-cpt/
* Description: Let you select which Custom Post Type to display on homepage, feed and author page.
* Author: Cybernaute.ch
* Version: 0.1
* Author URI: http://www.cybernaute.ch
* Text Domain: cyb-select-cpt
* Domain Path: /languages
* License: GPLv2 or later
*/

if (! function_exists ( 'add_action' )) {
	header ( 'Status: 403 Forbidden' );
	header ( 'HTTP/1.1 403 Forbidden' );
	exit ();
}

class CybSelectCPT
{

    public function __construct()
    {
        /* Add an admin menu */
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
        
        /* Register the settings */
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        /* Add selected CPT to homepage, feeds and author page */
        add_filter( 'pre_get_posts', array( $this, 'filter_cpt' ) );

        /* Load the translations */
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
    }
    
    public function add_admin_menu()
    {
        add_submenu_page( 'options-general.php', __( 'Select Custom Post Types', 'cyb-select-cpt' ), __( 'Select CPT', 'cyb-select-cpt' ), 'manage_options', 'cyb_select-cpt', array( $this, 'menu_html' ) );
    }
    
    public function menu_html()
    {
        echo '<h1>' . get_admin_page_title() . '</h1>';
        echo '<p>' . __( 'Select which Custom Post Types you want to add on your homepage, feeds and authors page.', 'cyb-select-cpt' ) . '</p>';
        ?>
        <!-- Crée le formulaire qui sera utilisé pour afficher nos options -->
        <form method="post" action="options.php">
            <?php settings_fields( 'cyb_select_cpt_settings' ); ?>
            <?php do_settings_sections( 'cyb_select_cpt_settings' ); ?>
            <?php submit_button(); ?>
        </form>
        <?php  
    }
    
    public function register_settings()
    {
        register_setting( 'cyb_select_cpt_settings', 'cyb_select_cpt_options' );
        
        /* For homepage */
        add_settings_section( 'cyb_select_cpt_home_section', __( 'Custom Post Types for your homepage', 'cyb-select-cpt' ), array( $this, 'section_home_html' ), 'cyb_select_cpt_settings' );
        
        add_settings_field( 'cyb_home_cpt', __( 'Custom Post Type for homepage', 'cyb-select-cpt' ), array( $this, 'home_html' ), 'cyb_select_cpt_settings', 'cyb_select_cpt_home_section' );

        /* For feed */
        add_settings_section( 'cyb_select_cpt_feed_section', __( 'Custom Post Types for your feeds', 'cyb-select-cpt' ), array( $this, 'section_feed_html' ), 'cyb_select_cpt_settings' );
        
        add_settings_field( 'cyb_feed_cpt', __( 'Custom Post Type for feeds', 'cyb-select-cpt' ), array( $this, 'feed_html' ), 'cyb_select_cpt_settings', 'cyb_select_cpt_feed_section' );

        /* For author page */
        add_settings_section( 'cyb_select_cpt_author_section', __( 'Custom Post Types for authors page', 'cyb-select-cpt' ), array( $this, 'section_author_html' ), 'cyb_select_cpt_settings' );
        
        add_settings_field( 'cyb_author_cpt', __( 'Custom Post Type for authors', 'cyb-select-cpt' ), array( $this, 'author_html' ), 'cyb_select_cpt_settings', 'cyb_select_cpt_author_section' );
    }
    
    public function section_home_html()
    {
    }
    
    public function home_html()
    {
        $cpts = get_post_types( array( 'public' => true ) );
        $options = get_option( 'cyb_select_cpt_options' );
        if ( $options == false || empty( $options['home'] ) ) {
            $home_cpt = array( 'post' => 1 );
        } else {
            $home_cpt = $options['home'];
        }
        
        foreach ( $cpts as $cpt ) {
        ?>
        <input type="checkbox" name="cyb_select_cpt_options[home][<?php echo esc_attr( $cpt ); ?>]" id="cyb_select_cpt_options[home][<?php echo esc_attr( $cpt ); ?>]" value="1" <?php checked( $home_cpt[$cpt], 1 ); ?> /> <label for="cyb_select_cpt_options[home][<?php echo esc_attr( $cpt ); ?>]"><?php echo $cpt; ?></label><br />
        <?php
        }
    }

    public function section_feed_html()
    {
    }
    
    public function feed_html()
    {
        $cpts = get_post_types( array( 'public' => true ) );
        $options = get_option( 'cyb_select_cpt_options' );
        if ( $options == false || empty( $options['feed'] ) ) {
            $feed_cpt = array( 'post' => 1 );
        } else {
            $feed_cpt = $options['feed'];
        }
        
        foreach ( $cpts as $cpt ) {
        ?>
        <input type="checkbox" name="cyb_select_cpt_options[feed][<?php echo esc_attr( $cpt ); ?>]" id="cyb_select_cpt_options[feed][<?php echo esc_attr( $cpt ); ?>]" value="1" <?php checked( $feed_cpt[$cpt], 1 ); ?> /> <label for="cyb_select_cpt_options[feed][<?php echo esc_attr( $cpt ); ?>]"><?php echo $cpt; ?></label><br />
        <?php
        }
    }

    public function section_author_html()
    {
    }
    
    public function author_html()
    {
        $cpts = get_post_types( array( 'public' => true ) );
        $options = get_option( 'cyb_select_cpt_options' );
        if ( $options == false || empty( $options['author'] ) ) {
            $author_cpt = array( 'post' => 1 );
        } else {
            $author_cpt = $options['author'];
        }
        
        foreach ( $cpts as $cpt ) {
        ?>
        <input type="checkbox" name="cyb_select_cpt_options[author][<?php echo esc_attr( $cpt ); ?>]" value="1" id="cyb_select_cpt_options[author][<?php echo esc_attr( $cpt ); ?>]" <?php checked( $author_cpt[$cpt], 1 ); ?> /> <label for="cyb_select_cpt_options[author][<?php echo esc_attr( $cpt ); ?>]"><?php echo $cpt; ?></label><br />
        <?php
        }
    }
    
    public function filter_cpt( $query )
    {
        $options = get_option( 'cyb_select_cpt_options' );
        $cpts = get_post_types( array( 'public' => true ) );
        if ( $options == false )
            return $query;
        
        /* homepage */
        if ( is_home() && $query->is_main_query() ) {
            $sel_cpt = array();
            foreach ( $cpts as $cpt ) {
                if ( $options['home'][$cpt] ) {
                    $sel_cpt[] = $cpt;
                }
            }
            if ( empty( $sel_cpt ) )
                $sel_cpt[] = 'post';
            $query->set( 'post_type', $sel_cpt );
        }
        
        /* feeds */
        if ( is_feed() ) {
            $sel_cpt = array();
            foreach ( $cpts as $cpt ) {
                if ( $options['feed'][$cpt] ) {
                    $sel_cpt[] = $cpt;
                }
            }
            if ( empty( $sel_cpt ) )
                $sel_cpt[] = 'post';
            $query->set( 'post_type', $sel_cpt );
        }
        
        /* author */
        if ( $query->is_author() && $query->is_main_query() ) {
            $sel_cpt = array();
            foreach ( $cpts as $cpt ) {
                if ( $options['author'][$cpt] ) {
                    $sel_cpt[] = $cpt;
                }
            }
            if ( empty( $sel_cpt ) )
                $sel_cpt[] = 'post';
            $query->set( 'post_type', $sel_cpt );
        }

        return $query;
    }

    public function load_plugin_textdomain()
    {
        load_plugin_textdomain( 'cyb-select-cpt', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    }
    
}

new CybSelectCPT();
