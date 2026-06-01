# Simple Library Management System

Simple Library Management System adalah aplikasi manajemen perpustakaan sederhana berbasis Laravel. Aplikasi ini dibuat untuk membantu proses pengelolaan data buku, kategori buku, copy/eksemplar buku, member, peminjaman buku, dan pengembalian buku.

Aplikasi ini memiliki dua jenis pengguna, yaitu admin dan member. Admin bertugas mengelola data perpustakaan, sedangkan member dapat melihat daftar buku, detail buku, ketersediaan buku, dan riwayat peminjaman. Berikut dengan penetapan 7 hari setelah hari peminjaman sebagai due date buku.

## Fitur Aplikasi

### 1. Register dan Login

Aplikasi menyediakan fitur register, login, dan logout. Password user disimpan menggunakan hashing Laravel sehingga password tidak tersimpan dalam bentuk teks asli di database.

Setelah register berhasil, user akan diarahkan ke halaman login.

### 2. Role Pengguna

Aplikasi memiliki dua role utama:

* Admin
* Member

Admin ditandai menggunakan kolom `is_admin`. Jika `is_admin` bernilai `true`, maka user memiliki akses untuk mengelola data perpustakaan. Jika `false`, maka user dianggap sebagai member biasa.

### 3. Status Member

Member memiliki status akun yang digunakan untuk menentukan apakah member dapat melakukan peminjaman buku.

Status member:

* `active`
* `suspended`
* `inactive`

Member dengan status `active` dapat melakukan peminjaman melalui admin. Member dengan status `suspended` atau `inactive` tidak dapat melakukan peminjaman.

Sistem juga dapat mengubah status member menjadi `suspended` jika member memiliki buku yang belum dikembalikan dan sudah melewati tanggal jatuh tempo.

### 4. Manajemen Kategori Buku

Admin dapat mengelola kategori buku, seperti menambahkan, mengubah, dan menghapus kategori.

Kategori digunakan untuk mengelompokkan buku berdasarkan jenis atau topik tertentu.

### 5. Manajemen Buku

Admin dapat menambahkan dan mengubah data buku. Data buku yang dikelola meliputi:

* Title
* Author
* Publisher
* Year Published
* ISBN
* Image
* Language
* Category
* Description

Data buku utama disimpan sebagai metadata buku.

### 6. Manajemen Copy Buku

Setiap buku dapat memiliki lebih dari satu copy atau eksemplar fisik. Copy buku digunakan untuk membedakan setiap buku fisik yang tersedia di perpustakaan.

Status copy buku:

* `available`
* `borrowed`
* `reserved`
* `lost`
* `damaged`
* `transferred`

Admin dapat menambahkan copy baru dan mengubah status copy buku.

### 7. Detail Buku

Halaman detail buku menampilkan informasi lengkap buku, seperti gambar buku, metadata buku, kategori, total copy, dan jumlah copy yang tersedia.

Guest dapat melihat detail buku. Member yang sudah login dapat melihat jumlah buku yang tersedia. Admin dapat melihat tombol edit dan daftar copy buku.

### 8. Peminjaman Buku

Peminjaman buku hanya dapat dilakukan oleh admin. Member tidak dapat meminjam buku secara langsung dari sistem.

Pada proses peminjaman, admin memilih member, memilih copy buku yang tersedia, lalu mengisi tanggal peminjaman dan tanggal jatuh tempo. Setelah peminjaman berhasil, status copy buku berubah menjadi `borrowed`.

Sistem membatasi jumlah peminjaman aktif. Jika member masih memiliki 3 buku yang belum dikembalikan, maka member tidak dapat meminjam buku lagi.

### 9. Pengembalian Buku

Admin dapat mencatat pengembalian buku. Setelah buku dikembalikan, status borrowed log berubah menjadi `returned` dan status copy buku berubah kembali menjadi `available`.

### 10. Riwayat Peminjaman

Aplikasi menampilkan riwayat peminjaman dari sisi member dan admin.

Member dapat melihat riwayat peminjamannya sendiri. Admin dapat melihat riwayat peminjaman dan pengembalian pada halaman pengelolaan peminjaman.

### 11. Search dan Filter Buku

Pada halaman utama, user dapat mencari buku berdasarkan:

* Title
* Author
* Publisher

