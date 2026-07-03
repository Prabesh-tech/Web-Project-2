
DROP DATABASE IF EXISTS Assignment2;
CREATE DATABASE IF NOT EXISTS Assignment2;
USE Assignment2;

-- Main tables first (no dependencies)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TINYINT NOT NULL DEFAULT 0 COMMENT '0=Jobseeker, 1=Employer, 2=Admin, 3=SuperAdmin',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_email (email),
    INDEX idx_status (status)
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);

CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);

CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    companyName VARCHAR(150) NOT NULL,
    description TEXT,
    logo TEXT,
    website VARCHAR(200),
    email VARCHAR(100),
    phone VARCHAR(20),
    location VARCHAR(100),
    industryType VARCHAR(100),
    employees INT,
    ownerId INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ownerId) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_ownerId (ownerId),
    INDEX idx_status (status)
);

-- User Profiles for additional information
CREATE TABLE user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL UNIQUE,
    firstName VARCHAR(50),
    lastName VARCHAR(50),
    phone VARCHAR(20),
    profilePhoto TEXT,
    bio TEXT,
    resume TEXT,
    location VARCHAR(100),
    experience VARCHAR(50),
    company VARCHAR(100),
    designation VARCHAR(100),
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_userId (userId),
    INDEX idx_location (location)
);

-- Jobs table
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    salary VARCHAR(50),
    salaryMax VARCHAR(50),
    location VARCHAR(100),
    jobType VARCHAR(50),
    experience VARCHAR(50),
    qualification VARCHAR(100),
    categoryId INT,
    companyId INT,
    postedBy INT,
    closingDate DATE,
    status ENUM('Open', 'Closed', 'Draft') DEFAULT 'Open',
    isArchived TINYINT(1) NOT NULL DEFAULT 0,
    views INT DEFAULT 0,
    applications INT DEFAULT 0,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (categoryId) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (companyId) REFERENCES companies(id) ON DELETE SET NULL,
    FOREIGN KEY (postedBy) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_postedBy (postedBy),
    INDEX idx_categoryId (categoryId),
    INDEX idx_companyId (companyId),
    INDEX idx_createdAt (createdAt)
);

-- Job Requirements (skills needed for a job)
CREATE TABLE job_requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jobId INT NOT NULL,
    skillId INT,
    skillName VARCHAR(100),
    proficiencyLevel ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Intermediate',
    
    FOREIGN KEY (jobId) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (skillId) REFERENCES skills(id) ON DELETE SET NULL,
    INDEX idx_jobId (jobId)
);

-- User Skills
CREATE TABLE user_skills (er 
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    skillId INT,
    skillName VARCHAR(100),
    proficiencyLevel ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') DEFAULT 'Intermediate',
    yearsOfExperience INT,
    endorsements INT DEFAULT 0,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (skillId) REFERENCES skills(id) ON DELETE SET NULL,
    INDEX idx_userId (userId),
    UNIQUE KEY unique_user_skill (userId, skillId)
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jobId INT NOT NULL,
    userId INT NOT NULL,
    fullName VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    cv TEXT,
    coverLetter TEXT,
    status ENUM('Applied', 'Shortlisted', 'Rejected', 'Accepted') DEFAULT 'Applied',
    appliedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (jobId) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_userId (userId),
    INDEX idx_jobId (jobId),
    UNIQUE KEY unique_application (jobId, userId)
);

-- Ratings and Reviews for companies and users
CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ratedBy INT NOT NULL,
    companyId INT,
    userId INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ratedBy) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (companyId) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_companyId (companyId),
    INDEX idx_userId (userId)
);

-- Admin Audit Logs
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adminId INT NOT NULL,
    action VARCHAR(100),
    entityType VARCHAR(50),
    entityId INT,
    oldValue TEXT,
    newValue TEXT,
    description TEXT,
    ipAddress VARCHAR(45),
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (adminId) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_adminId (adminId),
    INDEX idx_createdAt (createdAt),
    INDEX idx_entityType (entityType)
);

CREATE TABLE saved_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    jobId INT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (jobId) REFERENCES jobs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_saved (userId, jobId),
    INDEX idx_userId (userId)
);

