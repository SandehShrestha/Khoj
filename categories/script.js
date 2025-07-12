function initCategoriesPage() {
    const categoryButtons = document.querySelectorAll('.category-btn');
    const itemsDisplay = document.getElementById('items-display');

    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelector('.category-btn.active').classList.remove('active');
            btn.classList.add('active');
            const cat = btn.getAttribute('data-category');
            let message = '';
            if (cat === 'all') {
                message = '<p style="padding:2rem;">All items will be shown here.</p>';
            } else {
                message = `<p style=\"padding:2rem;\">Items for <strong>${btn.textContent}</strong> will be shown here.</p>`;
            }
            itemsDisplay.innerHTML = message;
        });
    });

    // Initial placeholder
    itemsDisplay.innerHTML = '<p style="padding:2rem;">All items will be shown here.</p>';

    // Upload Modal Functionality
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadModal = document.getElementById('uploadModal');
    const closeModal = document.querySelector('.close-modal');
    if (uploadBtn && uploadModal && closeModal) {
        uploadBtn.addEventListener('click', () => {
            uploadModal.style.display = 'block';
        });
        closeModal.addEventListener('click', () => {
            uploadModal.style.display = 'none';
        });
        window.addEventListener('click', (e) => {
            if (e.target === uploadModal) uploadModal.style.display = 'none';
        });
    }

    // --- Item Fetching and Rendering ---
    let lastFetchedItems = [];
    let lastCategory = 'all';

    function renderItems(items) {
        const itemsDisplay = document.getElementById('items-display');
        if (!items || items.length === 0) {
            itemsDisplay.innerHTML = '<p style="padding:2rem;">No items found.</p>';
            return;
        }
        itemsDisplay.innerHTML = '';
        items.forEach(item => {
            const itemCard = document.createElement('div');
            itemCard.className = 'item-card facebook-style';
            itemCard.innerHTML = `
                <div class="item-header-row">
                    <div class="item-profile-block">
                        <div class="profile-pic-circle"><i class='fa fa-user'></i></div>
                        <div class="profile-details">
                            <span class="item-username">${item.found_by_name || 'Unknown'}</span>
                            <span class="item-phone">${item.uploader_phone || 'N/A'}</span>
                            <span class="item-date">${item.Created_at ? new Date(item.Created_at).toLocaleString() : ''}</span>
                        </div>
                    </div>
                    <div class="item-meta">
                        <span class="item-status ${item.Is_claimed ? 'claimed' : 'unclaimed'}">${item.Is_claimed ? 'Claimed' : 'Unclaimed'}</span>
                    </div>
                </div>
                <div class="item-title">${item.Item_Name}</div>
                <div class="item-description full-width">${item.Description}</div>
                <div class="item-image"><img src="${item.Image_url ? item.Image_url : './images/login-page-bg.jpg'}" alt="Item Image" onerror="this.src='./images/login-page-bg.jpg'"></div>
            `;
            itemsDisplay.appendChild(itemCard);
        });
    }

    async function fetchAndDisplayItems(category = 'all') {
        const itemsDisplay = document.getElementById('items-display');
        itemsDisplay.innerHTML = '<p style="padding:2rem;">Loading items...</p>';
        try {
            const response = await fetch(`./api/get_items.php?category=${encodeURIComponent(category)}`);
            const result = await response.json();
            if (result.success && Array.isArray(result.items)) {
                lastFetchedItems = result.items;
                lastCategory = category;
                renderItems(lastFetchedItems);
            } else {
                itemsDisplay.innerHTML = '<p style="padding:2rem;">Failed to load items.</p>';
            }
        } catch (error) {
            itemsDisplay.innerHTML = '<p style="padding:2rem;">Error loading items.</p>';
        }
    }

    // Search bar functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = searchInput.value.trim().toLowerCase();
            if (!query) {
                renderItems(lastFetchedItems);
                return;
            }
            const filtered = lastFetchedItems.filter(item =>
                (item.Item_Name && item.Item_Name.toLowerCase().includes(query)) ||
                (item.Description && item.Description.toLowerCase().includes(query))
            );
            renderItems(filtered);
        });
    }

    // Fetch items on page load (default: all)
    fetchAndDisplayItems('all');

    // Fetch items on category button click (use already declared categoryButtons)
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const cat = btn.getAttribute('data-category');
            fetchAndDisplayItems(cat);
            if (searchInput) searchInput.value = '';
        });
    });

    // After successful upload, refresh items (use already declared uploadForm)
    if (uploadForm) {
        uploadForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(uploadForm);
            try {
                const response = await fetch('./api/upload_item.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    alert('Item uploaded successfully!');
                    document.getElementById('uploadModal').style.display = 'none';
                    uploadForm.reset();
                    // Refresh items after upload
                    const activeBtn = document.querySelector('.category-btn.active');
                    const activeCat = activeBtn ? activeBtn.getAttribute('data-category') : 'all';
                    fetchAndDisplayItems(activeCat);
                    if (searchInput) searchInput.value = '';
                } else {
                    alert(result.message || 'Failed to upload item.');
                }
            } catch (error) {
                alert('An error occurred while uploading.');
            }
        });
    }
}
