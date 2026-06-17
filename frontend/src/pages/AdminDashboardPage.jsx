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
import { subscribeToAdminRideStatusUpdates } from '../lib/status-stream';

export function AdminDashboardPage() {
  const { token } = useAuth();
  const [dashboard, setDashboard] = useState(null);
  const [selectedRide, setSelectedRide] = useState(null);

  const loadDashboard = useEffectEvent(async () => {
    const response = await api.get('/dashboard', token);
    setDashboard(response.dashboard);
  });

  useEffect(() => {
    loadDashboard();
  }, [loadDashboard, token]);

  useEffect(() => {
    return subscribeToAdminRideStatusUpdates(loadDashboard);
  }, [loadDashboard]);

  if (!dashboard) {
    return <LoadingScreen label="Loading admin dashboard" />;
  }

  const pendingRides = dashboard.recent_rides.filter((r) => r.status === 'Pending');
  const activeRide = dashboard.recent_rides.find(
    (r) => r.status === 'Assigned' || r.status === 'Accepted' || r.status === 'In Progress',
  ) ?? null;

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

      {/* Operations map — shows the most recent active or pending ride */}
      {(activeRide ?? pendingRides[0]) && (
        <Panel
          description="Live route preview for the most recent active or pending ride."
          title="Operations map"
        >
          <div className="ops-map-header">
            <div className="ops-ride-meta">
              <span className="ops-ride-id">Ride #{(activeRide ?? pendingRides[0]).id}</span>
              <StatusBadge status={(activeRide ?? pendingRides[0]).status} />
            </div>
            <div className="ops-ride-route">
              <span className="route-from">{(activeRide ?? pendingRides[0]).pickup_location}</span>
              <span className="route-arrow">→</span>
              <span className="route-to">{(activeRide ?? pendingRides[0]).destination_location}</span>
            </div>
          </div>
          <RideMap
            pickupLocation={(activeRide ?? pendingRides[0]).pickup_location}
            destinationLocation={(activeRide ?? pendingRides[0]).destination_location}
            height="320px"
          />
          <p className="map-info-note">
            Click any ride row below to view its route on the map.
          </p>
        </Panel>
      )}

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
              <div className="table-summary-card">
                <span className="table-summary-label">Active riders</span>
                <strong className="table-summary-value">{dashboard.stats.riders}</strong>
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
                    <th>Created</th>
                    <th />
                  </tr>
                </thead>
                <tbody>
                  {dashboard.recent_rides.map((ride) => (
                    <>
                      <tr
                        key={ride.id}
                        className={selectedRide?.id === ride.id ? 'row-selected' : ''}
                        style={{ cursor: 'pointer' }}
                        onClick={() => setSelectedRide(selectedRide?.id === ride.id ? null : ride)}
                      >
                        <td>#{ride.id}</td>
                        <td>{ride.customer?.name ?? 'Unknown'}</td>
                        <td className="route-text">
                          {ride.pickup_location ? (
                            <>
                              <span className="route-from">{ride.pickup_location}</span>
                              <span className="route-arrow">→</span>
                              <span className="route-to">{ride.destination_location}</span>
                            </>
                          ) : (
                            <span className="muted">—</span>
                          )}
                        </td>
                        <td>
                          <StatusBadge status={ride.status} />
                        </td>
                        <td>{formatDate(ride.created_at)}</td>
                        <td>
                          <button
                            className="button button-ghost button-small map-btn"
                            onClick={(e) => {
                              e.stopPropagation();
                              setSelectedRide(selectedRide?.id === ride.id ? null : ride);
                            }}
                            type="button"
                            title="View on map"
                          >
                            📍
                          </button>
                        </td>
                      </tr>
                      {selectedRide?.id === ride.id && ride.pickup_location && (
                        <tr key={`map-${ride.id}`} className="map-expand-row">
                          <td colSpan={6} className="map-expand-cell">
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
                                height="260px"
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
    </div>
  );
}
