<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';

$pageTitle = 'Training Programs - Prabesh Job';
$breadcrumbs = [
    'Home' => 'index.php',
];

// Training courses data
$courses = [
    [
        'id' => 1,
        'title' => 'Master Creative & Analytical Digital Marketing Skills with Hands-On Training',
        'duration' => '60 Days',
        'image' => 'assets/images/training-1.jpg',
        'description' => 'Step into the world of professional digital marketing with our comprehensive program. This course provides hands-on practical training in graphic design, social media advertising, email campaigns, website development, SEO, Google Ads, and freelancing strategies.',
        'features' => [
            'Training Certificates',
            'CV Writing Session',
            'Networking Opportunities',
            'Internship/Placement Assistance'
        ]
    ],
    [
        'id' => 2,
        'title' => 'Get Hired Faster with Internship and Placement Support',
        'duration' => '30 Days',
        'image' => 'assets/images/training-2.jpg',
        'description' => 'Internationally Standard Advanced Training Programs designed to help you secure job opportunities faster. We provide comprehensive support to help you succeed in your career journey.',
        'stats' => [
            ['label' => 'Satisfaction Rate', 'value' => '98%'],
            ['label' => 'Placement Rate', 'value' => '89%'],
            ['label' => 'Average Salary', 'value' => '78%']
        ],
        'features' => [
            'Training Certificates',
            'Internship Placement',
            'Job Interview Prep',
            'Career Guidance'
        ]
    ],
    [
        'id' => 3,
        'title' => 'Web Development Fundamentals',
        'duration' => '45 Days',
        'image' => 'assets/images/training-3.jpg',
        'description' => 'Learn the basics of web development including HTML, CSS, JavaScript, and PHP. Build real-world projects and become job-ready.',
        'features' => [
            'Training Certificates',
            'Project Portfolio',
            'Code Review Sessions',
            'Job Placement Support'
        ]
    ]
];

ob_start();
require_once __DIR__ . '/../views/training.html.php';
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/layout-main.php';
