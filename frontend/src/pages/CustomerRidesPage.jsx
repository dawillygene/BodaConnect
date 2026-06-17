import { useEffect, useEffectEvent, useState } from 'react';
import { useAuth } from '../auth/useAuth';
import { EmptyState } from '../components/EmptyState';
import { FormField } from '../components/FormField';
import { LoadingScreen } from '../components/LoadingScreen';
import { PageHeader } from '../components/PageHeader';
import { Panel } from '../components/Panel';
import { RideMap } from '../components/RideMap';
import { StatusBadge } from '../components/StatusBadge';
import { api } from '../lib/api';
import { formatDate } from '../lib/formatters';
import { subscribeToRideStatusUpdates } from '../lib/status-stream';

const initialForm = {
  pickup_location: '',
  destination_location: '',
  notes: '',
};

export function CustomerRidesPage() {
  const { token, user } = useAuth();
  const [rides, setRides] = useState(null);
  const [form, setForm] = useState(initialForm);
  const [error, setError] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [selectedRide, setSelectedRide] = useState(null);
  const [showMapPreview, setShowMapPreview] = useState(false);

  async function loadRides() {
    const response = await api.get('/rides', token);
    setRides(response.rides);
  }

  const handleRideStatusUpdate = useEffectEvent((statusUpdate) => {
    setRides((currentRides) => {
      if (!currentRides) {
        return currentRides;
      }

      const existingRide = currentRides.find((ride) => ride.id === statusUpdate.ride_id);

      if (!existingRide) {
        const newRide = {
          id: statusUpdate.ride_id,
          customer_id: statusUpdate.customer_id,
          rider_id: statusUpdate.rider_id,
          rider: statusUpdate.rider,
          status: statusUpdate.status,
          pickup_location: statusUpdate.pickup_location,
          destination_location: statusUpdate.destination_location,
          created_at: statusUpdate.created_at ?? statusUpdate.updated_at,
          updated_at: statusUpdate.updated_at,
        };

        return [newRide, ...currentRides];
      }

      return currentRides.map((ride) =>
        ride.id === statusUpdate.ride_id
          ? {
              ...ride,
              status: statusUpdate.status,
              rider_id: statusUpdate.rider_id,
              rider: statusUpdate.rider ?? ride.rider,
              updated_at: statusUpdate.updated_at,
            }
          : ride,
      );
    });
  });

  useEffect(() => {
    let ignore = false;

    async function bootstrap() {
      const response = await api.get('/rides', token);

      if (!ignore) {
        setRides(response.rides);
      }
    }

    bootstrap();

    return () => {
      ignore = true;
    };
  }, [token]);

  useEffect(() => {
    return subscribeToRideStatusUpdates(user?.id, handleRideStatusUpdate);
  }, [handleRideStatusUpdate, user?.id]);

  function handleChange(event) {
    setForm((current) => ({ ...current, [event.target.name]: event.target.value }));
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setError('');
    setIsSubmitting(true);

    try {
      await api.post('/rides', form, token);
      setForm(initialForm);
      setShowMapPreview(false);
      await loadRides();
    } catch (submissionError) {
      setError(submissionError.message);
    } finally {
      setIsSubmitting(false);
    }
  }

  async function cancelRide(rideId) {
    await api.post(`/rides/${rideId}/cancel`, {}, token);
    await loadRides();
  }

  const hasLocations = form.pickup_location.trim().length > 2 || form.destination_location.trim().length > 2;

  if (!rides) {
    return <LoadingScreen label="Loading rides" />;
  }

  const pendingCount = rides.filter((r) => r.status === 'Pending').length;
  const completedCount = rides.filter((r) => r.status === 'Completed').length;

  return (
    <div className="page-stack">
      <PageHeader
        description="Create a new ride request and review your full trip history."
        eyebrow="Customer"
        meta="Pending rides can be cancelled here before they move into active delivery."
        title="My rides"
      />

      <Panel
        description="Enter the trip details below and send the request to dispatch."
        title="Request a ride"
      >
        <form className="form-stack" onSubmit={handleSubmit}>
          <div className="two-column-grid">
            <FormField
              label="Pickup location"
              name="pickup_location"
              onChange={handleChange}
              value={form.pickup_location}
            />
            <FormField
              label="Destination"
              name="destination_location"
              onChange={handleChange}
              value={form.destination_location}
            />
          </div>

          {/* Map preview toggle */}
          {hasLocations && (
            <div className="map-preview-toggle-row">
              <button
                className="button button-ghost button-small"
                onClick={() => setShowMapPreview((v) => !v)}
                type="button"
              >
                <span className="map-pin-icon" aria-hidden="true">📍</span>
                {showMapPreview ? 'Hide map preview' : 'Preview route on map'}
              </button>
            </div>
          )}

          {showMapPreview && hasLocations && (
            <div className="ride-map-panel">
              <div className="ride-map-legend">
                <span className="map-legend-item map-legend-pickup">
                  <span className="map-legend-dot" />
                  Pickup
                </span>
                <span className="map-legend-item map-legend-dest">
                  <span className="map-legend-dot" />
                  Destination
                </span>
                <span className="map-legend-note">Powered by OpenStreetMap</span>
              </div>
              <RideMap
                pickupLocation={form.pickup_location}
                destinationLocation={form.destination_location}
                height="300px"
              />
            </div>
          )}

          <label className="field">
            <span>Notes</span>
            <textarea
              className="input textarea"
              name="notes"
              onChange={handleChange}
              rows="3"
              value={form.notes}
            />
          </label>
          {error ? <p className="form-error">{error}</p> : null}
          <button className="button" disabled={isSubmitting} type="submit">
            {isSubmitting ? 'Submitting…' : 'Request ride'}
          </button>
        </form>
      </Panel>

      <Panel
        description="Every ride tied to your account, including rider assignment and status."
        title="Ride history"
      >
        {rides.length === 0 ? (
          <EmptyState title="No rides found" description="Your ride history will appear here." />
        ) : (
          <>
            <div className="table-summary-grid">
              <div className="table-summary-card">
                <span className="table-summary-label">Total records</span>
                <strong className="table-summary-value">{rides.length}</strong>
              </div>
              <div className="table-summary-card">
                <span className="table-summary-label">Pending rides</span>
                <strong className="table-summary-value">{pendingCount}</strong>
              </div>
              <div className="table-summary-card">
                <span className="table-summary-label">Completed</span>
                <strong className="table-summary-value">{completedCount}</strong>
              </div>
            </div>
            <div className="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Route</th>
                    <th>Status</th>
                    <th>Rider</th>
                    <th>Created</th>
                    <th />
                  </tr>
                </thead>
                <tbody>
                  {rides.map((ride) => (
                    <>
                      <tr
                        key={ride.id}
                        className={selectedRide?.id === ride.id ? 'row-selected' : ''}
                        onClick={() => setSelectedRide(selectedRide?.id === ride.id ? null : ride)}
                        style={{ cursor: 'pointer' }}
                      >
                        <td>#{ride.id}</td>
                        <td className="route-text">
                          <span className="route-from">{ride.pickup_location}</span>
                          <span className="route-arrow">→</span>
                          <span className="route-to">{ride.destination_location}</span>
                        </td>
                        <td>
                          <StatusBadge status={ride.status} />
                        </td>
                        <td>{ride.rider?.name ?? <span className="muted">Unassigned</span>}</td>
                        <td>{formatDate(ride.created_at)}</td>
                        <td>
                          <div className="row-actions">
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
                            {ride.status === 'Pending' ? (
                              <button
                                className="button button-secondary button-small"
                                onClick={(e) => {
                                  e.stopPropagation();
                                  cancelRide(ride.id);
                                }}
                                type="button"
                              >
                                Cancel
                              </button>
                            ) : null}
                          </div>
                        </td>
                      </tr>
                      {selectedRide?.id === ride.id && (
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
