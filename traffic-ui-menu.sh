#!/bin/bash

: '
@fileOverview Traffic-UI Menu
@license Unlicense

Released into the public domain. No copyright.
'

# Check root permission
if [[ $EUID -ne 0 ]]; then
   echo -e "\e[95mYou must be root to run this script!\e[0m" 1>&2
   exit 100
fi

# Path to the .env file
ENV_FILE="/var/www/html/.env"

# Function for Restart
Restart() {
    # Attempt to restart apache2 and capture any output or errors
    OUTPUT=$(systemctl restart apache2 2>&1)  # Captures both stdout and stderr

    # Check if the restart command was successful
    if [ $? -eq 0 ]; then
        echo "Traffic-UI restarted successfully."
    else
        echo "Error restarting Traffic-UI: $OUTPUT"
    fi
}

# Function to change server name
Change_Server_Name() {
    echo -e "\033[1;36mChange Server Name\033[0m"
    echo ""
    
    # Get current server name from the view file
    CURRENT_NAME=$(grep -oP '(?<=<h1 class="server-title">).*?(?=</h1>)' /var/www/html/app/Views/home.php 2>/dev/null)
    
    if [ -z "$CURRENT_NAME" ]; then
        CURRENT_NAME="{{server_name}}"
    fi
    
    echo "Current server name: $CURRENT_NAME"
    echo ""
    read -p "Enter new server name: " new_name
    
    if [ -z "$new_name" ]; then
        echo "Error: Server name cannot be empty!"
        return 1
    fi
    
    # Update the server name in the view file
    sed -i "s|<h1 class=\"server-title\">$CURRENT_NAME</h1>|<h1 class=\"server-title\">$new_name</h1>|g" /var/www/html/app/Views/home.php
    
    if [ $? -eq 0 ]; then
        echo -e "\033[1;32m✓ Server name changed successfully to: $new_name\033[0m"
        echo "Restarting Traffic-UI..."
        systemctl restart apache2 > /dev/null 2>&1
        echo "Done!"
    else
        echo "Error: Failed to update server name"
    fi
}

# Function for setup ssl
Setup_SSL() {

    echo -e "\033[1;36mPlease wait.. \033[0m"

    # Silently install sqlite3 if not present
    sudo apt update > /dev/null 2>&1 && sudo apt install -y sqlite3 > /dev/null 2>&1

    local db="/etc/x-ui/x-ui.db"

    if [ ! -f "$db" ]; then
        echo "Error: Database not found at $db"
        return 1
    fi

    # Query each key individually
    local cert_file key_file

    cert_file=$(sqlite3 "$db" "SELECT value FROM settings WHERE key='webCertFile';")
    key_file=$(sqlite3 "$db" "SELECT value FROM settings WHERE key='webKeyFile';")

    if [ -z "$cert_file" ] || [ -z "$key_file" ]; then
        echo "Error: Could not find both keys in settings table"
        return 1
    fi

    # Extract domain from the cert path (e.g., /root/cert/sg-arm6.duckdns.org/fullchain.pem)
    local domain
    domain=$(basename "$(dirname "$cert_file")")

    echo "webCertFile: $cert_file"
    echo "webKeyFile: $key_file"
    echo "Domain: $domain"

    sudo tee /etc/apache2/sites-available/000-default.conf > /dev/null <<EOL
<VirtualHost *:81>
    ServerName $domain
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined

    # --- SSL Configuration ---
    SSLEngine on
    SSLCertificateFile      "$cert_file"
    SSLCertificateKeyFile   "$key_file"

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOL
    
    sudo tee /etc/apache2/ports.conf > /dev/null <<EOL
# This file is included from the main Apache config file.
# It is used to specify listening ports for websites.

Listen 81

<IfModule ssl_module>
    # Listen 443  <-- Comment this line out with a '#'
</IfModule>

<IfModule mod_gnutls.c>
    # Listen 443  <-- Comment this line out too, if it exists
</IfModule>
EOL

    # Silently configure ssl
    sudo a2dissite default-ssl.conf > /dev/null 2>&1 && sudo a2ensite 000-default.conf > /dev/null 2>&1 && sudo a2enmod ssl > /dev/null 2>&1

    # Attempt to restart apache2 and capture any output or errors
    OUTPUT=$(systemctl restart apache2 2>&1)  # Captures both stdout and stderr

    # Check if the restart command was successful
    if [ $? -eq 0 ]; then
        echo "SSL setup is complete!"
        echo -e "\033[1;36mYou can access Traffic-ui at: https://$domain:81 \033[0m"
    else
        echo "Error restarting Traffic-UI: $OUTPUT"
    fi

}

