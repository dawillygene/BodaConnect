import { useEffect, useState } from 'react';
import { useAuth } from '../auth/useAuth';
import { FormField } from '../components/FormField';
import { LoadingScreen } from '../components/LoadingScreen';
import { PageHeader } from '../components/PageHeader';
import { Panel } from '../components/Panel';
import { StatusBadge } from '../components/StatusBadge';
import { api } from '../lib/api';

const initialForm = {
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  status: 'active',
};

export function AdminRidersPage() {
  const { token } = useAuth();
  const [payload, setPayload] = useState(null);
  const [form, setForm] = useState(initialForm);

  async function loadRiders() {
    const response = await api.get('/admin/riders', token);
    setPayload(response);
  }

  useEffect(() => {
    let ignore = false;

    async function bootstrap() {
      const response = await api.get('/admin/riders', token);

      if (!ignore) {
        setPayload(response);
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

  async function createRider(event) {
    event.preventDefault();
    await api.post('/admin/riders', form, token);
    setForm(initialForm);
    await loadRiders();
  }

  async function deactivateRider(userId) {
    await api.delete(`/admin/users/${userId}`, token);
    await loadRiders();
  }

  async function updateRiderStatus(rider, status) {
    await api.put(
      `/admin/riders/${rider.id}`,
      {
        name: rider.name,
        email: rider.email,
        phone: rider.phone,
        password: '',
        password_confirmation: '',
        status,
      },
      token,
    );

    await loadRiders();
  }

  if (!payload) {
    return <LoadingScreen label="Loading riders" />;
  }

  return (
    <div className="page-stack">
      <PageHeader
        description="Create rider accounts, review the current fleet, and retire inactive access."
        eyebrow="Admin"
        meta="This workspace keeps onboarding and fleet maintenance in one operational view."
        title="Rider management"
      />

      <Panel
        description="Add a new rider account with contact details and an initial status."
        title="Create rider account"
      >
        <form className="form-stack" onSubmit={createRider}>
          <div className="three-column-grid">
            <FormField autoComplete="name" label="Name" name="name" onChange={handleChange} value={form.name} />
            <FormField autoComplete="email" label="Email" name="email" onChange={handleChange} value={form.email} />
            <FormField autoComplete="tel" label="Phone" name="phone" onChange={handleChange} value={form.phone} />
            <FormField
              autoComplete="new-password"
              label="Password"
              name="password"
              onChange={handleChange}
              type="password"
              value={form.password}
            />
            <FormField
              autoComplete="new-password"
              label="Confirm password"
              name="password_confirmation"
              onChange={handleChange}
              type="password"
              value={form.password_confirmation}
            />
            <label className="field">
              <span>Status</span>
              <select className="select" name="status" onChange={handleChange} value={form.status}>
                <option value="active">active</option>
                <option value="inactive">inactive</option>
              </select>
            </label>
          </div>
          <button className="button" type="submit">Create rider</button>
        </form>
      </Panel>

      <Panel
        description="Current rider accounts and their operating status."
        title="Fleet roster"
      >
        <div className="table-summary-grid">
          <div className="table-summary-card">
            <span className="table-summary-label">Total riders</span>
            <strong className="table-summary-value">{payload.data.length}</strong>
          </div>
          <div className="table-summary-card">
            <span className="table-summary-label">Active riders</span>
            <strong className="table-summary-value">
              {payload.data.filter((rider) => rider.status === 'active').length}
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
                <th />
              </tr>
            </thead>
            <tbody>
              {payload.data.map((rider) => (
                <tr key={rider.id}>
                  <td>{rider.name}</td>
                  <td>{rider.email}</td>
                  <td>{rider.phone}</td>
                  <td><StatusBadge status={rider.status} /></td>
                  <td>
                    {rider.status === 'active' ? (
                      <button
                        className="button button-secondary button-small"
                        onClick={() => deactivateRider(rider.id)}
                        type="button"
                      >
                        Deactivate
                      </button>
                    ) : (
                      <button
                        className="button button-small"
                        onClick={() => updateRiderStatus(rider, 'active')}
                        type="button"
                      >
                        Activate
                      </button>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </Panel>
    </div>
  );
}
