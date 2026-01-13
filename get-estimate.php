<?php
/**
 * KV Wood Works - Get Free Estimate
 */

require_once __DIR__ . '/config/database.php';

$pageTitle = 'Get Free Estimate';
$pageDescription = 'Get a free estimate for your home interior or wooden works project. Fill the form and our team will contact you shortly.';

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $propertyType = sanitize($_POST['property_type'] ?? '');
    $projectType = sanitize($_POST['project_type'] ?? '');
    $budgetRange = sanitize($_POST['budget_range'] ?? '');
    $msgContent = sanitize($_POST['message'] ?? '');

    if ($name && $email && $phone) {
        // 1. Try Database Insert (Non-Fatal)
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO estimate_requests (name, email, phone, city, property_type, project_type, budget_range, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $city, $propertyType, $projectType, $budgetRange, $msgContent]);
            file_put_contents('d:/website/KVwoodworks/trace_log.txt', date('H:i:s') . " - Estimate DB Success\n", FILE_APPEND);
        } catch (Exception $e) {
            file_put_contents('d:/website/KVwoodworks/trace_log.txt', date('H:i:s') . " - Estimate DB ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        }

        // 2. Send Telegram Notification (Always runs)
        $msg = "💰 <b>New Estimate Request</b>\n\n";
        $msg .= "👤 <b>Name:</b> " . htmlspecialchars($name) . "\n";
        $msg .= "📱 <b>Phone:</b> " . htmlspecialchars($phone) . "\n";
        $msg .= "📧 <b>Email:</b> " . htmlspecialchars($email) . "\n";
        $msg .= "📍 <b>City:</b> " . htmlspecialchars($city) . "\n";
        $msg .= "🏠 <b>Property:</b> " . htmlspecialchars($propertyType) . "\n";
        $msg .= "🛠 <b>Project:</b> " . htmlspecialchars($projectType) . "\n";
        $msg .= "💵 <b>Budget:</b> " . htmlspecialchars($budgetRange) . "\n";
        $msg .= "💬 <b>Message:</b> " . htmlspecialchars($msgContent);

        $tgResult = sendTelegram($msg);
        file_put_contents('d:/website/KVwoodworks/trace_log.txt', date('H:i:s') . " - Estimate Telegram: " . ($tgResult ? 'OK' : 'FAIL') . "\n", FILE_APPEND);

        $message = 'Thank you! Your estimate request has been submitted. Our team will contact you within 24 hours.';
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
        <h1>Get Free Estimate</h1>
        <p>Share your requirements and get a personalized quote for your project</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <span>Get Estimate</span>
        </div>
    </div>
</section>

<!-- Estimate Form Section -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 60px; align-items: start;">
            <!-- Benefits -->
            <div>
                <h2 style="margin-bottom: 30px;">Why Get an Estimate?</h2>

                <div style="display: flex; flex-direction: column; gap: 25px;">
                    <div style="display: flex; gap: 20px; align-items: start;">
                        <div
                            style="width: 50px; height: 50px; background: var(--gradient-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;">100% Free Consultation</h4>
                            <p style="margin: 0;">No obligation, no hidden fees. Get expert advice at no cost.</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 20px; align-items: start;">
                        <div
                            style="width: 50px; height: 50px; background: var(--gradient-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;">Transparent Pricing</h4>
                            <p style="margin: 0;">Get detailed quotations with clear breakdown of costs.</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 20px; align-items: start;">
                        <div
                            style="width: 50px; height: 50px; background: var(--gradient-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                            <i class="fas fa-pencil-ruler"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;">Personalized Designs</h4>
                            <p style="margin: 0;">Receive design recommendations tailored to your space and style.</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 20px; align-items: start;">
                        <div
                            style="width: 50px; height: 50px; background: var(--gradient-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;">Quick Response</h4>
                            <p style="margin: 0;">Our team will contact you within 24 hours with your estimate.</p>
                        </div>
                    </div>
                </div>

                <div
                    style="margin-top: 40px; padding: 25px; background: var(--light-gray); border-radius: var(--radius-lg);">
                    <h4><i class="fas fa-phone" style="color: var(--primary); margin-right: 10px;"></i> Prefer to Talk?
                    </h4>
                    <p style="margin-bottom: 15px;">Call us directly for immediate assistance:</p>
                    <a href="tel:<?php echo str_replace(' ', '', SITE_PHONE); ?>" class="btn btn-primary">
                        <i class="fas fa-phone"></i> <?php echo SITE_PHONE; ?>
                    </a>
                </div>
            </div>

            <!-- Estimate Form -->
            <div class="contact-form-card">
                <h3>Request Your Free Estimate</h3>

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
                            <label for="phone">Phone Number <span>*</span></label>
                            <input type="tel" id="phone" name="phone" class="form-control"
                                placeholder="Your phone number" required>
                        </div>

                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" class="form-control" placeholder="Your city">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="property_type">Property Type</label>
                            <select id="property_type" name="property_type" class="form-control">
                                <option value="">Select property type</option>
                                <option value="1bhk">1 BHK Apartment</option>
                                <option value="2bhk">2 BHK Apartment</option>
                                <option value="3bhk">3 BHK Apartment</option>
                                <option value="4bhk">4 BHK Apartment</option>
                                <option value="villa">Villa / Independent House</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="project_type">Project Type</label>
                            <select id="project_type" name="project_type" class="form-control">
                                <option value="">What do you need?</option>
                                <option value="complete">Complete Home Interior</option>
                                <option value="kitchen">Modular Kitchen Only</option>
                                <option value="wardrobe">Wardrobe Only</option>
                                <option value="bedroom">Bedroom Interior</option>
                                <option value="living">Living Room Interior</option>
                                <option value="wooden">Wooden Works (Vasakal, Staircase)</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="budget_range">Budget Range</label>
                        <select id="budget_range" name="budget_range" class="form-control">
                            <option value="">Select your budget</option>
                            <option value="1-3">₹1 - 3 Lakhs</option>
                            <option value="3-5">₹3 - 5 Lakhs</option>
                            <option value="5-8">₹5 - 8 Lakhs</option>
                            <option value="8-12">₹8 - 12 Lakhs</option>
                            <option value="12-20">₹12 - 20 Lakhs</option>
                            <option value="20+">₹20 Lakhs+</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Additional Details</label>
                        <textarea id="message" name="message" class="form-control"
                            placeholder="Tell us more about your project (optional)"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Submit Request
                    </button>

                    <p style="text-align: center; margin-top: 15px; font-size: 0.9rem; color: var(--gray);">
                        <i class="fas fa-lock"></i> Your information is 100% secure and will never be shared.
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>