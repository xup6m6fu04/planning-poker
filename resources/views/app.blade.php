<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>Planning Poker - 敏捷開發估點工具</title>
    <meta name="description" content="專業的敏捷開發團隊估點工具，支援多人實時協作、Fibonacci 數列標準卡牌，讓您的團隊估點更有效率！">
    <meta name="keywords" content="Planning Poker, 敏捷開發, Agile, 估點工具, Scrum, 團隊協作, Story Points">
    <meta name="author" content="Right Chou">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Planning Poker - 敏捷開發估點工具">
    <meta property="og:description" content="專業的敏捷開發團隊估點工具，支援多人實時協作、Fibonacci 數列標準卡牌，讓您的團隊估點更有效率！">
    <meta property="og:image" content="{{ url('/images/planning-poker-preview.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Planning Poker">
    <meta property="og:locale" content="zh_TW">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="Planning Poker - 敏捷開發估點工具">
    <meta property="twitter:description" content="專業的敏捷開發團隊估點工具，支援多人實時協作、Fibonacci 數列標準卡牌，讓您的團隊估點更有效率！">
    <meta property="twitter:image" content="{{ url('/images/planning-poker-preview.png') }}">
    <meta property="twitter:creator" content="@right_chou">
    
    <!-- Additional Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#1f2937">
    <link rel="canonical" href="{{ url('/') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/images/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/images/apple-touch-icon.png') }}">
    
    <!-- Font Preloading -->
    <link rel="preload" href="{{ Vite::asset('resources/fonts/LXGWWenKaiTC-Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/LXGWWenKaiTC-Medium.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/LXGWWenKaiMonoTC-Regular.woff2') }}" as="font" type="font/woff2" crossorigin>

    @vite('resources/js/app.js')
    @inertiaHead
</head>
<body>
@inertia
</body>
</html>