import { useEffect, useState } from 'react';
import { useAuth } from '../auth/useAuth';
import { EmptyState } from '../components/EmptyState';
import { LoadingScreen } from '../components/LoadingScreen';
import { PageHeader } from '../components/PageHeader';
import { Panel } from '../components/Panel';
import { StatusBadge } from '../components/StatusBadge';
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
        description="Review customer profiles, contact details, and account status."
        eyebrow="Admin"
        meta="Keep the customer base visible to operations when resolving support or dispatch issues."
        title="Customer directory"
      />

      <Panel
        description="All registered customer accounts."
        title="Customer records"
      >
        {payload.data.length === 0 ? (
          <EmptyState title="No customers found" description="Customer accounts will appear here." />
        ) : (
          <>
            <div className="table-summary-grid">
              <div className="table-summary-card">
                <span className="table-summary-label">Total customers</span>
                <strong className="table-summary-value">{payload.data.length}</strong>
              </div>
              <div className="table-summary-card">
                <span className="table-summary-label">Active records</span>
                <strong className="table-summary-value">
                  {payload.data.filter((customer) => customer.status === 'active').length}
                </strong>
              </div>
            </div>
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
                    <td><StatusBadge status={customer.status} /></td>
                    <td>{formatDate(customer.created_at)}</td>
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
