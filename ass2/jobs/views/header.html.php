<?php
/**
 * Header View
 * Variables passed from includes/header.php:
 * @var string $homeUrl - Home page URL
 * @var string $categoriesHTML - Categories navigation HTML
 * @var string $navItemsHTML - Navigation items HTML
 * @var string $actionButtonHTML - Action button (More) HTML
 * @var string $userSectionHTML - User section HTML
 */
?>
<header class="site-header">
    <div class="header-inner">

        <!-- LOGO -->
        <a href="<?= htmlspecialchars($homeUrl) ?>" class="logo">
            <img src="assets/images/Image3.png" alt="Prabesh Jobs" class="logo-img">
        </a>

        <input type="checkbox" id="nav-toggle" class="nav-toggle-input">
        <label for="nav-toggle" class="nav-toggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </label>

        <!-- NAV -->
        <nav class="main-nav">
            <div class="nav-dropdown">
                <button class="nav-link nav-dropdown-toggle" type="button">Job Categories</button>
                <div class="nav-dropdown-menu nav-dropdown-menu-columns">
                    <?= $categoriesHTML ?>
                </div>
            </div>

            <div class="nav-dropdown">
                <button class="nav-link nav-dropdown-toggle" type="button">Services</button>
                <div class="nav-dropdown-menu">
                    <a href="services.php#vacancy-announcement">Vacancy Announcement & Management Tools</a>
                    <a href="services.php#recruitment-services">Recruitment Services</a>
                    <a href="services.php#outsourcing-services">Outsourcing Tools & Services</a>
                    <a href="services.php#hr-consulting">Human Resource Consulting</a>
                </div>
            </div>
            <a href="<?= htmlspecialchars($homeUrl) ?>" class="nav-link">Home</a>
            <a href="careers.php" class="nav-link">Career Advice</a>
            <a href="blogs.php" class="nav-link">Blogs</a>
            <a href="training.php" class="nav-link">Training</a>
            <a href="about.php" class="nav-link">About Us</a>
            <a href="contact.php" class="nav-link">Contact Us</a>

            <?= $navItemsHTML ?>
        </nav>

        <div class="header-actions-right">
            <?= $actionButtonHTML ?>
            <div class="user-tools">
                <?= $userSectionHTML ?>
            </div>
        </div>
    </div>
</header>
