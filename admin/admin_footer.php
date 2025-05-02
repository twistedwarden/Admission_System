</main>
        </div>
    </div>
    
    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const closeSidebar = document.getElementById('close-sidebar');
        
        if (mobileMenuButton && mobileSidebar && closeSidebar) {
            mobileMenuButton.addEventListener('click', () => {
                mobileSidebar.classList.toggle('hidden');
            });
            
            closeSidebar.addEventListener('click', () => {
                mobileSidebar.classList.add('hidden');
            });
        }
    </script>
</body>
</html>