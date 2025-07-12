function initHelpPage() {
    // FAQ dropdown toggle
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = btn.parentElement;
            item.classList.toggle('open');
        });
    });

    // Ask question search button
    document.getElementById('searchBtn').addEventListener('click', function() {
        const question = document.getElementById('questionInput').value.trim().toLowerCase();
        const faqItems = document.querySelectorAll('.faq-item');
        let found = false;
        if (question) {
            faqItems.forEach(item => {
                const q = item.querySelector('.faq-question').textContent.toLowerCase();
                const a = item.querySelector('.faq-answer').textContent.toLowerCase();
                if (q.includes(question) || a.includes(question)) {
                    item.style.display = '';
                    found = true;
                } else {
                    item.style.display = 'none';
                }
                item.classList.remove('open');
            });
            // Show message if nothing found
            let noResult = document.getElementById('no-faq-result');
            if (!found) {
                if (!noResult) {
                    noResult = document.createElement('div');
                    noResult.id = 'no-faq-result';
                    noResult.style.padding = '1rem';
                    noResult.style.color = '#c00';
                    noResult.textContent = 'No relevant FAQ found.';
                    document.querySelector('.faq-list').appendChild(noResult);
                }
            } else if (noResult) {
                noResult.remove();
            }
        } else {
            // Show all if input is empty
            faqItems.forEach(item => {
                item.style.display = '';
                item.classList.remove('open');
            });
            let noResult = document.getElementById('no-faq-result');
            if (noResult) noResult.remove();
        }
    });
} 