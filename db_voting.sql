CREATE DATABASE IF NOT EXISTS db_voting
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE db_voting;

CREATE TABLE candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code CHAR(1) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    position VARCHAR(150) NULL
);

CREATE TABLE indicators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_no TINYINT NOT NULL,
    category VARCHAR(100) NOT NULL,
    focus VARCHAR(255) NOT NULL,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL
);

CREATE TABLE polls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_penilai VARCHAR(120) NOT NULL,
    nip VARCHAR(50) NOT NULL UNIQUE,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    candidate_id INT NOT NULL,
    indicator_id INT NOT NULL,
    score TINYINT NOT NULL,
    CONSTRAINT fk_rating_poll FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    CONSTRAINT fk_rating_candidate FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
    CONSTRAINT fk_rating_indicator FOREIGN KEY (indicator_id) REFERENCES indicators(id) ON DELETE CASCADE,
    CONSTRAINT chk_score CHECK (score BETWEEN 1 AND 5),
    UNIQUE KEY unique_rating (poll_id, candidate_id, indicator_id)
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

INSERT INTO candidates (code, name, position) VALUES
('A', 'Aditya Chandra Yudistra, SE', NULL),
('B', 'Iama''adi, S.Mn.', NULL),
('C', 'Elisabet Tri Laksmi, SST., MM', NULL);

INSERT INTO indicators (category_no, category, focus, code, name, description) VALUES
(1, 'BRAIN (Kinerja & Inovasi)', 'Kualitas kerja, penguasaan tugas, dan daya cipta.', '1.1', 'Kompetensi Teknis', 'Sejauh mana kandidat menguasai bidang tugasnya dan menjadi rujukan/tempat bertanya saat rekan kerja menemui kendala?'),
(1, 'BRAIN (Kinerja & Inovasi)', 'Kualitas kerja, penguasaan tugas, dan daya cipta.', '1.2', 'Inovasi & Inisiatif', 'Sejauh mana kandidat memberikan ide kreatif, cara kerja baru, atau solusi digital yang mempermudah pekerjaan di Satker?'),
(1, 'BRAIN (Kinerja & Inovasi)', 'Kualitas kerja, penguasaan tugas, dan daya cipta.', '1.3', 'Problem Solving', 'Sejauh mana kandidat mampu berpikir objektif, tenang, dan memberikan solusi efektif saat menghadapi tekanan/krisis pekerjaan?'),
(2, 'BEAUTY (Komunikasi, Citra Diri & Pengaruh Positif)', 'Soft skill, keteladanan sikap, dan energi positif di tempat kerja.', '2.1', 'Komunikasi Efektif', 'Sejauh mana kandidat mampu menyampaikan gagasan secara jelas, santun, persuasif, serta mau mendengarkan masukan orang lain?'),
(2, 'BEAUTY (Komunikasi, Citra Diri & Pengaruh Positif)', 'Soft skill, keteladanan sikap, dan energi positif di tempat kerja.', '2.2', 'Role Model & Profesionalisme', 'Sejauh mana kandidat menampilkan sikap profesional, percaya diri, rapi, dan menjaga citra positif institusi?'),
(2, 'BEAUTY (Komunikasi, Citra Diri & Pengaruh Positif)', 'Soft skill, keteladanan sikap, dan energi positif di tempat kerja.', '2.3', 'Daya Pengaruh Positif', 'Sejauh mana kehadiran kandidat mampu memberikan motivasi, inspirasi, dan membangun suasana kerja yang menyenangkan?');

-- Login admin default:
-- username: admin
-- password: admin123
INSERT INTO admins (username, password_hash) VALUES
('admin', '$2y$12$s0gNbejw5Th9XJ4.aMI3X.clZzV3DSWLy20JAxz74iOZLNc4AgNye');
