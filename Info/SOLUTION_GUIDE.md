# 🔧 دليل حل مشاكل قاعدة البيانات

# Database Issues Solution Guide

## 📋 المشاكل المحددة

1. **عمود 'type' مفقود** في جدول المستشفيات
2. **عمود 'email' مفقود** في جدول العيادات
3. **دالة get_logged_in_user** غير متوقعة
4. **لا توجد بيانات** في الجداول

## 🚀 الحل الشامل

### الخطوة الأولى: إصلاح قاعدة البيانات

#### الطريقة الأولى: استخدام ملف الإصلاح الشامل (مُوصى به)

```bash
# 1. تأكد من تشغيل خادم MySQL
# 2. نفذ ملف الإصلاح الشامل
mysql -u root -p < fix_database.sql
```

#### الطريقة الثانية: الإصلاح اليدوي

```sql
-- 1. إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS medical_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medical_booking;

-- 2. إضافة الأعمدة المفقودة لجدول المستشفيات
ALTER TABLE hospitals
ADD COLUMN type ENUM('حكومي', 'خاص') DEFAULT 'حكومي' AFTER image,
ADD COLUMN is_24h BOOLEAN DEFAULT FALSE AFTER rating;

-- 3. إضافة الأعمدة المفقودة لجدول العيادات
ALTER TABLE clinics
ADD COLUMN email VARCHAR(100) AFTER phone,
ADD COLUMN consultation_fee DECIMAL(10,2) DEFAULT 0.00 AFTER image;

-- 4. إدراج البيانات
-- (استخدم محتوى fake_data.sql)
```

### الخطوة الثانية: اختبار الإصلاح

```bash
# افتح المتصفح واذهب إلى:
http://localhost/App-Demo/test_database_fix.php
```

### الخطوة الثالثة: التحقق من النتائج

يجب أن تظهر النتائج التالية:

- ✅ نجح الاتصال بقاعدة البيانات
- ✅ جميع الجداول موجودة
- ✅ جميع الأعمدة المطلوبة موجودة
- ✅ دالة get_logged_in_user موجودة
- ✅ البيانات موجودة في جميع الجداول

## 📊 البيانات المتوقعة

بعد الإصلاح، يجب أن تحتوي قاعدة البيانات على:

| الجدول            | عدد السجلات |
| ----------------- | ----------- |
| التخصصات الطبية   | 20          |
| المستشفيات        | 15          |
| العيادات          | 45+         |
| الأطباء           | 30+         |
| أوقات عمل الأطباء | 150+        |

## 🔍 اختبار الوظائف

### اختبار دالة get_logged_in_user

```php
// تأكد من وجود الدالة
if (function_exists('get_logged_in_user')) {
    echo "✅ الدالة موجودة";
} else {
    echo "❌ الدالة مفقودة";
}
```

### اختبار الأعمدة الجديدة

```sql
-- اختبار عمود type في جدول المستشفيات
DESCRIBE hospitals;

-- اختبار عمود email في جدول العيادات
DESCRIBE clinics;
```

## 🛠️ حل المشاكل الشائعة

### مشكلة 1: فشل الاتصال بقاعدة البيانات

```bash
# تأكد من تشغيل MySQL
sudo service mysql start  # Linux
# أو
net start mysql           # Windows
```

### مشكلة 2: خطأ في الصلاحيات

```sql
-- إنشاء مستخدم جديد مع صلاحيات كاملة
CREATE USER 'medical_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON medical_booking.* TO 'medical_user'@'localhost';
FLUSH PRIVILEGES;
```

### مشكلة 3: خطأ في الترميز

```sql
-- تأكد من استخدام UTF-8
ALTER DATABASE medical_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 📁 الملفات المطلوبة

1. **fix_database.sql** - ملف الإصلاح الشامل
2. **test_database_fix.php** - ملف اختبار الإصلاح
3. **config/database.php** - إعدادات الاتصال
4. **includes/functions.php** - الوظائف الأساسية

## 🔗 روابط الاختبار

بعد الإصلاح، اختبر الروابط التالية:

- **الصفحة الرئيسية:** `http://localhost/App-Demo/index.php`
- **صفحة المستشفيات:** `http://localhost/App-Demo/hospitals.php`
- **صفحة التسجيل:** `http://localhost/App-Demo/register.php`
- **صفحة تسجيل الدخول:** `http://localhost/App-Demo/login.php`
- **اختبار شامل:** `http://localhost/App-Demo/test_all_features.php`

## ⚠️ ملاحظات مهمة

1. **النسخ الاحتياطي:** احتفظ بنسخة احتياطية من قاعدة البيانات قبل الإصلاح
2. **البيانات الموجودة:** سيتم حذف البيانات الموجودة عند استخدام `fix_database.sql`
3. **الصلاحيات:** تأكد من وجود صلاحيات كافية لقاعدة البيانات
4. **الترميز:** استخدم UTF-8 لضمان عرض النصوص العربية بشكل صحيح

## 🎯 النتيجة المتوقعة

بعد تطبيق الحل، يجب أن يعمل النظام بشكل كامل مع:

- ✅ عرض المستشفيات مع نوعها (حكومي/خاص)
- ✅ عرض العيادات مع رسوم الاستشارة
- ✅ تسجيل الدخول والخروج
- ✅ حجز المواعيد
- ✅ عرض أوقات عمل الأطباء

## 📞 الدعم

إذا استمرت المشاكل:

1. راجع رسائل الخطأ في `test_database_fix.php`
2. تأكد من إعدادات `config/database.php`
3. تحقق من تشغيل خادم MySQL
4. راجع ملف `ERROR_HANDLING_GUIDE.md`
