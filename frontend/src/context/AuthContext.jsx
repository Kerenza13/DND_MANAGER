import { createContext, useState, useEffect, useContext } from "react";

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  const API_URL = import.meta.env.VITE_API_URL;

  // Persistence logic
  useEffect(() => {
    const savedUser = localStorage.getItem("user");
    if (savedUser) {
      try {
        setUser(JSON.parse(savedUser));
      } catch (e) {
        localStorage.removeItem("user");
      }
    }
    setLoading(false);
  }, []);

  /**
   * 📝 REGISTER
   * Payload: { email, password, isWorker }
   * Note: Symfony RegistrationFormType uses a prefix, 
   * so we send FormData for that specific endpoint.
   */
  const register = async (email, password, isWorker = false) => {
    const formData = new FormData();
    // Maps to your RegistrationFormType fields
    formData.append("registration_form[email]", email);
    formData.append("registration_form[plainPassword]", password);
    formData.append("registration_form[agreeTerms]", "1"); 
    
    // role logic: if isWorker is true, backend sets ROLE_WORKER
    if (isWorker) {
      formData.append("registration_form[isWorker]", "1");
    }

    const res = await fetch(`${API_URL}/register`, {
      method: "POST",
      headers: { "Accept": "application/json" } 
    });

    const data = await res.json();

    if (!res.ok) {
      const errorMsg = data.errors ? data.errors.join(" | ") : "Registration failed";
      throw new Error(errorMsg);
    }

    return data;
  };

  /**
   * 🔐 LOGIN
   * Payload: { email, password }
   */
  const login = async (email, password) => {
    const res = await fetch(`${API_URL}/api/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }), // Sending email/password payload
    });

    const data = await res.json();

    if (!res.ok) {
      throw new Error(data.error || "Invalid credentials");
    }

    // Normalize user data (Symfony usually returns email or user object)
    const userPayload = typeof data.user === 'string' 
      ? { email: data.user, roles: ['ROLE_USER'] } 
      : data.user;

    localStorage.setItem("user", JSON.stringify(userPayload));
    setUser(userPayload);
    
    return userPayload;
  };

  const logout = () => {
    localStorage.removeItem("user");
    setUser(null);
  };

  // Helper for Role-based UI
  const isAdmin = user?.roles?.includes("ROLE_WORKER") || user?.roles?.includes("ROLE_ADMIN");

  return (
    <AuthContext.Provider value={{ user, login, logout, register, loading, isAdmin }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);