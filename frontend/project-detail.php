<?php
// Page configuration
$page_title = 'Project Details - Ten12 Portfolio';
$meta_description = 'Detailed view of our project work and creative solutions.';
$current_page = 'projects';

// Get project ID from URL
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Include header
require_once 'includes/header.php';
?>

<!-- Project Detail Section -->
<section class="project-detail">
    <div class="container">
        <?php
        // Load project details from database
        try {
            require_once '../backend/config/database.php';
            require_once '../backend/models/Project.php';
            
            $database = new Database();
            $db = $database->getConnection();
            
            $project = new Project($db);
            $project->id = $project_id;
            
            if($project->readOne() && $project->published) {
                $images_array = !empty($project->images) ? json_decode($project->images, true) : [];
                $tags_array = explode(',', $project->tags);
                $tags_html = '';
                foreach($tags_array as $tag) {
                    $tag = trim($tag);
                    if(!empty($tag)) {
                        $tags_html .= '<span class="tag">' . htmlspecialchars($tag) . '</span>';
                    }
                }
                
                echo '<div class="project-detail-header">';
                echo '<div class="breadcrumb">';
                echo '<a href="index.php">Home</a> / <a href="projects.php">Projects</a> / ' . htmlspecialchars($project->title);
                echo '</div>';
                echo '<h1 class="project-title">' . htmlspecialchars($project->title) . '</h1>';
                echo '<div class="project-meta">';
                echo '<span class="project-category">' . htmlspecialchars($project->category) . '</span>';
                echo '<span class="project-date">Created: ' . date('M j, Y', strtotime($project->created_at)) . '</span>';
                echo '</div>';
                echo '<div class="project-tags">' . $tags_html . '</div>';
                echo '</div>';
                
                echo '<div class="project-detail-content">';
                echo '<div class="project-detail-main">';
                
                // Image Gallery
                if(!empty($images_array) || !empty($project->thumbnail)) {
                    echo '<div class="project-gallery">';
                    echo '<div class="main-image">';
                    $main_image = !empty($images_array) ? $images_array[0] : $project->thumbnail;
                    echo '<img id="mainImage" src="../backend/' . htmlspecialchars($main_image) . '" alt="' . htmlspecialchars($project->title) . '">';
                    echo '</div>';
                    
                    if(!empty($images_array) && count($images_array) > 1) {
                        echo '<div class="gallery-thumbnails">';
                        foreach($images_array as $index => $image) {
                            $active_class = $index === 0 ? 'active' : '';
                            echo '<div class="gallery-thumbnail ' . $active_class . '" data-image="' . htmlspecialchars($image) . '">';
                            echo '<img src="../backend/' . htmlspecialchars($image) . '" alt="Gallery image ' . ($index + 1) . '">';
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                } elseif(!empty($project->thumbnail)) {
                    echo '<div class="project-gallery">';
                    echo '<div class="main-image">';
                    echo '<img id="mainImage" src="../backend/' . htmlspecialchars($project->thumbnail) . '" alt="' . htmlspecialchars($project->title) . '">';
                    echo '</div>';
                    echo '</div>';
                }
                
                // Project Description
                echo '<div class="project-description">';
                echo '<h2>About This Project</h2>';
                echo '<div class="project-content-text">' . nl2br(htmlspecialchars($project->content)) . '</div>';
                echo '</div>';
                
                echo '</div>';
                
                // Project Sidebar
                echo '<div class="project-detail-sidebar">';
                echo '<div class="project-info-card">';
                echo '<h3>Project Information</h3>';
                echo '<div class="info-item">';
                echo '<strong>Category:</strong> ' . htmlspecialchars($project->category);
                echo '</div>';
                echo '<div class="info-item">';
                echo '<strong>Created:</strong> ' . date('F j, Y', strtotime($project->created_at));
                echo '</div>';
                echo '<div class="info-item">';
                echo '<strong>Last Updated:</strong> ' . date('F j, Y', strtotime($project->updated_at));
                echo '</div>';
                echo '</div>';
                
                // Project Links
                if(!empty($project->live_url) || !empty($project->github_url)) {
                    echo '<div class="project-links-card">';
                    echo '<h3>Project Links</h3>';
                    if(!empty($project->live_url)) {
                        echo '<a href="' . htmlspecialchars($project->live_url) . '" target="_blank" class="btn btn-primary btn-block">View Live Demo</a>';
                    }
                    if(!empty($project->github_url)) {
                        echo '<a href="' . htmlspecialchars($project->github_url) . '" target="_blank" class="btn btn-outline btn-block">View on GitHub</a>';
                    }
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
                
// Related Projects
                echo '<div class="related-projects">';
                echo '<h2>Related Projects</h2>';
                echo '<div class="projects-grid">';
                
                // Load related projects (same category, excluding current)
                $related_category = $project->category;
                $related_query = "SELECT * FROM projects 
                                WHERE category = :category AND id != :id AND published = 1 
                                ORDER BY created_at DESC LIMIT 3";
                $related_stmt = $db->prepare($related_query);
                $related_stmt->bindParam(':category', $related_category);
                $related_stmt->bindParam(':id', $project_id);
                $related_stmt->execute();
                
                $related_count = $related_stmt->rowCount();
                if($related_count > 0) {
                    while($related_row = $related_stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($related_row);
                        $related_tags = explode(',', $tags);
                        $related_tags_html = '';
                        foreach($related_tags as $tag) {
                            $tag = trim($tag);
                            if(!empty($tag)) {
                                $related_tags_html .= '<span class="tag">' . htmlspecialchars($tag) . '</span>';
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
                        echo '<div class="project-tags">' . $related_tags_html . '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-related">No related projects found.</p>';
                }
                
                echo '</div>';
                echo '</div>';
                
            } else {
                echo '<div class="project-not-found">';
                echo '<h2>Project Not Found</h2>';
                echo '<p>The project you\'re looking for doesn\'t exist or has been removed.</p>';
                echo '<a href="projects.php" class="btn btn-primary">View All Projects</a>';
                echo '</div>';
            }
        } catch(Exception $e) {
            echo '<div class="error-message">Failed to load project details.</div>';
        }
        ?>
    </div>
</section>

<?php
// Add inline JavaScript for image gallery
$inline_js = "
function initImageGallery() {
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.gallery-thumbnail');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const imageUrl = this.getAttribute('data-image');
                mainImage.src = '../backend/' + imageUrl;
                
                // Update active thumbnail
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
}
";

// Include footer
require_once 'includes/footer.php';
?>
