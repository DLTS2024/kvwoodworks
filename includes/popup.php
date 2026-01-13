<?php
/**
 * Consultation Popup Component
 * Include this file in footer.php to show popup on all pages
 */

// Load popup settings
$popupSettingsFile = __DIR__ . '/../config/popup_settings.json';
$popupSettings = [
    'enabled' => true,
    'title' => 'Get a FREE Design Consultation',
    'offer_text' => 'New Year Special Offer',
    'discount' => '25% OFF',
    'discount_label' => 'On Modular Interiors',
    'offer_expiry' => 'Hurry, Book Before 31st January 2026',
    'show_after_seconds' => 5,
    'image' => ''
];

if (file_exists($popupSettingsFile)) {
    $popupSettings = json_decode(file_get_contents($popupSettingsFile), true);
}

// Only show if enabled
if (!$popupSettings['enabled'])
    return;
?>

<!-- Consultation Popup -->
<div class="consultation-popup" id="consultationPopup">
    <div class="popup-overlay" onclick="closePopup()"></div>
    <div class="popup-container">
        <button class="popup-close" onclick="closePopup()">&times;</button>

        <div class="popup-content">
            <!-- Left Side - Offer Banner -->
            <div class="popup-offer">
                <?php if (!empty($popupSettings['image'])): ?>
                    <img src="<?php echo baseUrl($popupSettings['image']); ?>" alt="Offer" class="popup-banner-img">
                <?php else: ?>
                    <div class="offer-content">
                        <div class="offer-badge">
                            <span class="offer-year">2026</span>
                            <span class="welcome-text">WELCOME</span>
                        </div>
                        <p class="offer-text">
                            <?php echo $popupSettings['offer_text']; ?>
                        </p>
                        <div class="discount-box">
                            <span class="flat-text">FLAT</span>
                            <span class="discount-percent">
                                <?php echo $popupSettings['discount']; ?>
                            </span>
                        </div>
                        <p class="discount-label">
                            <?php echo $popupSettings['discount_label']; ?>
                        </p>
                        <p class="offer-expiry">
                            <?php echo $popupSettings['offer_expiry']; ?>
                        </p>
                        <div class="offer-features">
                            <span><i class="fas fa-check-circle"></i> Personalized Designs</span>
                            <span><i class="fas fa-check-circle"></i> Zero Interest EMIs</span>
                            <span><i class="fas fa-check-circle"></i> 5-Year Warranty</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Side - Form -->
            <div class="popup-form">
                <h3>
                    <?php echo $popupSettings['title']; ?>
                </h3>

                <form id="popupConsultationForm">
                    <div class="popup-form-group">
                        <label>Property Type</label>
                        <div class="property-types">
                            <label class="property-type-option">
                                <input type="radio" name="property_type" value="1 BHK" checked>
                                <span>1 BHK</span>
                            </label>
                            <label class="property-type-option">
                                <input type="radio" name="property_type" value="2 BHK">
                                <span>2 BHK</span>
                            </label>
                            <label class="property-type-option">
                                <input type="radio" name="property_type" value="3 BHK">
                                <span>3 BHK</span>
                            </label>
                            <label class="property-type-option">
                                <input type="radio" name="property_type" value="4+ BHK">
                                <span>4+ BHK / Duplex</span>
                            </label>
                        </div>
                    </div>

                    <div class="popup-form-group">
                        <label>Property Location</label>
                        <select name="location" class="popup-form-control" required>
                            <option value="">Select your area</option>
                            <option value="Maduravoyal">Maduravoyal</option>
                            <option value="Ambattur">Ambattur</option>
                            <option value="Porur">Porur</option>
                            <option value="Velachery">Velachery</option>
                            <option value="Anna Nagar">Anna Nagar</option>
                            <option value="T Nagar">T Nagar</option>
                            <option value="Adyar">Adyar</option>
                            <option value="OMR">OMR</option>
                            <option value="Other">Other Chennai Areas</option>
                        </select>
                    </div>

                    <div class="popup-form-group">
                        <label>Your Name</label>
                        <input type="text" name="name" class="popup-form-control" placeholder="Enter your name"
                            required>
                    </div>

                    <div class="popup-form-group">
                        <label>Mobile Number</label>
                        <div class="phone-input">
                            <span class="country-code">+91</span>
                            <input type="tel" name="phone" class="popup-form-control" placeholder="Mobile Number"
                                required pattern="[0-9]{10}">
                        </div>
                    </div>

                    <div class="popup-form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="whatsapp_updates" checked>
                            Yes, send me updates via WhatsApp <i class="fab fa-whatsapp" style="color: #25D366;"></i>
                        </label>
                    </div>

                    <button type="submit" class="popup-submit-btn">
                        Book a Free Consultation
                    </button>

                    <p class="popup-terms">By submitting, you consent to our <a href="#">privacy policy</a> and <a
                            href="#">terms of use</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .consultation-popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 99999;
        align-items: center;
        justify-content: center;
    }

    .consultation-popup.active {
        display: flex;
    }

    .popup-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
    }

    .popup-container {
        position: relative;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        max-width: 850px;
        width: 95%;
        max-height: 90vh;
        overflow-y: auto;
        animation: popupSlideIn 0.4s ease;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    @keyframes popupSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .popup-close {
        position: absolute;
        top: 10px;
        right: 15px;
        background: none;
        border: none;
        font-size: 2rem;
        color: #666;
        cursor: pointer;
        z-index: 10;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .popup-close:hover {
        background: #f0f0f0;
        color: #000;
    }

    .popup-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    /* Left Side - Offer */
    .popup-offer {
        background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
        color: white;
        padding: 40px 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .popup-banner-img {
        max-width: 100%;
        border-radius: 10px;
    }

    .offer-content {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .offer-badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
    }

    .offer-year {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ffd700, #ff8c00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .welcome-text {
        font-size: 0.9rem;
        letter-spacing: 5px;
        opacity: 0.8;
    }

    .offer-text {
        color: #4ade80;
        font-size: 1rem;
        margin-bottom: 15px;
    }

    .discount-box {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        padding: 15px 30px;
        border-radius: 10px;
        margin: 10px 0;
    }

    .flat-text {
        display: block;
        font-size: 0.9rem;
    }

    .discount-percent {
        font-size: 2.5rem;
        font-weight: 800;
    }

    .discount-label {
        margin-top: 10px;
        font-size: 1.1rem;
    }

    .offer-expiry {
        margin-top: 15px;
        font-size: 0.85rem;
        opacity: 0.7;
    }

    .offer-features {
        margin-top: 25px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .offer-features span {
        background: rgba(255, 255, 255, 0.1);
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .offer-features i {
        color: #4ade80;
    }

    /* Right Side - Form */
    .popup-form {
        padding: 35px 30px;
    }

    .popup-form h3 {
        font-size: 1.5rem;
        margin-bottom: 25px;
        color: #1a1a1a;
    }

    .popup-form-group {
        margin-bottom: 18px;
    }

    .popup-form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 0.9rem;
        color: #333;
    }

    .property-types {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .property-type-option {
        cursor: pointer;
    }

    .property-type-option input {
        display: none;
    }

    .property-type-option span {
        display: inline-block;
        padding: 8px 15px;
        border: 2px solid #e5e5e5;
        border-radius: 25px;
        font-size: 0.85rem;
        transition: all 0.3s;
    }

    .property-type-option input:checked+span {
        background: #1a1a1a;
        color: white;
        border-color: #1a1a1a;
    }

    .popup-form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e5e5e5;
        border-radius: 8px;
        font-size: 1rem;
        font-family: inherit;
    }

    .popup-form-control:focus {
        outline: none;
        border-color: var(--primary);
    }

    .phone-input {
        display: flex;
        align-items: center;
    }

    .country-code {
        padding: 12px 15px;
        background: #f5f5f5;
        border: 2px solid #e5e5e5;
        border-right: none;
        border-radius: 8px 0 0 8px;
        color: #666;
    }

    .phone-input .popup-form-control {
        border-radius: 0 8px 8px 0;
    }

    .checkbox-label {
        display: flex !important;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .checkbox-label input {
        width: 18px;
        height: 18px;
        accent-color: var(--primary);
    }

    .popup-submit-btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #dc2626, #ef4444);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
        font-family: inherit;
    }

    .popup-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(220, 38, 38, 0.3);
    }

    .popup-terms {
        text-align: center;
        font-size: 0.8rem;
        color: #666;
        margin-top: 15px;
    }

    .popup-terms a {
        color: var(--primary);
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .popup-content {
            grid-template-columns: 1fr;
        }

        .popup-offer {
            padding: 25px 20px;
        }

        .offer-year {
            font-size: 2rem;
        }

        .discount-percent {
            font-size: 2rem;
        }

        .popup-form {
            padding: 25px 20px;
        }
    }
</style>

<script>
    // Popup control
    function closePopup() {
        document.getElementById('consultationPopup').classList.remove('active');
        // Store current page as shown so popup doesn't repeat on same page refresh
        sessionStorage.setItem('popupShownOn', window.location.pathname);
    }

    function openPopup() {
        // Only show if not already shown on THIS specific page in this session
        const shownOnPage = sessionStorage.getItem('popupShownOn');
        const currentPage = window.location.pathname;

        // Show popup if it's a different page or first time
        if (shownOnPage !== currentPage) {
            document.getElementById('consultationPopup').classList.add('active');
        }
    }

    // Show popup after delay or scroll
    document.addEventListener('DOMContentLoaded', function () {
        const showAfterSeconds = <?php echo $popupSettings['show_after_seconds']; ?>;
        const showOnScrollPercent = <?php echo $popupSettings['show_on_scroll_percent'] ?? 50; ?>;

        // Show after timeout
        if (showAfterSeconds > 0) {
            setTimeout(openPopup, showAfterSeconds * 1000);
        }

        // Or show on scroll
        if (showOnScrollPercent > 0) {
            let scrollTriggered = false;
            window.addEventListener('scroll', function () {
                if (scrollTriggered) return;
                const scrollPercent = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
                if (scrollPercent >= showOnScrollPercent) {
                    scrollTriggered = true;
                    openPopup();
                }
            });
        }
    });

    // Handle form submission
    document.getElementById('popupConsultationForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Sending...';
        btn.disabled = true;

        const formData = new FormData(this);

        fetch('submit-popup.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Thank you! We will contact you shortly.');
                    closePopup();
                    this.reset();
                } else {
                    alert('Something went wrong. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Connection error. Please try again.');
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    });

    // Close on ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePopup();
    });
</script>