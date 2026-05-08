<?php
// Page configuration
$page_title = 'About - Ten12';
$meta_description = 'Learn about Ten12 - Modern. Memorable. Ten12. A design studio creating exceptional digital experiences.';
$current_page = 'about';

// Include header
require_once 'includes/header.php';
?>

<!-- About Section -->
<section id="about" class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>About Ten12</h1>
            <p>Modern. Memorable. Ten12.</p>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="about-section">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2>Our Story</h2>
                <p>Ten12 is a design studio dedicated to creating exceptional digital experiences. We believe in the power of clean design and thoughtful functionality to transform ideas into memorable digital products.</p>
                <p>Our approach combines strategic thinking with creative execution, ensuring every project we undertake not only looks beautiful but also delivers measurable results. From brand identity to web development, we bring ideas to life with precision and passion.</p>
                
                <h3>Our Values</h3>
                <div class="values-grid">
                    <div class="value-item">
                        <h4>Modern Design</h4>
                        <p>We stay ahead of design trends to create contemporary, future-proof solutions.</p>
                    </div>
                    <div class="value-item">
                        <h4>User-Centered</h4>
                        <p>Every decision starts with the user experience in mind.</p>
                    </div>
                    <div class="value-item">
                        <h4>Quality First</h4>
                        <p>We never compromise on quality, from concept to execution.</p>
                    </div>
                    <div class="value-item">
                        <h4>Collaborative</h4>
                        <p>We work closely with clients to bring their vision to life.</p>
                    </div>
                </div>
            </div>
            
            <div class="about-stats">
                <div class="stat-item">
                    <h3>100+</h3>
                    <p>Projects Completed</p>
                </div>
                <div class="stat-item">
                    <h3>50+</h3>
                    <p>Happy Clients</p>
                </div>
                <div class="stat-item">
                    <h3>8+</h3>
                    <p>Years Experience</p>
                </div>
                <div class="stat-item">
                    <h3>15+</h3>
                    <p>Awards Won</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <h2>What We Do</h2>
        <div class="services-grid">
            <div class="service-item">
                <div class="service-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4zM8 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H9a1 1 0 01-1-1V4zM15 3a1 1 0 00-1 1v12a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1h-2z"/>
                    </svg>
                </div>
                <h3>Web Design</h3>
                <p>Beautiful, responsive websites that engage users and drive results.</p>
            </div>
            <div class="service-item">
                <div class="service-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                        <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                    </svg>
                </div>
                <h3>Mobile Apps</h3>
                <p>Native and cross-platform mobile applications with seamless user experiences.</p>
            </div>
            <div class="service-item">
                <div class="service-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3>Brand Identity</h3>
                <p>Comprehensive brand design that captures your essence and stands out.</p>
            </div>
            <div class="service-item">
                <div class="service-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                    </svg>
                </div>
                <h3>UI/UX Design</h3>
                <p>Intuitive interfaces and delightful user experiences that keep users engaged.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Let's Work Together</h2>
            <p>Have a project in mind? We'd love to hear about it.</p>
            <a href="/frontend/contact.php" class="btn btn-primary">Get In Touch</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
