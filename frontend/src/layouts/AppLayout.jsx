import { NavLink, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/useAuth';

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

export function AppLayout({ children }) {
  const { logout, user } = useAuth();
  const navigate = useNavigate();

  async function handleLogout() {
    await logout();
    navigate('/login');
  }

  return (
    <div className="shell">
      <aside className="sidebar">
        <div className="brand-block">
          <span className="eyebrow">BodaConnect</span>
          <h1>Operations Console</h1>
          <p>React frontend talking to the Laravel API.</p>
        </div>

        <nav className="nav">
          {navigationByRole[user.role].map((item) => (
            <NavLink
              key={item.to}
              className={({ isActive }) =>
                `nav-link${isActive ? ' nav-link-active' : ''}`
              }
              to={item.to}
            >
              {item.label}
            </NavLink>
          ))}
        </nav>

        <div className="user-card">
          <span className="eyebrow">{user.role}</span>
          <strong>{user.name}</strong>
          <span>{user.email}</span>
          <button className="button button-secondary" onClick={handleLogout} type="button">
            Logout
          </button>
        </div>
      </aside>

      <main className="content">{children}</main>
    </div>
  );
}
