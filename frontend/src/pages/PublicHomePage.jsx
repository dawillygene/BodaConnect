import { Link } from 'react-router-dom';
import { ThemeToggle } from '../components/ThemeToggle';

export function PublicHomePage() {
  return (
    <main className="marketing-shell">
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
      <section className="hero-panel">
        <div className="hero-copy">
          <span className="eyebrow">Urban ride operations</span>
          <h1>Run bookings, dispatch, and trip progress from one calm workspace.</h1>
          <p>
            BodaConnect brings customers, riders, and operations teams into the
            same live system so every ride moves from request to completion with
            less friction.
          </p>
          <div className="hero-actions">
            <Link className="button" to="/login">
              Sign in
            </Link>
            <Link className="button button-secondary" to="/register">
              Create account
            </Link>
          </div>
          <div className="hero-highlights">
            <div className="hero-highlight">
              <span aria-hidden="true" className="hero-highlight-dot" />
              <div>
                <strong>Faster assignment</strong>
                <p className="supporting-copy">Dispatch pending rides to available riders with fewer handoffs.</p>
              </div>
            </div>
            <div className="hero-highlight">
              <span aria-hidden="true" className="hero-highlight-dot" />
              <div>
                <strong>Live trip visibility</strong>
                <p className="supporting-copy">Track what is requested, active, completed, or cancelled in one place.</p>
              </div>
            </div>
          </div>
        </div>

        <div className="hero-grid hero-sidebar">
          <article className="metric-card">
            <span>Customer journey</span>
            <strong>Request rides, review trip history, and manage pending bookings.</strong>
          </article>
          <article className="metric-card">
            <span>Operations workflow</span>
            <strong>Review demand, manage rider accounts, and assign new trips quickly.</strong>
          </article>
          <article className="metric-card">
            <span>Rider execution</span>
            <strong>Accept, start, and complete assigned work without leaving the dashboard.</strong>
          </article>
          <dl className="detail-list">
            <div>
              <dt>Availability</dt>
              <dd>Built for light and dark mode</dd>
            </div>
            <div>
              <dt>Coverage</dt>
              <dd>Admin, customer, and rider views</dd>
            </div>
            <div>
              <dt>Design</dt>
              <dd>Brand-aligned palette with clear hierarchy</dd>
            </div>
            <div>
              <dt>Focus</dt>
              <dd>Production-ready language and cleaner screens</dd>
            </div>
          </dl>
        </div>
      </section>
    </main>
  );
}
