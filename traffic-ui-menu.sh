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
    echo -e "${GREEN}2)${RESET} Setup SSL"
    echo -e "${GREEN}3)${RESET} Uninstall"
    echo -e "${GREEN}4)${RESET} Exit"
    
    # Reset color for the last line
    echo -e "===================================="
}

# Infinite loop for the menu
while true; do
    show_menu
    read -p "Please choose an option (1-7): " choice
    
    case $choice in
        1)
            Restart
            ;;
        2)
            Setup_SSL
            ;;
        3)
            Uninstall
            break
            ;;
        4)
            echo "Exiting the program."
            break
            ;;
        *)
            echo "Invalid choice. Please select a valid option."
            ;;
    esac
done
