<?php
/**
 * Reveil 2026 Theme Functions
 *
 * Enqueues the high-performance creative animation layer.
 */

function reveil2026_enqueue_assets() {
    // 1. Enqueue Main CSS
    wp_enqueue_style( 'reveil2026-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version') );

    // 2. Enqueue Custom Vanilla JS Animations (Lazy Loaded/Deferred High Performance Script)
    // We intentionally bypass loading React + Framer core on the frontend to maintain "Lightning-Fast" score
    // while executing the exact same magnetic/staggered effects via native Web API.
    wp_enqueue_script( 'reveil2026-animations', get_template_directory_uri() . '/assets/js/animations.js', array(), wp_get_theme()->get('Version'), true );
}
add_action( 'wp_enqueue_scripts', 'reveil2026_enqueue_assets' );

/**
 * EXPERT SEO MATRIX: Le Réveil Clermontois 2026
 * Engineered to dominate search intent for Clermont-Ferrand municipal elections.
 */
function reveil2026_add_seo_meta_tags() {
    $site_name   = "Le Réveil Clermontois | Clermont-Ferrand 2026";
    $title       = "Le Réveil Clermontois - Élections Municipales Clermont-Ferrand 2026";
    // Highly optimized meta description (150-160 characters for optimal SERP display)
    $description = "Le Réveil Clermontois. L'unique alternative citoyenne, libre et ambitieuse pour les élections municipales de 2026 à Clermont-Ferrand. Découvrez notre programme.";
    // Aggressive, localized keyword targeting all high-volume search intents
    $keywords    = "lereveilclermontois, le réveil clermontois, réveil clermontois, elections municipales clermont-ferrand 2026, municipales 2026 clermont, clermont-ferrand 2026, maire clermont-ferrand 2026, candidat mairie clermont-ferrand, programme municipal clermont, sécurité clermont-ferrand, economie clermont, mobilite clermont, alternative citoyenne clermont, politique clermont-ferrand, yannick clermont, renouveau clermont";

    // Primary SEO Meta Tags
    echo '<title>' . esc_html( $title ) . '</title>' . "\n";
    echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
    echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '" />' . "\n";
    echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />' . "\n";
    echo '<meta name="author" content="Le Réveil Clermontois" />' . "\n";
    echo '<link rel="canonical" href="' . esc_url( home_url( $_SERVER['REQUEST_URI'] ?? '/' ) ) . '" />' . "\n";

    // Advanced Open Graph (Facebook, LinkedIn)
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url( home_url( $_SERVER['REQUEST_URI'] ?? '/' ) ) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta property="og:locale" content="fr_FR" />' . "\n";

    // Advanced Twitter Cards
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
    echo '<meta name="twitter:site" content="@ReveilClermont" />' . "\n";
}
add_action( 'wp_head', 'reveil2026_add_seo_meta_tags', 1 );
