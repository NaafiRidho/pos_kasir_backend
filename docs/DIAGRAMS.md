# PlantUML Diagrams - Sistem POS Kasir

Dokumentasi lengkap diagram arsitektur sistem POS Kasir menggunakan PlantUML.

## 📋 Daftar Diagram

### 1. **Context Diagram** (`context-diagram.puml`)
Menunjukkan sistem secara keseluruhan dan interaksi dengan aktor eksternal.

**Komponen:**
- **Actors:** Admin/Owner, Kasir
- **Systems:** Web Application, Mobile Application, REST API
- **External Systems:** JWT Authentication, DomPDF, File Storage, MySQL Database

**Highlights:**
- Admin menggunakan web app untuk manajemen data dan laporan
- Kasir menggunakan mobile app untuk transaksi penjualan
- Kedua aplikasi berkomunikasi dengan API backend
- Autentikasi menggunakan JWT (cookie untuk web, bearer untuk mobile)

---

### 2. **Container Diagram** (`container-diagram.puml`)
Menunjukkan containers/komponen utama dalam sistem dan komunikasi antar containers.

**Containers:**
- **Web UI:** Laravel Blade + Alpine.js + Tailwind CSS
- **Mobile UI:** Flutter/React Native (API consumer)
- **Web Controllers:** Handle web routes untuk admin
- **API Controllers:** Handle REST API endpoints untuk mobile
- **Middleware Layer:** Security & validation (JWT, CORS, Role-based)
- **Business Logic:** Models & Services
- **Database:** MySQL 8.0
- **PDF Service:** DomPDF for report generation
- **Storage:** File system untuk images & PDFs

**Flow:**
1. Admin → Web UI → Web Controllers → Business Logic → Database
2. Kasir → Mobile UI → API Controllers → Business Logic → Database
3. Report generation: Web Controllers → PDF Service → Storage

---

### 3. **Component Diagram** (`component-diagram.puml`)
Detail internal struktur backend API dengan semua components.

**Layer Structure:**

#### Web Layer
- `web.php` - Web routing
- `api.php` - API routing

#### Middleware Layer
- JWT Cookie Middleware (extract dari cookie)
- JWT Verify Middleware (verify token)
- Role Check Middleware (Admin authorization)
- CORS Middleware

#### Controllers
- `AuthController` - Login, logout, refresh token
- `DashboardController` - Dashboard & category page
- `ProductController` - CRUD products
- `CategoryController` - CRUD categories
- `UsersController` - CRUD users
- `SaleController` - Sales transactions
- `SalesReportController` - Reports & PDF export

#### Models (Eloquent ORM)
- `User` - Authentication, relations: Role
- `Role` - User roles
- `Product` - Relations: Category
- `Category` - Product categorization
- `Sale` - Relations: SaleItem, Payment, User
- `SaleItem` - Relations: Sale, Product
- `Payment` - Relations: Sale

#### Views (Blade Templates)
```
views/
├── auth/              - login.blade.php
├── pages/             - dashboard.blade.php
├── categories/        - index.blade.php
├── products/          - index.blade.php
├── users/             - index.blade.php
├── reports/           - sales.blade.php, sales-report-pdf.blade.php
├── layouts/           - app.blade.php (main layout)
└── components/        - modals/, pagination/
```

#### External Services
- JWT Service (Tymon\JWTAuth)
- PDF Service (DomPDF)

---

### 4. **Deployment Diagram** (`deployment-diagram.puml`)
Menunjukkan deployment architecture dan runtime environment.

**Nodes:**

#### Client Side
- **Admin Workstation:** Web browser (Chrome/Firefox/Safari)
- **Kasir Device:** Mobile app (Android/iOS)

#### Server Side
- **Web Server:** Laragon/XAMPP (Development)
  - **PHP Runtime 8.1+:** Laravel application + dependencies
  - **Public Directory:** Static assets (CSS/JS via CDN, images)
- **Database Server:** MySQL 8.0
- **Storage Server:** Local file system (storage/)

**Technologies:**
- PHP 8.1+ with extensions (OpenSSL, PDO, Mbstring, etc.)
- Composer 2.x for dependency management
- MySQL 8.0 with InnoDB engine
- Laravel 10 framework

---

### 5. **Sequence Diagram - Authentication** (`sequence-auth.puml`)
Menunjukkan flow autentikasi lengkap (login, access protected route, logout).

**Scenarios:**

#### Login Process (Web)
1. Admin membuka `/login`
2. System render login form
3. Admin submit credentials
4. System verify dengan JWT
5. Generate token & store in cookie + session
6. Redirect to dashboard

#### Accessing Protected Route
1. Admin navigate ke protected route
2. JWT middleware extract token from cookie
3. Validate token & check user role
4. If valid & authorized → render page
5. If invalid → redirect to login
6. If unauthorized → 403 Forbidden

#### Logout Process
1. Admin click logout
2. Invalidate JWT token (blacklist)
3. Clear session data
4. Clear JWT cookie
5. Redirect to login

