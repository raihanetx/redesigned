<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submonth - Premium Digital Subscriptions</title>
    
    <meta name="description" content="Submonth is your trusted source for premium digital subscriptions and courses in Bangladesh. Get affordable access to tools like Canva Pro, ChatGPT Plus, and more.">
    <meta name="keywords" content="digital subscriptions, premium accounts, online courses, submonth, bangladesh, canva pro, chatgpt plus, affordable price">

    <?php if (!empty($favicon_path) && file_exists($favicon_path)): ?>
        <link rel="icon" type="image/png" href="<?= htmlspecialchars(BASE_PATH . '/' . $favicon_path) ?>">
        <link rel="apple-touch-icon" href="<?= htmlspecialchars(BASE_PATH . '/' . $favicon_path) ?>">
    <?php else: ?>
        <link rel="icon" type="image/png" href="https://i.postimg.cc/ncGxB1jm/IMG-20250919-WA0036.jpg">
        <link rel="apple-touch-icon" href="https://i.postimg.cc/ncGxB1jm/IMG-20250919-WA0036.jpg">
    <?php endif; ?>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=League+Spartan:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <style>
        :root {
            --primary-color: #7C3AED;
            --primary-color-darker: #6D28D9;
            --primary-color-light: #F5F3FF;
            --strong-border-color: #C4B5FD;
        }
        [v-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; }
        h1, h2, h3, h4, .font-display { font-family: 'League Spartan', 'Inter', sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .hero-section { margin: 1rem; position: relative; }
        .hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            background-size: cover;
            background-position: center;
        }
        .hero-slide.active { opacity: 1; }
        .preserve-whitespace { white-space: pre-wrap; }
        .category-icon { display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; text-decoration: none; width: 72px; height: 72px; padding: 0.5rem; flex-shrink: 0; border: 2px solid var(--strong-border-color); border-radius: 0.75rem; background-color: #ffffff; }
        .category-icon:hover { border-color: var(--primary-color); }
        .category-icon i { font-size: 1.75rem; color: var(--primary-color); }
        .category-icon span { font-size: 0.7rem; color: #374151; font-weight: 500; text-align: center; line-height: 1.1; margin-top: 0.25rem; }
        .category-scroll-container { display: flex; flex-wrap: nowrap; gap: 1rem; width: max-content; padding: 0 1rem; }
        .horizontal-scroll { overflow-x: auto; scrollbar-width: none; }
        .horizontal-scroll::-webkit-scrollbar { display: none; }
        .smooth-scroll { scroll-behavior: smooth; }
        @media (min-width: 768px) { .category-scroll-container { gap: 2rem; padding: 0; } div[ref="categoryScroller"] { padding: 0; } .category-icon { width: 90px; height: 90px; } .category-icon i { font-size: 2.5rem; margin-bottom: -0.25rem; } .category-icon span { font-size: 0.875rem; margin-top: 0.75rem; } }
        .product-card { transition: all 0.2s ease; border-width: 2px; border-color: #e5e7eb; box-shadow: none; }
        .product-card:hover { border-color: #d1d5db; }
        .product-card:active { transform: scale(0.98); filter: brightness(0.98); }
        .product-card { width: 170px; display: flex; flex-direction: column; flex-shrink: 0; border-radius: 0.75rem; overflow: hidden; position: relative; scroll-snap-align: start; cursor: pointer; background-color: white; }
        .product-scroll-container { display: flex; width: max-content; padding-left: 10px; padding-right: 10px; gap: 10px; }
        .product-card-image-container { aspect-ratio: 4 / 3; background-color: #f3f4f6; overflow: hidden; }
        .product-image { width: 100%; height: 100%; object-fit: cover; }
        .line-clamp-1 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; }
        .line-clamp-2 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .hot-deals-container { overflow: hidden; -webkit-mask-image: linear-gradient(to right, transparent 0%, white 10%, white 90%, transparent 100%); mask-image: linear-gradient(to right, transparent 0%, white 10%, white 90%, transparent 100%); }
        .hot-deals-scroller { display: flex; width: max-content; animation-name: scroll-anim; animation-timing-function: linear; animation-iteration-count: infinite; }
        .hot-deal-card { width: 100px; margin: 0 8px; flex-shrink: 0; text-align: center; text-decoration: none; color: inherit; }
        .hot-deal-image-container { aspect-ratio: 4 / 3; border-radius: 0.75rem; overflow: hidden; margin-bottom: 0.5rem; border: 2px solid #e5e7eb; }
        .hot-deal-image { width: 100%; height: 100%; object-fit: cover; }
        .hot-deal-title { font-size: 0.75rem; font-weight: 500; color: #374151; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        @keyframes scroll-anim { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        @media (min-width: 768px) { .hero-section { aspect-ratio: 5 / 2; } .product-card { width: 280px; } .product-scroll-container { padding-left: 30px; padding-right: 30px; gap: 30px; } .hot-deal-card { width: 180px; margin: 0 14px; } .hot-deal-title { font-size: 0.875rem; } .hot-deals-container { -webkit-mask-image: linear-gradient(to right, transparent, #f9fafb 8%, #f9fafb 92%, transparent); mask-image: linear-gradient(to right, transparent, #f9fafb 8%, #f9fafb 92%, transparent); } }
        @media (max-width: 767px) { html { font-size: 80%; } .hero-section { aspect-ratio: 2 / 1; } #related-products-container > div:nth-of-type(n+3) { display: none; } }
        .feature-card { transition: all 0.3s ease; }
        .feature-card:hover { box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); }
        .product-grid-card { display: flex; flex-direction: column; background-color: white; border-radius: 0.75rem; border-width: 2px; border-color: #e5e7eb; overflow: hidden; transition: all 0.3s ease; position: relative; cursor: pointer; box-shadow: none; }
        .product-grid-card:hover { border-color: #d1d5db; }
        .notification-badge { position: absolute; top: -2px; right: -4px; background-color: #ef4444; color: white; border-radius: 50%; width: 12px; height: 12px; display: flex; align-items: center; justify-content: center; font-size: 6px; font-weight: bold; line-height: 1; }
        .product-detail-title { font-size: 1.75rem; max-width: 25ch; }
        @media (min-width: 768px) { .product-detail-title { font-size: 2rem; } .product-detail-content { display: flex; flex-direction: row; align-items: flex-start; gap: 2rem;} .product-detail-image-container { flex-shrink: 0; position: relative; width: 50%; } .product-detail-info-container { flex-grow: 1; } .duration-button-selected::after { content: '✓'; position: absolute; top: -8px; right: -8px; font-size: 1rem; color: white; background-color: var(--primary-color); border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2); } }
        @media (max-width: 767px) { .product-detail-image-container { aspect-ratio: 1 / 1; } .duration-button-selected::after { content: '✓'; position: absolute; top: -8px; right: -8px; font-size: 0.8rem; color: white; background-color: var(--primary-color); border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2); } }
        .fab-icon { transition: transform 0.3s ease; }
    </style>
</head>