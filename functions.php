<?php
/**
 * Reveil 2026 Theme Functions
 */

function reveil2026_enqueue_assets() {
    wp_enqueue_style( 'reveil2026-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version') );
    wp_enqueue_script( 'reveil2026-animations', get_template_directory_uri() . '/assets/js/animations.js', array(), wp_get_theme()->get('Version'), true );
}
add_action( 'wp_enqueue_scripts', 'reveil2026_enqueue_assets' );

/**
 * MIGRATION & REDIRECTS — Safely transition to /liste and /programme
 */
function reveil2026_migration_and_redirects() {
    if ( is_admin() ) return;

    // 1. One-time Slug Update (Database)
    $alt = get_page_by_path('l-alternative');
    if ( $alt ) {
        wp_update_post([ 'ID' => $alt->ID, 'post_name' => 'liste' ]);
    }
    $man = get_page_by_path('le-manifeste');
    if ( $man ) {
        wp_update_post([ 'ID' => $man->ID, 'post_name' => 'programme' ] );
    }

    // 2. 301 Redirects (Fallback)
    $path = trim( $_SERVER['REQUEST_URI'], '/' );
    if ( $path === 'l-alternative' ) {
        wp_redirect( home_url( '/liste' ), 301 );
        exit;
    }
    if ( $path === 'le-manifeste' ) {
        wp_redirect( home_url( '/programme' ), 301 );
        exit;
    }
    // 3. Auto-Create Analysis Pages (Self-Healing)
    $analysis_pages = [
        'analysis-naissance'     => 'La naissance du Réveil',
        'analysis-partisans'     => 'Les mouvements apartisans',
        'analysis-jeunesse'      => 'Les jeunes de Clermont-Ferrand',
        'analysis-raison-etre'   => 'Notre raison d\'être',
        'analysis-audits'        => 'Audits citoyens',
    ];

    foreach ( $analysis_pages as $slug => $title ) {
        if ( ! get_page_by_path( $slug ) ) {
            wp_insert_post([
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => '<!-- wp:paragraph --><p>Template driven content.</p><!-- /wp:paragraph -->',
            ]);
        }
    }
}
add_action( 'init', 'reveil2026_migration_and_redirects' );

/**
 * TEMPLATE OVERRIDE CLEANER — Remove stale FSE database overrides
 * WordPress FSE saves template edits to the database (wp_template CPT).
 * When we update theme files, the DB version can be stale and override our new files.
 * This function removes DB overrides for our key templates so the file always wins.
 */
function reveil2026_clear_stale_template_overrides() {
    $templates_to_clear = [
        'page-liste',
        'page-analyses',
        'page-programme',
        'page-le-projet',
        'page-contact',
    ];

    foreach ( $templates_to_clear as $slug ) {
        $existing = get_posts([
            'post_type'   => 'wp_template',
            'name'        => $slug,
            'post_status' => 'any',
            'numberposts' => 1,
            'tax_query'   => [
                [
                    'taxonomy' => 'wp_theme',
                    'field'    => 'slug',
                    'terms'    => 'reveil2026',
                ],
            ],
        ]);

        foreach ( $existing as $post ) {
            wp_delete_post( $post->ID, true );
        }
    }

    // Only run once per deploy by setting a transient
    set_transient( 'reveil2026_templates_cleared', '1', HOUR_IN_SECONDS );
}

// Run only if not already cleared this hour
if ( ! get_transient( 'reveil2026_templates_cleared' ) ) {
    add_action( 'init', 'reveil2026_clear_stale_template_overrides', 5 );
}


/**
 * PER-PAGE SEO MATRIX — Le Réveil Clermontois 2026
 * Engineered to dominate all search intents for Clermont-Ferrand 2026.
 */
