-- ========================================
-- Medical Appointment System - Database Migration Fixes
-- ========================================
-- This script fixes all identified database schema issues
-- Run this after your existing database is set up

USE medical_booking;

-- ========================================
-- 1. Fix Users Table - Add missing columns if they don't exist
-- ========================================

-- Add name column if it doesn't exist (for backward compatibility)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = 'medical_booking'
     AND TABLE_NAME = 'users'
     AND COLUMN_NAME = 'name') = 0,
    'ALTER TABLE users ADD COLUMN name VARCHAR(100) AFTER full_name',
    'SELECT "name column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add profile_image column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = 'medical_booking'
     AND TABLE_NAME = 'users'
     AND COLUMN_NAME = 'profile_image') = 0,
    'ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) AFTER phone',
    'SELECT "profile_image column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- 2. Fix Appointments Table - Add missing columns if they don't exist
-- ========================================

-- Add patient_id column if it doesn't exist (for backward compatibility)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = 'medical_booking'
     AND TABLE_NAME = 'appointments'
     AND COLUMN_NAME = 'patient_id') = 0,
    'ALTER TABLE appointments ADD COLUMN patient_id INT AFTER user_id',
    'SELECT "patient_id column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update patient_id to match user_id for existing records
UPDATE appointments SET patient_id = user_id WHERE patient_id IS NULL;

-- ========================================
-- 3. Create Missing Reminders Table
-- ========================================

-- Check if reminders table exists, if not create it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
     WHERE TABLE_SCHEMA = 'medical_booking'
     AND TABLE_NAME = 'reminders') = 0,
    'CREATE TABLE reminders (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        appointment_id INT NOT NULL,
        reminder_type ENUM("email", "sms", "push") NOT NULL,
        reminder_time DATETIME NOT NULL,
        message TEXT,
        is_sent TINYINT(1) DEFAULT 0,
        sent_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
    )',
    'SELECT "reminders table already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- 4. Fix Database Connection Issues
-- ========================================

-- Ensure all foreign key constraints are properly set
-- This will help identify any orphaned records

-- Check for orphaned appointments
SELECT 'Orphaned appointments (no user):' as message;
SELECT a.id, a.user_id FROM appointments a
LEFT JOIN users u ON a.user_id = u.id
WHERE u.id IS NULL;

SELECT 'Orphaned appointments (no doctor):' as message;
SELECT a.id, a.doctor_id FROM appointments a
LEFT JOIN doctors d ON a.doctor_id = d.id
WHERE d.id IS NULL;

SELECT 'Orphaned appointments (no clinic):' as message;
SELECT a.id, a.clinic_id FROM appointments a
LEFT JOIN clinics c ON a.clinic_id = c.id
WHERE c.id IS NULL;

-- ========================================
-- 5. Add Missing Indexes for Performance
-- ========================================

-- Add indexes if they don't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE TABLE_SCHEMA = 'medical_booking'
     AND TABLE_NAME = 'appointments'
     AND INDEX_NAME = 'idx_appointments_patient') = 0,
    'CREATE INDEX idx_appointments_patient ON appointments(patient_id)',
    'SELECT "patient index already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- 6. Clean Up Duplicate Data
-- ========================================

-- Remove duplicate cities (keep only the first occurrence)
DELETE c1 FROM cities c1
INNER JOIN cities c2
WHERE c1.id > c2.id
AND c1.name = c2.name
AND c1.governorate = c2.governorate;

-- Remove duplicate hospitals (keep only the first occurrence)
DELETE h1 FROM hospitals h1
INNER JOIN hospitals h2
WHERE h1.id > h2.id
AND h1.name = h2.name
AND h1.address = h2.address;

-- ========================================
-- 7. Update Database Statistics
-- ========================================

-- Update table statistics for better query performance
ANALYZE TABLE users, appointments, doctors, clinics, hospitals, cities;

-- ========================================
-- 8. Create Missing Views
-- ========================================

-- Create or replace the appointment_info_view
DROP VIEW IF EXISTS appointment_info_view;
CREATE VIEW appointment_info_view AS
SELECT
    a.id,
    a.appointment_date,
    a.appointment_time,
    a.appointment_datetime,
    a.status,
    a.notes,
    a.created_at,
    u.full_name as patient_name,
    u.phone as patient_phone,
    u.email as patient_email,
    d.full_name as doctor_name,
    d.phone as doctor_phone,
    c.name as clinic_name,
    h.name as hospital_name,
    s.name as specialty_name
FROM appointments a
JOIN users u ON a.user_id = u.id
JOIN doctors d ON a.doctor_id = d.id
JOIN clinics c ON a.clinic_id = c.id
JOIN hospitals h ON c.hospital_id = h.id
JOIN specialties s ON d.specialty_id = s.id;

-- ========================================
-- 9. Verify Database Integrity
-- ========================================

-- Check for any remaining issues
SELECT 'Database migration completed successfully!' as status;

-- Show table counts
SELECT 'users' as table_name, COUNT(*) as record_count FROM users
UNION ALL
SELECT 'appointments', COUNT(*) FROM appointments
UNION ALL
SELECT 'doctors', COUNT(*) FROM doctors
UNION ALL
SELECT 'clinics', COUNT(*) FROM clinics
UNION ALL
SELECT 'hospitals', COUNT(*) FROM hospitals
UNION ALL
SELECT 'cities', COUNT(*) FROM cities
UNION ALL
SELECT 'specialties', COUNT(*) FROM specialties;

-- ========================================
-- Migration Complete
-- ========================================
