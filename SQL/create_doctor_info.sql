-- Enhanced Doctor Information Table Creation
-- This file creates a comprehensive doctor information system with additional features

-- Use the medical booking database
USE medical_booking;

-- Create enhanced doctor information table with additional fields
CREATE TABLE IF NOT EXISTS doctor_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    national_id VARCHAR(20) NOT NULL,
    date_of_birth DATE,
    gender ENUM('male', 'female') NOT NULL,
    nationality VARCHAR(50),
    marital_status ENUM('single', 'married', 'divorced', 'widowed'),
    address TEXT,
    emergency_contact VARCHAR(100),
    emergency_phone VARCHAR(20),
    languages_spoken TEXT,
    consultation_fee DECIMAL(10,2) DEFAULT 0.00,
    follow_up_fee DECIMAL(10,2) DEFAULT 0.00,
    emergency_fee DECIMAL(10,2) DEFAULT 0.00,
    accepts_insurance BOOLEAN DEFAULT FALSE,
    insurance_companies TEXT,
    education_details JSON,
    certifications JSON,
    awards JSON,
    publications JSON,
    research_interests TEXT,
    professional_memberships TEXT,
    hospital_affiliations JSON,
    clinic_locations JSON,
    working_hours JSON,
    appointment_duration INT DEFAULT 30,
    max_daily_appointments INT DEFAULT 20,
    online_consultation BOOLEAN DEFAULT FALSE,
    telemedicine_available BOOLEAN DEFAULT FALSE,
    home_visits BOOLEAN DEFAULT FALSE,
    special_interests TEXT,
    treatment_methods TEXT,
    equipment_used TEXT,
    success_rate DECIMAL(5,2),
    patient_satisfaction_rate DECIMAL(5,2),
    years_of_experience INT,
    total_patients_treated INT DEFAULT 0,
    verified BOOLEAN DEFAULT FALSE,
    verification_date TIMESTAMP NULL,
    background_check_completed BOOLEAN DEFAULT FALSE,
    malpractice_insurance BOOLEAN DEFAULT FALSE,
    insurance_expiry DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create doctor verification table
CREATE TABLE IF NOT EXISTS doctor_verification (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_info_id INT NOT NULL,
    verification_type ENUM('license', 'education', 'certification', 'background') NOT NULL,
    document_path VARCHAR(500),
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    verified_by VARCHAR(100),
    verification_date TIMESTAMP NULL,
    expiry_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_info_id) REFERENCES doctor_info(id) ON DELETE CASCADE
);

-- Create doctor schedule table
CREATE TABLE IF NOT EXISTS doctor_schedule (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_info_id INT NOT NULL,
    day_of_week ENUM('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    break_start TIME NULL,
    break_end TIME NULL,
    max_appointments INT DEFAULT 10,
    is_available BOOLEAN DEFAULT TRUE,
    location VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_info_id) REFERENCES doctor_info(id) ON DELETE CASCADE
);

-- Create doctor availability exceptions
CREATE TABLE IF NOT EXISTS doctor_availability_exceptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_info_id INT NOT NULL,
    exception_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    reason TEXT,
    is_available BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_info_id) REFERENCES doctor_info(id) ON DELETE CASCADE
);

