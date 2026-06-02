<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

// Active category, brand & search
$activeCat = $_GET['category'] ?? 'All';
$activeBrand = $_GET['brand'] ?? '';
$search = trim($_GET['search'] ?? '');

$availableTables = ['auction', 'jobs', 'job', 'listings'];
$jobTable = null;
foreach ($availableTables as $tableName) {
    if (tableExists($pdo, $tableName)) {
        $jobTable = $tableName;
        break;
    }
}

$categoriesAvailable = tableExists($pdo, 'categories');
$usersAvailable = tableExists($pdo, 'users');
$brandsAvailable = tableExists($pdo, 'brands');
$categoryCards = [];
$auctions = [];
$dbError = '';

if ($categoriesAvailable) {
    try {
        $catQuery = 'SELECT c.id, c.name, c.description, c.image, COUNT(a.id) AS jobCount FROM categories c';
        if ($jobTable) {
            $catQuery .= " LEFT JOIN `$jobTable` a ON a.categoryId = c.id";
        }
        $catQuery .= ' GROUP BY c.id ORDER BY c.name ASC';
        $categoryStmt = $pdo->prepare($catQuery);
        $categoryStmt->execute();
        $categoryCards = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // ignore category section if table schema doesn't match
        $categoryCards = [];
    }
}

