<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuansa Rindu – Perjalanan Hati</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --cream:   #F5F0E8;
            --warm:    #EDE3D0;
            --brown:   #3B2A1A;
            --gold:    #C4A35A;
            --gold-lt: #D4B97A;
            --dark:    #1A1008;
            --muted:   #7A6A56;
            --font-display: 'Cormorant Garamond', serif;
            --font-body:    'Jost', sans-serif;
        }

        html { scroll-behavior: smooth; }

        body {
            background: var(--cream);
            color: var(--brown);
            font-family: var(--font-body);
            font-weight: 300;
            line-height: 1.7;
            overflow-x: hidden;
        }

        /* ─── NAVBAR ─────────────────────────────────────── */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 24px 60px;
            background: rgba(245,240,232,0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(196,163,90,0.15);
        }
        .nav-logo {
            display: flex; align-items: center; gap: 10px;
            font-family: var(--font-display); font-size: 1.05rem;
            letter-spacing: 0.12em; color: var(--brown); text-decoration: none;
            font-weight: 500;
        }
        .nav-logo svg { width: 28px; height: 28px; }
        .nav-links { display: flex; gap: 36px; list-style: none; }
        .nav-links a {
            font-size: 0.7rem; letter-spacing: 0.16em; text-transform: uppercase;
            color: var(--brown); text-decoration: none; font-weight: 400;
            transition: color .25s;
        }
        .nav-links a:hover { color: var(--gold); }
        .nav-cta {
            padding: 9px 22px; border: 1.5px solid var(--brown);
            background: transparent; color: var(--brown);
            font-family: var(--font-body); font-size: 0.65rem;
            letter-spacing: 0.18em; text-transform: uppercase;
            cursor: pointer; transition: all .25s;
        }
        .nav-cta:hover { background: var(--brown); color: var(--cream); }

        /* ─── HERO ────────────────────────────────────────── */
        #hero {
            position: relative; height: 100vh; min-height: 640px;
            display: flex; align-items: flex-end;
            padding: 0 60px 72px;
            overflow: hidden;
        }
        .hero-bg {
            position: absolute; inset: 0;
            background: linear-gradient(
                135deg,
                #2C1E10 0%,
                #4A3020 30%,
                #6B4C32 55%,
                #3D2812 80%,
                #1A0E06 100%
            );
        }
        .hero-bg::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(to right, rgba(26,16,8,0.7) 35%, transparent 70%);
        }
        /* Decorative silhouette couple */
        .hero-silhouette {
            position: absolute; right: 8%; bottom: 0; top: 0;
            width: 52%;
            background:
                radial-gradient(ellipse at 60% 40%, rgba(196,163,90,0.08) 0%, transparent 60%);
            display: flex; align-items: flex-end; justify-content: center;
        }
        .hero-figures {
            width: 420px; height: 100%;
            background: linear-gradient(to bottom, transparent 5%, rgba(90,55,25,0.25) 40%, rgba(50,28,10,0.6) 80%);
            position: relative;
        }
        /* Arch texture lines */
        .hero-arch {
            position: absolute; right: 14%; top: 10%; bottom: 0;
            width: 340px;
            border-left: 1px solid rgba(196,163,90,0.18);
            border-right: 1px solid rgba(196,163,90,0.18);
            border-radius: 200px 200px 0 0;
        }
        .hero-content { position: relative; z-index: 2; max-width: 480px; }
        .hero-counter {
            position: absolute; bottom: 72px; right: 60px;
            color: rgba(245,240,232,0.5); font-size: 0.7rem;
            letter-spacing: 0.14em; display: flex; align-items: center; gap: 16px;
        }
        .hero-counter span { color: rgba(245,240,232,0.9); }
        .hero-counter::before { content: ''; width: 48px; height: 1px; background: rgba(245,240,232,0.3); }
        .hero-tag {
            font-size: 0.65rem; letter-spacing: 0.22em; text-transform: uppercase;
            color: var(--gold-lt); margin-bottom: 24px;
        }
        .hero-title {
            font-family: var(--font-display); font-size: clamp(2.8rem, 5vw, 4.2rem);
            font-weight: 300; line-height: 1.15; color: #F5F0E8; margin-bottom: 20px;
        }
        .hero-title em { font-style: italic; color: var(--gold); display: block; }
        .hero-desc {
            font-size: 0.82rem; color: rgba(245,240,232,0.65);
            max-width: 320px; margin-bottom: 40px; line-height: 1.8;
        }
        .hero-cta {
            display: inline-flex; align-items: center; gap: 14px;
            font-size: 0.65rem; letter-spacing: 0.2em; text-transform: uppercase;
            color: var(--gold-lt); text-decoration: none; transition: gap .3s;
        }
        .hero-cta:hover { gap: 22px; }
        .hero-cta::after {
            content: ''; display: block; width: 36px; height: 1px;
            background: var(--gold-lt); transition: width .3s;
        }
        .hero-cta:hover::after { width: 52px; }
        /* Slide dots */
        .hero-dots {
            position: absolute; left: 60px; top: 50%; transform: translateY(-50%);
            display: flex; flex-direction: column; gap: 10px; z-index: 3;
        }
        .hero-dots span {
            width: 6px; height: 6px; border-radius: 50%;
            background: rgba(245,240,232,0.25);
            transition: all .3s;
        }
        .hero-dots span.active { background: var(--gold); transform: scale(1.3); }

        /* ─── SECTION COMMONS ─────────────────────────────── */
        section { padding: 96px 60px; }
        .section-label {
            font-size: 0.62rem; letter-spacing: 0.24em; text-transform: uppercase;
            color: var(--gold); display: flex; align-items: center; gap: 14px;
            margin-bottom: 28px;
        }
        .section-label::after { content: ''; flex: 0 0 36px; height: 1px; background: var(--gold); }
        .section-heading {
            font-family: var(--font-display); font-size: clamp(2rem, 3.5vw, 3rem);
            font-weight: 300; line-height: 1.2; color: var(--brown);
        }

        /* ─── ABOUT ───────────────────────────────────────── */
        #about {
            background: var(--cream);
            display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;
        }
        .about-text .section-heading { margin-bottom: 28px; }
        .about-text p { font-size: 0.85rem; color: var(--muted); margin-bottom: 16px; }
        .about-link {
            display: inline-flex; align-items: center; gap: 12px;
            font-size: 0.65rem; letter-spacing: 0.2em; text-transform: uppercase;
            color: var(--brown); text-decoration: none; margin-top: 28px;
            transition: gap .3s;
        }
        .about-link:hover { gap: 20px; }
        .about-link::after {
            content: ''; width: 28px; height: 1px; background: var(--brown); transition: width .3s;
        }
        .about-link:hover::after { width: 44px; }
        .about-media {
            position: relative; border-radius: 2px; overflow: hidden;
            aspect-ratio: 4/3;
            background: linear-gradient(135deg, #C4A35A22, #6B4C3244, #3B2A1A66);
        }
        .about-media-inner {
            width: 100%; height: 100%;
            background: linear-gradient(
                160deg,
                #D4B97A33 0%,
                #C4A35A22 30%,
                #8B6B3E55 60%,
                #3B2A1A88 100%
            );
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: 14px;
        }
        .play-btn {
            width: 60px; height: 60px; border-radius: 50%;
            border: 1.5px solid rgba(245,240,232,0.6);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all .3s;
            background: rgba(245,240,232,0.08);
        }
        .play-btn:hover { background: rgba(245,240,232,0.18); transform: scale(1.07); }
        .play-btn svg { width: 18px; height: 18px; fill: rgba(245,240,232,0.85); margin-left: 3px; }
        .play-label {
            font-size: 0.6rem; letter-spacing: 0.22em; text-transform: uppercase;
            color: rgba(245,240,232,0.6);
        }
        /* Fabric-like decorative element */
        .about-deco {
            position: absolute; inset: 0;
            background:
                repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 40px,
                    rgba(196,163,90,0.04) 40px,
                    rgba(196,163,90,0.04) 41px
                );
        }

        /* ─── JOURNEY ─────────────────────────────────────── */
        #journey { background: var(--warm); }
        .journey-header {
            display: grid; grid-template-columns: 1fr 2fr; gap: 60px;
            align-items: start; margin-bottom: 56px;
        }
        .journey-title-col { }
        .journey-right { display: flex; flex-direction: column; justify-content: flex-end; }
        .view-all {
            display: inline-flex; align-items: center; gap: 12px;
            font-size: 0.65rem; letter-spacing: 0.2em; text-transform: uppercase;
            color: var(--brown); text-decoration: none; align-self: flex-start;
            margin-top: auto; transition: gap .3s;
        }
        .view-all:hover { gap: 20px; }
        .view-all::after { content: ''; width: 28px; height: 1px; background: var(--brown); transition: width .3s; }
        .view-all:hover::after { width: 44px; }
        .journey-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px; }
        .journey-card {
            position: relative; overflow: hidden; cursor: pointer;
            aspect-ratio: 3/4;
        }
        .journey-card-img {
            width: 100%; height: 100%;
            transition: transform .5s ease;
        }
        .journey-card:hover .journey-card-img { transform: scale(1.04); }
        /* Card backgrounds with distinct colors */
        .journey-card:nth-child(1) .journey-card-img {
            background: linear-gradient(160deg, #A8C4B8 0%, #7EA898 30%, #4A7B6A 60%, #2A4A3A 100%);
        }
        .journey-card:nth-child(2) .journey-card-img {
            background: linear-gradient(160deg, #3B2A1A 0%, #5C3D20 40%, #7A5030 65%, #2A1A0A 100%);
        }
        .journey-card:nth-child(3) .journey-card-img {
            background: linear-gradient(160deg, #B8A882 0%, #9A8060 30%, #6A5030 60%, #E8D8C0 90%);
        }
        .card-scene {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
        }
        /* Masjid silhouette card 1 */
        .masjid-dome {
            width: 180px; height: 180px;
            border-radius: 50% 50% 0 0;
            background: rgba(255,255,255,0.15);
            position: absolute; bottom: 35%;
        }
        .masjid-dome::before {
            content: ''; position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%);
            width: 12px; height: 40px; background: rgba(255,255,255,0.2);
        }
        /* Door silhouette card 2 */
        .door-shape {
            width: 100px; height: 160px;
            border-radius: 50px 50px 0 0;
            background: rgba(196,163,90,0.2);
            border: 1px solid rgba(196,163,90,0.3);
            position: absolute; bottom: 20%;
        }
        /* Lantern card 3 */
        .lantern {
            width: 30px; height: 70px;
            background: rgba(255,220,120,0.25);
            border-radius: 4px;
            position: absolute; top: 20%;
            box-shadow: 0 0 30px rgba(255,220,120,0.15);
        }
        .lantern::before {
            content: ''; position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%);
            width: 1px; height: 30px; background: rgba(255,220,120,0.3);
        }
        .journey-card-info {
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: 28px 24px 24px;
            background: linear-gradient(to top, rgba(26,16,8,0.75) 0%, transparent 100%);
        }
        .card-subtitle {
            font-size: 0.58rem; letter-spacing: 0.2em; text-transform: uppercase;
            color: var(--gold-lt); margin-bottom: 6px;
        }
        .card-title {
            font-family: var(--font-display); font-size: 1.1rem; font-weight: 400;
            color: #F5F0E8;
        }
        .card-desc { font-size: 0.7rem; color: rgba(245,240,232,0.6); margin-top: 4px; }
        .card-arrow {
            display: block; width: 28px; height: 1px;
            background: var(--gold-lt); margin-top: 14px;
            transition: width .3s;
        }
        .journey-card:hover .card-arrow { width: 44px; }

        /* ─── EXPERIENCE ──────────────────────────────────── */
        #experience {
            background: var(--dark);
            display: grid; grid-template-columns: 1fr 2fr; gap: 80px;
            align-items: center;
        }
        #experience .section-label { color: rgba(196,163,90,0.7); }
        #experience .section-label::after { background: rgba(196,163,90,0.4); }
        #experience .section-heading { color: #F5F0E8; }
        .experience-icons {
            display: grid; grid-template-columns: repeat(5, 1fr); gap: 32px;
            align-items: start;
        }
        .exp-item {
            display: flex; flex-direction: column; align-items: center;
            gap: 16px; text-align: center;
        }
        .exp-icon {
            width: 52px; height: 52px;
            border: 1px solid rgba(196,163,90,0.3);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
        }
        .exp-icon svg { width: 22px; height: 22px; stroke: var(--gold-lt); fill: none; stroke-width: 1.2; }
        .exp-label {
            font-size: 0.62rem; letter-spacing: 0.14em; text-transform: uppercase;
            color: rgba(245,240,232,0.5); line-height: 1.5;
        }

        /* ─── JOURNAL ─────────────────────────────────────── */
        #journal { background: var(--cream); }
        .journal-header {
            display: grid; grid-template-columns: 1fr 2fr; gap: 60px;
            align-items: start; margin-bottom: 48px;
        }
        .journal-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .journal-card {
            position: relative; overflow: hidden; cursor: pointer;
            border-radius: 2px; aspect-ratio: 4/3;
        }
        .journal-card-bg {
            width: 100%; height: 100%; transition: transform .5s ease;
        }
        .journal-card:hover .journal-card-bg { transform: scale(1.04); }
        .journal-card:nth-child(1) .journal-card-bg {
            background: linear-gradient(160deg, #8BA8B0 0%, #5A7A88 40%, #2A4A55 70%, #1A2A30 100%);
        }
        .journal-card:nth-child(2) .journal-card-bg {
            background: linear-gradient(160deg, #7A9070 0%, #4A6040 40%, #2A3820 70%, #C8D8B8 90%);
        }
        .journal-card:nth-child(3) .journal-card-bg {
            background: linear-gradient(160deg, #C4A87A 0%, #8A6840 40%, #5A3820 70%, #DEC898 90%);
        }
        .journal-card-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(26,16,8,0.82) 0%, rgba(26,16,8,0.2) 50%, transparent 100%);
            padding: 24px 22px;
            display: flex; flex-direction: column; justify-content: flex-end;
        }
        .journal-card-title {
            font-family: var(--font-display); font-size: 1.2rem; font-weight: 400;
            color: #F5F0E8; line-height: 1.3; margin-bottom: 12px;
        }
        .journal-read {
            display: inline-flex; align-items: center; gap: 10px;
            font-size: 0.6rem; letter-spacing: 0.2em; text-transform: uppercase;
            color: var(--gold-lt); text-decoration: none; transition: gap .3s;
        }
        .journal-read:hover { gap: 16px; }
        .journal-read::after { content: ''; width: 22px; height: 1px; background: var(--gold-lt); transition: width .3s; }
        .journal-read:hover::after { width: 34px; }

        /* ─── FOOTER CTA ──────────────────────────────────── */
        #footer-cta {
            background: var(--dark);
            padding: 80px 60px;
            display: grid; grid-template-columns: 1fr 1fr; gap: 80px;
            align-items: center;
        }
        .footer-cta-heading {
            font-family: var(--font-display); font-size: clamp(1.8rem, 3vw, 2.8rem);
            font-weight: 300; color: #F5F0E8; line-height: 1.3;
        }
        .footer-cta-right { display: flex; flex-direction: column; gap: 28px; }
        .footer-cta-desc {
            font-size: 0.82rem; color: rgba(245,240,232,0.5);
        }
        .footer-btn {
            display: inline-flex; align-items: center; gap: 16px;
            padding: 14px 28px;
            border: 1px solid var(--gold);
            background: transparent; color: var(--gold);
            font-family: var(--font-body); font-size: 0.65rem;
            letter-spacing: 0.2em; text-transform: uppercase;
            cursor: pointer; text-decoration: none; transition: all .3s;
            align-self: flex-start;
        }
        .footer-btn:hover { background: var(--gold); color: var(--dark); }
        .footer-btn::after { content: '→'; transition: transform .3s; }
        .footer-btn:hover::after { transform: translateX(6px); }

        /* ─── FOOTER NAV ──────────────────────────────────── */
        footer {
            background: var(--dark);
            padding: 24px 60px;
            border-top: 1px solid rgba(196,163,90,0.12);
            display: flex; align-items: center; justify-content: space-between;
        }
        .footer-logo {
            font-family: var(--font-display); font-size: 0.85rem;
            letter-spacing: 0.12em; color: rgba(245,240,232,0.5); text-decoration: none;
        }
        .footer-links { display: flex; gap: 32px; list-style: none; }
        .footer-links a {
            font-size: 0.62rem; letter-spacing: 0.16em; text-transform: uppercase;
            color: rgba(245,240,232,0.35); text-decoration: none; transition: color .25s;
        }
        .footer-links a:hover { color: var(--gold-lt); }
        .footer-social { display: flex; gap: 18px; }
        .footer-social a {
            width: 34px; height: 34px;
            border: 1px solid rgba(196,163,90,0.25); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: rgba(245,240,232,0.4); text-decoration: none; font-size: 0.7rem;
            transition: all .25s;
        }
        .footer-social a:hover { border-color: var(--gold); color: var(--gold); }
        .footer-star {
            font-size: 1.1rem; color: var(--gold); opacity: 0.7;
        }

        /* ─── ANIMATIONS ──────────────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .hero-tag    { animation: fadeUp .8s .2s both; }
        .hero-title  { animation: fadeUp .8s .38s both; }
        .hero-desc   { animation: fadeUp .8s .54s both; }
        .hero-cta    { animation: fadeUp .8s .68s both; }

        /* Scroll reveal */
        .reveal {
            opacity: 0; transform: translateY(24px);
            transition: opacity .7s ease, transform .7s ease;
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* ─── RESPONSIVE ──────────────────────────────────── */
        @media (max-width: 1024px) {
            nav, section, #about, #experience, #journal, #footer-cta, footer {
                padding-left: 32px; padding-right: 32px;
            }
            #hero { padding: 0 32px 56px; }
            #about, #experience, #footer-cta { grid-template-columns: 1fr; gap: 48px; }
            .journey-header, .journal-header { grid-template-columns: 1fr; gap: 32px; }
            .experience-icons { grid-template-columns: repeat(3, 1fr); }
            .nav-links { display: none; }
        }
        @media (max-width: 768px) {
            .journey-cards, .journal-cards { grid-template-columns: 1fr; }
            .journey-cards { gap: 2px; }
            .journal-cards { gap: 16px; }
        }
    </style>
