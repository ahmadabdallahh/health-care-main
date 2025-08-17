-- Generate Doctors SQL File - Duplicate Safe Version
-- ملف إنشاء الأطباء - نسخة آمنة من التكرار

-- First, ensure we have the required tables and data
USE medical_booking;

-- Create specialties if they don't exist
INSERT IGNORE INTO specialties (name, description, icon) VALUES
('طب القلب', 'تشخيص وعلاج أمراض القلب والأوعية الدموية', 'fas fa-heartbeat'),
('طب الأعصاب', 'تشخيص وعلاج أمراض الجهاز العصبي', 'fas fa-brain'),
('طب الجهاز الهضمي', 'تشخيص وعلاج أمراض الجهاز الهضمي', 'fas fa-stomach'),
('طب الرئة', 'تشخيص وعلاج أمراض الجهاز التنفسي', 'fas fa-lungs'),
('جراحة القلب', 'عمليات القلب المفتوح وجراحات الصدر', 'fas fa-heart'),
('جراحة المخ والأعصاب', 'عمليات المخ والعمود الفقري', 'fas fa-brain'),
('جراحة العظام', 'علاج كسور العظام وأمراض المفاصل', 'fas fa-bone'),
('طب الأطفال', 'رعاية الأطفال من الولادة حتى 18 سنة', 'fas fa-baby'),
('طب الأسنان', 'علاج الأسنان واللثة وتقويم الأسنان', 'fas fa-tooth'),
('طب العيون', 'تشخيص وعلاج أمراض العيون', 'fas fa-eye'),
('طب الجلد', 'تشخيص وعلاج أمراض الجلد', 'fas fa-user-md'),
('طب النساء', 'رعاية صحة المرأة والحمل والولادة', 'fas fa-female'),
('طب المسالك البولية', 'تشخيص وعلاج أمراض الجهاز البولي', 'fas fa-kidney'),
('طب الأنف والأذن والحنجرة', 'تشخيص وعلاج أمراض الأنف والأذن والحنجرة', 'fas fa-ear'),
('طب النفسية', 'تشخيص وعلاج الأمراض النفسية', 'fas fa-brain'),
('طب التخدير', 'تخدير المرضى للعمليات الجراحية', 'fas fa-syringe');

-- Create cities if they don't exist
INSERT IGNORE INTO cities (name, governorate) VALUES
('الرياض', 'الرياض'),
('جدة', 'مكة المكرمة'),
('الدمام', 'الشرقية'),
('مكة المكرمة', 'مكة المكرمة'),
('المدينة المنورة', 'المدينة المنورة'),
('تبوك', 'تبوك'),
('بريدة', 'القصيم'),
('خميس مشيط', 'عسير'),
('حائل', 'حائل'),
('أبها', 'عسير');

-- Create hospitals if they don't exist
INSERT IGNORE INTO hospitals (name, address, phone, email, website, description, image, type, rating, is_24h) VALUES
('مستشفى الملك فهد', 'شارع الملك فهد، حي النزهة، الرياض', '+966 11 123 4567', 'info@kfh.com', 'www.kfh.com', 'مستشفى متخصص في علاج أمراض القلب والجراحات المتقدمة', 'hospital-1.jpg', 'حكومي', 4.8, 1),
('مركز الأمير سلطان الطبي', 'شارع التحلية، حي الكورنيش، جدة', '+966 12 234 5678', 'info@pstc.com', 'www.pstc.com', 'مركز طبي متقدم في طب العيون وجراحات التجميل', 'hospital-2.jpg', 'خاص', 4.9, 1),
('مستشفى الملك خالد', 'شارع الملك خالد، حي الشاطئ، الدمام', '+966 13 345 6789', 'info@kkh.com', 'www.kkh.com', 'مستشفى متخصص في طب الأعصاب وجراحات العظام', 'hospital-3.jpg', 'حكومي', 4.7, 1),
('مستشفى الملك عبدالعزيز', 'شارع التحلية، حي الكورنيش، جدة', '+966 12 456 7890', 'info@kauh.com', 'www.kauh.com', 'مستشفى جامعي متقدم في جميع التخصصات', 'hospital-4.jpg', 'حكومي', 4.6, 1),
('مركز الملك فهد الطبي', 'شارع الملك فهد، حي النزهة، الرياض', '+966 11 567 8901', 'info@kfmc.com', 'www.kfmc.com', 'مركز طبي متخصص في علاج السرطان', 'hospital-5.jpg', 'حكومي', 4.8, 1);

