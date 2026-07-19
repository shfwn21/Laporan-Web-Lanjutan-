-- =====================================================
-- DATABASE: db_penjualan_jasa
-- Sistem Manajemen Bengkel Jasa Bubut & Mesin
-- =====================================================

-- =====================================================
-- 1. TABEL USERS (Autentikasi Login)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password: admin123 (di-hash dengan password_hash)
INSERT INTO users (username, password, nama_lengkap) VALUES
('admin', '$2y$12$j8BoXNrJDV8ZuzBHAE/mPOhPD2dLixG/PvzLfFgFAKuhyRZEURDGG', 'Administrator');

-- =====================================================
-- 2. TABEL SERVICE_ORDERS (Order Jasa Bubut/Mesin)
-- =====================================================
CREATE TABLE IF NOT EXISTS service_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20),
    jenis_mesin VARCHAR(100),
    jenis_jasa VARCHAR(100) NOT NULL,
    deskripsi_pekerjaan TEXT,
    biaya DECIMAL(12,2) NOT NULL DEFAULT 0,
    status ENUM('Antrian','Sedang Dikerjakan','Selesai') DEFAULT 'Antrian',
    tanggal_masuk DATETIME DEFAULT CURRENT_TIMESTAMP,
    tanggal_selesai DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 3. TABEL INVENTORY (Stok Barang / Bahan)
-- =====================================================
CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(150) NOT NULL,
    kategori VARCHAR(50),
    jumlah_stok INT NOT NULL DEFAULT 0,
    harga_beli DECIMAL(12,2) DEFAULT 0,
    satuan VARCHAR(20) DEFAULT 'pcs',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 4. TABEL DAILY_INCOMES (Pemasukan Harian)
-- =====================================================
CREATE TABLE IF NOT EXISTS daily_incomes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    sumber VARCHAR(150) NOT NULL,
    jumlah DECIMAL(12,2) NOT NULL,
    keterangan TEXT,
    order_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES service_orders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 5. TABEL EXPENSES (Pengeluaran)
-- =====================================================
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    kategori VARCHAR(50),
    deskripsi VARCHAR(255) NOT NULL,
    jumlah DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

