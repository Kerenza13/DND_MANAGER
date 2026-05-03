import { createContext, useState, useEffect, useContext } from "react";

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  const API_URL = import.meta.env.VITE_API_URL;

  // 🔥 DEBUG: confirm backend URL
  console.log("🔌 API_URL =", API_URL);

  // -------------------------
  // LOAD SESSION USER
  // -------------------------
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

  // -------------------------
  // SAFE FETCH WRAPPER (DEBUGGING ADDED)
  // -------------------------
  const safeFetch = async (url, options = {}) => {
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
      console.error("❌ NETWORK ERROR (FAILED TO FETCH):", err);
      throw new Error(
        "Network error: backend unreachable (check API_URL / CORS / server)"
      );
    }
  };

  // -------------------------
  // REGISTER
  // -------------------------
  const register = async (email, password, isWorker = false) => {
    const res = await safeFetch(`${API_URL}/register`, {
      method: "POST",
      body: JSON.stringify({ email, password, isWorker }),
    });

    const text = await res.text();

    let data = {};
    try {
      data = JSON.parse(text);
    } catch {}

    if (!res.ok) {
      console.log("❌ REGISTER ERROR RESPONSE:", text);
      throw new Error(data.error || data.message || "Registration failed");
    }

    return data;
  };

  // -------------------------
  // LOGIN
  // -------------------------
  const login = async (email, password) => {
    const res = await safeFetch(`${API_URL}/login`, {
      method: "POST",
      body: JSON.stringify({ email, password }),
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
      throw new Error(data.message || data.error || "Login failed");
    }

    const userPayload = data.user;

    localStorage.setItem("user", JSON.stringify(userPayload));
    setUser(userPayload);

    return userPayload;
  };

  // -------------------------
  // LOGOUT
  // -------------------------
  const logout = async () => {
    await safeFetch(`${API_URL}/logout`, {
      method: "POST",
    });

    localStorage.removeItem("user");
    setUser(null);
  };

  // -------------------------
  // ROLE CHECK
  // -------------------------
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
        loading,
        isAdmin,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

// -------------------------
export const useAuth = () => {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error("useAuth must be used within AuthProvider");
  }

  return context;
};

export default AuthContext;