CREATE TABLE enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(150),
    message TEXT NOT NULL,
    status ENUM('Unread', 'Read', 'Replied') DEFAULT 'Unread',
    reply TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_email (email)
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL UNIQUE,
    permissions TEXT,
    notes TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_userId (userId)
);

-- Notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    type VARCHAR(50),
    title VARCHAR(150),
    message TEXT,
    relatedEntityType VARCHAR(50),
    relatedEntityId INT,
    isRead BOOLEAN DEFAULT FALSE,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_userId (userId),
    INDEX idx_isRead (isRead)
);


-- ============================================
-- SAMPLE DATA
-- ============================================

INSERT INTO users(username, email, password, role)
VALUES
('superadmin', 'superadmin@prabesh.com', 'superadmin123', 3),
('admin', 'admin@prabesh.com', 'admin123', 2),
('prabesh_employer', 'prabesh@gmail.com', 'employer123', 1),
('ram_jobseeker', 'ram@gmail.com', 'ram123', 0),
('john_jobseeker', 'john@gmail.com', 'john123', 0);

-- Insert user profiles
INSERT INTO user_profiles(userId, firstName, lastName, phone, bio, location, experience, designation)
VALUES
(3, 'Prabesh', 'Sharma', '9841234567', 'Experienced employer', 'Kathmandu', '5 years', 'HR Manager'),
(4, 'Ram', 'Dhakal', '9841234568', 'Seeking full-time opportunity', 'Pokhara', '2 years', 'PHP Developer'),
(5, 'John', 'Doe', '9841234569', 'Fresh graduate seeking opportunity', 'Bhaktapur', 'Fresher', 'Software Developer');

-- Insert skills
INSERT INTO skills(name) VALUES
('PHP'), ('Laravel'), ('MySQL'), ('JavaScript'), ('React'), ('HTML/CSS'),
('Python'), ('Java'), ('Node.js'), ('MongoDB'), ('API Development'),
('REST APIs'), ('Database Design'), ('Project Management'), ('Leadership');

-- Categories seeded for site taxonomy. The header and category listings prioritize HR, Sales, and IT categories at the top.
INSERT INTO categories (name, description) VALUES
('Sales & Marketing', 'Sales and marketing jobs'),
('Sales/Business Development', 'Sales and business development jobs'),
('Accounting/Finance', 'Accounting and finance jobs'),
('Education', 'Education jobs'),
('Digital Marketing', 'Digital marketing jobs'),
('Health/Medical/Pharmaceuticals', 'Healthcare and pharmaceutical jobs'),
('General Mgmt', 'General management jobs'),
('Video Editing', 'Video editing jobs'),
('Hospitality', 'Hospitality jobs'),
('Sales', 'Sales jobs'),
('Information Technology', 'Information technology jobs'),
('Accounting And Finance', 'Accounting and finance jobs'),
('Customer Service', 'Customer service jobs'),
('Business Firm', 'Business firm jobs'),
('Engineering', 'Engineering jobs'),
('IT – Programming & Development', 'Programming and development jobs'),
('Web & Application Development', 'Web and application development jobs'),
('Communication/Journalism', 'Communication and journalism jobs'),
('Health/Pharma/Biotech/Medical/R&D', 'Healthcare, biotech and R&D jobs'),
('Counselor', 'Counseling jobs'),
('Front Desk', 'Front desk jobs'),
('Creative / Graphics / Designing', 'Creative and design jobs'),
('Driver', 'Driver jobs'),
('Administration', 'Administration jobs'),
('Abroad Study', 'Abroad study consultancy jobs'),
('Hospitality/ Travel/ Ticketing/ Tour', 'Travel and tourism jobs'),
('Production/Maintance', 'Production and maintenance jobs'),
('Advertising', 'Advertising jobs'),
('Logistic', 'Logistics jobs'),
('NGO/INGO', 'NGO and INGO jobs'),
('Nursing', 'Nursing jobs'),
('Manufacturing', 'Manufacturing jobs'),
('IT&Telecommunication', 'IT and telecommunication jobs'),
('Travel And Tourism', 'Travel and tourism jobs'),
('School/College', 'School and college jobs'),
('CA/ACCA', 'CA and ACCA jobs'),
('Human Resource', 'Human resource jobs');

