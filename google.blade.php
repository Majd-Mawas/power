@extends(site_layout())

@section('content')
    @php
        $googleWhatsappNumber = \App\Support\WhatsappTracking::resolveNumber($settings->whatsapp_number ?: $settings->contact_phone) ?: '971501696228';
        $googleWhatsappTarget = 'https://wa.me/' . ltrim($googleWhatsappNumber, '0');
        $googleBasePhone = trim((string) ($settings->contact_phone ?: '+971 50 169 6228'));
        $googleBaseEmail = trim((string) ($settings->contact_email ?: 'powersiment@gmail.com'));
        $googleBaseBrandName = trim((string) ($settings->site_name ?: 'بورسايمنت | خدمات جامعية متكاملة'));
        $googlePageUrl = route('landing.pages.show', ['slug' => $landingPage->slug], true);
        $googleContent = \App\Support\GoogleLandingDefaults::merged(
            is_array(data_get($landingPage->meta, 'google')) ? data_get($landingPage->meta, 'google') : []
        );

        $resolveImage = function (?string $value): ?string {
            $value = trim((string) $value);

            if ($value === '') {
                return null;
            }

            if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://', '//', 'data:'])) {
                return $value;
            }

            if (\Illuminate\Support\Str::startsWith($value, ['/storage/', 'storage/'])) {
                return asset(ltrim($value, '/'));
            }

            return asset('storage/' . ltrim($value, '/'));
        };

        $listFrom = function (mixed $items): array {
            if (! is_array($items)) {
                return [];
            }

            $values = [];

            foreach ($items as $item) {
                if (is_array($item)) {
                    $item = data_get($item, 'value');
                }

                $item = trim((string) $item);
                if ($item !== '') {
                    $values[] = $item;
                }
            }

            return $values;
        };

        $googleWhatsappHref = function (string $placement, string $message) use ($googleWhatsappTarget, $googleWhatsappNumber, $googlePageUrl, $landingPage) {
            return \App\Support\WhatsappTracking::trackedRedirectUrl($googleWhatsappTarget, [
                'label' => 'واتساب',
                'value' => $googleWhatsappNumber,
                'platform' => $placement,
                'placement' => $placement,
                'source' => 'landing_page_google',
                'landing_page_id' => $landingPage->id,
                'landing_page_slug' => $landingPage->slug,
                'landing_page_url' => $googlePageUrl,
                'text' => $message,
            ], request()) ?: route('pages.contact');
        };

        $googleContactPhone = trim((string) data_get($googleContent, 'header.phone', '')) ?: $googleBasePhone;
        $resolveImage = static function (?string $value): ?string {
            $value = trim((string) $value);

            if ($value === '') {
                return null;
            }

            if (preg_match('/^https?:\/\//i', $value) === 1 || str_starts_with($value, '//')) {
                return $value;
            }

            if (str_starts_with($value, '/')) {
                return $value;
            }

            if (str_starts_with($value, 'storage/')) {
                return asset($value);
            }

            return asset('storage/' . ltrim($value, '/'));
        };

        $googleContactEmail = trim((string) data_get($googleContent, 'header.email', '')) ?: $googleBaseEmail;
        $googleBrandName = trim((string) data_get($googleContent, 'header.brand_name', '')) ?: $googleBaseBrandName;
        $googleLogoLabel = trim((string) data_get($googleContent, 'header.logo_label', 'PS')) ?: 'PS';
        $googleHeaderLogo = $resolveImage((string) data_get($googleContent, 'header.logo_image_path', ''))
            ?: $resolveImage((string) data_get($googleContent, 'header.logo_url', ''));
        if (! $googleHeaderLogo && $settings->logo_path) {
            $googleHeaderLogo = asset('storage/' . ltrim($settings->logo_path, '/'));
        }
        $googleHeaderNote = trim((string) data_get($googleContent, 'header.note', ''));

        $googleNavLinks = $listFrom(data_get($googleContent, 'nav_links'));
        $googleNavTargets = ['#hero', '#proof', '#problems', '#steps', '#compare'];

        $googleHeroTitle = trim((string) data_get($googleContent, 'hero.title', ''));
        $googleHeroSubtitle = trim((string) data_get($googleContent, 'hero.subtitle', ''));
        $googleHeroBadge = trim((string) data_get($googleContent, 'hero.badge', ''));
        $googleHeroChecks = $listFrom(data_get($googleContent, 'hero.checks'));
        $googleHeroPrimaryLabel = trim((string) data_get($googleContent, 'hero.primary_cta', ''));
        $googleHeroPrimaryMessage = trim((string) data_get($googleContent, 'hero.primary_message', ''));
        $heroImage = $resolveImage((string) data_get($googleContent, 'hero.media_image_path', ''));

        $googleProofTitle = trim((string) data_get($googleContent, 'proof.title', ''));
        $googleProofSubtitle = trim((string) data_get($googleContent, 'proof.subtitle', ''));
        $googleProofStats = array_values(array_filter(
            is_array(data_get($googleContent, 'proof.stats')) ? data_get($googleContent, 'proof.stats') : [],
            static fn ($item) => is_array($item)
                && (trim((string) data_get($item, 'value', '')) !== '' || trim((string) data_get($item, 'label', '')) !== '')
        ));
        $proofImageOne = $resolveImage((string) data_get($googleContent, 'proof.image_1_path', ''));
        $proofImageTwo = $resolveImage((string) data_get($googleContent, 'proof.image_2_path', ''));
        $googleProofCtaLabel = trim((string) data_get($googleContent, 'proof.cta', ''));
        $googleProofCtaMessage = trim((string) data_get($googleContent, 'proof.cta_message', ''));

        $googleProblemsTitle = trim((string) data_get($googleContent, 'problems.title', ''));
        $googleProblemsSubtitle = trim((string) data_get($googleContent, 'problems.subtitle', ''));
        $googleProblemsItems = array_values(array_filter(
            is_array(data_get($googleContent, 'problems.items')) ? data_get($googleContent, 'problems.items') : [],
            static fn ($item) => is_array($item) && trim((string) data_get($item, 'title', '')) !== ''
        ));
        $googleProblemsNote = trim((string) data_get($googleContent, 'problems.note', ''));

        $googleStepsTitle = trim((string) data_get($googleContent, 'steps.title', ''));
        $googleStepsSubtitle = trim((string) data_get($googleContent, 'steps.subtitle', ''));
        $googleStepsItems = array_values(array_filter(
            is_array(data_get($googleContent, 'steps.items')) ? data_get($googleContent, 'steps.items') : [],
            static fn ($item) => is_array($item) && trim((string) data_get($item, 'title', '')) !== ''
        ));
        $googleStepsCtaLabel = trim((string) data_get($googleContent, 'steps.cta', ''));
        $googleStepsCtaMessage = trim((string) data_get($googleContent, 'steps.cta_message', ''));

        $googleCompareTitle = trim((string) data_get($googleContent, 'compare.title', ''));
        $googleCompareSubtitle = trim((string) data_get($googleContent, 'compare.subtitle', ''));
        $googleCompareBeforeHeading = trim((string) data_get($googleContent, 'compare.before_heading', ''));
        $googleCompareAfterHeading = trim((string) data_get($googleContent, 'compare.after_heading', ''));
        $beforeImage = $resolveImage((string) data_get($googleContent, 'compare.before_image_path', ''));
        $afterImage = $resolveImage((string) data_get($googleContent, 'compare.after_image_path', ''));
        $googleCompareBeforeItems = $listFrom(data_get($googleContent, 'compare.before_items'));
        $googleCompareAfterItems = $listFrom(data_get($googleContent, 'compare.after_items'));

        $googleFinalTitle = trim((string) data_get($googleContent, 'final.title', ''));
        $googleFinalSubtitle = trim((string) data_get($googleContent, 'final.subtitle', ''));
        $googleFinalCtaLabel = trim((string) data_get($googleContent, 'final.cta', ''));
        $googleFinalCtaMessage = trim((string) data_get($googleContent, 'final.cta_message', ''));
        $googleFooterText = trim((string) data_get($googleContent, 'final.footer_text', ''));

        $heroCtaHref = $googleWhatsappHref('landing_google_hero_primary', $googleHeroPrimaryMessage);
        $proofCtaHref = $googleWhatsappHref('landing_google_proof_cta', $googleProofCtaMessage);
        $stepsCtaHref = $googleWhatsappHref('landing_google_steps_cta', $googleStepsCtaMessage);
        $finalCtaHref = $googleWhatsappHref('landing_google_final_cta', $googleFinalCtaMessage);
    @endphp

    @push('styles')
        <style>
            :root {
                --bg-1: #02092b;
                --bg-2: #0a1f74;
                --bg-3: #1245d4;
                --surface: rgba(8, 20, 79, 0.72);
                --surface-soft: rgba(14, 34, 118, 0.55);
                --border: rgba(133, 179, 255, 0.35);
                --text: #eaf3ff;
                --muted: rgba(232, 242, 255, 0.78);
                --accent: #2bd9b5;
                --accent-2: #2b77ff;
                --danger: #ff7b9f;
                --success: #91ffe7;
                --shadow: 0 24px 60px rgba(3, 11, 42, 0.35);
            }

            * {
                box-sizing: border-box;
            }

            html {
                scroll-behavior: smooth;
            }

            .google-lp {
                margin: 0;
                color: var(--text);
                font-family: "Cairo", "Tajawal", "Segoe UI", Arial, sans-serif;
                background:
                    radial-gradient(circle at 12% 18%, rgba(43, 217, 181, 0.22), transparent 40%),
                    radial-gradient(circle at 86% 8%, rgba(43, 119, 255, 0.28), transparent 44%),
                    radial-gradient(circle at 50% 120%, rgba(43, 119, 255, 0.25), transparent 60%),
                    linear-gradient(135deg, var(--bg-1), var(--bg-2) 50%, var(--bg-3));
                min-height: 100vh;
                line-height: 1.7;
                padding-bottom: 1rem;
                overflow-x: clip;
            }

            .google-lp a {
                text-decoration: none;
                color: inherit;
            }

            .google-lp img {
                max-width: 100%;
                display: block;
            }

            .google-lp .container {
                width: min(1160px, calc(100% - 2rem));
                margin-inline: auto;
            }

            .google-lp .grid-bg {
                position: fixed;
                inset: 0;
                pointer-events: none;
                opacity: 0.18;
                background-image:
                    linear-gradient(rgba(255, 255, 255, 0.13) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 255, 255, 0.13) 1px, transparent 1px);
                background-size: 62px 62px;
                mask-image: radial-gradient(circle at center, black 45%, transparent 100%);
            }

            .google-lp .topbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 1rem;
                padding: 0.7rem 1rem;
                font-size: 0.86rem;
                color: rgba(224, 236, 255, 0.9);
                background: rgba(4, 13, 53, 0.88);
            }

            .google-lp .brandline {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-weight: 800;
            }

            .google-lp .logo {
                width: 50px;
                height: 50px;
                border-radius: 14px;
                display: grid;
                place-items: center;
                overflow: hidden;
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
                border: 1px solid rgba(170, 205, 255, 0.18);
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.09);
                font-size: 1.1rem;
                font-weight: 900;
                color: #f7fbff;
                flex: 0 0 auto;
            }

            .google-lp .logo img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                display: block;
            }

            .google-lp .nav {
                padding: 0.9rem 1rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 1rem;
                background: rgba(11, 27, 102, 0.75);
                border-top: 1px solid rgba(92, 141, 255, 0.22);
            }

            .google-lp .nav-links {
                list-style: none;
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                margin: 0;
                padding: 0;
                color: rgba(236, 245, 255, 0.88);
                font-weight: 700;
                font-size: 0.92rem;
            }

            .google-lp .nav-links a {
                color: inherit;
                text-decoration: none;
                padding: 0.35rem 0.7rem;
                border-radius: 10px;
                display: inline-block;
                transition: background-color 0.2s ease, color 0.2s ease;
            }

            .google-lp .nav-links a:hover {
                background: rgba(255, 255, 255, 0.08);
                color: #ffffff;
            }

            .google-lp .hero {
                margin-top: 1.4rem;
                border: 1px solid rgba(151, 191, 255, 0.28);
                border-radius: 30px;
                overflow: hidden;
                box-shadow: 0 30px 80px rgba(2, 10, 40, 0.42);
                background:
                    radial-gradient(circle at 85% 15%, rgba(67, 138, 255, 0.26), transparent 22%),
                    radial-gradient(circle at 15% 85%, rgba(43, 217, 181, 0.18), transparent 25%),
                    linear-gradient(135deg, rgba(8, 22, 88, 0.98), rgba(19, 61, 190, 0.94));
                display: grid;
                grid-template-columns: 1.06fr 0.94fr;
                min-height: 640px;
                position: relative;
                place-items: stretch;
            }

            .google-lp .hero::after {
                content: "";
                position: absolute;
                inset: 0;
                z-index: 0;
                pointer-events: none;
                background:
                    linear-gradient(180deg, rgba(255, 255, 255, 0.06), transparent 22%),
                    linear-gradient(90deg, rgba(255, 255, 255, 0.03), transparent 35%, rgba(255, 255, 255, 0.02) 65%, transparent);
                mix-blend-mode: screen;
                opacity: 0.7;
            }

            .google-lp .hero > * {
                grid-row-start: auto;
                grid-column-start: auto;
                min-width: 0;
            }

            .google-lp .hero-media {
                position: relative;
                min-height: 360px;
                display: flex;
                align-items: flex-end;
                padding: 1.25rem;
                overflow: hidden;
                background:
                    linear-gradient(180deg, rgba(5, 16, 60, 0.08), rgba(5, 16, 60, 0.28)),
                    linear-gradient(90deg, rgba(8, 23, 84, 0.06), rgba(10, 30, 108, 0.18));
            }

            .google-lp .hero-media::before {
                content: "";
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(circle at 28% 24%, rgba(255, 255, 255, 0.1), transparent 24%),
                    linear-gradient(180deg, transparent 60%, rgba(3, 10, 40, 0.22) 100%);
                pointer-events: none;
                z-index: 1;
            }

            .google-lp .hero-media-image {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: contain;
                object-position: center center;
                background: #dfe4f0;
                padding: 0;
            }

            .google-lp .hero-content {
                position: relative;
                padding: 2.4rem 2.25rem 2.3rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 1.25rem;
                text-align: right;
                background:
                    linear-gradient(180deg, rgba(255, 255, 255, 0.03), transparent 12%),
                    linear-gradient(135deg, rgba(20, 70, 216, 0.88), rgba(8, 35, 120, 0.95));
                z-index: 1;
                order: 1;
                box-shadow: 0 24px 60px rgba(6, 18, 72, 0.22) inset;
            }

            .google-lp .hero-content::before {
                content: "";
                position: absolute;
                inset-inline-start: -120px;
                top: 50%;
                transform: translateY(-50%);
                width: 260px;
                height: 260px;
                border-radius: 999px;
                background: radial-gradient(circle, rgba(76, 161, 255, 0.38), rgba(76, 161, 255, 0.06) 55%, transparent 72%);
                filter: blur(8px);
                pointer-events: none;
            }

            .google-lp .hero-content > * {
                position: relative;
                z-index: 1;
            }

            .google-lp .hero-media {
                order: 2;
            }

            .google-lp .eyebrow {
                display: inline-flex;
                width: fit-content;
                align-items: center;
                gap: 0.45rem;
                border-radius: 999px;
                padding: 0.45rem 0.9rem;
                font-size: 0.82rem;
                font-weight: 800;
                border: 1px solid rgba(168, 214, 255, 0.24);
                background: rgba(255, 255, 255, 0.08);
                color: #eef7ff;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
            }

            .google-lp .hero-heading {
                margin: 0;
                font-size: clamp(2rem, 3.7vw, 3.45rem);
                line-height: 1.42;
                font-weight: 950;
                letter-spacing: -0.02em;
            }

            .google-lp .lead {
                margin: 0;
                color: rgba(236, 245, 255, 0.92);
                font-size: 1.08rem;
                max-width: 58ch;
            }

            .google-lp .checks {
                list-style: none;
                margin: 0.15rem 0 0;
                padding: 0;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.72rem 0.9rem;
            }

            .google-lp .checks li {
                display: flex;
                align-items: flex-start;
                gap: 0.6rem;
                font-size: 0.98rem;
                color: #f2f7ff;
                padding: 0.6rem 0.7rem;
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgba(160, 200, 255, 0.18);
            }

            .google-lp .checks li span:last-child {
                font-weight: 800;
                color: rgba(240, 248, 255, 0.96);
            }

            .google-lp .check {
                width: 24px;
                height: 24px;
                border-radius: 999px;
                display: inline-grid;
                place-items: center;
                flex: 0 0 auto;
                background: linear-gradient(180deg, rgba(41, 225, 188, 0.34), rgba(52, 153, 255, 0.34));
                border: 1px solid rgba(125, 241, 224, 0.45);
                color: #fff;
                font-size: 0.84rem;
                margin-top: 0.14rem;
                box-shadow: 0 8px 20px rgba(36, 166, 214, 0.18);
            }

            .google-lp .actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                align-items: center;
            }

            .google-lp .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                min-height: 54px;
                padding: 0 1.45rem;
                border-radius: 999px;
                border: 0;
                font-weight: 900;
                font-size: 1rem;
                cursor: pointer;
                transition: 0.2s transform, 0.2s filter, 0.2s box-shadow;
            }

            .google-lp .btn:hover {
                transform: translateY(-2px);
                filter: brightness(1.04);
            }

            .google-lp .btn-primary {
                color: #fff;
                background: linear-gradient(90deg, #1fd29f, #33a5ff);
                box-shadow: 0 16px 36px rgba(21, 160, 237, 0.32);
            }

            .google-lp .wa-btn {
                background: linear-gradient(90deg, #25d366, #128c7e);
                box-shadow: 0 16px 36px rgba(37, 211, 102, 0.32);
            }

            .google-lp .wa-btn:hover {
                filter: brightness(1.05);
                box-shadow: 0 18px 40px rgba(37, 211, 102, 0.36);
            }

            .google-lp .icon-22 {
                width: 22px;
                height: 22px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 22px;
            }

            .google-lp .hero-content h1 {
                max-width: 20ch;
                line-height: 1.35;
                letter-spacing: -0.01em;
                text-shadow: 0 1px 0 rgba(0, 0, 0, 0.08);
            }

            .google-lp .hero-content .actions {
                margin-top: 0.4rem;
            }

            .google-lp .section {
                margin-top: 1.5rem;
                padding: 1.4rem;
                border: 1px solid var(--border);
                border-radius: 24px;
                background: var(--surface-soft);
                backdrop-filter: blur(6px);
                scroll-margin-top: 1rem;
            }

            .google-lp .section.deep {
                background: var(--surface);
            }

            .google-lp .title {
                margin: 0;
                text-align: center;
                font-size: clamp(1.5rem, 2.8vw, 2.4rem);
                font-weight: 900;
            }

            .google-lp .subtitle {
                max-width: 820px;
                margin: 0.55rem auto 0;
                text-align: center;
                color: var(--muted);
                font-size: 1.03rem;
            }

            .google-lp .stats {
                margin-top: 1rem;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.9rem;
            }

            .google-lp .card {
                border: 1px solid rgba(130, 177, 255, 0.36);
                background: rgba(245, 250, 255, 0.09);
                border-radius: 18px;
                padding: 1rem;
            }

            .google-lp .stat {
                text-align: center;
            }

            .google-lp .stat strong {
                display: block;
                font-size: 1.7rem;
                font-weight: 900;
            }

            .google-lp .stat span {
                color: var(--muted);
                font-weight: 700;
            }

            .google-lp .proof {
                margin-top: 1rem;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.9rem;
            }

            .google-lp .proof .card {
                display: grid;
                place-items: center;
                min-height: 160px;
                text-align: center;
                color: var(--muted);
                font-weight: 800;
            }

            .google-lp .proof img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.05);
            }

            .google-lp .problems,
            .google-lp .steps,
            .google-lp .compare {
                margin-top: 1rem;
                display: grid;
                gap: 0.9rem;
            }

            .google-lp .problems {
                grid-template-columns: 1fr 1fr;
            }

            .google-lp .steps {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .google-lp .compare {
                grid-template-columns: 1fr 1fr;
            }

            .google-lp .problem h3,
            .google-lp .step h3,
            .google-lp .compare h3 {
                margin: 0.2rem 0 0;
                font-size: 1.2rem;
                line-height: 1.55;
            }

            .google-lp .muted {
                color: var(--muted);
            }

            .google-lp .icon-badge {
                width: 44px;
                height: 44px;
                border-radius: 999px;
                border: 1px solid rgba(127, 184, 255, 0.45);
                background: rgba(35, 80, 220, 0.38);
                display: grid;
                place-items: center;
                flex: 0 0 auto;
                font-weight: 900;
            }

            .google-lp .problem-head {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .google-lp .step {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 0.6rem;
                justify-content: space-between;
                min-height: 180px;
                text-align: start;
            }

            .google-lp .step::before,
            .google-lp .step::after,
            .google-lp .step-meta::before,
            .google-lp .step-meta::after,
            .google-lp .step h3::before,
            .google-lp .step h3::after,
            .google-lp .step-desc::before,
            .google-lp .step-desc::after {
                content: none !important;
                display: none !important;
            }

            .google-lp .step-meta {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .google-lp .step-index {
                font-size: 1.45rem;
                font-weight: 900;
                color: rgba(188, 215, 255, 0.9);
            }

            .google-lp .step-desc {
                margin: 0;
                color: var(--muted);
                font-size: 0.95rem;
            }

            .google-lp .note {
                text-align: center;
                margin-top: 1rem;
                font-size: 1.18rem;
                font-weight: 800;
                color: rgba(232, 245, 255, 0.92);
            }

            .google-lp .compare-head {
                display: inline-flex;
                justify-content: center;
                align-items: center;
                padding: 0.4rem 1rem;
                border-radius: 999px;
                font-size: 1rem;
                font-weight: 900;
                color: #fff;
                margin-bottom: 0.8rem;
            }

            .google-lp .good-head {
                background: linear-gradient(90deg, #2bd9b5, #2a97ff);
            }

            .google-lp .bad-head {
                background: linear-gradient(90deg, #ff5a8d, #e23f5f);
            }

            .google-lp .doc {
                min-height: 220px;
                overflow: hidden;
                border-radius: 16px;
                border: 1px solid rgba(146, 190, 255, 0.35);
                background: rgba(255, 255, 255, 0.09);
                margin-bottom: 0.8rem;
            }

            .google-lp .doc img {
                width: 100%;
                height: 100%;
                min-height: 220px;
                object-fit: cover;
            }

            .google-lp .list {
                list-style: none;
                margin: 0;
                padding: 0;
                display: grid;
                gap: 0.55rem;
                font-weight: 800;
            }

            .google-lp .list li {
                display: flex;
                gap: 0.45rem;
                align-items: flex-start;
            }

            .google-lp .good {
                color: var(--success);
            }

            .google-lp .bad {
                color: #ff9fb8;
            }

            .google-lp .final {
                text-align: center;
                background:
                    radial-gradient(circle at center, rgba(63, 124, 255, 0.32), transparent 60%),
                    rgba(8, 18, 72, 0.8);
            }

            .google-lp footer {
                padding: 1.5rem 0 2rem;
                color: rgba(232, 242, 255, 0.75);
                font-size: 0.92rem;
                text-align: center;
            }

            [dir="rtl"] .google-lp .ltr {
                direction: ltr;
                unicode-bidi: bidi-override;
            }

            @media (max-width: 1200px) {
                .google-lp .hero {
                    grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
                    min-height: 560px;
                }

                .google-lp .hero-heading {
                    font-size: clamp(1.42rem, 2.2vw, 2rem);
                }

                .google-lp .steps {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 992px) {
                .google-lp .container {
                    width: calc(100% - 1rem);
                }

                .google-lp .topbar {
                    flex-direction: column;
                    align-items: flex-start;
                    padding: 0.65rem 0.9rem;
                }

                .google-lp .nav {
                    flex-direction: column;
                    align-items: flex-start;
                    padding: 0.85rem 0.9rem;
                }

                .google-lp .brandline {
                    width: 100%;
                    justify-content: space-between;
                    flex-wrap: wrap;
                }

                .google-lp .nav-links {
                    width: 100%;
                    gap: 0.6rem 0.9rem;
                    font-size: 0.88rem;
                }

                .google-lp .hero {
                    grid-template-columns: 1fr;
                    min-height: auto;
                }

                .google-lp .hero-content {
                    padding: 1.6rem 1.25rem 1.5rem;
                }

                .google-lp .hero-content::before {
                    display: none;
                }

                .google-lp .hero-heading {
                    font-size: clamp(1.68rem, 7.6vw, 2.12rem);
                    line-height: 1.48;
                }

                .google-lp .hero-highlight,
                .google-lp .checks,
                .google-lp .stats,
                .google-lp .proof,
                .google-lp .problems,
                .google-lp .steps,
                .google-lp .compare,
                .google-lp .cta-grid {
                    grid-template-columns: 1fr;
                }

                .google-lp .hero-media {
                    min-height: 260px;
                    padding: 1rem;
                    order: 2;
                }

                .google-lp .hero-media-image {
                    position: absolute;
                    inset: 0;
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                    object-position: center center;
                }

                .google-lp .hero-content {
                    order: 1;
                }

                .google-lp .actions {
                    flex-direction: column;
                    align-items: stretch;
                }

                .google-lp .btn {
                    width: 100%;
                }

                .google-lp .step {
                    min-height: auto;
                }

                .google-lp #steps .steps {
                    grid-template-columns: 1fr !important;
                    grid-auto-flow: row !important;
                }

                .google-lp #steps .step {
                    min-height: auto;
                }
            }

            @media (max-width: 640px) {
                .google-lp .section {
                    padding: 1.15rem;
                    border-radius: 20px;
                }

                .google-lp .hero {
                    border-radius: 24px;
                }

                .google-lp .topbar .ltr {
                    font-size: 0.78rem;
                }

                .google-lp .steps {
                    grid-template-columns: 1fr;
                }

                .google-lp .doc,
                .google-lp .doc img {
                    min-height: 180px;
                }

                .google-lp .doc img {
                    object-fit: contain;
                    background: rgba(255, 255, 255, 0.05);
                }
            }
        </style>
    @endpush

    <div class="google-lp" dir="rtl">
        <div class="grid-bg" aria-hidden="true"></div>

        <header class="container">
            <div class="topbar">
                <div class="ltr">{{ $googleContactPhone }} • {{ $googleContactEmail }}</div>
                <div>{{ $googleHeaderNote }}</div>
            </div>
            <nav class="nav">
                <div class="brandline">
                    <div class="logo">
                        @if ($googleHeaderLogo)
                            <img src="{{ $googleHeaderLogo }}" alt="{{ $googleBrandName }}">
                        @else
                            {{ $googleLogoLabel }}
                        @endif
                    </div>
                    <div>{{ $googleBrandName }}</div>
                </div>
                <ul class="nav-links">
                    @foreach ($googleNavLinks as $index => $navLabel)
                        <li><a href="{{ $googleNavTargets[$index] ?? '#hero' }}">{{ $navLabel }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <section id="hero" class="hero">
                <div class="hero-media" aria-hidden="true">
                    @if ($heroImage)
                        <img class="hero-media-image" src="{{ $heroImage }}" alt="">
                    @endif
                </div>
                <div class="hero-content">
                    <span class="eyebrow">{{ $googleHeroBadge }}</span>

                    <h1 class="hero-heading">{{ $googleHeroTitle }}</h1>

                    <ul class="checks">
                        @foreach ($googleHeroChecks as $heroCheck)
                            <li><span class="check">✓</span><span>{{ $heroCheck }}</span></li>
                        @endforeach
                    </ul>

                    <p class="lead">{{ $googleHeroSubtitle }}</p>

                    <div class="actions">
                        <a class="btn btn-primary wa-btn" href="{{ $heroCtaHref }}">
                            <span class="icon-22" aria-hidden="true">
                                <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="currentColor" d="M13.601 2.326A7.854 7.854 0 0 0 8.165 0C3.734 0 .132 3.58.132 8c0 1.4.366 2.766 1.06 3.965L0 16l4.153-1.088a7.987 7.987 0 0 0 4.012 1.073h.003c4.43 0 8.032-3.58 8.032-8 0-2.14-.833-4.154-2.599-5.659zM8.165 14.67h-.002a6.62 6.62 0 0 1-3.372-.92l-.242-.145-2.463.645.657-2.4-.157-.248a6.586 6.586 0 0 1-1.007-3.5c0-3.647 2.977-6.614 6.633-6.614a6.59 6.59 0 0 1 4.708 1.942A6.56 6.56 0 0 1 14.8 8.105c-.001 3.647-2.977 6.614-6.635 6.614z"/>
                                    <path fill="currentColor" d="M11.603 9.854c-.197-.099-1.17-.578-1.351-.643-.181-.066-.313-.099-.445.099-.131.197-.51.643-.626.775-.115.131-.23.148-.427.05-.197-.1-.833-.307-1.588-.98-.588-.525-.985-1.174-1.1-1.372-.115-.197-.012-.304.087-.402.089-.088.197-.23.296-.345.099-.115.131-.197.197-.329.066-.132.033-.247-.017-.345-.05-.099-.445-1.073-.61-1.47-.16-.387-.323-.334-.445-.34-.115-.007-.247-.008-.379-.008a.727.727 0 0 0-.526.247c-.181.197-.69.676-.69 1.65 0 .972.707 1.912.805 2.044.099.131 1.39 2.124 3.37 2.977.471.203.839.324 1.125.415.472.15.902.129 1.242.078.379-.056 1.17-.478 1.336-.94.165-.461.165-.857.115-.94-.05-.082-.181-.131-.379-.23z"/>
                                </svg>
                            </span>
                            <span>{{ $googleHeroPrimaryLabel }}</span>
                        </a>
                    </div>
                </div>
            </section>

            <section id="proof" class="section">
                <h2 class="title">{{ $googleProofTitle }}</h2>
                <p class="subtitle">{{ $googleProofSubtitle }}</p>

                <div class="stats">
                    @foreach ($googleProofStats as $stat)
                        <div class="card stat">
                            <strong>{{ data_get($stat, 'value') }}</strong>
                            <span>{{ data_get($stat, 'label') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="proof">
                    <div class="card">
                        @if ($proofImageOne)
                            <img src="{{ $proofImageOne }}" alt="نماذج آراء العملاء" loading="lazy">
                        @endif
                    </div>
                    <div class="card">
                        @if ($proofImageTwo)
                            <img src="{{ $proofImageTwo }}" alt="نماذج آراء العملاء" loading="lazy">
                        @endif
                    </div>
                </div>

                <div class="actions" style="justify-content: center; margin-top: 1rem;">
                    <a class="btn btn-primary wa-btn" href="{{ $proofCtaHref }}">
                        <span class="icon-22" aria-hidden="true">
                            <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M13.601 2.326A7.854 7.854 0 0 0 8.165 0C3.734 0 .132 3.58.132 8c0 1.4.366 2.766 1.06 3.965L0 16l4.153-1.088a7.987 7.987 0 0 0 4.012 1.073h.003c4.43 0 8.032-3.58 8.032-8 0-2.14-.833-4.154-2.599-5.659zM8.165 14.67h-.002a6.62 6.62 0 0 1-3.372-.92l-.242-.145-2.463.645.657-2.4-.157-.248a6.586 6.586 0 0 1-1.007-3.5c0-3.647 2.977-6.614 6.633-6.614a6.59 6.59 0 0 1 4.708 1.942A6.56 6.56 0 0 1 14.8 8.105c-.001 3.647-2.977 6.614-6.635 6.614z"/>
                                <path fill="currentColor" d="M11.603 9.854c-.197-.099-1.17-.578-1.351-.643-.181-.066-.313-.099-.445.099-.131.197-.51.643-.626.775-.115.131-.23.148-.427.05-.197-.1-.833-.307-1.588-.98-.588-.525-.985-1.174-1.1-1.372-.115-.197-.012-.304.087-.402.089-.088.197-.23.296-.345.099-.115.131-.197.197-.329.066-.132.033-.247-.017-.345-.05-.099-.445-1.073-.61-1.47-.16-.387-.323-.334-.445-.34-.115-.007-.247-.008-.379-.008a.727.727 0 0 0-.526.247c-.181.197-.69.676-.69 1.65 0 .972.707 1.912.805 2.044.099.131 1.39 2.124 3.37 2.977.471.203.839.324 1.125.415.472.15.902.129 1.242.078.379-.056 1.17-.478 1.336-.94.165-.461.165-.857.115-.94-.05-.082-.181-.131-.379-.23z"/>
                            </svg>
                        </span>
                        <span>{{ $googleProofCtaLabel }}</span>
                    </a>
                </div>
            </section>

            <section id="problems" class="section deep">
                <h2 class="title">{{ $googleProblemsTitle }}</h2>
                <p class="subtitle">{{ $googleProblemsSubtitle }}</p>

                <div class="problems">
                    @foreach ($googleProblemsItems as $problem)
                        <article class="card problem">
                            <div class="problem-head">
                                <div class="icon-badge">{{ data_get($problem, 'index') }}</div>
                                <h3>{{ data_get($problem, 'title') }}</h3>
                            </div>
                            <p class="muted">{{ data_get($problem, 'description') }}</p>
                        </article>
                    @endforeach
                </div>

                <p class="note">{{ $googleProblemsNote }}</p>
            </section>

            <section id="steps" class="section">
                <h2 class="title">{{ $googleStepsTitle }}</h2>
                @if ($googleStepsSubtitle !== '')
                    <p class="subtitle">{{ $googleStepsSubtitle }}</p>
                @endif

                <div class="steps">
                    @foreach ($googleStepsItems as $step)
                        <article class="card step">
                            <div class="step-meta">
                                <span class="step-index">{{ data_get($step, 'index') }}</span>
                                <div class="icon-badge">✓</div>
                            </div>
                            <h3>{{ data_get($step, 'title') }}</h3>
                            <p class="step-desc">{{ data_get($step, 'description') }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="actions" style="justify-content: center; margin-top: 1rem;">
                    <a class="btn btn-primary wa-btn" href="{{ $stepsCtaHref }}">
                        <span class="icon-22" aria-hidden="true">
                            <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M13.601 2.326A7.854 7.854 0 0 0 8.165 0C3.734 0 .132 3.58.132 8c0 1.4.366 2.766 1.06 3.965L0 16l4.153-1.088a7.987 7.987 0 0 0 4.012 1.073h.003c4.43 0 8.032-3.58 8.032-8 0-2.14-.833-4.154-2.599-5.659zM8.165 14.67h-.002a6.62 6.62 0 0 1-3.372-.92l-.242-.145-2.463.645.657-2.4-.157-.248a6.586 6.586 0 0 1-1.007-3.5c0-3.647 2.977-6.614 6.633-6.614a6.59 6.59 0 0 1 4.708 1.942A6.56 6.56 0 0 1 14.8 8.105c-.001 3.647-2.977 6.614-6.635 6.614z"/>
                                <path fill="currentColor" d="M11.603 9.854c-.197-.099-1.17-.578-1.351-.643-.181-.066-.313-.099-.445.099-.131.197-.51.643-.626.775-.115.131-.23.148-.427.05-.197-.1-.833-.307-1.588-.98-.588-.525-.985-1.174-1.1-1.372-.115-.197-.012-.304.087-.402.089-.088.197-.23.296-.345.099-.115.131-.197.197-.329.066-.132.033-.247-.017-.345-.05-.099-.445-1.073-.61-1.47-.16-.387-.323-.334-.445-.34-.115-.007-.247-.008-.379-.008a.727.727 0 0 0-.526.247c-.181.197-.69.676-.69 1.65 0 .972.707 1.912.805 2.044.099.131 1.39 2.124 3.37 2.977.471.203.839.324 1.125.415.472.15.902.129 1.242.078.379-.056 1.17-.478 1.336-.94.165-.461.165-.857.115-.94-.05-.082-.181-.131-.379-.23z"/>
                            </svg>
                        </span>
                        <span>{{ $googleStepsCtaLabel }}</span>
                    </a>
                </div>
            </section>

            <section id="compare" class="section">
                <h2 class="title">{{ $googleCompareTitle }}</h2>
                <p class="subtitle">{{ $googleCompareSubtitle }}</p>

                <div class="compare">
                    <article class="card">
                        <span class="compare-head bad-head">{{ $googleCompareBeforeHeading }}</span>
                        <div class="doc">
                            @if ($beforeImage)
                                <img src="{{ $beforeImage }}" alt="قبل المراجعة" loading="lazy">
                            @endif
                        </div>
                        <ul class="list">
                            @foreach ($googleCompareBeforeItems as $beforeItem)
                                <li><span class="bad">✕</span><span>{{ $beforeItem }}</span></li>
                            @endforeach
                        </ul>
                    </article>

                    <article class="card">
                        <span class="compare-head good-head">{{ $googleCompareAfterHeading }}</span>
                        <div class="doc">
                            @if ($afterImage)
                                <img src="{{ $afterImage }}" alt="بعد المراجعة" loading="lazy">
                            @endif
                        </div>
                        <ul class="list">
                            @foreach ($googleCompareAfterItems as $afterItem)
                                <li><span class="good">✓</span><span>{{ $afterItem }}</span></li>
                            @endforeach
                        </ul>
                    </article>
                </div>
            </section>

            <section class="section final">
                <h2 class="title">{{ $googleFinalTitle }}</h2>
                <p class="subtitle">{{ $googleFinalSubtitle }}</p>

                <div class="actions" style="justify-content: center; margin-top: 1rem;">
                    <a class="btn btn-primary wa-btn" href="{{ $finalCtaHref }}">
                        <span class="icon-22" aria-hidden="true">
                            <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M13.601 2.326A7.854 7.854 0 0 0 8.165 0C3.734 0 .132 3.58.132 8c0 1.4.366 2.766 1.06 3.965L0 16l4.153-1.088a7.987 7.987 0 0 0 4.012 1.073h.003c4.43 0 8.032-3.58 8.032-8 0-2.14-.833-4.154-2.599-5.659zM8.165 14.67h-.002a6.62 6.62 0 0 1-3.372-.92l-.242-.145-2.463.645.657-2.4-.157-.248a6.586 6.586 0 0 1-1.007-3.5c0-3.647 2.977-6.614 6.633-6.614a6.59 6.59 0 0 1 4.708 1.942A6.56 6.56 0 0 1 14.8 8.105c-.001 3.647-2.977 6.614-6.635 6.614z"/>
                                <path fill="currentColor" d="M11.603 9.854c-.197-.099-1.17-.578-1.351-.643-.181-.066-.313-.099-.445.099-.131.197-.51.643-.626.775-.115.131-.23.148-.427.05-.197-.1-.833-.307-1.588-.98-.588-.525-.985-1.174-1.1-1.372-.115-.197-.012-.304.087-.402.089-.088.197-.23.296-.345.099-.115.131-.197.197-.329.066-.132.033-.247-.017-.345-.05-.099-.445-1.073-.61-1.47-.16-.387-.323-.334-.445-.34-.115-.007-.247-.008-.379-.008a.727.727 0 0 0-.526.247c-.181.197-.69.676-.69 1.65 0 .972.707 1.912.805 2.044.099.131 1.39 2.124 3.37 2.977.471.203.839.324 1.125.415.472.15.902.129 1.242.078.379-.056 1.17-.478 1.336-.94.165-.461.165-.857.115-.94-.05-.082-.181-.131-.379-.23z"/>
                            </svg>
                        </span>
                        <span>{{ $googleFinalCtaLabel }}</span>
                    </a>
                </div>
            </section>

            <footer>
                {{ $googleFooterText }}
            </footer>
        </header>
    </div>

@endsection
