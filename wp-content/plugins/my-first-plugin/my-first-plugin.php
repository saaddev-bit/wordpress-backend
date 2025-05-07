<?php
/*
Plugin Name: My First Plugin
Description: A simple WordPress plugin for learning backend development.
Version: 1.0
Author: Saad Waheed
*/

/** 
 *Activates the plugin and sets up the database table.
*/
function mfp_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mfp_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    data varchar(255) NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    update_option( 'mfp_version', '1.0');
    error_log( 'My First Plugin activated' );
}
register_activation_hook( __FILE__, 'mfp_activate' );

/**
 * Inserts data into the custom table.
 *
 * @param string $data The data to insert.
 * @return int|bool The insert ID or false on failure.
 */
function mfp_insert_data($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mfp_data';
    return $wpdb->insert(
        $table_name,
        ['data' => $data],
        ['%s']
    );
}

/**
 * Shortcode to display a hello message.
 *
 * @return string The hello message.
 */
function mfp_shortcode() {
    return 'Hello, this is my first plugin!';
}
add_shortcode( 'mfp_hello', 'mfp_shortcode' );


/**
 * Deactivation Hook
 */
function mfp_deactivate() {
    error_log( 'My First Plugin deactivated' );
}
register_deactivation_hook( __FILE__, 'mfp_deactivate' );



/**
 * Renders a secure contact form.
 *
 * @param array $atts Shortcode attributes.
 * @return string The form HTML.
 */

 function mfp_contact_form_shortcode($atts) {
    ob_start();
    ?>
    <form method="post" action="">
        <?php wp_nonce_field( 'mfp_contact_form', 'mfp_nonce' ); ?>
        <label for="mfp_name">Name: </label>
        <input type="text" id="mfp_name" name="mfp_name" required>
        <label for="mfp_message">Message:</label>
        <textarea id="mfp_message" name="mfp_message" required></textarea>
        <input type="submit" name="mfp_submit" value="Submit">
    </form>
    <?php 
    if (isset ($_POST['mfp_submit'])) {
        mfp_process_form();
    }
    return ob_get_clean() ;
 }
add_shortcode( 'mfp_contact_form', 'mfp_contact_form_shortcode' );


/**
 * Processes the contact form submission.
 */
 function mfp_process_form() {
    if ( !isset ( $_POST['mfp_nonce']) || ! wp_verify_nonce( $_POST['mfp_nonce'], 'mfp_contact_form' )){
        echo '<p>Security check failed!</p>';
        return;
    }

    $name = isset( $_POST['mfp_name'] ) ? sanitize_text_field( wp_unslash( $_POST['mfp_name'] ) ) : '';
    $message = isset( $_POST['mfp_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mfp_message'] ) ) : '';

    if ( empty( $name ) || empty( $message ) ) {
        echo '<p>Please fill out all fields.</p>';
        return;
    }

    $data = "Name: $name, Message: $message";
    if( mfp_insert_data( $data)){
        echo '<p>Form Submitted Successfully!!</p>';
    } else {
        echo '<p>Error submitting form.</p>';
    }
 }

 /**
  * Registers the Project CPT
  */

  function mfp_register_project_cpt() {
    $labels = [
        'name' => 'Projects',
        'singular_name' => 'Project',
        'menu_name' => 'Projects',
        'name_admin_bar' => 'Project',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Project',
        'new_item' => 'New Project',
        'edit_item' => 'Edit Project',
        'view_item' => 'View Project',
        'all_items' => 'All Projects',
        'search_items'  => 'Search Projects',
        'not_found'  => 'No projects found.',
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'has_archive'  => true,
        'supports' => [ 'title', 'editor', 'thumbnail' ],
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-portfolio',
        'rewrite' => [ 'slug' => 'projects' ],
    ];
    register_post_type(' mfp_project', $args);
  }

  add_action( 'init', 'mfp_register_project_cpt' );

  /**
 * Adds a meta box for project details.
 */

 function mfp_add_project_meta_box() {
    add_meta_box( 
        'mfp_project_details', 
        'Project Details', 
        'mfp_project_meta_box_callback', 
        'mfp_project', 
        'normal', 
        'high', 
    );
 }

 add_action( 'add_meta_boxes', 'mfp_add_project_meta_box' );

 /**
 * Renders the project meta box.
 *
 * @param WP_Post $post The current post object.
 */
