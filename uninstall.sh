#!/bin/bash

: '
@fileOverview Traffic-UI Uninstall Script
@license Unlicense

Released into the public domain. No copyright.
'

# Stop Apache server
echo "Stopping Apache server..."
sudo systemctl stop apache2

# Disable Apache server from starting on boot
echo "Disabling Apache from starting on boot..."
sudo systemctl disable apache2

# Remove Apache and PHP packages
echo "Removing Apache and PHP packages..."
sudo apt remove --purge apache2 apache2-utils apache2-bin apache2.2-common libapache2-mod-php8.2 php8.2 php8.2-cli php8.2-fpm php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-mysql sqlite3 -y

# Remove PHP and Apache dependencies no longer needed
echo "Removing unnecessary dependencies..."
sudo apt autoremove --purge -y

# Remove Apache and PHP configuration files
echo "Removing Apache and PHP configuration files..."
sudo rm -rf /etc/apache2
sudo rm -rf /etc/php/8.2
sudo rm -rf /var/www/*

sudo rm /usr/local/bin/traffic-ui
sudo rm /usr/local/bin/traffic-ui-menu.sh

# Revert Apache port configuration (back to 80)
echo "Reverting Apache port configuration to 80..."
sudo sed -i 's/81/80/' /etc/apache2/ports.conf
sudo sed -i 's/<VirtualHost \*:81>/<VirtualHost \*:80>/' /etc/apache2/sites-available/000-default.conf

# Re-enable the default Apache site if it was disabled
echo "Re-enabling default Apache site..."
sudo a2ensite 000-default.conf
sudo systemctl reload apache2

# Finally, update package list
echo "Updating package list..."
sudo apt update -y

# Uninstallation message
echo -e "\n\033[1;32mTraffic-UI has been removed... It's hard to say goodbye. Thank you for using our script!\033[0m\n"