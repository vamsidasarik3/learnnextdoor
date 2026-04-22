<?php
/**
 * config/HomePageData.php — LearnNextDoor Home Page Data Contract
 * ─────────────────────────────────────────────────────────────────
 * This file serves as a REFERENCE for all data shapes consumed by
 * home.php and its component partials.
 *
 * In production, data flows from Frontend.php controller.
 * In development / static preview, this file can be used directly.
 *
 * Usage (standalone static preview):
 *   <?php $data = require __DIR__ . '/HomePageData.php'; extract($data); ?>
 *
 * Usage (CI4 controller override for testing):
 *   return view('frontend/home', require APPPATH . 'Config/HomePageData.php');
 * ─────────────────────────────────────────────────────────────────
 */

return [

    /* ── Page SEO ─────────────────────────────────────────────── */
    'page_title'       => 'Find Classes Near You | LearnNextDoor',
    'meta_description' => "Discover hand-picked local classes — sports, arts, coding, and more near you.",

    /* ── Top Bar ─────────────────────────────────────────────── */
    'topBar' => [
        'message'   => '✨ Join 1000+ local learners in your neighbourhood today!',
        'bgColor'   => '#3F3590',
        'textColor' => '#ffffff',
        'visible'   => true,
    ],

    /* ── Hero Section ─────────────────────────────────────────── */
    'hero' => [
        'title'           => 'Learn Something <span class="hero-h1-accent">Next Door</span>',
        'subtitle'        => 'Find the best local classes, courses, and workshops in your neighbourhood. From dance to coding — all just a doorstep away.',
        'searchAction'    => '/classes',
        'searchParam'     => 'q',
        'searchPlaceholder'=> 'What do you want to learn?',
        'searchBtnText'   => 'Search',
        'heroImage'       => '/assets/demo/hero.png',   // swap to base_url() in controller
        'badges'          => [
            [
                'position'  => 'bottom-left',
                'iconClass' => 'bi-grid-fill',
                'iconBg'    => 'linear-gradient(135deg,#F9A05E,#FF68B4)',
                'title'     => '8+ Categories',
                'subtitle'  => 'To explore',
                'animDelay' => '0s',
            ],
            [
                'position'  => 'top-right',
                'iconClass' => 'bi-star-fill',
                'iconBg'    => 'linear-gradient(135deg,#3F3590,#7778F6)',
                'title'     => '4.7+ Rating',
                'subtitle'  => 'Avg. reviews',
                'animDelay' => '1s',
            ],
        ],
    ],

    /* ── Categories ───────────────────────────────────────────── */
    'categoriesSection' => [
        'title'    => 'Explore Categories',
        'ctaText'  => 'View All Categories',
        'ctaUrl'   => '/classes',
        'sectionId'=> 'categories',
        // 'categories' key is populated from the DB by the controller ($categories).
        // When empty, category_grid_section.php renders 6 static demo cards.
        'categories' => [], // populated by controller
    ],

    /* ── Sample/Demo Categories (used in static preview mode) ─── */
    '_demo_categories' => [
        ['id'=>1,'name'=>'Performing Arts','icon'=>'bi-music-note-beamed','iconBg'=>'rgba(63,53,144,.1)','iconColor'=>'#3F3590'],
        ['id'=>2,'name'=>'Art & Craft',    'icon'=>'bi-palette-fill',     'iconBg'=>'rgba(119,120,246,.1)', 'iconColor'=>'#7778F6'],
        ['id'=>3,'name'=>'Sports Lab',     'icon'=>'bi-trophy-fill',      'iconBg'=>'rgba(249,160,94,.1)','iconColor'=>'#F9A05E'],
        ['id'=>4,'name'=>'Tech & Coding',  'icon'=>'bi-code-slash',       'iconBg'=>'rgba(255,104,180,.1)','iconColor'=>'#FF68B4'],
        ['id'=>5,'name'=>'Brain Boost',    'icon'=>'bi-lightbulb-fill',   'iconBg'=>'rgba(63,53,144,.1)', 'iconColor'=>'#3F3590'],
        ['id'=>6,'name'=>'Culinary Arts',  'icon'=>'bi-egg-fried',        'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    ],

    /* ── Listing Sections ─────────────────────────────────────── */
    'listingSections' => [
        'regular' => [
            'title'   => 'Curated For You',
            'ctaText' => 'Discover More',
            'ctaUrl'  => '/classes?type=regular',
        ],
        'workshop' => [
            'title'   => 'Upcoming Workshops',
            'ctaText' => 'Discover More',
            'ctaUrl'  => '/classes?type=workshop',
        ],
        'course' => [
            'title'   => 'Courses Near You',
            'ctaText' => 'Discover More',
            'ctaUrl'  => '/classes?type=course',
        ],
    ],

    /* ── Sample/Demo Listings (used in static preview) ─────────── */
    '_demo_listings' => [
        'regular' => [
            [
                'id'            => 101,
                'title'         => 'Rhythm & Soul: Modern Dance Workshop',
                'category_name' => 'Contemporary Dance',
                'avg_rating'    => 4.9,
                'review_count'  => 38,
                'price'         => 899,
                'price_type'    => 'monthly',
                'cover_image'   => '/assets/demo/dance.png',
                'type'          => 'regular',
                'free_trial'    => true,
                'provider_verified' => true,
            ],
            [
                'id'            => 102,
                'title'         => 'Future Creators: Python Mastery for Teens',
                'category_name' => 'Programming',
                'avg_rating'    => 5.0,
                'review_count'  => 61,
                'price'         => 2499,
                'price_type'    => 'monthly',
                'cover_image'   => '/assets/demo/coding.png',
                'type'          => 'regular',
                'free_trial'    => false,
                'is_featured'   => true,
                'provider_verified' => true,
            ],
            [
                'id'            => 103,
                'title'         => 'Morning Flow: Hatha Yoga Serenity',
                'category_name' => 'Wellness',
                'avg_rating'    => 4.8,
                'review_count'  => 24,
                'price'         => 1200,
                'price_type'    => 'monthly',
                'cover_image'   => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&q=80&w=400',
                'type'          => 'regular',
                'free_trial'    => false,
                'provider_verified' => false,
            ],
        ],
        'workshop' => [
            [
                'id'            => 201,
                'title'         => 'Watercolour Landscape Masterclass',
                'category_name' => 'Art & Craft',
                'avg_rating'    => 4.7,
                'review_count'  => 15,
                'price'         => 750,
                'price_type'    => 'session',
                'cover_image'   => 'https://images.unsplash.com/photo-1456086272160-b28b0645b729?auto=format&fit=crop&q=80&w=400',
                'type'          => 'workshop',
                'provider_verified' => true,
            ],
        ],
        'course' => [],
    ],

    /* ── CTA Banner ───────────────────────────────────────────── */
    'ctaBanner' => [
        'title'   => 'Start Teaching Today!',
        'subtitle'=> 'Join our community of expert instructors and share your passion with eager learners in your area. Easy listing, secure payouts.',
        'btnText' => 'Start as Provider',
        'btnUrl'  => '/provider/login',
        'btnId'   => 'cta-provider-btn',
        'gradFrom'=> '#F9A05E',
        'gradTo'  => '#FF68B4',
        'visible' => true,
    ],

    /* ── Nav Links (used by navbar partial) ───────────────────── */
    'navLinks' => [
        ['label' => 'Home',      'href' => '/',           'active' => true],
        ['label' => 'Browse',    'href' => '/classes',    'active' => false],
        ['label' => 'Workshops', 'href' => '/classes?type=workshop', 'active' => false],
    ],
];
