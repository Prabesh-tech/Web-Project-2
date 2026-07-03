<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';

$jobId = intval($_GET['jobId'] ?? $_POST['jobId'] ?? 0);
if ($jobId > 0) {
    $_SESSION['apply_job_id'] = $jobId;
} elseif (!empty($_SESSION['apply_job_id'])) {
    $jobId = intval($_SESSION['apply_job_id']);
}

$step = intval($_GET['step'] ?? $_POST['step'] ?? 1);
if ($step < 1 || $step > 4) {
    $step = 1;
}

$error = '';
$success = '';

$jobController = new JobController($pdo);
$job = null;
$formData = $_SESSION['apply_form_data'] ?? [];

try {
    if ($jobId <= 0) {
        throw new Exception('Job not found.');
    }

    $job = $jobController->getJobById($jobId);
    if (!$job) {
        throw new Exception('Job not found.');
    }

    if (empty($_SESSION['user']['id'])) {
        $redirect = 'apply.php?jobId=' . $jobId . '&step=' . $step;
        header('Location: login.php?redirect=' . urlencode($redirect));
        exit;
    }

    $userId = intval($_SESSION['user']['id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($jobId > 0) {
            $_SESSION['apply_job_id'] = $jobId;
        }

        $action = $_POST['action'] ?? 'next';

        if ($step === 1) {
            $formData = array_merge($formData, [
                'firstName' => trim($_POST['firstName'] ?? ''),
                'middleName' => trim($_POST['middleName'] ?? ''),
                'lastName' => trim($_POST['lastName'] ?? ''),
                'dob' => trim($_POST['dob'] ?? ''),
                'gender' => trim($_POST['gender'] ?? ''),
            ]);
        } elseif ($step === 2) {
            $formData = array_merge($formData, [
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'currentAddress' => trim($_POST['currentAddress'] ?? ''),
                'currentCity' => trim($_POST['currentCity'] ?? ''),
                'currentProvince' => trim($_POST['currentProvince'] ?? ''),
                'currentZip' => trim($_POST['currentZip'] ?? ''),
            ]);
        } elseif ($step === 3) {
            $formData = array_merge($formData, [
                'recentEmployer' => trim($_POST['recentEmployer'] ?? ''),
                'recentJobTitle' => trim($_POST['recentJobTitle'] ?? ''),
                'recentStartDate' => trim($_POST['recentStartDate'] ?? ''),
                'recentEndDate' => trim($_POST['recentEndDate'] ?? ''),
                'recentResponsibilities' => trim($_POST['recentResponsibilities'] ?? ''),
                'previousEmployer' => trim($_POST['previousEmployer'] ?? ''),
                'previousJobTitle' => trim($_POST['previousJobTitle'] ?? ''),
            ]);
        } else {
            $formData = array_merge($formData, [
                'additionalInfo' => trim($_POST['additionalInfo'] ?? ''),
            ]);
        }

        $_SESSION['apply_form_data'] = $formData;

        if ($action === 'previous') {
            $step = max(1, $step - 1);
            header('Location: apply.php?jobId=' . $jobId . '&step=' . $step);
            exit;
        }

        if ($action === 'next') {
            $step = min(4, $step + 1);
            header('Location: apply.php?jobId=' . $jobId . '&step=' . $step);
            exit;
        }

        if ($action === 'submit') {
            if (empty($formData['firstName']) || empty($formData['lastName']) || empty($formData['phone'])) {
                throw new Exception('Please complete your name and phone number.');
            }

            $fullName = trim(sprintf('%s %s %s', $formData['firstName'], $formData['middleName'] ?? '', $formData['lastName']));
            $email = trim($formData['email'] ?? $_SESSION['user']['email'] ?? '');
            $phone = trim($formData['phone'] ?? '');
            // Handle CV file upload (optional)
            $cv = '';
            if (!empty($_FILES['cv']['name'])) {
                $uploadDir = __DIR__ . '/../assets/uploads/cvs/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $file = $_FILES['cv'];
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $allowed = [
                        'pdf' => ['application/pdf'],
                        'doc' => ['application/msword', 'application/octet-stream'],
                        'docx' => [
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/zip',
                            'application/octet-stream',
                            'application/msword'
                        ]
                    ];
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);

                    if (!array_key_exists($ext, $allowed) || !in_array($mime, $allowed[$ext], true)) {
                        throw new Exception('Invalid CV file type. Allowed: PDF, DOC, DOCX.');
                    }

                    if ($file['size'] > 5 * 1024 * 1024) {
                        throw new Exception('CV file is too large. Max 5MB allowed.');
                    }

                    $basename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    $destination = $uploadDir . $basename;
                    if (!move_uploaded_file($file['tmp_name'], $destination)) {
                        throw new Exception('Failed to save uploaded CV.');
                    }

                    // Store relative path for DB
                    $cv = 'assets/uploads/cvs/' . $basename;
                    $formData['uploadedCv'] = $cv;
                } else {
                    throw new Exception('Error uploading CV file.');
                }
            } else {
                // If no file uploaded, allow storing objective as CV text (existing behavior)
                $cv = trim($formData['objective'] ?? '');
            }

            $coverLetter = '';
            if (!empty($_FILES['coverLetter']['name'])) {
                $uploadDir = __DIR__ . '/../assets/uploads/coverLetters/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $file = $_FILES['coverLetter'];
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $allowed = [
                        'pdf' => ['application/pdf'],
                        'doc' => ['application/msword', 'application/octet-stream'],
                        'docx' => [
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/zip',
                            'application/octet-stream',
                            'application/msword'
                        ]
                    ];
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);

                    if (!array_key_exists($ext, $allowed) || !in_array($mime, $allowed[$ext], true)) {
                        throw new Exception('Invalid cover letter file type. Allowed: PDF, DOC, DOCX.');
                    }

                    if ($file['size'] > 5 * 1024 * 1024) {
                        throw new Exception('Cover letter file is too large. Max 5MB allowed.');
                    }

                    $basename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    $destination = $uploadDir . $basename;
                    if (!move_uploaded_file($file['tmp_name'], $destination)) {
                        throw new Exception('Failed to save uploaded cover letter.');
                    }

                    // Store relative path for DB
                    $coverLetter = 'assets/uploads/coverLetters/' . $basename;
                    $formData['uploadedCoverLetter'] = $coverLetter;
                } else {
                    throw new Exception('Error uploading cover letter file.');
                }
            } else {
                $coverLetter = json_encode([
                    'dob' => $formData['dob'] ?? '',
                    'gender' => $formData['gender'] ?? '',
                    'currentAddress' => $formData['currentAddress'] ?? '',
                    'currentCity' => $formData['currentCity'] ?? '',
                    'currentProvince' => $formData['currentProvince'] ?? '',
                    'currentZip' => $formData['currentZip'] ?? '',
                    'recentEmployer' => $formData['recentEmployer'] ?? '',
                    'recentJobTitle' => $formData['recentJobTitle'] ?? '',
                    'recentStartDate' => $formData['recentStartDate'] ?? '',
                    'recentEndDate' => $formData['recentEndDate'] ?? '',
                    'recentResponsibilities' => $formData['recentResponsibilities'] ?? '',
                    'previousEmployer' => $formData['previousEmployer'] ?? '',
                    'previousJobTitle' => $formData['previousJobTitle'] ?? '',
                    'additionalInfo' => $formData['additionalInfo'] ?? '',
                ], JSON_UNESCAPED_UNICODE);
            }

            $stmt = $pdo->prepare(
                'INSERT INTO applications (jobId, userId, fullName, email, phone, cv, coverLetter) VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$jobId, $userId, $fullName, $email, $phone, $cv, $coverLetter]);

            $success = 'Your application was submitted successfully. We will contact you soon.';
            unset($_SESSION['apply_form_data'], $_SESSION['apply_job_id']);
            $formData = [];
            $step = 1;
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = $job ? htmlspecialchars($job['title']) . ' - Apply' : 'Apply for Job';
$formTitle = $job ? 'Apply for ' . htmlspecialchars($job['title']) : 'Complete Your Application';
$formDescription = 'Complete the four-step application process. Your progress will be kept until you submit.';
$pageClass = 'apply-application';
$formFooter = '<p>Already have an account? <a href="login.php">Login here</a>.</p>';
$includeHeader = false;
$hideRoleSwitcher = true;

ob_start();
require __DIR__ . '/../views/apply.html.php';
$content = ob_get_clean();
require __DIR__ . '/../layouts/layout-form.php';
