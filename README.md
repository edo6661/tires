# Tire Storage Reservation System

A comprehensive web application for managing tire storage reservations and services, built with Laravel. This system allows customers to book tire storage services online while providing administrators with powerful tools to manage reservations, customers, and business operations.

## 🚗 About

Tire Pro Service is a professional tire service provider located in Iruma-shi, Saitama, Japan. Our system offers a complete solution for tire storage management, including online booking, reservation management, customer communication, and administrative oversight.

### Key Features

- **Customer Portal**
  - Online tire storage booking
  - Reservation management
  - Service inquiry system
  - Multi-language support (English/Japanese)
  - Real-time availability checking

- **Admin Dashboard**
  - Complete reservation management
  - Customer management
  - Business settings configuration
  - Tire storage inventory tracking
  - Payment processing
  - Announcement system
  - FAQ management

- **API Integration**
  - RESTful API for external integrations
  - Public endpoints for business information
  - Authenticated endpoints for customer operations
  - Admin API for system management

- **Multi-language Support**
  - English and Japanese localization
  - Automatic locale detection
  - Localized content management

## 🛠️ Tech Stack

- **Backend**: Laravel 12.0 (PHP 8.2+)
- **Frontend**: Alpine.js, Tailwind CSS
- **Database**: MySQL/PostgreSQL
- **Build Tool**: Vite
- **Authentication**: Laravel Sanctum
- **File Storage**: AWS S3
- **Email**: Laravel Mail
- **Calendar**: Spatie iCalendar Generator

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL/PostgreSQL database
- AWS S3 (optional, for file storage)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd tires
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Configure your `.env` file with:
   - Database credentials
   - Mail settings
   - AWS S3 credentials (if using)
   - App URL and locale settings

5. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build Assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

7. **Start the Application**
   ```bash
   php artisan serve
   ```

## 📖 Usage

### For Customers

1. **Browse Services**: Visit the homepage to view available tire storage services
2. **Make a Reservation**: Select a service and follow the booking process
3. **Manage Reservations**: Access your dashboard to view and manage bookings
4. **Contact Support**: Use the inquiry form for questions or support

### For Administrators

1. **Access Admin Panel**: Navigate to `/admin` and login with admin credentials
2. **Manage Reservations**: View, edit, and process customer reservations
3. **Configure Business Settings**: Update business hours, contact info, and policies
4. **Manage Content**: Create announcements, FAQs, and menu items

## 🔗 API Documentation

### Public Endpoints
- Business settings and company information
- Service menus and pricing
- Contact form submission

### Authenticated Endpoints
- Customer reservation management
- Tire storage operations
- Profile management

### Admin Endpoints
- Full system management
- User administration
- Content management

Detailed API documentation is available in:
- `PUBLIC_API_ENDPOINTS.md`
- `ADMIN_API_ENDPOINTS.md`
- `RESERVATION_API_USAGE.md`

## 🌐 Localization

The application supports English and Japanese languages. Locale is automatically detected based on browser preferences or can be manually set via URL prefix (`/en/` or `/jp/`).

## 📧 Email Notifications

The system sends automated emails for:
- Booking confirmations
- Reservation updates
- Admin notifications
- Password resets

## 🔒 Security Features

- Laravel Sanctum for API authentication
- CSRF protection
- Input validation and sanitization
- Role-based access control
- Secure password hashing

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support and inquiries:
- **Address**: 2095-8 Miyadera, Iruma-shi, Saitama 358-0014, Japan
- **Phone**: 04-2937-5296
- **Email**: Contact through the inquiry form

## 🙏 Acknowledgments

- Laravel Framework
- Alpine.js
- Tailwind CSS
- Font Awesome
- All contributors and supporters
