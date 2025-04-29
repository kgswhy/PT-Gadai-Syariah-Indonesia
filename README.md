# 📘 API Specification

Dokumentasi ini berisi spesifikasi endpoint untuk layanan API Anda. Endpoint dibagi ke dalam rute publik, rute yang membutuhkan autentikasi JWT, serta rute khusus untuk Bank dan data Wilayah.

---

## 🔒 Authentication

- **Middleware**: `auth.jwt`
- Digunakan untuk melindungi endpoint yang membutuhkan user yang sudah login dan memiliki token JWT.

---

## 🔓 Public Routes

### POST `/register`

- **Deskripsi**: Mendaftarkan user baru.
- **Auth**: ❌ Public

---

### POST `/login`

- **Deskripsi**: Login dan mendapatkan JWT token.
- **Auth**: ❌ Public
- **Named Route**: `login`

---

## 🔐 Protected Routes (JWT)

> Semua endpoint di bawah memerlukan autentikasi menggunakan token JWT.

### POST `/logout`

- **Deskripsi**: Logout user dan mencabut token.
- **Auth**: ✅ JWT

---

### PUT `/update-profile`

- **Deskripsi**: Memperbarui data profil user.
- **Auth**: ✅ JWT

---

### GET `/get-profile`

- **Deskripsi**: Mengambil data profil user.
- **Auth**: ✅ JWT

---

## 🏦 Bank API Routes

### POST `/bank/inquiry`

- **Deskripsi**: Melakukan proses inquiry bank.
- **Auth**: ❌ Public *(Pastikan apakah ini memang terbuka atau harus dilindungi)*

---

## 🗺️ Wilayah (Region) Routes

> Semua endpoint di bawah berada dalam prefix `/wilayah`

### POST `/wilayah/provinsi`

- **Deskripsi**: Mengambil daftar provinsi.
- **Auth**: ❌ Public

---

### POST `/wilayah/kabupaten`

- **Deskripsi**: Mengambil daftar kabupaten berdasarkan provinsi.
- **Auth**: ❌ Public

---

### POST `/wilayah/kecamatan`

- **Deskripsi**: Mengambil daftar kecamatan berdasarkan kabupaten.
- **Auth**: ❌ Public

---

### POST `/wilayah/kelurahan`

- **Deskripsi**: Mengambil daftar kelurahan berdasarkan kecamatan.
- **Auth**: ❌ Public

---

### POST `/wilayah/kabupaten/all`

- **Deskripsi**: Mengambil semua data kabupaten tanpa filter.
- **Auth**: ❌ Public

---

## ✅ Catatan Tambahan

- Semua rute POST di atas mengharapkan **Content-Type: application/json**
- Format **request body** dan **response** akan ditentukan melalui uji coba Postman

---

