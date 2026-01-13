<?php
/**
 * KV Wood Works - Contact Us
 */

require_once __DIR__ . '/config/database.php';

$pageTitle = 'Contact Us - Get FREE Interior Design Quote';
$pageDescription = 'Contact KV Wood Works Chennai for home interior & wooden works. Visit our Maduravoyal office, call +91-98849 72483, or request FREE consultation. Fast response guaranteed!';
$pageKeywords = 'contact kv wood works, interior designers near me, home interior chennai contact, modular kitchen quote, wooden works enquiry, free home design consultation';

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $msgContent = sanitize($_POST['message'] ?? '');

    if ($name && $email && $msgContent) {
        // 1. Try Database Insert (Non-Fatal)
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $msgContent]);
            file_put_contents('d:/website/KVwoodworks/trace_log.txt', date('H:i:s') . " - Contact DB Success\n", FILE_APPEND);
        } catch (Exception $e) {
            file_put_contents('d:/website/KVwoodworks/trace_log.txt', date('H:i:s') . " - Contact DB ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        }

        // 2. Send Telegram Notification (Always runs)
        $msg = "🔔 <b>New Website Inquiry</b>\n\n";
        $msg .= "👤 <b>Name:</b> " . htmlspecialchars($name) . "\n";
        $msg .= "📱 <b>Phone:</b> " . htmlspecialchars($phone) . "\n";
        $msg .= "📧 <b>Email:</b> " . htmlspecialchars($email) . "\n";
        $msg .= "📝 <b>Subject:</b> " . htmlspecialchars($subject) . "\n";
        $msg .= "💬 <b>Message:</b> " . htmlspecialchars($msgContent);

        $tgResult = sendTelegram($msg);
        file_put_contents('d:/website/KVwoodworks/trace_log.txt', date('H:i:s') . " - Contact Telegram: " . ($tgResult ? 'OK' : 'FAIL') . "\n", FILE_APPEND);

        $message = 'Thank you for contacting us! We will get back to you soon.';
        $messageType = 'success';
    } else {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    }
}

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you. Get in touch with our team.</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <span>Contact Us</span>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Info -->
            <div class="contact-info-cards">
                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h4>Visit Our Office</h4>
                        <p><?php echo SITE_ADDRESS; ?></p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <h4>Call Us</h4>
                        <p><a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a></p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h4>Email Us</h4>
                        <p><a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a></p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h4>Working Hours</h4>
                        <p>Monday - Saturday: 9:00 AM - 7:00 PM<br>Sunday: Closed</p>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-card">
                <h3>Send Us a Message</h3>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" data-validate>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Full Name <span>*</span></label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Your name"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span>*</span></label>
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="your@email.com" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control"
                                placeholder="Your phone number">
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" name="subject" class="form-control">
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="quote">Request Quote</option>
                                <option value="support">Support</option>
                                <option value="feedback">Feedback</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message">Your Message <span>*</span></label>
                        <textarea id="message" name="message" class="form-control" placeholder="How can we help you?"
                            required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="section section-light">
    <div class="container">
        <div class="section-header">
            <h2>Find Us on Map</h2>
            <p>Visit our office for a personal consultation</p>
        </div>

        <div style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4610.374806790385!2d80.16064607572403!3d13.055863913023925!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3698328b93cc11%3A0x5bbb7b8b2b896b87!2sKV%20Wood%20Works!5e1!3m2!1sen!2sin!4v1768154166027!5m2!1sen!2sin"
                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        <div style="text-align: center; margin-top: 25px;">
            <a href="https://maps.app.goo.gl/BdaibN2Q3pbT8E1A9" target="_blank" rel="noopener"
                class="btn btn-primary btn-lg">
                <i class="fas fa-map-marker-alt"></i> Open in Google Maps
            </a>
            <a href="https://maps.app.goo.gl/BdaibN2Q3pbT8E1A9" target="_blank" rel="noopener"
                class="btn btn-secondary btn-lg" style="margin-left: 10px;">
                <i class="fas fa-directions"></i> Get Directions
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>