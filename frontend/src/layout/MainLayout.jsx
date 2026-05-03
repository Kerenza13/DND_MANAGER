import { Outlet } from "react-router-dom";
import Navbar from "../components/Navbar";

function MainLayout() {
  return (
    <div className="min-h-screen flex flex-col bg-gray-50">
      {/* Top Navigation */}
      <Navbar />

      {/* Main Content Area */}
      <main className="flex-grow container mx-auto p-4 md:p-6">
        <div className="bg-white shadow-sm rounded-lg p-4 min-h-[80vh]">
          <Outlet />
        </div>
      </main>

      {/* Optional Footer */}
      <footer className="p-4 text-center text-gray-500 text-sm border-t bg-white">
        &copy; {new Date().getFullYear()} Your Store Name. All rights reserved.
      </footer>
    </div>
  );
}

export default MainLayout;