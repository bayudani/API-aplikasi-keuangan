
# Dokumentasi API Manajemen Keuangan

API ini digunakan untuk autentikasi, manajemen user, dan pengelolaan transaksi pemasukan serta pengeluaran keuangan.

---

## 🔑 Autentikasi

### Login User

- **Endpoint:** `POST /api/Auth.php`
- **Hak Akses:** Publik

#### Request Body (JSON)

```json
{
    "username": "admin",
    "password": "adminpassword"
}
```

#### Success Response (200 OK)

```json
{
    "status": "success",
    "message": "Login berhasil.",
    "token": "eyJhbGciOiJI...",
    "user": {
        "id_user": "1",
        "nama_user": "Admin",
        "level": "Admin"
    }
}
```

---

## 👤 Manajemen User (`users.php`)

Endpoint untuk mengelola data user.

### 1. Registrasi User Baru

- **Endpoint:** `POST /api/users.php`
- **Hak Akses:** Publik

#### Request Body (JSON)

```json
{
    "nama_user": "Budi Karyawan",
    "level": "Karyawan",
    "username": "budi",
    "password": "password123"
}
```

#### Success Response (201 Created)

```json
{
    "status": "success",
    "message": "Registrasi berhasil. Silakan login."
}
```

### 2. Melihat Semua User

- **Endpoint:** `GET /api/users.php`
- **Hak Akses:** Admin

#### Request Body

Tidak ada.

### 3. Update Info User

- **Endpoint:** `POST /api/users.php/update/{id}`
- **Contoh URL:** `/api/users.php/update/2`
- **Hak Akses:** Admin

#### Request Body (JSON)

```json
{
    "nama_user": "Budi Hartono",
    "level": "Karyawan",
    "username": "budihartono"
}
```

### 4. Update Password

- **Endpoint:** `POST /api/users.php/update_password/{id}`
- **Contoh URL:** `/api/users.php/update_password/2`
- **Hak Akses:** Pemilik Akun atau Admin

#### Request Body (JSON)

```json
{
    "new_password": "passwordBaruYangAman"
}
```

### 5. Hapus User

- **Endpoint:** `POST /api/users.php/delete/{id}`
- **Contoh URL:** `/api/users.php/delete/3`
- **Hak Akses:** Admin

#### Request Body

Tidak ada.

---

## 💰 Manajemen Transaksi (`transactions.php`)

Endpoint terkait pemasukan dan pengeluaran keuangan.

### 1. Membuat Transaksi Baru

- **Endpoint:** `POST /api/transactions.php`
- **Hak Akses:** Admin & Karyawan

#### Request Body (JSON)

```json
{
    "tgl_transaksi": "2025-08-11",
    "nilai_transaksi": 50000,
    "ket_transaksi": "Beli ATK Kantor",
    "status": "pg"
}
```

**Catatan:**
`status` bisa diisi `'pg'` (pengeluaran) atau `'pm'` (pemasukan).

---

### 2. Melihat Transaksi

- **Endpoint:** `GET /api/transactions.php`
- **Hak Akses:**
  - Admin (melihat semua transaksi)
  - Karyawan (melihat miliknya sendiri)
- **Request Body:** Tidak ada

---

### 3. Update Transaksi

- **Endpoint:** `PUT /api/transactions.php/{id}`
- **Contoh URL:** `/api/transactions.php/5`
- **Hak Akses:** Admin

#### Request Body (JSON)

**Kirim hanya field yang ingin diubah**

```json
{
    "nilai_transaksi": 55000,
    "ket_transaksi": "Beli ATK Kantor dan Kopi"
}
```

---

### 4. Hapus Transaksi

- **Endpoint:** `DELETE /api/transactions.php/{id}`
- **Contoh URL:** `/api/transactions.php/5`
- **Hak Akses:** Admin
- **Request Body:** Tidak ada

---

### 5. Data Dashboard

- **Endpoint:** `GET /api/transactions.php/dashboard`
- **Hak Akses:** Admin
- **Request Body:** Tidak ada

#### Success Response (200 OK)

```json
{
    "status": "success",
    "data": {
        "pemasukan_bulan_ini": 5000000.0,
        "pengeluaran_bulan_ini": 1500000.0,
        "saldo_bulan_ini": 3500000.0
    }
}
```

---

### 6. Laporan Transaksi (Filter Tanggal)

- **Endpoint:** `GET /api/transactions.php/report`
- **Hak Akses:** Admin

#### Query Params (Opsional)

- `start`: Tanggal mulai (format: `YYYY-MM-DD`). Default: awal bulan ini.
- `end`: Tanggal akhir (format: `YYYY-MM-DD`). Default: akhir bulan ini.

**Contoh URL:**
`/api/transactions.php/report?start=2025-07-01&end=2025-07-31`

---

## Catatan Umum

- Semua endpoint menerima dan mengembalikan data dalam format JSON.
- Pastikan untuk menyertakan Bearer Token pada header Authorization untuk endpoint yang membutuhkan autentikasi.
- Hak akses harus diperhatikan dan disesuaikan dengan role user.
