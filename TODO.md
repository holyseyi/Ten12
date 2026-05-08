# TODO: Fix All Issues Found in Ten12 Portfolio

## Critical Issues (Must Fix)

### 1. header.php - Multiple HTML Tag Errors
- [x] Fix anchor tags `<a href="#about"` - should be proper anchor elements

### 2. footer.php - Missing Closing Tags
- [x] Fixed anchor tags to use proper href anchors

### 3. index.php - Anchor Tag Error
- [x] Fixed View Projects button to link to projects.php page

### 4. project-detail.php - PHP Errors
- [x] Fixed `$project->table_name` - private property access, use direct table name
- [x] Fixed duplicate inline JS at end of file
- [x] Fixed related projects query to properly read the category field instead of accessing private property

### 5. Create Missing API File
- [x] Created backend/api/projects.php - Project CRUD API endpoint

### 6. Security Issues
- [x] Contact model - add input sanitization before insert

## Medium Priority Issues

### 7. CSS Improvements
- [x] Add missing placeholder-thumbnail styles in admin.css
- [x] Add error/success message styles

### 8. JavaScript Fixes
- [x] Fix project thumbnail path in main.js (should be relative to backend/uploads)
- [x] Fix project link to use .php extension

## Completed: 2025-01-20
