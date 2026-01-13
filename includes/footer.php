</main>
<!-- End Main Content -->

<?php
// Show footer banner (if banners.php was included in header)
if (function_exists('show_banner')) {
    show_banner('footer');
}
?>

<!-- Footer -->
<footer class="footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div class="footer-col">
                    <a href="<?php echo SITE_URL; ?>" class="footer-logo">
                        <img src="<?php echo baseUrl('assets/images/logos/kv-woodworks-logo.png'); ?>"
                            alt="KV Wood Works Logo" class="footer-logo-img">
                    </a>
                    <p class="footer-about">
                        Premium home interiors and wooden works crafted with passion. Transform your space with our
                        expert designs and quality craftsmanship.
                    </p>
                    <div class="footer-social">
                        <a href="<?php echo SITE_INSTAGRAM; ?>" target="_blank" aria-label="Instagram"><i
                                class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/<?php echo SITE_WHATSAPP; ?>" target="_blank" aria-label="WhatsApp"><i
                                class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Home Interiors -->
                <div class="footer-col">
                    <h4>Home Interiors</h4>
                    <ul>
                        <li><a href="<?php echo baseUrl('pages/interior-design/modular-kitchen.php'); ?>">Modular
                                Kitchen</a></li>
                        <li><a href="<?php echo baseUrl('pages/interior-design/wardrobe.php'); ?>">Wardrobe Designs</a>
                        </li>
                        <li><a href="<?php echo baseUrl('pages/interior-design/bedroom.php'); ?>">Bedroom</a></li>
                        <li><a href="<?php echo baseUrl('pages/interior-design/living-room.php'); ?>">Living Room</a>
                        </li>
                        <li><a href="<?php echo baseUrl('pages/interior-design/space-saving.php'); ?>">Space Saving</a>
                        </li>
                        <li><a href="<?php echo baseUrl('pages/bhk/2bhk.php'); ?>">2 BHK Interior</a></li>
                    </ul>
                </div>

                <!-- Wooden Works -->
                <div class="footer-col">
                    <h4>Wooden Works</h4>
                    <ul>
                        <li><a href="<?php echo baseUrl('pages/wooden-works/vasakal.php'); ?>">Vasakal</a></li>
                        <li><a href="<?php echo baseUrl('pages/wooden-works/window-janal.php'); ?>">Window / Janal</a>
                        </li>
                        <li><a href="<?php echo baseUrl('pages/wooden-works/wooden-staircase.php'); ?>">Wooden
                                Staircase</a></li>
                    </ul>
                    <h4 style="margin-top: 1.5rem;">Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo baseUrl('about.php'); ?>">About Us</a></li>
                        <li><a href="<?php echo baseUrl('recent-projects.php'); ?>">Our Projects</a></li>
                        <li><a href="<?php echo baseUrl('reviews.php'); ?>">Reviews</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-col">
                    <h4>Contact Us</h4>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo SITE_ADDRESS; ?></span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?php echo SITE_PHONE; ?>">
                                <?php echo SITE_PHONE; ?>
                            </a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo SITE_EMAIL; ?>">
                                <?php echo SITE_EMAIL; ?>
                            </a>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Mon - Sat: 9:00 AM - 7:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <p>&copy;
                    <?php echo date('Y'); ?>
                    <?php echo SITE_NAME; ?>. All Rights Reserved.
                </p>
                <div class="footer-bottom-links">
                    <a href="<?php echo baseUrl('faqs.php'); ?>">FAQs</a>
                    <a href="<?php echo baseUrl('contact.php'); ?>">Contact</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Fixed Bottom Navigation Bar -->
