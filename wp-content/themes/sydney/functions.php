<?php
/**
 * Sydney functions and definitions
 *
 * @package Sydney
 */

if ( ! function_exists( 'sydney_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function remove_cf7() {
    if( ! is_page( array( 'contact', 'contact us' ) ) ) {
        add_filter( 'wpcf7_load_js', '__return_false' );
        add_filter( 'wpcf7_load_css', '__return_false' );
        remove_action( 'wp_enqueue_scripts', 'wpcf7_recaptcha_enqueue_scripts', 20, 0 );
    }
}
add_action( 'get_header', 'remove_cf7' );

function remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'remove_wp_ver_css_js', 9999 );
add_filter( 'script_loader_src', 'remove_wp_ver_css_js', 9999 );

function getPagesWithExcludedPages(){
	$menuItems = array();
	if ( $menu_items = wp_get_nav_menu_items( 'Menu set 1' ) ) {			
		foreach ( $menu_items as $menu_item ) {
			$current = ( $menu_item->object_id == get_queried_object_id() ) ? 'current' : '';
			array_push($menuItems, $menu_item->object_id );	
		}
	}

	$args = array(
		'sort_order' => 'asc',
		'sort_column' => 'post_title',
		'hierarchical' => 1,
		'exclude' => '' ,
		'include' => '',
		'meta_key' => '',
		'meta_value' => '',
		'authors' => '',
		'child_of' => 0,
		'parent' => -1,
		'exclude_tree' => '',
		'number' => '',
		'offset' => 0,
		'post_type' => 'post',
		'post_status' => 'publish'
	); 
	return $args;
}

function getArticles()
{
    $currentPageName = $_SERVER['REQUEST_URI'];
    if(!strpos ($currentPageName,"articole")) {
        return "";
    }
    echo "<div class='articles-container row'>";
	$pages = get_pages(getPagesWithExcludedPages()); 
	$posts = get_posts(array(
        'posts_per_page'	=> -1,
        'post_type'			=> 'post'
    ));
	
	foreach ($posts as $page) {
		$title = $page->post_title;
		$content = $page->post_content;

		$image = get_the_post_thumbnail($page);
		
		$imageContent = "<div class='article-img-content'>" . $image . "</div>";
		$titleContent = "<div class='col-md-12 text4'>" . $title . "</div>";
		$textContent = "<div class='textP'>" . substr(strip_tags($content),0,350) . "...</div>";
		$titleWithContent = "<div class='col-md-12'>". $imageContent . $textContent ."</div>";
		$cardContainer = "<div class='row card-content'>". $titleContent  . $titleWithContent . "</div>";
		echo "<a href=". get_page_link( $page->ID ) ." class='col-md-5 col-xs-10 articole-box'>" . $cardContainer . "</a>";
	}
	echo "</div>";
}

function getPageCards()
{
	$post = get_posts(array(
        'posts_per_page'	=> -1,
        'post_type'			=> 'post'
    ));
    $indexNr = 1;
	foreach ($post as $page) {
		$title = $page->post_title;
		$content = $page->post_content;

		$image = get_the_post_thumbnail($page);
		
		$imageContent = "<div class='col-md-12 lib-img-show img-image'>" . $image . "</div>";
		$titleContent = "<div class='col-md-12 lib-row title-cards'>" . $title . "</div>";
		$textContent = "<div class='col-md-12 card-content-text'>" . substr(strip_tags($content),0,150) . "...</div>";
		$cardContainer = "<div class='row'>" . $imageContent . $titleContent .  $textContent . "</div>";
		$clearLeft = $indexNr%3 == 1 ? "clearLeft" : "";
		$indexNr++;
		echo "<a href=". get_page_link( $page->ID ) ." class='col-md-4 " . $clearLeft . " cards-container-home img-container-home full-width clearfix'>" . $cardContainer . "</a>";
	}
}

