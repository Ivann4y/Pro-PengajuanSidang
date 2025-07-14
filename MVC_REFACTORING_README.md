# MVC Refactoring - Sistem Pengajuan Sidang

## Overview

This document outlines the complete refactoring of the thesis defense submission system from a procedural PHP structure to a modern Model-View-Controller (MVC) architecture. The refactoring improves code organization, maintainability, security, and scalability.

## New Architecture Structure

```
Pro-PengajuanSidang/
├── app/
│   ├── controllers/
│   │   ├── Controller.php (Base controller)
│   │   ├── AuthController.php (Authentication)
│   │   ├── MahasiswaController.php (Student operations)
│   │   ├── DosenController.php (Lecturer operations)
│   │   └── AdminController.php (Admin operations)
│   ├── models/
│   │   ├── Model.php (Base model)
│   │   ├── User.php (Abstract user model)
│   │   ├── Mahasiswa.php (Student model)
│   │   ├── Dosen.php (Lecturer model)
│   │   ├── Admin.php (Admin model)
│   │   ├── Sidang.php (Thesis defense model)
│   │   ├── Penilaian.php (Evaluation model)
│   │   ├── Kelompok.php (Group model)
│   │   ├── Notifikasi.php (Notification model)
│   │   ├── Bimbingan.php (Guidance model)
│   │   ├── Penjadwalan.php (Scheduling model)
│   │   ├── DetailSidang.php (Defense detail model)
│   │   └── PasswordReset.php (Password reset model)
│   ├── services/
│   │   ├── AuthService.php (Authentication service)
│   │   └── FileUploadService.php (File handling service)
│   ├── middleware/
│   │   └── SessionMiddleware.php (Session management)
│   └── core/
│       ├── Database.php (Database connection)
│       ├── Model.php (Base model class)
│       ├── Controller.php (Base controller class)
│       ├── View.php (View rendering)
│       ├── Router.php (URL routing)
│       └── Autoloader.php (Class autoloading)
├── config/
│   ├── database.php (Database configuration)
│   └── security.php (Security settings)
├── public/
│   ├── index.php (Front controller)
│   ├── assets/ (CSS, JS, images)
│   └── uploads/ (File uploads)
├── views/
│   ├── layouts/
│   │   └── main.php (Main layout template)
│   ├── auth/
│   │   ├── login.php
│   │   ├── forgot-password.php
│   │   └── reset-password.php
│   ├── mahasiswa/ (Student views)
│   ├── dosen/ (Lecturer views)
│   └── admin/ (Admin views)
├── logs/ (Application logs)
└── security/ (Security helper files)
```

## Key Improvements

### 1. **Separation of Concerns**
- **Models**: Handle data access and business logic
- **Views**: Handle presentation and user interface
- **Controllers**: Handle user input and coordinate between models and views

### 2. **Enhanced Security**
- CSRF protection on all forms
- Input sanitization and validation
- Rate limiting for login attempts
- Secure file upload handling
- Session security with timeout and hijacking protection
- Password strength validation
- SQL injection prevention with prepared statements

### 3. **Code Organization**
- Role-based controller structure
- Centralized configuration
- Service layer for complex operations
- Middleware for cross-cutting concerns
- Consistent naming conventions

### 4. **Maintainability**
- Single responsibility principle
- Dependency injection ready
- Easy to extend and modify
- Clear separation of business logic

## Migration Guide

### Step 1: Database Setup
1. Import the latest SQL schema (`SistemSidang1V4.sql`)
2. Update database configuration in `config/database.php`
3. Ensure all tables exist and have proper relationships

### Step 2: Configuration
1. Update `config/security.php` with your specific settings
2. Set proper email configuration for password reset
3. Configure file upload paths and permissions

### Step 3: File Structure
1. Create the new directory structure as shown above
2. Move existing assets to `public/assets/`
3. Move existing uploads to `public/uploads/`
4. Create necessary directories for logs and temporary files

### Step 4: View Migration
1. Convert existing PHP views to use the new template system
2. Update form actions to use new routing
3. Add CSRF tokens to all forms
4. Update asset paths to use new structure

### Step 5: Testing
1. Test all authentication flows
2. Test file upload functionality
3. Test role-based access control
4. Test all CRUD operations

## Usage Examples

### Creating a New Controller Method

```php
// In MahasiswaController.php
public function newMethod()
{
    // Get data from model
    $data = $this->mahasiswaModel->getSomeData();
    
    // Render view with data
    $this->view->render('mahasiswa/new-view', [
        'data' => $data
    ]);
}
```

### Adding a New Route

