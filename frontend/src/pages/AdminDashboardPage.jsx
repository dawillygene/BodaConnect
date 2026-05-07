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
        description="A live view of demand, rider availability, and recent booking activity."
        eyebrow="Admin"
        meta="Use this dashboard to spot pending work quickly and keep assignments moving."
        title="Operations dashboard"
      />

      <div className="stats-grid">
        <StatCard caption="Registered customers using the service." label="Customers" value={dashboard.stats.customers} />
        <StatCard caption="Riders currently in your network." label="Riders" value={dashboard.stats.riders} />
        <StatCard caption="All ride requests captured in the platform." label="Total rides" value={dashboard.stats.total_rides} />
        <StatCard caption="Requests waiting for action or assignment." label="Pending" value={dashboard.stats.pending} />
      </div>

      <Panel
        description="Latest ride activity across the platform."
        title="Recent rides"
      >
        {dashboard.recent_rides.length === 0 ? (
          <EmptyState title="No rides found" description="Recent ride activity will appear here." />
        ) : (
          <>
            <div className="table-summary-grid">
              <div className="table-summary-card">
                <span className="table-summary-label">Recent requests</span>
                <strong className="table-summary-value">{dashboard.recent_rides.length}</strong>
              </div>
              <div className="table-summary-card">
                <span className="table-summary-label">Pending focus</span>
                <strong className="table-summary-value">{dashboard.stats.pending}</strong>
              </div>
            </div>
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
          </>
        )}
      </Panel>
    </div>
  );
}
