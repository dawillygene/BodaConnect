const API_PREFIX = '/api';

class ApiError extends Error {
  constructor(message, status, errors = {}) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.errors = errors;
  }
}

async function request(path, { token, method = 'GET', body } = {}) {
  let response;

  try {
    response = await fetch(`${API_PREFIX}${path}`, {
      method,
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
      body: body ? JSON.stringify(body) : undefined,
    });
  } catch {
    throw new ApiError(
      'Cannot reach the Laravel API. Make sure `php artisan serve` is running on http://127.0.0.1:8000.',
      0,
    );
  }

  const payload = await response.json().catch(() => ({}));

  if (!response.ok) {
    const defaultMessage =
      response.status === 502
        ? 'Vite proxy cannot reach Laravel on http://127.0.0.1:8000. Start or restart `php artisan serve`.'
        : 'Request failed.';

    throw new ApiError(
      payload.message ?? defaultMessage,
      response.status,
      payload.errors ?? {},
    );
  }

  return payload;
}

export const api = {
  delete: (path, token) => request(path, { method: 'DELETE', token }),
  get: (path, token) => request(path, { token }),
  post: (path, body, token) => request(path, { method: 'POST', body, token }),
  put: (path, body, token) => request(path, { method: 'PUT', body, token }),
};

export { ApiError };
