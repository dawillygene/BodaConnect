import { Link } from 'react-router-dom';
import { ThemeToggle } from '../components/ThemeToggle';

const stats = [
  { value: '3', label: 'User roles', sub: 'Admin · Customer · Rider' },
  { value: '🗺', label: 'Live maps', sub: 'Real-time route preview' },
  { value: '⚡', label: 'MQTT live', sub: 'Instant status updates' },
];

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

          {/* Quick stats row */}
          <div className="hero-stats-row">
            {stats.map((s) => (
              <div key={s.label} className="hero-stat">
                <strong className="hero-stat-value">{s.value}</strong>
                <span className="hero-stat-label">{s.label}</span>
                <span className="hero-stat-sub">{s.sub}</span>
              </div>
            ))}
          </div>

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
                <p className="supporting-copy">Track every ride on an interactive map — request, active, or completed.</p>
              </div>
            </div>
            <div className="hero-highlight">
              <span aria-hidden="true" className="hero-highlight-dot" />
              <div>
                <strong>MQTT real-time updates</strong>
                <p className="supporting-copy">Status changes pushed instantly to every connected dashboard.</p>
              </div>
            </div>
          </div>
        </div>

        <div className="hero-grid hero-sidebar">
          <article className="metric-card hero-map-card">
            <div className="hero-map-placeholder" aria-hidden="true">
              <div className="hero-map-pin hero-map-pin-a" />
              <div className="hero-map-line" />
              <div className="hero-map-pin hero-map-pin-b" />
              <div className="hero-map-grid" />
            </div>
            <span>Interactive maps</span>
            <strong>Preview pickup and destination on a live OpenStreetMap before confirming.</strong>
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
              <dt>Maps</dt>
              <dd>OpenStreetMap — no API key needed</dd>
            </div>
            <div>
              <dt>Streaming</dt>
              <dd>MQTT via Eclipse Mosquitto</dd>
            </div>
          </dl>
        </div>
      </section>
    </main>
  );
}
