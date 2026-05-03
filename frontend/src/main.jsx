import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import './index.css';
import App from './App.jsx';

// FIXED: Added curly braces to import the named exports properly
import { AuthProvider } from './context/AuthContext.jsx'; 
import { OrderProvider } from './context/OrderContext.jsx';

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <BrowserRouter>
      <AuthProvider>
        <OrderProvider>
          <App />
        </OrderProvider>
      </AuthProvider>
    </BrowserRouter>
  </StrictMode>
);