<?php
session_start();

$pageTitle = 'Our Services - Prabesh Job';
$formTitle = 'Complete Hiring, Recruitment & HR Services in Nepal';
$formDescription = 'Prabesh Job provides a wide range of services designed to make hiring and managing people easier for businesses in Nepal. From recruitment and outsourcing to training tools and HR consulting, our solutions help companies save time, reduce effort, and focus more on growth.';
$pageClass = 'services-page';
$includeHeader = true;

$services = [
    [
        'id' => 'vacancy-announcement',
        'icon' => '📋',
        'title' => 'Vacancy Announcement & Management Tools',
        'description' => 'Our hiring and management tools simplify recruitment and employee tracking, helping businesses in Nepal catch jobs, manage capabilities, and provide assistance efficiently and effectively.',
        'tags' => ['Job Posting', 'Candidate Search', 'Dashboard'],
        'color' => 'bg-blue'
    ],
    [
        'id' => 'outsourcing',
        'icon' => '🤝',
        'title' => 'Outsourcing Services',
        'description' => 'Third-party outsourcing services handled for your staffing, payroll, and HR operations. Businesses from everywhere can focus on growth while we handle day-to-day HR duties.',
        'tags' => ['Payroll', 'Staffing', 'Compliance'],
        'color' => 'bg-yellow'
    ],
    [
        'id' => 'recruitment-tests',
        'icon' => '✓',
        'title' => 'Recruitment Tests & Services',
        'description' => 'Our recruitment services connect companies in Nepal with qualified candidates quickly. We handle screening, shortlisting, and matching talent to save your time and effort.',
        'tags' => ['Candidate Pool', 'Shortlisting', 'Fact Finding'],
        'color' => 'bg-pink'
    ],
    [
        'id' => 'hr-consulting',
        'icon' => '💼',
        'title' => 'HR Consulting',
        'description' => 'Guide other people practices, policies, and employee engagement. Our consulting services help businesses develop employees, and improve workplace engagement to build stronger teams and more productive workplaces.',
        'tags' => ['Policy Design', 'Training', 'Extension'],
        'color' => 'bg-purple'
    ]
];

ob_start();
require __DIR__ . '/../views/services.html.php';
$content = ob_get_clean();
require __DIR__ . '/../layouts/layout-main.php';
