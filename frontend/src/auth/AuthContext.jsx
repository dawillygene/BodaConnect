import { useEffect, useState } from 'react';
import { api } from '../lib/api';
import { AuthContext } from './auth-context';

const STORAGE_KEY = 'bodaconnect.auth.token';

export function AuthProvider({ children }) {
  const [token, setToken] = useState(() => localStorage.getItem(STORAGE_KEY));
  const [user, setUser] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    let ignore = false;

    async function bootstrap() {
      if (!token) {
        setIsLoading(false);
        return;
      }

      try {
        const response = await api.get('/auth/user', token);

        if (!ignore) {
          setUser(response.user);
        }
      } catch {
        if (!ignore) {
          localStorage.removeItem(STORAGE_KEY);
          setToken(null);
          setUser(null);
        }
      } finally {
        if (!ignore) {
          setIsLoading(false);
        }
      }
    }

    bootstrap();

    return () => {
      ignore = true;
    };
  }, [token]);

  async function login(credentials) {
    const response = await api.post('/auth/login', credentials);
    localStorage.setItem(STORAGE_KEY, response.token);
    setToken(response.token);
    setUser(response.user);
    return response.user;
  }

  async function register(payload) {
    const response = await api.post('/auth/register', payload);
    localStorage.setItem(STORAGE_KEY, response.token);
    setToken(response.token);
    setUser(response.user);
    return response.user;
  }

  async function logout() {
    try {
      if (token) {
        await api.post('/auth/logout', {}, token);
      }
    } finally {
      localStorage.removeItem(STORAGE_KEY);
      setToken(null);
      setUser(null);
    }
  }

  return (
    <AuthContext.Provider
      value={{
        isLoading,
        isAuthenticated: Boolean(user),
        login,
        logout,
        register,
        token,
        user,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}
