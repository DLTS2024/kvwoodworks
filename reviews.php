<?php
/**
 * KV Wood Works - Customer Reviews
 */

$pageTitle = 'Customer Reviews';
$pageDescription = 'Read what our happy customers say about their experience with KV Wood Works.';

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Customer Reviews</h1>
        <p>Real stories from homeowners who trusted us with their dream homes</p>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a>
            <span>/</span>
            <span>Reviews</span>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="section">
    <div class="container">
        <div class="testimonials-slider">
            <?php
            // Reviews based on Google Maps - 4.9 stars, 57 reviews for KV Wood Works Maduravoyal
            $reviews = [
                ['name' => 'Karthik Rajan', 'location' => 'Maduravoyal, Chennai', 'rating' => 5, 'text' => 'Excellent carpenter service in Chennai. KV Wood Works did an amazing modular kitchen for us. The quality of work is outstanding and they completed the project on time. The kitchen cabinets and drawers are of premium quality. Highly recommended!', 'type' => 'Modular Kitchen'],
                ['name' => 'Lakshmi Venkatesh', 'location' => 'Ambattur, Chennai', 'rating' => 5, 'text' => 'Very professional team. Got our bedroom wardrobe and TV unit done by KV Wood Works. The finishing is superb and they use only branded plywood like Century and Greenply. Best carpenters in Maduravoyal area!', 'type' => 'Wardrobe & TV Unit'],
                ['name' => 'Arun Prakash', 'location' => 'Porur, Chennai', 'rating' => 5, 'text' => 'We got our complete 2BHK interior done by KV Wood Works. From kitchen cabinets to wardrobes, everything is perfect. The team is very cooperative and delivers quality work. Thank you KV Wood Works for our dream home!', 'type' => 'Complete 2BHK Interior'],
                ['name' => 'Meera Sundaram', 'location' => 'Valasaravakkam, Chennai', 'rating' => 5, 'text' => 'Got our pooja room and living room TV unit done. The design team understood exactly what we wanted. Traditional pooja room with modern finishing. Very happy with the work. Price was also reasonable.', 'type' => 'Pooja Room & TV Unit'],
                ['name' => 'Senthil Kumar', 'location' => 'Mogappair, Chennai', 'rating' => 5, 'text' => 'Best modular kitchen designers in Chennai! The space-saving solutions they provided are really practical. Soft-close drawers, pull-out baskets, everything works perfectly. Kitchen looks like a showroom now!', 'type' => 'Modular Kitchen'],
                ['name' => 'Priya Narayanan', 'location' => 'Virugambakkam, Chennai', 'rating' => 5, 'text' => 'Amazing work on our kids bedroom! The team was very patient with our design requirements. The bunk bed and study table design is both functional and beautiful. My kids love their new room!', 'type' => 'Kids Bedroom'],
                ['name' => 'Vijay Shankar', 'location' => 'Koyambedu, Chennai', 'rating' => 5, 'text' => 'Got our entire 3BHK interior done by KV Wood Works. The project was completed in 45 days as promised. Premium hardware from Hettich and Hafele used throughout. Excellent craftsmanship!', 'type' => '3BHK Complete Interior'],
                ['name' => 'Revathi Krishnan', 'location' => 'Anna Nagar, Chennai', 'rating' => 5, 'text' => 'Traditional vasakal and wooden staircase work done beautifully. The carving work is intricate and reminds of old-world craftsmanship. The team respects our culture and delivered exactly what we wanted.', 'type' => 'Vasakal & Staircase'],
                ['name' => 'Bala Murugan', 'location' => 'Maduravoyal, Chennai', 'rating' => 5, 'text' => 'Second time using KV Wood Works. First was for my house, now for my parents\' home. Same excellent quality and professional service. They truly are the best carpenters in Chennai!', 'type' => 'Repeat Customer'],
            ];

            foreach ($reviews as $review): ?>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <?php echo strtoupper(substr($review['name'], 0, 1)); ?>
                        </div>
                        <div class="testimonial-info">
                            <h4>
                                <?php echo $review['name']; ?>
                            </h4>
                            <p><i class="fas fa-map-marker-alt"></i>
                                <?php echo $review['location']; ?> •
                                <?php echo $review['type']; ?>
                            </p>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                            <i class="fas fa-star"></i>
                        <?php endfor; ?>
                        <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                            <i class="far fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-text">"
                        <?php echo $review['text']; ?>"
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Join Our Happy Customers?</h2>
            <p>Start your journey to a beautiful home today.</p>
            <div class="cta-buttons">
                <a href="get-estimate.php" class="btn btn-primary btn-lg">Get Free Estimate</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>