<?php
$services = $services ?? [];
?>

<section class="services-hero">
    <div class="services-hero-content">
        <h1><?= htmlspecialchars($formTitle) ?></h1>
        <p><?= htmlspecialchars($formDescription) ?></p>
    </div>
    <div class="services-hero-image">
        <img src="assets/images/Image3.png" alt="Services illustration" class="hero-image">
    </div>
</section>

<section class="services-grid">
    <?php foreach ($services as $service): ?>
        <article id="<?= htmlspecialchars($service['id']) ?>" class="service-card">
            <div class="service-card-header">
                <span class="service-icon"><?= $service['icon'] ?></span>
                <h3><?= htmlspecialchars($service['title']) ?></h3>
            </div>
            <p class="service-description"><?= htmlspecialchars($service['description']) ?></p>
            <div class="service-card-tags">
                <?php foreach ($service['tags'] as $tag): ?>
                    <span class="service-tag"><?= htmlspecialchars($tag) ?></span>
                <?php endforeach; ?>
            </div>
            <a href="#" class="service-link">Learn More</a>
        </article>
    <?php endforeach; ?>
</section>
