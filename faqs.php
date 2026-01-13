<?php
/**
 * KV Wood Works - FAQs
 */

$pageTitle = 'Frequently Asked Questions';
$pageDescription = 'Find answers to common questions about our home interior and wooden works services.';

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Frequently Asked Questions</h1>
        <p>Find answers to common questions about our services</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <span>FAQs</span>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section">
    <div class="container">
        <div class="faq-list">
            <?php
            $faqs = [
                [
                    'question' => 'How can I get started with KV Wood Works?',
                    'answer' => 'Getting started is easy! Simply fill out our "Get Free Estimate" form or call us directly at ' . SITE_PHONE . '. Our team will schedule a free consultation at your convenience where we will understand your requirements, take measurements, and provide a detailed quote.'
                ],
                [
                    'question' => 'What is the timeline for completing a project?',
                    'answer' => 'The timeline varies based on the scope of work. A modular kitchen typically takes 15-25 days, wardrobes take 10-15 days, while complete home interiors for a 2BHK may take 30-45 days. We provide a detailed timeline during the consultation.'
                ],
                [
                    'question' => 'Do you provide warranty on your work?',
                    'answer' => 'Yes, we provide a 5-year warranty on all our modular furniture and wooden works. This covers manufacturing defects, hardware issues, and structural problems. We also offer extended warranty options for additional peace of mind.'
                ],
                [
                    'question' => 'What materials do you use?',
                    'answer' => 'We use only high-quality materials including BWR/BWP grade plywood, premium laminates from brands like Merino and Century, and branded hardware from Hettich and Hafele. All materials are termite-resistant and come with quality certifications. For wooden works, we use seasoned Teak and Sal wood.'
                ],
                [
                    'question' => 'Do you offer EMI options?',
                    'answer' => 'Yes, we offer flexible EMI options through our partner banks. You can get your dream home interior with easy monthly payments spread over 6-36 months. We also accept payment in milestones - 20% advance, 50% mid-project, and 30% on completion.'
                ],
                [
                    'question' => 'Which areas do you serve?',
                    'answer' => 'We currently serve Chennai and surrounding areas including Maduravoyal, Ambattur, Avadi, Porur, Guindy, Velachery, and other localities. We also serve Tamil Nadu region. Contact us to check if we serve your specific location.'
                ],
                [
                    'question' => 'Can I see your previous work before deciding?',
                    'answer' => 'Absolutely! You can browse our portfolio on the "Recent Projects" page. We can also arrange visits to some of our completed projects near your location, subject to homeowner permission. This helps you see the quality of our work firsthand.'
                ],
                [
                    'question' => 'What is the process for getting interior work done?',
                    'answer' => 'Our process is simple: 1) Free Consultation - We visit your home to understand requirements. 2) Design & Quote - We provide 3D designs and detailed pricing. 3) Agreement - Sign contract with payment schedule. 4) Production - Work begins at our workshop. 5) Installation - Professional installation at your home. 6) Handover - Final quality checks and handover.'
                ],
                [
                    'question' => 'How do you ensure quality of work?',
                    'answer' => 'Quality is our top priority. We follow a rigorous 51-point checklist at every stage. Our carpenters are trained and experienced. We use only branded hardware and certified materials. Every project goes through multiple quality inspections before handover.'
                ],
                [
                    'question' => 'Can I customize the designs as per my requirements?',
                    'answer' => 'Yes, all our designs are fully customizable! Our designers work closely with you to understand your needs, preferences, and space constraints. We create personalized designs that match your style and budget. You can make changes until you are completely satisfied.'
                ],
            ];

            foreach ($faqs as $index => $faq): ?>
                <div class="faq-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <button class="faq-question">
                        <?php echo $faq['question']; ?>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <?php echo $faq['answer']; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Still Have Questions -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Still Have Questions?</h2>
            <p>Can't find what you're looking for? Our team is here to help!</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-primary btn-lg">Contact Us</a>
                <a href="tel:<?php echo str_replace(' ', '', SITE_PHONE); ?>" class="btn btn-white btn-lg"><i
                        class="fas fa-phone"></i> Call Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>