import { useState } from "react";

export default function ProductForm({ onProductAdded }) {
  const [name, setName] = useState("");
  const [price, setPrice] = useState("");

  const API_URL = import.meta.env.VITE_API_URL;

  const handleSubmit = async (e) => {
    e.preventDefault();
    const res = await fetch(`${API_URL}/products`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, price: parseFloat(price) }),
    });

    if (res.ok) {
      setName("");
      setPrice("");
      if (onProductAdded) onProductAdded();
    }
  };

  return (
    <form onSubmit={handleSubmit} className="p-4 border rounded shadow-sm bg-white">
      <h3 className="font-bold mb-2">New Product</h3>
      <input 
        className="border p-2 w-full mb-2" 
        placeholder="Product Name" 
        value={name} 
        onChange={(e) => setName(e.target.value)} 
      />
      <input 
        className="border p-2 w-full mb-2" 
        type="number" 
        placeholder="Price" 
        value={price} 
        onChange={(e) => setPrice(e.target.value)} 
      />
      <button className="bg-blue-600 text-white px-4 py-2 rounded w-full">Add Product</button>
    </form>
  );
}