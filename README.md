# 📒 Dokumentasi API Aplikasi Keuangan

Selamat datang di dokumentasi resmi API Aplikasi Keuangan!
API ini dibuat menggunakan PHP Native dengan struktur sederhana dan aman menggunakan **JWT** untuk otorisasi.

---

## 🌐 Base URL

```
http://backendaplikasikeuangan.test
```

---

## 🔑 Otentikasi & Otorisasi

API ini menggunakan **JSON Web Token (JWT)** untuk mengamankan endpoint.

1. **Dapatkan token** dengan melakukan **POST** ke endpoint `/auth/login`.
2. Untuk setiap _request_ ke endpoint yang memerlukan otorisasi, **sertakan token** di dalam header:
   - **Key:** `Authorization`
   - **Value:** `Bearer {token_yang_didapat_saat_login}`

---

## 👤 Endpoint User

Endpoint ini digunakan untuk semua operasi yang berkaitan dengan data user, seperti registrasi dan manajemen user oleh Admin.

### 1. Registrasi User Baru

- **URL:** `/users`
- **Method:** `POST`
- **Otorisasi:** Publik _(tidak memerlukan token)_
- **Body:**
  ```json
  {
    "nama_user": "Nama Lengkap User",
    "level": "Karyawan",
    "username": "usernamebaru",
    "password": "passwordrahasia"
  }
  ```
- **Catatan:**
  Value `level` harus diisi dengan `Admin` atau `Karyawan`.

---

## 🔐 Endpoint Auth

Khusus untuk proses login.

### 1. Login User

- **URL:** `/auth`
- **Method:** `POST`
- **Otorisasi:** Publik
- **Body:**
  ```json
  {
    "username": "usernameyangterdaftar",
    "password": "passwordnya"
  }
  ```
- **Respon Sukses:**
  ```json
  {
    "status": "success",
    "message": "Login berhasil.",
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id_user": 1,
      "nama_user": "Nama User",
      "level": "Admin",
      "username": "admin"
    }
  }
  ```

---

## 💼 Endpoint Manajemen User (Admin Only)

Semua endpoint di bawah ini **memerlukan token Admin**.

### 1. Melihat Semua User

- **URL:** `/users`
- **Method:** `GET`
- **Otorisasi:** Token Admin

### 2. Mengubah Data User

- **URL:** `/users/update/{id_user}`
- **Method:** `POST`
- **Otorisasi:** Token Admin
- **Body:**
  ```json
  {
    "nama_user": "Nama Baru",
    "level": "Karyawan",
    "username": "usernamebaru"
  }
  ```

### 3. Menghapus User

- **URL:** `/users/delete/{id_user}`
- **Method:** `POST`
- **Otorisasi:** Token Admin

---

## 🔄 Endpoint Umum User (Butuh Login)

### 1. Mengubah Password

- **URL:** `/users/update_password/{id_user}`
- **Method:** `POST`
- **Otorisasi:** Token_(Admin bisa mengubah password siapa saja, Karyawan hanya bisa mengubah password sendiri)_
- **Body:**
  ```json
  {
    "new_password": "passwordbaru"
  }
  ```

---

## 💸 Endpoint Transaksi (`/transactions`)

Endpoint ini digunakan untuk mengelola data transaksi.

### 1. Input Transaksi Baru (Admin & Karyawan)

- **URL:** `/transactions`
- **Method:** `POST`
- **Otorisasi:** Token (Admin & Karyawan)
- **Body:**
  ```json
  {
    "tgl_transaksi": "2025-08-10",
    "nilai_transaksi": 75000,
    "ket_transaksi": "Makan siang tim",
    "status": "pg"
  }
  ```
- **Catatan:**
  Value `status` diisi dengan `pm` (pemasukan) atau `pg` (pengeluaran).

### 2. Melihat Semua Transaksi (Admin Only)

- **URL:** `/transactions`
- **Method:** `GET`
- **Otorisasi:** Token Admin

### 3. Monitoring (Dashboard) (Admin Only)

- **URL:** `/transactions/dashboard`
- **Method:** `GET`
- **Otorisasi:** Token Admin

### 4. Cetak Laporan (Admin Only)

- **URL:** `/transactions/report`
- **Method:** `GET`
- **Otorisasi:** Token Admin
- **Parameter (Opsional):**
  - `?start=YYYY-MM-DD`
  - `?end=YYYY-MM-DD`
- **Contoh:**
  `/transactions/report?start=2025-07-01&end=2025-07-31`

---

## 📌 Catatan

- Gantilah `{id_user}` di URL dengan _id_ user terkait.
- Gunakan header Authorization di setiap endpoint yang membutuhkan otorisasi:
  ```
  Authorization: Bearer {token}
  ```
- Admin dapat melakukan seluruh operasi manajemen user, Karyawan hanya dapat mengubah password sendiri.

---