function getKeyWords(){
	$post = get_posts(array(
        'posts_per_page'	=> -1,
        'post_type'			=> 'post'
    ));
    $bigWordsArray = array();
    $finalArray = array();
	foreach ($post as $page) {
		$content = $page->post_content;
		
		$keysArray = explode(" ", strip_tags($content));
		
		foreach($keysArray as $word){
		    if(strlen($word)> 5){
                array_push($bigWordsArray, $word);
		    }
		}
		
		foreach($bigWordsArray as $word){
		    if(isset(array_count_values($bigWordsArray)[$word]) && array_count_values($bigWordsArray)[$word] > 4){
                array_push($finalArray, $word);
		    }
            foreach($bigWordsArray as $subWord){
                unset($bigWordsArray[$subWord]);
            }
		}
		foreach($bigWordsArray as $word){
		    echo "<a href=". get_page_link( $page->ID ) . ">" . $word . "</a>";
		}
	}
}

function sydney_setup() {
	load_theme_textdomain( 'sydney', get_template_directory() . '/languages' );

	remove_action( 'wp_head', 'feed_links', 2 );
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 1170; 
	}

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_image_size('sydney-large-thumb', 830);
	add_image_size('sydney-medium-thumb', 550, 400, true);
	add_image_size('sydney-small-thumb', 230);
	add_image_size('sydney-service-thumb', 350);
	add_image_size('sydney-mas-thumb', 480);

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'sydney' ),
	) );

	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	add_theme_support( 'custom-background', apply_filters( 'sydney_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	add_theme_support( 'align-wide' );

	$forked_owl = get_theme_mod( 'forked_owl_carousel', false );
	if ( !$forked_owl ) {
		set_theme_mod( 'forked_owl_carousel', true );
	}	
}
endif; 
add_action( 'after_setup_theme', 'sydney_setup' );

function sydney_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'sydney' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	$widget_areas = get_theme_mod('footer_widget_areas', '3');
	for ($i=1; $i<=$widget_areas; $i++) {
		register_sidebar( array(
			'name'          => __( 'Footer ', 'sydney' ) . $i,
			'id'            => 'footer-' . $i,
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}

	if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
		register_widget( 'Sydney_List' );
		register_widget( 'Sydney_Services_Type_A' );
		register_widget( 'Sydney_Services_Type_B' );
		register_widget( 'Sydney_Facts' );
		register_widget( 'Sydney_Clients' );
		register_widget( 'Sydney_Testimonials' );
		register_widget( 'Sydney_Skills' );
		register_widget( 'Sydney_Action' );
		register_widget( 'Sydney_Video_Widget' );
		register_widget( 'Sydney_Social_Profile' );
		register_widget( 'Sydney_Employees' );
		register_widget( 'Sydney_Latest_News' );
		register_widget( 'Sydney_Portfolio' );
	}
	register_widget( 'Sydney_Contact_Info' );
}
add_action( 'widgets_init', 'sydney_widgets_init' );

if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
	require get_template_directory() . "/widgets/fp-list.php";
	require get_template_directory() . "/widgets/fp-services-type-a.php";
	require get_template_directory() . "/widgets/fp-services-type-b.php";
	require get_template_directory() . "/widgets/fp-facts.php";
	require get_template_directory() . "/widgets/fp-clients.php";
	require get_template_directory() . "/widgets/fp-testimonials.php";
	require get_template_directory() . "/widgets/fp-skills.php";
	require get_template_directory() . "/widgets/fp-call-to-action.php";
	require get_template_directory() . "/widgets/video-widget.php";
	require get_template_directory() . "/widgets/fp-social.php";
	require get_template_directory() . "/widgets/fp-employees.php";
	require get_template_directory() . "/widgets/fp-latest-news.php";
	require get_template_directory() . "/widgets/fp-portfolio.php";
	require get_template_directory() . '/inc/page-builder.php';	
}
require get_template_directory() . "/widgets/contact-info.php";

