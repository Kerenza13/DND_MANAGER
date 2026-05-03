/* eslint-disable react-refresh/only-export-components */
import { createContext, useContext, useState, useMemo } from "react";
import { useAuth } from "./AuthContext";

export const OrderContext = createContext();

export const OrderProvider = ({ children }) => {
  const { authFetch } = useAuth();
  const [items, setItems] = useState([]);
  const [type, setType] = useState("take_away");
  const API_URL = import.meta.env.VITE_API_URL;

  const addProduct = (product) => {
    setItems((prev) => {
      const exists = prev.find((p) => p.id === product.id);
      if (exists) {
        return prev.map((p) =>
          p.id === product.id ? { ...p, quantity: p.quantity + 1 } : p
        );
      }
      return [
        ...prev,
        {
          id: product.id,
          name: product.name,
          price: Number(product.price) || 0,
          quantity: 1,
        },
      ];
    });
  };

  const removeProduct = (id) => {
    setItems((prev) => prev.filter((p) => p.id !== id));
  };

  const updateQuantity = (id, quantity) => {
    if (quantity <= 0) return removeProduct(id);
    setItems((prev) => prev.map((p) => (p.id === id ? { ...p, quantity } : p)));
  };

  const clearOrder = () => {
    setItems([]);
    setType("take_away");
  };

  const total = useMemo(() => {
    return items.reduce((sum, item) => sum + item.price * item.quantity, 0);
  }, [items]);

  const createOrder = async () => {
    if (items.length === 0) throw new Error("Order is empty");

    const payload = {
      type,
      products: items.map((item) => ({
        id: item.id,
        quantity: item.quantity,
      })),
    };

    // FIXED: Endpoint changed from /order/new to /api/order per your docs
    const res = await authFetch(`${API_URL}/api/order`, {
      method: "POST",
      body: JSON.stringify(payload),
    });

    if (!res.ok) {
      const errorData = await res.json().catch(() => ({}));
      throw new Error(errorData.message || "Error creating order");
    }

    const data = await res.json();
    clearOrder();
    return data;
  };

  return (
    <OrderContext.Provider
      value={{
        items,
        type,
        setType,
        addProduct,
        removeProduct,
        updateQuantity,
        clearOrder,
        total,
        createOrder,
      }}
    >
      {children}
    </OrderContext.Provider>
  );
};

export const useOrder = () => {
  const context = useContext(OrderContext);
  if (!context) throw new Error("useOrder must be used within an OrderProvider");
  return context;
};