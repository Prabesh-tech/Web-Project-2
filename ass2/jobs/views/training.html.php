<section class="training-section page-section">
    <div class="section-inner">
        <div class="about-copy">
            <span class="eyebrow">Training Programs</span>
            <h2>Advance Your Skills with Professional Training</h2>
            <p>Explore our comprehensive training programs designed to help you master in-demand skills and land your dream job.</p>
        </div>

        <div class="training-grid">
            <?php foreach ($courses as $course): ?>
                <div class="training-card">
                    <div class="training-image">
                        <img src="<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
                        <span class="duration-badge"><?= htmlspecialchars($course['duration']) ?></span>
                    </div>
                    <div class="training-content">
                        <h3><?= htmlspecialchars($course['title']) ?></h3>
                        <p class="training-description"><?= htmlspecialchars($course['description']) ?></p>
                        
                        <?php if (isset($course['stats'])): ?>
                            <div class="training-stats">
                                <?php foreach ($course['stats'] as $stat): ?>
                                    <div class="stat-item">
                                        <strong><?= htmlspecialchars($stat['value']) ?></strong>
                                        <span><?= htmlspecialchars($stat['label']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="training-features">
                            <?php foreach ($course['features'] as $feature): ?>
                                <div class="feature-item">
                                    <span class="checkmark">✓</span>
                                    <span><?= htmlspecialchars($feature) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="training-actions">
                            <button type="button" class="btn-enroll" onclick="openRegistration(<?= htmlspecialchars($course['id']) ?>, '<?= htmlspecialchars($course['title']) ?>')">Enroll Now</button>
                            <a href="#" class="btn-details">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Registration Modal -->
<div id="registrationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeRegistration()">&times;</span>
        <h2>Registration Form</h2>
        <form id="registrationForm" method="POST" action="">
            <input type="hidden" id="courseId" name="course_id" value="">
            
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="full_name" placeholder="Enter your name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" required>
            </div>

            <div class="form-group">
                <label for="courseName">Choose Course</label>
                <input type="text" id="courseName" name="course_name" readonly style="background-color: #f5f5f5;">
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Enter your message" rows="4"></textarea>
            </div>

            <div class="form-group checkbox">
                <input type="checkbox" id="captcha" name="captcha" required>
                <label for="captcha">I'm not a robot</label>
            </div>

            <button type="submit" class="btn-apply">Apply</button>
        </form>
    </div>
</div>

<script>
function openRegistration(courseId, courseName) {
    document.getElementById('courseId').value = courseId;
    document.getElementById('courseName').value = courseName;
    document.getElementById('registrationModal').style.display = 'block';
}

function closeRegistration() {
    document.getElementById('registrationModal').style.display = 'none';
    document.getElementById('registrationForm').reset();
}

window.onclick = function(event) {
    const modal = document.getElementById('registrationModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>
