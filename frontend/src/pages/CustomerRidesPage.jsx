import { useEffect, useState } from 'react';
import { useAuth } from '../auth/useAuth';
import { EmptyState } from '../components/EmptyState';
import { FormField } from '../components/FormField';
import { LoadingScreen } from '../components/LoadingScreen';
import { PageHeader } from '../components/PageHeader';
import { Panel } from '../components/Panel';
import { StatusBadge } from '../components/StatusBadge';
import { api } from '../lib/api';
import { formatDate } from '../lib/formatters';

const initialForm = {
  pickup_location: '',
  destination_location: '',
  notes: '',
};

export function CustomerRidesPage() {
  const { token } = useAuth();
  const [rides, setRides] = useState(null);
  const [form, setForm] = useState(initialForm);
  const [error, setError] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  async function loadRides() {
    const response = await api.get('/rides', token);
    setRides(response.rides);
  }

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
        description="Create new requests and manage pending ones."
        eyebrow="Customer"
        title="My rides"
      />

      <Panel>
        <h3>Create a ride request</h3>
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

      <Panel>
        <h3>Ride history</h3>
        {rides.length === 0 ? (
          <EmptyState title="No rides found" description="Your ride history will appear here." />
        ) : (
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
                    <td>{ride.pickup_location} to {ride.destination_location}</td>
                    <td><StatusBadge status={ride.status} /></td>
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
        )}
      </Panel>
    </div>
  );
}