if ($jobTable) {
    $query = 'SELECT a.*';
    if ($categoriesAvailable) {
        $query .= ', c.name AS category';
    }
    if ($usersAvailable) {
        $query .= ', u.username AS owner';
    }
    $query .= " FROM `$jobTable` a";

    if ($categoriesAvailable) {
        $query .= ' LEFT JOIN categories c ON a.categoryId = c.id';
    }
    if ($usersAvailable) {
        $query .= ' LEFT JOIN users u ON a.postedBy = u.id';
    }

    $params = [];
    $conditions = [];

    // Filter by category
    if ($activeCat !== 'All' && $categoriesAvailable) {
        $conditions[] = 'c.name = ?';
        $params[] = $activeCat;
    }

    if ($activeBrand !== '' && $brandsAvailable) {
        $conditions[] = '(a.title LIKE ? OR a.description LIKE ?)';
        $params[] = "%$activeBrand%";
        $params[] = "%$activeBrand%";
    }

    if ($search !== '') {
        $searchConditions = ['a.title LIKE ?', 'a.description LIKE ?'];
        $params[] = "%$search%";
        $params[] = "%$search%";

        if ($categoriesAvailable) {
            $searchConditions[] = 'c.name LIKE ?';
            $params[] = "%$search%";
        }
        if ($usersAvailable) {
            $searchConditions[] = 'u.username LIKE ?';
            $params[] = "%$search%";
        }

        $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
    }

    if ($conditions) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $query .= ' ORDER BY a.closingDate ASC';

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $auctions = $stmt->fetchAll();
    } catch (PDOException $e) {
        $dbError = 'Unable to load job listings: ' . htmlspecialchars($e->getMessage());
    }
} else {
    $dbError = 'No job listings table was found in the database. Please initialize the database before loading jobs.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Prabesh Job – Job Listings</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a href="index.php" class="logo">Prabesh Job</a>

        <nav class="main-nav">
            <div class="nav-dropdown">
                <button class="nav-link nav-dropdown-toggle" type="button">Job Categories</button>
                <div class="nav-dropdown-menu nav-dropdown-menu-columns">
                    <a href="#">Sales & Marketing</a>
                    <a href="#">Education</a>
                    <a href="#">General Mgmt</a>
                    <a href="#">Sales</a>
                    <a href="#">Customer Service</a>
                    <a href="#">IT – Programming & Development</a>
                    <a href="#">Health/Pharma/Biotech/Medical/R&D</a>
                    <a href="#">Creative / Graphics / Designing</a>
                    <a href="#">Abroad Study</a>
                    <a href="#">Advertising</a>
                    <a href="#">Nursing</a>
                    <a href="#">Travel And Tourism</a>
                    <a href="#">Human Resource</a>
                </div>
            </div>
            <div class="nav-dropdown">
                <button class="nav-link nav-dropdown-toggle" type="button">Trainings</button>
                <div class="nav-dropdown-menu">
                    <a href="#">Advanced Digital Marketing Training</a>
                    <a href="#">Job-Oriented Digital Marketing Training</a>
                    <a href="#">Advanced Accounting Training</a>
                    <a href="#">Professional HR Training</a>
                    <a href="#">Job-Oriented Accounting Training</a>
                </div>
            </div>
            <div class="nav-dropdown">
                <button class="nav-link nav-dropdown-toggle" type="button">Services</button>
                <div class="nav-dropdown-menu">
                    <a href="#">Vacancy Announcement & Management Tools</a>
                    <a href="#">Recruitment Services</a>
                    <a href="#">Outsourcing Tools & Services</a>
                    <a href="#">Human Resource Consulting</a>
                </div>
            </div>
            <a href="#" class="nav-link">Blogs</a>
            <div class="nav-dropdown">
                <button class="nav-link nav-dropdown-toggle" type="button">More</button>
                <div class="nav-dropdown-menu">
                    <a href="#">Contact Us</a>
                    <a href="#">About Us</a>
                </div>
            </div>
        </nav>

        <div class="user-area">
            <?php if (!empty($_SESSION['user']['username'])):
                $isAdminUser = (!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) ||
                               (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin');
            ?>
                <?php if ($isAdminUser): ?>
                    <a href="admin.php" class="btn-addauction admin-dashboard">Admin Dashboard</a>
                <?php else: ?>
                    <a href="jobs/addJob.php" class="btn-addauction">Add Listing</a>
                <?php endif; ?>
                <span class="username">Hi, <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                <a href="logout.php" class="user-btn">Logout</a>
            <?php else: ?>
                <a href="login.php" class="auth-link">
                    <span>Sign up</span>
                    <span class="user-icon">👤</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="hero">
    <div class="hero-content">
        <div class="hero-copy">
            <span class="eyebrow">Find your next role</span>
            <h1>Find Your Dream Job In Nepal With Prabesh Job</h1>
            <p>Search and apply for the best jobs, trainings, and employer opportunities across Nepal.</p>

            <form class="hero-search-form" id="searchForm" method="get" action="index.php" autocomplete="off">
                <div class="hero-search-field">
                    <span class="field-icon">🔎</span>
                    <input type="text" name="search" id="searchInput" autocomplete="off" placeholder="Job title, keywords, or company" value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="hero-search-field">
                    <span class="field-icon">📍</span>
                    <input type="text" name="location" autocomplete="off" placeholder="City, state, zip code, or \"remote\"">
                </div>
                <button type="submit" class="hero-search-btn">Search</button>
            </form>
        </div>
    </div>
</section>

        <section id="about" class="about-section">
            <div class="section-inner">
                <div class="about-grid">
                    <div class="about-copy">
                        <span class="eyebrow">About Prabesh Job</span>
                        <h2>Trusted HR and recruitment solutions across Nepal</h2>
                        <p>Prabesh Job connects job seekers with employers in Nepal using smart hiring, training, outsourcing and HR consulting services. We help candidates discover career opportunities and employers find the right talent faster.</p>
                        <a href="#contact" class="btn-secondary">Contact Us</a>
                    </div>
                    <div class="about-cards">
                        <article class="feature-card">
                            <div class="feature-icon">📢</div>
                            <h3>Vacancy Announcement</h3>
                            <p>Publish the latest job vacancies and reach qualified candidates quickly.</p>
                        </article>
                        <article class="feature-card">
                            <div class="feature-icon">🤝</div>
                            <h3>Outsourcing</h3>
                            <p>Offer skilled outsourcing services that help companies save time and resources.</p>
                        </article>
                        <article class="feature-card">
                            <div class="feature-icon">🎓</div>
                            <h3>Training</h3>
                            <p>Deliver practical training programs that prepare candidates for better jobs.</p>
                        </article>
                        <article class="feature-card">
                            <div class="feature-icon">📋</div>
                            <h3>HR Consulting</h3>
                            <p>Support businesses with HR solutions for improved hiring, management, and employee growth.</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="contact-section">
            <div class="section-inner">
                <div class="contact-grid">
                    <div class="contact-details">
                        <span class="eyebrow">Contact Us</span>
                        <h2>Need to get in touch with us?</h2>
                        <p>Either fill out the form or contact us at <a href="mailto:info@prabeshjob.com">info@prabeshjob.com</a> or call us at +977-01-5199600.</p>
                        <div class="map-card">
                            <iframe
                                src="https://www.google.com/maps?q=Prabesh%20Job%20Kathmandu%20Nepal&output=embed"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                frameborder="0"
                                allowfullscreen=""
                            ></iframe>
                        </div>
                    </div>
                    <form class="contact-form" action="#" method="post">
                        <div class="form-row split-row">
                            <div class="form-field">
                                <label>First Name</label>
                                <input type="text" name="firstName" placeholder="First Name" required>
                            </div>
                            <div class="form-field">
                                <label>Last Name</label>
                                <input type="text" name="lastName" placeholder="Last Name" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="Email Address" required>
                        </div>
                        <div class="form-row">
                            <label>Your Phone Number</label>
                            <input type="tel" name="phone" placeholder="Your Phone Number" required>
                        </div>
                        <div class="form-row">
                            <label>What can we help you with?</label>
                            <textarea name="message" rows="5" placeholder="Message"></textarea>
                        </div>
                        <div class="form-row recaptcha-row">
                            <label class="recaptcha-fake">
                                <input type="checkbox" disabled>
                                I'm not a robot
                            </label>
                        </div>
                        <button type="submit" class="btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </section>

    <?php if (!empty($categoryCards)): ?>
    <section class="brand-options">
        <div class="brand-options-inner">
            <div class="brand-options-header">
                <h2>Explore Job Categories</h2>
                <p>Browse all categories and job counts.</p>
            </div>
            <div class="brand-options-list">
                <h3>All Job Categories</h3>
                <div class="brand-options-list-grid">
                    <?php foreach ($categoryCards as $cat): ?>
                        <a href="index.php?category=<?= urlencode($cat['name']) ?>" class="brand-options-list-item">
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                            <span>(<?= (int)($cat['jobCount'] ?? 0) ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>


        <?php if (!empty($dbError)): ?>
            <div class="db-error"><?= htmlspecialchars($dbError) ?></div>
        <?php endif; ?>

        <?php if (empty($auctions)): ?>
            <div class="no-results">No jobs yet. Add listings to see them here.</div>
        <?php else: ?>
            <div class="job-grid">
                <?php
                $countBidStmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE jobId = ?");
                
                // Prepare saved jobs count with error handling (in case table doesn't exist)
                try {
                    $countWatchStmt = $pdo->prepare("SELECT COUNT(*) FROM saved_jobs WHERE jobId = ?");
                } catch (PDOException $e) {
                    $countWatchStmt = null;
                }
                
                foreach ($auctions as $a):
                    $countBidStmt->execute([$a['id']]);
                    $bidsCount = (int)$countBidStmt->fetchColumn();
                    
                    $watchCount = 0;
                    if ($countWatchStmt) {
                        try {
                            $countWatchStmt->execute([$a['id']]);
                            $watchCount = (int)$countWatchStmt->fetchColumn();
                        } catch (PDOException $e) {
                            $watchCount = 0;
                        }
                    }

                        $timeRemaining = '—';
                        try {
                            if (!empty($a['closingDate'])) {
                                $now = new DateTime();
                                $end = new DateTime($a['closingDate']);
                                $diff = $now->diff($end);
                                $timeRemaining = ($diff->invert === 0) ? $diff->days . 'd ' . $diff->h . 'h' : 'Closed';
                            }
                        } catch (Exception $e) {
                            $timeRemaining = '—';
                        }

                        $jobLocation = !empty($a['location']) ? $a['location'] : (!empty($a['category']) ? $a['category'] : 'Nepal');
                        $jobExperience = !empty($a['year']) ? htmlspecialchars($a['year']) . ' yrs' : (!empty($a['mileage']) ? htmlspecialchars($a['mileage']) . ' yrs' : 'Fresher');
                        $salaryText = !empty($a['salary']) ? 'Nrs. ' . htmlspecialchars($a['salary']) : 'Not specified';
                    ?>

                    <div class="job-card">
                        <div class="card-top">
                            <div class="card-thumb">
                                <?php
                                    $auctionImage = '';
                                    if (!empty($a['image'])) {
                                        $imagePath = $a['image'];
                                        if (strpos($imagePath, 'images/auctions/') === 0) {
                                            if (file_exists(__DIR__ . '/' . $imagePath)) {
                                                $auctionImage = htmlspecialchars($imagePath);
                                            }
                                        } elseif (file_exists(__DIR__ . '/images/auctions/' . $imagePath)) {
                                            $auctionImage = 'images/auctions/' . htmlspecialchars($imagePath);
                                        } elseif (file_exists(__DIR__ . '/assets/images/' . $imagePath)) {
                                            $auctionImage = 'assets/images/' . htmlspecialchars($imagePath);
                                        }
                                    }
                                ?>
                                <?php if ($auctionImage): ?>
                                    <img src="<?= $auctionImage ?>" alt="Job Image">
                                <?php else: ?>
                                    <img src="assets/images/image1.jpg" alt="Default Job Image">
                                <?php endif; ?>
                            </div>
                            <div class="card-top-info">
                                <div>
                                    <h3><?= htmlspecialchars($a['title']) ?></h3>
                                    <p class="company-name"><?= htmlspecialchars($a['owner'] ?? 'Unknown') ?></p>
                                </div>
                                <button type="button" class="card-menu" aria-label="More actions">⋮</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="card-meta-grid">
                                <div class="meta-item"><span class="meta-icon">📍</span><span><?= htmlspecialchars($jobLocation) ?></span></div>
                                <div class="meta-item"><span class="meta-icon">💼</span><span><?= htmlspecialchars($jobExperience) ?></span></div>
                                <div class="meta-item"><span class="meta-icon">⏳</span><span><?= htmlspecialchars($timeRemaining) ?> left</span></div>
                                <div class="meta-item"><span class="meta-icon">💰</span><span><?= htmlspecialchars($salaryText) ?></span></div>
                            </div>

                            <div class="card-badges">
                                <span class="badge reserve">Application Deadline</span>
                                <span class="badge time"><?= htmlspecialchars($timeRemaining) ?></span>
                            </div>

                            <div class="card-footer">
                                <?php if (!empty($_SESSION['user']['username'])): ?>
                                    <a href="jobs/job.php?id=<?= $a['id'] ?>" class="btn view-detail">View Detail</a>
                                <?php else: ?>
                                    <?php $returnUrl = urlencode('jobs/job.php?id=' . $a['id']); ?>
                                    <a href="login.php?redirect=<?= $returnUrl ?>" class="btn view-detail">Login to view</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<div id="searchResults" class="search-results-modal" style="display: none;">
    <div class="search-results-overlay" onclick="closeSearchResults()"></div>
    <div class="search-results-container">
        <button class="search-close-btn" onclick="closeSearchResults()">✕</button>
        <div id="searchResultsContent"></div>
    </div>
</div>

<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> Prabesh Job</p>
</footer>

<script>
const searchInput = document.getElementById('searchInput');
const searchForm = document.getElementById('searchForm');
const searchResults = document.getElementById('searchResults');
const searchResultsContent = document.getElementById('searchResultsContent');

let searchTimeout;

// Live search as user types
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
        closeSearchResults();
        return;
    }
    
    // Debounce search requests
    searchTimeout = setTimeout(() => {
        performSearch(query);
    }, 300);
});