<div class="bottom-nav" id="bottomNav">
    <div class="bottom-nav-item" id="designsNav">
        <i class="fas fa-palette"></i>
        <span>Designs</span>
        <!-- Dropdown Menu -->
        <div class="bottom-nav-dropdown" id="designsDropdown">
            <a href="<?php echo baseUrl('home-interior-designs.php'); ?>">
                <i class="fas fa-home"></i> Interior Designs
            </a>
            <a href="<?php echo baseUrl('wooden-works.php'); ?>">
                <i class="fas fa-tree"></i> Wooden Works
            </a>
        </div>
    </div>
    <a href="<?php echo baseUrl('gallery.php'); ?>" class="bottom-nav-item">
        <i class="fas fa-images"></i>
        <span>Gallery</span>
    </a>
    <a href="https://wa.me/<?php echo SITE_WHATSAPP; ?>" target="_blank" class="bottom-nav-item whatsapp-nav">
        <i class="fab fa-whatsapp"></i>
        <span>WhatsApp</span>
    </a>
    <a href="<?php echo baseUrl('get-estimate.php'); ?>" class="bottom-nav-item estimate-nav">
        <i class="fas fa-file-invoice-dollar"></i>
        <span>Free Quote</span>
    </a>
</div>

<style>
    /* Fixed Bottom Navigation */
    .bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #1a3a4a 0%, #0d2535 100%);
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 8px 0;
        z-index: 9998;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
    }

    .bottom-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: white;
        font-size: 0.7rem;
        padding: 8px 15px;
        border-radius: 8px;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
    }

    .bottom-nav-item i {
        font-size: 1.3rem;
        margin-bottom: 4px;
    }

    .bottom-nav-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .bottom-nav-item.whatsapp-nav {
        color: #25D366;
    }

    .bottom-nav-item.whatsapp-nav:hover {
        background: rgba(37, 211, 102, 0.2);
    }

    .bottom-nav-item.estimate-nav {
        background: var(--primary, #c8956c);
        border-radius: 8px;
    }

    .bottom-nav-item.estimate-nav:hover {
        background: #a67b5b;
    }

    /* Dropdown for Designs */
    .bottom-nav-dropdown {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        border-radius: 12px;
        box-shadow: 0 -5px 30px rgba(0, 0, 0, 0.2);
        padding: 10px 0;
        min-width: 180px;
        display: none;
        margin-bottom: 10px;
    }

    .bottom-nav-dropdown::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 8px solid white;
    }

    .bottom-nav-dropdown a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        font-size: 0.9rem;
        transition: background 0.3s;
    }

    .bottom-nav-dropdown a:hover {
        background: #f5f5f5;
        color: var(--primary, #c8956c);
    }

    .bottom-nav-dropdown a i {
        font-size: 1.1rem;
        color: var(--primary, #c8956c);
        width: 20px;
    }

    .bottom-nav-dropdown.show {
        display: block;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }

    /* Add padding to body to account for bottom nav */
    body {
        padding-bottom: 70px;
    }

    /* Hide WhatsApp float button on mobile since it's in bottom nav */
    .whatsapp-float {
        display: none !important;
    }

    /* Adjust back to top button position on mobile */
    .back-to-top {
        bottom: 80px !important;
    }

    /* Hide bottom nav on desktop/laptop (992px and above) */
    @media (min-width: 992px) {
        .bottom-nav {
            display: none !important;
        }

        /* Restore WhatsApp float button on desktop */
        .whatsapp-float {
            display: flex !important;
        }

        /* Reset body padding on desktop */
        body {
            padding-bottom: 0 !important;
        }

        /* Reset back to top button position on desktop */
        .back-to-top {
            bottom: 30px !important;
        }
    }
</style>

<script>
    // Toggle Designs dropdown
    document.getElementById('designsNav').addEventListener('cl ick', function (e) {
        e.stopPropagation();
        const dropdown = document.getElementById('designsDropdown');
        dropdown.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('c lick', function (e) {
        const dropdown = document.getElementById('designsDropdown');
        if (!e.target.closest('#designsNav')) {
            dropdown.classList.remove('show');
        }
    });
</script>

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Consultation Popup -->
<?php include __DIR__ . '/popup.php'; ?>

<!-- Custom JavaScript -->
<script src="<?php echo baseUrl('assets/js/main.js'); ?>"></script>
</body>

</html>