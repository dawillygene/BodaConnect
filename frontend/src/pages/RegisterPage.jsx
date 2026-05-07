import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/useAuth';
import { FormField } from '../components/FormField';
import { ThemeToggle } from '../components/ThemeToggle';

const initialForm = {
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
};

export function RegisterPage() {
  const { register } = useAuth();
  const navigate = useNavigate();
  const [form, setForm] = useState(initialForm);
  const [error, setError] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  function handleChange(event) {
    setForm((current) => ({ ...current, [event.target.name]: event.target.value }));
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setError('');
    setIsSubmitting(true);

    try {
      await register(form);
      navigate('/customer/dashboard');
    } catch (submissionError) {
      setError(submissionError.message);
    } finally {
      setIsSubmitting(false);
    }
  }

  return (
    <main className="auth-shell">
      <div className="content-card">
        <div className="topbar">
          <span className="brand-mark">
            <span aria-hidden="true" className="brand-mark-dot" />
            BodaConnect
          </span>
          <div className="topbar-actions">
            <ThemeToggle />
          </div>
        </div>
      </div>
      <form className="auth-card auth-card-wide" onSubmit={handleSubmit}>
        <section className="auth-side-panel">
          <span className="eyebrow">New customer</span>
          <div className="auth-copy">
            <h1>Create a booking account in minutes.</h1>
            <p>
              Start requesting rides, reviewing your trip history, and managing
              pending bookings from one account.
            </p>
          </div>
          <div className="supporting-pill-row">
            <span className="supporting-pill">Fast onboarding</span>
            <span className="supporting-pill">Ride requests</span>
            <span className="supporting-pill">Trip history</span>
          </div>
        </section>

        <section className="auth-form">
          <div>
            <span className="eyebrow">Registration</span>
            <h2>Set up your profile</h2>
          </div>
          <div className="auth-grid">
            <FormField
              autoComplete="name"
              label="Full name"
              name="name"
              onChange={handleChange}
              value={form.name}
            />
            <FormField
              autoComplete="tel"
              label="Phone number"
              name="phone"
              onChange={handleChange}
              value={form.phone}
            />
            <FormField
              autoComplete="email"
              label="Email address"
              name="email"
              onChange={handleChange}
              type="email"
              value={form.email}
            />
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
          </div>
          {error ? <p className="form-error">{error}</p> : null}
          <div className="auth-actions">
            <button className="button" disabled={isSubmitting} type="submit">
              {isSubmitting ? 'Creating account...' : 'Create account'}
            </button>
            <Link className="button button-secondary" to="/login">
              Back to sign in
            </Link>
          </div>
          <p className="form-footnote">
            Already registered? <Link to="/login">Sign in</Link>.
          </p>
        </section>
      </form>
    </main>
  );
}
