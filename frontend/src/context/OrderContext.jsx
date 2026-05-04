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
    return items.reduce((sum, i) => sum + i.price * i.quantity, 0);
  }, [items]);

  /**
   * ✅ FIXED CREATE ORDER
   * Matches Symfony Controller:
   * 1. Uses 'items' key instead of 'products'
   * 2. Uses 'product_id' instead of 'id'
   */
  const createOrder = async () => {
    if (items.length === 0) throw new Error("Order is empty");

    const payload = {
      type,
      items: items.map((i) => ({
        product_id: i.id, // Symfony expects product_id
        quantity: i.quantity,
      })),
    };

    try {
      const res = await authFetch(`${API_URL}/order`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(payload),
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok) {
        throw new Error(data.error || data.message || "Error creating order");
      }

      clearOrder();
      return data;
    } catch (error) {
      console.error("❌ Checkout Error:", error.message);
      throw error;
    }
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
  if (!context) throw new Error("useOrder must be used within OrderProvider");
  return context;
};