-- Check if doctors already exist and delete them if they do
DELETE FROM working_hours WHERE doctor_id IN (SELECT id FROM doctors WHERE specialty IN ('طب القلب', 'طب الأعصاب', 'طب الجهاز الهضمي', 'طب الرئة', 'جراحة القلب', 'جراحة المخ والأعصاب', 'جراحة العظام', 'طب الأطفال', 'طب الأسنان', 'طب العيون', 'طب الجلد', 'طب النساء', 'طب المسالك البولية', 'طب الأنف والأذن والحنجرة', 'طب النفسية', 'طب التخدير'));

DELETE FROM doctors WHERE specialty IN ('طب القلب', 'طب الأعصاب', 'طب الجهاز الهضمي', 'طب الرئة', 'جراحة القلب', 'جراحة المخ والأعصاب', 'جراحة العظام', 'طب الأطفال', 'طب الأسنان', 'طب العيون', 'طب الجلد', 'طب النساء', 'طب المسالك البولية', 'طب الأنف والأذن والحنجرة', 'طب النفسية', 'طب التخدير');

DELETE FROM users WHERE username LIKE 'dr.%' AND role = 'doctor';

-- Generate Users (Doctors) - Using INSERT IGNORE to prevent duplicates
INSERT IGNORE INTO users (username, email, password, full_name, phone, date_of_birth, gender, role, created_at) VALUES
-- Cardiology Doctors
('dr.ahmed.cardio', 'ahmed.cardio@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. أحمد محمد العلي', '+966 50 111 1111', '1980-05-15', 'male', 'doctor', NOW()),
('dr.fatima.heart', 'fatima.heart@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. فاطمة أحمد الزهراني', '+966 50 111 1112', '1985-08-22', 'female', 'doctor', NOW()),
('dr.khalid.cardio', 'khalid.cardio@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. خالد عبدالله السعد', '+966 50 111 1113', '1978-12-10', 'male', 'doctor', NOW()),

-- Neurology Doctors
('dr.sara.neuro', 'sara.neuro@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. سارة محمد القحطاني', '+966 50 111 1114', '1982-03-18', 'female', 'doctor', NOW()),
('dr.omar.brain', 'omar.brain@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. عمر أحمد الشمري', '+966 50 111 1115', '1975-07-25', 'male', 'doctor', NOW()),
('dr.noor.neuro', 'noor.neuro@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. نور عبدالرحمن الدوسري', '+966 50 111 1116', '1988-11-30', 'female', 'doctor', NOW()),

-- Gastroenterology Doctors
('dr.yousef.gastro', 'yousef.gastro@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. يوسف محمد الحربي', '+966 50 111 1117', '1981-04-12', 'male', 'doctor', NOW()),
('dr.layla.digestive', 'layla.digestive@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. ليلى أحمد الغامدي', '+966 50 111 1118', '1983-09-05', 'female', 'doctor', NOW()),
('dr.abdul.gastro', 'abdul.gastro@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. عبدالله محمد العنزي', '+966 50 111 1119', '1979-06-20', 'male', 'doctor', NOW()),

-- Pulmonary Doctors
('dr.mariam.lung', 'mariam.lung@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. مريم أحمد السلمي', '+966 50 111 1120', '1984-01-15', 'female', 'doctor', NOW()),
('dr.hassan.respiratory', 'hassan.respiratory@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. حسن محمد المطيري', '+966 50 111 1121', '1980-10-08', 'male', 'doctor', NOW()),

