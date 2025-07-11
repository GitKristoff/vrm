import './bootstrap';
import Alpine from 'alpinejs';

// Initialize Alpine store BEFORE starting Alpine
document.addEventListener('alpine:init', () => {
    // Create global sidebar state
    Alpine.store('sidebar', {
        expanded: window.innerWidth >= 1024,
        
        toggle() {
            this.expanded = !this.expanded;
        }
    });
});

// Now start Alpine
window.Alpine = Alpine;
Alpine.start();

// Update state on window resize
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        Alpine.store('sidebar').expanded = true;
    } else {
        Alpine.store('sidebar').expanded = false;
    }
});