```php
// In public/index.php
$router->add('mahasiswa/new-route', [
    'controller' => 'MahasiswaController', 
    'action' => 'newMethod'
]);
```

### Creating a New Model Method

```php
// In Mahasiswa.php
public function getSomeData()
{
    $sql = "SELECT * FROM mahasiswa WHERE status = ?";
    return $this->db->query($sql, ['active'])->fetchAll();
}
```

## Security Features

### 1. **CSRF Protection**
- All forms include CSRF tokens
- Tokens are validated on form submission
- Tokens expire after 1 hour

### 2. **Input Validation**
- Server-side validation for all inputs
- Sanitization of user data
- Type checking and format validation

### 3. **File Upload Security**
- File type validation
- File size limits
- Secure file naming
- Malicious content detection

### 4. **Session Security**
- Session timeout (8 hours)
- IP address validation
- User agent validation
- Secure session configuration

### 5. **Rate Limiting**
- Login attempt limiting
- Password reset request limiting
- Configurable thresholds and lockout periods

## Database Schema

The system uses the following main tables:

- **Mahasiswa**: Student information
- **Dosen**: Lecturer information
- **Admin**: Administrator information
- **Sidang**: Thesis defense records
- **Penilaian**: Evaluation scores
- **Kelompok**: Student groups
- **Notifikasi**: System notifications
- **Penjadwalan**: Defense scheduling
- **password_resets**: Password reset tokens

## API Endpoints

### Authentication
- `POST /login` - User login
- `POST /logout` - User logout
- `POST /forgot-password` - Request password reset
- `POST /reset-password` - Reset password with token

### Student Operations
- `GET /mahasiswa/dashboard` - Student dashboard
- `GET /mahasiswa/pengajuan` - Thesis submission form
- `POST /mahasiswa/submit-pengajuan` - Submit thesis
- `GET /mahasiswa/kelola-pengajuan` - Manage submissions
- `GET /mahasiswa/sidang` - View defense schedule
- `GET /mahasiswa/nilai-akhir` - View final grades

### Lecturer Operations
- `GET /dosen/dashboard` - Lecturer dashboard
- `GET /dosen/daftar-pengajuan` - View submissions
- `POST /dosen/evaluasi-pengajuan/{id}` - Evaluate submission
- `GET /dosen/evaluasi-sidang/{id}` - Evaluate defense
- `POST /dosen/submit-evaluasi/{id}` - Submit evaluation

### Admin Operations
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/penjadwalan` - Manage scheduling
- `POST /admin/create-penjadwalan` - Create schedule
- `GET /admin/kelola-user` - Manage users
- `POST /admin/create-user` - Create new user

## Configuration

### Database Configuration (`config/database.php`)
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistem_sidang');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Security Configuration (`config/security.php`)
```php
define('APP_SECRET', 'your-secret-key');
define('SESSION_LIFETIME', 28800); // 8 hours
define('LOGIN_MAX_ATTEMPTS', 5);
```

## Deployment

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependencies)

### Installation Steps
1. Clone or download the project
2. Configure database settings
3. Import database schema
4. Set proper file permissions
5. Configure web server to point to `public/` directory
6. Update security settings for production

### Production Considerations
- Set `DEBUG_MODE` to `false`
- Use HTTPS and set `SESSION_SECURE` to `true`
- Configure proper email settings
- Set up log rotation
- Use environment variables for sensitive data
- Configure proper file upload limits

## Troubleshooting

### Common Issues

1. **404 Errors**: Check route definitions and controller method names
2. **Database Connection**: Verify database credentials and connection
3. **File Uploads**: Check directory permissions and file size limits
4. **Session Issues**: Verify session configuration and storage
5. **CSRF Errors**: Ensure forms include CSRF tokens

### Debug Mode
Enable debug mode in `config/security.php` to see detailed error messages:
```php
define('DEBUG_MODE', true);
```

## Future Enhancements

1. **API Development**: Create RESTful API endpoints
2. **Real-time Notifications**: Implement WebSocket support
3. **File Versioning**: Add document version control
4. **Advanced Reporting**: Create comprehensive reporting system
5. **Mobile App**: Develop mobile application
6. **Multi-language Support**: Add internationalization
7. **Advanced Security**: Implement two-factor authentication

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review error logs in `logs/` directory
3. Verify configuration settings
4. Test with debug mode enabled

## Version History

- **v1.0**: Initial MVC refactoring
- **v1.1**: Enhanced security features
- **v1.2**: Improved file handling
- **v1.3**: Added comprehensive logging
- **v1.4**: Role-based access control improvements

---

**Note**: This refactoring maintains backward compatibility with existing data while providing a modern, secure, and maintainable codebase for future development. 