function InvoiceCard({ invoice }) {
  // Helper for currency formatting
  const formatCurrency = (amount) => 
    new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(amount);

  return (
    <div className="border border-gray-200 rounded-xl p-5 shadow-sm bg-white hover:border-blue-400 transition-colors group">
      {/* Header */}
      <div className="flex justify-between items-start mb-4">
        <div>
          <h3 className="font-extrabold text-gray-900 group-hover:text-blue-600 transition-colors">
            Invoice #{invoice.id}
          </h3>
          <p className="text-[10px] uppercase tracking-widest font-bold text-gray-400">
            {new Date(invoice.createdAt).toLocaleDateString()}
          </p>
        </div>
        <div className="text-right">
          <p className="text-[10px] uppercase font-bold text-gray-400">Total</p>
          <p className="text-lg font-black text-gray-900">{formatCurrency(invoice.total)}</p>
        </div>
      </div>

      {/* User Info (Conditional) */}
      {invoice.user && (
        <div className="flex items-center gap-2 mb-4 bg-gray-50 p-2 rounded-lg">
          <div className="w-2 h-2 bg-gray-400 rounded-full"></div>
          <p className="text-xs text-gray-600 truncate">
            {invoice.user.email}
          </p>
        </div>
      )}

      {/* Linked Order Summary */}
      {invoice.orderRelation && (
        <div className="mb-4 space-y-1">
          <div className="flex justify-between text-xs">
            <span className="text-gray-500 font-medium">Order Ref:</span>
            <span className="text-gray-800 font-bold">#{invoice.orderRelation.id}</span>
          </div>
          <div className="flex justify-between text-xs">
            <span className="text-gray-500 font-medium">Status:</span>
            <span className={`font-bold px-1.5 py-0.5 rounded uppercase text-[9px] ${
              invoice.orderRelation.status === 'paid' 
                ? 'bg-green-100 text-green-700' 
                : 'bg-blue-100 text-blue-700'
            }`}>
              {invoice.orderRelation.status}
            </span>
          </div>
        </div>
      )}

      {/* Item Teaser (Shows first 2 items) */}
      <div className="pt-3 border-t border-dashed border-gray-100">
        <h4 className="text-[10px] uppercase font-black text-gray-400 mb-2">Items Included</h4>
        <ul className="space-y-1.5">
          {invoice.orderRelation?.orderLines?.slice(0, 2).map((line, index) => (
            <li key={index} className="flex justify-between text-xs items-center">
              <span className="text-gray-600">
                <span className="font-bold text-gray-900">{line.quantity}x</span> {line.productName}
              </span>
              <span className="text-gray-400 font-mono">
                {formatCurrency(line.priceAtOrder)}
              </span>
            </li>
          ))}
          {invoice.orderRelation?.orderLines?.length > 2 && (
            <li className="text-[10px] text-blue-500 font-medium italic">
              + {invoice.orderRelation.orderLines.length - 2} more items...
            </li>
          )}
        </ul>
      </div>

      {/* Footer "View" Hint */}
      <div className="mt-4 flex justify-end">
        <span className="text-[10px] font-bold text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity">
          Click to view details →
        </span>
      </div>
    </div>
  );
}

export default InvoiceCard;