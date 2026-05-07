export function formatDate(value) {
  if (!value) {
    return 'N/A';
  }

  return new Intl.DateTimeFormat('en', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value));
}

export function titleizeStatus(status) {
  if (!status) {
    return 'Unknown';
  }

  return String(status)
    .replaceAll('_', ' ')
    .split(' ')
    .filter(Boolean)
    .map((segment) => segment.charAt(0).toUpperCase() + segment.slice(1).toLowerCase())
    .join(' ');
}
