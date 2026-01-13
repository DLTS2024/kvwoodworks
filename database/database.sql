-- KV Wood Works Database Schema
-- Import this file into phpMyAdmin

-- Create Database (if needed)
-- CREATE DATABASE IF NOT EXISTS kvwoodworks;
-- USE kvwoodworks;

-- =============================================
-- Categories Table
-- =============================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image_url VARCHAR(255),
    parent_category ENUM('interior_design', 'wooden_works', 'bhk') NOT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Projects Table
-- =============================================
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    category_id INT,
    location VARCHAR(100),
    client_name VARCHAR(100),
    completion_date DATE,
    budget_range VARCHAR(50),
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('completed', 'ongoing', 'upcoming') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Project Images Table
-- =============================================
CREATE TABLE IF NOT EXISTS project_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    alt_text VARCHAR(200),
    is_primary BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Reviews/Testimonials Table
-- =============================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_location VARCHAR(100),
    customer_image VARCHAR(255),
    rating INT DEFAULT 5 CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT NOT NULL,
    project_type VARCHAR(100),
    is_featured BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Blog Posts Table
-- =============================================
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT,
    featured_image VARCHAR(255),
    author VARCHAR(100) DEFAULT 'KV Wood Works',
    category VARCHAR(100),
    tags VARCHAR(255),
    is_published BOOLEAN DEFAULT TRUE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- FAQs Table
