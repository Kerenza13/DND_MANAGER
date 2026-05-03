import { Routes, Route, Navigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

// Layouts
import MainLayout from "../layout/MainLayout";
import AdminLayout from "../layout/AdminLayout";

// Pages
import Login from "../page/Auth/Login";
import Register from "../page/Auth/Register";
import Orders from "../page/Orders/Orders";
import OrderDetail from "../page/Orders/OrderDetail";
import CreateOrder from "../page/Orders/CreateOrder";
import Invoices from "../page/Invoices/Invoices";
import InvoiceDetail from "../page/Invoices/InvoiceDetail";
import Dashboard from "../page/Admin/Dashboard";
import ManageProducts from "../page/Admin/ManageProducts";
import ManageOrders from "../page/Admin/ManageOrders";
import ManageInvoices from "../page/Admin/ManageInvoices";

// Guards
import ProtectedRoute from "../components/ProtectedRoute";

function AppRouter() {
  const { user, loading } = useAuth();

  // Very Important: Wait for AuthContext to finish reading localStorage
  if (loading) {
    return <div className="flex h-screen items-center justify-center">Loading...</div>;
  }

  return (
    <Routes>
      {/* 🔐 PUBLIC ROUTES */}
      <Route 
        path="/login" 
        element={user ? <Navigate to="/orders" /> : <Login />} 
      />
      <Route 
        path="/register" 
        element={user ? <Navigate to="/orders" /> : <Register />} 
      />

      {/* 🧑 CLIENT ROUTES */}
      <Route
        element={
          <ProtectedRoute>
            <MainLayout />
          </ProtectedRoute>
        }
      >
        <Route path="/" element={<Navigate to="/orders" replace />} />
        <Route path="/orders" element={<Orders />} />
        <Route path="/orders/:id" element={<OrderDetail />} />
        <Route path="/create-order" element={<CreateOrder />} />
        <Route path="/invoices" element={<Invoices />} />
        <Route path="/invoices/:id" element={<InvoiceDetail />} />
      </Route>

      {/* 🧑‍🍳 ADMIN ROUTES */}
      <Route
        element={
          <ProtectedRoute role="ROLE_ADMIN">
            <AdminLayout />
          </ProtectedRoute>
        }
      >
        <Route path="/admin" element={<Dashboard />} />
        <Route path="/admin/products" element={<ManageProducts />} />
        <Route path="/admin/orders" element={<ManageOrders />} />
        <Route path="/admin/invoices" element={<ManageInvoices />} />
      </Route>

      {/* 🚫 FALLBACK */}
      <Route
        path="*"
        element={<Navigate to={user ? "/orders" : "/login"} replace />}
      />
    </Routes>
  );
}

export default AppRouter;