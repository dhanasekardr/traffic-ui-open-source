# Traffic-UI

[![Version](https://img.shields.io/badge/version-1.3.4-blue.svg)](https://github.com/ScriptCascade/traffic-ui-open-source)
[![PHP](https://img.shields.io/badge/PHP-8.2-purple.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-Open%20Source-green.svg)](LICENSE)
[![Telegram](https://img.shields.io/badge/Telegram-@traffic__ui-blue.svg)](https://t.me/traffic_ui)

A beautiful, modern web dashboard for monitoring VPN panel user statistics and server health metrics in real-time.

![Traffic-UI Dashboard](https://via.placeholder.com/800x400/0f172a/8b5cf6?text=Traffic-UI+Dashboard)

---

## ✨ Features

### 🎨 Beautiful Modern UI
- Stunning gradient design with animated backgrounds
- Responsive layout for all devices
- Smooth animations and transitions
- Dark theme optimized for long viewing sessions

### 📊 Real-Time Monitoring
- **Live bandwidth tracking** - Monitor upload/download in real-time
- **Server health metrics** - CPU, RAM, and disk usage
- **User account status** - Active/inactive status display
- **Data limit visualization** - Progress bars and circular gauges
- **Auto-refresh** - Dashboard updates every 2 seconds

### 🔒 Security Features
- CSRF protection
- SQL injection prevention
- Developer tools protection
- Secure session handling

### 🎯 Panel Support
- ✅ **3X-UI Panel**
- ✅ **Marzban Panel**
- 🔄 **Hiddify Panel** (Coming soon)

---

## 🚀 Quick Installation

### One-Line Install (Recommended)

```bash
bash <(curl -Ls https://raw.githubusercontent.com/ScriptCascade/traffic-ui-open-source/main/install.sh)
```

### Manual Installation

```bash
# Clone the repository
git clone https://github.com/ScriptCascade/traffic-ui-open-source.git
cd traffic-ui-open-source

# Run installer
sudo bash install.sh
```

### Access Your Dashboard

After installation:
```
http://YOUR_SERVER_IP:81
```

---

## 📋 Requirements

| Requirement | Version/Details |
|------------|----------------|
| **OS** | Ubuntu 18.04+ / Debian 10+ |
| **RAM** | Minimum 512MB |
| **VPN Panel** | 3X-UI or Marzban |
| **Root Access** | Required for installation |

---

## 🎯 Usage

### Check User Statistics

1. Open Traffic-UI in your browser: `http://YOUR_IP:81`
2. Enter **username** or **UUID**
3. Click **"Check Usage"**
4. View real-time statistics

### Management Menu

Access the management menu:

```bash
traffic-ui
```

**Available Options:**

| Option | Description |
|--------|-------------|
| **1. Restart** | Restart Traffic-UI service |
| **2. Change Server Name** | Customize your server display name |
| **3. Setup SSL** | Configure SSL certificates |
| **4. Uninstall** | Remove Traffic-UI completely |
| **5. Exit** | Close the menu |

---

## ⚙️ Configuration

### Database Configuration

Edit `/var/www/html/.env`:

```ini
# For 3X-UI
database.default.database = /etc/x-ui/x-ui.db

# For Marzban
database.default.database = /var/lib/marzban/db.sqlite3
```

### Panel Type Selection

Edit `/var/www/html/public/panel.txt`:

```
1   # For 3X-UI
2   # For Marzban
3   # For Hiddify
```

### Change Server Name

**Option 1: Using Menu (Recommended)**
```bash
traffic-ui
# Select option 2 - Change Server Name
```

**Option 2: Manual Edit**

Edit `/var/www/html/app/Views/home.php` and find:
```html
<h1 class="server-title">{{server_name}}</h1>
```

Replace `{{server_name}}` with your desired name.

---

## 🛠️ Troubleshooting

### Installation Fails

```bash
# Check installation logs
cat /tmp/traffic-ui-install.log
```

### Can't Access Dashboard

```bash
# Check if Apache is running
sudo systemctl status apache2

# Restart Apache
sudo systemctl restart apache2

# Check firewall
sudo ufw allow 81/tcp
```

### Database Connection Error

```bash
# Verify database exists
ls -la /etc/x-ui/x-ui.db

# Check permissions
sudo chmod 644 /etc/x-ui/x-ui.db

# Verify panel type
cat /var/www/html/public/panel.txt
```

### "Whoops! We seem to have hit a snag" Error

This usually means the view file is missing or has wrong permissions:

```bash
# Check if view file exists
ls -la /var/www/html/app/Views/home.php

# Fix permissions
sudo chmod 644 /var/www/html/app/Views/home.php
sudo systemctl restart apache2
```

---

## 🗑️ Uninstallation

### Option 1: Using Menu
```bash
traffic-ui
# Select option 4 - Uninstall
```

### Option 2: Using Uninstall Script
```bash
cd traffic-ui-open-source
sudo bash uninstall.sh
```

### Option 3: One-Line Command
```bash
bash <(curl -Ls https://raw.githubusercontent.com/ScriptCascade/traffic-ui-open-source/main/uninstall.sh)
```

---

## 📁 File Structure

```
traffic-ui-open-source/
├── install.sh                 # Quick installer (curl-compatible)
├── install-traffic-ui.sh      # Detailed installer with progress
├── uninstall.sh              # Uninstaller script
├── traffic-ui-menu.sh        # Management menu
├── Home.php                  # Main controller
├── home-content.php          # Dashboard view template
├── Routes.php                # URL routing configuration
├── Filters.php               # Security filters
├── env                       # Environment configuration
├── panel.txt                 # Panel type selector
├── GaugeMeter.js            # Gauge meter library
├── commands.txt             # Quick reference commands
└── README.md                # This file
```

---

## 🎨 Dashboard Features

### Main Dashboard Sections

1. **Usage Statistics**
   - Total data used vs. limit
   - Upload/download breakdown
   - Progress bar visualization

2. **Usage Progress Circle**
   - Animated circular gauge
   - Real-time percentage display
   - Color-coded status

3. **Server Health**
   - CPU load percentage
   - RAM usage monitoring
   - Disk space tracking

4. **Server Information**
   - Server uptime
   - Geographic location
   - IP address
   - Server status

5. **Account Information**
   - Active/inactive status
   - Days remaining
   - Data limit
   - Last update time

---

## 🔐 Security Features

- **CSRF Protection** - Prevents cross-site request forgery
- **SQL Injection Prevention** - Parameterized queries
- **Developer Tools Protection** - Blocks inspect element
- **Right-click Protection** - Prevents context menu
- **Keyboard Shortcuts Disabled** - Blocks F12, Ctrl+Shift+I, etc.
- **Input Validation** - UUID and username verification

---

## 🌐 Supported Panels

### 3X-UI
- Full support for user statistics
- Real-time traffic monitoring
- Account status tracking

### Marzban
- Complete integration
- User data tracking
- Traffic usage monitoring

### Hiddify (Coming Soon)
- Planned support in future updates

---

## 🤝 Support & Community

- **Telegram Channel**: [@traffic_ui](https://t.me/traffic_ui)
- **Issues**: [GitHub Issues](https://github.com/ScriptCascade/traffic-ui-open-source/issues)
- **Discussions**: [GitHub Discussions](https://github.com/ScriptCascade/traffic-ui-open-source/discussions)

---

## 📝 Changelog

### Version 1.3.4 (Current)
- ✅ Initial open source release
- ✅ Support for 3X-UI and Marzban
- ✅ Modern responsive UI design
- ✅ Real-time monitoring dashboard
- ✅ SSL support
- ✅ One-line installation
- ✅ Management menu with server name customization

---

## 🙏 Credits

- **Created by**: [mAX web™](https://t.me/traffic_ui)
- **Framework**: [CodeIgniter 4](https://codeigniter.com/)
- **Gauge Library**: Modified [GaugeMeter](https://github.com/mictronics/GaugeMeter) by Mictronics
- **Original Concept**: AshAlom
- **Icons**: [Font Awesome](https://fontawesome.com/)
- **Fonts**: [Google Fonts](https://fonts.google.com/)

---

## 📜 License

This project is open source and available for free use.

---

## 💝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## ⭐ Star History

If you find this project useful, please consider giving it a star ⭐

---

## 📧 Contact

For support and questions:
- Telegram: [@traffic_ui](https://t.me/traffic_ui)

---

<div align="center">

**Made with ❤️ by mAX web™**

[⬆ Back to Top](#traffic-ui)

</div>