function mfp_project_meta_box_callback( $post ) {
    wp_nonce_field( 'mfp_project_meta', 'mfp_project_nonce' );
    $project_url = get_post_meta( $post->ID, 'mfp_project_url', true );
    $client_name = get_post_meta( $post->ID, 'mfp_client_name', true );
    ?>
    <p>
        <label for="mfp_project_url">Project URL:</label><br>
        <input type="url" id="mfp_project_url" name="mfp_project_url" value="<?php echo esc_attr( $project_url ); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="mfp_client_name">Client Name:</label><br>
        <input type="text" id="mfp_client_name" name="mfp_client_name" value="<?php echo esc_attr( $client_name ); ?>" style="width: 100%;">
    </p>
    <?php
}

/**
 * Saves the project meta data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function mfp_save_project_meta( $post_id ) {
    if ( ! isset( $_POST['mfp_project_nonce'] ) || ! wp_verify_nonce( $_POST['mfp_project_nonce'], 'mfp_project_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['mfp_project_url'] ) ) {
        update_post_meta( $post_id, 'mfp_project_url', sanitize_text_field( $_POST['mfp_project_url'] ) );
    }

    if ( isset( $_POST['mfp_client_name'] ) ) {
        update_post_meta( $post_id, 'mfp_client_name', sanitize_text_field( $_POST['mfp_client_name'] ) );
    }
}
add_action( 'save_post', 'mfp_save_project_meta' );


/**
 * Shortcode to display a list of projects.
 *
 * @param array $atts Shortcode attributes.
 * @return string The HTML output.
 */
function mfp_projects_shortcode( $atts ) {
    $atts = shortcode_atts( [
        'category' => '',
        'limit'    => 3, // Reduced to test Load More
    ], $atts, 'mfp_projects' );

    $args = [
        'post_type'      => 'mfp_project',
        'posts_per_page' => $atts['limit'],
        'paged'          => 1,
    ];

    if ( ! empty( $atts['category'] ) ) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'mfp_project_category',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ],
        ];
    }

    $query = new WP_Query( $args );
    ob_start();
    ?>
    <div class="mfp-projects" data-category="<?php echo esc_attr( $atts['category'] ); ?>" data-limit="<?php echo esc_attr( $atts['limit'] ); ?>" data-paged="1">
        <?php
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $project_url = get_post_meta( get_the_ID(), 'mfp_project_url', true );
                $client_name = get_post_meta( get_the_ID(), 'mfp_client_name', true );
                ?>
                <div class="project">
                    <h3><?php the_title(); ?></h3>
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'thumbnail' ); ?>
                    <?php endif; ?>
                    <p><?php the_content(); ?></p>
                    <p><strong>Client:</strong> <?php echo esc_html( $client_name ); ?></p>
                    <p><strong>URL:</strong> <a href="<?php echo esc_url( $project_url ); ?>"><?php echo esc_html( $project_url ); ?></p>
                </div>
                <?php
            }
            if ( $query->max_num_pages > 1 ) {
                echo '<a href="#" class="mfp-load-more">Load More</a>';
            }
        } else {
            echo '<p>No projects found.</p>';
        }
        ?>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'mfp_projects', 'mfp_projects_shortcode' );

/**
 * Handles AJAX request to load more projects.
 */
function mfp_load_more_projects() {
    check_ajax_referer( 'mfp_load_projects', 'nonce' );

    $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
    $limit    = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 3;
    $paged    = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;

    $args = [
        'post_type'      => 'mfp_project',
        'posts_per_page' => $limit,
        'paged'          => $paged,
    ];

    if ( ! empty( $category ) ) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'mfp_project_category',
                'field'    => 'slug',
                'terms'    => $category,
            ],
        ];
    }

    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $project_url = get_post_meta( get_the_ID(), 'mfp_project_url', true );
            $client_name = get_post_meta( get_the_ID(), 'mfp_client_name', true );
            ?>
            <div class="project">
                <h3><?php the_title(); ?></h3>
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'thumbnail' ); ?>
                <?php endif; ?>
                <p><?php the_content(); ?></p>
                <p><strong>Client:</strong> <?php echo esc_html( $client_name ); ?></p>
                <p><strong>URL:</strong> <a href="<?php echo esc_url( $project_url ); ?>"><?php echo esc_html( $project_url ); ?></p>
            </div>
            <?php
        }
        wp_reset_postdata();
    }

    wp_die();
}
add_action( 'wp_ajax_mfp_load_more_projects', 'mfp_load_more_projects' );
add_action( 'wp_ajax_nopriv_mfp_load_more_projects', 'mfp_load_more_projects' );
/**
 * Adds a settings page for the plugin.
 */
