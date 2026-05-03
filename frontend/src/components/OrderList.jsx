import { OrderCard } from "./OrderCard";

export default function OrderList({ orders }) {
  if (!orders || orders.length === 0) return <p>No orders found.</p>;

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
      {orders.map(order => (
        <OrderCard key={order.id} order={order} />
      ))}
    </div>
  );
}