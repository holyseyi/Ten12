<?php
// Page configuration
$page_title = 'Projects - Ten12 Portfolio';
$meta_description = 'Explore our portfolio of creative projects and digital solutions.';
$current_page = 'projects';

// Include header
require_once 'includes/header.php';
?>

<!-- Projects Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1>Our Projects</h1>
            <p>Explore our portfolio of creative solutions and digital experiences</p>
        </div>
    </div>
</section>

<!-- Projects Filter -->
<section class="projects-filter">
    <div class="container">
        <div class="filter-controls">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search projects...">
                <button class="search-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </button>
            </div>
            <div class="category-filter">
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="Web">Web Design</option>
                    <option value="Mobile">Mobile Apps</option>
                    <option value="Design">UI/UX Design</option>
                    <option value="Brand">Brand Identity</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Projects Grid -->
<section class="projects-section">
    <div class="container">
        <div class="projects-grid" id="projectsContainer">
            <?php
            // Load all projects from database
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
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
                        }
                    } else {
                        echo '<div class="no-projects">No projects found.</div>';
                    }
                } else {
                    // Database connection failed - show placeholder projects
                    $placeholder_projects = [
                        ['title' => 'E-Commerce Platform', 'desc' => 'Modern e-commerce solution with advanced features', 'tags' => ['PHP', 'MySQL']],
                        ['title' => 'Mobile Banking App', 'desc' => 'Secure and intuitive mobile banking application', 'tags' => ['React Native', 'Node.js']],
                        ['title' => 'Brand Identity System', 'desc' => 'Complete brand identity design for tech startup', 'tags' => ['Design', 'Branding']],
                        ['title' => 'Task Management Dashboard', 'desc' => 'Collaborative project management solution', 'tags' => ['Vue.js', 'Express.js']],
                        ['title' => 'Social Media Analytics', 'desc' => 'Advanced analytics platform for social media', 'tags' => ['Python', 'Django']],
                        ['title' => 'Restaurant Booking System', 'desc' => 'Modern reservation system for hospitality', 'tags' => ['PHP', 'Laravel']]
                    ];
                    
                    foreach($placeholder_projects as $proj) {
                        $tags_html = '';
                        foreach($proj['tags'] as $tag) {
                            $tags_html .= '<span class="tag">' . htmlspecialchars($tag) . '</span>';
                        }
                        
                        echo '<div class="project-card">';
                        echo '<div class="project-thumbnail"><div class="placeholder-thumbnail">' . htmlspecialchars($proj['title']) . '</div></div>';
                        echo '<div class="project-content">';
                        echo '<h3 class="project-title">' . htmlspecialchars($proj['title']) . '</h3>';
                        echo '<p class="project-description">' . htmlspecialchars($proj['desc']) . '</p>';
                        echo '<div class="project-tags">' . $tags_html . '</div>';
                        echo '</div></div>';
                    }
                }
            } catch(Exception $e) {
                // Show placeholder projects on error
                $placeholder_projects = [
                    ['title' => 'E-Commerce Platform', 'desc' => 'Modern e-commerce solution with advanced features', 'tags' => ['PHP', 'MySQL']],
                    ['title' => 'Mobile Banking App', 'desc' => 'Secure and intuitive mobile banking application', 'tags' => ['React Native', 'Node.js']],
                    ['title' => 'Brand Identity System', 'desc' => 'Complete brand identity design for tech startup', 'tags' => ['Design', 'Branding']],
                    ['title' => 'Task Management Dashboard', 'desc' => 'Collaborative project management solution', 'tags' => ['Vue.js', 'Express.js']],
                    ['title' => 'Social Media Analytics', 'desc' => 'Advanced analytics platform for social media', 'tags' => ['Python', 'Django']],
                    ['title' => 'Restaurant Booking System', 'desc' => 'Modern reservation system for hospitality', 'tags' => ['PHP', 'Laravel']]
                ];
                
                foreach($placeholder_projects as $proj) {
                    $tags_html = '';
                    foreach($proj['tags'] as $tag) {
                        $tags_html .= '<span class="tag">' . htmlspecialchars($tag) . '</span>';
                    }
                    
                    echo '<div class="project-card">';
                    echo '<div class="project-thumbnail"><div class="placeholder-thumbnail">' . htmlspecialchars($proj['title']) . '</div></div>';
                    echo '<div class="project-content">';
                    echo '<h3 class="project-title">' . htmlspecialchars($proj['title']) . '</h3>';
                    echo '<p class="project-description">' . htmlspecialchars($proj['desc']) . '</p>';
                    echo '<div class="project-tags">' . $tags_html . '</div>';
                    echo '</div></div>';
                }
            }
            ?>
        </div>
        <div class="loading-more" id="loadingMore" style="display: none;">
            <div class="spinner"></div>
            <p>Loading more projects...</p>
        </div>
    </div>
</section>

<?php
// Include footer
require_once 'includes/footer.php';
?>

<script>
// Initialize project search and filtering
document.addEventListener('DOMContentLoaded', function() {
    initProjectSearch();
});

// Load all projects via AJAX for filtering
function performSearch() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const container = document.getElementById('projectsContainer');
    
    if (!container) return;
    
    const searchTerm = searchInput ? searchInput.value : '';
    const category = categoryFilter ? categoryFilter.value : '';
    
    // Show loading
    container.innerHTML = '<div class="loading">Searching projects...</div>';
    
    // Build URL
    let url = '../backend/api/projects.php';
    const params = new URLSearchParams();
    
    if (searchTerm) params.append('search', searchTerm);
    if (category) params.append('category', category);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    // Fetch filtered projects
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.records && data.records.length > 0) {
                container.innerHTML = data.records.map(project => createProjectCard(project)).join('');
            } else {
                container.innerHTML = '<div class="no-projects">No projects found matching your criteria.</div>';
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            container.innerHTML = '<div class="error-message">Failed to search projects.</div>';
        });
}

function createProjectCard(project) {
    const tags = project.tags ? project.tags.map(tag => `<span class="tag">${tag}</span>`).join('') : '';
    const thumbnail = project.thumbnail ? `../backend/${project.thumbnail}` : 'assets/images/placeholder.jpg';
    
    return `
        <div class="project-card" onclick="window.location.href='/frontend/project-detail.php?id=${project.id}'">
            <div class="project-thumbnail">
                <img src="${thumbnail}" alt="${project.title}" loading="lazy">
            </div>
            <div class="project-content">
                <h3 class="project-title">${project.title}</h3>
                <p class="project-description">${project.description}</p>
                <div class="project-tags">${tags}</div>
                <div class="project-links">
                    ${project.live_url ? `<a href="${project.live_url}" target="_blank" class="project-link">Live Demo</a>` : ''}
                    ${project.github_url ? `<a href="${project.github_url}" target="_blank" class="project-link">GitHub</a>` : ''}
                </div>
            </div>
        </div>
    `;
}
</script>
