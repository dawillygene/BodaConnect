export function Panel({ children, className = '' }) {
  return <section className={`panel ${className}`.trim()}>{children}</section>;
}
