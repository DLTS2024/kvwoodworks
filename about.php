<?php
/**
 * KV Wood Works - About Us
 */

$pageTitle = 'About Us - Best Interior Designers Chennai';
$pageDescription = 'About KV Wood Works - Chennai\'s trusted home interior company since 1995. Founded by Mr.M.Kolanji, now run with his son Mr.K.Sridhar. 500+ projects, 30+ years experience, 5-year warranty.';
$pageKeywords = 'about kv wood works, interior designers chennai, home interior company, wooden works experts, trusted interior designers, best carpenters chennai, modular kitchen experts';

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>About Us</h1>
        <p>Your Trusted Partner for Premium Home Interiors & Wooden Works</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <span>About Us</span>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
            <div>
                <h2>Crafting Dream Homes Since 1995</h2>
                <p style="font-size: 1.1rem; margin-bottom: 20px;">KV Wood Works has been transforming homes across
                    Chennai with premium interior solutions and exquisite wooden craftsmanship for over three decades.
                </p>
                <p>Founded in 1995 by <strong>Mr.M.Kolanji</strong> with a passion for quality and design, we started as
                    a small workshop specializing in
                    traditional wooden works. Today, the brand is successfully run by both the founder and his elder son
                    <strong>Mr.K.Sridhar</strong>, continuing the legacy of excellence. We are now a full-service
                    interior design company offering modular
                    kitchens, wardrobes, complete home interiors, and custom wooden works.
                </p>
                <p>Our journey has been driven by one simple belief: every home deserves to be beautiful and functional.
                    We combine traditional craftsmanship with modern design sensibilities to create spaces that our
                    clients love.</p>

                <div style="display: flex; gap: 30px; margin-top: 30px;">
                    <div style="text-align: center;">
                        <h3 style="color: var(--primary); font-size: 2.5rem; margin-bottom: 5px;">500+</h3>
                        <p style="margin: 0;">Projects Completed</p>
                    </div>
                    <div style="text-align: center;">
                        <h3 style="color: var(--primary); font-size: 2.5rem; margin-bottom: 5px;">30+</h3>
                        <p style="margin: 0;">Years Experience</p>
                    </div>
                    <div style="text-align: center;">
                        <h3 style="color: var(--primary); font-size: 2.5rem; margin-bottom: 5px;">50+</h3>
                        <p style="margin: 0;">Expert Craftsmen</p>
                    </div>
                </div>
            </div>
            <div>
                <?php if (file_exists(__DIR__ . '/assets/images/about-us.jpg')): ?>
                    <img src="<?php echo SITE_URL; ?>/assets/images/about-us.jpg" alt="KV Wood Works Team"
                        style="width: 100%; height: 450px; object-fit: cover; border-radius: var(--radius-lg);">
                <?php else: ?>
                    <div class="placeholder-image" style="height: 450px; border-radius: var(--radius-lg);">
                        <i class="fas fa-users"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section section-light" id="why-us">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose KV Wood Works?</h2>
            <p>What sets us apart from the rest</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <h4>Quality Assurance</h4>
                <p>We use only premium materials - BWR/BWP grade plywood, branded hardware from Hettich/Hafele, and
                    quality laminates with ISI certification.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h4>Expert Team</h4>
                <p>Our team includes experienced interior designers, skilled carpenters, and dedicated project managers
                    who ensure flawless execution.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <h4>Fair Pricing</h4>
                <p>Transparent pricing with no hidden costs. We provide detailed quotations upfront and offer flexible
                    payment options including EMI.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>Timely Delivery</h4>
                <p>We respect your time. Our streamlined process and dedicated factory ensure projects are completed
                    within the promised timeline.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4>5-Year Warranty</h4>
                <p>Complete peace of mind with our comprehensive warranty covering manufacturing defects and hardware
                    issues.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h4>After-Sales Support</h4>
                <p>Our relationship doesn't end after installation. We provide dedicated after-sales support for any
                    maintenance needs.</p>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Our Values</h2>
            <p>The principles that guide everything we do</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
            <div style="text-align: center; padding: 30px;">
                <div style="font-size: 3rem; color: var(--primary); margin-bottom: 20px;">
                    <i class="fas fa-heart"></i>
                </div>
                <h4>Passion</h4>
                <p>Every project is crafted with love and attention to detail. We are passionate about creating
                    beautiful spaces.</p>
            </div>

            <div style="text-align: center; padding: 30px;">
                <div style="font-size: 3rem; color: var(--primary); margin-bottom: 20px;">
                    <i class="fas fa-handshake"></i>
                </div>
                <h4>Integrity</h4>
                <p>Honesty and transparency in all our dealings. No hidden costs, no surprises - just quality work.</p>
            </div>

            <div style="text-align: center; padding: 30px;">
                <div style="font-size: 3rem; color: var(--primary); margin-bottom: 20px;">
                    <i class="fas fa-gem"></i>
                </div>
                <h4>Excellence</h4>
                <p>We never compromise on quality. From materials to execution, we strive for excellence in everything.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Let's Create Something Beautiful Together</h2>
            <p>Ready to transform your home? Get in touch with our team for a free consultation.</p>
            <div class="cta-buttons">
                <a href="get-estimate.php" class="btn btn-primary btn-lg">Get Free Estimate</a>
                <a href="contact.php" class="btn btn-white btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>