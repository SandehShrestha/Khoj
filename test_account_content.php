<?php
echo "<h2>Account Content Test</h2>";

// Read the account content file
$contentFile = __DIR__ . '/account/content.html';
if (file_exists($contentFile)) {
    $content = file_get_contents($contentFile);
    echo "<p style='color: green;'>✓ File exists and contains:</p>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0; background: #f9f9f9;'>";
    echo "<pre>" . htmlspecialchars($content) . "</pre>";
    echo "</div>";
    
    // Check for unwanted content
    if (strpos($content, 'preferences') !== false) {
        echo "<p style='color: red;'>✗ Found 'preferences' in content</p>";
    } else {
        echo "<p style='color: green;'>✓ No 'preferences' found</p>";
    }
    
    if (strpos($content, 'Delete Account') !== false) {
        echo "<p style='color: red;'>✗ Found 'Delete Account' in content</p>";
    } else {
        echo "<p style='color: green;'>✓ No 'Delete Account' found</p>";
    }
    
    if (strpos($content, 'sign-out') !== false) {
        echo "<p style='color: red;'>✗ Found 'sign-out' in content</p>";
    } else {
        echo "<p style='color: green;'>✓ No 'sign-out' found</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ File does not exist!</p>";
}

echo "<h3>Instructions:</h3>";
echo "<ol>";
echo "<li>If the content above shows the unwanted sections, the file wasn't updated properly</li>";
echo "<li>If the content is clean but you still see the sections in the app, it's a browser cache issue</li>";
echo "<li>Try hard refresh: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)</li>";
echo "<li>Or try incognito/private browsing mode</li>";
echo "</ol>";

echo "<p><a href='index.php'>Go back to main app</a></p>";
?> 