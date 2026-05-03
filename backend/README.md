# 📘 Restaurant Manager API Documentation (v2.0)

## 🧭 Overview
A headless Symfony-based restaurant management system designed for a React frontend.
* **Architecture:** RESTful API (Stateless)
* **Format:** All requests/responses use `application/json`
* **Storage:** Dockerized database with Soft Delete logic
* **Exports:** PDF generation for legal billing

---

## 🔐 Authentication & Identity

### Login
`POST /api/login`
* **Body:** `{"email": "...", "password": "..."}`
* **Response:** Returns User ID, Email, and Roles on success.

### Logout
`POST /api/logout`
* **Action:** Intercepted by the firewall. Frontends should clear local storage/session cookies upon calling this.

### Register
`POST /api/register`
* **Body:** `{"email": "...", "password": "...", "isWorker": true/false}`
* **Logic:** Automatically hashes passwords and assigns `ROLE_USER`. If `isWorker` is true, assigns `ROLE_WORKER`.

---

## 👤 Permissions & RBAC
The system enforces a strict hierarchy to ensure data privacy.

| Role | Permissions |
| :--- | :--- |
| `ROLE_USER` | Create orders, view personal order history, download personal invoices. |
| `ROLE_WORKER` | Manage product catalog, view all orders/invoices, mark orders as complete. |

---

## 🏠 Dashboard Data
`GET /api/dashboard`
Provides a context-aware state for the React frontend to build the UI.
* **Payload:** Contains `isAuthenticated`, `userIdentifier`, and a `permissions` object (e.g., `can_view_products`).

---

## 📦 Product Management (Worker Only)
All product endpoints require `ROLE_WORKER`.

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/product` | List all non-deleted products. |
| `POST` | `/api/product` | Create a product (`nombre`, `precio`, `avalible`). |
| `GET` | `/api/product/{id}` | Get specific product details. |
| `PUT/PATCH`| `/api/product/{id}` | Update product details. |
| `DELETE` | `/api/product/{id}` | **Soft Delete:** Sets `deletedAt` timestamp. |

---

## 🍔 Order Lifecycle

### 1. Placement
`POST /api/order`
* **Access:** `ROLE_USER` or `ROLE_WORKER`.
* **Process:** Snapshots the current price of products into `OrderLines`. This ensures that if a product price changes later, the order history remains accurate.

### 2. Tracking
`GET /api/order`
* **Worker:** Returns every active order in the system.
* **User:** Filters results to show only orders belonging to the authenticated user.

### 3. Fulfillment
`POST /api/order/{id}/complete`
* **Access:** `ROLE_WORKER` only.
* **Action:** Changes status to `completed` and **automatically triggers** the creation of a permanent `Invoice` record.

---

## 🧾 Billing & Invoices

### Invoice List
`GET /api/invoice`
Retrieves billing history. Access is filtered by owner unless the requester is a Worker.

### PDF Export
`GET /api/invoice/{id}/pdf`
* **Output:** `application/pdf` binary stream.
* **Visuals:** Uses Dompdf with A4 portrait orientation.
* **Security:** Prevents users from downloading invoices that do not belong to them.

---

## 🏗️ Technical Business Rules

1.  **Immutable Financials:** Once an `OrderLine` is created, it stores the price as a static value. Even if the `Product` is edited or deleted, the financial record remains unchanged.
2.  **Soft Delete Pattern:** We use a "Trash" logic. Data is never `REMOVED` from the DB; it is filtered out in the `Repository` level using `where('p.deletedAt IS NULL')`.
3.  **Stateless Design:** The API is built to be stateless, making it ideal for mobile apps or React frontends.

---

## 🚀 Integration Guide for React
* **Base URL:** Ensure your `.env.local` points to the Symfony Docker container.
* **Auth State:** Store the user roles returned by `/api/login` in a Global Context (like `useAuth`).
* **PDF Handling:** When calling the PDF endpoint, use a blob response type or open the link in a new tab (`_blank`).