INSERT INTO companies(companyName, description, logo, website, email, phone, location, ownerId, status)
VALUES
('ABC Tech', 'Software Company', NULL, 'www.abctech.com', 'info@abctech.com', '9841111111', 'Kathmandu', 3, 'active'),
('XYZ Solutions', 'IT Services Company', NULL, 'www.xyz.com', 'info@xyz.com', '9841111112', 'Lalitpur', 3, 'active'),
('Global Education', 'Education Institute', NULL, 'www.globaledu.com', 'info@globaledu.com', '9841111113', 'Bhaktapur', 3, 'active');

INSERT INTO jobs(title, description, salary, salaryMax, location, jobType, experience, qualification, categoryId, companyId, postedBy, closingDate, status)
VALUES
-- Information Technology Jobs (Category 11)
(
    'PHP Developer',
    'Develop and maintain web applications using PHP and Laravel. Experience with MySQL required.',
    '40000',
    '60000',
    'Kathmandu',
    'Full Time',
    '2-3 years',
    'Bachelor in IT or related field',
    11,
    1,
    3,
    '2026-12-31',
    'Open'
),
(
    'Senior PHP Developer',
    'Lead development team, architect backend systems and manage database optimization.',
    '80000',
    '120000',
    'Kathmandu',
    'Full Time',
    '5-7 years',
    'Bachelor in IT or related field',
    11,
    1,
    3,
    '2026-12-25',
    'Open'
),
(
    'PHP Web Developer',
    'Create dynamic web applications, work with REST APIs and integrate third-party services.',
    '35000',
    '50000',
    'Pokhara',
    'Full Time',
    '1-2 years',
    'Bachelor in IT or related field',
    11,
    1,
    3,
    '2026-12-20',
    'Open'
),
-- IT Programming & Development Jobs (Category 16)
(
    'Frontend React Developer',
    'Build responsive web applications using React.js and modern JavaScript.',
    '50000',
    '70000',
    'Kathmandu',
    'Full Time',
    '3-5 years',
    'Bachelor in IT or related field',
    16,
    1,
    3,
    '2026-11-30',
    'Open'
),
(
    'Full Stack Developer',
    'Develop complete web solutions with Node.js backend and React frontend.',
    '60000',
    '90000',
    'Kathmandu',
    'Full Time',
    '3-4 years',
    'Bachelor in IT or related field',
    16,
    1,
    3,
    '2026-12-15',
    'Open'
),
(
    'Junior Python Developer',
    'Build Python applications and learn modern development practices.',
    '30000',
    '45000',
    'Lalitpur',
    'Full Time',
    'Fresher-1 year',
    'Bachelor in IT or related field',
    16,
    2,
    3,
    '2026-12-10',
    'Open'
),
-- Accounting/Finance Jobs (Category 3)
(
    'Account Officer',
    'Handle accounting activities, manage financial records and reports.',
    '35000',
    '50000',
    'Pokhara',
    'Full Time',
    '1-2 years',
    'Bachelor in Accounting/Finance',
    3,
    2,
    3,
    '2026-12-31',
    'Open'
),
(
    'Senior Accountant',
    'Manage accounting operations, audit financial statements and supervise team.',
    '65000',
    '95000',
    'Kathmandu',
    'Full Time',
    '4-6 years',
    'Bachelor in Accounting/Finance or CPA',
    3,
    2,
    3,
    '2026-12-28',
    'Open'
),
(
    'Finance Analyst',
    'Analyze financial data, prepare reports and support budgeting decisions.',
    '45000',
    '65000',
    'Kathmandu',
    'Full Time',
    '2-3 years',
    'Bachelor in Finance',
    3,
    2,
    3,
    '2026-12-22',
    'Open'
),
-- Education Jobs (Category 4)
(
    'Mathematics Teacher',
    'Teach mathematics to high school students with interactive methods.',
    '25000',
    '40000',
    'Bhaktapur',
    'Full Time',
    '2-3 years',
    'Bachelor in Education/Mathematics',
    4,
    3,
    3,
    '2026-12-20',
    'Open'
),
(
    'English Language Instructor',
    'Teach English language and literature with focus on communication skills.',
    '28000',
    '45000',
    'Kathmandu',
    'Full Time',
    '2-4 years',
    'Bachelor in English/Education',
    4,
    3,
    3,
    '2026-12-18',
    'Open'
),
(
    'Computer Science Teacher',
    'Teach programming and computer science fundamentals to students.',
    '32000',
    '50000',
    'Lalitpur',
    'Full Time',
    '2-3 years',
    'Bachelor in Computer Science',
    4,
    3,
    3,
    '2026-12-15',
    'Open'
),
-- Digital Marketing Jobs (Category 5)
(
    'Digital Marketing Executive',
    'Execute social media campaigns, SEO optimization and content marketing strategies.',
    '30000',
    '50000',
    'Kathmandu',
    'Full Time',
    '1-2 years',
    'Bachelor in Marketing/Communications',
    5,
    1,
    3,
    '2026-12-25',
    'Open'
),
(
    'Social Media Manager',
    'Manage company social media presence, create engaging content and analyze metrics.',
    '35000',
    '55000',
    'Kathmandu',
    'Full Time',
    '2-3 years',
    'Bachelor in Marketing or Communications',
    5,
    1,
    3,
    '2026-12-20',
    'Open'
),
(
    'SEO Specialist',
    'Optimize website for search engines, conduct keyword research and improve rankings.',
    '40000',
    '60000',
    'Kathmandu',
    'Full Time',
    '2-3 years',
    'Bachelor in IT or Digital Marketing',
    5,
    2,
    3,
    '2026-12-12',
    'Open'
),
-- Sales & Marketing Jobs (Category 1)
(
    'Sales Executive',
    'Meet sales targets, manage client relationships and close deals.',
    '25000',
    '45000',
    'Kathmandu',
    'Full Time',
    '1-2 years',
    'Bachelor in Business/Commerce',
    1,
    1,
    3,
    '2026-12-30',
    'Open'
),
(
    'Marketing Manager',
    'Develop marketing strategies, manage campaigns and lead marketing team.',
    '55000',
    '80000',
    'Kathmandu',
    'Full Time',
    '4-5 years',
    'Bachelor in Marketing or Business',
    1,
    2,
    3,
    '2026-12-28',
    'Open'
),
(
    'Brand Manager',
    'Manage brand image, develop strategies and coordinate marketing activities.',
    '60000',
    '85000',
    'Kathmandu',
    'Full Time',
    '3-5 years',
    'Bachelor in Marketing or Communications',
    1,
    3,
    3,
    '2026-12-25',
    'Open'
),
-- Health/Medical/Pharmaceuticals Jobs (Category 6)
(
    'Registered Nurse',
    'Provide patient care, administer medications and monitor health conditions.',
    '35000',
    '55000',
    'Kathmandu',
    'Full Time',
    '1-3 years',
    'Bachelor in Nursing or Diploma',
    6,
    1,
    3,
    '2026-12-31',
    'Open'
),
(
    'Pharmacist',
    'Dispense medications, provide pharmaceutical advice and manage inventory.',
    '45000',
    '65000',
    'Pokhara',
    'Full Time',
    '2-3 years',
    'Bachelor in Pharmacy',
    6,
    2,
    3,
    '2026-12-29',
    'Open'
),
(
    'Medical Lab Technician',
    'Conduct laboratory tests, maintain equipment and ensure data accuracy.',
    '28000',
    '45000',
    'Lalitpur',
    'Full Time',
    '1-2 years',
    'Diploma or Bachelor in Medical Laboratory',
    6,
    3,
    3,
    '2026-12-27',
    'Open'
),
-- Engineering Jobs (Category 15)
(
    'Software Engineer',
    'Design and develop software solutions following best practices.',
    '55000',
    '80000',
    'Kathmandu',
    'Full Time',
    '3-4 years',
    'Bachelor in Engineering',
    15,
    1,
    3,
    '2026-12-26',
    'Open'
),
(
    'Civil Engineer',
    'Design and supervise construction projects and infrastructure development.',
    '50000',
    '75000',
    'Kathmandu',
    'Full Time',
    '2-4 years',
    'Bachelor in Civil Engineering',
    15,
    2,
    3,
    '2026-12-24',
    'Open'
),
(
    'Mechanical Engineer',
    'Design mechanical systems and oversee manufacturing processes.',
    '48000',
    '70000',
    'Kathmandu',
    'Full Time',
    '2-3 years',
    'Bachelor in Mechanical Engineering',
    15,
    3,
    3,
    '2026-12-23',
    'Open'
),
-- Customer Service Jobs (Category 13)
(
    'Customer Service Representative',
    'Handle customer inquiries, resolve complaints and provide support.',
    '20000',
    '35000',
    'Kathmandu',
    'Full Time',
    'Fresher-1 year',
    'Bachelor or higher secondary',
    13,
    1,
    3,
    '2026-12-30',
    'Open'
),
(
    'Customer Support Specialist',
    'Provide technical and billing support to customers via phone and email.',
    '25000',
    '40000',
    'Kathmandu',
    'Full Time',
    '1-2 years',
    'Bachelor in any field',
    13,
    2,
    3,
    '2026-12-28',
    'Open'
),
(
    'Customer Success Manager',
    'Ensure customer satisfaction, manage accounts and identify upsell opportunities.',
    '40000',
    '60000',
    'Kathmandu',
    'Full Time',
    '2-3 years',
    'Bachelor in Business or Marketing',
    13,
    3,
    3,
    '2026-12-25',
    'Open'
),
-- Human Resource Jobs (Category 37)
(
    'HR Executive',
    'Recruit candidates, conduct interviews and manage employee relations.',
    '30000',
    '50000',
    'Kathmandu',
    'Full Time',
    '2-3 years',
    'Bachelor in HR or Management',
    37,
    1,
    3,
    '2026-12-29',
    'Open'
),
(
    'HR Manager',
    'Manage HR department, handle payroll, policies and employee development.',
    '55000',
    '80000',
    'Kathmandu',
    'Full Time',
    '4-6 years',
    'Bachelor in HR or Management',
    37,
    2,
    3,
    '2026-12-27',
    'Open'
),
(
    'Recruitment Specialist',
    'Source candidates, manage hiring pipeline and build talent database.',
    '35000',
    '55000',
    'Kathmandu',
    'Full Time',
    '2-3 years',
    'Bachelor in HR or any field',
    37,
    3,
    3,
    '2026-12-26',
    'Open'
);

