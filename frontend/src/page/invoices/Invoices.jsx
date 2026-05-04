import { useEffect, useState } from "react";
import { useAuth } from "../../context/AuthContext";
import InvoiceCard from "../../components/InvoiceCard";
import { useNavigate } from "react-router-dom";

function Invoices() {
  const { authFetch } = useAuth();
  const [invoices, setInvoices] = useState([]);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  const API_URL = import.meta.env.VITE_API_URL;

  useEffect(() => {
    const fetchInvoices = async () => {
      try {
        const res = await authFetch(`${API_URL}/invoice`);

        if (!res.ok) {
          throw new Error("Failed to fetch invoices");
        }

        // safety check (prevents HTML crash)
        const contentType = res.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
          throw new Error("Invalid response (expected JSON)");
        }

        const data = await res.json();
        setInvoices(data);
      } catch (err) {
        console.error("❌ fetchInvoices error:", err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchInvoices();
  }, [authFetch, API_URL]);

  if (loading) {
    return <div className="p-8 text-center">Loading your invoices...</div>;
  }

  return (
    <div className="max-w-4xl mx-auto">
      <header className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold text-gray-800">My Invoices</h1>
        <span className="text-sm bg-gray-200 px-3 py-1 rounded-full text-gray-600">
          Total: {invoices.length}
        </span>
      </header>

      {invoices.length === 0 ? (
        <div className="text-center p-12 border-2 border-dashed rounded-lg">
          <p className="text-gray-500">No invoices found.</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {invoices.map((inv) => (
            <div
              key={inv.id}
              onClick={() => navigate(`/invoices/${inv.id}`)}
              className="cursor-pointer transition-transform hover:scale-[1.02]"
            >
              <InvoiceCard invoice={inv} />
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

export default Invoices;