User juga dapat memfilter buku berdasarkan kategori.

## Struktur Database Utama

### users

Tabel `users` digunakan untuk menyimpan data user, baik admin maupun member.

Kolom utama:

* id
* name
* email
* password
* phone
* address
* status
* is_admin

### book_categories

Tabel `book_categories` digunakan untuk menyimpan kategori buku.

Kolom utama:

* id
* name

### book_metadata

Tabel `book_metadata` digunakan untuk menyimpan data utama buku.

Kolom utama:

* id
* title
* author
* publisher
* year_published
* isbn
* image
* language
* book_category_id
* description

### book_copies

Tabel `book_copies` digunakan untuk menyimpan data copy atau eksemplar buku.

Kolom utama:

* id
* book_metadata_id
* status

### borrowed_logs

Tabel `borrowed_logs` digunakan untuk menyimpan data peminjaman dan pengembalian buku.

Kolom utama:

* id
* user_id
* book_copy_id
* borrowed_date
* due_date
* returned_date
* status

## Relasi Database

Relasi utama dalam aplikasi:

* Satu kategori memiliki banyak buku.
* Satu buku memiliki banyak copy buku.
* Satu user memiliki banyak borrowed log.
* Satu book copy memiliki banyak borrowed log.
* Satu borrowed log terhubung ke satu user dan satu book copy.

## Aturan Bisnis

Beberapa aturan bisnis dalam aplikasi:

1. Guest dapat melihat halaman utama dan detail buku.
2. Member dapat melihat ketersediaan buku dan riwayat peminjaman.
3. Admin dapat mengelola buku, kategori, member, copy buku, peminjaman, dan pengembalian.
4. Satu member maksimal dapat meminjam 3 buku aktif.
5. Buku yang sedang dipinjam tidak dapat dipinjam oleh member lain.
6. Copy buku yang berhasil dipinjam akan berubah status menjadi `borrowed`.
7. Copy buku yang sudah dikembalikan akan berubah status menjadi `available`.
8. Member yang memiliki buku overdue dapat berubah status menjadi `suspended`.

## Teknologi yang Digunakan

* Laravel
* PHP
* Blade Template
* HTML
* CSS
* MySQL / PostgreSQL
* Laravel Authentication
* Laravel Gate Authorization

## Instalasi Project

Clone repository:

```bash
git clone https://github.com/Bel-UCO/simple-library-management-system.git
```

Masuk ke folder project:

```bash
cd simple-library-management-system
```

Install dependency PHP:

```bash
composer install
```

Copy file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Atur koneksi database pada file `.env`.

Contoh konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_app
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration:

```bash
php artisan migrate
```

Jalankan seeder untuk membuat data awal, termasuk akun admin:

```bash
php artisan db:seed
```

Buat storage link agar file gambar yang disimpan di storage dapat diakses dari folder public:

```bash
php artisan storage:link
```

Jalankan aplikasi:

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

```text
http://127.0.0.1:8000
```

## Akun Admin Seeder

Project ini sudah menyediakan akun admin melalui seeder.

Gunakan akun berikut untuk login sebagai admin:

```text
Email: admin@mail.com
Password: password
```

Admin dapat mengakses fitur pengelolaan data seperti kategori, buku, member, peminjaman, dan pengembalian.

## Menjalankan Test

Untuk menjalankan test Laravel:

```bash
php artisan test
```

## Catatan Penggunaan

Jika gambar buku tidak muncul, pastikan sudah menjalankan perintah berikut:

```bash
php artisan storage:link
```

Jika database masih kosong setelah migration, jalankan:

```bash
php artisan db:seed
```

Jika ingin reset database dan menjalankan migration ulang beserta seeder, gunakan:

```bash
php artisan migrate:fresh --seed
```

Perintah tersebut akan menghapus seluruh tabel dan data lama, lalu membuat ulang struktur database dan data awal.

## Kesimpulan

Simple Library Management System dibuat untuk membantu proses pengelolaan perpustakaan secara sederhana. Aplikasi ini mencakup fitur pengelolaan buku, kategori, member, copy buku, peminjaman, pengembalian, serta riwayat peminjaman. Dengan adanya pembagian role admin dan member, sistem dapat mengatur hak akses pengguna dengan lebih terstruktur.
