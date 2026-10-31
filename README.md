Termux DB Wizard

cPanel-Style Database Management for Android Termux

Termux DB Wizard is a lightweight, self-hosted database management tool designed for Android users running Termux.

It brings a simple cPanel-style MySQL Database Wizard experience to your Android device. You can create databases, create database users, assign privileges, manage existing databases, and control your local phpMyAdmin and Database Wizard servers — all from Termux.

No cPanel account or hosting control panel is required.

---

What Is This Project For?

Normally, when using MariaDB in Termux, you need to run SQL commands manually to create databases, create users, assign permissions, and delete old databases.

Termux DB Wizard makes these tasks easier through a web-based control panel.

It is especially useful for:

- Android developers using Termux.
- PHP developers working on local projects.
- Users who want a cPanel-like database workflow.
- Developers testing PHP and MySQL/MariaDB applications.
- Users who want to manage local databases without typing SQL commands every time.

Features

Database Management

- Create new databases.
- Create database usernames.
- Set and confirm database passwords.
- Automatically assign database privileges.
- View existing databases.
- Delete databases.

Database User Management

- View local database users.
- Create database users.
- Delete database users.
- Grant all privileges to a user on the newly created database.

Server Controls

- Start the Database Wizard.
- Stop the Database Wizard.
- Check server status.
- Open the panel in your browser.
- View server logs.

phpMyAdmin Controls

- Start phpMyAdmin.
- Stop phpMyAdmin.
- Restart phpMyAdmin.
- Check phpMyAdmin status.
- Open phpMyAdmin in your browser.
- View phpMyAdmin logs.

---

Requirements

Before installing, make sure you have:

- Android device.
- Termux.
- PHP CLI with the "mysqli" extension.
- MariaDB.
- Git.
- Curl.

Optional:

- phpMyAdmin installed in "~/phpmyadmin".

Installation Guide

Step 1: Update Termux

pkg update && pkg upgrade -y

Step 2: Install Required Packages

pkg install git php mariadb curl nano -y

Step 3: Clone This Repository

git clone https://github.com/tzalmamun/termux-db-wizard.git

Step 4: Enter the Project Directory

cd termux-db-wizard

Step 5: Give Installation Permission

chmod +x install.sh

Step 6: Run the Installer

./install.sh

Step 7: Reload Termux Configuration

source ~/.bashrc

Installation is complete!

---

MariaDB Setup

If MariaDB has not been initialized before, run:

mariadb-install-db

Start MariaDB:

mysqld_safe &

Check the database connection:

mariadb -h 127.0.0.1 -P 3306 -u root -e "SELECT VERSION();"

If your MariaDB root account has a password, configure it in the wizard settings.

Configure Admin Password

Open the configuration file:

nano ~/dbwizard/config.php

Find:

$admin_password = 'ChangeThis-Strong-Password-2026';

Replace it with your own strong password.

Also configure your MariaDB credentials if needed:

$db_host = '127.0.0.1';
$db_port = 3306;
$db_admin_user = 'root';
$db_admin_password = '';

Save the file and exit.

Never upload your personal "config.php" or passwords to GitHub.

---

How to Use

Database Wizard

Start the Database Wizard:

dbw on

Open in your browser:

http://127.0.0.1:8082

Stop:

dbw off

Check status:

dbw status

Open browser:

dbw open

View logs:

dbw log

phpMyAdmin

Start:

pma on

Open:

http://127.0.0.1:8081

Stop:

pma off

Restart:

pma restart

Check status:

pma status

Open browser:

pma open

View logs:

pma log

---

Default Local Addresses

Service| Address
Database Wizard| "http://127.0.0.1:8082"
phpMyAdmin| "http://127.0.0.1:8081"
MariaDB Host| "127.0.0.1"
MariaDB Port| "3306"

Project Structure

termux-db-wizard/
│
├── install.sh
├── README.md
├── LICENSE
│
├── scripts/
│   ├── dbw
│   └── pma
│
└── wizard/
    ├── index.php
    └── config.example.php

Updating the Project

To download the latest repository changes:

cd ~/termux-db-wizard
git pull
chmod +x install.sh
./install.sh

Your existing "~/dbwizard/config.php" is preserved by the installer.

Troubleshooting

1. mysqli extension is missing

php -m | grep -i mysqli

Make sure PHP's MySQLi extension is installed and enabled.

2. Database connection failed

Check whether MariaDB is running:

mariadb -h 127.0.0.1 -P 3306 -u root -e "SELECT VERSION();"

Check your settings in:

nano ~/dbwizard/config.php

3. Address already in use

dbw status
dbw off
dbw on

Security Notice

- This project is designed for local development on your own Android device.
- The web panel binds to "127.0.0.1" only.
- Do not expose the panel to the public internet.
- Change the default admin password immediately.
- Never share your database root password.
- Always back up important databases before deleting them.
- Database deletion permanently removes its data.

This is a lightweight personal development tool inspired by cPanel's database workflow. It is not an official cPanel product or a full replacement for cPanel.

Developer

Developed by TZ Mamun

GitHub: https://github.com/tzalmamun

Repository: https://github.com/tzalmamun/termux-db-wizard

License

This project is licensed under the MIT License.

See the "LICENSE" (LICENSE) file for details.

---

If you find this project useful, please give it a ⭐ Star on GitHub!
