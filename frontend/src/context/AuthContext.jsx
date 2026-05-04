import { createContext, useState, useEffect, useContext } from "react";

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  const API_URL = import.meta.env.VITE_API_URL;

  useEffect(() => {
    const savedUser = localStorage.getItem("user");

    if (savedUser) {
      try {
        setUser(JSON.parse(savedUser));
      } catch {
        localStorage.removeItem("user");
      }
    }

    setLoading(false);
  }, []);

  // 🔥 FIXED FETCH WRAPPER
  const authFetch = async (url, options = {}) => {
    console.log("➡️ REQUEST:", url);

    try {
      const res = await fetch(url, {
        ...options,
        headers: {
          "Content-Type": "application/json",
          ...(options.headers || {}),
        },
        credentials: "include",
      });

      console.log("⬅️ STATUS:", res.status);
      return res;
    } catch (err) {
      console.error("❌ NETWORK ERROR:", err);
      throw new Error("Backend unreachable");
    }
  };

  // ✅ REGISTER (FIXED: NO /api HERE)
  const register = async (email, password, isWorker = false) => {
    const res = await authFetch(`${API_URL}/register`, {
      method: "POST",
      body: JSON.stringify({ email, password, isWorker }),
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
      throw new Error(data.error || "Registration failed");
    }

    return data;
  };

  // ✅ LOGIN
  const login = async (email, password) => {
    const res = await authFetch(`${API_URL}/login`, {
      method: "POST",
      body: JSON.stringify({ email, password }),
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
      throw new Error(data.error || "Login failed");
    }

    localStorage.setItem("user", JSON.stringify(data.user));
    setUser(data.user);

    return data.user;
  };

  // ✅ LOGOUT
  const logout = async () => {
    try {
      await authFetch(`${API_URL}/logout`, {
        method: "POST",
      });
    } catch (err) {
      console.error("Backend logout failed:", err);
    } finally {
      localStorage.removeItem("user");
      setUser(null);
    }
  };

  const isAdmin =
    user?.roles?.includes("ROLE_WORKER") ||
    user?.roles?.includes("ROLE_ADMIN");

  return (
    <AuthContext.Provider
      value={{
        user,
        setUser,
        login,
        register,
        logout,
        authFetch,
        loading,
        isAdmin,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) throw new Error("useAuth must be used within AuthProvider");
  return context;
};