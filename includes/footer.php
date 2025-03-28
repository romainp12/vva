</main>
<footer>
    <p>&copy; <?php echo date('Y'); ?> VVA</p>
</footer>

<!-- Script JavaScript -->
<script>
    // Toggle Menu Mobile
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('show');
        });
    }
    
    // Gestion du mode sombre
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    const themeIcon = themeToggle.querySelector('i');
    
    // Vérifier les préférences enregistrées
    if (localStorage.getItem('darkMode') === 'enabled') {
        enableDarkMode();
    }
    
    themeToggle.addEventListener('click', (e) => {
        e.preventDefault();
        
        if (body.classList.contains('dark-mode')) {
            disableDarkMode();
        } else {
            enableDarkMode();
        }
    });
    
    function enableDarkMode() {
        body.classList.add('dark-mode');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        localStorage.setItem('darkMode', 'enabled');
    }
    
    function disableDarkMode() {
        body.classList.remove('dark-mode');
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        localStorage.setItem('darkMode', 'disabled');
    }
    
    // Animation de chargement des éléments
    document.addEventListener('DOMContentLoaded', () => {
        const cardElements = document.querySelectorAll('.card, .animations-list li, .activity-item');
        
        cardElements.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('fade-in');
            }, index * 100);
        });
    });
</script>
</body>
</html>