-- Insert job requirements for all jobs
INSERT INTO job_requirements(jobId, skillId, skillName, proficiencyLevel) VALUES
-- PHP Developer positions
(1, 1, 'PHP', 'Intermediate'),
(1, 2, 'Laravel', 'Intermediate'),
(1, 3, 'MySQL', 'Intermediate'),
(2, 1, 'PHP', 'Advanced'),
(2, 2, 'Laravel', 'Advanced'),
(2, 3, 'MySQL', 'Advanced'),
(3, 1, 'PHP', 'Intermediate'),
(3, 3, 'MySQL', 'Intermediate'),
-- React Developer positions
(4, 4, 'JavaScript', 'Advanced'),
(4, 5, 'React', 'Advanced'),
(4, 6, 'HTML/CSS', 'Intermediate'),
(5, 4, 'JavaScript', 'Advanced'),
(5, 5, 'React', 'Advanced'),
(5, 9, 'Node.js', 'Intermediate'),
(6, 7, 'Python', 'Intermediate'),
-- Accounting positions
(7, NULL, 'Accounting', 'Advanced'),
(8, NULL, 'Financial Analysis', 'Advanced'),
(9, NULL, 'Financial Reporting', 'Intermediate'),
-- Education positions
(10, NULL, 'Teaching Skills', 'Advanced'),
(11, NULL, 'Communication', 'Advanced'),
(12, 8, 'Java', 'Intermediate'),
-- Marketing positions
(13, NULL, 'Social Media Marketing', 'Intermediate'),
(13, NULL, 'SEO', 'Intermediate'),
(14, NULL, 'Content Management', 'Intermediate'),
(15, NULL, 'SEO Optimization', 'Advanced'),
-- Sales positions
(16, NULL, 'Sales Skills', 'Intermediate'),
(17, NULL, 'Marketing Strategy', 'Advanced'),
(18, NULL, 'Brand Management', 'Advanced'),
-- Healthcare positions
(19, NULL, 'Nursing', 'Advanced'),
(20, NULL, 'Pharmacy', 'Advanced'),
(21, NULL, 'Laboratory Skills', 'Intermediate'),
-- Engineering positions
(22, 4, 'JavaScript', 'Advanced'),
(22, 9, 'Node.js', 'Intermediate'),
(23, NULL, 'CAD Design', 'Intermediate'),
(24, NULL, 'Mechanical Design', 'Intermediate'),
-- Customer Service positions
(25, NULL, 'Communication Skills', 'Intermediate'),
(26, NULL, 'Problem Solving', 'Intermediate'),
(27, NULL, 'Account Management', 'Advanced'),
-- HR positions
(28, NULL, 'Recruitment', 'Intermediate'),
(29, NULL, 'Payroll Management', 'Advanced'),
(30, NULL, 'Talent Acquisition', 'Intermediate');

