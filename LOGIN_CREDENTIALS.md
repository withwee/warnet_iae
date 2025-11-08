# Login Credentials

## Admin Account
- **Email**: admin@example.com
- **Password**: admin123
- **Role**: admin

## Test User Accounts

### User 1 (Test User)
- **NIK**: (check database)
- **Email**: test@example.com
- **Password**: password

### User 2 (Cipengs)
- **NIK**: 1234567890123456
- **Email**: cipengs@example.com
- **Password**: password

## Login Instructions

### For Admin:
1. Go to `/login`
2. Enter email: `admin@example.com`
3. Enter password: `admin123`
4. Click "Log in"
5. **Will redirect to**: `/admin/dashboard` (Admin Dashboard)

### For User/Warga:
1. Go to `/login`
2. Enter NIK (16 digits) or Email
3. Enter password
4. Click "Log in"
5. **Will redirect to**: `/dashboard` (User Dashboard)

### For New User Registration:
1. Go to `/register`
2. Fill in all required fields:
   - Nama Lengkap
   - NIK (16 digits)
   - Nomor KK (16 digits)
   - Nomor Telepon
   - Email
   - Jumlah Laki-laki
   - Jumlah Perempuan
   - Password
   - Konfirmasi Password
3. Click "Daftar"

## Features Implemented

✅ Laravel Breeze authentication
✅ Login with NIK or Email
✅ Separate authentication for Admin and User
✅ Complete registration form with all required fields
✅ Password hashing
✅ Session management
✅ Middleware protection for admin routes
✅ Database seeding with test data
