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
        description="See recent activity, understand trip status, and keep your next ride close."
        eyebrow="Customer"
        meta="Your dashboard summarizes everything from new bookings to completed trips."
        title="Customer dashboard"
      />

      <div className="stats-grid">
        <StatCard caption="All rides created from your account." label="Total rides" value={dashboard.stats.total} />
        <StatCard caption="Requests waiting to be assigned or updated." label="Pending" value={dashboard.stats.pending} />
        <StatCard caption="Trips that reached completion." label="Completed" value={dashboard.stats.completed} />
        <StatCard caption="Requests cancelled before completion." label="Cancelled" value={dashboard.stats.cancelled} />
      </div>

      <Panel
        description="Your latest booking activity in chronological order."
        title="Recent rides"
      >
        {dashboard.recent_rides.length === 0 ? (
          <EmptyState
            description="Create your first ride request from the My Rides page."
            title="No rides yet"
          />
        ) : (
          <>
            <div className="table-summary-grid">
              <div className="table-summary-card">
                <span className="table-summary-label">Recent activity</span>
                <strong className="table-summary-value">{dashboard.recent_rides.length}</strong>
              </div>
              <div className="table-summary-card">
                <span className="table-summary-label">Completed rides</span>
                <strong className="table-summary-value">{dashboard.stats.completed}</strong>
              </div>
            </div>
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
                    <td className="route-text">{ride.pickup_location} to {ride.destination_location}</td>
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
