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

export function RiderDashboardPage() {
  const { token } = useAuth();
  const [dashboard, setDashboard] = useState(null);

  async function loadDashboard() {
    const response = await api.get('/dashboard', token);
    setDashboard(response.dashboard);
  }

  useEffect(() => {
    let ignore = false;

    async function bootstrap() {
      const response = await api.get('/dashboard', token);

      if (!ignore) {
        setDashboard(response.dashboard);
      }
    }

    bootstrap();

    return () => {
      ignore = true;
    };
  }, [token]);

  async function advanceRide(ride) {
    const action =
      ride.status === 'Assigned'
        ? 'accept'
        : ride.status === 'Accepted'
          ? 'start'
          : 'complete';

    await api.post(`/rider/rides/${ride.id}/${action}`, {}, token);
    await loadDashboard();
  }

  if (!dashboard) {
    return <LoadingScreen label="Loading rider dashboard" />;
  }

  return (
    <div className="page-stack">
      <PageHeader
        description="Handle assigned work, move trips forward, and keep completion records organized."
        eyebrow="Rider"
        meta="This dashboard is built for active delivery work with clear next actions."
        title="Rider dashboard"
      />

      <div className="stats-grid">
        <StatCard caption="Trips currently waiting on rider action." label="Assigned" value={dashboard.stats.assigned} />
        <StatCard caption="Trips fully delivered and closed out." label="Completed" value={dashboard.stats.completed} />
        <StatCard caption="All rides handled by this rider profile." label="Total rides" value={dashboard.stats.total_rides} />
      </div>

      <Panel
        description="Trips that still need to be accepted, started, or completed."
        title="Active trips"
      >
        {dashboard.assigned_trips.length === 0 ? (
          <EmptyState title="No active trips" description="Assigned rides will appear here." />
        ) : (
          <>
            <div className="table-summary-grid">
              <div className="table-summary-card">
                <span className="table-summary-label">Active trips</span>
                <strong className="table-summary-value">{dashboard.assigned_trips.length}</strong>
              </div>
              <div className="table-summary-card">
                <span className="table-summary-label">Completed trips</span>
                <strong className="table-summary-value">{dashboard.stats.completed}</strong>
              </div>
            </div>
            <div className="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer</th>
                  <th>Route</th>
                  <th>Status</th>
                  <th />
                </tr>
              </thead>
              <tbody>
                {dashboard.assigned_trips.map((ride) => (
                  <tr key={ride.id}>
                    <td>#{ride.id}</td>
                    <td>{ride.customer?.name}</td>
                    <td className="route-text">{ride.pickup_location} to {ride.destination_location}</td>
                    <td><StatusBadge status={ride.status} /></td>
                    <td>
                      <button className="button button-small" onClick={() => advanceRide(ride)} type="button">
                        {ride.status === 'Assigned'
                          ? 'Accept'
                          : ride.status === 'Accepted'
                            ? 'Start'
                            : 'Complete'}
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          </>
        )}
      </Panel>

      <Panel
        description="Closed trips for quick review and reconciliation."
        title="Completed trips"
      >
        {dashboard.completed_trips.length === 0 ? (
          <EmptyState title="No completed rides" description="Completed rides will appear here." />
        ) : (
          <div className="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer</th>
                  <th>Completed</th>
                </tr>
              </thead>
              <tbody>
                {dashboard.completed_trips.map((ride) => (
                  <tr key={ride.id}>
                    <td>#{ride.id}</td>
                    <td>{ride.customer?.name}</td>
                    <td>{formatDate(ride.updated_at)}</td>
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
