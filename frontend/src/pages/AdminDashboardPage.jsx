import { useEffect, useState } from 'react';
import { useAuth } from '../auth/useAuth';
import { EmptyState } from '../components/EmptyState';
import { LoadingScreen } from '../components/LoadingScreen';
import { PageHeader } from '../components/PageHeader';
import { Panel } from '../components/Panel';
import { StatCard } from '../components/StatCard';
import { StatusBadge } from '../components/StatusBadge';
import { api } from '../lib/api';
import { formatDate } from '../lib/formatters';

export function AdminDashboardPage() {
  const { token } = useAuth();
  const [dashboard, setDashboard] = useState(null);

  useEffect(() => {
    api.get('/dashboard', token).then((response) => setDashboard(response.dashboard));
  }, [token]);

  if (!dashboard) {
    return <LoadingScreen label="Loading admin dashboard" />;
  }

  return (
    <div className="page-stack">
      <PageHeader
        description="High-level view of customers, riders, and ride activity."
        eyebrow="Admin"
        title="Dashboard"
      />

      <div className="stats-grid">
        <StatCard label="Customers" value={dashboard.stats.customers} />
        <StatCard label="Riders" value={dashboard.stats.riders} />
        <StatCard label="Total rides" value={dashboard.stats.total_rides} />
        <StatCard label="Pending" value={dashboard.stats.pending} />
      </div>

      <Panel>
        <h3>Recent rides</h3>
        {dashboard.recent_rides.length === 0 ? (
          <EmptyState title="No rides found" description="Recent ride activity will appear here." />
        ) : (
          <div className="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer</th>
                  <th>Status</th>
                  <th>Created</th>
                </tr>
              </thead>
              <tbody>
                {dashboard.recent_rides.map((ride) => (
                  <tr key={ride.id}>
                    <td>#{ride.id}</td>
                    <td>{ride.customer?.name ?? 'Unknown'}</td>
                    <td><StatusBadge status={ride.status} /></td>
                    <td>{formatDate(ride.created_at)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </Panel>
    </div>
  );
}
