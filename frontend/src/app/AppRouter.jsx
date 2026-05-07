import { Navigate, Route, Routes } from 'react-router-dom';
import { useAuth } from '../auth/useAuth';
import { AppLayout } from '../layouts/AppLayout';
import { LoadingScreen } from '../components/LoadingScreen';
import { AdminCustomersPage } from '../pages/AdminCustomersPage';
import { AdminDashboardPage } from '../pages/AdminDashboardPage';
import { AdminRidersPage } from '../pages/AdminRidersPage';
import { AdminRidesPage } from '../pages/AdminRidesPage';
import { CustomerDashboardPage } from '../pages/CustomerDashboardPage';
import { CustomerRidesPage } from '../pages/CustomerRidesPage';
import { LoginPage } from '../pages/LoginPage';
import { PublicHomePage } from '../pages/PublicHomePage';
import { RegisterPage } from '../pages/RegisterPage';
import { RiderDashboardPage } from '../pages/RiderDashboardPage';

function RoleRedirect() {
  const { user } = useAuth();

  if (!user) {
    return <Navigate to="/" replace />;
  }

  return (
    <Navigate
      replace
      to={
        user.role === 'admin'
          ? '/admin/dashboard'
          : user.role === 'rider'
            ? '/rider/dashboard'
            : '/customer/dashboard'
      }
    />
  );
}

function ProtectedRoute({ children, roles }) {
  const { isLoading, user } = useAuth();

  if (isLoading) {
    return <LoadingScreen label="Checking your session and permissions." />;
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  if (roles && !roles.includes(user.role)) {
    return <RoleRedirect />;
  }

  return children;
}

function GuestRoute({ children }) {
  const { isLoading, user } = useAuth();

  if (isLoading) {
    return <LoadingScreen label="Loading the sign-in experience." />;
  }

  if (user) {
    return <RoleRedirect />;
  }

  return children;
}

export function AppRouter() {
  return (
    <Routes>
      <Route
        path="/"
        element={
          <GuestRoute>
            <PublicHomePage />
          </GuestRoute>
        }
      />
      <Route
        path="/login"
        element={
          <GuestRoute>
            <LoginPage />
          </GuestRoute>
        }
      />
      <Route
        path="/register"
        element={
          <GuestRoute>
            <RegisterPage />
          </GuestRoute>
        }
      />
      <Route
        path="/customer/dashboard"
        element={
          <ProtectedRoute roles={['customer']}>
            <AppLayout>
              <CustomerDashboardPage />
            </AppLayout>
          </ProtectedRoute>
        }
      />
      <Route
        path="/customer/rides"
        element={
          <ProtectedRoute roles={['customer']}>
            <AppLayout>
              <CustomerRidesPage />
            </AppLayout>
          </ProtectedRoute>
        }
      />
      <Route
        path="/admin/dashboard"
        element={
          <ProtectedRoute roles={['admin']}>
            <AppLayout>
              <AdminDashboardPage />
            </AppLayout>
          </ProtectedRoute>
        }
      />
      <Route
        path="/admin/rides"
        element={
          <ProtectedRoute roles={['admin']}>
            <AppLayout>
              <AdminRidesPage />
            </AppLayout>
          </ProtectedRoute>
        }
      />
      <Route
        path="/admin/customers"
        element={
          <ProtectedRoute roles={['admin']}>
            <AppLayout>
              <AdminCustomersPage />
            </AppLayout>
          </ProtectedRoute>
        }
      />
      <Route
        path="/admin/riders"
        element={
          <ProtectedRoute roles={['admin']}>
            <AppLayout>
              <AdminRidersPage />
            </AppLayout>
          </ProtectedRoute>
        }
      />
      <Route
        path="/rider/dashboard"
        element={
          <ProtectedRoute roles={['rider']}>
            <AppLayout>
              <RiderDashboardPage />
            </AppLayout>
          </ProtectedRoute>
        }
      />
      <Route path="*" element={<RoleRedirect />} />
    </Routes>
  );
}