-- Insert user skills
INSERT INTO user_skills(userId, skillId, skillName, proficiencyLevel, yearsOfExperience) VALUES
(4, 1, 'PHP', 'Advanced', 2),
(4, 2, 'Laravel', 'Intermediate', 1),
(4, 3, 'MySQL', 'Advanced', 2),
(4, 4, 'JavaScript', 'Intermediate', 1),
(5, 4, 'JavaScript', 'Beginner', 0),
(5, 5, 'React', 'Beginner', 0),
(5, 6, 'HTML/CSS', 'Intermediate', 1);

-- Insert applications
INSERT INTO applications(jobId, userId, fullName, email, phone, status) VALUES
(1, 4, 'Ram Dhakal', 'ram@gmail.com', '9841234568', 'Applied'),
(1, 5, 'John Doe', 'john@gmail.com', '9841234569', 'Applied'),
(3, 4, 'Ram Dhakal', 'ram@gmail.com', '9841234568', 'Shortlisted'),
(4, 5, 'John Doe', 'john@gmail.com', '9841234569', 'Applied'),
(7, 4, 'Ram Dhakal', 'ram@gmail.com', '9841234568', 'Applied'),
(10, 5, 'John Doe', 'john@gmail.com', '9841234569', 'Applied'),
(13, 4, 'Ram Dhakal', 'ram@gmail.com', '9841234568', 'Applied'),
(16, 5, 'John Doe', 'john@gmail.com', '9841234569', 'Applied'),
(19, 4, 'Ram Dhakal', 'ram@gmail.com', '9841234568', 'Applied'),
(22, 5, 'John Doe', 'john@gmail.com', '9841234569', 'Applied');

