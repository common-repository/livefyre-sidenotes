<?php
/*
Plugin Name: Livefyre Sidenotes
Plugin URI: http://livefyre.com
Description: Implements Livefyre Sidenotes widget for WordPress
Author: Livefyre, Inc.
Version: 1.0.1
Author URI: http://livefyre.com/
*/

define( 'LIVEFYRE_SIDENOTES_LIB', 'http://cdn.livefyre.com/libs/sidenotes/v0.1.0-beta/sidenotes.min.js' );
define( 'LIVEFYRE_SIDENOTES_AUTH_LIB', 'http://cdn.livefyre.com/libs/auth-delegates/v0.4.4-beta/auth-delegates.min.js' );

require_once( dirname( __FILE__ ) . "/Livefyre_Sidenotes_Admin.php" );
require_once( dirname( __FILE__ ) . "/Livefyre_Sidenotes_JWT.php" );

class Livefyre_Sidenotes {

    function __construct() {

        $livefyre_sc_admin = new Livefyre_Sidenotes_Admin();
        $jwt = new Livefyre_Sidenotes_JWT();

        add_action( 'wp_enqueue_scripts', array( $this, 'livefyre_sidenotes_add_zor' ) );
        add_filter( 'the_content', array( $this, 'livefyre_sidenotes_wrapper' ) );
        add_action( 'wp_footer', array( $this, 'livefyre_build_sidenotes' ) );
    
    }

    function livefyre_sidenotes_add_zor() {

        if( !self::display_sidenotes() ) {
            return;
        }

        $zor_domain = "livefyre.com";
        if ( get_option( 'environment', 'development' ) == 'development' ) {
            $zor_domain = "t402.livefyre.com";
        }

        $zor_domain = "livefyre.com";
        wp_enqueue_script( 'livefyre_sidenotes_zor', LIVEFYRE_SIDENOTES_LIB );
        wp_enqueue_script( 'livefyre_sidenotes_auth', LIVEFYRE_SIDENOTES_AUTH_LIB );

    }

    function livefyre_build_sidenotes() {

        if( !self::display_sidenotes() ) {
            return;
        }

        $network = get_option( 'livefyre_sidenotes_domain_name', 'livefyre.com' );
        $network = ( $network == '' ? 'livefyre.com' : $network );
        $siteId = get_option( 'livefyre_sidenotes_site_id' );
        $siteKey = get_option( 'livefyre_sidenotes_site_key' );
        $environment = "livefyre.com";
        if ( get_option( 'environment', 'development' ) == 'development' ) {
            $environment = "t402.livefyre.com";
        }
        $environment = "livefyre.com";
        $post = get_post();
        $articleId = get_the_ID().'-sidenotes';
        $title = get_the_title($articleId);
        $url = get_permalink(get_the_ID());

        $sidenotesDeclarations = "var Sidenotes = Livefyre.Sidenotes;";
        $delegateDeclarations = "var LivefyreDelegate = Livefyre.authDelegates.Livefyre;";
        $delegateCreation = "var delegate = new LivefyreDelegate('$articleId', '$siteId', 'http://$environment');";
        $selectors = "selectors: '#livefyre-sidenotes-wrap > p:not(:has(img)), #livefyre-sidenotes-wrap > p > a > img, #livefyre-sidenotes-wrap > ul > li'";
        $infoButton = "numSidenotesEl: '#livefyre-sidenotes-header-wrapper'";
        $authDelegate = "authDelegate: delegate";
        $collection = "collection: {
                        articleId: '$articleId',
                        environment: '$environment',
                        network: '$network',
                        siteId: '$siteId'
                    }";
        $collectionMeta = array(
            'title' => $title,
            'url' => $url,
            'type' => 'sidenotes'
        );
        $checksum = md5( json_encode( $collectionMeta ) );
        $collectionMeta['checksum'] = $checksum;
        $collectionMeta['articleId'] = $articleId;
        $jwtString = Livefyre_Sidenotes_JWT::encode($collectionMeta, $siteKey);
        $collectionMetaString = "collectionMeta: '$jwtString'";
        $sidenotesJS = "new Sidenotes({
                    $selectors,
                    $infoButton,
                    $authDelegate,
                    $collection,
                    $collectionMetaString
                });";

        echo "<script>
                $domManipulator
                $sidenotesDeclarations
                $artcleIdDeclarations
                $delegateDeclarations
                $delegateCreation
                $sidenotesJS
            </script>";

    }

    function livefyre_sidenotes_wrapper( $content ) {

        if( !self::display_sidenotes() ) {
            return $content;
        }

        return "<div id='livefyre-sidenotes-header-wrapper' style='padding-bottom: 10px;'></div><div id='livefyre-sidenotes-wrap'>$content</div>";

    }

    static function display_sidenotes() {

        global $post;
        /* Is this a post and is the settings checkbox on? */
        $display_posts = ( is_single() && get_option( 'livefyre_sidenotes_display_posts','true') == 'true' );
        /* Is this a page and is the settings checkbox on? */
        $display_pages = ( is_page() && get_option( 'livefyre_sidenotes_display_pages','true') == 'true' );

        $display = $display_posts || $display_pages;
        $post_type = get_post_type();

        if ( $post_type != 'post' && $post_type != 'page' ) {
            $post_type_name = 'livefyre_display_' .$post_type;            
            $display = ( get_option( $post_type_name, 'true' ) == 'true' );
        }

        return $display
            && !is_preview();

    }

}

$livefyre_sidenotes = new Livefyre_Sidenotes();
