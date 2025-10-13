#!/bin/bash

: '
@fileOverview Check usage stats of 3X-UI users
@license Unlicense

Released into the public domain. No copyright.
'

# Spinner animation characters
SPINNER_CHARS='-\|/'

# Function to start spinner in background
start_spinner() {
    local msg=$1
    tput civis  # Hide cursor
    (
        while :; do
            for (( i=0; i<${#SPINNER_CHARS}; i++ )); do
                printf "\r\033[1;36m%s\033[0m %s" "${SPINNER_CHARS:$i:1}" "$msg"
                sleep 0.1
            done
        done
    ) &
    SPINNER_PID=$!
}

# Function to stop spinner
stop_spinner() {
    tput cnorm  # Show cursor
    if [ -n "$SPINNER_PID" ]; then
        kill "$SPINNER_PID" >/dev/null 2>&1
    fi
    printf "\r\033[K"  # Clear the line
}

# Modify the update_progress function to use spinner for non-100% progress
update_progress() {
    local progress=$1
    local status=$2
    if [ "$progress" = "100" ]; then
        stop_spinner
        printf "\r\033[1;32m✓\033[0m %s\n" "$status"
    else
        stop_spinner
        start_spinner "$status"
    fi
}

# Add trap to ensure spinner is stopped if script is interrupted
trap 'stop_spinner' EXIT

# Function to run installation
install_traffic_ui() {

    SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

    update_progress 0 "Updating system packages..."
    sudo apt update -y >/tmp/traffic-ui-install.log 2>&1
    
    update_progress 10 "Installing Web Server..."
    apt install apache2 -y >>/tmp/traffic-ui-install.log 2>&1
    
    update_progress 20 "Installing dependencies..."
    apt install software-properties-common -y >>/tmp/traffic-ui-install.log 2>&1
    add-apt-repository ppa:ondrej/php -y >>/tmp/traffic-ui-install.log 2>&1
    apt update -y >>/tmp/traffic-ui-install.log 2>&1
    
    update_progress 30 "Installing Required Programming Languages for the Application..."
    apt install php8.2 php8.2-cli php8.2-fpm php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-mysql php8.2-ctype php8.2-intl -y >>/tmp/traffic-ui-install.log 2>&1
    apt install libapache2-mod-php8.2 -y >>/tmp/traffic-ui-install.log 2>&1
    apt install php8.2-sqlite3 -y >>/tmp/traffic-ui-install.log 2>&1
    
    update_progress 40 "Configuring Application Languages..."
    sed -i 's/;extension=intl/extension=intl/' /etc/php/8.2/apache2/php.ini >>/tmp/traffic-ui-install.log 2>&1
    sed -i 's/;extension=mbstring/extension=mbstring/' /etc/php/8.2/apache2/php.ini >>/tmp/traffic-ui-install.log 2>&1
    echo "extension=ctype" | tee -a /etc/php/8.2/apache2/php.ini >>/tmp/traffic-ui-install.log 2>&1
    echo "extension=tokenizer" | tee -a /etc/php/8.2/apache2/php.ini >>/tmp/traffic-ui-install.log 2>&1
    echo "extension=sqlite3" | tee -a /etc/php/8.2/apache2/php.ini >>/tmp/traffic-ui-install.log 2>&1
    
    update_progress 50 "Configuring Web Server..."
    a2enmod rewrite >>/tmp/traffic-ui-install.log 2>&1
    systemctl restart apache2 >>/tmp/traffic-ui-install.log 2>&1
    sed -i 's/80/81/' /etc/apache2/ports.conf >>/tmp/traffic-ui-install.log 2>&1
    
    # Update Apache virtual host configuration
    sudo bash -c 'cat > /etc/apache2/sites-available/000-default.conf << EOL
<VirtualHost *:81>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOL' >>/tmp/traffic-ui-install.log 2>&1
    
    systemctl restart apache2 >>/tmp/traffic-ui-install.log 2>&1
    
    update_progress 60 "Installing Framework..."
    cd /var/www >>/tmp/traffic-ui-install.log 2>&1
    wget https://github.com/codeigniter4/CodeIgniter4/archive/refs/tags/v4.5.5.zip >>/tmp/traffic-ui-install.log 2>&1
    apt install unzip -y >>/tmp/traffic-ui-install.log 2>&1
    yes | unzip v4.5.5.zip >>/tmp/traffic-ui-install.log 2>&1
    rm v4.5.5.zip >>/tmp/traffic-ui-install.log 2>&1
    mv CodeIgniter4-4.5.5/* html >>/tmp/traffic-ui-install.log 2>&1
    
    update_progress 70 "Setting up permissions..."
    chmod 777 /var/www/html/writable -R >>/tmp/traffic-ui-install.log 2>&1
    systemctl enable apache2 >>/tmp/traffic-ui-install.log 2>&1
    
    update_progress 80 "Coping required files..."
    rm /var/www/html/app/Config/Filters.php /var/www/html/app/Config/Routes.php /var/www/html/app/Controllers/Home.php /var/www/html/app/Views/home.php /var/www/html/public/panel.txt /var/www/html/.env >>/tmp/traffic-ui-install.log 2>&1
    
    cp "$SCRIPT_DIR/env" /var/www/html/.env >>/tmp/traffic-ui-install.log 2>&1
    
    update_progress 90 "Configuring application..."
    
    cp "$SCRIPT_DIR/Routes.php" /var/www/html/app/Config/ >>/tmp/traffic-ui-install.log 2>&1
    
    cp "$SCRIPT_DIR/Filters.php" /var/www/html/app/Config/ >>/tmp/traffic-ui-install.log 2>&1
    
    cp "$SCRIPT_DIR/Home.php" /var/www/html/app/Controllers/ >>/tmp/traffic-ui-install.log 2>&1
    
    cp "$SCRIPT_DIR/home.php" /var/www/html/app/Views/ >>/tmp/traffic-ui-install.log 2>&1
    
    cp "$SCRIPT_DIR/panel.txt" /var/www/html/public/ >>/tmp/traffic-ui-install.log 2>&1
    
    mkdir -p /var/www/html/public/assets >>/tmp/traffic-ui-install.log 2>&1
    cp "$SCRIPT_DIR/GaugeMeter.js" /var/www/html/public/assets/ >>/tmp/traffic-ui-install.log 2>&1
    
    cp "$SCRIPT_DIR/traffic-ui-menu.sh" /usr/local/bin/ >>/tmp/traffic-ui-install.log 2>&1
    sed -i 's/\r//' /usr/local/bin/traffic-ui-menu.sh >>/tmp/traffic-ui-install.log 2>&1
    chmod +x /usr/local/bin/traffic-ui-menu.sh >>/tmp/traffic-ui-install.log 2>&1
    ln -s /usr/local/bin/traffic-ui-menu.sh /usr/local/bin/traffic-ui >>/tmp/traffic-ui-install.log 2>&1
    
    update_progress 95 "Finalizing installation..."
    iptables -I INPUT 6 -m state --state NEW -p tcp --dport 81 -j ACCEPT >>/tmp/traffic-ui-install.log 2>&1
    netfilter-persistent save >>/tmp/traffic-ui-install.log 2>&1
    systemctl restart apache2 >>/tmp/traffic-ui-install.log 2>&1
    
    update_progress 100 "Installation complete!"
    return 0
}

# Clear screen
clear

# Show initial message
echo -e "\033[1;36mInstalling Traffic-UI\033[0m"
echo -e "\033[1;33mPlease do not close this window. Installation may take several minutes...\033[0m\n"

# Run installation
install_traffic_ui "$1"

# Check if installation was successful
if [ $? -eq 0 ]; then
    # Get public IP
    pubip="$(dig +short myip.opendns.com @resolver1.opendns.com)"
    if [ "$pubip" == "" ];then
        pubip=`ifconfig eth0 | awk 'NR==2 {print $2}'`
    fi
    if [ "$pubip" == "" ];then
        pubip=`ifconfig ens3 | awk 'NR==2 {print $2}'`
    fi

    # Show success message
    echo -e "\n\033[1;32m✓ Installation completed successfully!\033[0m"
    echo -e "\n\033[1;34mTraffic-UI is running on \033[1;31mhttp://$pubip:81\033[0m"
    echo -e "\033[1;36mRun \033[1;33mtraffic-ui \033[1;36mcommand to access the menu\033[0m\n"
else
    # Show error message
    echo -e "\n\033[1;31m✗ Installation failed!\033[0m"
    echo -e "\033[1;33mCheck the logs at /tmp/traffic-ui-install.log for details\033[0m\n"
fi
