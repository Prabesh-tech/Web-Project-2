<?php
/**
 * User Profile View
 * Displays user profile information and applications
 */
?>

<div class="profile-container">
    <div class="profile-header">
        <h1><?= htmlspecialchars($user['username']) ?></h1>
        <p class="member-since">Member since <?= date('M d, Y', strtotime($user['createdAt'])) ?></p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">Profile updated successfully!</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="profile-grid">
        <section class="profile-section">
            <h2>Profile Information</h2>
            <dl>
                <dt>Username:</dt>
                <dd><?= htmlspecialchars($user['username']) ?></dd>
                
                <dt>Email:</dt>
                <dd><?= htmlspecialchars($user['email']) ?></dd>
                
                <dt>Account Type:</dt>
                <dd>
                    <?php 
                    $role = intval($user['isAdmin'] ?? 0);
                    echo $role === 2 ? 'Super Admin' : ($role === 1 ? 'Admin' : 'Regular User');
                    ?>
                </dd>
            </dl>

            <?php if ($isOwnProfile): ?>
                <a href="editProfile.php" class="btn btn-primary">Edit Profile</a>
                <a href="changePassword.php" class="btn btn-secondary">Change Password</a>
            <?php endif; ?>
        </section>

        <section class="profile-section">
            <h2>Account Statistics</h2>
            <dl>
                <dt>Profile Completeness:</dt>
                <dd><?= intval($profileCompleteness) ?>%</dd>
                
                <dt>Member Status:</dt>
                <dd>Active</dd>
            </dl>
        </section>
    </div>
</div>

<style>
.profile-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.profile-header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 20px;
}

.member-since {
    color: #666;
    font-size: 14px;
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.profile-section {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.profile-section h2 {
    font-size: 18px;
    margin-top: 0;
    margin-bottom: 15px;
}

.profile-section dl {
    list-style: none;
    padding: 0;
}

.profile-section dt {
    font-weight: bold;
    margin-top: 10px;
    color: #333;
}

.profile-section dd {
    margin-left: 0;
    margin-bottom: 5px;
    color: #666;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    margin-right: 10px;
    border-radius: 4px;
    text-decoration: none;
    border: none;
    cursor: pointer;
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