-- Create doctor reviews and ratings table
CREATE TABLE IF NOT EXISTS doctor_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_info_id INT NOT NULL,
    patient_id INT NOT NULL,
    rating DECIMAL(3,2) NOT NULL CHECK (rating >= 0 AND rating <= 5),
    review_text TEXT,
    treatment_date DATE,
    wait_time_rating DECIMAL(3,2),
    staff_rating DECIMAL(3,2),
    facility_rating DECIMAL(3,2),
    communication_rating DECIMAL(3,2),
    would_recommend BOOLEAN DEFAULT TRUE,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_info_id) REFERENCES doctor_info(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create doctor specialties mapping table
CREATE TABLE IF NOT EXISTS doctor_specialties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_info_id INT NOT NULL,
    specialty_id INT NOT NULL,
    years_experience INT,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_info_id) REFERENCES doctor_info(id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_doctor_info_doctor_id ON doctor_info(doctor_id);
CREATE INDEX idx_doctor_info_license ON doctor_info(license_number);
CREATE INDEX idx_doctor_info_verified ON doctor_info(verified);
CREATE INDEX idx_doctor_info_experience ON doctor_info(years_of_experience);
CREATE INDEX idx_doctor_info_rating ON doctor_info(patient_satisfaction_rate);

CREATE INDEX idx_verification_doctor ON doctor_verification(doctor_info_id);
CREATE INDEX idx_verification_type ON doctor_verification(verification_type);
CREATE INDEX idx_verification_status ON doctor_verification(verification_status);

CREATE INDEX idx_schedule_doctor ON doctor_schedule(doctor_info_id);
CREATE INDEX idx_schedule_day ON doctor_schedule(day_of_week);
CREATE INDEX idx_schedule_available ON doctor_schedule(is_available);

CREATE INDEX idx_exceptions_doctor ON doctor_availability_exceptions(doctor_info_id);
CREATE INDEX idx_exceptions_date ON doctor_availability_exceptions(exception_date);

CREATE INDEX idx_reviews_doctor ON doctor_reviews(doctor_info_id);
CREATE INDEX idx_reviews_patient ON doctor_reviews(patient_id);
CREATE INDEX idx_reviews_rating ON doctor_reviews(rating);
CREATE INDEX idx_reviews_date ON doctor_reviews(created_at);

CREATE INDEX idx_specialties_doctor ON doctor_specialties(doctor_info_id);
CREATE INDEX idx_specialties_specialty ON doctor_specialties(specialty_id);

-- Create views for easy access
CREATE OR REPLACE VIEW doctor_complete_profile AS
SELECT
    di.id as doctor_info_id,
    d.full_name,
    d.email,
    d.phone,
    di.license_number,
    di.national_id,
    di.date_of_birth,
    di.gender,
    di.nationality,
    di.address,
    di.emergency_contact,
    di.emergency_phone,
    di.languages_spoken,
    di.consultation_fee,
    di.follow_up_fee,
    di.emergency_fee,
    di.accepts_insurance,
    di.education_details,
    di.certifications,
    di.awards,
    di.publications,
    di.research_interests,
    di.years_of_experience,
    di.special_interests,
    di.treatment_methods,
    di.success_rate,
    di.patient_satisfaction_rate,
    di.total_patients_treated,
    di.verified,
    di.verification_date,
    di.created_at,
    di.updated_at,
    AVG(dr.rating) as average_rating,
    COUNT(dr.id) as total_reviews
FROM doctor_info di
JOIN doctors d ON di.doctor_id = d.id
LEFT JOIN doctor_reviews dr ON di.id = dr.doctor_info_id
GROUP BY di.id;

-- Create stored procedures for doctor management
DELIMITER //

-- Procedure to create new doctor info
CREATE PROCEDURE CreateDoctorInfo(
    IN p_doctor_id INT,
    IN p_license_number VARCHAR(50),
    IN p_national_id VARCHAR(20),
    IN p_date_of_birth DATE,
    IN p_gender ENUM('male', 'female'),
    IN p_nationality VARCHAR(50),
    IN p_address TEXT,
    IN p_emergency_contact VARCHAR(100),
    IN p_emergency_phone VARCHAR(20),
    IN p_languages_spoken TEXT,
    IN p_consultation_fee DECIMAL(10,2),
    IN p_years_of_experience INT,
    IN p_special_interests TEXT
)
BEGIN
    INSERT INTO doctor_info (
        doctor_id, license_number, national_id, date_of_birth, gender,
        nationality, address, emergency_contact, emergency_phone,
        languages_spoken, consultation_fee, years_of_experience,
        special_interests, created_at
    ) VALUES (
        p_doctor_id, p_license_number, p_national_id, p_date_of_birth, p_gender,
        p_nationality, p_address, p_emergency_contact, p_emergency_phone,
        p_languages_spoken, p_consultation_fee, p_years_of_experience,
        p_special_interests, NOW()
    );

    SELECT LAST_INSERT_ID() as doctor_info_id;
END //

-- Procedure to update doctor availability
CREATE PROCEDURE UpdateDoctorSchedule(
    IN p_doctor_info_id INT,
    IN p_day_of_week VARCHAR(10),
    IN p_start_time TIME,
    IN p_end_time TIME,
    IN p_max_appointments INT
)
BEGIN
    INSERT INTO doctor_schedule (doctor_info_id, day_of_week, start_time, end_time, max_appointments)
    VALUES (p_doctor_info_id, p_day_of_week, p_start_time, p_end_time, p_max_appointments)
    ON DUPLICATE KEY UPDATE
    start_time = p_start_time,
    end_time = p_end_time,
    max_appointments = p_max_appointments;
END //

-- Procedure to add doctor review
CREATE PROCEDURE AddDoctorReview(
    IN p_doctor_info_id INT,
    IN p_patient_id INT,
    IN p_rating DECIMAL(3,2),
    IN p_review_text TEXT,
    IN p_treatment_date DATE
)
BEGIN
    INSERT INTO doctor_reviews (doctor_info_id, patient_id, rating, review_text, treatment_date, created_at)
    VALUES (p_doctor_info_id, p_patient_id, p_rating, p_review_text, p_treatment_date, NOW());

    -- Update average rating in doctor_info
    UPDATE doctor_info
    SET patient_satisfaction_rate = (
        SELECT AVG(rating)
        FROM doctor_reviews
        WHERE doctor_info_id = p_doctor_info_id
    )
    WHERE id = p_doctor_info_id;
END //

DELIMITER ;

-- Insert sample data
INSERT INTO doctor_info (
    doctor_id, license_number, national_id, date_of_birth, gender, nationality,
    address, emergency_contact, emergency_phone, languages_spoken,
    consultation_fee, years_of_experience, special_interests, verified
) VALUES
(1, 'MED-2024-001', '1234567890', '1980-05-15', 'male', 'Saudi',
 'Riyadh, King Fahad Road', 'Ahmed Ali', '+966 50 111 1111', 'Arabic, English',
 200.00, 15, 'Cardiology, Heart Diseases', TRUE),
(2, 'MED-2024-002', '0987654321', '1985-08-22', 'female', 'Saudi',
 'Jeddah, Prince Sultan Street', 'Fatima Ahmed', '+966 50 111 1112', 'Arabic, English, French',
 180.00, 12, 'Cardiology, Women Heart Health', TRUE),
(3, 'MED-2024-003', '1122334455', '1978-12-10', 'male', 'Saudi',
 'Dammam, King Khalid Street', 'Khalid Abdullah', '+966 50 111 1113', 'Arabic, English',
 220.00, 18, 'Cardiology, Interventional Cardiology', TRUE);

-- Insert sample schedules
INSERT INTO doctor_schedule (doctor_info_id, day_of_week, start_time, end_time, max_appointments)
VALUES
(1, 'sunday', '09:00:00', '17:00:00', 10),
(1, 'monday', '09:00:00', '17:00:00', 10),
(1, 'tuesday', '09:00:00', '17:00:00', 10),
(2, 'sunday', '10:00:00', '18:00:00', 8),
(2, 'monday', '10:00:00', '18:00:00', 8),
(3, 'sunday', '08:00:00', '16:00:00', 12);

-- Display success message
SELECT 'Doctor information system created successfully!' as Status;
SELECT 'Tables created: doctor_info, doctor_verification, doctor_schedule, doctor_availability_exceptions, doctor_reviews, doctor_specialties' as Tables_Created;
SELECT 'Views created: doctor_complete_profile' as Views_Created;
SELECT 'Procedures created: CreateDoctorInfo, UpdateDoctorSchedule, AddDoctorReview' as Procedures_Created;
