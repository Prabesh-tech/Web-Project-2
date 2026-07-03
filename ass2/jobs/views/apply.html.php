<?php
$job = $job ?? null;
$jobId = intval($jobId ?? $job['id'] ?? $_GET['jobId'] ?? $_POST['jobId'] ?? 0);
$error = $error ?? '';
$success = $success ?? '';
$formData = $formData ?? [];
$step = intval($_GET['step'] ?? $_POST['step'] ?? 1);
if ($step < 1 || $step > 4) {
    $step = 1;
}

$jobTitle = htmlspecialchars($job['title'] ?? 'Job Application');
$user = $_SESSION['user'] ?? [];
?>

<div class="application-container">
    <h1>Apply for <?= $jobTitle ?></h1>
    <p class="application-description">Complete the application process. Your information will be saved.</p>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Step Indicator -->
    <div class="steps-indicator">
        <div class="step <?= $step === 1 ? 'active' : 'completed' ?>">
            <span>1</span>
            <p>Personal Info</p>
        </div>
        <div class="step-line <?= $step > 1 ? 'completed' : '' ?>"></div>
        <div class="step <?= $step === 2 ? 'active' : ($step > 2 ? 'completed' : '') ?>">
            <span>2</span>
            <p>Contact & Address</p>
        </div>
        <div class="step-line <?= $step > 2 ? 'completed' : '' ?>"></div>
        <div class="step <?= $step === 3 ? 'active' : ($step > 3 ? 'completed' : '') ?>">
            <span>3</span>
            <p>Employment History</p>
        </div>
        <div class="step-line <?= $step > 3 ? 'completed' : '' ?>"></div>
        <div class="step <?= $step === 4 ? 'active' : 'pending' ?>">
            <span>4</span>
            <p>Documents</p>
        </div>
    </div>

    <form method="POST" class="application-form" enctype="multipart/form-data">
        <input type="hidden" name="jobId" value="<?= htmlspecialchars($jobId) ?>">
        <input type="hidden" name="step" value="<?= htmlspecialchars($step) ?>">

        <!-- Step 1: Personal Information -->
        <?php if ($step === 1): ?>
            <fieldset class="form-section">
                <legend>Personal Information</legend>

                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" required 
                            value="<?= htmlspecialchars($formData['firstName'] ?? '') ?>" placeholder="Enter your first name">
                    </div>
                    <div class="form-group">
                        <label for="middleName">Middle Name</label>
                        <input type="text" id="middleName" name="middleName" 
                            value="<?= htmlspecialchars($formData['middleName'] ?? '') ?>" placeholder="Middle name (optional)">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" required 
                            value="<?= htmlspecialchars($formData['lastName'] ?? '') ?>" placeholder="Enter your last name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="dob">Date of Birth *</label>
                        <div class="date-input-wrapper">
                            <input type="date" id="dob" name="dob" required 
                                value="<?= htmlspecialchars($formData['dob'] ?? '') ?>">
                            <span class="calendar-icon">📅</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="male" <?= ($formData['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= ($formData['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= ($formData['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>
            </fieldset>
        <?php endif; ?>

        <!-- Step 2: Contact & Address -->
        <?php if ($step === 2): ?>
            <fieldset class="form-section">
                <legend>Contact Information</legend>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required 
                            value="<?= htmlspecialchars($formData['phone'] ?? '') ?>" placeholder="+977-XXXXXXXXXX">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required 
                            value="<?= htmlspecialchars($formData['email'] ?? $user['email'] ?? '') ?>" placeholder="your@email.com">
                    </div>
                </div>

                <legend>Current Address</legend>
                <div class="form-group">
                    <label for="currentAddress">Address *</label>
                    <input type="text" id="currentAddress" name="currentAddress" required 
                        value="<?= htmlspecialchars($formData['currentAddress'] ?? '') ?>" placeholder="Street address">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="currentCity">City/Municipality *</label>
                        <input type="text" id="currentCity" name="currentCity" required 
                            value="<?= htmlspecialchars($formData['currentCity'] ?? '') ?>" placeholder="City">
                    </div>
                    <div class="form-group">
                        <label for="currentProvince">Province/State</label>
                        <input type="text" id="currentProvince" name="currentProvince" 
                            value="<?= htmlspecialchars($formData['currentProvince'] ?? '') ?>" placeholder="Province">
                    </div>
                    <div class="form-group">
                        <label for="currentZip">Zip Code</label>
                        <input type="text" id="currentZip" name="currentZip" 
                            value="<?= htmlspecialchars($formData['currentZip'] ?? '') ?>" placeholder="Zip code">
                    </div>
                </div>
            </fieldset>
        <?php endif; ?>

        <!-- Step 3: Employment History -->
        <?php if ($step === 3): ?>
            <fieldset class="form-section">
                <legend>Employment History</legend>

                <div class="employment-section">
                    <h3>Most Recent Employer</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="recentEmployer">Company Name</label>
                            <input type="text" id="recentEmployer" name="recentEmployer" 
                                value="<?= htmlspecialchars($formData['recentEmployer'] ?? '') ?>" placeholder="Company name">
                        </div>
                        <div class="form-group">
                            <label for="recentJobTitle">Job Title</label>
                            <input type="text" id="recentJobTitle" name="recentJobTitle" 
                                value="<?= htmlspecialchars($formData['recentJobTitle'] ?? '') ?>" placeholder="Your job title">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="recentStartDate">From (MM/YYYY)</label>
                            <div class="date-input-wrapper">
                                <input type="month" id="recentStartDate" name="recentStartDate" 
                                    value="<?= htmlspecialchars($formData['recentStartDate'] ?? '') ?>">
                                <span class="calendar-icon">📅</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="recentEndDate">To (MM/YYYY)</label>
                            <div class="date-input-wrapper">
                                <input type="month" id="recentEndDate" name="recentEndDate" 
                                    value="<?= htmlspecialchars($formData['recentEndDate'] ?? '') ?>">
                                <span class="calendar-icon">📅</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="recentResponsibilities">Responsibilities</label>
                        <textarea id="recentResponsibilities" name="recentResponsibilities" rows="4" 
                            placeholder="Describe your main responsibilities">><?= htmlspecialchars($formData['recentResponsibilities'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="employment-section">
                    <h3>Previous Employer (Optional)</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="previousEmployer">Company Name</label>
                            <input type="text" id="previousEmployer" name="previousEmployer" 
                                value="<?= htmlspecialchars($formData['previousEmployer'] ?? '') ?>" placeholder="Company name">
                        </div>
                        <div class="form-group">
                            <label for="previousJobTitle">Job Title</label>
                            <input type="text" id="previousJobTitle" name="previousJobTitle" 
                                value="<?= htmlspecialchars($formData['previousJobTitle'] ?? '') ?>" placeholder="Your job title">
                        </div>
                    </div>
                </div>
            </fieldset>
        <?php endif; ?>

        <!-- Step 4: Documents -->
        <?php if ($step === 4): ?>
            <fieldset class="form-section">
                <legend>Upload Documents</legend>

                <div class="document-group">
                    <label for="cv">CV/Resume (PDF, DOC, DOCX) *</label>
                    <div class="file-upload-box" id="cvUploadBox" data-input="cv">
                        <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" required class="file-input">
                        <div class="file-upload-placeholder">
                            <span class="upload-icon">📄</span>
                            <p>Click to upload or drag and drop</p>
                            <small>PDF, DOC, or DOCX (Max 5MB)</small>
                            <button type="button" class="upload-btn">Choose File</button>
                        </div>
                        <div class="file-upload-preview" style="display:none;">
                            <span class="preview-icon">✓</span>
                            <p class="file-name"></p>
                            <small class="file-size"></small>
                            <button type="button" class="upload-btn-change">Change File</button>
                        </div>
                    </div>
                </div>

                <div class="document-group">
                    <label for="coverLetter">Cover Letter (PDF, DOC, DOCX)</label>
                    <div class="file-upload-box" id="coverLetterUploadBox" data-input="coverLetter">
                        <input type="file" id="coverLetter" name="coverLetter" accept=".pdf,.doc,.docx" class="file-input">
                        <div class="file-upload-placeholder">
                            <span class="upload-icon">📝</span>
                            <p>Click to upload or drag and drop</p>
                            <small>PDF, DOC, or DOCX (Max 5MB)</small>
                            <button type="button" class="upload-btn">Choose File</button>
                        </div>
                        <div class="file-upload-preview" style="display:none;">
                            <span class="preview-icon">✓</span>
                            <p class="file-name"></p>
                            <small class="file-size"></small>
                            <button type="button" class="upload-btn-change">Change File</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="additionalInfo">Additional Information</label>
                    <textarea id="additionalInfo" name="additionalInfo" rows="4" 
                        placeholder="Any additional information you'd like to share with the employer"><?= htmlspecialchars($formData['additionalInfo'] ?? '') ?></textarea>
                </div>
            </fieldset>
        <?php endif; ?>

        <!-- Form Actions -->
        <div class="form-actions">
            <?php if ($step > 1): ?>
                <button type="submit" name="action" value="previous" class="btn btn-secondary">← Previous</button>
            <?php endif; ?>
            
            <?php if ($step < 4): ?>
                <button type="submit" name="action" value="next" class="btn btn-primary">Next →</button>
            <?php else: ?>
                <button type="submit" name="action" value="submit" class="btn btn-primary">Submit Application</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<style>
.application-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 30px;
}

.application-container h1 {
    color: #f1f5f9;
    margin-bottom: 10px;
    font-size: 2rem;
}

.application-description {
    color: #cbd5e1;
    margin-bottom: 30px;
    font-size: 0.95rem;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: #86efac;
    border-left-color: #10b981;
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: #fca5a5;
    border-left-color: #ef4444;
}

.steps-indicator {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 40px;
    padding: 30px 20px;
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border: 1px solid #374151;
    border-radius: 12px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    flex: 1;
    position: relative;
}

.step span {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #374151;
    color: #9ca3af;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.1rem;
}

.step.active span {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
}

.step.completed span {
    background: #10b981;
    color: #ffffff;
}

.step p {
    color: #cbd5e1;
    font-size: 0.8rem;
    margin: 0;
    text-align: center;
}

.step-line {
    height: 2px;
    background: #374151;
    flex: 1;
    margin: 0 10px;
}

.step-line.completed {
    background: #10b981;
}

.application-form {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border: 1px solid #374151;
    border-radius: 12px;
    padding: 30px;
}

.form-section {
    border: none;
    padding: 0;
    margin-bottom: 30px;
}

.form-section legend {
    color: #f1f5f9;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #374151;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    color: #f1f5f9;
    font-weight: 600;
    font-size: 0.95rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px;
    background: #0f172a;
    color: #f1f5f9;
    border: 1px solid #374151;
    border-radius: 6px;
    font-family: inherit;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.employment-section {
    background: rgba(31, 41, 55, 0.5);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.employment-section h3 {
    color: #cbd5e1;
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 1rem;
}

.document-group {
    margin-bottom: 25px;
}

.document-group label {
    color: #f1f5f9;
    font-weight: 600;
    display: block;
    margin-bottom: 10px;
}

.file-upload-box {
    position: relative;
    border: 2px dashed #374151;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: rgba(31, 41, 55, 0.5);
}

.file-upload-box:hover {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.05);
}

.file-input {
    display: none;
}

.file-upload-placeholder {
    pointer-events: none;
}

.upload-icon {
    font-size: 2.5rem;
    display: block;
    margin-bottom: 10px;
}

.file-upload-placeholder p {
    color: #f1f5f9;
    margin: 10px 0;
    font-weight: 500;
}

.file-upload-placeholder small {
    color: #9ca3af;
    display: block;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    border: none;
    transition: all 0.3s ease;
    flex: 1;
}

.btn-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
}

.btn-secondary {
    background: #374151;
    color: #f1f5f9;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .application-container {
        padding: 15px;
    }

    .steps-indicator {
        flex-direction: column;
        gap: 15px;
    }

    .step-line {
        display: none;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .application-form {
        padding: 20px;
    }

    .form-actions {
        flex-direction: column;
    }
}

.date-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.date-input-wrapper input[type="date"],
.date-input-wrapper input[type="month"] {
    padding-right: 40px;
    cursor: pointer;
    font-size: 0.95rem;
}

.date-input-wrapper input[type="date"]::-webkit-calendar-picker-indicator,
.date-input-wrapper input[type="month"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    filter: invert(0.8);
}

.calendar-icon {
    position: absolute;
    right: 12px;
    pointer-events: none;
    font-size: 1.2rem;
    opacity: 0.7;
}

.upload-btn {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 12px;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
}

.upload-btn-change {
    background: #3b82f6;
    color: #ffffff;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 12px;
    transition: all 0.3s ease;
    font-size: 0.85rem;
}

.upload-btn-change:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

.file-upload-box.drag-over {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.15);
}

.file-upload-preview {
    padding: 20px;
    text-align: center;
}

.preview-icon {
    font-size: 2rem;
    display: block;
    color: #10b981;
    margin-bottom: 12px;
}

.file-name {
    color: #f1f5f9;
    font-weight: 600;
    margin: 8px 0;
    word-break: break-all;
}

.file-size {
    color: #9ca3af;
    display: block;
    font-size: 0.85rem;
}
</style>

<script>
function setupFileUpload(boxId, inputId) {
    const uploadBox = document.getElementById(boxId);
    const fileInput = document.getElementById(inputId);
    const uploadBtn = uploadBox.querySelector('.upload-btn');
    const changeBtn = uploadBox.querySelector('.upload-btn-change');
    const placeholder = uploadBox.querySelector('.file-upload-placeholder');
    const preview = uploadBox.querySelector('.file-upload-preview');
    const fileName = uploadBox.querySelector('.file-name');
    const fileSize = uploadBox.querySelector('.file-size');

    // Click to upload
    uploadBox.addEventListener('click', function(e) {
        if (e.target !== fileInput) {
            fileInput.click();
        }
    });

    uploadBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.click();
    });

    changeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.click();
    });

    // Handle file selection
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            displayFileInfo(file, fileName, fileSize, placeholder, preview);
        }
    });

    // Drag and drop
    uploadBox.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadBox.classList.add('drag-over');
    });

    uploadBox.addEventListener('dragleave', function() {
        uploadBox.classList.remove('drag-over');
    });

    uploadBox.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadBox.classList.remove('drag-over');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            const file = files[0];
            displayFileInfo(file, fileName, fileSize, placeholder, preview);
        }
    });
}

function displayFileInfo(file, fileNameEl, fileSizeEl, placeholder, preview) {
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        return;
    }

    // Validate file type
    const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (!validTypes.includes(file.type)) {
        alert('Only PDF, DOC, and DOCX files are allowed');
        return;
    }

    fileNameEl.textContent = file.name;
    fileSizeEl.textContent = formatFileSize(file.size);
    placeholder.style.display = 'none';
    preview.style.display = 'block';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Initialize file uploads when document is ready
document.addEventListener('DOMContentLoaded', function() {
    setupFileUpload('cvUploadBox', 'cv');
    setupFileUpload('coverLetterUploadBox', 'coverLetter');
    setupFileUpload('profileCvUploadBox', 'profileCv');
});
</script>