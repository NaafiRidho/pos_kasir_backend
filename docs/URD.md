# POS Kasir Backend — User Requirements Document (URD)

## 1. Overview
- System: POS Kasir Backend API
- Tech: Laravel 10 (PHP ≥ 8.1), MySQL, JWT Auth
- Domain: Retail point-of-sale (kasir), pengelolaan kategori, produk, pengguna, dan transaksi penjualan.

## 2. Goals
- Mendukung proses penjualan retail secara aman dan konsisten (stok berkurang saat item ditambahkan, kembali saat item dihapus).
- Menyediakan API terstandarisasi untuk integrasi dengan front-end POS.
- Menyediakan pengelolaan master data (kategori, produk, pengguna, metode pembayaran).

## 3. Scope
- Authentication (login, logout, refresh, profil/me) via JWT.
- CRUD kategori, produk, dan pengguna.
- Penjualan: buat sale (draft), tambah/hapus item, lihat detail sale, perhitungan otomatis subtotal/discount/total.
- Manajemen stok: decrement saat add_item, increment saat remove_item.

## 4. Actors & Roles
- Admin: kelola pengguna, kategori, produk; akses semua data.
- Kasir: autentikasi, membuat sale, menambah/menghapus item, melihat sale.

## 5. Functional Requirements
1. Authentication
   - FR-1.1: Pengguna dapat login dan menerima JWT.
   - FR-1.2: Pengguna dapat logout dan refresh token.
   - FR-1.3: Endpoint `me` mengembalikan profil pengguna.
2. Category Management
   - FR-2.1: List kategori.
   - FR-2.2: Tambah kategori.
   - FR-2.3: Edit kategori.
   - FR-2.4: Hapus kategori (tergantung relasi produk).
3. Product Management
   - FR-3.1: List produk.
   - FR-3.2: Tambah produk (termasuk harga, stok, barcode, images).
   - FR-3.3: Edit produk.
   - FR-3.4: Hapus produk (dengan validasi keterkaitan).
4. User Management
   - FR-4.1: List pengguna (dengan role).
   - FR-4.2: Tambah pengguna (username unik, uuid, role_id).
   - FR-4.3: Edit pengguna.
   - FR-4.4: Hapus pengguna.
5. Sales
   - FR-5.1: Buat sale draft dengan `user_id` (dari JWT atau payload) dan `payment_status='draft'`.
   - FR-5.2: Tambah item ke sale: validasi stok cukup; kurangi `products.stock` sesuai `quantity` dalam transaksi; simpan snapshot `name_product` dan `subtotal` baris; kalkulasi ulang header (subtotal, discount_amount, total_amount).
   - FR-5.3: Hapus item dari sale: kembalikan `products.stock` sebesar `quantity` dalam transaksi; kalkulasi ulang header.
   - FR-5.4: Lihat sale beserta items, user, dan payment.
   - FR-5.5: Sistem menggunakan kunci pesimis (`lockForUpdate`) saat modifikasi agar konsisten pada akses bersamaan.

## 6. Non-Functional Requirements
- NFR-1: Keamanan: JWT, validasi input, pembatasan akses (middleware) untuk endpoint tertentu.
- NFR-2: Konsistensi Data: gunakan transaksi database dan row-level locking saat update totals dan stok.
- NFR-3: Kinerja: query sederhana dengan indeks pada PK/FK (custom PK seperti `product_id`, `sale_id`).
- NFR-4: Observabilitas: logging error terstandarisasi (Response helper), struktur respons konsisten.
- NFR-5: Maintainability: model dan controller dipisah, response helper konsisten, seeders untuk data awal.

## 7. Constraints & Assumptions
- Laravel 10, PHP ≥ 8.1, MySQL (InnoDB untuk FK dan locking), Tymon JWT Auth.
- Nama PK/FK kustom (mis. `sale_id`, `user_id`) harus konsisten pada model dan migrasi.
- Pajak (`tax_amount`) dan status pembayaran diatur di header sale; perhitungan pajak tidak otomatis.

## 8. API Summary (ringkas)
- Auth: `POST /api/login`, `POST /api/logout`, `POST /api/refresh`, `GET /api/me`.
- Categories: `GET /api/categories`, `POST /api/categories/add_category`, `PUT /api/categories/{id}`, `DELETE /api/categories/{id}`.
- Users: `GET /api/users`, `POST /api/users/add_user`, `PUT /api/users/{id}`, `DELETE /api/users/{id}`.
- Products: `GET/POST/PUT/DELETE` serupa (lihat ProductController).
- Sales: `POST /api/sales` (create draft), `POST /api/sales/items` (add item), `DELETE /api/sales/items/{saleItemId}` (remove item), `GET /api/sales/{saleId}` (get sale).

## 9. Data Model (ringkas)
- roles(role_id, name, ...)
- users(user_id, role_id, uuid, username, ...)
- categories(categories_id, name, ...)
- products(product_id, categories_id, name, selling_price, stock, ...)
- payments(payment_id, payment_method, ...)
- sales(sale_id, user_id, payment_id, subtotal, discount_amount, tax_amount, total_amount, payment_status, sale_date)
- sale_items(sale_item_id, sale_id, product_id, name_product, quantity, discount_amount, subtotal)
