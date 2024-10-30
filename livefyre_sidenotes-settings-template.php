<?php

?>

<div id="fyresettings">
    <div id="fyreheader" style= <?php echo '"background-image: url(' .plugins_url( '/livefyre-sidenotes/images/header-bg.png', 'livefyre-comments' ). ')"' ?> >
        <img src= <?php echo '"' .plugins_url( '/livefyre-sidenotes/images/logo.png', 'livefyre-comments' ). '"' ?> rel="Livefyre" style="padding: 5px; padding-left: 15px;" />
    </div>
    <div id="fyrebody">
        <div id="fyrebodycontent">
            <div id="fyrestatus">
                <?php
                    $status[0] = 'Good to go!';
                    $status[1] = 'green';
                    if ( get_option( 'livefyre_sidenotes_site_id ' ) == '' || 
                        get_option( 'livefyre_sidenotes_site_key' ) == '' ) {
                        $network = get_option( 'livefyre_sidenotes_domain_name', 'livefyre.com' );
                        $network = ( $network == '' ? 'livefyre.com' : $network );
                        $message = 'To activate Livefyre Sidenotes, you must register your blog with Livefyre:</br><strong>- </strong>If you already have a registered blog, please enter in your Livefyre Site ID and Site Key below. You can 
                        find this information <a href="'.home_url().'/wp-admin/options-general.php?page=livefyre">here</a> under Site Settings on the right side of the page.</br><strong>- </strong> If you have not registered a blog, 
                        you can register your blog by following <a href="http://www.livefyre.com/installation/logout/?site_url='.
                        urlencode(home_url()).'&domain=rooms.'.$network.'&version=sidenotes&type=wordpress&lfversion=sidenotes&postback_hook='.
                        urlencode(home_url()).'&transport=http">this link</a>.';
                        $status[0] = $message;
                        $status[1] = 'red';
                    }
                    echo '<h1><span class="statuscircle' .esc_attr($status[1]). '"></span>Livefyre Status: <span>' .$status[0]. '</span></h1>';
                ?>
            </div>
            <div id="settings_information">
                <form method="post" action="options.php">
                    <?php
                        settings_fields( 'livefyre_sidenotes_site_options' );
                        do_settings_sections( 'livefyre_sidenotes' );
                    ?>
                    <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
                    </p>
                </form>
            </div>
            <div id="fyresidepanel">
                <div id="fyresidesettings">
                    <h1>Network Settings</h1>
                        <p class="lf_label">Livefyre Network: </p>
                        <?php
                        $network = get_option( 'livefyre_sidenotes_domain_name', 'livefyre.com' );
                        $network = ( $network == '' ? 'livefyre.com' : $network );
                        echo '<p class="lf_text">' .$network. '</p>';
                        ?>
                        <br />
                        <!-- <p class="lf_label">Livefyre Network Key: </p> -->
                        <?php //echo '<p class="lf_text">' .get_option('livefyre_sidenotes_domain_key'). '</p>'; ?>
                        <!-- <br /> -->
                        <!-- <p class="lf_label">Livefyre Auth Delegate Name: </p> -->
                        <?php //echo '<p class="lf_text">' .get_option('livefyre_sidenotes_auth_delegate_name'). '</p>'; ?>
                    <h1>Site Settings</h1>
                        <p class="lf_label">Livefyre Site ID: </p>
                        <?php echo '<p class="lf_text">' .get_option('livefyre_sidenotes_site_id'). '</p>'; ?>
                        <br />
                        <p class="lf_label">Livefyre Site Key: </p>
                        <?php echo '<p class="lf_text">' .get_option('livefyre_sidenotes_site_key'). '</p>'; ?>
                    <h1>Links</h1>
                        <a href="http://livefyre.com/admin" target="_blank">Livefyre Admin</a>
                        <br />
                        <a href="http://support.livefyre.com" target="_blank">Livefyre Support</a>
                </div>
                <div id="fyredisplayinfo">
                    <h1>Display Comments</h1>
                    <p class="lf_text">I would like comments displayed on:</p>
                    <?php

                    $excludes = array( '_builtin' => false );
                    $post_types = get_post_types( $args = $excludes );

                    if( isset( $_GET['save_display_settings']) ) {
                        if ( isset( $_GET['display_posts'] ) ) {
                            update_option( 'livefyre_sidenotes_display_posts', sanitize_text_field( $_GET['display_posts'] ) );
                        }
                        else {
                            update_option( 'livefyre_sidenotes_display_posts', 'false' );
                        }
                        if ( isset( $_GET['display_pages'] ) ) {
                            update_option( 'livefyre_sidenotes_display_pages', sanitize_text_field( $_GET['display_pages'] ) );
                        }
                        else {
                            update_option( 'livefyre_sidenotes_display_pages', 'false' );
                        }

                        foreach ($post_types as $post_type ) {
                            $post_type_name = 'livefyre_display_' .$post_type;
                            if ( isset( $_GET[$post_type] ) ) {
                                update_option( $post_type_name, sanitize_text_field( $_GET[$post_type] ) );
                            }
                            else {
                                update_option( $post_type_name, 'false' );
                            }
                        }
                    }

                    $posts_checkbox = "";
                    $pages_checkbox = "";
                    if ( get_option('livefyre_sidenotes_display_posts', 'true') == 'true' ) {
                        $posts_checkbox = 'checked="yes"';
                    }
                    if ( get_option('livefyre_sidenotes_display_pages', 'true') == 'true' ) {
                        $pages_checkbox = 'checked="yes"';
                    }
                    ?>
                    <form id="fyredisplayform" action="options-general.php?page=livefyre-sidenotes">
                        <input type="hidden" name="page" value="livefyre-sidenotes" />
                        <input type="checkbox" class="checkbox" name="display_posts" value="true" <?php echo $posts_checkbox;?> />Posts<br />
                        <input type="checkbox" class="checkbox" name="display_pages" value="true" <?php echo $pages_checkbox;?> />Pages<br />
                        <?php 
                        foreach ($post_types as $post_type ) {
                            $post_type_name = 'livefyre_display_' .$post_type;
                            if ( get_option($post_type_name, 'true') == 'true' ) {
                                $post_type_checkbox = 'checked="yes"';
                            }
                            ?>
                            <input type="checkbox" class="checkbox" name=<?php echo '"' .$post_type. '"';?> value="true" <?php echo $post_type_checkbox;?> /><?php echo $post_type; ?><br />
                            <?php
                        }
                        ?>
                        <input type="submit" class="fyrebutton" name="save_display_settings" value="Submit" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    <?php echo file_get_contents( dirname( __FILE__ ) . '/settings-template.css' )  ?>
</style>
    