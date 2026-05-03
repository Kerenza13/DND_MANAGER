import { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { useAuth } from "../../context/AuthContext";

function InvoiceDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { authFetch, token } = useAuth();

  const [invoice, setInvoice] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchInvoice = async () => {
      try {
        const res = await authFetch(`http://localhost:8000/invoice/${id}`);
        if (!res.ok) throw new Error("Invoice not found");
        const data = await res.json();
        setInvoice(data);
      } catch (err) {
        console.error(err);
        navigate("/invoices"); // Redirect if not found
      } finally {
        setLoading(false);
      }
    };

    fetchInvoice();
  }, [id, authFetch, navigate]);

  // SECURE PDF DOWNLOAD
  const handleDownload = async () => {
    try {
      const response = await authFetch(`http://localhost:8000/invoice/${id}/pdf`);
      const blob = await response.blob();
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `invoice_${id}.pdf`);
      document.body.appendChild(link);
      link.click();
      link.parentNode.removeChild(link);
    } catch (error) {
      alert("Could not download PDF");
    }
  };

  if (loading) return <p className="p-8 text-center">Loading invoice details...</p>;
  if (!invoice) return null;

  return (
    <div className="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-xl border border-gray-100">
      <div className="flex justify-between items-start border-b pb-4 mb-6">
        <div>
          <h1 className="text-3xl font-extrabold text-gray-900">Invoice</h1>
          <p className="text-blue-600 font-mono text-lg">#{invoice.id}</p>
        </div>
        <div className="text-right">
          <p className="text-sm text-gray-500">Status</p>
          <span className={`px-2 py-1 rounded text-xs font-bold uppercase ${
            invoice.orderRelation.status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
          }`}>
            {invoice.orderRelation.status}
          </span>
        </div>
      </div>

      <div className="grid grid-cols-2 gap-4 mb-8">
        <div>
          <p className="text-xs text-gray-400 uppercase font-bold">Billed To</p>
          <p className="font-medium text-gray-800">{invoice.user.email}</p>
        </div>
        <div className="text-right">
          <p className="text-xs text-gray-400 uppercase font-bold">Service Type</p>
          <p className="font-medium text-gray-800 capitalize">{invoice.orderRelation.type.replace('_', ' ')}</p>
        </div>
      </div>

      <h2 className="font-bold text-gray-700 mb-3 border-b pb-1">Order Summary</h2>
      <ul className="divide-y divide-gray-100 mb-6">
        {invoice.orderRelation.orderLines.map((line, idx) => (
          <li key={idx} className="py-3 flex justify-between items-center">
            <div>
              <span className="font-bold text-gray-900 mr-2">{line.quantity}x</span>
              <span className="text-gray-600">{line.productName}</span>
            </div>
            <span className="font-medium text-gray-900">€{(line.priceAtOrder * line.quantity).toFixed(2)}</span>
          </li>
        ))}
      </ul>

      <div className="bg-gray-50 p-4 rounded-lg flex justify-between items-center mb-8">
        <span className="text-lg font-bold text-gray-700">Total Amount</span>
        <span className="text-2xl font-black text-gray-900">€{invoice.total.toFixed(2)}</span>
      </div>

      <div className="flex gap-4">
        <button
          onClick={handleDownload}
          className="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2"
        >
          <span>📄</span> Download PDF
        </button>
        <button 
          onClick={() => navigate("/invoices")}
          className="px-6 py-3 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50"
        >
          Back
        </button>
      </div>
    </div>
  );
}

export default InvoiceDetail;