if ( ! defined( 'ELEMENTOR_PARTNER_ID' ) ) {
    define( 'ELEMENTOR_PARTNER_ID', 2128 );
}

function sydney_elementor_editor_scripts() {
	wp_enqueue_script( 'sydney-elementor-editor', get_template_directory_uri() . '/js/elementor.js', array( 'jquery' ), '20200504', true );
}
add_action('elementor/frontend/after_register_scripts', 'sydney_elementor_editor_scripts');

function sydney_scripts() {
	wp_enqueue_style( 'sydney-style', get_stylesheet_uri(), '', '20200129' );
	wp_enqueue_style( 'sydney-ie9', get_template_directory_uri() . '/css/ie9.css', array( 'sydney-style' ) );
	wp_style_add_data( 'sydney-ie9', 'conditional', 'lte IE 9' );
	wp_enqueue_script( 'sydney-scripts', get_template_directory_uri() . '/js/scripts.js', array('jquery'),'', true );
 
	wp_add_inline_script(
		'sydney-scripts',
		'if (typeof jQuery !== "undefined" && typeof jQuery.fn.fitVids === "undefined") { jQuery.fn.fitVids = function(){ return this; }; }',
		'after'
	);
 
	wp_enqueue_script( 'sydney-main', get_template_directory_uri() . '/js/main.min.js', array('jquery'),'20200504', true );
}
add_action( 'wp_enqueue_scripts', 'sydney_scripts' );

function sydney_disable_elementor_globals () {
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );
}
add_action('after_switch_theme', 'sydney_disable_elementor_globals');

function sydney_enqueue_bootstrap() {
	wp_enqueue_style( 'sydney-bootstrap', get_template_directory_uri() . '/css/bootstrap/bootstrap.min.css', array(), true );
}
add_action( 'wp_enqueue_scripts', 'sydney_enqueue_bootstrap', 9 );

function sydney_excerpt_length( $length ) {
  $excerpt = get_theme_mod('exc_lenght', '55');
  return $excerpt;
}
add_filter( 'excerpt_length', 'sydney_excerpt_length', 999 );

function sydney_blog_layout() {
	$layout = get_theme_mod('blog_layout','classic-alt');
	return $layout;
}

function sydney_menu_fallback() {
	if ( current_user_can('edit_theme_options') ) {
		echo '<a class="menu-fallback" href="' . admin_url('nav-menus.php') . '">' . __( 'Create your menu here', 'sydney' ) . '</a>';
	}
}

function sydney_header_overlay() {
	$overlay = get_theme_mod( 'hide_overlay', 0);
	if ( !$overlay ) {
		echo '<div class="overlay"></div>';
	}
}

function sydney_header_video() {
	if ( !function_exists('the_custom_header_markup') ) {
		return;
	}
	if ( ( get_theme_mod('front_header_type') == 'core-video' && is_front_page() || get_theme_mod('site_header_type') == 'core-video' && !is_front_page() ) ) {
		the_custom_header_markup();
	}
}

if ( function_exists('pll_register_string') ) :
function sydney_polylang() {
	for ( $i=1; $i<=5; $i++) {
		pll_register_string('Slide title ' . $i, get_theme_mod('slider_title_' . $i), 'Sydney');
		pll_register_string('Slide subtitle ' . $i, get_theme_mod('slider_subtitle_' . $i), 'Sydney');
	}
	pll_register_string('Slider button text', get_theme_mod('slider_button_text'), 'Sydney');
	pll_register_string('Slider button URL', get_theme_mod('slider_button_url'), 'Sydney');
}
add_action( 'admin_init', 'sydney_polylang' );
endif;

function sydney_preloader() {
	?>
	<div class="preloader">
	    <div class="spinner">
	        <div class="pre-bounce1"></div>
	        <div class="pre-bounce2"></div>
	    </div>
	</div>
	<?php
}
add_action('sydney_before_site', 'sydney_preloader');

