import { useEffect, useState } from 'react';
import { useAuth } from '../auth/useAuth';
import { FormField } from '../components/FormField';
import { LoadingScreen } from '../components/LoadingScreen';
import { PageHeader } from '../components/PageHeader';
import { Panel } from '../components/Panel';
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

  if (!payload) {
    return <LoadingScreen label="Loading riders" />;
  }

  return (
    <div className="page-stack">
      <PageHeader
        description="Provision new riders and deactivate inactive ones."
        eyebrow="Admin"
        title="Riders"
      />

      <Panel>
        <h3>Create rider</h3>
        <form className="form-stack" onSubmit={createRider}>
          <div className="three-column-grid">
            <FormField label="Name" name="name" onChange={handleChange} value={form.name} />
            <FormField label="Email" name="email" onChange={handleChange} value={form.email} />
            <FormField label="Phone" name="phone" onChange={handleChange} value={form.phone} />
            <FormField
              label="Password"
              name="password"
              onChange={handleChange}
              type="password"
              value={form.password}
            />
            <FormField
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

      <Panel>
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
                  <td>{rider.status}</td>
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
                      <span className="muted">Inactive</span>
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
