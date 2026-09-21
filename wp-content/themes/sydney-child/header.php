<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Sydney
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    
<?php
/**
 * Optimizes CSS and LCP Image for Google Lighthouse
 * 
 * @param array $css_files Array of CSS file paths relative to the server (e.g., ['assets/style.css'])
 * @param string $lcp_image_url URL of the Largest Contentful Paint image (e.g., '/images/hero.jpg')
 * @param bool $inline_css If true, reads, minifies, and inlines the CSS. If false, loads it asynchronously.
 */
function optimize_rendering_assets($css_files = [], $lcp_image_url = null, $inline_css = true) {
    $output = '';

    // 4. PRELOAD LCP ELEMENT: Inject preload tag for the hero image
    if (!empty($lcp_image_url)) {
        $output .= '<link rel="preload" as="image" href="' . htmlspecialchars($lcp_image_url) . '">' . "\n";
    }

    foreach ($css_files as $file) {
        if ($inline_css && file_exists($file)) {
            // Read the CSS file
            $css = file_get_contents($file);

            // 2. MINIFY CSS: Remove comments, tabs, spaces, and newlines
            $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css); // Remove comments
            $css = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css); // Remove whitespace
            $css = str_replace([': ', ' {', '} ', ';}'], [':', '{', '}', '}'], $css); // Tighten syntax

            // 3. FIX FONT DISPLAY: Automatically inject font-display: swap into @font-face rules
            $css = preg_replace('/@font-face\s*\{/', '@font-face {font-display:swap;', $css);

            // 1. ELIMINATE RENDER-BLOCKING: Output as an inline <style> tag directly in the DOM
            $output .= '<style>' . $css . '</style>' . "\n";
        } else {
            // 1. ALTERNATIVE NON-BLOCKING: Load CSS asynchronously (Great for non-critical CSS)
            $file_url = htmlspecialchars($file);
            $output .= '<link rel="preload" href="' . $file_url . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
            $output .= '<noscript><link rel="stylesheet" href="' . $file_url . '"></noscript>' . "\n";
        }
    }

    echo $output;
}
?>
<?php
/*
 * REZOLVAT 2026-09-21: aici erau trei perechi de <link> care incarcau
 * fontawesome.min.css, solid.min.css si brands.min.css de la
 * https://simonamarin.ro/wp-content//themes/sydney/css/ - adresa scrisa de
 * mana, cu dublu slash, in afara sistemului de enqueue.
 *
 * Trei probleme intr-un loc:
 *   1. pe orice domeniu diferit de productie (Local, staging, o mutare de
 *      hosting) fisierele nu se incarcau si iconitele dispareau complet;
 *   2. cele trei CSS-uri atrageau dupa ele doua webfonturi de 78 si 77 KB,
 *      adica 155 KB, pentru CINCI iconite afisate pe site;
 *   3. fiind scrise direct in sablon, ocoleau wp_enqueue_style, deci niciun
 *      plugin de cache sau de optimizare nu le putea atinge.
 *
 * Inlocuite cu SVG inline - vezi inc/icons.php. Conturile sunt extrase din
 * exact aceleasi webfonturi, deci iconitele arata la fel, dar ocupa sub 3 KB
 * si nu mai genereaza nicio cerere de retea.
 */
?>

<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!--
	Title, meta description, meta keywords, meta robots, canonical link,
	and Open Graph / Twitter tags are intentionally NOT set here.
	Rank Math generates all of these per-page via wp_head() below.
	Hardcoding them here created duplicate, conflicting tags on every page.
-->

<meta name="author" content="Simona Marin">
<meta name="copyright" content="Copyright © 2024, Simona Marin">
<meta name="theme-color" content="#ffffff">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "MedicalBusiness",
  "name": "Cabinet Individual de Psihologie Simona Marin",
  "description": "Cabinetul Individual de Psihologie Simona Marin oferă servicii profesionale de consiliere și psihoterapie. Simona Marin, psiholog clinician și consilier psihologic, vă ajută să vă depășiți dificultățile și să vă atingeți obiectivele de sănătate mentală.",
  "url": "https://simonamarin.ro",
  "logo": "https://simonamarin.ro/logo.png",
  "image": "https://simonamarin.ro/wp-content/uploads/2021/10/Simona-Marin.jpg",
  "telephone": "0747668204",
  "email": "psihologsimonamarin@gmail.com",
  "priceRange": "350-450 RON",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Strada Solstițiului, Nr. 2D",
    "addressLocality": "Popești-Leordeni",
    "addressRegion": "Ilfov",
    "postalCode": "077160",
    "addressCountry": "RO"
  },
  "openingHoursSpecification": [
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday"
      ],
      "opens": "10:00",
      "closes": "19:00"
    },
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": "Saturday",
      "opens": "10:00",
      "closes": "15:00"
    }
  ],
  "sameAs": [
    "https://www.facebook.com/PsihologSimonaMarin",
    "https://www.instagram.com/simonamarin.ro/"
  ],
  "specialty": [
    {
      "@type": "MedicalSpecialty",
      "name": "Psihoterapie Individuală"
    },
    {
      "@type": "MedicalSpecialty",
      "name": "Psihoterapie de Cuplu și Familie"
    },
    {
      "@type": "MedicalSpecialty",
      "name": "Psihologie Online"
    }
  ],
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://simonamarin.ro"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Cabinet Individual de Psihologie Simona Marin",
    "url": "https://simonamarin.ro",
    "sameAs": [
      "https://www.facebook.com/PsihologSimonaMarin",
      "https://www.instagram.com/simonamarin.ro/"
    ]
  },
  "areaServed": {
    "@type": "AdministrativeArea",
    "name": "Ilfov"
  },
  "foundingDate": "2012",
  "memberOf": {
    "@type": "Organization",
    "name": "Colegiul Psihologilor din România"
  },
  "contactPoint": {
    "@type": "ContactPoint",
    "contactType": "customer support",
    "telephone": "+40747668204",
    "email": "psihologsimonamarin@gmail.com",
    "availableLanguage": ["Romanian"]
  }
}