-- Cardiac Surgery Doctors
('dr.ali.cardiac.surgeon', 'ali.cardiac.surgeon@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. علي أحمد القحطاني', '+966 50 111 1122', '1975-03-25', 'male', 'doctor', NOW()),
('dr.raghad.heart.surgeon', 'raghad.heart.surgeon@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. رغد محمد الشمري', '+966 50 111 1123', '1982-07-12', 'female', 'doctor', NOW()),

-- Neurosurgery Doctors
('dr.saad.neurosurgeon', 'saad.neurosurgeon@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. سعد أحمد الدوسري', '+966 50 111 1124', '1978-12-03', 'male', 'doctor', NOW()),
('dr.huda.brain.surgeon', 'huda.brain.surgeon@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. هدى محمد العنزي', '+966 50 111 1125', '1985-05-18', 'female', 'doctor', NOW()),

-- Orthopedic Surgery Doctors
('dr.mohammed.ortho', 'mohammed.ortho@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. محمد أحمد الحربي', '+966 50 111 1126', '1981-08-30', 'male', 'doctor', NOW()),
('dr.amal.bone', 'amal.bone@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. أمل محمد الغامدي', '+966 50 111 1127', '1983-11-22', 'female', 'doctor', NOW()),
('dr.fahad.ortho', 'fahad.ortho@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. فهد أحمد السلمي', '+966 50 111 1128', '1979-04-15', 'male', 'doctor', NOW()),

-- Pediatric Doctors
('dr.nouf.pediatrician', 'nouf.pediatrician@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. نوف أحمد المطيري', '+966 50 111 1129', '1986-02-28', 'female', 'doctor', NOW()),
('dr.sultan.child', 'sultan.child@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. سلطان محمد القحطاني', '+966 50 111 1130', '1980-09-10', 'male', 'doctor', NOW()),
('dr.ghada.pediatrician', 'ghada.pediatrician@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. غادة أحمد الشمري', '+966 50 111 1131', '1984-06-05', 'female', 'doctor', NOW()),

-- Dental Doctors
('dr.abdulrahman.dental', 'abdulrahman.dental@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. عبدالرحمن محمد الدوسري', '+966 50 111 1132', '1982-01-20', 'male', 'doctor', NOW()),
('dr.reem.tooth', 'reem.tooth@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. ريم أحمد العنزي', '+966 50 111 1133', '1987-12-08', 'female', 'doctor', NOW()),
('dr.turki.dental', 'turki.dental@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. تركي محمد الحربي', '+966 50 111 1134', '1981-07-14', 'male', 'doctor', NOW()),

-- Ophthalmology Doctors
('dr.dalia.eye', 'dalia.eye@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. داليا أحمد الغامدي', '+966 50 111 1135', '1985-03-25', 'female', 'doctor', NOW()),
('dr.khalil.ophthalmologist', 'khalil.ophthalmologist@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. خليل محمد السلمي', '+966 50 111 1136', '1978-11-12', 'male', 'doctor', NOW()),

-- Dermatology Doctors
('dr.lama.skin', 'lama.skin@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. لمى أحمد المطيري', '+966 50 111 1137', '1983-08-18', 'female', 'doctor', NOW()),
('dr.wael.dermatologist', 'wael.dermatologist@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. وائل محمد القحطاني', '+966 50 111 1138', '1980-05-30', 'male', 'doctor', NOW()),

-- Gynecology Doctors
('dr.manal.gyno', 'manal.gyno@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. منال أحمد الشمري', '+966 50 111 1139', '1982-10-22', 'female', 'doctor', NOW()),
('dr.ibrahim.obgyn', 'ibrahim.obgyn@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. إبراهيم محمد الدوسري', '+966 50 111 1140', '1977-04-15', 'male', 'doctor', NOW()),

-- Urology Doctors
('dr.ahmed.urology', 'ahmed.urology@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. أحمد محمد العنزي', '+966 50 111 1141', '1981-12-08', 'male', 'doctor', NOW()),
('dr.salwa.urologist', 'salwa.urologist@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. سلوى أحمد الحربي', '+966 50 111 1142', '1984-07-20', 'female', 'doctor', NOW()),

