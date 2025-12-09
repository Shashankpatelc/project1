# Wellness Tracker Project - Complete Analysis

## 📋 Project Overview
A web-based **Wellness Tracker** application that allows users to track their mood and stress levels daily with secure authentication and data storage.

---

## ✅ What's Working

### 1. **Database Setup**
- ✅ MySQL database `wellness_tracker_db` created automatically on first load
- ✅ Tables created: `users`, `mood_entries`, `coping_resources`
- ✅ Schema properly defined with appropriate columns and constraints
- ✅ Foreign key relationships established (mood_entries → users)

### 2. **Authentication System**
- ✅ **Login** (`/html/login.html` + `/php/login.php`)
  - Username/password form with validation
  - Password hashing with `password_hash()` and `password_verify()`
  - Session creation on successful login
  - Secure column mapping (password_hash)
  
- ✅ **Registration** (`/html/register.html` + `/php/register.php`)
  - Username, email, password registration
  - Duplicate username/email checking
  - Password confirmation validation
  - Secure password hashing (PASSWORD_DEFAULT)
  - Correct database column mapping

### 3. **File Structure**
- ✅ Well-organized folder hierarchy
- ✅ Proper separation of concerns (HTML views, PHP controllers, database config)
- ✅ Correct relative paths in forms and links

### 4. **Styling**
- ✅ `style.css` present with professional styling
- ✅ Responsive layout for forms and dashboard
- ✅ Proper color scheme and typography

---

## ⚠️ Critical Issues Found

### 1. **`/php/connect_db.php` is EMPTY** 🔴
**Impact:** Database connections in `dashboard.php` will fail
**Files Affected:**
- `dashboard.php` (line 17): `require_once '../php/connect_db.php';`

**Required Fix:** Restore the connection file:
```php
<?php
$conn = new mysqli("localhost", "root", "", "wellness_tracker_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

### 2. **`/php/login.php` - Missing User ID in Session** 🔴
**Issue:** Line 31 sets `$_SESSION["user_id"] = $user_id;` but `$user_id` is never fetched
**Impact:** Dashboard won't receive user_id, breaking all mood entry queries
**Fix:** Add user_id to the SELECT and bind_result:
```php
// Change line 21:
$stmt = $conn->prepare("SELECT user_id, username, password_hash FROM users WHERE username = ?");

// Change line 26:
$stmt->bind_result($fetched_user_id, $fetched_username, $fetched_hashed_password);

// Change line 30:
$_SESSION["user_id"] = $fetched_user_id;
```

### 3. **`/html/dashboard.html` - Missing PHP Preprocessor** 🔴
**Issue:** 
- Line 5: Broken CSS path: `href="../style.css"` should be `href="../style/style.css"`
- Contains PHP variables (`<?php echo $username; ?>`) but is not being processed as PHP
- CSS is a separate file, not embedded HTML
**Impact:** Dashboard won't display user name or styling

**Fix:** This file should be included via `require_once` from `dashboard.php` (which is correct), but the CSS path is wrong.

### 4. **`/php/dashboard.php` - Duplicate Require Statements** 🟡
**Issue:** Lines 18 and 20 both require `connect_db.php` with different paths:
```php
require_once '../php/connect_db.php';  // Line 18
require_once 'php/connect_db.php';     // Line 20 (DUPLICATE, WRONG PATH)
```

**Also:** Lines 89 and 93 both require the HTML file:
```php
require_once 'html/dashboard.html';        // Line 89 (wrong path)
require_once '../html/dashboard.html';     // Line 93 (correct path)
```

**Fix:** Remove duplicates and keep only the correct paths.

### 5. **`/php/register.php` - Typo in Success Message** 🟡
**Issue:** Line 8: `echo "Connected Scussfully";` should be `"Successfully"`
**Impact:** Minor - just a display typo

### 6. **`/php/login.php` - Validation Logic Issue** 🟡
**Issue:** Lines 14-19 validate inputs but don't prevent execution if empty
**Problem:** The query still executes even with empty username/password, returning "Invalid username or password"
**Fix:** Add `exit;` after the error message in the validation block

### 7. **Missing `/php/logout.php`** 🟡
**Issue:** `dashboard.html` references logout button (line 11): `href="php/logout.php"` but file doesn't exist
**Fix:** Create logout.php:
```php
<?php
session_start();
session_destroy();
header("Location: ../html/login.html");
exit;
?>
```

### 8. **`/html/register.html` - Link to Wrong Login Page** 🟡
**Issue:** Line 30: `<a href="login.html">Log in here</a>` 
**Should be:** `<a href="login.html">` (currently correct, but verify path consistency)

---

## 📊 Database Schema Verification

```sql
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,      ✅
    username VARCHAR(50) UNIQUE NOT NULL,        ✅
    email VARCHAR(100) UNIQUE NOT NULL,          ✅
    password_hash VARCHAR(255) NOT NULL,         ✅
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP ✅
);

