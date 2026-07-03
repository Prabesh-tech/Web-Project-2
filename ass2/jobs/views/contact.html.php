<?php
$name = $name ?? '';
$email = $email ?? '';
$subject = $subject ?? '';
$message = $message ?? '';
$error = $error ?? '';
$success = $success ?? '';
?>

<section class="contact-section page-section contact-page">
    <div class="section-inner">
        <div class="about-grid contact-grid">
            <div class="about-copy">
                <span class="eyebrow">Contact Us</span>
                <h2>Get in touch with the Prabesh Jobs team</h2>
                <p>If you have a question, need help with your account, or want to list a job opportunity, we are here to help.</p>
                <div class="contact-info">
                    <p><strong>Email:</strong> support@prabeshjobs.com</p>
                    <p><strong>Phone:</strong> +977 01 2345678</p>
                    <p><strong>Location:</strong> Kathmandu, Nepal</p>
                </div>
            </div>

            <div class="contact-form">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error">
                        <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <strong>Success:</strong> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form action="contact.php" method="post">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Your name" value="<?= htmlspecialchars($name) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Your email" value="<?= htmlspecialchars($email) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="Subject (optional)" value="<?= htmlspecialchars($subject) ?>">
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="How can we help?" rows="5" required><?= htmlspecialchars($message) ?></textarea>
                    </div>
                    <button type="submit" class="btn-secondary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>