-- =============================================
CREATE TABLE IF NOT EXISTS faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Contact Submissions Table
-- =============================================
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Estimate Requests Table
-- =============================================
CREATE TABLE IF NOT EXISTS estimate_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    city VARCHAR(100),
    property_type ENUM('1bhk', '2bhk', '3bhk', '4bhk', 'villa', 'other') DEFAULT 'other',
    project_type VARCHAR(100),
    budget_range VARCHAR(50),
    preferred_date DATE,
    message TEXT,
    status ENUM('new', 'contacted', 'quoted', 'converted', 'closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Insert Sample Categories
-- =============================================
INSERT INTO categories (name, slug, description, parent_category, is_featured, display_order) VALUES
-- Interior Design Categories
('Modular Kitchen Designs', 'modular-kitchen', 'Transform your kitchen with our modern modular designs', 'interior_design', TRUE, 1),
('Customize Your Kitchen', 'customize-kitchen', 'Create your dream kitchen with custom solutions', 'interior_design', FALSE, 2),
('Wardrobe Designs', 'wardrobe', 'Stylish and functional wardrobe solutions', 'interior_design', TRUE, 3),
('Bedroom', 'bedroom', 'Beautiful bedroom interior designs', 'interior_design', TRUE, 4),
('Living Room', 'living-room', 'Elegant living room designs for your home', 'interior_design', TRUE, 5),
('Kid Bedroom', 'kid-bedroom', 'Fun and safe bedroom designs for children', 'interior_design', FALSE, 6),
('Dining Room', 'dining-room', 'Sophisticated dining room interiors', 'interior_design', FALSE, 7),
('Pooja Room', 'pooja-room', 'Sacred and serene pooja room designs', 'interior_design', FALSE, 8),
('Space Saving', 'space-saving', 'Smart space-saving interior solutions', 'interior_design', TRUE, 9),
('Home Office', 'home-office', 'Professional home office setups', 'interior_design', FALSE, 10),
('Bathroom', 'bathroom', 'Modern bathroom interior designs', 'interior_design', FALSE, 11),
('Balcony', 'balcony', 'Beautiful balcony transformation ideas', 'interior_design', FALSE, 12),

-- BHK Categories
('1 BHK Interior', '1bhk', 'Complete interior solutions for 1 BHK homes', 'bhk', TRUE, 1),
('2 BHK Interior', '2bhk', 'Complete interior solutions for 2 BHK homes', 'bhk', TRUE, 2),

-- Wooden Works Categories
('Vasakal', 'vasakal', 'Traditional and modern main door frame designs', 'wooden_works', TRUE, 1),
('Window / Janal', 'window-janal', 'Custom window and janal wooden works', 'wooden_works', TRUE, 2),
('Wooden Staircase', 'wooden-staircase', 'Elegant wooden staircase designs', 'wooden_works', TRUE, 3);

-- =============================================
-- Insert Sample Reviews
-- =============================================
INSERT INTO reviews (customer_name, customer_location, rating, review_text, project_type, is_featured) VALUES
('Ramesh Kumar', 'Bangalore', 5, 'Excellent work by KV Wood Works! They transformed our 2BHK into a beautiful home. The modular kitchen is absolutely stunning and the wardrobe designs are both stylish and functional.', '2 BHK Interior', TRUE),
('Priya Sharma', 'Mysore', 5, 'We are extremely happy with the wooden staircase and vasakal work done by KV Wood Works. The craftsmanship is outstanding and the team was very professional.', 'Wooden Works', TRUE),
('Suresh Reddy', 'Hubli', 5, 'Got our entire home interior done by KV Wood Works. From living room to kitchen, everything is perfect. Highly recommend their services!', 'Complete Home Interior', TRUE),
('Lakshmi Devi', 'Mangalore', 4, 'Great quality work for our pooja room and bedroom. The team was punctual and completed the work within the promised timeline.', 'Pooja Room & Bedroom', FALSE),
('Venkatesh Rao', 'Belgaum', 5, 'The modular kitchen design exceeded our expectations. The space-saving solutions they provided are really practical for our small kitchen.', 'Modular Kitchen', TRUE);

-- =============================================
-- Insert Sample FAQs
-- =============================================
INSERT INTO faqs (question, answer, category, display_order) VALUES
('How can I get started with KV Wood Works?', 'Getting started is easy! Simply fill out our "Get Free Estimate" form or call us directly. Our team will schedule a free consultation at your convenience where we will understand your requirements, take measurements, and provide a detailed quote.', 'General', 1),
('What is the timeline for completing a project?', 'The timeline varies based on the scope of work. A modular kitchen typically takes 15-25 days, while complete home interiors for a 2BHK may take 30-45 days. We provide a detailed timeline during the consultation.', 'General', 2),
('Do you provide warranty on your work?', 'Yes, we provide a 5-year warranty on all our modular furniture and wooden works. This covers manufacturing defects and hardware issues.', 'Warranty', 3),
('What materials do you use?', 'We use only high-quality materials including BWR/BWP grade plywood, premium laminates, and branded hardware from Hettich/Hafele. All materials are termite-resistant and come with quality certifications.', 'Materials', 4),
('Do you offer EMI options?', 'Yes, we offer flexible EMI options through our partner banks. You can get your dream home interior with easy monthly payments spread over 12-36 months.', 'Payment', 5),
('Which areas do you serve?', 'We currently serve Karnataka including Bangalore, Mysore, Mangalore, Hubli, Belgaum, and surrounding areas. Contact us to check if we serve your location.', 'Service Area', 6);

-- =============================================
-- Insert Sample Blog Posts
-- =============================================
INSERT INTO blog_posts (title, slug, excerpt, content, category) VALUES
('10 Modular Kitchen Design Ideas for 2025', 'modular-kitchen-design-ideas-2025', 'Discover the latest trends in modular kitchen designs that are taking homes by storm in 2025.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Explore the top 10 modular kitchen design ideas that combine functionality with style...', 'Kitchen'),
('How to Choose the Perfect Wardrobe for Your Bedroom', 'choose-perfect-wardrobe', 'A complete guide to selecting the right wardrobe design based on your space and storage needs.', 'Choosing the right wardrobe can transform your bedroom. This guide covers everything from sliding vs hinged doors to internal configurations...', 'Bedroom'),
('Space-Saving Interior Ideas for Small Homes', 'space-saving-ideas-small-homes', 'Smart solutions to maximize space in compact apartments and homes.', 'Living in a small space doesnt mean compromising on style or functionality. Here are proven space-saving strategies...', 'Tips');

-- =============================================
-- Insert Sample Projects
-- =============================================
INSERT INTO projects (title, slug, description, category_id, location, client_name, completion_date, budget_range, is_featured) VALUES
('Modern 2BHK Interior - Whitefield', 'modern-2bhk-whitefield', 'Complete home interior for a 2BHK apartment featuring contemporary modular kitchen, elegant living room, and functional bedroom wardrobes.', 14, 'Whitefield, Bangalore', 'Mr. Rajesh', '2025-12-15', '8-12 Lakhs', TRUE),
('Traditional Vasakal & Staircase', 'traditional-vasakal-staircase', 'Beautiful traditional wooden vasakal with intricate carvings paired with a matching wooden staircase.', 15, 'Mysore', 'Mrs. Lakshmi', '2025-11-20', '3-5 Lakhs', TRUE),
('Contemporary Modular Kitchen', 'contemporary-modular-kitchen', 'U-shaped modular kitchen with premium acrylic finish, soft-close drawers, and integrated appliances.', 1, 'Koramangala, Bangalore', 'Mr. Sunil', '2025-10-10', '4-6 Lakhs', TRUE),
('Compact 1BHK Makeover', '1bhk-makeover-electronic-city', 'Space-efficient interior design for a 1BHK with murphy bed, compact kitchen, and multi-functional furniture.', 13, 'Electronic City, Bangalore', 'Ms. Priya', '2025-09-25', '5-7 Lakhs', FALSE),
('Luxury Living Room Design', 'luxury-living-room-design', 'Premium living room interior with custom TV unit, false ceiling, and designer furniture.', 5, 'Indiranagar, Bangalore', 'Dr. Venkat', '2025-08-30', '6-8 Lakhs', TRUE);
