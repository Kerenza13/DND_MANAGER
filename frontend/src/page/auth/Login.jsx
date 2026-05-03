import { useState } from "react";
import { useNavigate, useLocation, Link } from "react-router-dom";
import { useAuth } from "../../context/AuthContext";

function Login() {
  const { login } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  // Capture success messages from the Register navigation
  const successMsg = location.state?.message;

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setLoading(true);

    try {
      // 1. Perform login logic
      await login(email, password);
      
      // 2. Logic for Role-Based redirection or "back to where you were"
      // We check if the user was redirected here by a ProtectedRoute (location.state.from)
      const from = location.state?.from?.pathname;
      
      if (from) {
        navigate(from, { replace: true });
      } else {
        // Fallback: If they came here directly, check roles
        // Note: We get the user data from localStorage if the state isn't updated yet,
        // or just rely on a standard path.
        navigate("/orders");
      }
    } catch (err) {
      setError(err.message || "Invalid email or password");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="h-screen flex items-center justify-center bg-gray-50 p-4">
      <form
        onSubmit={handleSubmit}
        className="p-8 bg-white shadow-xl rounded-lg w-full max-w-md border border-gray-100"
      >
        <h2 className="text-2xl font-bold mb-6 text-gray-800 text-center">Login</h2>

        {/* Success Message from Registration */}
        {successMsg && (
          <div className="bg-green-50 text-green-700 p-3 rounded mb-4 text-sm border border-green-200">
            {successMsg}
          </div>
        )}

        {/* Error Message */}
        {error && (
          <div className="bg-red-50 text-red-600 p-3 rounded mb-4 text-sm border border-red-200">
            {error}
          </div>
        )}

        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input
              type="email"
              placeholder="name@company.com"
              className="border w-full p-2.5 rounded focus:ring-2 focus:ring-blue-500 outline-none transition"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input
              type="password"
              placeholder="••••••••"
              className="border w-full p-2.5 rounded focus:ring-2 focus:ring-blue-500 outline-none transition"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>

          <button 
            disabled={loading}
            className="bg-blue-600 hover:bg-blue-700 text-white w-full p-3 rounded-md font-semibold shadow-sm disabled:opacity-50 transition-colors mt-2"
          >
            {loading ? "Authenticating..." : "Sign In"}
          </button>
        </div>

        <p className="text-sm mt-6 text-center text-gray-600">
          Don't have an account?{" "}
          <Link to="/register" className="text-blue-600 font-medium hover:underline">
            Register here
          </Link>
        </p>
      </form>
    </div>
  );
}

export default Login;