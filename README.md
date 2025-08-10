# Advanced Medical Appointment System 🏥

A comprehensive and advanced medical appointment booking system with a modern user interface and advanced features.

## ✨ Main Features

### 🔐 User System

- **Login and Registration**: Secure system with password encryption
- **Profile**: Personal data management
- **Dashboard**: Display user statistics

### 🏥 Hospital and Clinic Management

- **Hospital Display**: Comprehensive list with details and categories
- **Specialized Clinics**: Display clinics by specialty
- **Doctors**: Detailed information about doctors and their schedules
- **Ratings**: Advanced rating system for doctors and hospitals

### 📅 Advanced Appointment System

- **Appointment Booking**: User-friendly interface with date and time selection
- **Rescheduling**: Easy appointment modification
- **Cancellation**: Appointment cancellation with permission verification
- **Available Times**: Dynamic display of available times
- **Availability Check**: Prevention of double booking

### 🔔 Notification System

- **Instant Notifications**: Booking and cancellation confirmations
- **Appointment Reminders**: Notifications before appointments
- **System Messages**: General user notifications
- **Notification Management**: View and update read status

### ⭐ Rating System

- **Doctor Ratings**: 1-5 star rating with comments
- **Rating Updates**: Ability to modify previous ratings
- **Average Ratings**: Calculate average rating for each doctor
- **Rating Display**: Show previous patient ratings

### 🔍 Advanced Search

- **Search by Specialty**: Filter doctors by specialty
- **Search by Location**: Filter by area
- **Search by Rating**: Filter by rating level
- **Search by Experience**: Filter by years of experience

### 📊 Reports and Statistics

- **User Statistics**: Number of appointments and status
- **Appointment Reports**: Detailed reports for managers
- **Doctor Statistics**: Number of appointments and ratings

## 🛠️ Technologies Used

### Backend

- **PHP 8.0+**: Main programming language
- **MySQL**: Database
- **PDO**: For secure database connection
- **Sessions**: For user session management

### Frontend

- **HTML5**: Page structure
- **CSS3**: Design and animation
- **JavaScript**: Dynamic interaction
- **Font Awesome**: Icons
- **Responsive Design**: Adaptive design

### Security

- **Password Hashing**: Password encryption
- **SQL Injection Protection**: Protection from SQL injection
- **XSS Protection**: Protection from XSS attacks
- **Input Validation**: Input verification

## 📁 Project Structure

```
App-Demo/
├── assets/
│   ├── css/
│   │   └── style.css          # Main style file
│   ├── js/
│   │   └── script.js          # JavaScript file
│   └── images/                # Images
├── config/
│   └── database.php           # Database settings
├── includes/
│   ├── functions.php          # Helper functions
│   ├── header.php            # Page header
│   └── footer.php            # Page footer
├── index.php                 # Homepage
├── login.php                 # Login
├── register.php              # Registration
├── dashboard.php             # Dashboard
├── hospitals.php             # Hospitals
├── clinics.php               # Clinics
├── doctors.php               # Doctors
├── doctor-details.php        # Doctor details
├── book.php                  # Appointment booking
├── appointments.php          # Appointment management
├── reschedule.php            # Appointment rescheduling
├── notifications.php         # Notifications
├── review_appointment.php    # Appointment review
├── search.php                # Search
├── profile.php               # Profile
├── about.php                 # About us
├── database.sql              # Database structure
├── fix_database.sql          # Database fixes
└── README.md                 # This file
```

## 🚀 Installation and Setup

### Requirements

- PHP 8.0 or newer
- MySQL 5.7 or newer
- Web server (Apache/Nginx)
- Modern JavaScript-enabled browser

### Installation Steps

1. **Download Project**

   ```bash
   git clone [repository-url]
   cd App-Demo
   ```

2. **Database Setup**

   ```bash
   # Create database
   mysql -u root -p
   CREATE DATABASE medical_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

   # Import data
   mysql -u root -p medical_booking < fix_database.sql
   ```

3. **Configure Database Connection**

   ```php
   // config/database.php
   private $host = 'localhost';
   private $db_name = 'medical_booking';
   private $username = 'root';
   private $password = '';
   ```

4. **Server Setup**

   - Place files in server directory
   - Ensure PHP is enabled
   - Ensure MySQL is enabled

5. **System Testing**
   - Open browser and go to `http://localhost/App-Demo`
   - Try registration and booking

## 📋 New Added Features

### 🔄 Appointment Rescheduling

- Easy interface for selecting new date and time
- Verification of new appointment availability
- Automatic notifications for rescheduling

### 🔔 Advanced Notification System

- Instant notifications for booking and cancellation
- Appointment reminders
- Read status management
- Notification categorization (read/unread)

### ⭐ Comprehensive Rating System

- 1-5 star rating with comments
- Rating update capability
- Doctor average rating calculation
- Display of previous patient ratings

### 🎨 Interface Improvements

- Advanced responsive design
- Animations and visual effects
- Gradient colors and modern icons
- Enhanced user experience

### 🔍 Advanced Search

- Filter by specialty
- Filter by location
- Filter by rating
- Filter by experience

## 🎯 How to Use

### For Patients

1. **Register**: Create new account
2. **Search**: Find doctor or specialty
3. **Book**: Choose suitable appointment
4. **Monitor**: View and manage appointments
5. **Rate**: Rate completed appointments

### For Managers

1. **Reports**: View system statistics
2. **User Management**: Monitor activity
3. **Appointment Management**: Track bookings
4. **Settings**: Configure system

## 🔧 Advanced Settings

### System Settings

```sql
-- System settings table
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('appointment_reminder_hours', '24', 'Hours before appointment to send reminder'),
('max_appointments_per_day', '50', 'Maximum daily appointments per doctor'),
('appointment_duration_minutes', '30', 'Appointment duration in minutes'),
('auto_confirm_appointments', 'true', 'Auto-confirm appointments'),
('enable_notifications', 'true', 'Enable notification system');
```

### Design Customization

```css
/* Color customization */
:root {
  --primary-blue: #2563eb;
  --medical-green: #10b981;
  --warning: #f59e0b;
  --error: #ef4444;
  --success: #10b981;
}
```

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**

   - Verify connection details
   - Ensure MySQL is running

2. **Page Display Error**

   - Ensure PHP is enabled
   - Check file permissions

3. **Booking Issues**
   - Verify login status
   - Check appointment availability

### Error Logs

```php
// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 🔒 Security

### Data Protection

- Password encryption using `password_hash()`
- SQL injection protection using Prepared Statements
- Input sanitization using `clean_input()`
- User permission verification

### Best Practices

- Regular PHP and database updates
- HTTPS usage in production
- Regular data backup
- Error log monitoring

## 📈 Future Development

### Planned Features

- [ ] Mobile application
- [ ] Electronic payment system
- [ ] Video conferencing
- [ ] AI recommendations
- [ ] Medical records system
- [ ] Hospital system integration

### Technical Improvements

- [ ] Performance optimization
- [ ] API addition
- [ ] Security enhancement
- [ ] Automated testing addition

## 🤝 Contributing

Contributions welcome! Please follow these steps:

1. Fork the project
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📄 License

This project is licensed under the MIT License. See `LICENSE` file for details.

## 📞 Support

For support and inquiries:

- 📧 Email: support@medical-booking.com
- 🌐 Website: www.medical-booking.com
- 📱 Phone: +20-123-456-789

---

**Developed by a specialized healthcare applications development team** 🏥✨
