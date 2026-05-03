/* eslint-disable react-refresh/only-export-components */
import { createContext, useContext, useState, useMemo } from "react";
import { useAuth } from "./AuthContext";

const OrderContext = createContext();
export const useOrder = () => useContext(OrderContext);

const OrderProvider = ({ children }) => {
  const { authFetch } = useAuth();
  const [items, setItems] = useState([]);
  const [type, setType] = useState("take_away");

  const API_URL = import.meta.env.VITE_API_URL;

  // Logic is clean, but ensure we don't add "undefined" prices
  const addProduct = (product) => {
    setItems((prev) => {
      const exists = prev.find((p) => p.id === product.id);

      if (exists) {
        return prev.map((p) =>
          p.id === product.id ? { ...p, quantity: p.quantity + 1 } : p,
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

  // Memoize total so it only recalculates when "items" change
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

    // Use a relative path or the API_URL from env if available
    const res = await authFetch(`${API_URL}/order/new`, {
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
        total, // Export the value, not the function
        createOrder,
      }}
    >
      {children}
    </OrderContext.Provider>
  );
};

export default OrderProvider;
