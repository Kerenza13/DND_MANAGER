import { useEffect, useState, useContext } from "react";
import { AuthContext } from "../../context/AuthContext";
import InvoiceCard from "../../components/InvoiceCard";

function ManageInvoices() {
  const { authFetch } = useContext(AuthContext);
  const [invoices, setInvoices] = useState([]);

  const fetchInvoices = async () => {
    const res = await authFetch("http://localhost:8000/invoice");
    const data = await res.json();
    setInvoices(data);
  };

  useEffect(() => {
    fetchInvoices();
  }, []);

  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">
        Manage Invoices
      </h1>

      <div className="grid grid-cols-2 gap-4">
        {invoices.map((inv) => (
          <InvoiceCard key={inv.id} invoice={inv} />
        ))}
      </div>
    </div>
  );
}

export default ManageInvoices;