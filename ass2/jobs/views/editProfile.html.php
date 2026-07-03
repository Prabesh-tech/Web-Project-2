<?php
/**
 * Edit Profile View
 * Form for updating user profile information
 */
$user = $user ?? ['id' => 0, 'username' => '', 'email' => ''];
$error = $error ?? '';
$success = $success ?? false;
?>

<div class="edit-profile-container">
    <h1>Edit Profile</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">Profile updated successfully!</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="edit-form" enctype="multipart/form-data">
        <!-- Personal Information -->
        <fieldset>
            <legend>Personal Information</legend>

            <div class="form-group">
                <label for="username">Username (cannot be changed)</label>
                <input type="text" id="username" disabled value="<?= htmlspecialchars($user['username']) ?>" class="form-control">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" 
                        value="<?= htmlspecialchars($user['firstName'] ?? '') ?>" placeholder="First name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" 
                        value="<?= htmlspecialchars($user['lastName'] ?? '') ?>" placeholder="Last name" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="dateOfBirth">Date of Birth</label>
                    <div class="date-input-wrapper">
                        <input type="date" id="dateOfBirth" name="dateOfBirth" 
                            value="<?= htmlspecialchars($user['dateOfBirth'] ?? '') ?>" class="form-control">
                        <span class="calendar-icon">📅</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" class="form-control">
                        <option value="">Select Gender</option>
                        <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
            </div>
        </fieldset>

        <!-- Contact Information -->
        <fieldset>
            <legend>Contact Information</legend>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required 
                    value="<?= htmlspecialchars($user['email']) ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" 
                    value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+977-XXXXXXXXXX" class="form-control">
            </div>
        </fieldset>

        <!-- Address Information -->
        <fieldset>
            <legend>Current Address</legend>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" 
                    value="<?= htmlspecialchars($user['address'] ?? '') ?>" placeholder="Street address" class="form-control">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city">City/Municipality</label>
                    <input type="text" id="city" name="city" 
                        value="<?= htmlspecialchars($user['city'] ?? '') ?>" placeholder="City" class="form-control">
                </div>
                <div class="form-group">
                    <label for="province">Province/State</label>
                    <input type="text" id="province" name="province" 
                        value="<?= htmlspecialchars($user['province'] ?? '') ?>" placeholder="Province" class="form-control">
                </div>
                <div class="form-group">
                    <label for="zipCode">Zip Code</label>
                    <input type="text" id="zipCode" name="zipCode" 
                        value="<?= htmlspecialchars($user['zipCode'] ?? '') ?>" placeholder="Zip code" class="form-control">
                </div>
            </div>
        </fieldset>

        <!-- CV/Resume Upload -->
        <fieldset>
            <legend>CV & Documents</legend>

            <div class="document-group">
                <label for="profileCv">Upload CV/Resume (PDF, DOC, DOCX)</label>
                <div class="file-upload-box" id="profileCvUploadBox" data-input="profileCv">
                    <input type="file" id="profileCv" name="cv" accept=".pdf,.doc,.docx" class="file-input">
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
                <?php if (!empty($user['cvFile'])): ?>
                    <p class="file-info">✓ Current file: <?= htmlspecialchars($user['cvFile']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="bio">Bio/Summary</label>
                <textarea id="bio" name="bio" rows="4" placeholder="Write a brief summary about yourself" class="form-control"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>
        </fieldset>

        <!-- Employment History -->
        <fieldset>
            <legend>Employment History</legend>

            <div class="employment-section">
                <h3>Most Recent Position</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="recentCompany">Company Name</label>
                        <input type="text" id="recentCompany" name="recentCompany" 
                            value="<?= htmlspecialchars($user['recentCompany'] ?? '') ?>" placeholder="Company name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="recentJobTitle">Job Title</label>
                        <input type="text" id="recentJobTitle" name="recentJobTitle" 
                            value="<?= htmlspecialchars($user['recentJobTitle'] ?? '') ?>" placeholder="Job title" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="recentStartDate">Employment Start Date</label>
                        <div class="date-input-wrapper">
                            <input type="month" id="recentStartDate" name="recentStartDate" 
                                value="<?= htmlspecialchars($user['recentStartDate'] ?? '') ?>" class="form-control">
                            <span class="calendar-icon">📅</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="recentEndDate">Employment End Date (Leave blank if current)</label>
                        <div class="date-input-wrapper">
                            <input type="month" id="recentEndDate" name="recentEndDate" 
                                value="<?= htmlspecialchars($user['recentEndDate'] ?? '') ?>" class="form-control">
                            <span class="calendar-icon">📅</span>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Emergency Contact -->
        <fieldset>
            <legend>Emergency Contact</legend>

            <div class="form-row">
                <div class="form-group">
                    <label for="emergencyName">Contact Name</label>
                    <input type="text" id="emergencyName" name="emergencyName" 
                        value="<?= htmlspecialchars($user['emergencyName'] ?? '') ?>" placeholder="Full name" class="form-control">
                </div>
                <div class="form-group">
                    <label for="emergencyRelation">Relationship</label>
                    <input type="text" id="emergencyRelation" name="emergencyRelation" 
                        value="<?= htmlspecialchars($user['emergencyRelation'] ?? '') ?>" placeholder="e.g., Spouse, Parent" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="emergencyPhone">Phone Number</label>
                <input type="tel" id="emergencyPhone" name="emergencyPhone" 
                    value="<?= htmlspecialchars($user['emergencyPhone'] ?? '') ?>" placeholder="+977-XXXXXXXXXX" class="form-control">
            </div>
        </fieldset>

        <!-- Password Change -->
        <fieldset>
            <legend>Change Password (optional)</legend>
            
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input type="password" id="currentPassword" name="currentPassword" 
                    placeholder="Leave blank to skip password change" class="form-control">
            </div>

            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="newPassword" 
                    placeholder="Leave blank to skip password change" class="form-control">
                <small>Must be at least 6 characters</small>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" 
                    placeholder="Re-enter new password" class="form-control">
            </div>
        </fieldset>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="profile.php?id=<?= intval($user['id']) ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.edit-profile-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 30px;
}

