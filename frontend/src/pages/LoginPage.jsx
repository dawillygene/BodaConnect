import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/useAuth';
import { FormField } from '../components/FormField';

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
      <form className="auth-card" onSubmit={handleSubmit}>
        <span className="eyebrow">Welcome back</span>
        <h1>Sign in to BodaConnect</h1>
        <p>Use your existing account to access the API-driven frontend.</p>
        <FormField
          label="Email"
          name="email"
          onChange={handleChange}
          placeholder="name@example.com"
          type="email"
          value={form.email}
        />
        <FormField
          label="Password"
          name="password"
          onChange={handleChange}
          placeholder="Your password"
          type="password"
          value={form.password}
        />
        {error ? <p className="form-error">{error}</p> : null}
        <button className="button" disabled={isSubmitting} type="submit">
          {isSubmitting ? 'Signing in...' : 'Login'}
        </button>
        <p className="form-footnote">
          Need an account? <Link to="/register">Register</Link>
        </p>
      </form>
    </main>
  );
}
