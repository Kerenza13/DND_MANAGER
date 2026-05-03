import { useContext } from "react";
import { AuthContext } from "../../context/AuthContext";

function Dashboard() {
  const { user } = useContext(AuthContext);

  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">
        Admin Dashboard
      </h1>

      <p>Welcome, {user?.email}</p>

      <div className="mt-4">
        <p className="text-gray-600">
          Use the navigation to manage:
        </p>

        <ul className="list-disc ml-6 mt-2">
          <li>Products</li>
          <li>Orders</li>
          <li>Invoices</li>
        </ul>
      </div>
    </div>
  );
}

export default Dashboard;