import { useEffect, useState } from 'react';
import { useAuth } from '../auth/useAuth';
import { EmptyState } from '../components/EmptyState';
import { LoadingScreen } from '../components/LoadingScreen';
import { PageHeader } from '../components/PageHeader';
import { Panel } from '../components/Panel';
import { StatusBadge } from '../components/StatusBadge';
import { api } from '../lib/api';
import { formatDate } from '../lib/formatters';

export function AdminRidesPage() {
  const { token } = useAuth();
  const [payload, setPayload] = useState(null);

  async function loadRides() {
    const response = await api.get('/admin/rides', token);
    setPayload(response);
  }

  useEffect(() => {
    let ignore = false;

    async function bootstrap() {
      const response = await api.get('/admin/rides', token);

      if (!ignore) {
        setPayload(response);
      }
    }

    bootstrap();

    return () => {
      ignore = true;
    };
  }, [token]);

  async function assignRide(rideId, riderId) {
    if (!riderId) {
      return;
    }

    await api.post(`/admin/rides/${rideId}/assign`, { rider_id: Number(riderId) }, token);
    await loadRides();
  }

  if (!payload) {
    return <LoadingScreen label="Loading rides" />;
  }

  return (
    <div className="page-stack">
      <PageHeader
        description="Assign pending rides and inspect the dispatch queue."
        eyebrow="Admin"
        title="Ride management"
      />

      <Panel>
        {payload.rides.length === 0 ? (
          <EmptyState title="No rides found" description="Ride requests will appear here." />
        ) : (
          <div className="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer</th>
                  <th>Route</th>
                  <th>Status</th>
                  <th>Rider</th>
                  <th>Created</th>
                  <th>Assign</th>
                </tr>
              </thead>
              <tbody>
                {payload.rides.map((ride) => (
                  <tr key={ride.id}>
                    <td>#{ride.id}</td>
                    <td>{ride.customer?.name ?? 'Unknown'}</td>
                    <td>{ride.pickup_location} to {ride.destination_location}</td>
                    <td><StatusBadge status={ride.status} /></td>
                    <td>{ride.rider?.name ?? 'Unassigned'}</td>
                    <td>{formatDate(ride.created_at)}</td>
                    <td>
                      {ride.status === 'Pending' ? (
                        <select
                          className="select"
                          defaultValue=""
                          onChange={(event) => assignRide(ride.id, event.target.value)}
                        >
                          <option value="">Select rider</option>
                          {payload.available_riders.map((rider) => (
                            <option key={rider.id} value={rider.id}>
                              {rider.name}
                            </option>
                          ))}
                        </select>
                      ) : (
                        <span className="muted">Locked</span>
                      )}
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
