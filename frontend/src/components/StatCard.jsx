export function StatCard({ label, value, caption }) {
  return (
    <article className="stat-card">
      <span>{label}</span>
      <strong>{value}</strong>
      {caption ? <p className="kpi-caption">{caption}</p> : null}
    </article>
  );
}