function mfp_add_admin_menu() {
    add_menu_page(
        'My First Plugin Settings',
        'MFP Settings',
        'manage_options',
        'mfp-settings',
        'mfp_settings_page_callback',
        'dashicons-admin-settings',
        80
    );
}
add_action( 'admin_menu', 'mfp_add_admin_menu' );

/**
 * Renders the settings page.
 */
function mfp_settings_page_callback() {
    global $wpdb;
    $table_name  = $wpdb->prefix . 'mfp_data';
    $submissions = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 10" );

    // Query Projects
    $args = [
        'post_type'      => 'mfp_project',
        'posts_per_page' => 10,
        'post_status'    => 'publish',
    ];
    $projects_query = new WP_Query( $args );

    ?>
    <div class="wrap">
        <h1>My First Plugin Settings</h1>
        <p>Welcome to the settings page for My First Plugin.</p>

        <h2>Form Submissions</h2>
        <?php
        if ( $submissions ) {
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Submission Data</th>
                        <th>Submitted On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $submissions as $submission ) : ?>
                        <tr>
                            <td><?php echo esc_html( $submission->data ); ?></td>
                            <td><?php echo esc_html( $submission->created_at ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
        } else {
            echo '<p>No submissions found.</p>';
        }
        ?>

        <h2>Manage Projects</h2>
        <?php
        if ( $projects_query->have_posts() ) {
            ?>
            <table class="wp-list-table widefat fixed striped mfp-projects-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Project URL</th>
                        <th>Client Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ( $projects_query->have_posts() ) {
                        $projects_query->the_post();
                        $project_url = get_post_meta( get_the_ID(), 'mfp_project_url', true );
                        $client_name = get_post_meta( get_the_ID(), 'mfp_client_name', true );
                        ?>
                        <tr data-project-id="<?php echo esc_attr( get_the_ID() ); ?>">
                            <td><?php the_title(); ?></td>
                            <td><?php echo esc_html( $project_url ); ?></td>
                            <td><?php echo esc_html( $client_name ); ?></td>
                            <td>
                                <a href="#" class="mfp-edit-project">Edit</a>
                            </td>
                        </tr>
                        <?php
                    }
                    wp_reset_postdata();
                    ?>
                </tbody>
            </table>
            <div id="mfp-project-edit-form" style="display: none; margin-top: 20px;">
                <h3>Edit Project</h3>
                <form id="mfp-update-project-form">
                    <input type="hidden" id="mfp-project-id" name="project_id">
                    <p>
                        <label for="mfp-edit-project-url">Project URL:</label><br>
                        <input type="url" id="mfp-edit-project-url" name="project_url" style="width: 100%;">
                    </p>
                    <p>
                        <label for="mfp-edit-client-name">Client Name:</label><br>
                        <input type="text" id="mfp-edit-client-name" name="client_name" style="width: 100%;">
                    </p>
                    <p>
                        <button type="submit" class="button button-primary">Update Project</button>
                        <button type="button" id="mfp-cancel-edit" class="button">Cancel</button>
                    </p>
                </form>
            </div>
            <?php
        } else {
            echo '<p>No projects found.</p>';
        }
        ?>
    </div>
    <?php
}

/**
 * Enqueues admin scripts and styles for the plugin.
 */
function mfp_enqueue_admin_scripts() {
    if ( isset( $_GET['page'] ) && $_GET['page'] === 'mfp-settings' ) {
        wp_enqueue_script(
            'mfp-admin-scripts',
            plugin_dir_url( __FILE__ ) . 'assets/mfp-admin-scripts.js',
            [ 'jquery' ],
            '1.0',
            true
        );

        wp_localize_script(
            'mfp-admin-scripts',
            'mfpAjax',
            [
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'mfp_update_project' ),
            ]
        );
    }
}
add_action( 'admin_enqueue_scripts', 'mfp_enqueue_admin_scripts' );

/**
 * Handles AJAX request to update project meta data.
 */
