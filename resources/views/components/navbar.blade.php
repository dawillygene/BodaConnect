@props(['title' => 'BodaConnect', 'user' => null])

<style>
    .glass-header {
        position: sticky;
        top: 0;
        z-index: 40;
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(24px) saturate(180%);
        -webkit-backdrop-filter: blur(24px) saturate(180%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow:
            0px 4px 24px -8px rgba(0, 0, 0, 0.06),
            0px 0px 0px 1px rgba(255, 255, 255, 0.3) inset;
        transition: all 0.3s ease;
    }

    .glass-header.scrolled {
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(28px) saturate(180%);
        -webkit-backdrop-filter: blur(28px) saturate(180%);
        box-shadow:
            0px 8px 32px -8px rgba(0, 0, 0, 0.1),
            0px 0px 0px 1px rgba(255, 255, 255, 0.4) inset;
        border-bottom: 1px solid rgba(255, 255, 255, 0.6);
    }

    .header-inner {
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
    }

    @media (min-width: 640px) {
        .header-inner { padding: 0.875rem 1.5rem; }
    }

    @media (min-width: 1024px) {
        .header-inner { padding: 0.875rem 2rem; }
    }

    .brand-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .brand-btn:hover {
        transform: translateY(-2px) scale(1.03);
    }

    .brand-btn:active {
        transform: translateY(1px) scale(0.98);
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
    }

    .user-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 0.5rem 0.875rem;
        border-radius: 9999px;
        font-weight: 500;
        color: #334155;
        font-size: 0.8125rem;
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0px 2px 8px -4px rgba(0, 0, 0, 0.04);
        letter-spacing: -0.01em;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .user-badge:hover {
        background: rgba(255, 255, 255, 0.85);
        box-shadow: 0px 4px 12px -4px rgba(0, 0, 0, 0.06);
    }

    .user-badge i {
        color: #0f766e;
        font-size: 0.9rem;
    }

    .user-role {
        font-weight: 400;
        color: #64748b;
    }

    .btn-logout {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 0.875rem;
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(203, 213, 225, 0.4);
        border-radius: 0.75rem;
        color: #475569;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: -0.01em;
        cursor: pointer;
        transition: all 0.25s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .btn-logout:hover {
        background: rgba(255, 255, 255, 0.85);
        border-color: rgba(239, 68, 68, 0.3);
        color: #dc2626;
        box-shadow: 0px 4px 12px -4px rgba(239, 68, 68, 0.15);
    }

    .btn-logout:active { transform: scale(0.96); }

    .btn-logout i {
        font-size: 0.7rem;
        transition: transform 0.2s;
    }

    .btn-logout:hover i { transform: translateX(2px); }

    .auth-link {
        color: #475569;
        font-weight: 500;
        text-decoration: none;
        padding: 0.375rem 0.75rem;
        border-radius: 0.625rem;
        transition: all 0.2s ease;
        position: relative;
        font-size: 0.875rem;
        letter-spacing: -0.01em;
    }

    .auth-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%) scaleX(0);
        width: 60%;
        height: 2px;
        background: #0f766e;
        border-radius: 1px;
        transition: transform 0.25s ease;
    }

    .auth-link:hover {
        color: #0f766e;
        background: rgba(255, 255, 255, 0.5);
    }

    .auth-link:hover::after { transform: translateX(-50%) scaleX(1); }

    .auth-divider {
        width: 1px;
        height: 20px;
        background: rgba(203, 213, 225, 0.5);
        margin: 0 0.25rem;
    }

    @media (max-width: 480px) {
        .user-badge {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            gap: 0.375rem;
        }
        .btn-logout {
            padding: 0.375rem 0.75rem;
            font-size: 0.7rem;
        }
        .header-actions { gap: 0.5rem; }
        .auth-link {
            font-size: 0.8125rem;
            padding: 0.25rem 0.5rem;
        }
    }
</style>

<header class="glass-header" id="mainHeader">
    <div class="header-inner">
        <a href="{{ route('home') }}" class="brand-btn" aria-label="BodaConnect Home">
            <x-logo-mark class="h-11 w-11" />
        </a>

        <div class="header-actions">
            @if($user)
                <div class="user-badge">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ $user->name }}</span>
                    <span class="user-role">({{ ucfirst($user->role) }})</span>
                </div>

                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-right-from-bracket"></i>
                        Logout
                    </button>
                </form>
            @else
                <a class="auth-link" href="{{ route('login') }}">Login</a>
                <span class="auth-divider"></span>
                <a class="auth-link" href="{{ route('register') }}">Register</a>
            @endif
        </div>
    </div>
</header>

<script>
    (function () {
        const header = document.getElementById('mainHeader');
        if (!header) return;

        const toggleScrolled = () => {
            if (window.scrollY > 10) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        };

        toggleScrolled();
        window.addEventListener('scroll', toggleScrolled, { passive: true });
    })();
</script>