</head>
<body>

{{-- ─── NAVBAR ─────────────────────────────────────── --}}
<nav>
    <a href="#" class="nav-logo">
        <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 4C16 4 8 10 8 20C8 24.4 11.6 28 16 28C20.4 28 24 24.4 24 20C24 10 16 4 16 4Z" stroke="#C4A35A" stroke-width="1.2" fill="none"/>
            <path d="M12 16C14 12 18 10 22 12" stroke="#C4A35A" stroke-width="0.8" fill="none" opacity="0.5"/>
            <circle cx="16" cy="6" r="1.5" fill="#C4A35A" opacity="0.6"/>
        </svg>
        NUANSA RINDU
    </a>
    <ul class="nav-links">
        <li><a href="#journey">Journey</a></li>
        <li><a href="#experience">Experience</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#journal">Journal</a></li>
        <li><a href="#footer-cta">Contact</a></li>
    </ul>
    <button class="nav-cta">Inquire</button>
</nav>

{{-- ─── HERO ────────────────────────────────────────── --}}
<section id="hero">
    <div class="hero-bg"></div>
    <div class="hero-arch"></div>
    <div class="hero-silhouette"><div class="hero-figures"></div></div>

    <div class="hero-dots">
        <span class="active"></span>
        <span></span>
        <span></span>
    </div>

    <div class="hero-content">
        <p class="hero-tag">Nuansa Rindu · Umrah & Spiritual Journey</p>
        <h1 class="hero-title">
            Perjalanan hati,<br>
            pulang membawa<br>
            <em>makna.</em>
        </h1>
        <p class="hero-desc">
            Nuansa Rindu hadir untuk menemani perjalanan spiritual Anda dengan ketenangan, kenyamanan, dan makna yang mendalam.
        </p>
        <a href="#journey" class="hero-cta">Explore Journey</a>
    </div>

    <div class="hero-counter">
        <span>01</span> 03
    </div>
