# Login Debug Guide - Ten12 Admin

## Quick Access

### Debug Tools Available:

1. **Login Debug Mode**
   - URL: `https://ten12.wasmer.app/admin/login.php?debug=1`
   - Shows detailed error messages and logs to browser console

2. **System Debug Report**
   - URL: `https://ten12.wasmer.app/admin/debug.php`
   - Complete system status, database configuration, and file permissions

3. **Server Logs**
   - Location: `backend/logs/login.log`
   - Contains all login attempts and errors

---

## Common Issues & Solutions

### Issue 1: "Database connection failed"

**Possible Causes:**
- DATABASE_URL environment variable not set
- Invalid database credentials
- Database server unreachable

**Solutions:**
1. Check your DATABASE_URL in environment variables
2. Visit `/admin/debug.php` to see current database configuration
3. For Wasmer deployment, ensure DATABASE_URL is properly set in deployment config

**In-Memory SQLite Warning:**
- If DATABASE_URL is not set, the app uses in-memory SQLite
- **This means all data is lost between requests**
- **You cannot use this for production login**

---

### Issue 2: "User not found"

**Possible Causes:**
- Admin user doesn't exist in database
- You're using in-memory SQLite (data lost between requests)
- Wrong username

**Solutions:**
1. Check `/admin/debug.php` - "Admin Users" section to see existing users
2. If no users exist, you need to:
   - Run the database setup script
   - Or manually create an admin user
3. Verify you're using the correct username (case-sensitive)

---

### Issue 3: "Invalid password"

**Possible Causes:**
- Wrong password
- Password hash corrupted
- Password_verify() function issue

**Solutions:**
1. Check `/admin/debug.php` - "Admin Users" table
2. Verify the user exists
3. Enable debug mode to see detailed password verification logs
4. Check browser console for detailed error messages

---

### Issue 4: No Error Message Displayed

**Possible Causes:**
- PHP error display is disabled
- Session not starting properly
- JavaScript error preventing form submission

**Solutions:**
1. Open browser Developer Tools (F12 → Console tab)
2. Look for JavaScript errors
3. Check Network tab → see the login request response
4. Enable debug mode: `login.php?debug=1`
5. Check server logs: `backend/logs/login.log`

---

## Debug Mode Usage

### Step 1: Access Login with Debug Enabled

```
https://ten12.wasmer.app/admin/login.php?debug=1
```

### Step 2: Open Browser Developer Tools

- **Chrome/Edge:** Press `F12` or `Ctrl+Shift+I`
- **Firefox:** Press `F12` or `Ctrl+Shift+I`

### Step 3: Try Logging In

The page will display:
- Detailed error messages in red
- Console logs showing what's happening
- Debug information banner

### Step 4: Check Console Output

Go to **Console** tab to see:
- "Debug Mode Enabled" message
- Form submission data
- Any JavaScript errors

### Step 5: Check Server Logs

Look at `/backend/logs/login.log` to see:
- Login attempts
- User lookup results
- Password verification steps
- Any exceptions

---

## Reading the Logs

### Example Success Log:
```
Login attempt for user: admin
Database connected successfully
Attempting to verify password for: admin
Login successful for user: admin
```

### Example Failure Log:
```
Login attempt for user: wronguser
Database connected successfully
Attempting to verify password for: wronguser
User does not exist: wronguser
Invalid credentials for user: wronguser
```

---

## Creating an Admin User

If no admin users exist, you'll need to create one.

### Option 1: Via Database Setup Script

If you have database access, run:
```sql
INSERT INTO admin_users (username, password, email, role) 
VALUES ('admin', '$2y$10$...', 'admin@ten12.local', 'admin');
```

The password must be a bcrypt hash. Generate one:

**Using PHP:**
```php
echo password_hash('your_password_here', PASSWORD_DEFAULT);
```

**Using Online Tools:**
- https://www.bcryptcalculator.com/

### Option 2: Via Admin Add User Page

If you can already log in as admin:
1. Go to `/admin/dashboard.php`
2. Navigate to "Manage Users"
3. Add new user

---

## Accessing Server Logs

### Via Browser (If File Permissions Allow)

Create a simple viewer at `/admin/logs.php`:

```php
<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$log_file = __DIR__ . '/../backend/logs/login.log';
if (file_exists($log_file)) {
    echo '<pre>';
    echo htmlspecialchars(file_get_contents($log_file));
    echo '</pre>';
} else {
    echo 'Log file not found';
}
?>
```

### Via SSH/Terminal

```bash
# View last 50 lines
tail -50 backend/logs/login.log

# View real-time updates
tail -f backend/logs/login.log

# Search for errors
grep "error\|failed\|Error" backend/logs/login.log
```

---

## Database Configuration Quick Check

Run the debug report:
```
https://ten12.wasmer.app/admin/debug.php
```

This shows:
- ✅ Server PHP version and configuration
- ✅ Database connection status
- ✅ Existing admin users
- ✅ File permissions
- ✅ Session status
- ✅ Recent log entries

---

## Environment Variables for Wasmer

If you're deploying on Wasmer, set:

```
DATABASE_URL=postgresql://user:password@host:5432/database
# or
DATABASE_URL=mysql://user:password@host:3306/database
```

**Important:** Without DATABASE_URL, the app uses in-memory SQLite and you cannot persist login data!

---

## Testing Connection Without Login

Create a test file at `/admin/test-connection.php`:

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

try {
    require_once __DIR__ . '/../backend/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "<p style='color: green;'>✓ Database connection successful</p>";
        
        // Test query
        $result = $db->query("SELECT COUNT(*) as count FROM admin_users");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        echo "<p>Admin users count: " . $row['count'] . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
```

---

## Contacting Support

When reporting issues, provide:

1. The output from `/admin/debug.php`
2. Recent entries from `/backend/logs/login.log`
3. Browser console errors (F12 → Console)
4. The exact URL you're trying to access
5. What error message you see (or that you see no message)
6. Your username/password (separately, securely)

---

## Additional Resources

- [PHP Password Hashing](https://www.php.net/manual/en/function.password-hash.php)
- [PDO Error Handling](https://www.php.net/manual/en/pdo.error-handling.php)
- [Session Configuration](https://www.php.net/manual/en/session.configuration.php)