function sydney_header_clone() {
	$front_header_type 	= get_theme_mod('front_header_type','nothing');
	$site_header_type 	= get_theme_mod('site_header_type');

	if ( ( $front_header_type == 'nothing' && is_front_page() ) || ( $site_header_type == 'nothing' && !is_front_page() ) ) { ?>
	<div class="header-clone"></div>
	<?php }
}
add_action('sydney_before_header', 'sydney_header_clone');

function sydney_get_image_alt( $image ) {
    global $wpdb;
    if( empty( $image ) ) {
        return false;
    }
    $attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid='%s';", strtolower( $image ) ) );
    $id = ( ! empty( $attachment ) ) ? $attachment[0] : 0;
    $alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
    return $alt;
}

function sydney_skip_link_focus_fix() {
	?>
	<script>
	/(trident|msie)/i.test(navigator.userAgent)&&document.getElementById&&window.addEventListener&&window.addEventListener("hashchange",function(){var t,e=location.hash.substring(1);/^[A-z0-9_-]+$/.test(e)&&(t=document.getElementById(e))&&(/^(?:a|select|input|button|textarea)$/i.test(t.tagName)||(t.tabIndex=-1),t.focus())},!1);
	</script>
	<?php
}
add_action( 'wp_print_footer_scripts', 'sydney_skip_link_focus_fix' );

function sydney_get_svg_icon( $icon, $echo = false ) {
	$svg_code = wp_kses( 
		Sydney_SVG_Icons::get_svg_icon( $icon ),
		array(
			'svg' => array(
				'class' => true,
				'xmlns' => true,
				'width' => true,
				'height' => true,
				'viewbox' => true,
				'aria-hidden' => true,
				'role' => true,
				'focusable' => true,
			),
			'path' => array(
				'fill' => true,
				'fill-rule' => true,
				'd' => true,
				'transform' => true,
			),
			'polygon' => array(
				'fill' => true,
				'fill-rule' => true,
				'points' => true,
				'transform' => true,
				'focusable' => true,
			),
		)
	);	

	if ( $echo != false ) {
		echo $svg_code; 
	} else {
		return $svg_code;
	}
}

require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/jetpack.php';
require get_template_directory() . '/inc/slider.php';
require get_template_directory() . '/inc/styles.php';
require get_template_directory() . '/inc/onboarding/theme-info.php';
require get_template_directory() . '/inc/woocommerce.php';

if ( class_exists( 'SitePress' ) ) {
	require get_template_directory() . '/inc/wpml/class-sydney-wpml.php';
}

require get_template_directory() . '/inc/upsell/class-customize.php';
require get_template_directory() . '/inc/editor.php';
require get_template_directory() . '/inc/fonts.php';
require get_template_directory() . '/inc/classes/class-sydney-svg-icons.php';
require_once dirname( __FILE__ ) . '/plugins/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'sydney_recommend_plugin' );
function sydney_recommend_plugin() {
	$plugins = array();
	if ( !defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
	    $plugins[] = array(
            'name' => 'Elementor',
            'slug' => 'elementor',
            'required' => false,
	    );
	}
	if ( !function_exists('wpcf_init') ) {
	    $plugins[] = array(
            'name' => 'Sydney Toolbox - custom posts and fields for the Sydney theme',
            'slug' => 'sydney-toolbox',
            'required' => false,
		);
	}
    tgmpa( $plugins);
}

require get_template_directory() . '/inc/notices/persist-admin-notices-dismissal.php';

function sydney_welcome_admin_notice() {
	if ( ! PAnD::is_admin_notice_active( 'sydney-welcome-forever' ) ) {
		return;
	}
	?>
	<div data-dismissible="sydney-welcome-forever" class="sydney-admin-notice updated notice notice-success is-dismissible">
		<p><?php echo sprintf( __( 'Welcome to Sydney. To get started please make sure to visit our <a href="%s">welcome page</a>.', 'sydney' ), admin_url( 'themes.php?page=sydney-info.php' ) ); ?></p>
		<a class="button" href="<?php echo admin_url( 'themes.php?page=sydney-info.php' ); ?>"><?php esc_html_e( 'Get started with Sydney', 'sydney' ); ?></a>
	</div>
	<?php
}

remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );

add_filter('use_block_editor_for_post_type', '__return_false', 10);
add_action( 'wp_enqueue_scripts', 'remove_block_css', 100 );
function remove_block_css() {
    wp_dequeue_style( 'wp-block-library' ); 
    wp_dequeue_style( 'wp-block-library-theme' ); 
    wp_dequeue_style( 'wc-block-style' ); 
    wp_dequeue_style( 'storefront-gutenberg-blocks' ); 
}

add_action( 'admin_init', array( 'PAnD', 'init' ) );
add_action( 'admin_notices', 'sydney_welcome_admin_notice' );

function defer_gtag_js( $tag, $handle ) {
    if ( strpos($tag, 'gtag/js') !== false ) { 
        return str_replace(' src', ' defer="defer" src', $tag);
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'defer_gtag_js', 10, 2 );

add_action( 'wp_ajax_get_amp_address', 'get_amp_address' );
add_action( 'wp_ajax_nopriv_get_amp_address', 'get_amp_address' ); 

function get_amp_address() {
  $address = get_option( 'Online' ); 
  $response = array(
    'items' => array(
      array(
        'address' => $address
      )
    )
  );
  header( 'Content-Type: application/json' );
  wp_send_json( $response );
}

/**
 * 1. Automatically add 'defer' attribute to non-critical JavaScript files
 */
function add_defer_attribute_to_js( $tag, $handle, $src ) {
    $exclude_scripts = array( 'jquery-core' );

    if ( in_array( $handle, $exclude_scripts ) ) {
        return $tag;
    }

    if ( true === strpos( $src, '.js' ) ) {
        return str_ireplace( ' src=', ' defer src=', $tag );
    }
    
    return $tag;
}
add_filter( 'script_loader_tag', 'add_defer_attribute_to_js', 10, 3 );

/**
 * 2. Fix Render-Blocking CSS using asynchronous non-blocking loader
 */
function make_css_non_render_blocking( $html, $handle, $href, $media ) {
    if ( is_admin() ) {
        return $html;
    }
    // Targets theme styles and bootstrap stylesheets to prevent render blocking
    if ( in_array( $handle, array( 'sydney-style', 'sydney-bootstrap' ) ) ) {
        return sprintf(
            '<link rel="preload" href="%s" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' .
            '<noscript><link rel="stylesheet" href="%s"></noscript>' . "\n",
            esc_url( $href ),
            esc_url( $href )
        );
    }
    return $html;
}
add_filter( 'style_loader_tag', 'make_css_non_render_blocking', 10, 4 );

/**
 * 3. Fix Font Display for FontAwesome and Custom Fonts via CSS injection
 */
function fix_font_display_rule() {
    echo '<style>
        @font-face {
            font-display: swap !important;
        }
    </style>';
}
add_action('wp_head', 'fix_font_display_rule', 1);

/**
 * 4. Set Efficient Cache Lifetimes for Static Assets
 */
function set_efficient_browser_caching() {
    if (!is_admin()) {
        $seconds = 2592000; // 30 days in seconds
        header('Cache-Control: public, max-age=' . $seconds);
    }
    echo "<!-- FUNCTIONS.PHP IS WORKING 1 -->";
}
add_action('send_headers', 'set_efficient_browser_caching');

function remove_jquery_migrate( $scripts ) {
    if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $script = $scripts->registered['jquery'];
        if ( ! empty( $script->deps ) ) {
            $script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
        }
    }
}
add_action( 'wp_default_scripts', 'remove_jquery_migrate' );
