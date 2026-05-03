# 📘 Restaurant Manager API Documentation

## 🧭 Overview
This backend is a Symfony-based restaurant management system with:
* User authentication (login/register)
* Role-based access control
* Order management
* Invoice generation (including PDF export)
* Product catalog (with soft delete)
* Automatic invoice calculation from orders

---

## 🔐 Authentication

### Login
`GET /api/login`

**Response:**
* Login page (Twig form)
* Returns authentication error if login fails

### Logout
`GET /api/logout`

* Handled automatically by Symfony firewall.

### Register
`POST /api/register`

**Behavior:**
* Creates a new user
* Password is hashed
* Assigns roles based on form input

**Roles:**
| Condition | Roles assigned |
| :--- | :--- |
| Worker selected | `ROLE_WORKER` + `ROLE_USER` |
| Default user | `ROLE_USER` |

---

## 👤 Roles System
The system uses Symfony roles:

| Role | Meaning |
| :--- | :--- |
| `ROLE_USER` | Normal customer |
| `ROLE_WORKER` | Admin /api/ staff |

---

## 🏠 Home /api/ Dashboard
`GET /api/`

**Redirects:**
* If not logged in → `/login`
* If logged in → dashboard

**Data passed to frontend:**
* `show_products` => `ROLE_WORKER` only

---

## 📦 Products

### List Products
`GET /api/product`

**Access:**
* Only `ROLE_WORKER`

**Behavior:**
* Shows only active products
* Soft-deleted products are hidden

### Create Product
`POST /api/product/new`

**Access:**
* `ROLE_WORKER`

### Edit Product
`POST /api/product/{id}/edit`

**Rules:**
* Cannot edit deleted products

### Soft Delete Product
`POST /api/product/{id}`

**Behavior:**
* Product is NOT removed from database
* Instead: `deletedAt = now()`

**Product Rules:**
* Products are never hard deleted
* Hidden using: `deletedAt IS NULL`

---

## 🍔 Orders

### List Orders
`GET /api/order`

**Behavior:**
| Role | Data visible |
| :--- | :--- |
| `ROLE_WORKER` | All active orders |
| `ROLE_USER` | Own active orders |

### Create Order
`POST /api/order/new`

**Flow:**
* User selects products
* Order is created
* OrderLines are created
* Product price is copied at order time

**Important:**
* Only active products are selectable
* Deleted products are hidden

### Complete Order
`POST /api/order/{id}/complete`

**Behavior:**
* Marks order as completed
* Generates invoice if not existing
* Calculates total from order lines

### Soft Delete Order
`POST /api/order/{id}`

**Behavior:**
* Sets: `deletedAt = now()`

---

## 🧾 Invoices

### List Invoices
`GET /api/invoice`

**Rules:**
| Role | Visibility |
| :--- | :--- |
| `ROLE_WORKER` | All invoices |
| `ROLE_USER` | Own invoices |

### View Invoice
`GET /api/invoice/{id}`

**Security:**
* Users can only view their own invoices
* Workers can view all

### Generate PDF Invoice
`GET /api/invoice/{id}/pdf`

**Features:**
* Fully hydrated data (order + products)
* Uses Dompdf
* A4 portrait PDF

**Includes:**
* Customer
* Order type
* Status
* Date
* Products list
* Total

---

## 🧠 Data Structure

### Order
* `id`
* `user`
* `status` (serving /api/ completed)
* `type` (dine_in /api/ take_away)
* `createdAt`
* `deletedAt` (soft delete)

### OrderLine
* `product`
* `quantity`
* `price` (snapshot at order time)
* `orderRelation`

### Product
* `name`
* `description`
* `price`
* `isAvailable`
* `deletedAt` (soft delete)

### Invoice
* `user`
* `orderRelation`
* `total`
* `createdAt`

---

## ⚠️ Important Business Rules

1. **Soft Delete System**
   * Orders and Products are never physically deleted
   * They are hidden using `deletedAt`
2. **Invoice Immutability**
   * Invoice always reflects original order state
   * Product changes do NOT affect invoices
3. **Price Snapshot**
   * OrderLine stores product price at time of order
4. **Security**
   * `ROLE_USER` → sees own data only
   * `ROLE_WORKER` → sees all data + admin features

---

## 🚀 Frontend Integration Notes
Frontend developers should:
* Always use `/order` for order data
* Always use `/invoice` for billing
* Never rely on deleted products (they are hidden)
* Expect null-safe product references in invoices (future-proofing)

---

## 📌 Summary
This backend provides:
* ✔ Secure authentication
* ✔ Role-based dashboard
* ✔ Full order lifecycle
* ✔ Invoice generation + PDF export
* ✔ Soft delete system (safe data retention)
* ✔ Stable financial history (no data loss)
