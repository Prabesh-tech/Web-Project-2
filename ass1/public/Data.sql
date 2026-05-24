-- USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    isAdmin TINYINT(1) DEFAULT 0
);

-- BRANDS
CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    isTopBrand TINYINT(1) DEFAULT 0
);

-- CATEGORIES
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name Text NOT NULL UNIQUE,
    description TEXT
);


-- AUCTIONS
CREATE TABLE auction (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title TEXT NOT NULL,
    description TEXT,
    categoryId INT NOT NULL,
    userId INT NOT NULL,
    image VARCHAR(255),
    year INT,
    mileage INT,
    currentBid DECIMAL(10,2),
    endDate DATETIME
);


-- REVIEWS
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,        
    reviewerId INT NOT NULL,   
    reviewText TEXT NOT NULL,
    rating INT DEFAULT 0,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- BIDS
CREATE TABLE bid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auctionId INT NOT NULL,
    userId INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- WATCHES
CREATE TABLE watches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auctionId INT NOT NULL,
    userId INT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_watch (auctionId, userId)
);

-- INSERT TOP BRANDS
INSERT INTO brands (name, isTopBrand) VALUES 
('BMW', 1), ('Audi', 1), ('Porsche', 1), ('Lamborghini', 1), 
('Rolls Royce', 1), ('Pagani', 1), ('Ferrari', 1);

-- INSERT MORE BRANDS
INSERT INTO brands (name, isTopBrand) VALUES 
('Mahindra', 0), ('Toyota', 0), ('Ford', 0), ('Honda', 0), 
('Hyundai', 0), ('Bugatti', 0), ('Nissan', 0), ('Mercedes Benz', 0), 
('Suzuki', 0), ('Land Rover', 0), ('Tata', 0), ('BYD', 0);
