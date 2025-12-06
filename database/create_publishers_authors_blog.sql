-- Bang nha xuat ban
CREATE TABLE IF NOT EXISTS publishers (
    publisher_id INT PRIMARY KEY AUTO_INCREMENT,
    publisher_name VARCHAR(255) NOT NULL,
    description TEXT,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(255),
    logo VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bang tac gia
CREATE TABLE IF NOT EXISTS authors (
    author_id INT PRIMARY KEY AUTO_INCREMENT,
    author_name VARCHAR(255) NOT NULL,
    pen_name VARCHAR(255),
    biography TEXT,
    birth_date DATE,
    nationality VARCHAR(100),
    email VARCHAR(100),
    photo VARCHAR(255),
    website VARCHAR(255),
    awards TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bang danh muc blog
CREATE TABLE IF NOT EXISTS blog_categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bang bai viet blog
CREATE TABLE IF NOT EXISTS blog_posts (
    post_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category_id INT,
    author_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    views INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(category_id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES authors(author_id) ON DELETE SET NULL
);

-- Bang binh luan blog
CREATE TABLE IF NOT EXISTS blog_comments (
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NULL,
    author_name VARCHAR(100),
    author_email VARCHAR(100),
    content TEXT NOT NULL,
    parent_id INT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(post_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES blog_comments(comment_id) ON DELETE CASCADE
);

-- Them du lieu mau cho nha xuat ban
INSERT INTO publishers (publisher_name, description, address, phone, email, website, status) VALUES
('NXB Tre', 'Nha xuat ban chuyen ve sach thieu nhi va van hoc', '161B Ly Chinh Thang, Quan 3, TP.HCM', '0283932704', 'hopthubandoc@nxbtre.com.vn', 'https://www.nxbtre.com.vn', 'active'),
('NXB Kim Dong', 'Nha xuat ban sach thieu nhi hang dau Viet Nam', '55 Quang Trung, Nguyen Du, Hai Ba Trung, Ha Noi', '0243943463', 'info@nxbkimdong.com.vn', 'https://nxbkimdong.com.vn', 'active'),
('NXB Van hoc', 'Chuyen xuat ban cac tac pham van hoc trong va ngoai nuoc', '18 Nguyen Truong To, Ba Dinh, Ha Noi', '0243733723', 'info@nxbvanhoc.com.vn', 'https://nxbvanhoc.com.vn', 'active'),
('NXB Lao dong', 'Xuat ban sach ve kinh te, xa hoi, ky nang song', '175 Giang Vo, Dong Da, Ha Noi', '0243514932', 'nxblaodong@gmail.com', 'https://nxblaodong.com.vn', 'active'),
('NXB The Gioi', 'Chuyen dich va xuat ban sach nuoc ngoai', '46 Tran Hung Dao, Hoan Kiem, Ha Noi', '0243253841', 'thegioi@thegioipublishers.vn', 'https://www.thegioipublishers.vn', 'active');

-- Them du lieu mau cho tac gia
INSERT INTO authors (author_name, pen_name, biography, birth_date, nationality, email, status) VALUES
('Nguyen Nhat Anh', NULL, 'Nha van noi tieng voi nhieu tac pham van hoc thieu nhi', '1955-05-07', 'Viet Nam', NULL, 'active'),
('Haruki Murakami', 'Murakami Haruki', 'Tieu thuyet gia va nha van nguoi Nhat Ban', '1949-01-12', 'Nhat Ban', NULL, 'active'),
('Paulo Coelho', NULL, 'Tieu thuyet gia nguoi Brazil, tac gia cuon Nha gia kim', '1947-08-24', 'Brazil', NULL, 'active'),
('Nguyen Du', NULL, 'Dai thi hao Viet Nam, tac gia Truyen Kieu', '1765-01-03', 'Viet Nam', NULL, 'active'),
('To Hoai', NULL, 'Nha van Viet Nam voi tac pham noi tieng De Men phieu luu ky', '1920-09-27', 'Viet Nam', NULL, 'active'),
('J.K. Rowling', 'Robert Galbraith', 'Tac gia cua bo truyen Harry Potter noi tieng', '1965-07-31', 'Anh', NULL, 'active'),
('Dan Brown', NULL, 'Tac gia cua Mat ma Da Vinci va nhieu tieu thuyet trinh tham', '1964-06-22', 'My', NULL, 'active'),
('Ngo Tat To', NULL, 'Nha van Viet Nam voi tac pham Tat den', '1894-11-30', 'Viet Nam', NULL, 'active'),
('Nam Cao', NULL, 'Nha van hien thuc Viet Nam', '1915-10-29', 'Viet Nam', NULL, 'active'),
('Vu Trong Phung', NULL, 'Nha van hien thuc phe phan Viet Nam', '1912-10-20', 'Viet Nam', NULL, 'active');

-- Them du lieu mau cho danh muc blog
INSERT INTO blog_categories (category_name, slug, description, status) VALUES
('Tin tuc sach moi', 'tin-tuc-sach-moi', 'Cap nhat cac dau sach moi ra mat', 'active'),
('Review sach hay', 'review-sach-hay', 'Danh gia va gioi thieu sach', 'active'),
('Tac gia noi bat', 'tac-gia-noi-bat', 'Gioi thieu ve cac tac gia va nha van', 'active'),
('Meo doc sach', 'meo-doc-sach', 'Chia se kinh nghiem va phuong phap doc sach hieu qua', 'active'),
('Su kien van hoc', 'su-kien-van-hoc', 'Thong tin ve cac su kien, hoi sach', 'active');