</section>

{{-- ─── ABOUT ───────────────────────────────────────── --}}
<section id="about">
    <div class="about-text reveal">
        <p class="section-label">Tentang Nuansa Rindu</p>
        <h2 class="section-heading">Lebih dari perjalanan,<br>ini tentang pulang.</h2>
        <p>Kami percaya setiap langkah menuju Baitullah adalah rindu yang menemukan jalan pulang.</p>
        <p>Nuansa Rindu bukan sekadar perjalanan, tapi pengalaman spiritual yang dirancang untuk menenangkan hati dan memperkaya jiwa.</p>
        <a href="#" class="about-link">About Us</a>
    </div>
    <div class="about-media reveal">
        <div class="about-deco"></div>
        <div class="about-media-inner">
            <div class="play-btn">
                <svg viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21" fill="rgba(245,240,232,0.85)"/></svg>
            </div>
            <span class="play-label">Play Our Story</span>
        </div>
    </div>
</section>

{{-- ─── JOURNEY ─────────────────────────────────────── --}}
<section id="journey">
    <div class="journey-header">
        <div>
            <p class="section-label">Journey</p>
            <h2 class="section-heading">Pilih perjalanan<br>yang sesuai<br>dengan hati Anda.</h2>
        </div>
        <div class="journey-right">
            <a href="#" class="view-all">View All Journey</a>
        </div>
    </div>

    <div class="journey-cards reveal">
        {{-- Card 1: Rindu Classic --}}
        <div class="journey-card">
            <div class="journey-card-img">
                <div class="card-scene">
                    <div class="masjid-dome"></div>
                </div>
            </div>
            <div class="journey-card-info">
                <p class="card-subtitle">Rindu Classic</p>
                <p class="card-title">Umrah Regular</p>
                <span class="card-arrow"></span>
            </div>
        </div>

        {{-- Card 2: Rindu Signature --}}
        <div class="journey-card">
            <div class="journey-card-img">
                <div class="card-scene">
                    <div class="door-shape"></div>
                </div>
            </div>
            <div class="journey-card-info">
                <p class="card-subtitle">Rindu Signature</p>
                <p class="card-title">Umrah Premium</p>
                <span class="card-arrow"></span>
            </div>
        </div>

        {{-- Card 3: Rindu Private --}}
        <div class="journey-card">
            <div class="journey-card-img">
                <div class="card-scene">
                    <div class="lantern"></div>
                </div>
            </div>
            <div class="journey-card-info">
                <p class="card-subtitle">Rindu Private</p>
                <p class="card-title">Umrah Private & Custom</p>
                <span class="card-arrow"></span>
            </div>
        </div>
    </div>