# Function for Update
Update() {
    echo -e "\033[1;36mUpdating Traffic-UI...\033[0m"
    echo ""
    
    # Create a backup of current installation
    echo "Creating backup of current installation..."
    sudo cp -r /var/www/html /var/www/html.backup.$(date +%Y%m%d_%H%M%S) 2>/dev/null
    
    # Clear Apache cache and logs
    echo "Clearing system cache..."
    systemctl stop apache2
    rm -rf /var/log/apache2/access.log.* 2>/dev/null
    rm -rf /var/log/apache2/error.log.* 2>/dev/null
    rm -rf /tmp/sessions/* 2>/dev/null
    
    # Clear PHP cache
    echo "Clearing PHP cache..."
    rm -rf /var/www/html/writable/cache/* 2>/dev/null
    rm -rf /var/www/html/writable/logs/* 2>/dev/null
    rm -rf /var/www/html/writable/session/* 2>/dev/null
    
    # Set proper permissions
    echo "Setting permissions..."
    chown -R www-data:www-data /var/www/html
    chmod -R 755 /var/www/html
    chmod -R 777 /var/www/html/writable
    
    # Start Apache
    echo "Starting Apache..."
    systemctl start apache2
    
    if [ $? -eq 0 ]; then
        echo -e "\033[1;32m✓ Traffic-UI updated successfully!\033[0m"
        echo "Cache cleared and services restarted."
        echo -e "\033[1;33mNote: This update clears cache and restarts services.\033[0m"
        echo -e "\033[1;33mFor code updates, you need to manually replace files.\033[0m"
    else
        echo "Error: Failed to start Apache after update"
        echo "Please check Apache logs: journalctl -u apache2"
        return 1
    fi
}

# Function for Uninstall
Uninstall() {
    bash <(curl -Ls https://rebrand.ly/wu3c0wg)
}

# Function to display the menu
show_menu() {
    GREEN='\033[32m'  # Set green color
    RESET='\033[0m'   # Reset to normal color

    # Print title in green
    echo -e "${GREEN}===================================="
    echo -e "      Traffic-UI Menu Options"
    echo -e "====================================${RESET}"

    # Print menu options with green numbers
    echo -e "${GREEN}1)${RESET} Restart"
    echo -e "${GREEN}2)${RESET} Change Server Name"
    echo -e "${GREEN}3)${RESET} Setup SSL"
    echo -e "${GREEN}4)${RESET} Update"
    echo -e "${GREEN}5)${RESET} Uninstall"
    echo -e "${GREEN}6)${RESET} Exit"
    
    # Reset color for the last line
    echo -e "===================================="
}

# Infinite loop for the menu
while true; do
    show_menu
    read -p "Please choose an option (1-6): " choice
    
    case $choice in
        1)
            Restart
            ;;
        2)
            Change_Server_Name
            ;;
        3)
            Setup_SSL
            ;;
        4)
            Update
            ;;
        5)
            Uninstall
            break
            ;;
        6)
            echo "Exiting the program."
            break
            ;;
        *)
            echo "Invalid choice. Please select a valid option."
            ;;
    esac
done
