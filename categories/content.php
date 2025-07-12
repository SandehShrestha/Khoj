<?php
require_once __DIR__ . '/../includes/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo '<p>Please log in to view this content.</p>';
    exit();
}

$currentUser = getCurrentUser();
?>

<div class="categories-container">
    <div class="search-bar" id="search-bar">
        <input type="text" class="search-input" placeholder="Search item name...">
    </div>

    <div class="category-buttons">
        <button class="category-btn active" data-category="all">All Items</button>
        <button class="category-btn" data-category="electronic">Electronic</button>
        <button class="category-btn" data-category="card">Card</button>
        <button class="category-btn" data-category="cloth">Cloth</button>
        <button class="category-btn" data-category="stationary">Stationary</button>
        <button class="category-btn" data-category="keys">Keys</button>
        <button class="category-btn" data-category="document">Document</button>
    </div>

    <button class="upload-btn" id="uploadBtn">
        <i class="fas fa-plus"></i> Upload Item
    </button>

    <section id="items-display" class="items-grid">
        <!-- Items will be dynamically loaded here -->
    </section>

    <!-- Upload Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Upload Lost/Found Item</h2>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="itemCategory">Category:</label>
                    <select id="itemCategory" name="category" required>
                        <option value="electronic">Electronic</option>
                        <option value="card">Card</option>
                        <option value="cloth">Cloth</option>
                        <option value="stationary">Stationary</option>
                        <option value="keys">Keys</option>
                        <option value="document">Document</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="itemName">Item Name:</label>
                    <input type="text" id="itemName" name="itemName" required placeholder="Enter item name">
                </div>

                <div class="form-group">
                    <label for="itemDescription">Description:</label>
                    <textarea id="itemDescription" name="description" required placeholder="Describe the item..."></textarea>
                </div>

                <div class="form-group">
                    <label for="itemLocation">Location:</label>
                    <input type="text" id="itemLocation" name="location" required placeholder="Where was it found/lost?">
                </div>

                <div class="form-group">
                    <label for="itemDate">Date:</label>
                    <input type="date" id="itemDate" name="dateFound" required>
                </div>

                <div class="form-group">
                    <label for="itemImage">Upload Image:</label>
                    <input type="file" id="itemImage" name="image" accept="image/*" required>
                    <div id="imagePreview" class="image-preview"></div>
                </div>

                <input type="hidden" name="found_by_id" value="<?php echo $currentUser['User_ID']; ?>">
                
                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
    </div>
</div>

<script>
// Add form submission handler for upload
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../api/upload_item.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Item uploaded successfully!');
            document.getElementById('uploadModal').style.display = 'none';
            // Reload items
            loadItems();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while uploading the item.');
    });
});

// Function to load items from database
function loadItems(category = 'all') {
    fetch(`../api/get_items.php?category=${category}`)
    .then(response => response.json())
    .then(data => {
        const itemsDisplay = document.getElementById('items-display');
        itemsDisplay.innerHTML = '';
        
        if (data.success && data.items.length > 0) {
            data.items.forEach(item => {
                const itemElement = createItemElement(item);
                itemsDisplay.appendChild(itemElement);
            });
        } else {
            itemsDisplay.innerHTML = '<p>No items found.</p>';
        }
    })
    .catch(error => {
        console.error('Error loading items:', error);
    });
}

// Function to create item element
function createItemElement(item) {
    const div = document.createElement('div');
    div.className = 'item-card';
    div.innerHTML = `
        <img src="${item.Image_url || '../images/default-item.jpg'}" alt="${item.Item_Name}">
        <div class="item-info">
            <h3>${item.Item_Name}</h3>
            <p><strong>Category:</strong> ${item.category || 'Unknown'}</p>
            <p><strong>Description:</strong> ${item.Description}</p>
            <p><strong>Location:</strong> ${item.Location}</p>
            <p><strong>Date Found:</strong> ${item.Date_found}</p>
            <p><strong>Status:</strong> ${item.Is_claimed ? 'Claimed' : 'Available'}</p>
            ${!item.Is_claimed ? `<button onclick="claimItem(${item.Item_ID})" class="claim-btn">Claim Item</button>` : ''}
        </div>
    `;
    return div;
}

// Function to claim item
function claimItem(itemId) {
    if (confirm('Are you sure you want to claim this item?')) {
        fetch('../api/claim_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                item_id: itemId,
                claim_message: 'I would like to claim this item.'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Claim submitted successfully!');
                loadItems();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while claiming the item.');
        });
    }
}

// Load items when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadItems();
    
    // Add category button event listeners
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const category = this.getAttribute('data-category');
            loadItems(category);
        });
    });
});
</script> 