function mfp_update_project() {
    // Verify nonce
    check_ajax_referer( 'mfp_update_project', 'nonce' );

    // Check user permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => 'Insufficient permissions.' ] );
    }

    // Validate input
    $project_id = isset( $_POST['project_id'] ) ? intval( $_POST['project_id'] ) : 0;
    $project_url = isset( $_POST['project_url'] ) ? sanitize_text_field( $_POST['project_url'] ) : '';
    $client_name = isset( $_POST['client_name'] ) ? sanitize_text_field( $_POST['client_name'] ) : '';

    if ( ! $project_id || 'mfp_project' !== get_post_type( $project_id ) ) {
        wp_send_json_error( [ 'message' => 'Invalid project ID.' ] );
    }

    if ( $project_url && ! filter_var( $project_url, FILTER_VALIDATE_URL ) ) {
        wp_send_json_error( [ 'message' => 'Invalid Project URL.' ] );
    }

    // Update meta fields
    update_post_meta( $project_id, 'mfp_project_url', $project_url );
    update_post_meta( $project_id, 'mfp_client_name', $client_name );

    wp_send_json_success( [ 'message' => 'Project updated successfully.' ] );
}
add_action( 'wp_ajax_mfp_update_project', 'mfp_update_project' );

/**
 * Adds a custom dashboard widget.
 */
function mfp_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'mfp_dashboard_widget',
        'My First Plugin Overview',
        'mfp_dashboard_widget_callback'
    );
}
add_action( 'wp_dashboard_setup', 'mfp_add_dashboard_widget' );

/**
 * Renders the dashboard widget.
 */
function mfp_dashboard_widget_callback() {
    global $wpdb;
    $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}mfp_data" );
    ?>
    <p>Form Submissions: <?php echo esc_html( $count ); ?></p>
    <p><a href="<?php echo admin_url( 'admin.php?page=mfp-settings' ); ?>">Go to Settings</a></p>
    <?php
}


/**
 * Adds a custom item to the admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar The admin bar object.
 */
function mfp_customize_admin_bar( $wp_admin_bar ) {
    $wp_admin_bar->add_node( [
        'id'    => 'mfp-quick-link',
        'title' => 'MFP Settings',
        'href'  => admin_url( 'admin.php?page=mfp-settings' ),
        'meta'  => [ 'class' => 'mfp-admin-bar' ],
    ] );
}
add_action( 'admin_bar_menu', 'mfp_customize_admin_bar', 100 );

// Register Widget Area (Sidebar)
function mytheme_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'mytheme'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'mytheme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'mytheme_widgets_init');

/**
 * Enqueues scripts and styles for the plugin.
 */
function mfp_enqueue_scripts() {
    wp_enqueue_style(
        'mfp-styles',
        plugin_dir_url( __FILE__ ) . 'assets/mfp-styles.css',
        [],
        '1.0'
    );

    wp_enqueue_script(
        'mfp-scripts',
        plugin_dir_url( __FILE__ ) . 'assets/mfp-scripts.js',
        [ 'jquery' ],
        '1.0',
        true
    );

    // Default query to get max pages
    $args = [
        'post_type'      => 'mfp_project',
        'posts_per_page' => 3,
        'paged'          => 1,
    ];
    $query = new WP_Query( $args );

    wp_localize_script(
        'mfp-scripts',
        'mfpAjax',
        [
            'ajaxurl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'mfp_load_projects' ),
            'maxPages'    => $query->max_num_pages,
        ]
    );

    wp_reset_postdata();
}
add_action( 'wp_enqueue_scripts', 'mfp_enqueue_scripts' );

/**
 * Appends a View Project link to the content of single Project posts.
 *
 * @param string $content The post content.
 * @return string The modified content.
 */
function mfp_modify_project_content( $content ) {
    if ( is_singular( 'mfp_project' ) && in_the_loop() && is_main_query() ) {
        $project_url = get_post_meta( get_the_ID(), 'mfp_project_url', true );
        if ( $project_url ) {
            $content .= '<p><a href="' . esc_url( $project_url ) . '" target="_blank">View Project</a></p>';
        }
    }
    return $content;
}
add_filter( 'the_content', 'mfp_modify_project_content' );

/**
 * Shortcode to display form submissions.
 *
 * @return string The HTML output.
 */
function mfp_form_submissions_shortcode() {
    global $wpdb;
    $table_name  = $wpdb->prefix . 'mfp_data';
    $submissions = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 5" );

    ob_start();
    if ( $submissions ) {
        echo '<ul class="mfp-submissions">';
        foreach ( $submissions as $submission ) {
            echo '<li>' . esc_html( $submission->data ) . ' (Submitted: ' . esc_html( $submission->created_at ) . ')</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No submissions found.</p>';
    }
    return ob_get_clean();
}
add_shortcode( 'mfp_form_submissions', 'mfp_form_submissions_shortcode' );