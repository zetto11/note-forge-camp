<?php
/**
 * StudyMate Footer Template
 */
?>

    <?php if (isLoggedIn()): ?>
            </div><!-- .content-container -->
        </main><!-- .main-content -->
    </div><!-- .app-layout -->
    <?php else: ?>
        </main><!-- .guest-main -->
        
        <footer class="guest-footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-brand">
                        <span class="logo-icon">📚</span>
                        <span class="logo-text"><?= APP_NAME ?></span>
                        <p class="footer-tagline">Your Professional Learning Companion</p>
                    </div>
                    
                    <div class="footer-links">
                        <div class="footer-column">
                            <h4>Product</h4>
                            <a href="#">Features</a>
                            <a href="#">Pricing</a>
                            <a href="#">FAQ</a>
                        </div>
                        <div class="footer-column">
                            <h4>Resources</h4>
                            <a href="#">Documentation</a>
                            <a href="#">Blog</a>
                            <a href="#">Support</a>
                        </div>
                        <div class="footer-column">
                            <h4>Legal</h4>
                            <a href="#">Privacy Policy</a>
                            <a href="#">Terms of Service</a>
                            <a href="#">Cookie Policy</a>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
                    <div class="footer-social">
                        <a href="#" aria-label="Twitter"><i data-feather="twitter"></i></a>
                        <a href="#" aria-label="GitHub"><i data-feather="github"></i></a>
                        <a href="#" aria-label="LinkedIn"><i data-feather="linkedin"></i></a>
                    </div>
                </div>
            </div>
        </footer>
    </div><!-- .guest-layout -->
    <?php endif; ?>
    
    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>
    
    <!-- Main JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <!-- Initialize Feather Icons -->
    <script>
        feather.replace();
    </script>
</body>
</html>