</section>

{{-- ─── EXPERIENCE ──────────────────────────────────── --}}
<section id="experience">
    <div class="reveal">
        <p class="section-label">Experience</p>
        <h2 class="section-heading" style="color:#F5F0E8;">Dirancang untuk kenyamanan dan ketenangan Anda.</h2>
    </div>

    <div class="experience-icons reveal">
        {{-- Akomodasi --}}
        <div class="exp-item">
            <div class="exp-icon">
                <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <span class="exp-label">Akomodasi Eksklusif</span>
        </div>
        {{-- Transportasi --}}
        <div class="exp-item">
            <div class="exp-icon">
                <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <span class="exp-label">Transportasi Nyaman</span>
        </div>
        {{-- Kuliner --}}
        <div class="exp-item">
            <div class="exp-icon">
                <svg viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
            </div>
            <span class="exp-label">Kuliner Berkualitas</span>
        </div>
        {{-- Pembimbing --}}
        <div class="exp-item">
            <div class="exp-icon">
                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <span class="exp-label">Pembimbing Profesional</span>
        </div>
        {{-- Pengalaman --}}
        <div class="exp-item">
            <div class="exp-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
            <span class="exp-label">Pengalaman Bermakna</span>
        </div>
    </div>
</section>

{{-- ─── JOURNAL ─────────────────────────────────────── --}}
<section id="journal">
    <div class="journal-header reveal">
        <div>
            <p class="section-label">Journal</p>
            <h2 class="section-heading">Catatan perjalanan,<br>cerita hati.</h2>
            <a href="#" class="about-link" style="margin-top:20px;">Explore Journal</a>
        </div>
    </div>

    <div class="journal-cards reveal">
        <div class="journal-card">
            <div class="journal-card-bg"></div>
            <div class="journal-card-overlay">
                <h3 class="journal-card-title">Ketika rindu<br>menuntun langkah</h3>
                <a href="#" class="journal-read">Baca Selengkapnya</a>
            </div>
        </div>
        <div class="journal-card">
            <div class="journal-card-bg"></div>
            <div class="journal-card-overlay">
                <h3 class="journal-card-title">Pulang bukanlah<br>akhir, tapi awal</h3>
                <a href="#" class="journal-read">Baca Selengkapnya</a>
            </div>
        </div>
        <div class="journal-card">
            <div class="journal-card-bg"></div>
            <div class="journal-card-overlay">
                <h3 class="journal-card-title">Mengapa perjalanan<br>ini begitu bermakna?</h3>
                <a href="#" class="journal-read">Baca Selengkapnya</a>
            </div>
        </div>
    </div>
