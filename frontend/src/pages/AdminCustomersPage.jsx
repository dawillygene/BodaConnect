import { useEffect, useState } from 'react';
import { useAuth } from '../auth/useAuth';
import { EmptyState } from '../components/EmptyState';
import { LoadingScreen } from '../components/LoadingScreen';
import { PageHeader } from '../components/PageHeader';
import { Panel } from '../components/Panel';
import { api } from '../lib/api';
import { formatDate } from '../lib/formatters';

export function AdminCustomersPage() {
  const { token } = useAuth();
  const [payload, setPayload] = useState(null);

  useEffect(() => {
    api.get('/admin/customers', token).then((response) => setPayload(response));
  }, [token]);

  if (!payload) {
    return <LoadingScreen label="Loading customers" />;
  }

  return (
    <div className="page-stack">
      <PageHeader
        description="Customer records exposed by the Laravel API."
        eyebrow="Admin"
        title="Customers"
      />

      <Panel>
        {payload.data.length === 0 ? (
          <EmptyState title="No customers found" description="Customer accounts will appear here." />
        ) : (
          <div className="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Status</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                {payload.data.map((customer) => (
                  <tr key={customer.id}>
                    <td>{customer.name}</td>
                    <td>{customer.email}</td>
                    <td>{customer.phone}</td>
                    <td>{customer.status}</td>
                    <td>{formatDate(customer.created_at)}</td>
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
