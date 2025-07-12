class Router {
    constructor(routes) {
        this.routes = routes;
        this.mainContent = document.getElementById('main-content');
        this.currentPage = null;

        // Handle initial route
        this.handleRoute();

        // Handle browser back/forward
        window.addEventListener('popstate', () => this.handleRoute());

        // Handle link clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('a[href^="#/"]')) {
                e.preventDefault();
                const path = e.target.getAttribute('href').slice(2);
                this.navigate(path);
            }
        });
    }

    async handleRoute() {
        const path = window.location.hash.slice(2) || 'categories';
        const route = this.routes[path] || this.routes['404'];

        try {
            // Update active state in navigation
            document.querySelectorAll('.sidebar nav a').forEach(link => {
                link.classList.toggle('active', link.getAttribute('href') === `#/${path}`);
            });

            // Load page content
            const response = await fetch(route.template);
            const content = await response.text();
            
            // Update main content
            this.mainContent.innerHTML = content;

            // Initialize page scripts
            if (route.script) {
                const script = document.createElement('script');
                script.src = route.script;
                document.body.appendChild(script);
            }

            this.currentPage = path;
        } catch (error) {
            console.error('Error loading page:', error);
            this.mainContent.innerHTML = '<h1>Error loading page</h1>';
        }
    }

    navigate(path) {
        window.location.hash = path;
    }
}

// Define routes
const routes = {
    'categories': {
        template: '/categories/content.html',
        script: '/categories/script.js'
    },
    'messages': {
        template: '/messages/content.html',
        script: '/messages/script.js'
    },
    'account': {
        template: '/account/content.html',
        script: '/account/script.js'
    },
    'help': {
        template: '/help/content.html',
        script: '/help/script.js'
    },
    '404': {
        template: '/404.html'
    }
};

// Initialize router
const router = new Router(routes);