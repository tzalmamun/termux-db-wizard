# Termux Database Wizard

A lightweight, local PHP database manager for Termux, inspired by the cPanel MySQL Database Wizard.

It helps you create databases, create database users, assign users to databases, grant privileges, and manage local databases from a simple web interface.

## Features

- Create a MySQL/MariaDB database
- Create a database user with a password
- Add a user to a database
- Grant all privileges to the selected user on the selected database
- View existing databases and local database users
- Delete databases and users
- Session-based admin login
- CSRF protection for form submissions
- Start and stop the web interface with the `dbw` command
- Optional phpMyAdmin start/stop helper with the `pma` command

## Requirements

- Android with Termux
- PHP with the `mysqli` extension
- MariaDB server
- Git (for installation from GitHub)
- curl (used by the helper scripts)

## Installation

Update Termux packages and install the required tools:

```bash
pkg update && pkg upgrade -y
pkg install git php mariadb curl nano -y
```

Clone the repository:

```bash
git clone https://github.com/tzalmamun/termux-db-wizard.git
cd termux-db-wizard
```

Run the installer:

```bash
chmod +x install.sh
./install.sh
source ~/.bashrc
```

## Start MariaDB

If MariaDB has not been initialized on this device, initialize it:

```bash
mariadb-install-db
```

Start the MariaDB server:

```bash
mysqld_safe &
```

Check the connection:

```bash
mariadb -h 127.0.0.1 -P 3306 -u root -e "SELECT VERSION();"
```

If your MariaDB installation uses a password for `root`, use that password when configuring the wizard.

## Configure the Wizard

Open the configuration file:

```bash
nano ~/dbwizard/config.php
```

Set a strong admin password and enter the correct MariaDB connection details.

Example settings (adjust them to match your device):

```php
<?php
return [
    'admin_password' => 'CHANGE_THIS_TO_A_LONG_RANDOM_PASSWORD',
    'db_host' => '127.0.0.1',
    'db_port' => 3306,
    'db_username' => 'root',
    'db_password' => '',
];
```

Do not publish `config.php` or commit it to GitHub. The repository's `.gitignore` excludes this file.

## Run

Start the Database Wizard:

```bash
dbw on
```

Open this address in your browser:

```text
http://127.0.0.1:8082
```

Useful commands:

```bash
dbw on       # Start the wizard
dbw off      # Stop the wizard
dbw status   # Check status
dbw open     # Open in browser
dbw log      # View the log
```

## Optional: phpMyAdmin

If phpMyAdmin is installed in `~/phpmyadmin`, you can use the included helper:

```bash
pma on
```

Open:

```text
http://127.0.0.1:8081
```

Commands:

```bash
pma on
pma off
pma status
pma open
pma log
```

phpMyAdmin is optional and is not installed automatically by this project.

## Project Structure

```text
termux-db-wizard/
├── README.md
├── LICENSE
├── .gitignore
├── install.sh
├── wizard/
│   ├── index.php
│   └── config.example.php
└── scripts/
    ├── dbw
    └── pma
```

## Update

To download the latest repository changes:

```bash
cd ~/termux-db-wizard
git pull
```

If the installer needs to be run again after an update:

```bash
chmod +x install.sh
./install.sh
```

Your local configuration should remain in `~/dbwizard/config.php`. Review the updated example configuration if the project adds new settings.

## Troubleshooting

### The `dbw` command is not found

Run:

```bash
source ~/.bashrc
```

If it still does not work, close and reopen Termux, then try again.

### MariaDB connection failed

Make sure MariaDB is running:

```bash
pgrep -a mysqld
```

Test the database connection:

```bash
mariadb -h 127.0.0.1 -P 3306 -u root -e "SELECT VERSION();"
```

Check that the host, port, username, and password in `~/dbwizard/config.php` are correct.

### PHP mysqli extension is missing

Check:

```bash
php -m | grep -i mysqli
```

The wizard requires `mysqli` to connect to MariaDB.

## Security Notes

- This project is intended for local use on your own device.
- The web server should remain bound to `127.0.0.1`; do not expose it publicly without adding proper production-grade security.
- Set a strong admin password before using the wizard.
- Do not share your MariaDB root password or `config.php`.
- Deleting a database permanently removes its data. Export a backup before deleting anything.
- Use a separate, limited-privilege MariaDB account instead of the root account if you adapt this project for a more secure setup.

## Important

Cloning this repository copies the application files only. It does not transfer your MariaDB databases or their contents to another phone. To move database data, export it on the old device and import it on the new device separately.

## Developer

**TZ Mamun**

GitHub: https://github.com/tzalmamun/termux-db-wizard

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE).