function performSearch(query) {
    const url = `search-api.php?search=${encodeURIComponent(query)}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.jobs.length > 0) {
                displaySearchResults(data.jobs);
                searchResults.style.display = 'flex';
            } else {
                searchResultsContent.innerHTML = '<div class="no-results">No jobs found</div>';
                searchResults.style.display = 'flex';
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            searchResultsContent.innerHTML = '<div class="auth-error">Search failed</div>';
            searchResults.style.display = 'flex';
        });
}

function displaySearchResults(jobs) {
    let html = '<h3 class="search-results-title">Search Results</h3>';
    html += '<div class="search-results-grid">';
    
    jobs.forEach(job => {
        const imageUrl = job.image
            ? (job.image.startsWith('assets/') ? job.image : `images/auctions/${job.image}`)
            : 'assets/images/image1.jpg';
        const salaryLabel = job.salary ? `Nrs. ${job.salary}` : 'Not specified';
        
        html += `
            <a href="jobs/job.php?id=${job.id}" class="search-result-card">
                <div class="search-result-thumb">
                    <img src="${imageUrl}" alt="Job Image">
                </div>
                <div class="search-result-body">
                    <h4>${job.title}</h4>
                    <p class="search-result-category">${job.category || 'General'}</p>
                    <div class="search-result-bid">${salaryLabel}</div>
                    <div class="search-result-meta">👁️ ${job.watchCount || 0} saved</div>
                </div>
            </a>
        `;
    });
    
    html += '</div>';
    searchResultsContent.innerHTML = html;
}

function closeSearchResults() {
    searchResults.style.display = 'none';
}

// Close search results when clicking on a result
document.addEventListener('click', function(event) {
    if (!event.target.closest('#searchResults') && !event.target.closest('#searchInput')) {
        closeSearchResults();
    }
});

// Clear browser autofill if there is no search query in the URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has('search') && searchInput.value.trim() !== '') {
        searchInput.value = '';
    }
});

// Prevent form submission on Enter (let live search handle it)
searchForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const query = searchInput.value.trim();
    if (query.length > 0) {
        const url = `index.php?search=${encodeURIComponent(query)}`;
        window.location.href = url;
    }
});
</script>

</body>
</html>
