import { Outlet, Link } from "react-router-dom";
import Navbar from "../components/Navbar";

function AdminLayout() {
  return (
    <div className="min-h-screen flex flex-col bg-slate-100">
      {/* Keep the shared Navbar for Profile/Logout actions */}
      <Navbar />

      <div className="flex flex-grow">
        {/* Admin Sidebar - Desktop View */}
        <aside className="w-64 bg-slate-800 text-white hidden md:block border-r border-slate-700">
          <nav className="p-4 space-y-2">
            <h2 className="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">
              Admin Menu
            </h2>
            <Link to="/admin" className="block p-2 hover:bg-slate-700 rounded transition">Dashboard</Link>
            <Link to="/admin/products" className="block p-2 hover:bg-slate-700 rounded transition">Manage Products</Link>
            <Link to="/admin/orders" className="block p-2 hover:bg-slate-700 rounded transition">Manage Orders</Link>
            <Link to="/admin/invoices" className="block p-2 hover:bg-slate-700 rounded transition">Manage Invoices</Link>
          </nav>
        </aside>

        {/* Admin Content Area */}
        <main className="flex-grow p-6 overflow-auto">
          <header className="mb-6">
            <h1 className="text-2xl font-bold text-slate-800">Admin Control Panel</h1>
            <p className="text-slate-500 text-sm">Manage your store operations here.</p>
          </header>

          <div className="bg-white shadow-md rounded-xl p-6 border border-slate-200">
            <Outlet />
          </div>
        </main>
      </div>
    </div>
  );
}

export default AdminLayout;