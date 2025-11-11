# Laravel Reverb Real-Time Notifications Setup

## Fitur yang Telah Diimplementasikan

Sistem notifikasi real-time menggunakan Laravel Reverb telah berhasil diimplementasikan dengan fitur-fitur berikut:

### 1. **Notifikasi Real-Time untuk:**
- ✅ Admin membuat acara di kalender → Notifikasi ke semua user
- ✅ Admin publikasi iuran → Notifikasi ke semua user (sudah ada sebelumnya)
- ✅ User membuat postingan baru di forum → Notifikasi ke semua user lain
- ✅ Reply komen user yang posting → Notifikasi ke pemilik postingan

### 2. **Fitur Notifikasi:**
- Badge notifikasi di navbar (menampilkan jumlah notifikasi baru)
- Toast notification pop-up saat notifikasi baru masuk
- Browser notification (jika user memberikan permission)
- Update real-time di halaman notifikasi
- Auto-delete notifikasi lama (hanya menyimpan 5 terbaru)

## Cara Menjalankan

### 1. Update Environment Variables

Pastikan file `.env` sudah dikonfigurasi dengan benar:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=warnet_iae
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 2. Install Dependencies

Jika belum terinstall, jalankan:

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Build Frontend Assets

```bash
npm run build
# atau untuk development
npm run dev
```

### 4. Jalankan Queue Worker

Buka terminal baru dan jalankan:

```bash
php artisan queue:work
```

### 5. Jalankan Reverb Server

Buka terminal baru dan jalankan:

```bash
php artisan reverb:start
```

### 6. Jalankan Development Server

```bash
php artisan serve
```

## Testing Notifikasi Real-Time

### Test 1: Notifikasi Acara Kalender
1. Login sebagai **admin**
2. Buka halaman Kalender
3. Buat acara baru
4. Buka tab browser baru dan login sebagai **user**
5. User akan menerima notifikasi real-time tentang acara baru

### Test 2: Notifikasi Iuran
1. Login sebagai **admin**
2. Buka halaman Kelola Iuran
3. Tambahkan iuran baru
4. Semua user akan menerima notifikasi real-time

### Test 3: Notifikasi Postingan Forum
1. Login sebagai **user**
2. Buka halaman Forum
3. Buat postingan baru
4. Semua user lain (termasuk admin) akan menerima notifikasi real-time

### Test 4: Notifikasi Komentar
1. Login sebagai **user A** dan buat postingan
2. Login sebagai **user B** atau **admin** di tab lain
3. Reply ke postingan user A
4. User A akan menerima notifikasi real-time

## Struktur File yang Ditambahkan/Dimodifikasi

### File Baru:
- `app/Events/NotificationSent.php` - Event untuk broadcasting notifikasi
- `routes/channels.php` - Konfigurasi channel broadcasting
- `REVERB_SETUP.md` - Dokumentasi ini

### File yang Dimodifikasi:
- `.env` - Konfigurasi Reverb
- `app/Models/notification.php` - Menambahkan event broadcasting
- `app/Http/Controllers/CalendarController.php` - Notifikasi acara kalender
- `app/Http/Controllers/ForumController.php` - Notifikasi forum dan komentar
- `resources/js/bootstrap.js` - Konfigurasi Laravel Echo
- `resources/views/layouts/app.blade.php` - Notifikasi badge dan listener
- `resources/views/layouts/admin-layout.blade.php` - Notifikasi badge dan listener
- `resources/views/notifikasi.blade.php` - Real-time update notifikasi

## Troubleshooting

### Notifikasi tidak muncul real-time:
1. Pastikan Reverb server berjalan (`php artisan reverb:start`)
2. Pastikan queue worker berjalan (`php artisan queue:work`)
3. Pastikan Vite dev server berjalan (`npm run dev`)
4. Cek console browser untuk error
5. Pastikan user sudah login

### Browser notification tidak muncul:
1. Klik icon gembok di address bar
2. Izinkan notifikasi untuk website ini
3. Refresh halaman

### WebSocket connection failed:
1. Pastikan port 8080 tidak digunakan aplikasi lain
2. Cek firewall settings
3. Pastikan REVERB_HOST dan REVERB_PORT sesuai di `.env`

## Catatan Penting

1. **Production Deployment**: Untuk production, ubah `REVERB_SCHEME=https` dan sesuaikan host/port
2. **Queue Driver**: Pastikan `QUEUE_CONNECTION=database` di `.env`
3. **Broadcasting**: Pastikan `BROADCAST_CONNECTION=reverb` di `.env`
4. **Notification Limit**: Sistem hanya menyimpan 5 notifikasi terbaru per user

## Cara Kerja Sistem

1. Ketika event terjadi (misal: admin buat acara), controller membuat record di tabel `notifications`
2. Model `Notification` otomatis trigger event `NotificationSent` saat record dibuat
3. Event di-broadcast ke channel private user (`notifications.{userId}`)
4. Frontend (Laravel Echo) mendengarkan channel tersebut
5. Saat menerima event, frontend:
   - Update badge notifikasi
   - Tampilkan toast notification
   - Tampilkan browser notification (jika diizinkan)
   - Update halaman notifikasi (jika sedang dibuka)

## Support

Jika ada pertanyaan atau masalah, silakan hubungi developer team.