---

### 6. **Sequence Diagram - Sales Transaction** (`sequence-sales.puml`)
Menunjukkan flow transaksi penjualan dari mobile app.

**Scenarios:**

#### Login (Mobile)
1. Kasir login via mobile app
2. Get JWT token
3. Store token locally

#### Create New Sale
1. Start new transaction
2. Create draft sale in database
3. Return sale_id to app

#### Add Product to Cart
1. Scan/select product
2. Validate stock availability
3. Create sale_item
4. Decrease product stock
5. Update sale total_amount
6. Return updated cart

#### Remove Item
1. Select item to remove
2. Delete sale_item
3. Restore product stock
4. Update sale total_amount

#### Complete Payment
1. Enter payment amount & method
2. Validate payment (amount >= total)
3. Create payment record
4. Update sale status to 'completed'
5. Generate receipt
6. Show receipt (with print option)

#### View Sale Details
1. Request sale data
2. Join tables (sales, sale_items, products, payments)
3. Return complete transaction data

---

### 7. **Activity Diagram - Product Management** (`activity-product.puml`)
Menunjukkan business process untuk manajemen produk.

**Actions:**
- **Add Product:** Validate → Upload image → Save to DB
- **Edit Product:** Load data → Modify → Validate → Update (handle image replacement)
- **Delete Product:** Check sales history → Confirm → Delete image & data
- **Search/Filter:** Query with keyword/category → Display results
- **View Details:** Show product info modal

**Validation Rules:**
- Name, SKU, category, price, stock required
- Stock must be non-negative
- Price must be positive
- Image optional (jpg/png, max size)

---

### 8. **Activity Diagram - Sales Report** (`activity-report.puml`)
Menunjukkan business process untuk laporan penjualan.

**Actions:**

#### View Report
1. Load current month data (default)
2. Calculate statistics & comparison
3. Load daily sales, top products, categories
4. Render with Chart.js

#### Change Month
1. Select from dropdown
2. Recalculate for new period
3. Reload page

#### Print Report
1. Apply print CSS
2. Trigger browser print
3. Print with preserved styling

#### Export PDF
1. Show month selector modal
2. Select period
3. Generate PDF with DomPDF:
   - Professional template
   - Statistics cards
   - Top products table
   - Category breakdown
   - Daily sales summary
4. Download file

#### Analyze Data
- Statistics with percentage changes
- Top products ranking (ALL products, scrollable)
- Category breakdown with progress bars
- Interactive daily chart

---

## 🎨 Viewing Diagrams

### Online Viewers
1. **PlantUML Web Server:** http://www.plantuml.com/plantuml/uml/
2. **PlantText:** https://www.planttext.com/

### VS Code Extensions
- **PlantUML** by jebbs
- **Markdown Preview Enhanced**

### Command Line
```bash
# Install PlantUML
npm install -g node-plantuml

# Generate PNG
puml generate context-diagram.puml -o output.png

# Generate SVG
puml generate context-diagram.puml -t svg -o output.svg
```

### Docker
```bash
docker run -d -p 8080:8080 plantuml/plantuml-server:jetty

# Access at http://localhost:8080
```

---

## 📁 File Structure

```
docs/
├── README.md                      # This file
├── context-diagram.puml           # C4 Context diagram
├── container-diagram.puml         # C4 Container diagram
├── component-diagram.puml         # C4 Component diagram
├── deployment-diagram.puml        # C4 Deployment diagram
├── sequence-auth.puml             # Authentication flow
├── sequence-sales.puml            # Sales transaction flow
├── activity-product.puml          # Product management process
├── activity-report.puml           # Report generation process
├── use-case-diagram.puml          # Use case (existing)
├── erd.puml                       # Entity Relationship (existing)
└── dfd.puml                       # Data Flow (existing)
```

---

## 🔗 C4 Model Reference

Diagrams menggunakan **C4 Model** (Context, Containers, Components, Code):

- **Level 1 - Context:** Big picture, system boundaries
- **Level 2 - Container:** High-level technology choices
- **Level 3 - Component:** Internal structure
- **Level 4 - Code:** Class diagrams (not included)

Reference: https://c4model.com/

---

## 📝 Maintenance Notes

### When to Update Diagrams

1. **Context Diagram:** Ketika ada external system baru atau perubahan actors
2. **Container Diagram:** Ketika menambah/menghapus major components
3. **Component Diagram:** Ketika ada controller/model/view baru
4. **Deployment Diagram:** Ketika infrastructure/environment berubah
5. **Sequence Diagrams:** Ketika business flow berubah
6. **Activity Diagrams:** Ketika process/workflow berubah

### Best Practices

- ✅ Keep diagrams simple and focused
- ✅ Use consistent naming with codebase
- ✅ Add notes for complex logic
- ✅ Update diagrams when code changes
- ✅ Export to PNG/SVG for documentation
- ✅ Version control diagram source files

---

Last Updated: November 25, 2025
Created by: GitHub Copilot
