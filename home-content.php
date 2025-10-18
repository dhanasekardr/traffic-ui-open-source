<?php 
    // Get server IP and location info (keeping existing PHP logic)
    $server_ip = file_get_contents('https://ipinfo.io/ip');
    $server_location = 'N/A';
    $location_details = file_get_contents("https://ipinfo.io/{$server_ip}/json");
    $location_data = json_decode($location_details, true);
    if ($location_data) {
        $server_location = $location_data['country'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Dashboard</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            /* Sophisticated Red-Gold Palette */
            --primary: #e63946;
            --primary-dark: #c1121f;
            --secondary: #fca311;
            --secondary-light: #ffd60a;
            --accent: #ff6b6b;
            --accent-2: #ee9b00;
            
            /* Backgrounds */
            --bg-dark: #0a0e27;
            --bg-darker: #070b1f;
            --bg-card: rgba(255, 255, 255, 0.03);
            --bg-glass: rgba(255, 255, 255, 0.05);
            
            /* Text */
            --text-primary: #ffffff;
            --text-secondary: #b8c1ec;
            --text-dim: #8892b3;
            
            /* Status */
            --success: #06d6a0;
            --warning: #ffd60a;
            --danger: #ef476f;
            --info: #4cc9f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Modern Animations */
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes shimmerFlow {
            0% {
                transform: translateX(-100%) rotate(45deg);
            }
            100% {
                transform: translateX(300%) rotate(45deg);
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 20px var(--primary), 0 0 40px rgba(230, 57, 70, 0.3);
            }
            50% {
                box-shadow: 0 0 30px var(--primary), 0 0 60px rgba(230, 57, 70, 0.5);
            }
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes rotateGlow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-darker);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(230, 57, 70, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(252, 163, 17, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(255, 107, 107, 0.1) 0%, transparent 50%);
            animation: gradientShift 20s ease infinite;
            z-index: 0;
        }

        /* Grid Pattern */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(230, 57, 70, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(230, 57, 70, 0.05) 1px, transparent 1px);
            background-size: 100px 100px;
            z-index: 0;
            pointer-events: none;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
                max-width: 100%;
            }
        }

        /* Header Section */
        .header {
            text-align: center;
            padding: 3rem 0 2rem;
            animation: fadeSlideUp 0.8s ease;
        }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, rgba(230, 57, 70, 0.15), rgba(252, 163, 17, 0.15));
            border: 2px solid rgba(230, 57, 70, 0.3);
            border-radius: 50px;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            animation: pulseGlow 3s ease-in-out infinite;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .main-title {
            font-size: 4rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary), var(--accent));
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            animation: gradientShift 8s ease infinite;
            letter-spacing: -2px;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 1.125rem;
            font-weight: 400;
            margin-bottom: 2rem;
        }

        /* Search Section */
        .search-section {
            max-width: 600px;
            margin: 0 auto 3rem;
            animation: fadeSlideUp 1s ease 0.2s backwards;
        }

        .search-card {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(230, 57, 70, 0.2);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .search-input {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(230, 57, 70, 0.2);
            border-radius: 12px;
            color: var(--text-primary);
            padding: 1rem 1.25rem;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.1);
        }

        .search-input::placeholder {
            color: var(--text-dim);
        }

        .btn-search {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            margin-top: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-search::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-search:hover::before {
            left: 100%;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(230, 57, 70, 0.4);
        }

        .btn-search:active {
            transform: translateY(0);
        }

        /* Stats Grid - Fixed Layout */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
            width: 100%;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.25rem;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        /* Glass Card */
        .glass-card {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(230, 57, 70, 0.15);
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: fadeSlideUp 0.6s ease backwards;
            min-height: 280px;
            display: flex;
            flex-direction: column;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .glass-card:hover::before {
            transform: scaleX(1);
        }

        .glass-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 20px 50px rgba(230, 57, 70, 0.2);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(230, 57, 70, 0.1);
        }

        .card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, rgba(230, 57, 70, 0.2), rgba(252, 163, 17, 0.2));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary);
            flex-shrink: 0;
        }

        .card-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-dim);
            font-size: 0.9rem;
        }

        /* Progress Circle */
        .progress-circle-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .progress-circle {
            position: relative;
            width: 200px;
            height: 200px;
            animation: float 6s ease-in-out infinite;
        }

        .progress-circle svg {
            transform: rotate(-90deg);
            filter: drop-shadow(0 0 20px rgba(230, 57, 70, 0.5));
        }

        .progress-circle circle {
            fill: none;
            stroke-width: 10;
            stroke-linecap: round;
        }

        .progress-bg {
            stroke: rgba(255, 255, 255, 0.1);
        }

        .progress-bar-circle {
            stroke: url(#redGoldGradient);
            transition: stroke-dashoffset 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .progress-percent {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }

        .progress-label {
            color: var(--text-dim);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        /* Modern Progress Bar */
        .modern-progress {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50px;
            height: 14px;
            overflow: hidden;
            margin-top: 1rem;
            position: relative;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .modern-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
            background-size: 200% 100%;
            border-radius: 50px;
            position: relative;
            transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            box-shadow: 0 0 20px rgba(230, 57, 70, 0.5);
        }

        .modern-progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
            animation: shimmerFlow 2.5s infinite;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-badge.active {
            background: rgba(6, 214, 160, 0.15);
            color: var(--success);
            border: 1px solid rgba(6, 214, 160, 0.3);
        }

        .status-badge.inactive {
            background: rgba(239, 71, 111, 0.15);
            color: var(--danger);
            border: 1px solid rgba(239, 71, 111, 0.3);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            animation: pulseGlow 2s ease-in-out infinite;
        }

        /* Info Grid - Flexbox Approach */
        .info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            flex: 1;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            border: 1px solid rgba(230, 57, 70, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 70px;
            flex: 1 1 calc(50% - 0.5rem);
            box-sizing: border-box;
            min-width: 0;
        }

        .info-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(230, 57, 70, 0.15);
        }

        .info-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, rgba(230, 57, 70, 0.15), rgba(252, 163, 17, 0.15));
            border: 1px solid rgba(230, 57, 70, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1rem;
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 0.25rem;
            overflow: visible;
        }

        .info-label {
            font-size: 0.7rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin: 0;
            line-height: 1;
        }

        .info-value {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-primary);
            overflow: visible;
            text-overflow: unset;
            white-space: normal;
            line-height: 1.2;
            margin: 0;
            word-break: break-word;
        }

        .info-value[style*="font-family"] {
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        /* Metric Bars */
        .metric-item {
            margin-bottom: 1.75rem;
        }

        .metric-item:last-child {
            margin-bottom: 0;
        }

        .metric-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
                margin-bottom: 0.75rem;
            }

        .metric-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-value {
            font-size: 0.9rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Error Message */
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            color: #fca5a5;
            padding: 1rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1), 0 2px 4px -1px rgba(239, 68, 68, 0.06);
            margin-bottom: 2rem;
            text-align: center;
            animation: fadeSlideUp 0.5s ease-out;
        }


        /* Client Info Section - Compact */
        .client-info-section {
            max-width: 400px;
            margin: 0 auto 1.5rem;
            animation: fadeSlideUp 1s ease 0.6s backwards;
        }

        .client-info-card {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(230, 57, 70, 0.2);
            border-radius: 16px;
            padding: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .client-info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(230, 57, 70, 0.2);
        }

        .client-info-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(230, 57, 70, 0.1);
        }

        .client-info-header i {
            font-size: 1.25rem;
            color: var(--primary);
        }

        .client-info-header h3 {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }

        .client-name-display {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
        }

        .client-name {
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
        }

        .client-status {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(239, 71, 111, 0.15);
            color: var(--danger);
            border: 1px solid rgba(239, 71, 111, 0.3);
            flex-shrink: 0;
        }

        .client-status.online {
            background: rgba(6, 214, 160, 0.15);
            color: var(--success);
            border: 1px solid rgba(6, 214, 160, 0.3);
        }

        .client-status::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            animation: pulseGlow 2s ease-in-out infinite;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 3rem 0 2rem;
            color: var(--text-dim);
        }

        .footer a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: var(--secondary);
        }

        /* Animation delays */
        .stats-grid > div:nth-child(1) { animation-delay: 0.1s; }
        .stats-grid > div:nth-child(2) { animation-delay: 0.2s; }
        .stats-grid > div:nth-child(3) { animation-delay: 0.3s; }
        .stats-grid > div:nth-child(4) { animation-delay: 0.4s; }
        .stats-grid > div:nth-child(5) { animation-delay: 0.5s; }
        .stats-grid > div:nth-child(6) { animation-delay: 0.6s; }

        /* Responsive */
        @media (max-width: 768px) {
            .main-title {
                font-size: 2.5rem;
                letter-spacing: -1px;
            }
            
            .subtitle {
                font-size: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .info-grid {
                flex-direction: column;
            }
            
            .info-item {
                flex: 1 1 auto;
                width: 100%;
            }

            .search-section {
                margin-bottom: 2rem;
            }

            .search-card {
                padding: 1.5rem;
            }

            .glass-card {
                padding: 1.5rem;
            }

            .progress-circle {
                width: 160px;
                height: 160px;
            }

            .stat-value {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .main-title {
                font-size: 2rem;
            }

            .logo-badge {
                padding: 0.5rem 1.25rem;
                font-size: 0.75rem;
            }

            .logo-icon {
                width: 28px;
                height: 28px;
                font-size: 1rem;
            }

            .progress-circle {
                width: 140px;
                height: 140px;
            }

            .stat-value {
                font-size: 1.75rem;
            }

            .card-header {
                gap: 0.75rem;
            }

            .card-icon {
                width: 40px;
                height: 40px;
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- SVG Gradients -->
        <svg width="0" height="0">
            <defs>
                <linearGradient id="redGoldGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color: #e63946;" />
                    <stop offset="100%" style="stop-color: #fca311;" />
                </linearGradient>
            </defs>
        </svg>

        <!-- Header -->
        <header class="header">
            <div class="logo-badge">
                <div class="logo-icon">
                    <i class="fas fa-bolt"></i>
            </div>
                <span style="font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 1px;">Premium Network</span>
            </div>
            <h1 class="main-title">TRAFFIC-UI</h1>
            <p class="subtitle">Real-time VPN monitoring dashboard</p>
        </header>

        <!-- Search Section -->
        <div class="search-section">
            <div class="search-card">
                <?php echo form_open('/', 'id="get_usage" class="search-form"'); ?>
                    <input type="text" class="search-input" placeholder="Enter username or UUID" id="email" name="email" required>
                        <input type="hidden" name="hid-email">
                    <button type="submit" class="btn-search" id="usage_button">
                        <i class="fas fa-search me-2"></i> Check Usage
                    </button>
                <?php echo form_close(); ?>
            </div>
        </div>

        <!-- Error Message -->
        <div id="error" class="error-message d-none" role="alert"></div>

        <!-- Client Name Display -->
        <div class="client-info-section">
            <div class="client-info-card">
                <div class="client-info-header">
                    <i class="fas fa-user-circle"></i>
                    <h3>Current Client</h3>
                    </div>
                <div class="client-name-display" id="client-name-display">
                    <span class="client-name" id="client-name">No client selected</span>
                    <span class="client-status" id="client-status">Offline</span>
                    </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <!-- Data Usage -->
            <div class="glass-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="card-title">Data Usage</h3>
                </div>
                <div class="stat-value" id="data-usage">0 GB / 0 GB</div>
                <div class="stat-label">of 100 GB used</div>
                <div class="modern-progress">
                    <div class="modern-progress-bar data-progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <!-- Usage Progress -->
            <div class="glass-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-circle-notch"></i>
                            </div>
                    <h3 class="card-title">Usage Progress</h3>
                </div>
                <div class="progress-circle-container">
                    <div class="progress-circle">
                        <svg viewBox="0 0 100 100" width="200" height="200">
                            <circle class="progress-bg" cx="50" cy="50" r="40"/>
                            <circle class="progress-bar-circle usage-progress-bar" cx="50" cy="50" r="40"
                                    stroke-dasharray="251.2"
                                    stroke-dashoffset="251.2"/>
                        </svg>
                        <div class="progress-text">
                            <div class="progress-percent" id="usage-percentage">0%</div>
                            <div class="progress-label">Used</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Server Health -->
            <div class="glass-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-server"></i>
                            </div>
                    <h3 class="card-title">Server Health</h3>
                            </div>
                <div class="metric-item">
                    <div class="metric-header">
                        <span class="metric-label">CPU Load</span>
                        <span class="metric-value cpu_usage">N/A</span>
                        </div>
                    <div class="modern-progress">
                        <div class="modern-progress-bar cpu-progress-bar" style="width: 0%"></div>
                            </div>
                            </div>
                <div class="metric-item">
                    <div class="metric-header">
                        <span class="metric-label">Memory</span>
                        <span class="metric-value ram_usage">N/A</span>
                        </div>
                    <div class="modern-progress">
                        <div class="modern-progress-bar ram-progress-bar" style="width: 0%"></div>
                            </div>
                            </div>
                <div class="metric-item">
                    <div class="metric-header">
                        <span class="metric-label">Storage</span>
                        <span class="metric-value disk_usage">N/A</span>
                        </div>
                    <div class="modern-progress">
                        <div class="modern-progress-bar disk-progress-bar" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <!-- Server Info -->
            <div class="glass-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="card-title">Server Info</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">UPTIME</div>
                            <div class="info-value uptime">N/A</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">LOCATION</div>
                            <div class="info-value"><?= $server_location ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">IP ADDRESS</div>
                            <div class="info-value" style="font-family: 'Courier New', monospace;"><?= $server_ip ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-signal"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">STATUS</div>
                            <div class="info-value server_status" style="color: var(--success);">Online</div>
                        </div>
                    </div>
                </div>
            </div>

                        <!-- Account Status -->
            <div class="glass-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-user-shield"></i>
                                    </div>
                    <h3 class="card-title">Account Status</h3>
                                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-toggle-on" id="enable-toggle"></i>
                            </div>
                        <div class="info-content">
                            <div class="info-label">Status</div>
                            <div class="status-badge inactive" id="status-badge">
                                <span class="status-dot"></span>
                                <span id="enable">Disabled</span>
                        </div>
                                    </div>
                                </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-calendar"></i>
                            </div>
                        <div class="info-content">
                            <div class="info-label">Days Left</div>
                            <div class="info-value" id="remaining-time">0 days</div>
                        </div>
                                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Data Limit</div>
                            <div class="info-value" id="data-limit">0 GB</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-clock-rotate-left"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Last Update</div>
                            <div class="info-value" id="last-update">Just now</div>
                        </div>
                                </div>
                            </div>
                        </div>

            <!-- Connection Speed -->
            <div class="glass-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-gauge-high"></i>
                                    </div>
                    <h3 class="card-title">Connection</h3>
                                </div>
                <div class="metric-item">
                    <div class="metric-header">
                        <span class="metric-label">Download</span>
                        <span class="metric-value" id="download-data">0 GB</span>
                            </div>
                    <div class="modern-progress">
                        <div class="modern-progress-bar" style="width: 0%" id="download-progress"></div>
                        </div>
                    </div>
                <div class="metric-item">
                    <div class="metric-header">
                        <span class="metric-label">Upload</span>
                        <span class="metric-value" id="upload-data">0 GB</span>
                    </div>
                    <div class="modern-progress">
                        <div class="modern-progress-bar" style="width: 0%" id="upload-progress"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>
                <a href="https://t.me/traffic_ui" target="_blank">Traffic-UI</a> v2.0.0 
                &mdash; Made with <i class="fas fa-heart" style="color: var(--danger);"></i> by 
                <strong style="color: var(--primary);">mAX web™</strong>
            </p>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let fetchInterval;

        $(document).ready(function() {
            $('#get_usage').on('submit', function(e) {
            e.preventDefault();
                
                var formData = new FormData(this);
                $('#usage_button').html('<i class="fas fa-spinner fa-spin"></i> Checking...');
                $('#usage_button').attr('disabled', true);
            $('#error').addClass('d-none');

            $.ajax({
                type: 'POST',
                url: '/getusage?_t=' + Date.now(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                    success: function(response) {
                    $("input[name='csrf_test_name']").val(response.token);
                        if (response.success) {
                        // Update the UI with the initial data from the first request
                        updateUsageData(response);

                            // Start a continuous fetch every 2 seconds
                        fetchInterval = setInterval(function() {
                                var newFormData = new FormData();
                                newFormData.append('csrf_test_name', $("input[name='csrf_test_name']").val());
                            newFormData.append('email', $("input[name='email']").val());

                            $.ajax({
                                type: 'POST',
                                url: '/getusage?_t=' + Date.now(),
                                data: newFormData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(response) {
                                    $("input[name='csrf_test_name']").val(response.token);
                                    if (response.success) {
                                        updateUsageData(response);
                                    } else {
                                        clearInterval(fetchInterval);
                                        $("input[name='hid-email']").val('');
                                        $('#error').html(response.error_msg);
                                        $('#error').removeClass('d-none');
                                        resetUI();
                                        }
                                    },
                                    timeout: 10000,
                                error: function(xhr, status, error) {
                                    if (status === "timeout") {
                                        $('#error').html('Request timed out. Please try again.');
                                    }
                                    clearInterval(fetchInterval);
                                    $("input[name='hid-email']").val('');
                                    $('#usage_button').html('<i class="fas fa-search"></i> Check Usage');
                                    $('#usage_button').removeAttr('disabled');
                                    $('#error').html('Please reload the page and try again!');
                                    $('#error').removeClass('d-none');
                                        resetUI();
                                    }
                                });
                            }, 2000);
                        } else {
                        $("input[name='hid-email']").val('');
                        $('#error').html(response.error_msg);
                        $('#error').removeClass('d-none');
                            resetUI();
                        }
                        $('#usage_button').html('<i class="fas fa-search"></i> Check Usage');
                        $('#usage_button').removeAttr('disabled');
                    },
                    timeout: 10000,
                error: function(xhr, status, error) {
                    if (status === "timeout") {
                        $('#error').html('Request timed out. Please try again.');
                    }
                    $('#usage_button').html('<i class="fas fa-search"></i> Check Usage');
                    $('#usage_button').removeAttr('disabled');
                    $('#error').html('Please reload the page and try again!');
                    $('#error').removeClass('d-none');
                        resetUI();
                    }
                });
            });
        });

        function updateUsageData(response) {
            // Debug: Log the response to see what enable value we're getting
            console.log('API Response:', response);
            console.log('Enable value:', response.enable);
            
            // Update the UI elements with new data from the response
            if (response.uuid == '') {
                $('#client-name').html(response.email);
            } else {
                $('#client-name').html(response.uuid);
            }

            $("input[name='hid-email']").val(response.email);
            $('#data-usage').html(response.total_up_down + ' / ' + response.total);
            $('#data-limit').html(response.total);
            $('#usage-percentage').html(response.percentage + '%');
            $('.data-progress-bar').css('width', response.percentage + '%');

            $('.cpu-progress-bar').css('width', response.cpu_usage + '%');
            $('.ram-progress-bar').css('width', response.ram_usage_percent + '%');
            $('.disk-progress-bar').css('width', response.disk_space.split('%')[0] + '%');

            // Update progress circle
            let percentage = response.percentage || 0;
            let offset = 251.2 - (percentage * 251.2 / 100);
            document.querySelector('.progress-circle .usage-progress-bar').style.strokeDashoffset = offset;

            // Update status
            console.log('Updating status with enable value:', response.enable);
            if (response.enable == 1) {
                console.log('Setting status to ACTIVE/ONLINE');
                $('#status-badge').removeClass('inactive').addClass('active');
                $('#enable').html('Active');
                $('#enable-toggle').removeClass('fa-toggle-off').addClass('fa-toggle-on');
                $('#client-status').removeClass('offline').addClass('online').html('Online');
                console.log('Status badge classes:', $('#status-badge').attr('class'));
                console.log('Client status classes:', $('#client-status').attr('class'));
            } else {
                console.log('Setting status to DISABLED/OFFLINE');
                $('#status-badge').removeClass('active').addClass('inactive');
                $('#enable').html('Disabled');
                $('#enable-toggle').removeClass('fa-toggle-on').addClass('fa-toggle-off');
                $('#client-status').removeClass('online').addClass('offline').html('Offline');
                console.log('Status badge classes:', $('#status-badge').attr('class'));
                console.log('Client status classes:', $('#client-status').attr('class'));
            }

            // Ensure both status indicators are in sync
            setTimeout(syncStatusIndicators, 100);

            $('#remaining-time').html(response.remaining_days + ' days');

            // Server info
            $('.server_status').html(response.server_status);
            $('.cpu_usage').html(response.cpu_usage + '%');
            $('.ram_usage').html(response.ram_usage);
            $('.disk_usage').html(response.disk_space);
            let uptime = response.uptime ? response.uptime : 'N/A';
            $('.uptime').html(uptime);
            
            // Connection data
            $('#download-data').html(response.download_data || '0 GB');
            $('#upload-data').html(response.upload_data || '0 GB');
            
            // Calculate progress bars for upload and download
            let totalBytes = response.total_bytes || 0;
            if (totalBytes > 0) {
                let downloadPercent = (response.download_bytes / totalBytes) * 100;
                let uploadPercent = (response.upload_bytes / totalBytes) * 100;
                
                $('#download-progress').css('width', Math.min(downloadPercent, 100) + '%');
                $('#upload-progress').css('width', Math.min(uploadPercent, 100) + '%');
            } else {
                $('#download-progress').css('width', '0%');
                $('#upload-progress').css('width', '0%');
            }
        }

        // Function to ensure both status indicators are in sync
        function syncStatusIndicators() {
            const statusBadge = $('#status-badge');
            const clientStatus = $('#client-status');
            
            console.log('Syncing status indicators...');
            console.log('Status badge classes:', statusBadge.attr('class'));
            console.log('Client status classes:', clientStatus.attr('class'));
            
            // Check if status badge is active
            if (statusBadge.hasClass('active')) {
                console.log('Status badge is active, ensuring client status is online');
                clientStatus.removeClass('offline').addClass('online').html('Online');
            } else {
                console.log('Status badge is inactive, ensuring client status is offline');
                clientStatus.removeClass('online').addClass('offline').html('Offline');
            }
        }

        function resetUI() {
            $('#data-usage').html('0 GB / 0 GB');
            $('#data-limit').html('0 GB');
            $('#usage-percentage').html('0%');
            $('.data-progress-bar').css('width', '0%');
            $('.cpu-progress-bar').css('width', '0%');
            $('.ram-progress-bar').css('width', '0%');
            $('.disk-progress-bar').css('width', '0%');

            let percentage = 0;
            let offset = 251.2 - (percentage * 251.2 / 100);
            document.querySelector('.progress-circle .usage-progress-bar').style.strokeDashoffset = offset;

            $('#status-badge').removeClass('active').addClass('inactive');
            $('#enable').html('Disabled');
            $('#enable-toggle').removeClass('fa-toggle-on').addClass('fa-toggle-off');
            $('#remaining-time').html('0 days');
            
            // Reset connection data
            $('#download-data').html('0 GB');
            $('#upload-data').html('0 GB');
            $('#download-progress').css('width', '0%');
            $('#upload-progress').css('width', '0%');
            
            // Reset client display
            $('#client-name').html('No client selected');
            $('#client-status').removeClass('online').addClass('offline').html('Offline');
        }

        // Prevent right click
        document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            return false;
        });

        // Prevent F12, Ctrl+Shift+I, Ctrl+U, Ctrl+S
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.key === 'u') ||
                (e.ctrlKey && e.key === 's')) {
                e.preventDefault();
                return false;
            }

            // Allow copy/paste in input fields
            if ((e.ctrlKey || e.metaKey) && 
                (e.key === 'c' || e.key === 'C' || e.key === 'v' || e.key === 'V')) {
                if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                }
            }
        });

        // Make all elements except inputs non-draggable
        document.querySelectorAll('*:not(input):not(textarea)').forEach(element => {
            element.setAttribute('draggable', 'false');
        });

        // Improved dev tools detection
        (function() {
            function detectDevTools() {
                const threshold = 160;
                const widthThreshold = Math.abs(window.outerWidth - window.innerWidth) > threshold;
                const heightThreshold = Math.abs(window.outerHeight - window.innerHeight) > threshold;
                
                if(widthThreshold || heightThreshold) {
                    document.body.innerHTML = '<h1 style="text-align: center; padding: 50px;">Access Denied</h1>';
                }
            }

            // Check for dev tools
            setInterval(detectDevTools, 1000);

            // Protect console
            let devtools = {
                isOpen: false,
                orientation: undefined
            };

            const threshold = 160;
            const emitEvent = (isOpen, orientation) => {
                window.dispatchEvent(new CustomEvent('devtoolschange', {
                    detail: {
                        isOpen,
                        orientation
                    }
                }));
            };

            setInterval(() => {
                const widthThreshold = window.outerWidth - window.innerWidth > threshold;
                const heightThreshold = window.outerHeight - window.innerHeight > threshold;
                const orientation = widthThreshold ? 'vertical' : 'horizontal';

                if (
                    !(heightThreshold && widthThreshold) &&
                    ((window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized) || widthThreshold || heightThreshold)
                ) {
                    if (!devtools.isOpen || devtools.orientation !== orientation) {
                        emitEvent(true, orientation);
                    }
                    devtools.isOpen = true;
                    devtools.orientation = orientation;
                } else {
                    if (devtools.isOpen) {
                        emitEvent(false, undefined);
                    }
                    devtools.isOpen = false;
                    devtools.orientation = undefined;
                }
            }, 500);

            // Listen for dev tools change
            window.addEventListener('devtoolschange', function(e) {
                if(e.detail.isOpen) {
                    document.body.innerHTML = '<h1 style="text-align: center; padding: 50px;">Access Denied</h1>';
                }
            });

            // Additional protection
            const _console = { ...console };
            const methods = ['log', 'debug', 'info', 'warn', 'error', 'table', 'trace'];
            
            methods.forEach(function(method) {
                console[method] = function() {
                    if((new Error()).stack.includes('debugger')) {
                        document.body.innerHTML = '<h1 style="text-align: center; padding: 50px;">Access Denied</h1>';
                    }
                    return _console[method].apply(this, arguments);
                };
            });
        })();
    </script>
</body>
</html>