function reveil2026_add_seo_meta_tags() {

    // Detect current page slug — front page wins over slug
    $slug = '';
    if ( is_front_page() ) {
        $slug = ''; // always use home meta
    } elseif ( is_page() ) {
        $slug = get_post_field( 'post_name', get_queried_object_id() );
    }

    // Per-page SEO data
    $pages = [

        '' => [ // Home / front-page
            'title'       => 'Le Réveil Clermontois | Alternative citoyenne pour Clermont-Ferrand 2026',
            'description' => 'Rejoignez Le Réveil Clermontois — la seule alternative non partisane, citoyenne et ambitieuse aux élections municipales de Clermont-Ferrand 2026. Sécurité, économie, mobilités, environnement.',
            'keywords'    => 'le réveil clermontois, réveil clermontois, élections municipales clermont-ferrand 2026, municipales 2026 clermont, clemont-ferrand 2026, maire clermont 2026, alternative citoyenne clermont, candidat mairie clermont-ferrand, non partisan clermont, renouveau municipal clermont, vote clermont 2026, yannick cartailler, liste sans étiquette clermont',
        ],

        'le-projet' => [
            'title'       => 'Notre Projet | Le Réveil Clermontois — Clermont-Ferrand 2026',
            'description' => 'Découvrez le projet du Réveil Clermontois pour transformer Clermont-Ferrand : une seule ambition, entreprendre pour Clermont. Vision claire, action concrète, résultats mesurables.',
            'keywords'    => 'projet clermont 2026, programme municipal clermont-ferrand, vision clermont 2026, entreprendre pour clermont, ambition clermont, politique locale clermont, réforme municipale clermont, développement local clermont-ferrand, qui sommes nous réveil clermontois, mouvement citoyen clermont',
        ],

        'programme' => [
            'title'       => 'Programme Municipal | Le Réveil Clermontois 2026',
            'description' => 'Programme complet du Réveil Clermontois : sécurité, économie, mobilités, environnement, logement, sport, gouvernance démocratique, ville inclusive et cœur de cité pour Clermont-Ferrand.',
            'keywords'    => 'programme municipales clermont 2026, sécurité clermont-ferrand, police municipale clermont, économie locale clermont, mobilités clermont, tramway clermont, transport en commun clermont, logement clermont, centre-ville clermont, sport clermont, gouvernance démocratique clermont, ville inclusive clermont, accessibilité clermont, coeur de cité clermont, animation centre-ville clermont',
        ],

        'liste' => [
            'title'       => 'Notre Liste | Le Réveil Clermontois — Clermont-Ferrand 2026',
            'description' => 'Rencontrez l\'équipe du Réveil Clermontois : des femmes et des hommes engagés, compétents et libres de toute attache partisane pour gouverner Clermont-Ferrand autrement.',
            'keywords'    => 'équipe réveil clermontois, liste clermont 2026, candidats municipales clermont, yannick cartailler clermont, catherine boutin clermont, cherif bouzid clermont, tête de liste clermont 2026, liste sans étiquette clermont-ferrand, colistiers clermont',
        ],

        'analyses' => [
            'title'       => 'Analyses & Actualités | Le Réveil Clermontois',
            'description' => 'Analyses politiques et décryptages de l\'actualité clermontoise par Le Réveil Clermontois : sécurité, économie, mobilités, vie locale. Restez informé, pensez libre.',
            'keywords'    => 'actualité clermont-ferrand, analyse politique clermont, blog municipal clermont, revue presse clermont 2026, décryptage politique clermont, problèmes clermont-ferrand, bilan mandat clermont, information municipale clermont',
        ],

        'analysis-naissance' => [
            'title'       => 'La Naissance du Réveil | Analyse | Le Réveil Clermontois',
            'description' => 'Pourquoi Le Réveil Clermontois est-il né ? Décryptage d\'un mouvement citoyen né du terrain pour répondre au déclin de Clermont-Ferrand.',
            'keywords'    => 'naissance réveil clermontois, mouvement citoyen clermont, politique clermont 2026, alternative municipale clermont',
        ],

        'analysis-partisans' => [
            'title'       => 'Les Mouvements Apartisans | Analyse | Le Réveil Clermontois',
            'description' => 'Pourquoi les partis politiques traditionnels reculent et comment les mouvements citoyens redessinent le pouvoir local à Clermont-Ferrand.',
            'keywords'    => 'apartisme clermont, mouvement citoyen clermont, crise confiance politique, alternative municipale',
        ],

        'analysis-jeunesse' => [
            'title'       => 'Avenir de la Jeunesse | Analyse | Le Réveil Clermontois',
            'description' => 'Le rôle crucial des jeunes dans le renouveau de Clermont-Ferrand. Citoyenneté, engagement et vision d\'avenir.',
            'keywords'    => 'jeunesse clermont-ferrand, engagement jeunes clermont, avenir clermont 2026',
        ],

        'analysis-raison-etre' => [
            'title'       => 'Notre Raison d\'Être | Analyse | Le Réveil Clermontois',
            'description' => 'Comprendre l\'ADN du Réveil Clermontois : un mouvement au service des habitants, loin des logiques de partis.',
            'keywords'    => 'raison d\'être réveil clermontois, valeurs politiques clermont, engagement citoyen clermont',
        ],

        'analysis-audits' => [
            'title'       => 'Audits Citoyens Indispensables | Analyse | Le Réveil Clermontois',
            'description' => 'Pourquoi des audits financiers et sociaux sont nécessaires avant toute action municipale à Clermont-Ferrand en 2026.',
            'keywords'    => 'audit municipal clermont, finances clermont-ferrand, gestion municipale clermont, transparence politique',
        ],

        'nous-contacter' => [
            'title'       => 'Nous Rejoindre | Le Réveil Clermontois 2026',
            'description' => 'Vous souhaitez rejoindre ou soutenir Le Réveil Clermontois ? Contactez-nous, participez à la construction d\'un Clermont-Ferrand plus ambitieux, plus inclusif et plus citoyen.',
            'keywords'    => 'rejoindre réveil clermontois, soutenir candidat clermont 2026, bénévole campagne municipale clermont, contact réveil clermontois, participer élections clermont, s\'engager politique clermont, adhérent liste clermont 2026',
        ],

        'mentions-legales' => [
            'title'       => 'Mentions Légales | Le Réveil Clermontois',
            'description' => 'Mentions légales du site Le Réveil Clermontois — liste sans étiquette candidate aux élections municipales de Clermont-Ferrand 2026. Éditeur, hébergeur, financement.',
            'keywords'    => 'mentions légales réveil clermontois, propagande électorale clermont, mandataire financier clermont 2026',
        ],

        'politique-confidentialite' => [
            'title'       => 'Politique de Confidentialité | Le Réveil Clermontois',
            'description' => 'Politique de confidentialité et protection des données personnelles (RGPD) du site Le Réveil Clermontois pour les élections municipales de Clermont-Ferrand 2026.',
            'keywords'    => 'politique confidentialité réveil clermontois, RGPD données personnelles clermont, protection données campagne électorale',
        ],
    ];

    // Pick the right page data or fall back to home
    $data = $pages[ $slug ] ?? $pages[''];

    $title       = $data['title'];
    $description = $data['description'];
    $keywords    = $data['keywords'];
    $site_name   = 'Le Réveil Clermontois | Clermont-Ferrand 2026';
    $current_url = home_url( $_SERVER['REQUEST_URI'] ?? '/' );

    // ── Primary SEO ──
    echo '<title>' . esc_html( $title ) . '</title>' . "\n";
    echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
    echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '" />' . "\n";
    echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />' . "\n";
    echo '<meta name="author" content="Le Réveil Clermontois" />' . "\n";
    echo '<link rel="canonical" href="' . esc_url( $current_url ) . '" />' . "\n";
    echo '<link rel="icon" href="' . esc_url( get_template_directory_uri() ) . '/assets/images/Logo.svg" type="image/svg+xml" />' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url( get_template_directory_uri() ) . '/assets/images/Logo.svg" />' . "\n";

    // ── Open Graph ──
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $current_url ) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta property="og:locale" content="fr_FR" />' . "\n";
    echo '<meta property="og:image" content="' . esc_url( get_template_directory_uri() ) . '/assets/images/Logo.svg" />' . "\n";

    // ── Twitter / X Cards ──
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
    echo '<meta name="twitter:site" content="@ReveilClermont" />' . "\n";

    // ── Schema.org Organization (homepage only) ──
    if ( is_front_page() || $slug === '' ) {
        $schema = [
            '@context'  => 'https://schema.org',
            '@type'     => 'PoliticalParty',
            'name'      => 'Le Réveil Clermontois',
            'url'       => home_url(),
            'logo'      => get_template_directory_uri() . '/assets/images/Logo.svg',
            'contactPoint' => [
                '@type'       => 'ContactPoint',
                'contactType' => 'campaign',
                'email'       => 'contact@lereveilclermontois.fr',
            ],
            'sameAs' => [
                'https://www.facebook.com/p/Le-R%C3%A9veil-Clermontois-61580813214074/',
                'https://www.instagram.com/lereveil_2026/',
                'https://x.com/ReveilClermont',
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'reveil2026_add_seo_meta_tags', 1 );
