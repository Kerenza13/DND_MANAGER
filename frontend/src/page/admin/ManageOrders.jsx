import { useEffect, useState, useContext } from "react";
import { AuthContext } from "../../context/AuthContext";
import OrderCard from "../../components/OrderCard";

function ManageOrders() {
  const { authFetch } = useContext(AuthContext);
  const [orders, setOrders] = useState([]);
  const API_URL = import.meta.env.VITE_API_URL;

  const fetchOrders = async () => {
    const res = await authFetch(`${API_URL}/api/order`);
    const data = await res.json();
    setOrders(data);
  };

  useEffect(() => {
    fetchOrders();
  }, []);

  const updateStatus = async (id, status) => {
    await authFetch(`${API_URL}/api/order/${id}/complete`, {
      method: "POST",
      body: JSON.stringify({ status }),
    });

    fetchOrders();
  };

  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">
        Manage Orders
      </h1>

      <div className="grid grid-cols-2 gap-4">
        {orders.map((order) => (
          <div key={order.id}>
            <OrderCard order={order} />

            <div className="flex gap-2 mt-2">
              <button
                onClick={() => updateStatus(order.id, "preparing")}
                className="bg-yellow-500 text-white px-2 py-1 rounded"
              >
                Preparing
              </button>

              <button
                onClick={() => updateStatus(order.id, "completed")}
                className="bg-green-500 text-white px-2 py-1 rounded"
              >
                Complete
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

export default ManageOrders;