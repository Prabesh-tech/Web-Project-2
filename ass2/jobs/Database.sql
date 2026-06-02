CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username TEXT NOT NULL,
    email TEXT NOT NULL,
    password TEXT NOT NULL,
    role TINYINT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT
);

CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    companyName TEXT NOT NULL,
    description TEXT,
    logo TEXT,
    website TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    salary TEXT,
    location TEXT,
    jobType TEXT,
    categoryId INT,
    companyId INT,
    postedBy INT,
    closingDate DATE,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (categoryId) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (companyId) REFERENCES companies(id) ON DELETE SET NULL,
    FOREIGN KEY (postedBy) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jobId INT NOT NULL,
    userId INT NOT NULL,
    fullName TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT,
    cv TEXT,
    coverLetter TEXT,
    status TEXT DEFAULT 'Pending',
    appliedAt DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (jobId) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE saved_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    jobId INT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (jobId) REFERENCES jobs(id) ON DELETE CASCADE
);

CREATE TABLE enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    message TEXT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
);


INSERT INTO users(username,email,password,role)
VALUES
('Admin','admin@gmail.com','123456','1'),
('Prabesh','prabesh@gmail.com','123456','2'),
('Ram','ram@gmail.com','123456','0');

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

INSERT INTO companies(companyName,description,website)
VALUES
('ABC Tech','Software Company','www.abctech.com'),
('XYZ Solutions','IT Services Company','www.xyz.com'),
('Global Education','Education Institute','www.globaledu.com');

INSERT INTO jobs
(title,description,salary,location,jobType,categoryId,companyId,postedBy,closingDate)
VALUES
(
'PHP Developer',
'Develop and maintain web applications',
'50000',
'Kathmandu',
'Full Time',
1,
1,
2,
'2026-12-31'
),
(
'Account Officer',
'Handle accounting activities',
'40000',
'Pokhara',
'Full Time',
2,
2,
2,
'2026-12-31'
);