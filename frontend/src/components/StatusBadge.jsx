import { titleizeStatus } from '../lib/formatters';

export function StatusBadge({ status }) {
  return (
    <span className={`status-badge status-${status?.toLowerCase().replaceAll(' ', '-')}`}>
      {titleizeStatus(status)}
    </span>
  );
}