</section>

{{-- ─── FOOTER CTA ──────────────────────────────────── --}}
<section id="footer-cta">
    <div class="reveal">
        <span class="footer-star">✦</span>
        <h2 class="footer-cta-heading" style="margin-top:16px;">
            Setiap langkah adalah rindu yang menemukan jalan pulang.
        </h2>
    </div>
    <div class="footer-cta-right reveal">
        <p class="footer-cta-desc">Mulai perjalanan spiritual Anda bersama Nuansa Rindu.</p>
        <a href="#" class="footer-btn">Mulai Perjalanan</a>
    </div>
</section>

{{-- ─── FOOTER NAV ──────────────────────────────────── --}}
<footer>
    <a href="#" class="footer-logo">
        <span style="color:rgba(196,163,90,0.6); margin-right:8px;">✦</span>
        NUANSA RINDU
    </a>
    <ul class="footer-links">
        <li><a href="#journey">Journey</a></li>
        <li><a href="#experience">Experience</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#journal">Journal</a></li>
        <li><a href="#footer-cta">Contact</a></li>
    </ul>
    <div class="footer-social">
        <a href="#" title="Instagram">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                <circle cx="12" cy="12" r="4"/>
                <circle cx="17.5" cy="6.5" r="0.8" fill="currentColor"/>
            </svg>
        </a>
        <a href="#" title="X / Twitter">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
        </a>
    </div>
</footer>

<script>
    // Scroll reveal
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), i * 80);
            }
        });
    }, { threshold: 0.12 });
    reveals.forEach(el => observer.observe(el));

    // Hero dots cycling
    const dots = document.querySelectorAll('.hero-dots span');
    let current = 0;
    setInterval(() => {
        dots[current].classList.remove('active');
        current = (current + 1) % dots.length;
        dots[current].classList.add('active');
    }, 3200);
</script>

</body>
</html>