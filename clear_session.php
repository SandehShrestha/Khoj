<?php
// Start session
session_start();

// Clear all session data
session_unset();
session_destroy();
session_write_close();

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Regenerate session ID
session_regenerate_id(true);

echo "<h2>Session Cleared</h2>";
echo "<p style='color: green;'>✓ All session data has been cleared!</p>";
echo "<p>You will need to log in again.</p>";
echo "<p><a href='login/index.php'>Go to Login Page</a></p>";
?> 