import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/useAuth';
import { FormField } from '../components/FormField';
import { ThemeToggle } from '../components/ThemeToggle';

export function LoginPage() {
  const { login } = useAuth();
  const navigate = useNavigate();
  const [form, setForm] = useState({ email: '', password: '' });
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
      const user = await login(form);
      navigate(
        user.role === 'admin'
          ? '/admin/dashboard'
          : user.role === 'rider'
            ? '/rider/dashboard'
            : '/customer/dashboard',
      );
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
      <form className="auth-card" onSubmit={handleSubmit}>
        <section className="auth-side-panel">
          <span className="eyebrow">Welcome back</span>
          <div className="auth-copy">
            <h1>Stay close to every ride in motion.</h1>
            <p>
              Sign in to review bookings, move active trips forward, and keep
              your operations running smoothly.
            </p>
          </div>
          <div className="supporting-pill-row">
            <span className="supporting-pill">Bookings</span>
            <span className="supporting-pill">Dispatch</span>
            <span className="supporting-pill">Trip status</span>
          </div>
        </section>

        <section className="auth-form">
          <div>
            <span className="eyebrow">Sign in</span>
            <h2>Access your workspace</h2>
          </div>
          <FormField
            autoComplete="email"
            label="Email address"
            name="email"
            onChange={handleChange}
            placeholder="name@example.com"
            type="email"
            value={form.email}
          />
          <FormField
            autoComplete="current-password"
            label="Password"
            name="password"
            onChange={handleChange}
            placeholder="Your password"
            type="password"
            value={form.password}
          />
          {error ? <p className="form-error">{error}</p> : null}
          <div className="auth-actions">
            <button className="button" disabled={isSubmitting} type="submit">
              {isSubmitting ? 'Signing in...' : 'Sign in'}
            </button>
            <Link className="button button-secondary" to="/register">
              Create account
            </Link>
          </div>
          <p className="form-footnote">
            New here? <Link to="/register">Create a customer account</Link>.
          </p>
        </section>
      </form>
    </main>
  );
}
