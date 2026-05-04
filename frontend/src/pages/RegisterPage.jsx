import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/useAuth';
import { FormField } from '../components/FormField';

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
      <form className="auth-card auth-card-wide" onSubmit={handleSubmit}>
        <span className="eyebrow">New customer</span>
        <h1>Create your account</h1>
        <div className="two-column-grid">
          <FormField label="Full name" name="name" onChange={handleChange} value={form.name} />
          <FormField label="Phone" name="phone" onChange={handleChange} value={form.phone} />
          <FormField
            label="Email"
            name="email"
            onChange={handleChange}
            type="email"
            value={form.email}
          />
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
        </div>
        {error ? <p className="form-error">{error}</p> : null}
        <button className="button" disabled={isSubmitting} type="submit">
          {isSubmitting ? 'Creating account...' : 'Register'}
        </button>
        <p className="form-footnote">
          Already registered? <Link to="/login">Login</Link>
        </p>
      </form>
    </main>
  );
}
