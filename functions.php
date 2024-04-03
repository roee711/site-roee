<?php
include_once get_stylesheet_directory()."/includes/classs/create-custom-post-type.php";
//include_once get_stylesheet_directory()."/blocks/recommendation-block/recommendation-block.php";
if(!function_exists('freshcode_enqueue_parent_styles')){
    function freshcode_enqueue_parent_styles() {
        wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
    }
    add_action( 'wp_enqueue_scripts', 'freshcode_enqueue_parent_styles' );
}
if(!function_exists('freshcode_enqueue_child_style_and_js')){
    function freshcode_enqueue_child_style_and_js() {
        wp_enqueue_style( 'child-style-prism', get_stylesheet_directory_uri().'/assets/css/prism.css',array(),mt_rand(10000, 99999) );
        wp_enqueue_script( 'child-js-prism', get_stylesheet_directory_uri().'/assets/js/prism.js',array(),mt_rand(10000, 99999) ,array('in_footer'=>true));
        wp_enqueue_style( 'child-style-code', get_stylesheet_directory_uri().'/sass-after/style.css',array(),mt_rand(10000, 99999) );
    }
    add_action( 'wp_enqueue_scripts', 'freshcode_enqueue_child_style_and_js' );
}
if(!function_exists('freshcode_custom_query')){
    function freshcode_custom_query( $query ) {
        if( $query->is_main_query() && ! is_admin() ) {
            $query->set( 'orderby', 'date' );
            $query->set( 'order', 'ASC' );
        }
    }
    add_action( 'pre_get_posts', 'freshcode_custom_query' );
}
if(!function_exists('freshcode_remove_action_and_filter')){
    function freshcode_remove_action_and_filter()
    {
        remove_filter('get_the_excerpt', 'marin_custom_excerpt');
        remove_filter('excerpt_more', 'marin_excerpt_more');
    }
    add_filter('init', 'freshcode_remove_action_and_filter');
}
if(!function_exists( 'freshcode_excerpt_more' ) ) {
    /**
     * Replaces "[...]" (appended to automatically generated excerpts) with ... and a option from customizer
     *
     * @return string option from customizer prepended with an ellipsis.
     */
    function freshcode_excerpt_more($more)
    {
        if (is_admin()) {
            return $more;
        }

        $more_tag_text = get_theme_mod('marin_excerpt_more_text', esc_html__('Continue Reading', 'marin'));

        $link = sprintf(
            '<p class="link-read-more"><a href="%1$s" class="av-btn av-btn-secondary av-btn-bubble">%2$s<i class="fa fa-arrow-right"></i><span class="bubble_effect"><span class="circle top-left"></span><span class="circle top-left"></span><span class="circle top-left"></span><span class="button effect-button"></span><span class="circle bottom-right"></span><span class="circle bottom-right"></span><span class="circle bottom-right"></span></span></a></p>',
            esc_url(get_permalink()),
            /* translators: %s: Name of current post */
            wp_kses_data($more_tag_text) . '<span class="screen-reader-text">' . get_the_title(get_the_ID()) . '</span>'
        );

        return $link;
    }

    add_filter('excerpt_more', 'freshcode_excerpt_more');
}
if(!function_exists( 'freshcode_custom_excerpt' ) ) {
    /**
     * Adds Continue reading link to more tag excerpts.
     *
     * function tied to the get_the_excerpt filter hook.
     *
     * @since marin Pro 1.0
     */
    function freshcode_custom_excerpt($output)
    {
        if (has_excerpt() && !is_attachment()) {
            $more_tag_text = get_theme_mod('marin_excerpt_more_text', esc_html__('Read More', 'marin'));

            $link = sprintf(
                '<p class="link-read-more"><a href="%1$s" class="av-btn av-btn-secondary av-btn-bubble">%2$s<i class="fa fa-arrow-right"></i><span class="bubble_effect"><span class="circle top-left"></span><span class="circle top-left"></span><span class="circle top-left"></span><span class="button effect-button"></span><span class="circle bottom-right"></span><span class="circle bottom-right"></span><span class="circle bottom-right"></span></span></a></p>',
                esc_url(get_permalink()),
                /* translators: %s: Name of current post */
                wp_kses_data($more_tag_text) . '<span class="screen-reader-text">' . get_the_title(get_the_ID()) . '</span>'
            );

            $link = ' &hellip; ' . $link;

            $output .= $link;
        }

        return $output;
    }

    add_filter('get_the_excerpt', 'freshcode_custom_excerpt');
}
if(!function_exists('freshcode_breadcrumbs')){

    function freshcode_breadcrumbs() {

        $showOnHome           = '1'; // 1 - Show breadcrumbs on the homepage, 0 - don't show
        $home                 = esc_html__( 'Home', 'marin' ); // Text for the 'Home' link.
        $showCurrent          = '1'; // 1 - Show current post/page title in breadcrumb, 0 - don't show.
        $before               = '<li class="active">'; // Tag before the current breadcrumb.
        $after                = '</li>'; // Tag after the current breadcrumb.
        $breadcrumb_separator = get_theme_mod( 'breadcrumb_separator', '-' ); // Fetching breadcrumb separator from theme mods.
        global $post;
        global $wp_query;
        $homeLink = home_url( '/' );

        if ( is_home() || is_front_page() ) {

            if ( $showOnHome == '1' ) {
                echo '<li><a href="' . esc_url( $homeLink ) . '">' . $home . '</a></li>';
            }
        } else {

            echo '<li><a href="' . esc_url( $homeLink ) . '">' . $home . '</a> ' . '&nbsp;' . wp_kses_post( $breadcrumb_separator ) . '&nbsp;';

            if ( is_category() ) {
                $thisCat = get_category( get_query_var( 'cat' ), false );
                if ( $thisCat->parent != 0 ) {
                    echo get_category_parents( $thisCat->parent, true,  ' - '  );
                }
                echo $before . single_cat_title( '', false ) . $after;

            } elseif ( is_search() ) {
                echo $before . esc_html__( 'Search results for', 'marin' ) . ' "' . get_search_query() . '"' . $after;

            } elseif ( is_day() ) {
                echo '<a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '">' . get_the_time( 'Y' ) . '</a> ' . '&nbsp;' . wp_kses_post( $breadcrumb_separator ) . '&nbsp;';
                echo '<a href="' . esc_url( get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) ) . '">' . get_the_time( 'F' ) . '</a> ' . '&nbsp;' . wp_kses_post( $breadcrumb_separator ) . '&nbsp;';
                echo $before . get_the_time( 'd' ) . $after;

            } elseif ( is_month() ) {
                echo '<a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '">' . get_the_time( 'Y' ) . '</a> ' . '&nbsp;' . wp_kses_post( $breadcrumb_separator ) . '&nbsp;';
                echo $before . get_the_time( 'F' ) . $after;

            } elseif ( is_year() ) {
                echo $before . get_the_time( 'Y' ) . $after;

            } elseif ( is_single() && ! is_attachment() ) {
                if ( get_post_type() != 'post' ) {
                    $post_type = get_post_type_object( get_post_type() );
                    $slug      = $post_type->rewrite;
                    echo '<a href="' . esc_url( $homeLink ) . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
                    if ( $showCurrent == '1' ) {
                        echo ' ' . '&nbsp;' . wp_kses_post( $breadcrumb_separator ) . '&nbsp;' . $before . get_the_title() . $after;
                    }
                } else {
                    $cat = get_the_category();
                    if ( ! empty( $cat ) ) {
                        $cat  = $cat[0];
                        $cats = get_category_parents( $cat, true, ' ' . '&nbsp;' . wp_kses_post( $breadcrumb_separator ) . '&nbsp;' );
                        if ( $showCurrent == '0' ) {
                            $cats = preg_replace( '#^(.+)\s$#', '$1', $cats );
                        }
                        echo $cats;
                    }
                    if ( $showCurrent == '1' ) {
                        echo $before . get_the_title() . $after;
                    }
                }
            } elseif ( is_page() && ! $post->post_parent ) {
                if ( $showCurrent == '1' ) {
                    echo $before . get_the_title() . $after;
                }
            } elseif ( is_page() && $post->post_parent ) {
                $parent_id   = $post->post_parent;
                $breadcrumbs = array();
                while ( $parent_id ) {
                    $page          = get_page( $parent_id );
                    $breadcrumbs[] = '<a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . get_the_title( $page->ID ) . '</a>' . '&nbsp;' . wp_kses_post( $breadcrumb_separator ) . '&nbsp;';
                    $parent_id     = $page->post_parent;
                }

                $breadcrumbs = array_reverse( $breadcrumbs );
                for ( $i = 0; $i < count( $breadcrumbs ); $i++ ) {
                    echo $breadcrumbs[ $i ];
                    if ( $i != count( $breadcrumbs ) - 1 ) {
                        echo ' ' . '&nbsp;' . wp_kses_post( $breadcrumb_separator ) . '&nbsp;';
                    }
                }
                if ( $showCurrent == '1' ) {
                    echo ' ' . $before . get_the_title() . $after;
                }
            }
            elseif ( is_tag() ) {
                echo $before . esc_html__( 'Posts tagged', 'marin' ) . ' "' . single_tag_title( '', false ) . '"' . $after;

            } elseif ( is_author() ) {
                $userdata = get_userdata( get_query_var( 'author' ) );
                echo $before . esc_html__( 'Articles posted by', 'marin' ) . ' ' . $userdata->display_name . $after;

            } elseif ( is_404() ) {
                echo $before . esc_html__( 'Error 404', 'marin' ) . $after;
            }
            if(is_archive() &&isset($wp_query->query['post_type']) ){
                echo $before . esc_html__(  $wp_query->query['post_type'], 'marin' ) . $after;

            }
            if ( get_query_var( 'paged' ) ) {
                echo ' (' . esc_html__( 'Page', 'marin' ) . ' ' . get_query_var( 'paged' ) . ')';
            }

            echo '</li>';
        }
    }

}
if(!function_exists('freshcode_script_footer')){

    function freshcode_script_footer(){ ?>
        <div class="freshcode-connection">
            <a href="tel:0502052837">
                <i class="fa fa-whatsapp"></i>
            </a>
            <a href="mailto:info@freshcode.com">
                <i class="fa fa-envelope"></i>
            </a>
        </div>
        <script>
            let scrollLink =document.querySelectorAll('.post-content ul li a')
            scrollLink.forEach((link)=>link.addEventListener("click", function(e) {
                e.preventDefault();
                let slef = e.target;
                let id =slef.getAttribute('href').replace("#",'');
                id =document.getElementById(id);
                id.style.marginTop = "50px";
                window.scroll({ top: id.offsetTop, left: 0, behavior: 'smooth' });
            }));
        </script>

    <?php }
    add_action('wp_footer','freshcode_script_footer');
}
if(!function_exists('freshcode_create_custom_post_type')){
    function freshcode_create_custom_post_type($nameSingle,$namePlural,$fields){
        $object = new CreateCustomPostType($nameSingle,$namePlural,$fields);
    }
    freshcode_create_custom_post_type('recommendation' ,'recommendations',     array('Site recommendation'=>
        array(
            'name_field'=>'site_recommendation'
        ,'type'=>'url')));
    freshcode_create_custom_post_type('project' ,'projects',     array('Site recommendation'=>
        array(
            'name_field'=>'site_recommendation'
        ,'type'=>'url')));


}
if(!function_exists('freshcode_load_textdomain')){
    function freshcode_load_textdomain() {
        load_child_theme_textdomain( 'freshcode', get_stylesheet_directory() . '/languages' );
    }
    add_action( 'after_setup_theme', 'freshcode_load_textdomain' );
}
if(!function_exists('freshcode_get_site_recommendation')){
    function freshcode_get_site_recommendation(){
        $site_recommendation =get_post_meta(get_the_ID(),'site_recommendation',true);
        if($site_recommendation):?>
            <div class="site-more">
                <a class="av-btn av-btn-secondary av-btn-bubble link-site-more" href="<?php echo $site_recommendation ?>" target="_blank"><?php
                    echo str_replace(array('https://www.','http://www.','https://','http://'),'',$site_recommendation); ?></a>
            </div>
        <?php
        endif;
    }
}
if(!function_exists('freshcode_get_recommendations')){
    function freshcode_get_recommendations( $atts ) {

        ob_start();
        $args = array(
            'post_type' => 'recommendation',
            'posts_per_page' =>6
        );
        $index =0;
        $count =wp_count_posts('recommendation');
        $count =(property_exists($count,'publish'))?$count->publish:0;
        $custom_query = new WP_Query( $args );
        if ($custom_query->have_posts()) :?>
                <div id="freshcode_list_recommendations">
            <?php
            while($custom_query->have_posts()) : $custom_query->the_post();
                if($index % 3 ==0):?>
                    <div class="flex-item">
                <?php endif;
                    get_template_part( 'template-parts/content/content', 'recommendation' );
                if( ($index+1) %3 ==0 || $index+1==$count):?>
                    </div>
                <?php
                endif;
                $index++;
            endwhile;
            ?>
                </div>
          <?php
        endif;
        wp_reset_postdata();
        $output =ob_get_clean();
        return $output;
    }
    add_shortcode( 'get_recommendations', 'freshcode_get_recommendations' );
}
if(!function_exists('freshcode_insert_body_class')){
    function freshcode_insert_body_class($classes) {
        global $post;
        $postName =array('about','home','contact-us');
        if($post &&property_exists($post,'post_name')
            && in_array($post->post_name,$postName)){
            $classes[] = 'freshcode-fullpage';
        }
        return $classes;
    }
    add_filter('body_class', 'freshcode_insert_body_class');
}
if(!function_exists('wpcf7_custom_reponse_html')) {
    add_filter( 'wpcf7_form_response_output', 'wpcf7_custom_reponse_html', 99, 1 );
    function wpcf7_custom_reponse_html( $html ) {
        $html = "<span>".$html."</span>";
        return $html;
    }
}
if(!function_exists('freshcode_disable_comments_post_types_support')) {
    function freshcode_disable_comments_post_types_support()
    {
        $post_types = get_post_types();
        foreach ($post_types as $post_type) {
            if (post_type_supports($post_type, 'comments')) {
                remove_post_type_support($post_type, 'comments');
                remove_post_type_support($post_type, 'trackbacks');
            }
        }
    }

    add_action('admin_init', 'freshcode_disable_comments_post_types_support');
}
if(!function_exists('freshcode_disable_comments_status')) {
    function freshcode_disable_comments_status()
    {
        return false;
    }

    add_filter('comments_open', 'freshcode_disable_comments_status', 20, 2);
    add_filter('pings_open', 'freshcode_disable_comments_status', 20, 2);
}
if(!function_exists('freshcode_disable_comments_hide_existing_comments')) {
    function freshcode_disable_comments_hide_existing_comments($comments)
    {
        $comments = array();
        return $comments;
    }
    add_filter('comments_array', 'freshcode_disable_comments_hide_existing_comments', 10, 2);
}
if(!function_exists('freshcode_disable_comments_admin_menu')) {

    function freshcode_disable_comments_admin_menu()
    {
        remove_menu_page('edit-comments.php');
    }
    add_action('admin_menu', 'freshcode_disable_comments_admin_menu');
}
if(!function_exists('freshcode_disable_comments_admin_menu_redirect')) {
    function freshcode_disable_comments_admin_menu_redirect()
    {
        global $pagenow;
        if ($pagenow === 'edit-comments.php') {
            wp_redirect(admin_url());
            exit;
        }
    }
    add_action('admin_init', 'freshcode_disable_comments_admin_menu_redirect');
}
if(!function_exists('freshcode_disable_comments_dashboard')) {

    function freshcode_disable_comments_dashboard()
    {
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    }
    add_action('admin_init', 'freshcode_disable_comments_dashboard');
}
if(!function_exists('freshcode_disable_comments_admin_bar')) {

// Remove comments links from admin bar
    function freshcode_disable_comments_admin_bar()
    {
        if (is_admin_bar_showing()) {
            remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
        }
    }

    add_action('init', 'freshcode_disable_comments_admin_bar');
}