.edit-profile-container h1 {
    color: #f1f5f9;
    margin-bottom: 30px;
    font-size: 2rem;
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

.edit-form {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.edit-form fieldset {
    border: 2px solid #1f2937;
    background: #e5e7eb;
    border-radius: 8px;
    padding: 20px;
}

.edit-form legend {
    color: #1f2937;
    background: #1f2937;
    color: #ffffff;
    padding: 4px 12px;
    border-radius: 4px;
    display: inline-block;
    font-weight: 600;
    font-size: 0.95rem;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group label {
    color: #1f2937;
    font-weight: 600;
    font-size: 0.9rem;
}

.form-control {
    padding: 10px 12px;
    background: #0f172a;
    color: #f1f5f9;
    border: 1px solid #374151;
    border-radius: 6px;
    font-family: inherit;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-control:disabled {
    background: #1f2937;
    color: #9ca3af;
    cursor: not-allowed;
}

.employment-section {
    background: rgba(31, 41, 55, 0.5);
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 15px;
}

.employment-section h3 {
    color: #cbd5e1;
    margin: 0 0 15px 0;
    font-size: 1rem;
}

.document-group {
    margin-bottom: 20px;
}

.document-group label {
    color: #1f2937;
    font-weight: 600;
    display: block;
    margin-bottom: 10px;
}

.file-upload-box {
    position: relative;
    border: 2px dashed #374151;
    border-radius: 8px;
    padding: 25px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: rgba(31, 41, 55, 0.3);
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
    font-size: 2rem;
    display: block;
    margin-bottom: 8px;
}

.file-upload-placeholder p {
    color: #f1f5f9;
    margin: 8px 0;
    font-weight: 500;
}

.file-upload-placeholder small {
    color: #9ca3af;
    display: block;
}

.file-info {
    color: #10b981;
    font-size: 0.85rem;
    margin-top: 8px;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    border: none;
    text-decoration: none;
    display: inline-block;
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
    .edit-profile-container {
        padding: 15px;
    }

    .edit-profile-container h1 {
        font-size: 1.5rem;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}

.date-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.date-input-wrapper .form-control[type="date"],
.date-input-wrapper .form-control[type="month"] {
    padding-right: 40px;
    cursor: pointer;
}

.date-input-wrapper .form-control[type="date"]::-webkit-calendar-picker-indicator,
.date-input-wrapper .form-control[type="month"]::-webkit-calendar-picker-indicator {
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
</style>
