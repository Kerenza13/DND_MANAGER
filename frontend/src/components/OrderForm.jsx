import { useOrder } from "../context/OrderContext";

export default function OrderForm() {
  const { items, total, type, setType, createOrder } = useOrder();

  const handleCheckout = async () => {
    if (items.length === 0) return alert("Cart is empty");
    try {
      await createOrder(); // This function should perform the POST to /api/orders
      alert("Order successful!");
    } catch (err) {
      alert("Error: " + err.message);
    }
  };

  return (
    <div className="p-4 border-t bg-gray-50">
      <select 
        value={type} 
        onChange={(e) => setType(e.target.value)}
        className="w-full p-2 border mb-4 rounded"
      >
        <option value="take_away">Take Away</option>
        <option value="dine_in">Dine In</option>
      </select>
      <div className="flex justify-between font-bold text-xl mb-4">
        <span>Total:</span>
        <span>{total.toFixed(2)} €</span>
      </div>
      <button 
        onClick={handleCheckout}
        className="w-full bg-black text-white py-3 rounded-lg font-bold"
      >
        Place Order
      </button>
    </div>
  );
}   