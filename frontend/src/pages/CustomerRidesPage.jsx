import { useEffect, useEffectEvent, useState } from 'react';
import { useAuth } from '../auth/useAuth';
import { EmptyState } from '../components/EmptyState';
import { FormField } from '../components/FormField';
import { LoadingScreen } from '../components/LoadingScreen';
import { PageHeader } from '../components/PageHeader';
import { Panel } from '../components/Panel';
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

  if (!rides) {
    return <LoadingScreen label="Loading rides" />;
  }

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
          <label className="field">
            <span>Notes</span>
            <textarea
              className="input textarea"
              name="notes"
              onChange={handleChange}
              rows="4"
              value={form.notes}
            />
          </label>
          {error ? <p className="form-error">{error}</p> : null}
          <button className="button" disabled={isSubmitting} type="submit">
            {isSubmitting ? 'Submitting...' : 'Request ride'}
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
                <strong className="table-summary-value">
                  {rides.filter((ride) => ride.status === 'Pending').length}
                </strong>
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
                    <tr key={ride.id}>
                      <td>#{ride.id}</td>
                      <td className="route-text">
                        {ride.pickup_location} to {ride.destination_location}
                      </td>
                      <td>
                        <StatusBadge status={ride.status} />
                      </td>
                      <td>{ride.rider?.name ?? 'Unassigned'}</td>
                      <td>{formatDate(ride.created_at)}</td>
                      <td>
                        {ride.status === 'Pending' ? (
                          <button
                            className="button button-secondary button-small"
                            onClick={() => cancelRide(ride.id)}
                            type="button"
                          >
                            Cancel
                          </button>
                        ) : null}
                      </td>
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
