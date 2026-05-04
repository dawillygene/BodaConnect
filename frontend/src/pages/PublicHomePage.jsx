import { Link } from 'react-router-dom';

export function PublicHomePage() {
  return (
    <main className="marketing-shell">
      <section className="hero-panel">
        <div>
          <span className="eyebrow">React + Laravel API</span>
          <h1>Dispatch, assign, and track boda rides from one frontend.</h1>
          <p>
            The frontend now lives in a standalone React app and the Laravel
            project acts as the API and business layer.
          </p>
          <div className="hero-actions">
            <Link className="button" to="/login">
              Login
            </Link>
            <Link className="button button-secondary" to="/register">
              Create customer account
            </Link>
          </div>
        </div>

        <div className="hero-grid">
          <article className="hero-metric">
            <span>Customer flow</span>
            <strong>Create and cancel ride requests</strong>
          </article>
          <article className="hero-metric">
            <span>Admin flow</span>
            <strong>Manage riders and assign pending trips</strong>
          </article>
          <article className="hero-metric">
            <span>Rider flow</span>
            <strong>Accept, start, and complete assigned rides</strong>
          </article>
        </div>
      </section>
    </main>
  );
}
