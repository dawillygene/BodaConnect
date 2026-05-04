import { useEffect, useState } from 'react';
import { useAuth } from '../auth/useAuth';
import { LoadingScreen } from '../components/LoadingScreen';
import { EmptyState } from '../components/EmptyState';
import { PageHeader } from '../components/PageHeader';
import { Panel } from '../components/Panel';
import { StatCard } from '../components/StatCard';
import { StatusBadge } from '../components/StatusBadge';
import { api } from '../lib/api';
import { formatDate } from '../lib/formatters';

export function CustomerDashboardPage() {
  const { token } = useAuth();
  const [dashboard, setDashboard] = useState(null);

  useEffect(() => {
    api.get('/dashboard', token).then((response) => setDashboard(response.dashboard));
  }, [token]);

  if (!dashboard) {
    return <LoadingScreen label="Loading customer dashboard" />;
  }

  return (
    <div className="page-stack">
      <PageHeader
        description="Track your latest requests and current ride activity."
        eyebrow="Customer"
        title="Dashboard"
      />

      <div className="stats-grid">
        <StatCard label="Total rides" value={dashboard.stats.total} />
        <StatCard label="Pending" value={dashboard.stats.pending} />
        <StatCard label="Completed" value={dashboard.stats.completed} />
        <StatCard label="Cancelled" value={dashboard.stats.cancelled} />
      </div>

      <Panel>
        <h3>Recent rides</h3>
        {dashboard.recent_rides.length === 0 ? (
          <EmptyState
            description="Create your first ride request from the My Rides page."
            title="No rides yet"
          />
        ) : (
          <div className="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Route</th>
                  <th>Status</th>
                  <th>Created</th>
                </tr>
              </thead>
              <tbody>
                {dashboard.recent_rides.map((ride) => (
                  <tr key={ride.id}>
                    <td>#{ride.id}</td>
                    <td>{ride.pickup_location} to {ride.destination_location}</td>
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
