function OrderCard({ order }) {
  const statusColors = {
    serving: "bg-green-100 text-green-700 border-green-200",
    pending: "bg-yellow-100 text-yellow-700 border-yellow-200",
    completed: "bg-blue-100 text-blue-700 border-blue-200",
    cancelled: "bg-red-100 text-red-700 border-red-200",
  };

  return (
    <div className="border rounded-xl p-5 shadow-sm bg-white border-gray-100 hover:border-blue-300 transition-colors">
      <div className="flex justify-between items-center mb-4">
        <div>
          <h3 className="font-bold text-gray-900">Order #{order.id}</h3>
          <p className="text-[10px] text-gray-400 uppercase font-bold tracking-widest">
            {new Date(order.createdAt).toLocaleDateString()}
          </p>
        </div>

        <span className={`px-3 py-1 text-xs font-black uppercase rounded-full border ${statusColors[order.status] || "bg-gray-100 text-gray-600"}`}>
          {order.status}
        </span>
      </div>

      <div className="space-y-1 mb-4">
        <div className="flex justify-between text-sm">
          <span className="text-gray-500">Service:</span>
          <span className="font-medium capitalize text-gray-700">{order.type.replace('_', ' ')}</span>
        </div>
        <div className="flex justify-between text-sm">
          <span className="text-gray-500">Total Amount:</span>
          <span className="font-bold text-gray-900">€{Number(order.total).toFixed(2)}</span>
        </div>
      </div>

      <div className="pt-3 border-t border-gray-50">
        <h4 className="font-bold text-[10px] uppercase text-gray-400 mb-2">Summary</h4>
        <ul className="space-y-1">
          {order.orderLines?.slice(0, 3).map((line) => (
            <li key={line.id} className="text-xs text-gray-600 flex justify-between">
              <span>{line.quantity}x {line.product?.name}</span>
              <span className="text-gray-400">€{Number(line.priceAtOrder).toFixed(2)}</span>
            </li>
          ))}
          {order.orderLines?.length > 3 && (
            <li className="text-[10px] text-blue-500 font-bold italic pt-1">
              + {order.orderLines.length - 3} more items...
            </li>
          )}
        </ul>
      </div>
    </div>
  );
}

export default OrderCard;