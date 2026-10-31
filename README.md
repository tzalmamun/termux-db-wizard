# Termux cPanel-style Database Wizard

A local-only database management panel for Android Termux. It provides a cPanel-inspired workflow:

- Create a MariaDB database
- Create a database user with a password
- Grant that user privileges on the selected database
- List databases and local database users
- Delete databases and users
- Start/stop/status commands for phpMyAdmin and this wizard

> **Important:** This is a personal local-development tool, not a replacement for cPanel. It binds to `127.0.0.1` only. Do not expose it to the public internet. Dropping a database permanently deletes its data.

## Requirements

- Android with Termux (install Termux from a trusted source such as F-Droid or the official Termux GitHub)
- MariaDB installed and initialized
- PHP CLI with `mysqli`
- Optional: phpMyAdmin installed in `~/phpmyadmin`

## Install

Clone this repository:

```bash
pkg update && pkg upgrade -y
pkg install git php mariadb curl nano -y
git clone https://github.com/YOUR_GITHUB_USERNAME/termux-cpanel-db-wizard.git
cd termux-cpanel-db-wizard
chmod +x install.sh
./install.sh
```

The installer copies the wizard to `~/dbwizard`, installs `pma` and `dbw` commands in `~/bin`, and adds `~/bin` to PATH.

If `git` is not installed, download the repository ZIP from GitHub, extract it, open the extracted folder in Termux, and run `./install.sh`.

## MariaDB first-time setup

If MariaDB is not initialized yet, run:

```bash
mariadb-install-db
```

Start MariaDB using the service command available in your Termux installation. Commonly:

```bash
mysqld_safe &
```

Test the connection:

```bash
mariadb -h 127.0.0.1 -P 3306 -u root -e "SELECT VERSION();"
```

The wizard expects MariaDB to accept local TCP connections at `127.0.0.1:3306` as `root`. If your root account has a password, edit `~/dbwizard/config.php` and set it there.

## Commands

### Database Wizard

```bash
dbw on       # start wizard and open browser
dbw off      # stop wizard
dbw status   # check status
dbw open     # open browser
dbw log      # show recent log
```

Wizard URL: `http://127.0.0.1:8082`

### phpMyAdmin (optional)

If phpMyAdmin exists in `~/phpmyadmin`:

```bash
pma on
pma off
pma restart
pma status
pma open
pma log
```

phpMyAdmin URL: `http://127.0.0.1:8081`

## Change the wizard admin password

Edit:

```bash
nano ~/dbwizard/config.php
```

Set a strong password in `$admin_password`. Do not commit your personal password to GitHub.

## Change MariaDB connection details

In the same `config.php` file, update:

- `$db_host`
- `$db_port`
- `$db_admin_user`
- `$db_admin_password`

## Backups

Before deleting a database, export it in phpMyAdmin or use:

```bash
mariadb-dump -h 127.0.0.1 -P 3306 -u root DATABASE_NAME > backup.sql
```

If root has a password, add `-p` and enter it when prompted.

## Security notes

- The panel binds only to `127.0.0.1`; it is intended for the same phone.
- Change the default wizard admin password immediately after installation.
- Keep MariaDB and the wizard off when not in use.
- Never upload `config.php` containing passwords to GitHub.
- The wizard does not create remote users; database users are restricted to `localhost`.
- Deleting a database or user is irreversible unless you have a backup.

## Troubleshooting

### `mysqli` extension missing

Check:

```bash
php -m | grep -i mysqli
```

If it is missing, install/enable the appropriate PHP MySQL extension for your Termux package version.

### Wizard cannot connect to MariaDB

Check MariaDB is running and test:

```bash
mariadb -h 127.0.0.1 -P 3306 -u root -e "SELECT VERSION();"
```

Then update `~/dbwizard/config.php` to match your MariaDB root credentials.

### Address already in use

Check the service status and stop the previous process:

```bash
dbw status
dbw off
dbw on
```

## License

MIT. See [LICENSE](LICENSE).
