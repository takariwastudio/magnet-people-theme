<?php
/**
 * Home Page Template
 * Magnet People
 */
get_header(); ?>

<!-- ===== HERO ===== -->
<section class="hero">

    <!-- Ilustraciones decorativas SVG inline -->
    <div class="hero__deco" aria-hidden="true">

        <!-- Imán grande -->
        <svg class="hero__deco-item hero__deco-magnet" viewBox="0 0 200 240" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="20" y="0" width="60" height="160" rx="10" stroke="currentColor" stroke-width="14" fill="none"/>
            <rect x="120" y="0" width="60" height="160" rx="10" stroke="currentColor" stroke-width="14" fill="none"/>
            <path d="M20 80 Q20 200 100 200 Q180 200 180 80" stroke="currentColor" stroke-width="14" fill="none" stroke-linecap="round"/>
            <rect x="6" y="140" width="88" height="30" rx="6" fill="currentColor"/>
            <rect x="106" y="140" width="88" height="30" rx="6" fill="#E31E24"/>
        </svg>

        <!-- Corazón -->
        <svg class="hero__deco-item hero__deco-heart" viewBox="0 0 100 90" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M50 80 C50 80 5 50 5 25 C5 10 15 2 28 2 C38 2 46 8 50 15 C54 8 62 2 72 2 C85 2 95 10 95 25 C95 50 50 80 50 80Z" stroke="currentColor" stroke-width="5" fill="none" stroke-linejoin="round"/>
        </svg>

        <!-- Burbuja de chat -->
        <svg class="hero__deco-item hero__deco-bubble" viewBox="0 0 100 90" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="5" y="5" width="90" height="70" rx="18" stroke="currentColor" stroke-width="5" fill="none"/>
            <path d="M20 75 L15 88 L35 78" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>

        <!-- Like/Thumbs up azul -->
        <svg class="hero__deco-item hero__deco-like" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 35 L10 68 L24 68 L24 35 Z" stroke="currentColor" stroke-width="4" fill="none" stroke-linejoin="round"/>
            <path d="M24 38 L36 18 C38 14 44 14 46 18 L46 32 L64 32 C68 32 70 36 68 40 L60 66 C59 69 56 70 53 70 L24 70 Z" stroke="currentColor" stroke-width="4" fill="none" stroke-linejoin="round"/>
        </svg>

        <!-- Rayo 1 -->
        <svg class="hero__deco-item hero__deco-bolt1" viewBox="0 0 40 60" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M28 4 L8 32 L20 32 L12 56 L34 24 L22 24 Z" stroke="currentColor" stroke-width="3" fill="none" stroke-linejoin="round" stroke-linecap="round"/>
        </svg>

        <!-- Rayo 2 -->
        <svg class="hero__deco-item hero__deco-bolt2" viewBox="0 0 40 60" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M28 4 L8 32 L20 32 L12 56 L34 24 L22 24 Z" stroke="currentColor" stroke-width="3" fill="none" stroke-linejoin="round" stroke-linecap="round"/>
        </svg>

    </div>

    <div class="container">
        <div class="hero__content">
            <h1 class="hero__title">What do magnets and people have in common?</h1>
            <p class="hero__subtitle">The ability of attraction without the need for direct contact!</p>
            <a href="<?php echo esc_url(home_url('/about/')); ?>" class="btn btn--red hero__cta">Get to know us</a>
        </div>
    </div>
</section>

<!-- ===== ABOUT / 3 COLUMNAS ===== -->
<section class="section">
    <div class="container">

        <?php
        // Imágenes de la sección About — subir desde Media Library y poner los IDs aquí
        // O usar ACF: get_field('about_image_1')
        $about_imgs = [
            get_theme_mod('magnet_about_img_1', ''),
            get_theme_mod('magnet_about_img_2', ''),
            get_theme_mod('magnet_about_img_3', ''),
        ];
        ?>

        <div class="about-grid">
            <?php foreach ($about_imgs as $img_url) : ?>
                <div class="about-grid__img">
                    <?php if ($img_url) : ?>
                        <img src="<?php echo esc_url($img_url); ?>" alt="" loading="lazy">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="about-text-grid">
            <div class="about-col">
                <h3>¿Qué es Magnet?</h3>
                <p>Magnet fue establecido en el 2018 por un grupo de expertos puertorriqueños en mercadeo digital, reforzado por la agencia de comunicaciones estratégicas, DOT Communications.</p>
                <p>En el 2025, hemos decidido evolucionar nuestra misión para impactar, no solo a creadores de contenido, sino también a empresarios y profesionales expertos en sus respectivas industrias.</p>
            </div>
            <div class="about-col">
                <h3>Take a closer look</h3>
                <p>Magnet se ha convertido en una plataforma para educar, exponer y conectar con líderes en sus industrias exponiéndolos a nuevas audiencias, sirviendo como apoyo en su etapa de crecimiento.</p>
                <p>Nuestra plataforma está compuesta de una comunidad de líderes, empresarios, creadores de contenido, conocidos como People, quiénes son aceleradores en sus industrias y motivan nuevas prácticas innovadoras para Puerto Rico, Latinoamérica y Estados Unidos.</p>
            </div>
            <div class="about-col">
                <h3>¿Quienes son Magnet People?</h3>
                <p>Magnet People es una comunidad de empresarios y expertos en sus industrias, quienes han entienden la importancia de amplificar sus voces, servicios y expertise en el ambiente del mercadeo digital, convirtiéndose en creadores de contenido para su marca.</p>
            </div>
        </div>

    </div>
</section>

<!-- ===== RECURSOS / POSTS ===== -->
<section class="resources">
    <div class="container">

        <div class="resources__header">
            <h2 class="resources__title">Resources for creators</h2>
        </div>

        <div class="posts-grid">
            <?php
            $recent = new WP_Query([
                'posts_per_page' => 3,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);
            if ($recent->have_posts()) :
                while ($recent->have_posts()) : $recent->the_post();
                    get_template_part('template-parts/card', 'post');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>

        <div class="resources__more">
            <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn--red">More</a>
        </div>

    </div>
</section>

<!-- ===== CTA BANNER ===== -->
<section class="cta-banner">
    <div class="container">
        <div class="cta-banner__inner">

            <!-- Rayo decorativo -->
            <div class="cta-banner__bolt" aria-hidden="true">
                <?php echo magnet_icon('bolt'); ?>
            </div>

            <h2 class="cta-banner__title">Want to be part of the biggest Hispanic influencer network?</h2>
            <a href="<?php echo esc_url(home_url('/membresia/')); ?>" class="btn btn--red">Get Access</a>

        </div>
    </div>
</section>

<?php get_footer(); ?>
