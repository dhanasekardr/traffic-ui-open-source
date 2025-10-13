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
    
    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #8b5cf6;
            --primary-dark: #6d28d9;
            --secondary: #60a5fa;
            --accent: #f472b6;
            --background: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.5);
            --text: #e2e8f0;
            --text-dim: #94a3b8;
            --success: #22c55e;
            --warning: #eab308;
            --danger: #ef4444;
            --progress-bg: rgba(255, 255, 255, 0.1);
            --progress-bar-color: var(--primary);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(139, 92, 246, 0.3); }
            50% { box-shadow: 0 0 30px rgba(139, 92, 246, 0.5); }
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--background) 0%, #1e1b4b 50%, var(--background) 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: var(--text);
            min-height: 100vh;
        }

        .dashboard-header {
            text-align: center;
        }

        .dashboard-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease-out backwards;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 0 30px rgba(139, 92, 246, 0.2);
        }

        .server-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: rgba(139, 92, 246, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 9999px;
            color: var(--primary);
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            animation: float 3s ease-in-out infinite;
        }

        .server-title {
            font-family: 'Outfit', sans-serif;
            font-size: 3.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, 
                var(--primary), 
                var(--secondary),
                var(--accent),
                var(--primary));
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
            animation: gradientBG 8s ease infinite;
        }

        .username-display {
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--text);
            padding: 0.75rem 2rem;
            background: rgba(139, 92, 246, 0.1);
            border-radius: 12px;
            display: inline-block;
            transition: all 0.3s ease;
            margin-bottom: 3rem;
            animation: fadeInUp 0.7s ease-out;
        }

        .username-display:empty {
            padding: 0;
            background: transparent;
        }


        .search-form {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            animation: fadeInUp 0.5s ease-out;
            border: 1px solid rgba(139, 92, 246, 0.2);
        }

        .search-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 8px;
            color: var(--text);
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2);
            outline: none;
        }

        .btn-primary {
            background: var(--primary) !important;
            border-color: var(--primary-dark) !important;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.4);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0.5rem 0;
        }

        #usage-percentage::after {
            content: attr(data-end) ;
            font-size: 1rem;
            font-weight: 400;
        }

        .progress {
            background: rgba(255, 255, 255, 0.1) !important;
            border-radius: 999px !important;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary), var(--secondary)) !important;
            transition: width 0.5s ease !important;
        }

        .progress-circle {
            position: relative;
            width: 170px;
            height: 170px;
            margin: 1rem auto;
/*            animation: float 4s ease-in-out infinite;*/
        }

        .progress-circle svg {
            transform: rotate(-90deg);
        }

        .progress-circle circle {
            fill: none;
            stroke-width: 8;
            stroke-linecap: round;
        }

        .progress-bg {
            stroke: rgba(255, 255, 255, 0.1);
        }

        .usage-progress-bar {
            stroke: url(#gradient);
            transition: stroke-dashoffset 0.5s ease !important;
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
            animation: pulse 2s infinite;
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .text-primary {
            color: var(--primary) !important;
        }

        .text-success {
            color: var(--success) !important;
        }

        .text-danger {
            color: var(--danger) !important;
        }

        .text-warning {
            color: var(--warning) !important;
        }

        /* Particles animation */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: var(--primary);
            border-radius: 50%;
            animation: float 3s infinite;
            opacity: 0.3;
        }

        /* Add this to the HTML just after the body tag opens */
        <div class="particles">
            <div class="particle" style="top: 20%; left: 10%;"></div>
            <div class="particle" style="top: 60%; left: 20%;"></div>
            <div class="particle" style="top: 40%; left: 30%;"></div>
            <div class="particle" style="top: 80%; left: 40%;"></div>
            <div class="particle" style="top: 30%; left: 50%;"></div>
            <div class="particle" style="top: 70%; left: 60%;"></div>
            <div class="particle" style="top: 50%; left: 70%;"></div>
            <div class="particle" style="top: 90%; left: 80%;"></div>
            <div class="particle" style="top: 10%; left: 90%;"></div>
        </div>

        /* Add this SVG gradient definition just after the body tag opens */
        <svg width="0" height="0">
            <defs>
                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color: var(--primary);" />
                    <stop offset="100%" style="stop-color: var(--secondary);" />
                </linearGradient>
            </defs>
        </svg>

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 2rem 0 0;
                margin-bottom: 2rem;
            }
            
            .server-title {
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }

            .server-badge {
                padding: 0.4rem 1.2rem;
                font-size: 0.8rem;
                margin-bottom: 0.75rem;
            }

            .username-display {
                font-size: 1.25rem;
                padding: 0.5rem 1.5rem;
                margin-bottom: 1.5rem;
            }

            .dashboard-card {
                margin-bottom: 1rem;
            }

            .progress-circle {
                margin: 1rem auto 0;
            }
            
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        /* Loading Animation */
        .loading-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 375px) {
            .stat-value {
                font-size: 1.25rem;
            }
            .dashboard-card {
                padding: 1rem;
            }
        }

        @supports not (backdrop-filter: blur(10px)) {
            .dashboard-card {
                background: rgba(30, 41, 59, 0.95);
            }
        }

        /* Prevent text selection except for input fields */
        body *:not(input):not(textarea) {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Prevent drag and drop */
        [draggable="false"] {
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
        }
    </style>
</head>
<body>
    <div class="container py-4" style="overflow: hidden">
        <!-- Background Elements -->
        <div class="particles">
            <div class="particle" style="top: 20%; left: 10%;"></div>
            <div class="particle" style="top: 60%; left: 20%;"></div>
            <div class="particle" style="top: 40%; left: 30%;"></div>
            <div class="particle" style="top: 80%; left: 40%;"></div>
            <div class="particle" style="top: 30%; left: 50%;"></div>
            <div class="particle" style="top: 70%; left: 60%;"></div>
            <div class="particle" style="top: 50%; left: 70%;"></div>
            <div class="particle" style="top: 90%; left: 80%;"></div>
            <div class="particle" style="top: 10%; left: 90%;"></div>
        </div>

        <svg width="0" height="0">
            <defs>
                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color: var(--primary);" />
                    <stop offset="100%" style="stop-color: var(--secondary);" />
                </linearGradient>
            </defs>
        </svg>

        <!-- Header Section -->
        <header class="dashboard-header">
            <div class="server-badge">
                <i class="fas fa-crown me-2"></i>Premium Server
            </div>
            <h1 class="server-title">{{server_name}}</h1>
            <div id="username" class="username-display"></div>
        </header>

        <!-- Check if there's an error message -->
        <div id="error" class="alert text-center d-none mb-4" role="alert" 
             style="background: rgba(239, 68, 68, 0.1); 
                    border: 1px solid rgba(239, 68, 68, 0.2);
                    border-radius: 12px;
                    color: #fca5a5;
                    padding: 1rem;
                    backdrop-filter: blur(10px);
                    box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1), 0 2px 4px -1px rgba(239, 68, 68, 0.06);">
        </div>

        <!-- Search Form -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-6">
                <?php echo form_open('/', 'id="get_usage" class="search-form"'); ?>
                    <div class="mb-3">
                        <input type="text" id="email" class="search-input" 
                               name="email" placeholder="Enter username or UUID" required>
                        <input type="hidden" name="hid-email">
                    </div>
                    <button type="submit" id="usage_button" 
                            class="btn btn-primary w-100 py-2">
                        <i class="fas fa-search me-2"></i> Check Usage
                    </button>
                <?php echo form_close(); ?>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="row g-4">
            <!-- Usage Stats -->
            <div class="col-lg-4">
                <div class="dashboard-card h-100">
                    <h5 class="mb-4"><i class="fas fa-chart-line me-2"></i>Usage Statistics</h5>
                    <div class="stat-value" id="data-usage">
                        0 GB / 
                        0 GB
                    </div>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="data-progress-bar progress-bar" role="progressbar" 
                             style="width: 0%"></div>
                    </div>
                    <p class="stat-label mt-2">Total Data Usage</p>
                </div>
            </div>

            <!-- Usage Progress -->
            <div class="col-lg-4">
                <div class="dashboard-card h-100">
                    <h5 class="mb-4"><i class="fas fa-circle-notch me-2"></i>Usage Progress</h5>
                    <div class="progress-circle" role="progressbar" aria-label="Usage Progress" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <!-- SVG Progress Circle -->
                        <svg viewBox="0 0 100 100">
                            <circle class="progress-bg" cx="50" cy="50" r="45"/>
                            <circle class="progress-bar usage-progress-bar" cx="50" cy="50" r="45"
                                    stroke-dasharray="283"
                                    stroke-dashoffset="283"/>
                        </svg>
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <div class="stat-value" id="usage-percentage" data-end="%">
                                0
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Server Health -->
            <div class="col-lg-4">
                <div class="dashboard-card h-100">
                    <h5 class="mb-4"><i class="fas fa-server me-2"></i>Server Health</h5>
                    <div class="server-metrics">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>CPU Load</span>
                                <span class="cpu_usage">N/A</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="cpu-progress-bar progress-bar" role="progressbar" 
                                     style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>RAM Usage</span>
                                <span class="ram_usage">N/A</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="ram-progress-bar progress-bar" role="progressbar" 
                                     style="width: 0%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Disk Space</span>
                                <span class="disk_usage">N/A</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="disk-progress-bar progress-bar" role="progressbar" 
                                     style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Server Information -->
            <div class="col-lg-6">
                <div class="dashboard-card h-100">
                    <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i>Server Information</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock me-2 text-primary"></i>
                                <div>
                                    <div class="stat-label">Uptime</div>
                                    <div class="uptime">N/A</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                <div>
                                    <div class="stat-label">Location</div>
                                    <div><?= $server_location ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-network-wired me-2 text-primary"></i>
                                <div>
                                    <div class="stat-label">IP Address</div>
                                    <div><?= $server_ip ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-signal me-2 text-primary"></i>
                                <div>
                                    <div class="stat-label">Server Status</div>
                                    <div class="server_status">N/A</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="col-lg-6">
                <div class="dashboard-card h-100">
                    <h5 class="mb-4"><i class="fas fa-user-shield me-2"></i>Account Information</h5>
                    <div class="row g-3">
                        <!-- Account Status -->
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-3">
                                <i id="enable-toggle" class="fas fa-toggle-off me-2 text-primary"></i>
                                <div>
                                    <div class="stat-label">Account Status</div>
                                    <div class="status-indicator status-inactive">
                                        <i class="fas fa-circle me-2"></i>
                                        <span id="enable">Disabled</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Expiry Time -->
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-hourglass-half me-2 text-primary"></i>
                                <div>
                                    <div class="stat-label">Time Remaining</div>
                                    <div class="status-indicator status-active">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        <span id="remaining-time">0 days</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Limit -->
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-database me-2 text-primary"></i>
                                <div>
                                    <div class="stat-label">Data Limit</div>
                                    <div class="text-success" id="data-limit">
                                        0 GB
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Last Login -->
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock-rotate-left me-2 text-primary"></i>
                                <div>
                                    <div class="stat-label">Last Updated</div>
                                    <div class="text-info" id="last-update">
                                        N/A
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center mt-5">
            <small class="text-secondary">
                <a href="https://t.me/traffic_ui" target="_blank">Traffic-UI</a> v<?php echo getenv('APP_VERSION'); ?>
                <span id="update-info">
                    <i class='fas fa-check-circle text-success'></i>
                </span>
            </small>
            <p class="text-secondary mb-0">
                © <?php echo date('Y'); ?> Made with 
                <i class="fas fa-heart text-danger"></i> by 
                <span class="text-primary">mAX web™</span>
            </p>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Keep your existing JavaScript for functionality -->
    <script>
        // Global variable to hold the interval ID for continuous requests
        let fetchInterval;

        // get usage
        $(document).on('submit', '#get_usage', function(e){
            
            e.preventDefault();
            $('#usage_button').attr('disabled', 'disabled');
            $('#usage_button').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="sr-only">Fetching Data...</span> Fetching Data');
            $('#error').html('');
            $('#error').addClass('d-none');

            var formData = new FormData($(this)[0]);

            $.ajax({
                type: 'POST',
                url: '/getusage',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response){

                    $("input[name='csrf_test_name']").val(response.token);

                    $('#usage_button').html('<i class="fas fa-search"></i> Check Usage');
                    $('#usage_button').removeAttr('disabled');
                    
                    if(response.success){

                        if(response.uuid == ''){
                            $("input[name='email']").val(response.email);
                            $('#username').html(response.email);
                        }else{
                            $("input[name='email']").val(response.uuid);
                            $('#username').html(response.uuid);
                        }
                        
                        $("input[name='hid-email']").val(response.email);
                        $('#data-usage').html(response.total_up_down+' / '+response.total);
                        $('#data-limit').html(response.total);
                        $('#usage-percentage').html(response.percentage);
                        $('.data-progress-bar').css('width', response.percentage+'%');

                        let percentage = response.percentage || 0; // Default to 0 if it's undefined
                        let offset = 283 - (percentage * 283 / 100);
                        document.querySelector('.progress-circle .progress-bar').style.strokeDashoffset = offset;

                        if(response.enable == 1){
                            $('.status-indicator').removeClass('status-inactive');
                            $('.status-indicator').addClass('status-active');
                            $('#enable').html('Enabled');
                            $('#enable-toggle').removeClass('fa-toggle-off');
                            $('#enable-toggle').addClass('fa-toggle-on');
                        }else{
                            $('.status-indicator').removeClass('status-active');
                            $('.status-indicator').addClass('status-inactive');
                            $('#enable').html('Disabled');
                            $('#enable-toggle').removeClass('fa-toggle-on');
                            $('#enable-toggle').addClass('fa-toggle-off');
                        }

                        $('#remaining-time').html(response.remaining_days+' days');

                        // server info
                        $('.server_status').html(response.server_status);
                        $('.cpu_usage').html(response.cpu_usage + '%');
                        $('.cpu-progress-bar').css('width', response.cpu_usage+'%');
                        $('.ram_usage').html(response.ram_usage);
                        $('.ram-progress-bar').css('width', response.ram_usage_percent+'%');
                        $('.disk_usage').html(response.disk_space);
                        $('.disk-progress-bar').css('width', response.disk_space.split('%')[0]+'%');
                        let uptime = response.uptime ? response.uptime : 'N/A';
                        $('.uptime').html(uptime);

                        if (fetchInterval) {
                            // If there's already an ongoing interval, clear it to start fresh
                            clearInterval(fetchInterval);
                        }

                        // Update the UI with the initial data from the first request
                        updateUsageData(response);

                        // Start a continuous fetch every 1 second (1000ms)
                        fetchInterval = setInterval(function() {
                            var newFormData = new FormData();  // Create a new FormData for the next request
                            newFormData.append('csrf_test_name', $("input[name='csrf_test_name']").val()); // Include CSRF token in every request
                            newFormData.append('email', $("input[name='email']").val());

                            $.ajax({
                                type: 'POST',
                                url: '/getusage',
                                data: newFormData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(response) {
                                    $("input[name='csrf_test_name']").val(response.token);
                                    if (response.success) {
                                        updateUsageData(response);
                                    } else {
                                        // Stop continuous fetching if response is not successful
                                        clearInterval(fetchInterval);
                                        $("input[name='hid-email']").val('');
                                        $('#error').html(response.error_msg);
                                        $('#error').removeClass('d-none');
                                        $('#username').html('');
                                        $('#data-usage').html('0 GB / 0 GB');
                                        $('#data-limit').html('0 GB');
                                        $('#usage-percentage').html('0');
                                        $('.data-progress-bar').css('width', '0%');
                                        $('.cpu-progress-bar').css('width', '0%');
                                        $('.ram-progress-bar').css('width', '0%');
                                        $('.disk-progress-bar').css('width', '0%');

                                        let percentage = 0; // Default to 0 if it's undefined
                                        let offset = 283 - (percentage * 283 / 100);
                                        document.querySelector('.progress-circle .progress-bar').style.strokeDashoffset = offset;

                                        $('.status-indicator').removeClass('status-active');
                                        $('.status-indicator').addClass('status-inactive');
                                        $('#enable').html('Disabled');
                                        $('#enable-toggle').removeClass('fa-toggle-on');
                                        $('#enable-toggle').addClass('fa-toggle-off');
                                        $('#remaining-time').html('0 days');
                                    }
                                },
                                timeout: 10000, // 10 second timeout
                                error: function(xhr, status, error) {
                                    if (status === "timeout") {
                                        $('#error').html('Request timed out. Please try again.');
                                    }
                                    // Handle error and stop the continuous fetching
                                    clearInterval(fetchInterval);
                                    $("input[name='hid-email']").val('');
                                    $('#usage_button').html('<i class="fas fa-search"></i> Check Usage');
                                    $('#usage_button').removeAttr('disabled');
                                    $('#error').html('Please reload the page and try again!');
                                    $('#error').removeClass('d-none');
                                    $('#username').html('');
                                    $('#data-usage').html('0 GB / 0 GB');
                                    $('#data-limit').html('0 GB');
                                    $('#usage-percentage').html('0');
                                    $('.data-progress-bar').css('width', '0%');
                                    $('.cpu-progress-bar').css('width', '0%');
                                    $('.ram-progress-bar').css('width', '0%');
                                    $('.disk-progress-bar').css('width', '0%');

                                    let percentage = 0; // Default to 0 if it's undefined
                                    let offset = 283 - (percentage * 283 / 100);
                                    document.querySelector('.progress-circle .progress-bar').style.strokeDashoffset = offset;

                                    $('.status-indicator').removeClass('status-active');
                                    $('.status-indicator').addClass('status-inactive');
                                    $('#enable').html('Disabled');
                                    $('#enable-toggle').removeClass('fa-toggle-on');
                                    $('#enable-toggle').addClass('fa-toggle-off');
                                    $('#remaining-time').html('0 days');

                                    console.log(xhr.responseText);
                                }
                            });
                        }, 2000); // Request data every 2 second (2000ms)
                    }else{
                        $("input[name='hid-email']").val('');
                        $('#error').html(response.error_msg);
                        $('#error').removeClass('d-none');
                        $('#username').html('');
                        $('#data-usage').html('0 GB / 0 GB');
                        $('#data-limit').html('0 GB');
                        $('#usage-percentage').html('0');
                        $('.data-progress-bar').css('width', '0%');
                        $('.cpu-progress-bar').css('width', '0%');
                        $('.ram-progress-bar').css('width', '0%');
                        $('.disk-progress-bar').css('width', '0%');

                        let percentage = 0; // Default to 0 if it's undefined
                        let offset = 283 - (percentage * 283 / 100);
                        document.querySelector('.progress-circle .progress-bar').style.strokeDashoffset = offset;

                        $('.status-indicator').removeClass('status-active');
                        $('.status-indicator').addClass('status-inactive');
                        $('#enable').html('Disabled');
                        $('#enable-toggle').removeClass('fa-toggle-on');
                        $('#enable-toggle').addClass('fa-toggle-off');
                        $('#remaining-time').html('0 days');
                    }
                },
                timeout: 10000, // 10 second timeout
                error: function(xhr, status, error) {
                    if (status === "timeout") {
                        $('#error').html('Request timed out. Please try again.');
                    }
                    $("input[name='hid-email']").val('');
                    $('#usage_button').html('<i class="fas fa-search"></i> Check Usage');
                    $('#usage_button').removeAttr('disabled');
                    $('#error').html('Please reload the page and try again!');
                    $('#error').removeClass('d-none');
                    $('#username').html('');
                    $('#data-usage').html('0 GB / 0 GB');
                    $('#data-limit').html('0 GB');
                    $('#usage-percentage').html('0');
                    $('.data-progress-bar').css('width', '0%');
                    $('.cpu-progress-bar').css('width', '0%');
                    $('.ram-progress-bar').css('width', '0%');
                    $('.disk-progress-bar').css('width', '0%');

                    let percentage = 0; // Default to 0 if it's undefined
                    let offset = 283 - (percentage * 283 / 100);
                    document.querySelector('.progress-circle .progress-bar').style.strokeDashoffset = offset;

                    $('.status-indicator').removeClass('status-active');
                    $('.status-indicator').addClass('status-inactive');
                    $('#enable').html('Disabled');
                    $('#remaining-time').html('0 days');
                    $('#enable-toggle').removeClass('fa-toggle-on');
                    $('#enable-toggle').addClass('fa-toggle-off');

                    console.log(xhr.responseText);
                }
            });
        });

        function updateUsageData(response) {

            // Update the UI elements with new data from the response
            if (response.uuid == '') {
                $('#username').html(response.email);
            } else {
                $('#username').html(response.uuid);
            }

            $("input[name='hid-email']").val(response.email);
            $('#data-usage').html(response.total_up_down + ' / ' + response.total);
            $('#data-limit').html(response.total);
            $('#usage-percentage').html(response.percentage);
            $('.data-progress-bar').css('width', response.percentage + '%');

            $('.cpu-progress-bar').css('width', response.cpu_usage + '%');
            $('.ram-progress-bar').css('width', response.ram_usage_percent + '%');
            $('.disk-progress-bar').css('width', response.disk_space.split('%')[0] + '%');

            // Assuming response.percentage contains the percentage value
            let percentage = response.percentage || 0; // Default to 0 if it's undefined
            let offset = 283 - (percentage * 283 / 100);
            document.querySelector('.progress-circle .progress-bar').style.strokeDashoffset = offset;

            if (response.enable == 1) {
                $('.status-indicator').removeClass('status-inactive');
                $('.status-indicator').addClass('status-active');
                $('#enable').html('Enabled');
                $('#enable-toggle').removeClass('fa-toggle-off');
                $('#enable-toggle').addClass('fa-toggle-on');
            } else {
                $('.status-indicator').removeClass('status-active');
                $('.status-indicator').addClass('status-inactive');
                $('#enable').html('Disabled');
                $('#enable-toggle').removeClass('fa-toggle-on');
                $('#enable-toggle').addClass('fa-toggle-off');
            }

            $('#remaining-time').html(response.remaining_days + ' days');

            // server info
            $('.server_status').html(response.server_status);
            $('.cpu_usage').html(response.cpu_usage + '%');
            $('.ram_usage').html(response.ram_usage);
            $('.disk_usage').html(response.disk_space);
            let uptime = response.uptime ? response.uptime : 'N/A';
            $('.uptime').html(uptime);
        }

        // Prevent right click
        document.addEventListener('contextmenu', function(e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });

        // Prevent keyboard shortcuts including F12 and inspect element shortcuts
        document.addEventListener('keydown', function(e) {
            // Prevent F12
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                return false;
            }
            
            // Prevent Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C
            if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) {
                e.preventDefault();
                return false;
            }
            
            // Prevent Ctrl+U (view source)
            if (e.ctrlKey && (e.key === 'U' || e.key === 'u')) {
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
