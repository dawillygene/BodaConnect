import { useEffect, useEffectEvent, useState } from 'react';
import { useAuth } from '../auth/useAuth';
import { EmptyState } from '../components/EmptyState';
import { LoadingScreen } from '../components/LoadingScreen';
import { PageHeader } from '../components/PageHeader';
import { Panel } from '../components/Panel';
import { RideMap } from '../components/RideMap';
import { StatCard } from '../components/StatCard';
import { StatusBadge } from '../components/StatusBadge';
import { api } from '../lib/api';
import { formatDate } from '../lib/formatters';
import { subscribeToRiderRideStatusUpdates } from '../lib/status-stream';

export function RiderDashboardPage() {
  const { token, user } = useAuth();
  const [dashboard, setDashboard] = useState(null);
  const [activeMapRide, setActiveMapRide] = useState(null);

  const loadDashboard = useEffectEvent(async () => {
    const response = await api.get('/dashboard', token);
    setDashboard(response.dashboard);
  });

  useEffect(() => {
    loadDashboard();
  }, [loadDashboard, token]);

  useEffect(() => {
    return subscribeToRiderRideStatusUpdates(user?.id, loadDashboard);
  }, [loadDashboard, user?.id]);

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

  const activeTrip = dashboard.assigned_trips.find(
    (t) => t.status === 'Accepted' || t.status === 'In Progress',
  ) ?? dashboard.assigned_trips[0] ?? null;

  return (
    <div className="page-stack">
      <PageHeader
        description="Handle assigned work, move trips forward, and keep completion records organised."
        eyebrow="Rider"
        meta="This dashboard is built for active delivery work with clear next actions."
        title="Rider dashboard"
      />

      <div className="stats-grid">
        <StatCard caption="Trips currently waiting on rider action." label="Assigned" value={dashboard.stats.assigned} />
        <StatCard caption="Trips fully delivered and closed out." label="Completed" value={dashboard.stats.completed} />
        <StatCard caption="All rides handled by this rider profile." label="Total rides" value={dashboard.stats.total_rides} />
      </div>

      {/* Live map for the current active trip */}
      {activeTrip && (
        <Panel description={`Live map for ride #${activeTrip.id}`} title="Current trip map">
          <div className="current-trip-header">
            <div className="current-trip-info">
              <div className="current-trip-route">
                <span className="route-label pickup-label">📍 Pickup</span>
                <span className="route-place">{activeTrip.pickup_location}</span>
              </div>
              <div className="current-trip-arrow">↓</div>
              <div className="current-trip-route">
                <span className="route-label dest-label">🏁 Drop-off</span>
                <span className="route-place">{activeTrip.destination_location}</span>
              </div>
            </div>
            <div className="current-trip-meta">
              <StatusBadge status={activeTrip.status} />
              <button
                className="button button-small"
                onClick={() => advanceRide(activeTrip)}
                type="button"
              >
                {activeTrip.status === 'Assigned' ? 'Accept' : activeTrip.status === 'Accepted' ? 'Start' : 'Complete'}
              </button>
            </div>
          </div>
          <RideMap
            pickupLocation={activeTrip.pickup_location}
            destinationLocation={activeTrip.destination_location}
            height="340px"
          />
        </Panel>
      )}

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
                    <>
                      <tr
                        key={ride.id}
                        className={activeMapRide?.id === ride.id ? 'row-selected' : ''}
                      >
                        <td>#{ride.id}</td>
                        <td>{ride.customer?.name}</td>
                        <td className="route-text">
                          <span className="route-from">{ride.pickup_location}</span>
                          <span className="route-arrow">→</span>
                          <span className="route-to">{ride.destination_location}</span>
                        </td>
                        <td><StatusBadge status={ride.status} /></td>
                        <td>
                          <div className="row-actions">
                            <button
                              className="button button-ghost button-small map-btn"
                              onClick={() => setActiveMapRide(activeMapRide?.id === ride.id ? null : ride)}
                              type="button"
                              title="View on map"
                            >
                              📍
                            </button>
                            <button className="button button-small" onClick={() => advanceRide(ride)} type="button">
                              {ride.status === 'Assigned'
                                ? 'Accept'
                                : ride.status === 'Accepted'
                                  ? 'Start'
                                  : 'Complete'}
                            </button>
                          </div>
                        </td>
                      </tr>
                      {activeMapRide?.id === ride.id && (
                        <tr key={`map-${ride.id}`} className="map-expand-row">
                          <td colSpan={5} className="map-expand-cell">
                            <div className="ride-map-panel">
                              <div className="ride-map-legend">
                                <span className="map-legend-item map-legend-pickup">
                                  <span className="map-legend-dot" />
                                  {ride.pickup_location}
                                </span>
                                <span className="map-legend-item map-legend-dest">
                                  <span className="map-legend-dot" />
                                  {ride.destination_location}
                                </span>
                              </div>
                              <RideMap
                                pickupLocation={ride.pickup_location}
                                destinationLocation={ride.destination_location}
                                height="240px"
                              />
                            </div>
                          </td>
                        </tr>
                      )}
                    </>
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
                  <th>Route</th>
                  <th>Completed</th>
                </tr>
              </thead>
              <tbody>
                {dashboard.completed_trips.map((ride) => (
                  <tr key={ride.id}>
                    <td>#{ride.id}</td>
                    <td>{ride.customer?.name}</td>
                    <td className="route-text">
                      <span className="route-from">{ride.pickup_location}</span>
                      <span className="route-arrow">→</span>
                      <span className="route-to">{ride.destination_location}</span>
                    </td>
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