CREATE TABLE mood_entries (
    entry_id INT AUTO_INCREMENT PRIMARY KEY,     ✅
    user_id INT NOT NULL,                        ✅
    mood_score TINYINT NOT NULL,                 ✅
    stress_score TINYINT NOT NULL,               ✅
    notes TEXT,                                  ✅
    entry_date DATE NOT NULL,                    ✅
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ✅
);

CREATE TABLE coping_resources (
    resource_id INT AUTO_INCREMENT PRIMARY KEY,  ✅
    category VARCHAR(50) NOT NULL,               ✅
    title VARCHAR(100) NOT NULL,                 ✅
    content TEXT NOT NULL,                       ✅
    sort_order TINYINT DEFAULT 0                 ✅
);
```

---

## 🔐 Security Observations

### Good Practices ✅
- Password hashing with `password_hash()` and `password_verify()`
- SQL prepared statements with bound parameters (prevents SQL injection)
- Session-based authentication
- `htmlspecialchars()` for output encoding

### Areas for Improvement ⚠️
- No CSRF token protection in forms
- No input sanitization for `notes` field (XSS risk)
- Consider adding password strength validation
- No rate limiting on login attempts (brute force risk)
- Email verification not implemented
- No "forgot password" functionality

---

## 🔗 Data Flow

### Registration Flow
1. User fills form at `/html/register.html`
2. POST to `/php/register.php`
3. Check for duplicate username/email
4. Hash password with PASSWORD_DEFAULT
5. Insert into `users` table (username, email, password_hash)
6. Redirect to login

### Login Flow
1. User fills form at `/html/login.html`
2. POST to `/php/login.php`
3. Query `users` table by username
4. Verify password with `password_verify()`
5. **BUG:** user_id not captured in SELECT
6. Create session with user_id, username
7. Redirect to `/php/dashboard.php`

### Dashboard Flow
1. Check session at `/php/dashboard.php`
2. Get user_id and username from session
3. Handle mood entry form submission (POST)
4. Query `mood_entries` table for past 7 days
5. Render `/html/dashboard.html` with session variables

---

## 📝 Action Items (Priority Order)

### 🔴 CRITICAL (Breaks Functionality)
1. [ ] Restore `/php/connect_db.php` with proper connection code
2. [ ] Fix login.php to capture and store user_id in session
3. [ ] Fix CSS path in dashboard.html from `../style.css` to `../style/style.css`
4. [ ] Remove duplicate require statements in dashboard.php
5. [ ] Create missing `/php/logout.php`

### 🟡 IMPORTANT (Improves Functionality)
6. [ ] Fix validation logic in login.php (add exit statement)
7. [ ] Fix typo in register.php ("Scussfully" → "Successfully")
8. [ ] Add error handling for database connections

### 🟢 NICE-TO-HAVE (Enhancements)
9. [ ] Add CSRF token protection
10. [ ] Add email verification
11. [ ] Implement password strength validation
12. [ ] Add rate limiting for login attempts
13. [ ] Add "forgot password" functionality

---

## 🧪 Testing Checklist

- [ ] User can register with valid credentials
- [ ] User cannot register with duplicate username/email
- [ ] User can login with registered credentials
- [ ] User cannot login with wrong password
- [ ] Session persists after login
- [ ] User can enter mood/stress data on dashboard
- [ ] Dashboard displays recent entries
- [ ] User can logout and session is destroyed
- [ ] Redirects work properly between pages
- [ ] Styling displays correctly on all pages

---

## 📚 Technology Stack
- **Backend:** PHP 7.4+
- **Database:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3
- **Server:** Apache 2.4.65
- **Session Management:** PHP native sessions

---

Generated: December 9, 2025
