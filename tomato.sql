CREATE DATABASE tomatolite_db;

USE tomatolite_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    PASSWORD VARCHAR(255) NOT NULL,
    ROLE ENUM('user', 'admin') DEFAULT 'user'
);

CREATE TABLE films (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    DESCRIPTION TEXT,
    release_year INT,
    poster_url VARCHAR(255)
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    film_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    COMMENT TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (film_id) REFERENCES films(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample admin user (password 'admin123')
INSERT INTO users (username, PASSWORD, ROLE) VALUES ('admin', '$2y$10$QWzI/W12b0xM0s4L/J/E.uY5W.fM.r6u2f.q.l.k.m.o.p.s.t.u.v.w.x.y.z.', 'admin');
-- Untuk password 'admin123', kamu bisa gunakan online bcrypt generator atau generate di PHP.
-- Contoh: password_hash('admin123', PASSWORD_DEFAULT) akan menghasilkan hash berbeda.
-- Gunakan hash yang kamu generate sendiri untuk keamanan yang lebih baik.

-- Insert sample user (password 'user123')
INSERT INTO users (username, PASSWORD, ROLE) VALUES ('user', '$2y$10$QWzI/W12b0xM0s4L/J/E.uY5W.fM.r6u2f.q.l.k.m.o.p.s.t.u.v.w.x.y.z.', 'user');
-- Ganti hash password sesuai yang kamu generate sendiri.

-- Insert sample films
INSERT INTO films (title, DESCRIPTION, release_year, poster_url) VALUES
('Dune: Part Two', 'Paul Atreides unites with Chani and the Fremen while seeking revenge against those who destroyed his family.', 2024, 'https://upload.wikimedia.org/wikipedia/en/d/d4/Dune_Part_Two_poster.jpeg'),
('Oppenheimer', 'The story of J. Robert Oppenheimer and his role in the development of the atomic bomb during World War II.', 2023, 'https://upload.wikimedia.org/wikipedia/en/1/1a/Oppenheimer_%28film%29.jpeg');