import { Link, useNavigate, useLocation } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

function Navbar() {
  const { user, logout, isAdmin } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  if (!user) return null;

const handleLogout = async () => {
  try {
    await logout(); // 👈 important
    navigate("/login");
  } catch (err) {
    console.error("Logout failed:", err);
    // still force logout locally if backend fails
    localStorage.removeItem("user");
    navigate("/login");
  }
};

  // Helper to highlight active links
  const isActive = (path) => location.pathname === path ? "text-white" : "text-gray-400 hover:text-gray-200";

  return (
    <nav className="flex justify-between items-center px-6 py-4 bg-gray-900 text-white shadow-lg sticky top-0 z-50">
      <div className="flex items-center gap-8">
        <Link to="/" className="text-xl font-black tracking-tighter text-blue-400">
          PS<span className="text-white">SYS</span>
        </Link>

        <div className="flex gap-6 text-sm font-bold uppercase tracking-wider">
          {/* CLIENT LINKS */}
          <Link to="/orders" className={isActive("/orders")}>Orders</Link>
          <Link to="/create-order" className={isActive("/create-order")}>New Order</Link>
          <Link to="/invoices" className={isActive("/invoices")}>Invoices</Link>

          {/* ADMIN/WORKER LINKS */}
          {isAdmin && (
            <>
              <div className="w-px h-4 bg-gray-700 self-center" />
              <Link to="/admin" className={isActive("/admin")}>Admin</Link>
              <Link to="/admin/products" className={isActive("/admin/products")}>Inventory</Link>
            </>
          )}
        </div>
      </div>

      <div className="flex gap-6 items-center">
        <div className="text-right hidden sm:block">
          <p className="text-[10px] font-black text-blue-400 uppercase leading-none">Logged in as</p>
          <p className="text-xs font-medium text-gray-300">{user.email}</p>
        </div>

        <button
          onClick={handleLogout}
          className="bg-red-500/10 text-red-500 border border-red-500/20 px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-red-500 hover:text-white transition-all"
        >
          Logout
        </button>
      </div>
    </nav>
  );
}

export default Navbar;