-- ENT Doctors
('dr.basem.ent', 'basem.ent@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. باسم محمد الغامدي', '+966 50 111 1143', '1980-09-12', 'male', 'doctor', NOW()),
('dr.rania.ent', 'rania.ent@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. رانيا أحمد السلمي', '+966 50 111 1144', '1983-02-25', 'female', 'doctor', NOW()),

-- Psychiatry Doctors
('dr.samir.psych', 'samir.psych@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. سمير محمد المطيري', '+966 50 111 1145', '1979-06-18', 'male', 'doctor', NOW()),
('dr.zeina.psychiatrist', 'zeina.psychiatrist@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. زينة أحمد القحطاني', '+966 50 111 1146', '1985-11-30', 'female', 'doctor', NOW()),

-- Anesthesiology Doctors
('dr.mazen.anesthesia', 'mazen.anesthesia@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. مازن أحمد الشمري', '+966 50 111 1147', '1982-03-14', 'male', 'doctor', NOW()),
('dr.nadine.anesthesiologist', 'nadine.anesthesiologist@shifa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'د. نادين أحمد الدوسري', '+966 50 111 1148', '1986-08-22', 'female', 'doctor', NOW());

-- Generate Doctor Profiles - Using INSERT IGNORE to prevent duplicates
INSERT IGNORE INTO doctors (user_id, specialty, experience_years, consultation_fee, bio, hospital_id, is_active, rating, created_at) VALUES
-- Cardiology Doctors
(LAST_INSERT_ID()-47, 'طب القلب', 15, 200.00, 'طبيب قلب متخصص في علاج أمراض القلب والأوعية الدموية مع خبرة 15 عاماً في المستشفيات الحكومية والخاصة', 1, 1, 4.8, NOW()),
(LAST_INSERT_ID()-46, 'طب القلب', 12, 180.00, 'طبيبة قلب متخصصة في أمراض القلب عند النساء مع خبرة في علاج أمراض القلب الخلقية', 1, 1, 4.9, NOW()),
(LAST_INSERT_ID()-45, 'طب القلب', 18, 220.00, 'طبيب قلب متخصص في قسطرة القلب والتدخلات العلاجية مع خبرة طويلة في المستشفيات المتقدمة', 2, 1, 4.7, NOW()),

-- Neurology Doctors
(LAST_INSERT_ID()-44, 'طب الأعصاب', 13, 190.00, 'طبيبة أعصاب متخصصة في علاج الصرع وأمراض الأعصاب عند الأطفال', 1, 1, 4.8, NOW()),
(LAST_INSERT_ID()-43, 'طب الأعصاب', 20, 250.00, 'طبيب أعصاب متخصص في علاج السكتة الدماغية وأمراض الأوعية الدموية الدماغية', 3, 1, 4.9, NOW()),
(LAST_INSERT_ID()-42, 'طب الأعصاب', 10, 170.00, 'طبيبة أعصاب متخصصة في علاج الصداع النصفي وأمراض الأعصاب الطرفية', 2, 1, 4.6, NOW()),

-- Gastroenterology Doctors
(LAST_INSERT_ID()-41, 'طب الجهاز الهضمي', 16, 200.00, 'طبيب جهاز هضمي متخصص في تنظير القولون والمعدة مع خبرة في علاج السرطانات', 1, 1, 4.8, NOW()),
(LAST_INSERT_ID()-40, 'طب الجهاز الهضمي', 14, 190.00, 'طبيبة جهاز هضمي متخصصة في أمراض الكبد والتهابات الأمعاء', 2, 1, 4.7, NOW()),
(LAST_INSERT_ID()-39, 'طب الجهاز الهضمي', 17, 210.00, 'طبيب جهاز هضمي متخصص في علاج البنكرياس والمرارة', 3, 1, 4.8, NOW()),

-- Pulmonary Doctors
(LAST_INSERT_ID()-38, 'طب الرئة', 15, 200.00, 'طبيبة رئة متخصصة في علاج الربو وأمراض الرئة المزمنة', 1, 1, 4.8, NOW()),
(LAST_INSERT_ID()-37, 'طب الرئة', 18, 220.00, 'طبيب رئة متخصص في علاج سرطان الرئة وأمراض الرئة المهنية', 2, 1, 4.9, NOW()),

-- Cardiac Surgery Doctors
(LAST_INSERT_ID()-36, 'جراحة القلب', 22, 350.00, 'جراح قلب متخصص في عمليات القلب المفتوح وجراحات الصمامات', 1, 1, 4.9, NOW()),
(LAST_INSERT_ID()-35, 'جراحة القلب', 19, 320.00, 'جراحة قلب متخصصة في جراحات القلب بالمنظار وجراحات القلب المفتوح', 2, 1, 4.8, NOW()),

-- Neurosurgery Doctors
(LAST_INSERT_ID()-34, 'جراحة المخ والأعصاب', 25, 400.00, 'جراح مخ وأعصاب متخصص في عمليات المخ والعمود الفقري', 1, 1, 4.9, NOW()),
(LAST_INSERT_ID()-33, 'جراحة المخ والأعصاب', 20, 380.00, 'جراحة مخ وأعصاب متخصصة في جراحات الأورام الدماغية', 3, 1, 4.8, NOW()),

-- Orthopedic Surgery Doctors
(LAST_INSERT_ID()-32, 'جراحة العظام', 18, 280.00, 'جراح عظام متخصص في جراحات المفاصل الاصطناعية والكسور', 1, 1, 4.8, NOW()),
(LAST_INSERT_ID()-31, 'جراحة العظام', 15, 260.00, 'جراحة عظام متخصصة في جراحات العمود الفقري والكسور', 2, 1, 4.7, NOW()),
(LAST_INSERT_ID()-30, 'جراحة العظام', 19, 290.00, 'جراح عظام متخصص في جراحات الرياضيين وإصابات الملاعب', 3, 1, 4.8, NOW()),

-- Pediatric Doctors
(LAST_INSERT_ID()-29, 'طب الأطفال', 12, 150.00, 'طبيبة أطفال متخصصة في رعاية الأطفال حديثي الولادة والرضع', 1, 1, 4.8, NOW()),
(LAST_INSERT_ID()-28, 'طب الأطفال', 16, 170.00, 'طبيب أطفال متخصص في أمراض القلب الخلقية عند الأطفال', 2, 1, 4.9, NOW()),
(LAST_INSERT_ID()-27, 'طب الأطفال', 13, 160.00, 'طبيبة أطفال متخصصة في أمراض الجهاز التنفسي عند الأطفال', 3, 1, 4.7, NOW()),

-- Dental Doctors
(LAST_INSERT_ID()-26, 'طب الأسنان', 14, 120.00, 'طبيب أسنان متخصص في تقويم الأسنان وزراعة الأسنان', 2, 1, 4.8, NOW()),
(LAST_INSERT_ID()-25, 'طب الأسنان', 11, 110.00, 'طبيبة أسنان متخصصة في علاج اللثة وجراحة الفم', 1, 1, 4.7, NOW()),
(LAST_INSERT_ID()-24, 'طب الأسنان', 16, 130.00, 'طبيب أسنان متخصص في جراحة الفم والوجه والفكين', 3, 1, 4.8, NOW()),

-- Ophthalmology Doctors
(LAST_INSERT_ID()-23, 'طب العيون', 17, 200.00, 'طبيبة عيون متخصصة في جراحات الشبكية والليزر', 2, 1, 4.9, NOW()),
(LAST_INSERT_ID()-22, 'طب العيون', 23, 250.00, 'طبيب عيون متخصص في جراحات المياه البيضاء والزرقاء', 1, 1, 4.8, NOW()),

-- Dermatology Doctors
(LAST_INSERT_ID()-21, 'طب الجلد', 15, 180.00, 'طبيبة جلد متخصصة في علاج الأمراض الجلدية والليزر', 2, 1, 4.8, NOW()),
(LAST_INSERT_ID()-20, 'طب الجلد', 18, 200.00, 'طبيب جلد متخصص في علاج السرطانات الجلدية والجراحات التجميلية', 1, 1, 4.7, NOW()),

-- Gynecology Doctors
(LAST_INSERT_ID()-19, 'طب النساء', 16, 200.00, 'طبيبة نساء متخصصة في علاج العقم والحمل عالي الخطورة', 1, 1, 4.8, NOW()),
(LAST_INSERT_ID()-18, 'طب النساء', 20, 220.00, 'طبيب نساء متخصص في جراحات النساء والمنظار', 3, 1, 4.9, NOW()),

-- Urology Doctors
(LAST_INSERT_ID()-17, 'طب المسالك البولية', 17, 220.00, 'طبيب مسالك بولية متخصص في جراحات البروستاتا والكلى', 1, 1, 4.8, NOW()),
(LAST_INSERT_ID()-16, 'طب المسالك البولية', 14, 200.00, 'طبيبة مسالك بولية متخصصة في أمراض النساء البولية', 2, 1, 4.7, NOW()),

-- ENT Doctors
(LAST_INSERT_ID()-15, 'طب الأنف والأذن والحنجرة', 18, 200.00, 'طبيب أنف وأذن وحنجرة متخصص في جراحات الأذن والسمع', 1, 1, 4.8, NOW()),
(LAST_INSERT_ID()-14, 'طب الأنف والأذن والحنجرة', 15, 190.00, 'طبيبة أنف وأذن وحنجرة متخصصة في جراحات الأنف والجيوب', 2, 1, 4.7, NOW()),

-- Psychiatry Doctors
(LAST_INSERT_ID()-13, 'طب النفسية', 19, 180.00, 'طبيب نفسي متخصص في علاج الاكتئاب والقلق', 1, 1, 4.8, NOW()),
(LAST_INSERT_ID()-12, 'طب النفسية', 13, 160.00, 'طبيبة نفسية متخصصة في علاج الأطفال والمراهقين', 2, 1, 4.7, NOW()),

-- Anesthesiology Doctors
(LAST_INSERT_ID()-11, 'طب التخدير', 16, 250.00, 'طبيب تخدير متخصص في تخدير القلب والعمليات المعقدة', 1, 1, 4.8, NOW()),
(LAST_INSERT_ID()-10, 'طب التخدير', 12, 220.00, 'طبيبة تخدير متخصصة في تخدير الأطفال والنساء الحوامل', 2, 1, 4.7, NOW());

-- Create working hours for doctors - Using INSERT IGNORE to prevent duplicates
INSERT IGNORE INTO working_hours (doctor_id, day_of_week, start_time, end_time, is_available) VALUES
-- Sample working hours for first 10 doctors
(1, 'sunday', '09:00:00', '17:00:00', 1),
(1, 'monday', '09:00:00', '17:00:00', 1),
(1, 'tuesday', '09:00:00', '17:00:00', 1),
(1, 'wednesday', '09:00:00', '17:00:00', 1),
(1, 'thursday', '09:00:00', '17:00:00', 1),

(2, 'sunday', '10:00:00', '18:00:00', 1),
(2, 'monday', '10:00:00', '18:00:00', 1),
(2, 'tuesday', '10:00:00', '18:00:00', 1),
(2, 'wednesday', '10:00:00', '18:00:00', 1),
(2, 'thursday', '10:00:00', '18:00:00', 1),

(3, 'sunday', '08:00:00', '16:00:00', 1),
(3, 'monday', '08:00:00', '16:00:00', 1),
(3, 'tuesday', '08:00:00', '16:00:00', 1),
(3, 'wednesday', '08:00:00', '16:00:00', 1),
(3, 'thursday', '08:00:00', '16:00:00', 1);

-- Display summary
SELECT
    'Doctors Generated Successfully!' as Status,
    COUNT(*) as Total_Doctors,
    COUNT(DISTINCT specialty) as Total_Specialties,
    COUNT(DISTINCT hospital_id) as Total_Hospitals
FROM doctors;

SELECT
    specialty,
    COUNT(*) as Doctor_Count,
    AVG(rating) as Average_Rating,
    AVG(consultation_fee) as Average_Fee
FROM doctors
GROUP BY specialty
ORDER BY Doctor_Count DESC;
