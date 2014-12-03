<?php

    /**
     * ReduxFramework Sample Config File
     * For full documentation, please visit: http://docs.reduxframework.com/
     */

    if ( ! class_exists( 'Docs_Redux_Framework_config' ) ) {

        class Docs_Redux_Framework_config {

            public $args = array();
            public $sections = array();
            public $theme;
            public $ReduxFramework;

            public function __construct() {

                if ( ! class_exists( 'ReduxFramework' ) ) {
                    return;
                }

                // This is needed. Bah WordPress bugs.  ;)
                if ( true == Redux_Helpers::isTheme( __FILE__ ) ) {
                    $this->initSettings();
                } else {
                    add_action( 'plugins_loaded', array( $this, 'initSettings' ), 10 );
                }

            }

            public function initSettings() {

                // Just for demo purposes. Not needed per say.
                $this->theme = wp_get_theme();

                // Set the default arguments
                $this->setArguments();

                // Create the sections and fields
                $this->setSections();

                if ( ! isset( $this->args['opt_name'] ) ) { // No errors please
                    return;
                }

                // If Redux is running as a plugin, this will remove the demo notice and links
                add_action( 'redux/loaded', array( $this, 'remove_demo' ) );

                // Function to test the compiler hook and demo CSS output.
                // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
                //add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 3);

                // Change the arguments after they've been declared, but before the panel is created
                //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );

                // Change the default value of a field after it's been set, but before it's been useds
                //add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );

                // Dynamically add a section. Can be also used to modify sections/fields
                //add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

                $this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
            }

            /**
             * This is a test function that will let you see when the compiler hook occurs.
             * It only runs if a field    set with compiler=>true is changed.
             * */
            function compiler_action( $options, $css, $changed_values ) {
                echo '<h1>The compiler hook has run!</h1>';
                echo "<pre>";
                print_r( $changed_values ); // Values that have changed since the last save
                echo "</pre>";
                //print_r($options); //Option values
                //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )

                /*
              // Demo of how to use the dynamic CSS and write your own static CSS file
              $filename = dirname(__FILE__) . '/style' . '.css';
              global $wp_filesystem;
              if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
              WP_Filesystem();
              }

              if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $filename,
                    $css,
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );
              }
             */
            }

            /**
             * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
             * Simply include this function in the child themes functions.php file.
             * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
             * so you must use get_template_directory_uri() if you want to use any of the built in icons
             * */
            function dynamic_section( $sections ) {
                //$sections = array();
                $sections[] = array(
                    'title'  => __( 'Section via hook', 'redux-framework-demo' ),
                    'desc'   => __( '<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'redux-framework-demo' ),
                    'icon'   => 'el-icon-paper-clip',
                    // Leave this as a blank section, no options just some intro text set above.
                    'fields' => array()
                );

                return $sections;
            }

            /**
             * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
             * */
            function change_arguments( $args ) {
                //$args['dev_mode'] = true;

                return $args;
            }

            /**
             * Filter hook for filtering the default value of any given field. Very useful in development mode.
             * */
            function change_defaults( $defaults ) {
                $defaults['str_replace'] = 'Testing filter hook!';

                return $defaults;
            }

            // Remove the demo link and the notice of integrated demo from the redux-framework plugin
            function remove_demo() {

                // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
                if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
                    remove_filter( 'plugin_row_meta', array(
                        ReduxFrameworkPlugin::instance(),
                        'plugin_metalinks'
                    ), null, 2 );

                    // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                    remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
                }
            }

            public function setSections() {

                /**
                 * Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
                 * */
                // Background Patterns Reader
                $sample_patterns_path = ReduxFramework::$_dir . '../sample/patterns/';
                $sample_patterns_url  = ReduxFramework::$_url . '../sample/patterns/';
                $sample_patterns      = array();

                if ( is_dir( $sample_patterns_path ) ) :

                    if ( $sample_patterns_dir = opendir( $sample_patterns_path ) ) :
                        $sample_patterns = array();

                        while ( ( $sample_patterns_file = readdir( $sample_patterns_dir ) ) !== false ) {

                            if ( stristr( $sample_patterns_file, '.png' ) !== false || stristr( $sample_patterns_file, '.jpg' ) !== false ) {
                                $name              = explode( '.', $sample_patterns_file );
                                $name              = str_replace( '.' . end( $name ), '', $sample_patterns_file );
                                $sample_patterns[] = array(
                                    'alt' => $name,
                                    'img' => $sample_patterns_url . $sample_patterns_file
                                );
                            }
                        }
                    endif;
                endif;

                ob_start();

                $ct          = wp_get_theme();
                $this->theme = $ct;
                $item_name   = $this->theme->get( 'Name' );
                $tags        = $this->theme->Tags;
                $screenshot  = $this->theme->get_screenshot();
                $class       = $screenshot ? 'has-screenshot' : '';

                $customize_title = sprintf( __( 'Customize &#8220;%s&#8221;', 'redux-framework-demo' ), $this->theme->display( 'Name' ) );

                ?>
                <div id="current-theme" class="<?php echo esc_attr( $class ); ?>">
                    <?php if ( $screenshot ) : ?>
                        <?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
                            <a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize"
                               title="<?php echo esc_attr( $customize_title ); ?>">
                                <img src="<?php echo esc_url( $screenshot ); ?>"
                                     alt="<?php esc_attr_e( 'Current theme preview', 'redux-framework-demo' ); ?>"/>
                            </a>
                        <?php endif; ?>
                        <img class="hide-if-customize" src="<?php echo esc_url( $screenshot ); ?>"
                             alt="<?php esc_attr_e( 'Current theme preview', 'redux-framework-demo' ); ?>"/>
                    <?php endif; ?>

                    <h4><?php echo $this->theme->display( 'Name' ); ?></h4>

                    <div>
                        <ul class="theme-info">
                            <li><?php printf( __( 'By %s', 'redux-framework-demo' ), $this->theme->display( 'Author' ) ); ?></li>
                            <li><?php printf( __( 'Version %s', 'redux-framework-demo' ), $this->theme->display( 'Version' ) ); ?></li>
                            <li><?php echo '<strong>' . __( 'Tags', 'redux-framework-demo' ) . ':</strong> '; ?><?php printf( $this->theme->display( 'Tags' ) ); ?></li>
                        </ul>
                        <p class="theme-description"><?php echo $this->theme->display( 'Description' ); ?></p>
                        <?php
                            if ( $this->theme->parent() ) {
                                printf( ' <p class="howto">' . __( 'This <a href="%1$s">child theme</a> requires its parent theme, %2$s.', 'redux-framework-demo' ) . '</p>', __( 'http://codex.wordpress.org/Child_Themes', 'redux-framework-demo' ), $this->theme->parent()->display( 'Name' ) );
                            }
                        ?>

                    </div>
                </div>

                <?php
                $item_info = ob_get_contents();

                ob_end_clean();

                $sampleHTML = '';
                if ( file_exists( dirname( __FILE__ ) . '/info-html.html' ) ) {
                    Redux_Functions::initWpFilesystem();

                    global $wp_filesystem;

                    $sampleHTML = $wp_filesystem->get_contents( dirname( __FILE__ ) . '/info-html.html' );
                }

                // ACTUAL DECLARATION OF SECTIONS
                $this->sections[] = array(
                    'icon'   => 'el-icon-cog',
                    'title'  => __( 'General', 'redux-framework-demo' ),
                    'fields' => array(
                        array(
                            'id' => 'logo',
                            'type' => 'media',
                            'title' => __('Logo Upload', 'pressapps' ),
                        ),
                        array(
                            'id' => 'favicon',
                            'type' => 'media',
                            'title' => __('Favicon Upload', 'pressapps' ),
                        ),
                        /*
                        array(
                            'title'     => __( 'License', 'shoestrap' ),
                            'desc'      => __( 'Paste your license key.', 'shoestrap' ),
                            'id'        => 'license_key',
                            'default'   => '',
                            'type'      => 'text',
                        ),
                        */
                        array(
                            'title'     => __( 'Google Analytics ID', 'shoestrap' ),
                            'desc'      => __( 'Paste your Google Analytics ID here to enable analytics tracking. ID should be in the form of UA-XXXXX-Y.', 'shoestrap' ),
                            'id'        => 'analytics_id',
                            'default'   => '',
                            'type'      => 'text',
                        ),
                        array(
                            'id'       => 'footer_text',
                            'type'     => 'editor',
                            'title'    => __( 'Footer Text', 'redux-framework-demo' ),
                            'default'  => 'Powered by Docs Theme.',
                            'args'     => array(
                                'media_buttons' => false,
                            ),
                        ),
                        array(
                            'id'       => 'custom_css',
                            'type'     => 'ace_editor',
                            'title'    => __( 'CSS Code', 'redux-framework-demo' ),
                            'desc' => __( 'Paste your custom CSS code here.', 'redux-framework-demo' ),
                            'mode'     => 'css',
                            'theme'    => 'monokai',
                            'default'  => '',
                        ),
                    )
                );

                $this->sections[] = array(
                    'icon'   => 'el-icon-website',
                    'title'  => __( 'Sidebar', 'redux-framework-demo' ),
                    'fields' => array(
                        array(
                            'id' => 'banner_width',
                            'type' => 'slider',
                            'title' => __('Sidebar Width', 'redux-framework-demo'),
                            'desc' => __('Select sidebar width in px.', 'redux-framework-demo'),
                            "default" => 300,
                            "min" => 200,
                            "step" => 10,
                            "max" => 400,
                            'display_value' => 'text'
                        ),
                        array(
                            'id'       => 'banner_bg',
                            'type'     => 'color',
                            'output'   => '.banner, .banner .toggle-menu',
                            'title'    => __( 'Background', 'redux-framework-demo' ),
                            'transparent' => false,
                            'validate' => 'color',
                            'mode'     => 'background',
                            'desc' => __( 'Sidebar background with image or color.', 'redux-framework-demo' ),
                            'default'  => '#232830',
                        ),
                        array(
                            'id'       => 'navbar_hover_bg',
                            'type'     => 'color',
                            'title'    => __( 'Navigation Hover', 'redux-framework-demo' ),
                            'default'  => '#5b90bf',
                            'mode'     => 'background',
                            'transparent' => false,
                            'validate' => 'color',
                            'output'    => '.nav li a:hover, .nav li a:focus, .navbar-docs li.active > a, .current-menu-parent > a, .current-menu-item a',
                        ),
                        array(
                            'title'     => __( 'Autocollapse Menu', 'shoestrap' ),
                            'desc'      => __( 'Autocollapse inactive subcategories in document menu', 'shoestrap' ),
                            'id'        => 'autocollapse_doc',
                            'default'   => 1,
                            'type'      => 'switch',
                        ),
                        array(
                            'id'       => 'banner_order',
                            'title'    => __( 'Sidebar Layout', 'redux-framework-demo' ),
                            'type'     => 'sorter',
                            'options'  => array(
                                'Sidebar'  => array(
                                    'nav' => 'Primary Nav',
                                    'docs'     => 'Docs Nav',
                                    'sidebar'   => 'Widgets',
                                ),
                            ),
                        ),
                    )
                );

                $this->sections[] = array(
                    'icon'       => 'el-icon-file',
                    'title'      => __( 'Document Page', 'redux-framework-demo' ),
                    'fields'     => array(
                        array(
                            'id' => 'reorder',
                            'type' => 'switch',
                            'title' => __('Reorder', 'pressapps' ), 
                            'desc' => __('Enable drag and drop reordering under Posts>>All Posts, Posts>>Categories and Posts>>Actions.', 'pressapps' ),
                            "default"       => 1,
                        ),
                        array(
                            'id' => 'header_filter',
                            'type' => 'switch',
                            'title'       => __( 'Filter', 'shoestrap' ),
                            'desc'        => __( 'Display a search filter in the document page headline.', 'shoestrap' ),
                            'default'     => 1,
                        ),
                        array(
                            'title'     => __( 'Filter Placeholder', 'shoestrap' ),
                            'desc'      => __( 'Enter filter field placeholder.', 'shoestrap' ),
                            'id'        => 'filter_placeholder',
                            'default'   => 'Filter Document',
                            'type'      => 'text',
                            'required' => array('header_filter','=','1'),       
                        ),
                        array(
                            'id'        => 'exlude_cats',
                            'title'     => __('Exclude Categories', 'redux-framework-demo'),
                            'type'      => 'select',
                            'data'      => 'categories',
                            'multi'     => true,
                            'desc'      => __('Select categories to exlude from query (If none selected all categories will be displayed).', 'redux-framework-demo'),
                        ),
                        array(
                            'id'       => 'style_ol',
                            'type'     => 'switch',
                            'title'    => __( 'Ordered List', 'redux-framework-demo' ),
                            'desc'     => __( 'Custom styled ordered list on document pages.', 'redux-framework-demo' ),
                            'default'  => 1,
                        ),
                    )
                );

                $this->sections[] = array(
                    'icon'   => 'el-icon-font',
                    'title'  => __( 'Typography', 'redux-framework-demo' ),
                    'fields' => array(
                        array(
                            'id'       => 'font_heading',
                            'type'     => 'typography',
                            'title'    => __( 'Heading Font', 'redux-framework-demo' ),
                            'desc' => __( 'Specify the heading font properties.', 'redux-framework-demo' ),
                            'google'   => true,
                            'font-size'   => false,
                            'line-height'   => false,
                            'text-align'   => false,
                            'subsets'     => false,
                            'default'  => array(
                                'color' => '#363C45',
                                'font-family' => 'Open Sans',
                                'font-weight' => '600',
                            ),
                            'output'   => array( 'h1, h2, h3, h4, h5, h6' ),
                        ),
                        array(
                            'id'       => 'font_body',
                            'type'     => 'typography',
                            'title'    => __( 'Body Font', 'redux-framework-demo' ),
                            'desc' => __( 'Specify the body font properties.', 'redux-framework-demo' ),
                            'google'   => true,
                            'line-height'   => false,
                            'text-align'   => false,
                            'subsets'     => false,
                            'default'  => array(
                                'color'       => '#727D93',
                                'font-size'   => '15px',
                                'font-family' => 'Open Sans',
                                'font-weight' => '300',
                            ),
                            'output'   => array( 'body', '.box p' ),
                        ),
                        array(
                            'id'       => 'primary_color',
                            'type'     => 'link_color',
                            'title'    => __( 'Link Colors', 'redux-framework-demo' ),
                            'desc'     => __( 'Select main content link colors.', 'redux-framework-demo' ),
                            'active'    => false, 
                            'default'  => array(
                                'regular' => '#439dd0',
                                'hover'   => '#3d3d3d',
                            ),
                            'output'   => array( 'a' ),
                        ),
                    )
                );

                $this->sections[] = array(
                    'title'  => __( 'Import / Export', 'redux-framework-demo' ),
                    'desc'   => __( 'Import and Export your Redux Framework settings from file, text or URL.', 'redux-framework-demo' ),
                    'icon'   => 'el-icon-refresh',
                    'fields' => array(
                        array(
                            'id'         => 'opt-import-export',
                            'type'       => 'import_export',
                            'title'      => 'Import Export',
                            'subtitle'   => 'Save and restore your Redux options',
                            'full_width' => false,
                        ),
                    ),
                );

                $this->sections[] = array(
                    'type' => 'divide',
                );

                if ( file_exists( dirname( __FILE__ ) . '/../README.md' ) ) {

                    $this->sections[] = array(
                        'icon'   => 'el-icon-list-alt',
                        'title'  => __( 'Documentation', 'redux-framework-demo' ),
                        'fields' => array(
                            array(
                                'id'       => '17',
                                'type'     => 'raw',
                                'markdown' => true,
                                'content'  => file_get_contents( dirname( __FILE__ ) . '/../README.md' )
                            ),
                        ),
                    );
                }

                if ( file_exists( dirname( __FILE__ ) . '/../CHANGELOG.md' ) ) {

                    $this->sections[] = array(
                        'icon'   => 'el-icon-fork',
                        'title'  => __( 'Changelog', 'redux-framework-demo' ),
                        'fields' => array(
                            array(
                                'id'       => '18',
                                'type'     => 'raw',
                                'markdown' => true,
                                'content'  => file_get_contents( dirname( __FILE__ ) . '/../CHANGELOG.md' )
                            ),
                        ),
                    );
                }

            }

            /**
             * All the possible arguments for Redux.
             * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
             * */
            public function setArguments() {

                $theme = wp_get_theme(); // For use with some settings. Not necessary.

                $this->args = array(
                    // TYPICAL -> Change these values as you need/desire
                    'opt_name'             => OPT_NAME,
                    // This is where your data is stored in the database and also becomes your global variable name.
                    'display_name'         => $theme->get( 'Name' ),
                    // Name that appears at the top of your panel
                    'display_version'      => $theme->get( 'Version' ),
                    // Version that appears at the top of your panel
                    'menu_type'            => 'menu',
                    //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                    'allow_sub_menu'       => true,
                    // Show the sections below the admin menu item or not
                    'menu_title'           => __( 'Theme Options', 'redux-framework-demo' ),
                    'page_title'           => __( 'Theme Options', 'redux-framework-demo' ),
                    // You will need to generate a Google API key to use this feature.
                    // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                    'google_api_key'       => '',
                    // Set it you want google fonts to update weekly. A google_api_key value is required.
                    'google_update_weekly' => false,
                    // Must be defined to add google fonts to the typography module
                    'async_typography'     => false,
                    // Use a asynchronous font on the front end or font string
                    //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                    'admin_bar'            => true,
                    // Show the panel pages on the admin bar
                    'admin_bar_icon'     => 'dashicons-portfolio',
                    // Choose an icon for the admin bar menu
                    'admin_bar_priority' => 50,
                    // Choose an priority for the admin bar menu
                    'global_variable'      => '',
                    // Set a different name for your global variable other than the opt_name
                    'dev_mode'             => false,
                    // Show the time the page took to load, etc
                    'update_notice'        => true,
                    // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                    'customizer'           => true,
                    // Enable basic customizer support
                    //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                    //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                    // OPTIONAL -> Give you extra features
                    'page_priority'        => null,
                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                    'page_parent'          => 'themes.php',
                    // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                    'page_permissions'     => 'manage_options',
                    // Permissions needed to access the options panel.
                    'menu_icon'            => '',
                    // Specify a custom URL to an icon
                    'last_tab'             => '',
                    // Force your panel to always open to a specific tab (by id)
                    'page_icon'            => 'icon-themes',
                    // Icon displayed in the admin panel next to your menu_title
                    'page_slug'            => '_options',
                    // Page slug used to denote the panel
                    'save_defaults'        => true,
                    // On load save the defaults to DB before user clicks save or not
                    'default_show'         => false,
                    // If true, shows the default value next to each field that is not the default value.
                    'default_mark'         => '',
                    // What to print by the field's title if the value shown is default. Suggested: *
                    'show_import_export'   => true,
                    // Shows the Import/Export panel when not used as a field.

                    // CAREFUL -> These options are for advanced use only
                    'transient_time'       => 60 * MINUTE_IN_SECONDS,
                    'output'               => true,
                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                    'output_tag'           => true,
                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                    'footer_credit'     => ' ',                   // Disable the footer credit of Redux. Please leave if you can help it.

                    // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                    'database'             => '',
                    // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                    'system_info'          => false,
                    // REMOVE

                    // HINTS
                    'hints'                => array(
                        'icon'          => 'icon-question-sign',
                        'icon_position' => 'right',
                        'icon_color'    => 'lightgray',
                        'icon_size'     => 'normal',
                        'tip_style'     => array(
                            'color'   => 'light',
                            'shadow'  => true,
                            'rounded' => false,
                            'style'   => '',
                        ),
                        'tip_position'  => array(
                            'my' => 'top left',
                            'at' => 'bottom right',
                        ),
                        'tip_effect'    => array(
                            'show' => array(
                                'effect'   => 'slide',
                                'duration' => '500',
                                'event'    => 'mouseover',
                            ),
                            'hide' => array(
                                'effect'   => 'slide',
                                'duration' => '500',
                                'event'    => 'click mouseleave',
                            ),
                        ),
                    )
                );

                // ADMIN BAR LINKS -> Setup custom links in the admin bar menu as external items.
                $this->args['admin_bar_links'][] = array(
                    'id'    => 'redux-docs',
                    'href'   => 'http://docs.reduxframework.com/',
                    'title' => __( 'Documentation', 'redux-framework-demo' ),
                );

                $this->args['admin_bar_links'][] = array(
                    //'id'    => 'redux-support',
                    'href'   => 'https://github.com/ReduxFramework/redux-framework/issues',
                    'title' => __( 'Support', 'redux-framework-demo' ),
                );

                $this->args['admin_bar_links'][] = array(
                    'id'    => 'redux-extensions',
                    'href'   => 'reduxframework.com/extensions',
                    'title' => __( 'Extensions', 'redux-framework-demo' ),
                );

            }

            public function validate_callback_function( $field, $value, $existing_value ) {
                $error = true;
                $value = 'just testing';

                /*
              do your validation

              if(something) {
                $value = $value;
              } elseif(something else) {
                $error = true;
                $value = $existing_value;

              }
             */

                $return['value'] = $value;
                $field['msg']    = 'your custom error message';
                if ( $error == true ) {
                    $return['error'] = $field;
                }

                return $return;
            }

            public function class_field_callback( $field, $value ) {
                print_r( $field );
                echo '<br/>CLASS CALLBACK';
                print_r( $value );
            }

        }

        global $reduxConfig;
        $reduxConfig = new Docs_Redux_Framework_config();
    } else {
        echo "The class named Docs_Redux_Framework_config has already been called. <strong>Developers, you need to prefix this class with your company name or you'll run into problems!</strong>";
    }

    /**
     * Custom function for the callback referenced above
     */
    if ( ! function_exists( 'redux_my_custom_field' ) ):
        function redux_my_custom_field( $field, $value ) {
            print_r( $field );
            echo '<br/>';
            print_r( $value );
        }
    endif;

    /**
     * Custom function for the callback validation referenced above
     * */
    if ( ! function_exists( 'redux_validate_callback_function' ) ):
        function redux_validate_callback_function( $field, $value, $existing_value ) {
            $error = true;
            $value = 'just testing';

            /*
          do your validation

          if(something) {
            $value = $value;
          } elseif(something else) {
            $error = true;
            $value = $existing_value;

          }
         */

            $return['value'] = $value;
            $field['msg']    = 'your custom error message';
            if ( $error == true ) {
                $return['error'] = $field;
            }

            return $return;
        }
    endif;
