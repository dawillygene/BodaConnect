import { useState } from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/useAuth';
import { ThemeToggle } from '../components/ThemeToggle';

const navigationByRole = {
  admin: [
    { label: 'Dashboard', to: '/admin/dashboard' },
    { label: 'Rides', to: '/admin/rides' },
    { label: 'Customers', to: '/admin/customers' },
    { label: 'Riders', to: '/admin/riders' },
  ],
  customer: [
    { label: 'Dashboard', to: '/customer/dashboard' },
    { label: 'My Rides', to: '/customer/rides' },
  ],
  rider: [
    { label: 'Dashboard', to: '/rider/dashboard' },
  ],
};

const roleTitles = {
  admin: 'Control Center',
  customer: 'Ride Hub',
  rider: 'Trip Desk',
};

export function AppLayout({ children }) {
  const { logout, user } = useAuth();
  const navigate = useNavigate();
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);

  async function handleLogout() {
    await logout();
    navigate('/login');
  }

  return (
    <div className="app-shell">
      <div className="shell" data-sidebar-open={isSidebarOpen}>
        <div
          aria-hidden="true"
          className="sidebar-backdrop"
          onClick={() => setIsSidebarOpen(false)}
        />

        <aside className="sidebar">
          <div className="sidebar-top">
            <div className="brand-block">
              <span className="brand-mark">
                <span aria-hidden="true" className="brand-mark-dot" />
                BodaConnect
              </span>
              <div className="brand-copy">
                <span className="eyebrow">{user.role}</span>
                <h1>{roleTitles[user.role]}</h1>
              </div>
            </div>

            <nav className="nav">
              {navigationByRole[user.role].map((item) => (
                <NavLink
                  key={item.to}
                  className={({ isActive }) => `nav-link${isActive ? ' nav-link-active' : ''}`}
                  onClick={() => setIsSidebarOpen(false)}
                  to={item.to}
                >
                  <span aria-hidden="true" className="nav-link-bullet" />
                  {item.label}
                </NavLink>
              ))}
            </nav>
          </div>

          <div className="sidebar-footer">
            <div className="user-card">
              <div className="user-head">
                <div className="user-meta">
                  <strong>{user.name}</strong>
                  <span>{user.email}</span>
                </div>
                <div aria-hidden="true" className="user-avatar">
                  {user.name.charAt(0).toUpperCase()}
                </div>
              </div>
              <div className="user-actions">
                <button className="button button-secondary" onClick={handleLogout} type="button">
                  Sign out
                </button>
              </div>
            </div>
          </div>
        </aside>

        <main className="content">
          <div className="content-card">
            <div className="topbar">
              <button
                aria-label="Open navigation"
                className="icon-button topbar-mobile-toggle"
                onClick={() => setIsSidebarOpen(true)}
                type="button"
              >
                Menu
              </button>
              <div className="topbar-actions">
                <ThemeToggle />
              </div>
            </div>
            {children}
          </div>
        </main>
      </div>
    </div>
  );
}
