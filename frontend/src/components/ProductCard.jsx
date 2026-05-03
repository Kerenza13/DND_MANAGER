function ProductCard({ product, onAdd }) {
  // If soft-deleted, don't render
  if (product.deletedAt) return null;

  return (
    <div className="flex flex-col border rounded-xl overflow-hidden shadow-sm bg-white hover:shadow-md transition-shadow border-gray-100">
      <div className="p-4 flex-grow">
        <div className="flex justify-between items-start mb-2">
          <h3 className="font-bold text-gray-800 text-lg leading-tight">
            {product.name}
          </h3>
          <span className="font-black text-blue-600 bg-blue-50 px-2 py-1 rounded text-sm">
            €{Number(product.price).toFixed(2)}
          </span>
        </div>

        <p className="text-sm text-gray-500 line-clamp-2 mb-4">
          {product.description || "No description available."}
        </p>

        <div className="flex items-center gap-2 mb-4">
          <div className={`h-2 w-2 rounded-full ${product.isAvailable ? 'bg-green-500' : 'bg-red-500'}`} />
          <span className={`text-xs font-bold uppercase tracking-wider ${product.isAvailable ? 'text-green-600' : 'text-red-600'}`}>
            {product.isAvailable ? 'In Stock' : 'Out of Stock'}
          </span>
        </div>
      </div>

      <button
        disabled={!product.isAvailable}
        onClick={() => onAdd(product)}
        className="w-full py-3 font-bold text-sm transition-colors bg-gray-900 text-white hover:bg-gray-800 disabled:bg-gray-200 disabled:text-gray-400"
      >
        {product.isAvailable ? "+ Add to Order" : "Currently Unavailable"}
      </button>
    </div>
  );
}

export default ProductCard;