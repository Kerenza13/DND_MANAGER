import { useEffect, useState, useContext } from "react";
import { AuthContext } from "../../context/AuthContext";
import ProductCard from "../../components/ProductCard";

function ManageProducts() {
  const { authFetch } = useContext(AuthContext);
  const [products, setProducts] = useState([]);

  const fetchProducts = async () => {
    const res = await authFetch("http://localhost:8000/product");
    const data = await res.json();
    setProducts(data);
  };

  useEffect(() => {
    fetchProducts();
  }, []);

  const handleDelete = async (id) => {
    await authFetch(`http://localhost:8000/product/${id}`, {
      method: "POST",
    });

    fetchProducts();
  };

  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">
        Manage Products
      </h1>

      <div className="grid grid-cols-3 gap-4">
        {products.map((p) => (
          <div key={p.id}>
            <ProductCard product={p} onAdd={() => {}} />

            <button
              onClick={() => handleDelete(p.id)}
              className="bg-red-500 text-white px-2 py-1 mt-2 rounded"
            >
              Delete
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}

export default ManageProducts;