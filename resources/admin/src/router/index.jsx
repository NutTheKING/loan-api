import { createBrowserRouter, Navigate } from 'react-router-dom';
import { lazy, Suspense } from 'react';
import LoadingSpinner from '../components/shared/LoadingSpinner';

// Lazy load components for better performance
const LandingPage = lazy(() => import('../pages/LandingPage'));
const AdminLogin = lazy(() => import('../pages/admin/AdminLogin'));
const AdminApp = lazy(() => import('../pages/admin/AdminApp'));
const ClientLogin = lazy(() => import('../pages/client/ClientLogin'));
const ClientRegister = lazy(() => import('../pages/client/ClientRegister'));
const ClientApp = lazy(() => import('../pages/client/ClientApp'));
const AuthError = lazy(() => import('../components/shared/AuthError'));

// Protected Route Components
const ProtectedRoute = ({ children, authType = 'admin' }) => {
  const adminUser = localStorage.getItem('adminUser');
  const clientUser = localStorage.getItem('clientUser');
  
  if (authType === 'admin' && !adminUser) {
    return <Navigate to="/admin/login" replace />;
  }
  
  if (authType === 'client' && !clientUser) {
    return <Navigate to="/client/login" replace />;
  }
  
  return children;
};

// Public Route Component (redirect if already logged in)
const PublicRoute = ({ children, authType = 'admin' }) => {
  const adminUser = localStorage.getItem('adminUser');
  const clientUser = localStorage.getItem('clientUser');
  
  if (authType === 'admin' && adminUser) {
    return <Navigate to="/admin" replace />;
  }
  
  if (authType === 'client' && clientUser) {
    return <Navigate to="/client" replace />;
  }
  
  return children;
};

// Admin Layout with nested routes
const AdminLayout = () => (
  <Suspense fallback={<LoadingSpinner />}>
    <AdminApp />
  </Suspense>
);

// Client Layout with nested routes
const ClientLayout = () => (
  <Suspense fallback={<LoadingSpinner />}>
    <ClientApp />
  </Suspense>
);

const router = createBrowserRouter([
  {
    path: '/',
    element: (
      <Suspense fallback={<LoadingSpinner />}>
        <LandingPage />
      </Suspense>
    )
  },
  {
    path: '/admin/login',
    element: (
      <PublicRoute authType="admin">
        <Suspense fallback={<LoadingSpinner />}>
          <AdminLogin />
        </Suspense>
      </PublicRoute>
    )
  },
  {
    path: '/admin',
    element: (
      <ProtectedRoute authType="admin">
        <AdminLayout />
      </ProtectedRoute>
    ),
    children: [
      {
        index: true,
        element: <Navigate to="dashboard" replace />
      },
      {
        path: 'dashboard',
        element: <AdminDashboardView />
      },
      {
        path: 'users',
        element: <UserManagementView />
      },
      {
        path: 'roles',
        element: <RolePermissionView />
      },
      {
        path: 'config',
        element: <LoanConfigView />
      },
      {
        path: 'reports',
        element: <ReportsView />
      },
      {
        path: 'financial',
        element: <FinancialStatsView />
      },
      {
        path: 'members',
        element: <AllMembersView />
      }
    ]
  },
  {
    path: '/client/login',
    element: (
      <PublicRoute authType="client">
        <Suspense fallback={<LoadingSpinner />}>
          <ClientLogin />
        </Suspense>
      </PublicRoute>
    )
  },
  {
    path: '/client/register',
    element: (
      <PublicRoute authType="client">
        <Suspense fallback={<LoadingSpinner />}>
          <ClientRegister />
        </Suspense>
      </PublicRoute>
    )
  },
  {
    path: '/client',
    element: (
      <ProtectedRoute authType="client">
        <ClientLayout />
      </ProtectedRoute>
    )
  },
  {
    path: '/auth-error',
    element: (
      <Suspense fallback={<LoadingSpinner />}>
        <AuthError />
      </Suspense>
    )
  },
  {
    path: '*',
    element: <Navigate to="/" replace />
  }
]);

export default router;