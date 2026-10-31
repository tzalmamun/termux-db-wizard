# Termux DB Wizard

### cPanel-Style Database Management for Android Termux

**Termux DB Wizard** is a lightweight database management tool designed for Android users running Termux.

It brings a simple **cPanel-style MySQL Database Wizard experience** to your Android device.

With this project, you can create databases, create database users, set passwords, assign privileges, manage existing databases, and control phpMyAdmin — all through simple terminal commands and a web-based control panel.

No cPanel account or hosting control panel is required.

---

## 🚀 What Is This Project For?

Normally, when using MariaDB in Termux, you need to execute SQL commands manually to create databases, users, passwords, and permissions.

Termux DB Wizard makes these tasks easier through a web-based control panel inspired by cPanel's MySQL Database Wizard.

This project is useful for:

- Android developers using Termux.
- PHP developers working on local projects.
- Users who want a cPanel-like database workflow.
- Developers testing PHP and MySQL/MariaDB applications.
- Users who want to manage databases without manually writing SQL commands.

---

## ✨ Features

### Database Management
- Create new databases.
- Create database usernames.
- Set and confirm database passwords.
- Automatically assign database privileges.
- View existing databases.
- Delete databases.

### Database User Management
- View local database users.
- Create database users.
- Delete database users.
- Grant all privileges to users on their databases.

### Server Controls
- Start Database Wizard.
- Stop Database Wizard.
- Check server status.
- Open the panel in your browser.
- View server logs.

### phpMyAdmin Controls
- Start phpMyAdmin.
- Stop phpMyAdmin.
- Restart phpMyAdmin.
- Check phpMyAdmin status.
- Open phpMyAdmin in your browser.
- View phpMyAdmin logs.

---

## 📋 Requirements

Before installing, make sure you have:

- Android device.
- Termux.
- PHP CLI with the `mysqli` extension.
- MariaDB.
- Git.
- Curl.

Optional:

- phpMyAdmin installed in `~/phpmyadmin`.

---

## 📥 Installation Guide

### Step 1: Update Termux

```bash
pkg update && pkg upgrade -y
