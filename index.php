<?php
require_once __DIR__ . '/includes/auth.php';

// Check if user is logged in, if not redirect to login page
if (!isLoggedIn()) {
    header("Location: login/index.php");
    exit();
}

$currentUser = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Hunt</title>
    <link rel="stylesheet" href="./styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Logout confirmation modal styles */
        .logout-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .logout-modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .logout-modal h3 {
            margin-top: 0;
            color: #333;
        }
        
        .logout-modal-buttons {
            margin-top: 20px;
        }
        
        .logout-modal-buttons button {
            margin: 0 10px;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .logout-confirm {
            background-color: #dc3545;
            color: white;
        }
        
        .logout-cancel {
            background-color: #6c757d;
            color: white;
        }
        
        .logout-confirm:hover {
            background-color: #c82333;
        }
        
        .logout-cancel:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <button class="toggle-btn" id="toggle-btn">☰</button>
    <div class="app-container">
        <!-- Static Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="logo">Item Hunt</div>
            <nav>
                <ul>
                    <li>
                        <a href="#" data-page="categories" class="active">Categories</a>
                    </li>
                    <li>
                        <a href="#" data-page="account">Account Settings</a>
                    </li>
                    <li>
                        <a href="#" data-page="help">Help</a>
                    </li>
                    <li>
                        <a href="#" id="logout-btn">Logout</a>
                    </li>
                </ul>
            </nav>
            <div class="user-info">
                <p>Welcome, <?php echo htmlspecialchars($currentUser['Username']); ?>!</p>
            </div>
        </aside>

        <!-- Dynamic Content Area -->
        <main id="main-content">
            <!-- Content will be loaded here dynamically -->
        </main>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="logout-modal">
        <div class="logout-modal-content">
            <h3>Logout Confirmation</h3>
            <p>Are you sure you want to logout?</p>
            <div class="logout-modal-buttons">
                <button class="logout-cancel" onclick="closeLogoutModal()">Cancel</button>
                <button class="logout-confirm" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>

    <script src="./js/app.js"></script>
    <script>
        // Load categories content by default
        document.addEventListener("DOMContentLoaded", () => {
            loadPageContent("categories");
        });

        // Function to load page content
        async function loadPageContent(page) {
            try {
                // Check if the page has a PHP version, otherwise use HTML
                let response = await fetch(`./${page}/content.php?v=${Date.now()}`);
                if (!response.ok) {
                    // If PHP version doesn't exist, try HTML version
                    response = await fetch(`./${page}/content.html?v=${Date.now()}`);
                }
                
                if (response.ok) {
                    const content = await response.text();
                    document.getElementById("main-content").innerHTML = content;

                    // Update active state in navigation
                    document.querySelectorAll(".sidebar nav a").forEach((link) => {
                        link.classList.toggle(
                            "active",
                            link.getAttribute("data-page") === page
                        );
                    });

                    // Load page-specific script if it exists
                    const script = document.createElement("script");
                    script.src = `./${page}/script.js?v=${Date.now()}`;
                    script.onload = () => {
                        if (page === "categories" && typeof initCategoriesPage === "function") {
                            initCategoriesPage();
                        } else if (page === "account" && typeof initializeAccountPage === "function") {
                            // Reset account page flag before initializing
                            if (typeof resetAccountPage === "function") {
                                resetAccountPage();
                            }
                            initializeAccountPage();
                        } else if (page === "help" && typeof initHelpPage === "function") {
                            initHelpPage();
                        }
                    };
                    document.body.appendChild(script);

                    // Load page-specific CSS if needed
                    if (page === "categories") {
                        loadCSS(`./categories/styles.css?v=${Date.now()}`);
                    } else if (page === "account") {
                        loadCSS(`./account/style.css?v=${Date.now()}`);
                    } else if (page === "help") {
                        loadCSS(`./help/styles.css?v=${Date.now()}`);
                    } else loadCSS("");
                } else {
                    document.getElementById("main-content").innerHTML = `<p>Page not found: ${page}</p>`;
                }
            } catch (error) {
                console.error("Error loading page:", error);
                document.getElementById("main-content").innerHTML = `<p>Error loading page: ${error.message}</p>`;
            }
        }

        // Handle navigation clicks
        document.querySelectorAll(".sidebar nav a").forEach((link) => {
            link.addEventListener("click", (e) => {
                e.preventDefault();
                const page = e.target.getAttribute("data-page");
                if (page) {
                    loadPageContent(page);
                }
            });
        });

        // Logout functionality
        document.getElementById('logout-btn').addEventListener('click', function(e) {
            e.preventDefault();
            showLogoutModal();
        });

        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'block';
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        function confirmLogout() {
            window.location.href = 'logout.php';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) {
                closeLogoutModal();
            }
        }

        function loadCSS(href) {
            // Remove any previously loaded page-specific CSS
            document.querySelectorAll("link[data-page-style]").forEach((link) => link.remove());
            // Add new CSS
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = href;
            link.setAttribute("data-page-style", "true");
            document.head.appendChild(link);
        }
    </script>
</body>
</html> 