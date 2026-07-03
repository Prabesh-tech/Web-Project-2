<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';

$jobId = intval($_GET['id'] ?? 0);
$job = null;
$error = '';
$success = '';
$action = strtolower($_GET['action'] ?? '');

if (!empty($_SESSION['job_message'])) {
    $success = $_SESSION['job_message'];
    unset($_SESSION['job_message']);
}

try {
    if ($jobId <= 0) {
        throw new Exception('Job not found.');
    }

    /** @var JobController $jobController */
    $jobController = new JobController($pdo);
    $job = $jobController->getJobById($jobId);

    if (!$job) {
        throw new Exception('Job not found.');
    }

    $canManageJob = false;
    $currentUserId = intval($_SESSION['user']['id'] ?? 0);
    $currentUserRole = intval($_SESSION['user']['role'] ?? 0);
    $isAdminOrSuper = in_array($currentUserRole, [2, 3], true);

    if ($job['postedBy'] !== null && intval($job['postedBy']) === $currentUserId && $currentUserRole === 1) {
        $canManageJob = true;
    }

    if ($isAdminOrSuper) {
        $canManageJob = true;
    }

    if (in_array($action, ['archive', 'unarchive', 'delete'], true) || in_array($action, ['shortlist', 'reject'], true)) {
        if (empty($_SESSION['user'])) {
            header('Location: login.php?role=employer');
            exit;
        }

        if (!$canManageJob) {
            throw new Exception('Only the job owner (employer) or an admin can manage this job.');
        }

        if (in_array($action, ['shortlist', 'reject'], true)) {
            $applicationId = intval($_GET['applicationId'] ?? 0);
            if ($applicationId <= 0) {
                throw new Exception('Application not specified.');
            }

            $application = $jobController->getApplicationById($applicationId);
            if (!$application || intval($application['jobId']) !== $jobId) {
                throw new Exception('Invalid application selected.');
            }

            $newStatus = $action === 'shortlist' ? 'Shortlisted' : 'Rejected';
            $jobController->updateApplicationStatus($applicationId, $newStatus);
            $_SESSION['job_message'] = 'Application has been marked as ' . strtolower($newStatus) . '.';
            header('Location: job.php?id=' . $jobId);
            exit;
        }

        if ($action === 'delete') {
            $jobController->deleteJob($jobId);
            $_SESSION['job_message'] = 'Job deleted successfully.';
            header('Location: index.php');
            exit;
        }

        $jobController->setJobArchived($jobId, $action === 'archive');
        $_SESSION['job_message'] = $action === 'archive' ? 'Job archived successfully.' : 'Job restored successfully.';
        header('Location: job.php?id=' . $jobId);
        exit;
    }

    $jobRequirements = $jobController->getJobRequirements($jobId);
    $jobApplications = $canManageJob ? $jobController->getApplicationsForJob($jobId) : [];
} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = $job ? htmlspecialchars($job['title']) . ' - Prabesh Job' : 'Job details - Prabesh Job';
$breadcrumbs = [
    'Home' => 'index.php',
    'Job Details' => '#',
];

ob_start();
require __DIR__ . '/../views/job.html.php';
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/layout-main.php';
