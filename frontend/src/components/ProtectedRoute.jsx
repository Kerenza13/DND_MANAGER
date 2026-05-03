import { Navigate, useLocation } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

const ProtectedRoute = ({ children, role }) => {
  const { user, isAuthenticated } = useAuth();
  const location = useLocation();

  if (!isAuthenticated) {
    // Redirect to login, but save the current location they were trying to go to
    return <Navigate to="/login" state={{ from: location }} replace />;
  }

  if (role && !user?.roles?.includes(role)) {
    // If user is logged in but doesn't have the right role
    return <Navigate to="/orders" replace />;
  }

  return children;
};

export default ProtectedRoute;