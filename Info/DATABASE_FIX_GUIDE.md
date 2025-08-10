# 🔧 دليل إصلاح قاعدة البيانات

## المشكلة المبلغ عنها
```
CREATE INDEX idx_doctors_hospital ON doctors(hospital_id); 
MySQL said: Documentation #1072 - Key column 'hospital_id' doesn't exist in table
```

## سبب المشكلة
كان الخطأ يحدث لأن:
1. جدول `doctors` الأصلي لا يحتوي على عمود `hospital_id`
2. كان يتم محاولة إنشاء فهرس على عمود غير موجود
3. لم يتم إضافة العمود قبل إنشاء الفهرس

## الحل المطبق

### 1. إنشاء ملف SQL جديد
تم إنشاء ملف `SQL/reminder_tables.sql` الذي يحتوي على:

#### أ. إضافة الأعمدة المفقودة أولاً
```sql
-- إضافة أعمدة جديدة لجدول الأطباء
ALTER TABLE doctors
ADD COLUMN IF NOT EXISTS hospital_id INT NULL,
ADD COLUMN IF NOT EXISTS department_id INT NULL,
ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS consultation_fee DECIMAL(10,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS bio TEXT NULL,
ADD COLUMN IF NOT EXISTS rating DECIMAL(3,2) DEFAULT 0.00,
ADD FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE SET NULL,
ADD FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL;
```

#### ب. إنشاء الجداول الجديدة
- `reminder_settings` - إعدادات التذكيرات للمستخدمين
- `reminder_logs` - سجل التذكيرات المرسلة
- `push_notifications` - الإشعارات الفورية
- `cities` - المدن
- `departments` - الأقسام
- `working_hours` - أوقات العمل للأطباء

#### ج. إنشاء الفهارس بعد إضافة الأعمدة
```sql
-- إنشاء فهارس لتحسين الأداء
CREATE INDEX idx_doctors_hospital ON doctors(hospital_id);
CREATE INDEX idx_doctors_active ON doctors(is_active);
-- ... باقي الفهارس
```

## كيفية تطبيق الإصلاح

### الخطوة 1: تشغيل ملف SQL الجديد
```bash
mysql -u username -p database_name < SQL/reminder_tables.sql
```

### الخطوة 2: اختبار الإصلاح
```bash
php test_database_schema.php
```

### الخطوة 3: التحقق من النتائج
يجب أن تظهر النتائج التالية:
- ✅ وجود عمود `hospital_id` في جدول `doctors`
- ✅ وجود جميع الجداول الجديدة
- ✅ وجود جميع الفهارس المطلوبة

## الميزات الجديدة المضافة

### 1. نظام التذكيرات المتقدم
- تذكيرات عبر البريد الإلكتروني
- تذكيرات عبر SMS
- إشعارات فورية
- إعدادات مخصصة لكل مستخدم

### 2. تحسينات قاعدة البيانات
- إضافة عمود `role` للمستخدمين (patient, doctor, hospital)
- إضافة معلومات التأمين للمرضى
- إضافة معلومات المدن والمناطق
- إضافة أقسام المستشفيات
- إضافة أوقات العمل للأطباء

### 3. فهارس محسنة للأداء
- فهارس على الأعمدة الأكثر استخداماً
- تحسين سرعة البحث والاستعلامات

## الملفات المحدثة

1. **`SQL/reminder_tables.sql`** - ملف SQL جديد لإصلاح قاعدة البيانات
2. **`test_database_schema.php`** - سكريبت اختبار للتحقق من الإصلاح
3. **`DATABASE_FIX_GUIDE.md`** - هذا الدليل

## ملاحظات مهمة

- استخدم `IF NOT EXISTS` لتجنب أخطاء التكرار
- تم ترتيب العمليات بشكل صحيح (إضافة الأعمدة أولاً، ثم الفهارس)
- جميع الجداول الجديدة تدعم الميزات المطلوبة في TODO list

## التحقق من النجاح

بعد تطبيق الإصلاح، يجب أن تعمل جميع الميزات التالية:
- ✅ البحث عن الأطباء حسب التخصص والموقع
- ✅ حجز المواعيد مع التذكيرات
- ✅ إدارة إعدادات التذكيرات
- ✅ عرض أوقات الشواغر الفعلية
- ✅ نظام التقييمات والمراجعات 