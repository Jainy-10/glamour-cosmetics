<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Glamour Cosmetics & Jewelry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #e91e63;
            --secondary-color: #f8bbd9;
            --accent-color: #4a148c;
            --text-dark: #2c2c2c;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .hero-section {
            background: linear-gradient(135deg, #ffeef8 0%, #f3e5f5 100%);
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="%23e91e63" opacity="0.1"/><circle cx="80" cy="40" r="1" fill="%234a148c" opacity="0.1"/><circle cx="40" cy="80" r="1" fill="%23e91e63" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            opacity: 0.3;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            border-radius: 2px;
        }

        .story-card, .value-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            overflow: hidden;
        }

        .story-card:hover, .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .value-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }

        .team-member {
            text-align: center;
            margin-bottom: 2rem;
        }

        .team-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
            border: 5px solid white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .stats-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            padding: 80px 0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-top: 0.5rem;
        }

        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            color: white;
            padding: 60px 0 20px;
            margin-top: 80px;
        }

        .footer-section h5 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .social-icons {
            display: flex;
            gap: 1rem;
        }

        .social-icon {
            width: 45px;
            height: 45px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .social-icon:hover {
            transform: scale(1.1);
            background: var(--accent-color);
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #444;
            padding-top: 2rem;
            margin-top: 3rem;
            text-align: center;
            color: #aaa;
        }

        .newsletter-form {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .newsletter-form input {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .newsletter-form input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .newsletter-form button {
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .newsletter-form button:hover {
            background: var(--accent-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-gem me-2"></i>Glamour Cosmetics
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-light text-dark ms-1">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-dark mb-4">Our Beautiful Story</h1>
                    <p class="lead text-muted mb-4">
                        Discover the passion, craftsmanship, and dedication behind Glamour Cosmetics & Jewelry - 
                        where beauty meets elegance in every product we create.
                    </p>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image">
                        <i class="fas fa-heart" style="font-size: 15rem; color: var(--primary-color); opacity: 0.1;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Our Story</h2>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="story-card h-100 p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="value-icon me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <h4 class="mb-0">The Beginning</h4>
                        </div>
                        <p class="text-muted">
                            Founded in 2018, Glamour Cosmetics began as a small dream in a home kitchen. 
                            Junaid, a passionate beauty enthusiast, started creating natural, high-quality 
                            cosmetics for her friends and family. Word spread quickly about these amazing products.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="story-card h-100 p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="value-icon me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <h4 class="mb-0">The Growth</h4>
                        </div>
                        <p class="text-muted">
                            As demand grew, we expanded our product line to include premium jewelry pieces 
                            that complement our cosmetics perfectly. Today, we serve thousands of satisfied 
                            customers worldwide with our commitment to quality and beauty.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">50K+</span>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">200+</span>
                        <div class="stat-label">Products</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">98%</span>
                        <div class="stat-label">Satisfaction Rate</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <div class="stat-label">Customer Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Our Values</h2>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="value-card p-4 text-center h-100">
                        <div class="value-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h5 class="mb-3">Natural & Safe</h5>
                        <p class="text-muted">
                            We use only the finest natural ingredients, ensuring our products are safe 
                            and gentle for all skin types.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="value-card p-4 text-center h-100">
                        <div class="value-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <h5 class="mb-3">Premium Quality</h5>
                        <p class="text-muted">
                            Every product undergoes rigorous quality testing to meet our high standards 
                            before reaching you.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="value-card p-4 text-center h-100">
                        <div class="value-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h5 class="mb-3">Customer Love</h5>
                        <p class="text-muted">
                            Your satisfaction is our priority. We're here to make you feel beautiful 
                            and confident every day.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5" style="background: #f8f9fa;">
        <div class="container">
            <h2 class="section-title text-center">Meet Our Team</h2>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="team-photo">
                            <i class="fa-solid fa-crown"></i>
                        </div>
                        <h5>Junaid Iqbal</h5>
                        <p class="text-muted mb-2">Founder & CEO</p>
                        <p class="small text-muted">
                            With over 10 years of experience in the beauty industry, junaid leads our 
                            team with passion and vision for creating exceptional products.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="team-photo">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h5>Dr. Sarah</h5>
                        <p class="text-muted mb-2">Head of Product Development</p>
                        <p class="small text-muted">
                            Dr. Sarah ensures all our formulations are safe, effective, and meet 
                            the highest standards of quality and innovation.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="team-photo">
                            <i class="fas fa-gem"></i>
                        </div>
                        <h5>Maria Aizal</h5>
                        <p class="text-muted mb-2">Jewelry Design Director</p>
                        <p class="small text-muted">
                            Maria brings artistic flair and craftsmanship to our jewelry collection, 
                            creating pieces that complement your natural beauty.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-section">
                        <h5><i class="fas fa-gem me-2"></i>Glamour Cosmetics</h5>
                        <p class="mb-4 text-light">
                            Your trusted partner for premium cosmetics and elegant jewelry. 
                            Enhancing natural beauty since 2018.
                        </p>
                        <div class="social-icons">
                            <a href="#" class="social-icon">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-icon">
                                <i class="fab fa-pinterest"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section">
                        <h5>Quick Links</h5>
                        <ul class="footer-links">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="products.php">Products</a></li>
                            <li><a href="contact.php">Contact</a></li>
                            <li><a href="cart.php">Shopping Cart</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Categories -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section">
                        <h5>Categories</h5>
                        <ul class="footer-links">
                            <li><a href="products.php?category=skincare">Skincare</a></li>
                            <li><a href="products.php?category=makeup">Makeup</a></li>
                            <li><a href="products.php?category=fragrance">Fragrance</a></li>
                            <li><a href="products.php?category=jewelry">Jewelry</a></li>
                            <li><a href="products.php?category=accessories">Accessories</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Newsletter -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-section">
                        <h5>Stay Updated</h5>
                        <p class="text-light mb-3">
                            Subscribe to our newsletter for exclusive offers and beauty tips!
                        </p>
                        <form class="newsletter-form">
                            <input type="email" placeholder="Enter your email" required>
                            <button type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        <div class="mt-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                <span class="text-light">123 Beauty Street, Glamour City, GC 12345</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                <span class="text-light">+1 (555) 123-4567</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                <span class="text-light">hello@Glamourcosmetics.com</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0">&copy; 2024 Glamour Cosmetics & Jewelry. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="#" class="text-decoration-none me-3" style="color: #aaa;">Privacy Policy</a>
                        <a href="#" class="text-decoration-none me-3" style="color: #aaa;">Terms of Service</a>
                        <a href="#" class="text-decoration-none" style="color: #aaa;">Return Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Newsletter form submission
        document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input').value;
            if (email) {
                alert('Thank you for subscribing! You will receive our latest updates.');
                this.querySelector('input').value = '';
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>