</script>

	<meta name="msvalidate.01" content="4AF9270700715E13E0C1DE23E223D14F">

<link rel="profile" href="http://gmpg.org/xfn/11">

<link href="https://fonts.googleapis.com/css2?family=Dancing+Script&family=Poppins&family=Sacramento&family=Taviraj&display=swap" rel="preload" as="style" onload="this.rel='stylesheet'">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php if ( ! function_exists( 'has_site_icon' ) || ! has_site_icon() ) : ?>
	<?php if ( get_theme_mod('site_favicon') ) : ?>
		<link rel="shortcut icon" href="<?php echo esc_url(get_theme_mod('site_favicon')); ?>" />
	<?php endif; ?>
<?php endif; ?>

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>


<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'sydney' ); ?></a>

	<?php do_action('sydney_before_header'); //Hooked: sydney_header_clone() ?>

	<header id="masthead1" class="site-header fixed float-header" role="banner">
	<div class="header-wrap" itemscope itemtype="http://schema.org/WPHeader">
    <div class="container-fluid">
        <div class="row" itemprop="breadcrumb">
            <div class="col-md-5 col-sm-8 col-xs-12">
                <?php if ( get_theme_mod('site_logo') ) : ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo('name'); ?>" itemprop="url"><img class="site-logo" src="<?php echo esc_url(get_theme_mod('site_logo')); ?>" alt="<?php bloginfo('name'); ?>" itemprop="logo" /></a>
                    <?php if ( is_home() && !is_front_page() ) : ?>
                        <h1 class="site-title screen-reader-text" itemprop="headline"><?php bloginfo( 'name' ); ?></h1>
                    <?php endif; ?>
                <?php else : ?>
                    <h1 class="site-title" itemprop="headline"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                    <!--<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>-->	      
                <?php endif; ?>
                <div class="text8 fb header-links" >
                    <a rel="nofollow" href="https://www.facebook.com/PsihologSimonaMarin" itemprop="sameAs" aria-label="Facebook link"><?php echo simonamarin_icon( 'facebook' ); ?></a>
                    <a rel="nofollow" href="https://api.whatsapp.com/send?phone=+40747668204" itemprop="sameAs" aria-label="Whatsapp link"><?php echo simonamarin_icon( 'whatsapp' ); ?></a>
                    <a rel="nofollow" href="https://www.instagram.com/simonamarin.ro/" itemprop="sameAs" aria-label="Instagram link"><?php echo simonamarin_icon( 'instagram' ); ?></a>
                    <a href="tel:0747668204" itemprop="telephone" aria-label="Phone link"><?php echo simonamarin_icon( 'phone' ); ?></a>
                    <a href="mailto:psihologsimonamarin@gmail.com" itemprop="email" aria-label="Email link"><?php echo simonamarin_icon( 'envelope' ); ?></a>
                </div>
            </div>
            <div class="col-md-7 col-sm-4 col-xs-12">
                <div class="btn-menu" aria-label="Meniu" tabindex="0"><i class="sydney-svg-icon" aria-hidden="true"><?php sydney_get_svg_icon( 'icon-menu', true ); ?></i></div>
                <nav id="mainnav" class="mainnav" role="navigation" aria-label="Meniu principal" itemscope itemtype="http://schema.org/SiteNavigationElement">
                    <?php wp_nav_menu( array( 'theme_location' => 'primary', 'fallback_cb' => 'sydney_menu_fallback' ) ); ?>
                </nav><!-- #site-navigation -->
            </div>
        </div>
    </div>
</div>

	</header><!-- #masthead -->

	<?php do_action('sydney_after_header'); ?>

	<div class="sydney-hero-area">
		<?php sydney_slider_template(); ?>
		<div class="header-image">
			<?php sydney_header_overlay(); ?>
			<img class="header-inner" src="<?php header_image(); ?>" width="<?php echo esc_attr( get_custom_header()->width ); ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>">
		</div>
		<?php sydney_header_video(); ?>

		<?php do_action('sydney_inside_hero'); ?>
	</div>

	<?php do_action('sydney_after_hero'); ?>

	<div id="content" class="page-wrap">
		<!-- to replace here with containter-fluid-->
		<div class="container-fluid content-wrapper">
			<div class="row">