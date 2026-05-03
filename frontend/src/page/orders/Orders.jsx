import { useEffect, useState } from "react";
import { useAuth } from "../../context/AuthContext";
import OrderCard from "../../components/OrderCard";
import { useNavigate, Link } from "react-router-dom";

function Orders() {
  const { authFetch } = useAuth();
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  useEffect(() => {
    const fetchOrders = async () => {
      try {
        const res = await authFetch("http://localhost:8000/order");
        if (!res.ok) throw new Error("Failed to load orders");
        const data = await res.json();
        setOrders(data);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    };
    fetchOrders();
  }, [authFetch]);

  if (loading) return <p className="text-center p-10">Loading orders...</p>;

  return (
    <div className="max-w-5xl mx-auto">
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-black text-gray-800">My Orders</h1>
        <Link 
          to="/create-order" 
          className="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700"
        >
          + New Order
        </Link>
      </div>

      {orders.length === 0 ? (
        <div className="text-center py-20 bg-gray-50 rounded-xl border-2 border-dashed">
          <p className="text-gray-500 mb-4">No orders placed yet.</p>
          <Link to="/create-order" className="text-blue-600 font-bold underline">Order something now!</Link>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {orders.map((order) => (
            <div
              key={order.id}
              onClick={() => navigate(`/orders/${order.id}`)}
              className="cursor-pointer transition hover:shadow-md rounded-xl"
            >
              <OrderCard order={order} />
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

export default Orders;