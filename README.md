# unsecure-website
A very unsecure social platform for demonstration and learning purposes

---

> ⚠️ **WARNING: This application is intentionally insecure. DO NOT deploy to production.**
>
> This project is designed for **security demonstrations, penetration testing practice, and learning common web vulnerabilities**.

---

## 📚 Purpose

This is a deliberately vulnerable PHP + SQLite social-style web application that allows users to:

* Register / Login
* Create posts with images
* Like/unlike posts
* Comment on posts
* Edit profile & avatar
* Delete posts/comments

It exists to help students and developers:

* Practice identifying vulnerabilities
* Learn exploitation techniques safely
* Understand why secure coding matters
* Test security tools (Burp, OWASP ZAP, sqlmap, etc.)
* Teach defensive programming

---

## 🚨 Security Notice

This app intentionally contains **serious security flaws**, including but not limited to:

* SQL Injection
* Plaintext passwords
* Missing authentication checks
* Insecure cookies
* File upload vulnerabilities
* CSRF
* XSS
* Broken access control

### ❌ Never:

* Deploy publicly
* Use real credentials
* Host on the internet
* Use in production environments

### ✅ Only:

* Run locally
* Use in labs/CTFs
* Use inside isolated environments or VMs

---

## 🧩 Tech Stack

* PHP (no framework)
* SQLite3
* Server-side rendered pages
* Minimal validation/sanitization

---

## ⚙️ Setup

### Requirements

* PHP 8+
* SQLite3 extension enabled

Verify:

```bash
php -v
php -m | grep sqlite
```

---

### Installation & Run

### 1. Clone the repository

```bash
git clone https://github.com/ionvop/unsecure-website
cd unsecure-website
```

### 2. Initialize the database (required)

The project uses SQLite and must be initialized before first run.

Run from the command line:

```bash
php init.php
```

You should see:

```
Database initialized.
```

> ⚠️ This script **must be run from CLI only** and will create/reset `database.db` using `init.sql`.

---

### 3. Start the development server

```bash
php -S localhost:8000
```

Open in browser:

```
http://localhost:8000
```

---

### 🔁 Resetting the database

If you want to reset all data:

```bash
rm database.db
php init.php
```

---

## 🗂 Project Structure

```
/
├── login/
│   └── index.php       # Login page
├── post/
│   └── index.php       # Post page
├── register/
│   └── index.php       # Register page
├── search/
│   └── index.php       # Search page
├── uploads/
│   ├── default.jpg     # Default placeholder image
│   └── ...             # User uploaded files
├── user/
│   ├── edit/
│   │   └── index.php   # Edit profile page
│   └── index.php       # Profile page
├── .gitignore          # Git ignore
├── common.php          # Shared helpers
├── database.db         # SQLite database
├── export.php          # Database export
├── index.php           # Homepage
├── init.php            # Database initialization
├── init.sql            # Database schema
├── README.md           # Documentation
├── script.js           # Client-side scripts
├── server.php          # Main backend logic (intentionally vulnerable)
└── style.css           # Stylesheet
```

---

## 🔍 Intentional Vulnerabilities (Learning Targets)

Below are examples of weaknesses you can explore.

### 🧨 SQL Injection

Raw string interpolation everywhere:

```php
WHERE "username" = '{$_POST["username"]}'
```

Try:

```
' OR 1=1 --
```

Targets:

* login
* register
* posts
* comments
* profile edits

---

### 🔐 Plaintext Password Storage

Passwords are stored directly:

```php
VALUES (..., '{$_POST["password"]}');
```

Learn:

* password hashing (bcrypt/Argon2)
* salting
* credential leaks

---

### 🍪 Insecure Authentication

User identity is stored in a cookie:

```php
setcookie("userId", $userId);
```

No:

* signing
* encryption
* HttpOnly
* Secure flag

Try:

* cookie tampering
* privilege escalation

---

### 📂 Unsafe File Uploads

Files are uploaded without:

* MIME validation
* extension checks
* content scanning

You can:

* upload PHP shells
* upload large files
* upload arbitrary content

Learn:

* safe upload handling
* storage isolation

---

### ❌ Missing Authorization

Example:

```php
DELETE FROM "posts"
WHERE "id" = '{$_POST["post_id"]}'
```

No ownership check.

Try:

* deleting others’ posts
* modifying others’ content

Learn:

* access control enforcement

---

### 🌐 CSRF Vulnerabilities

No CSRF tokens anywhere.

Try:

* cross-site form submissions
* forced actions

Learn:

* CSRF protection strategies

---

### 🧪 XSS

User content is not sanitized:

* posts
* comments
* profile descriptions

Try:

```html
<script>alert(1)</script>
```

Learn:

* output encoding
* CSP

---

## 🎯 Suggested Exercises

### Beginner

* Log in without knowing a password
* Delete another user's post
* Change your user ID via cookie editing

### Intermediate

* Dump all users via SQL injection
* Upload a malicious file
* Steal another user’s session

### Advanced

* Chain vulnerabilities
* Build automated exploit scripts
* Harden the app and patch all issues

---

## 🛡 Hardening Challenge (Optional)

Try fixing it:

* Prepared statements
* Password hashing
* CSRF tokens
* File validation
* Access control checks
* Escaped output
* Secure cookies
* Rate limiting

---

## 👩‍🏫 Ideal For

* Security workshops
* OWASP training
* CTF challenges
* University courses
* Pentesting practice
* Secure coding lessons

---

## ⚠️ Final Reminder

If you can break this app easily…

👉 That’s the point.

If you deploy this publicly…

👉 It **will** get compromised.

---

### Disclaimer

This documentation was generated by ChatGPT but the entire codebase was written by hand.