-- Insert saved jobs
INSERT INTO saved_jobs(userId, jobId) VALUES
(4, 1),
(4, 3),
(4, 4),
(4, 5),
(4, 13),
(5, 1),
(5, 2),
(5, 10),
(5, 16),
(5, 19);

-- Insert enquiries for contact page
INSERT INTO enquiries(name, email, subject, message, status) VALUES
('Ram Kumar', 'ram.kumar@email.com', 'Job Inquiry', 'I am interested in your PHP Developer position. Can you provide more details?', 'Read'),
('Sita Sharma', 'sita.sharma@email.com', 'Employer Account', 'How do I create an employer account to post jobs?', 'Unread'),
('Akshay Poudel', 'akshay.poudel@email.com', 'Technical Support', 'I am having trouble with my profile update.', 'Read'),
('Priya Singh', 'priya.singh@email.com', 'General Inquiry', 'What are the benefits of listing my company here?', 'Unread'),
('Bikash Yadav', 'bikash.yadav@email.com', 'Feedback', 'Great platform! Loved the user interface.', 'Read');

-- Insert ratings for companies
INSERT INTO ratings(ratedBy, companyId, rating, review) VALUES
(4, 1, 5, 'Excellent company to work for. Great team culture and learning opportunities.'),
(4, 2, 4, 'Good company with nice working environment. Could improve benefits.'),
(5, 3, 5, 'Amazing educational institute with quality training programs.'),
(4, 3, 4, 'Very supportive management and good learning opportunities.');

-- Insert sample admins
INSERT INTO admins(userId, permissions) VALUES
(1, 'full_access'),
(2, 'manage_users,manage_jobs,manage_content');
