<?php
/**
 * Edit Profile View
 * Form for updating user profile information
 */
?>

<div class="edit-profile-container">
    <h1>Edit Profile</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">Profile updated successfully!</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="edit-form">
        <fieldset>
            <legend>Profile Information</legend>

            <div class="form-group">
                <label for="username">Username (cannot be changed)</label>
                <input type="text" id="username" disabled value="<?= htmlspecialchars($user['username']) ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required 
                    value="<?= htmlspecialchars($user['email']) ?>" class="form-control">
            </div>
        </fieldset>

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
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.edit-profile-container h1 {
    margin-bottom: 30px;
    font-size: 28px;
}

.edit-form fieldset {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background: #f9f9f9;
}

.edit-form legend {
    padding: 0 10px;
    font-weight: bold;
    font-size: 16px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
}

.form-group input[disabled] {
    background-color: #e9ecef;
    cursor: not-allowed;
}

.form-group small {
    display: block;
    margin-top: 4px;
    color: #666;
    font-size: 13px;
}

.form-actions {
    margin-top: 30px;
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.alert {
    padding: 12px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
