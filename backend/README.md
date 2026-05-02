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
`GET /login`

**Response:**
* Login page (Twig form)
* Returns authentication error if login fails

### Logout
`GET /logout`

* Handled automatically by Symfony firewall.

### Register
`POST /register`

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
| `ROLE_WORKER` | Admin / staff |

---

## 🏠 Home / Dashboard
`GET /`

**Redirects:**
* If not logged in → `/login`
* If logged in → dashboard

**Data passed to frontend:**
* `show_products` => `ROLE_WORKER` only

---

## 📦 Products

### List Products
`GET /product`

**Access:**
* Only `ROLE_WORKER`

**Behavior:**
* Shows only active products
* Soft-deleted products are hidden

### Create Product
`POST /product/new`

**Access:**
* `ROLE_WORKER`

### Edit Product
`POST /product/{id}/edit`

**Rules:**
* Cannot edit deleted products

### Soft Delete Product
`POST /product/{id}`

**Behavior:**
* Product is NOT removed from database
* Instead: `deletedAt = now()`

**Product Rules:**
* Products are never hard deleted
* Hidden using: `deletedAt IS NULL`

---

## 🍔 Orders

### List Orders
`GET /order`

**Behavior:**
| Role | Data visible |
| :--- | :--- |
| `ROLE_WORKER` | All active orders |
| `ROLE_USER` | Own active orders |

### Create Order
`POST /order/new`

**Flow:**
* User selects products
* Order is created
* OrderLines are created
* Product price is copied at order time

**Important:**
* Only active products are selectable
* Deleted products are hidden

### Complete Order
`POST /order/{id}/complete`

**Behavior:**
* Marks order as completed
* Generates invoice if not existing
* Calculates total from order lines

### Soft Delete Order
`POST /order/{id}`

**Behavior:**
* Sets: `deletedAt = now()`

---

## 🧾 Invoices

### List Invoices
`GET /invoice`

**Rules:**
| Role | Visibility |
| :--- | :--- |
| `ROLE_WORKER` | All invoices |
| `ROLE_USER` | Own invoices |

### View Invoice
`GET /invoice/{id}`

**Security:**
* Users can only view their own invoices
* Workers can view all

### Generate PDF Invoice
`GET /invoice/{id}/pdf`

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
* `status` (serving / completed)
* `type` (dine_in / take_away)
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
