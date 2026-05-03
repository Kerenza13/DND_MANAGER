import { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import { useAuth } from "../../context/AuthContext";

function OrderDetail() {
  const { id } = useParams();
  const { authFetch } = useAuth();
  const [order, setOrder] = useState(null);

  useEffect(() => {
    const fetchOrder = async () => {
      try {
        const res = await authFetch(`http://localhost:8000/order/${id}`);
        const data = await res.json();
        setOrder(data);
      } catch (err) {
        console.error(err);
      }
    };
    fetchOrder();
  }, [id, authFetch]);

  if (!order) return <p className="text-center p-10">Loading order details...</p>;

  return (
    <div className="max-w-2xl mx-auto">
      <div className="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
        <div className="bg-gray-800 p-6 text-white flex justify-between items-center">
          <div>
            <h1 className="text-2xl font-bold">Order Details</h1>
            <p className="text-gray-400 font-mono text-sm">Ref: #{order.id}</p>
          </div>
          <span className="bg-yellow-500 text-black text-xs font-black px-3 py-1 rounded-full uppercase">
            {order.status}
          </span>
        </div>

        <div className="p-6">
          <div className="flex justify-between mb-8 text-sm text-gray-600">
            <div>
              <p className="uppercase tracking-widest font-bold text-[10px]">Type</p>
              <p className="text-gray-900 font-semibold capitalize">{order.type.replace('_', ' ')}</p>
            </div>
            <div className="text-right">
              <p className="uppercase tracking-widest font-bold text-[10px]">Total Paid</p>
              <p className="text-2xl font-black text-gray-900">€{Number(order.total).toFixed(2)}</p>
            </div>
          </div>

          <h2 className="font-bold text-gray-800 mb-4 border-b pb-2">Items Ordered</h2>
          <ul className="space-y-4 mb-8">
            {order.orderLines.map((line) => (
              <li key={line.id} className="flex justify-between items-center">
                <div className="flex items-center gap-4">
                  <span className="bg-gray-100 h-8 w-8 flex items-center justify-center rounded-full text-xs font-bold">
                    {line.quantity}
                  </span>
                  <span className="text-gray-700">{line.product.name}</span>
                </div>
                <span className="font-mono text-gray-900 font-semibold">
                  €{(line.priceAtOrder * line.quantity).toFixed(2)}
                </span>
              </li>
            ))}
          </ul>

          <div className="flex flex-col gap-3">
            {/* If there is an invoice related, show the link */}
            {order.invoice && (
              <Link 
                to={`/invoices/${order.invoice.id}`}
                className="w-full bg-blue-50 text-blue-600 text-center py-3 rounded-lg font-bold hover:bg-blue-100 transition"
              >
                View Invoice 📄
              </Link>
            )}
            <Link to="/orders" className="text-center text-gray-500 text-sm hover:underline">
              Back to all orders
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}

export default OrderDetail;