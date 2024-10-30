<?php

define( 'LF_SITE_SIDENOTES_SETTINGS_PAGE', '/livefyre_sidenotes-settings-template.php' );

class Livefyre_Sidenotes_Admin {
    
    function __construct( ) {
        
        add_action( 'admin_menu', array( &$this, 'register_admin_page' ) );
        add_action( 'admin_init', array( &$this, 'site_options_init' ) );

    }

    function register_admin_page() {
    
        add_submenu_page( 'options-general.php', 'Livefyre Sidenotes Settings', 'Livefyre Sidenotes', 'manage_options', 'livefyre-sidenotes', array( &$this, 'site_options_page' ) );
    }
    
    function site_options_init() {
    
        $name = 'livefyre_sidenotes';
        $section_name = 'lf_sidenotes_site_settings';
        $settings_section = 'livefyre_sidenotes_site_options';

        // Site settings
        register_setting( $settings_section, 'livefyre_sidenotes_site_id' );
        register_setting( $settings_section, 'livefyre_sidenotes_site_key' );
        // register_setting( $settings_section, 'livefyre_sidenotes_domain_name' );
        // register_setting( $settings_section, 'livefyre_sidenotes_domain_key' );
        // register_setting( $settings_section, 'livefyre_sidenotes_auth_delegate_name' );
        // register_setting( $settings_section, 'livefyre_sidenotes_environment' );

        if( self::returned_from_setup() ) {
            update_option( 'livefyre_sidenotes_site_id', sanitize_text_field( $_GET["site_id"] ) );
            update_option( 'livefyre_sidenotes_site_key', sanitize_text_field( $_GET["secretkey"] ) );
        }
        // Must be fixed in the Comments plugin
        if( get_option( 'livefyre_domain_name', '' ) == '' ) {
            update_option( 'livefyre_domain_name', 'livefyre.com');
        }

        add_settings_section($section_name,
            'Livefyre Sidenotes Network and Site Settings',
            array( &$this, 'settings_callback' ),
            $name
        );
        
        add_settings_field('livefyre_sidenotes_site_id',
            'Livefyre Site ID',
            array( &$this, 'site_id_callback' ),
            $name,
            $section_name
        );
        
        add_settings_field('livefyre_sidenotes_site_key',
            'Livefyre Site Key',
            array( &$this, 'site_key_callback' ),
            $name,
            $section_name
        );

        // add_settings_field('livefyre_sidenotes_domain_name',
        //     'Livefyre Network Name',
        //     array( &$this, 'domain_name_callback' ),
        //     $name,
        //     $section_name
        // );
        
        // add_settings_field('livefyre_sidenotes_domain_key',
        //     'Livefyre Network Key',
        //     array( &$this, 'domain_key_callback' ),
        //     $name,
        //     $section_name
        // );
        
        // add_settings_field('livefyre_sidenotes_auth_delegate_name',
        //     'Livefyre Authdelegate Name',
        //     array( &$this, 'auth_delegate_callback' ),
        //     $name,
        //     $section_name
        // );

        // add_settings_field('livefyre_sidenotes_environment',
        //     'Livefyre Environment',
        //     array( &$this, 'environment_callback' ),
        //     $name,
        //     $section_name
        // );
        
    }

    function settings_callback() {}

    function site_id_callback() {

        echo "<input name='livefyre_sidenotes_site_id' value='" . get_option( 'livefyre_sidenotes_site_id', '' ) . "' />";

    }
    
    function site_key_callback() { 

        echo "<input name='livefyre_sidenotes_site_key' value='" . get_option( 'livefyre_sidenotes_site_key', '' ) . "' />";

    }

    function auth_delegate_callback() {

        echo "<input name='livefyre_sidenotes_auth_delegate_name' value='". get_option( 'livefyre_sidenotes_auth_delegate_name', '' ) ."' />";

    }
    
    function domain_name_callback() {

        echo "<input name='livefyre_sidenotes_domain_name' value='". get_option( 'livefyre_sidenotes_domain_name', '' ) ."' />";
    
    }
    
    function domain_key_callback() { 
    
        echo "<input name='livefyre_sidenotes_domain_key' value='". get_option( 'livefyre_sidenotes_domain_key', '' ) ."' />";
        
    }

    function environment_callback() { 

        echo "<input name='livefyre_sidenotes_environment' value='" . get_option( 'livefyre_sidenotes_environment', '' ) . "' />";

    }

    function site_options_page() {

        /* Should we display the Enterprise or Regular version of the settings?
         * Needs to be decided by the build process
         * The file gets set in the bash script that builds this.
         * The default is community
        */
        include( dirname(__FILE__) . LF_SITE_SIDENOTES_SETTINGS_PAGE);
    
    }

    function returned_from_setup() {

        return (
            isset($_GET['lf_login_complete']) &&
            $_GET['lf_login_complete']=='1' &&
            isset($_GET['page']) &&
            $_GET['page']=='livefyre-sidenotes'
        );

    }

}