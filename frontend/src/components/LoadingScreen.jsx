export function LoadingScreen({ label }) {
  return (
    <div className="loading-screen">
      <div className="loading-card">
        <div className="spinner" />
        <h2 className="section-title">Preparing your workspace</h2>
        <p className="loading-copy">{label}</p>
      </div>
    </div>
  );
}
