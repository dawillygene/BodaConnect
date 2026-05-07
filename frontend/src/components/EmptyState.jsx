export function EmptyState({ title, description }) {
  return (
    <div className="empty-state">
      <div aria-hidden="true" className="empty-icon">
        0
      </div>
      <h3>{title}</h3>
      <p>{description}</p>
    </div>
  );
}
