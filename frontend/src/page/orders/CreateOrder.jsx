import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../../context/AuthContext";
import { useOrder } from "../../context/OrderContext";
import ProductCard from "../../components/ProductCard";

function CreateOrder() {
  const { authFetch } = useAuth();
  const navigate = useNavigate();
  const {
    items,
    addProduct,
    removeProduct,
    updateQuantity,
    total, // Use the memoized value, not the function
    createOrder,
    setType,
    type,
  } = useOrder();

  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(false);
  const API_URL = import.meta.env.VITE_API_URL;

  useEffect(() => {
    const fetchProducts = async () => {
      try {
        const res = await authFetch(`${API_URL}/product`);
        const data = await res.json();
        setProducts(data);
      } catch (err) {
        console.error("Failed to load products", err);
      }
    };
    fetchProducts();
  }, [authFetch]);

  const handleSubmit = async () => {
    if (items.length === 0) return alert("Your cart is empty!");
    
    setLoading(true);
    try {
      const result = await createOrder();
      alert("Order created successfully!");
      navigate(`/orders/${result.id}`); // Send user to see their new order
    } catch (err) {
      alert(err.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
      {/* PRODUCTS LIST */}
      <div>
        <h2 className="text-2xl font-bold mb-6">Menu</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {products.map((p) => (
            <ProductCard
              key={p.id}
              product={p}
              onAdd={() => addProduct(p)}
            />
          ))}
        </div>
      </div>

      {/* CART SUMMARY */}
      <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit sticky top-4">
        <h2 className="text-2xl font-bold mb-4">Your Order</h2>
        
        <div className="mb-6">
          <label className="block text-sm font-medium text-gray-600 mb-1">Order Type</label>
          <select
            value={type}
            onChange={(e) => setType(e.target.value)}
            className="w-full border rounded-lg p-2.5 bg-gray-50 focus:ring-2 focus:ring-green-500 outline-none"
          >
            <option value="take_away">🥡 Take Away</option>
            <option value="delivery">🛵 Delivery</option>
            <option value="dine_in">🍽️ Dine In</option>
          </select>
        </div>

        <ul className="divide-y mb-6">
          {items.length === 0 && <p className="text-gray-400 py-4 text-center">Empty cart...</p>}
          {items.map((item) => (
            <li key={item.id} className="py-3 flex justify-between items-center">
              <div className="flex-grow">
                <p className="font-semibold text-gray-800">{item.name}</p>
                <p className="text-sm text-gray-500">€{item.price.toFixed(2)} each</p>
              </div>

              <div className="flex items-center gap-3">
                <input
                  type="number"
                  min="1"
                  value={item.quantity}
                  onChange={(e) => updateQuantity(item.id, parseInt(e.target.value) || 1)}
                  className="w-14 border rounded p-1 text-center"
                />
                <button
                  onClick={() => removeProduct(item.id)}
                  className="text-red-400 hover:text-red-600 p-1"
                >
                  ✕
                </button>
              </div>
            </li>
          ))}
        </ul>

        <div className="border-t pt-4">
          <div className="flex justify-between text-xl font-black text-gray-900 mb-4">
            <span>Total:</span>
            <span>€{total.toFixed(2)}</span>
          </div>

          <button
            onClick={handleSubmit}
            disabled={loading || items.length === 0}
            className="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition-all disabled:opacity-50"
          >
            {loading ? "Processing..." : "Place Order"}
          </button>
        </div>
      </div>
    </div>
  );
}

export default CreateOrder;