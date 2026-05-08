<?php
// Page configuration
$page_title = 'Ten12 - Modern Portfolio';
$meta_description = 'Modern. Memorable. Ten12. A clean, professional portfolio showcasing creative work and projects.';
$current_page = 'home';

// Include header
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section id="home" class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-logo">
                <img src="assets/images/logo.png" alt="Ten12 Logo" class="hero-logo-light" />
                <img src="assets/images/primaryLogo.png" alt="Ten12 Logo" class="hero-logo-dark" />
            </div>
            <h2 class="hero-tagline">Modern. Memorable. Ten12.</h2>
            <p class="hero-description">
                Creating exceptional digital experiences with clean design, 
                powerful functionality, and attention to every detail.
            </p>
<div class="hero-buttons">
                <a href="projects.php" class="btn btn-primary">View Projects</a>
                <a href="#contact" class="btn btn-secondary">Get in Touch</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Projects -->
<section id="projects" class="featured-projects">
    <div class="container">
        <div class="section-header">
            <h2>Featured Projects</h2>
            <p>Explore our latest work and creative solutions</p>
        </div>
        <div class="projects-grid" id="featuredProjects">
            <?php
            // Load featured projects from database
            try {
                require_once '../backend/config/database.php';
                require_once '../backend/models/Project.php';
                
                $database = new Database();
                $db = $database->getConnection();
                
                if($db) {
                    $project = new Project($db);
                    $stmt = $project->readAll(true); // Only published projects
                    $num = $stmt->rowCount();
                    
                    if($num > 0) {
                        $count = 0;
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC) && $count < 6) {
                            extract($row);
                            $tags_array = explode(',', $tags);
                            $tags_html = '';
                            foreach($tags_array as $tag) {
                                $tag = trim($tag);
                                if(!empty($tag)) {
                                    $tags_html .= '<span class="tag">' . htmlspecialchars($tag) . '</span>';
                                }
                            }
                            
                            echo '<div class="project-card" onclick="window.location.href=\'project-detail.php?id=' . $id . '\'">';
                            echo '<div class="project-thumbnail">';
                            if(!empty($thumbnail)) {
                                echo '<img src="../backend/' . htmlspecialchars($thumbnail) . '" alt="' . htmlspecialchars($title) . '" loading="lazy">';
                            } else {
                                echo '<div class="placeholder-thumbnail">No Image</div>';
                            }
                            echo '</div>';
                            echo '<div class="project-content">';
                            echo '<h3 class="project-title">' . htmlspecialchars($title) . '</h3>';
                            echo '<p class="project-description">' . htmlspecialchars($description) . '</p>';
                            echo '<div class="project-tags">' . $tags_html . '</div>';
                            echo '<div class="project-links">';
                            if(!empty($live_url)) {
                                echo '<a href="' . htmlspecialchars($live_url) . '" target="_blank" class="project-link">Live Demo</a>';
                            }
                            if(!empty($github_url)) {
                                echo '<a href="' . htmlspecialchars($github_url) . '" target="_blank" class="project-link">GitHub</a>';
                            }
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            $count++;
                        }
                    } else {
                        echo '<p class="no-projects">No projects found.</p>';
                    }
                } else {
                    // Database connection failed - show placeholder projects
                    echo '<div class="project-card">';
                    echo '<div class="project-thumbnail"><div class="placeholder-thumbnail">Project 1</div></div>';
                    echo '<div class="project-content">';
                    echo '<h3 class="project-title">E-Commerce Platform</h3>';
                    echo '<p class="project-description">Modern e-commerce solution with advanced features</p>';
                    echo '<div class="project-tags"><span class="tag">PHP</span><span class="tag">MySQL</span></div>';
                    echo '</div></div>';
                    
                    echo '<div class="project-card">';
                    echo '<div class="project-thumbnail"><div class="placeholder-thumbnail">Project 2</div></div>';
                    echo '<div class="project-content">';
                    echo '<h3 class="project-title">Mobile Banking App</h3>';
                    echo '<p class="project-description">Secure and intuitive mobile banking application</p>';
                    echo '<div class="project-tags"><span class="tag">React Native</span><span class="tag">Node.js</span></div>';
                    echo '</div></div>';
                    
                    echo '<div class="project-card">';
                    echo '<div class="project-thumbnail"><div class="placeholder-thumbnail">Project 3</div></div>';
                    echo '<div class="project-content">';
                    echo '<h3 class="project-title">Brand Identity System</h3>';
                    echo '<p class="project-description">Complete brand identity design for tech startup</p>';
                    echo '<div class="project-tags"><span class="tag">Design</span><span class="tag">Branding</span></div>';
                    echo '</div></div>';
                }
            } catch(Exception $e) {
                // Show placeholder projects on error
                echo '<div class="project-card">';
                echo '<div class="project-thumbnail"><div class="placeholder-thumbnail">Project 1</div></div>';
                echo '<div class="project-content">';
                echo '<h3 class="project-title">E-Commerce Platform</h3>';
                echo '<p class="project-description">Modern e-commerce solution with advanced features</p>';
                echo '<div class="project-tags"><span class="tag">PHP</span><span class="tag">MySQL</span></div>';
                echo '</div></div>';
                
                echo '<div class="project-card">';
                echo '<div class="project-thumbnail"><div class="placeholder-thumbnail">Project 2</div></div>';
                echo '<div class="project-content">';
                echo '<h3 class="project-title">Mobile Banking App</h3>';
                echo '<p class="project-description">Secure and intuitive mobile banking application</p>';
                echo '<div class="project-tags"><span class="tag">React Native</span><span class="tag">Node.js</span></div>';
                echo '</div></div>';
                
                echo '<div class="project-card">';
                echo '<div class="project-thumbnail"><div class="placeholder-thumbnail">Project 3</div></div>';
                echo '<div class="project-content">';
                echo '<h3 class="project-title">Brand Identity System</h3>';
                echo '<p class="project-description">Complete brand identity design for tech startup</p>';
                echo '<div class="project-tags"><span class="tag">Design</span><span class="tag">Branding</span></div>';
                echo '</div></div>';
            }
            ?>
        </div>
        <div class="projects-cta">
            <a href="projects.php" class="btn btn-outline">View All Projects</a>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2>About Ten12</h2>
                <p>
                    Ten12 represents the perfect balance between creativity and functionality. 
                    We specialize in crafting digital experiences that not only look stunning 
                    but also deliver exceptional performance and user experience.
                </p>
                <p>
                    Our approach combines modern design principles with cutting-edge technology 
                    to create solutions that stand out in today's competitive digital landscape.
                </p>
                <div class="skills">
                    <div class="skill-category">
                        <h3>Services</h3>
                        <ul>
                            <li>Web Design & Development</li>
                            <li>Mobile Applications</li>
                            <li>UI/UX Design</li>
                            <li>Brand Identity</li>
                        </ul>
                    </div>
                    <div class="skill-category">
                        <h3>Technologies</h3>
                        <ul>
                            <li>HTML/CSS/JavaScript</li>
                            <li>PHP/MySQL</li>
                            <li>React/Vue</li>
                            <li>Node.js</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="about-stats">
                <div class="stat">
                    <h3>50+</h3>
                    <p>Projects Completed</p>
                </div>
                <div class="stat">
                    <h3>30+</h3>
                    <p>Happy Clients</p>
                </div>
                <div class="stat">
                    <h3>5+</h3>
                    <p>Years Experience</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact">
    <div class="container">
        <div class="section-header">
            <h2>Get In Touch</h2>
            <p>Let's discuss your next project</p>
        </div>
        <div class="contact-content">
            <div class="contact-form">
                <form id="contactForm">
                    <div class="form-group">
                        <input type="text" id="name" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <textarea id="message" name="message" placeholder="Your Message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
                <div id="formMessage" class="form-message"></div>
            </div>
            <div class="contact-info">
                <h3>Contact Information</h3>
                <div class="contact-item">
                    <h4>Email</h4>
                    <p>contact@ten12.com</p>
                </div>
                <div class="contact-item">
                    <h4>Location</h4>
                    <p>Available Worldwide</p>
                </div>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="GitHub">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link" aria-label="LinkedIn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link" aria-label="Twitter">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
require_once 'includes/footer.php';
?>
