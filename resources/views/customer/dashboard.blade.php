<x-customer-layout title="Customer Dashboard">
    <style>
        .cd-wrap {
            max-width: 1280px;
            margin: 0 auto;
        }
        .cd-alert {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #065f46;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
        .cd-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            border-radius: 1.5rem;
        }
        .cd-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .cd-brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cd-title {
            font-size: 1.625rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .cd-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0.875rem;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            box-shadow: 0px 6px 20px -6px rgba(15, 118, 110, 0.4);
            transition: all 0.25s ease;
        }
        .cd-btn:hover { transform: translateY(-1px); }

        .cd-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .cd-card {
            border-radius: 1.5rem;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .cd-card-head {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .cd-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            background: rgba(15, 118, 110, 0.08);
            color: #0f766e;
        }
        .cd-card:nth-child(2) .cd-icon { background: rgba(245, 158, 11, 0.08); color: #d97706; }
        .cd-card:nth-child(3) .cd-icon { background: rgba(16, 185, 129, 0.08); color: #059669; }
        .cd-card:nth-child(4) .cd-icon { background: rgba(239, 68, 68, 0.08); color: #dc2626; }
        .cd-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .cd-value {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1;
        }

        .cd-section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .cd-section-title i { color: #0f766e; }

        .cd-table {
            border-radius: 1.5rem;
            overflow: hidden;
        }
        .cd-table-wrap { overflow-x: auto; }
        .cd-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        .cd-table thead {
            background: rgba(248, 250, 252, 0.7);
            border-bottom: 1px solid rgba(203, 213, 225, 0.4);
        }
        .cd-table th {
            padding: 1rem 1.25rem;
            text-align: left;
            font-weight: 600;
            color: #334155;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }
        .cd-table td {
            padding: 1rem 1.25rem;
            color: #1e293b;
            font-weight: 450;
        }
        .cd-table tbody tr {
            border-bottom: 1px solid rgba(226, 232, 240, 0.4);
        }
        .cd-table tbody tr:last-child { border-bottom: none; }

        .cd-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: -0.01em;
        }
        .cd-pending { background: rgba(245, 158, 11, 0.1); color: #b45309; }
        .cd-completed { background: rgba(16, 185, 129, 0.1); color: #065f46; }
        .cd-cancelled { background: rgba(239, 68, 68, 0.1); color: #991b1b; }
        .cd-assigned, .cd-accepted, .cd-progress { background: rgba(59, 130, 246, 0.1); color: #1e40af; }

        .cd-action {
            color: #0f766e;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .cd-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: #64748b;
        }

        @media (max-width: 768px) {
            .cd-header { flex-direction: column; align-items: flex-start; }
            .cd-btn { width: 100%; justify-content: center; }
            .cd-title { font-size: 1.375rem; }
        }
    </style>

    @php
        $statusClass = static function (string $status): string {
            return match ($status) {
                'Pending' => 'cd-pending',
                'Completed' => 'cd-completed',
                'Cancelled' => 'cd-cancelled',
                'Assigned' => 'cd-assigned',
                'Accepted' => 'cd-accepted',
                'In Progress' => 'cd-progress',
                default => 'cd-pending',
            };
        };
    @endphp

    <div class="cd-wrap">
        @if (session('success'))
            <div class="cd-alert glass-panel">
                <i class="fas fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="cd-header glass-panel">
            <div class="cd-brand">
                <div class="cd-brand-icon">
                    <x-logo-mark class="h-12 w-12" />
                </div>
                <h1 class="cd-title">{{ auth()->user()->name }}</h1>
            </div>
            <a href="{{ route('rides.create') }}" class="cd-btn">
                <i class="fas fa-plus-circle"></i>
                Request New Ride
            </a>
        </div>

        <div class="cd-stats">
            <div class="cd-card glass-panel">
                <div class="cd-card-head">
                    <div class="cd-icon"><i class="fas fa-chart-bar"></i></div>
                    <span class="cd-label">Total Requests</span>
                </div>
                <div class="cd-value">{{ $stats['total'] }}</div>
            </div>

            <div class="cd-card glass-panel">
                <div class="cd-card-head">
                    <div class="cd-icon"><i class="fas fa-clock"></i></div>
                    <span class="cd-label">Pending</span>
                </div>
                <div class="cd-value">{{ $stats['pending'] }}</div>
            </div>

            <div class="cd-card glass-panel">
                <div class="cd-card-head">
                    <div class="cd-icon"><i class="fas fa-circle-check"></i></div>
                    <span class="cd-label">Completed</span>
                </div>
                <div class="cd-value">{{ $stats['completed'] }}</div>
            </div>

            <div class="cd-card glass-panel">
                <div class="cd-card-head">
                    <div class="cd-icon"><i class="fas fa-circle-xmark"></i></div>
                    <span class="cd-label">Cancelled</span>
                </div>
                <div class="cd-value">{{ $stats['cancelled'] }}</div>
            </div>
        </div>

        <h2 class="cd-section-title">
            <i class="fas fa-clock-rotate-left"></i>
            Recent Rides
        </h2>

        <div class="cd-table glass-panel">
            <div class="cd-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pickup</th>
                            <th>Destination</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRides as $ride)
                            <tr>
                                <td>#{{ $ride->id }}</td>
                                <td>{{ $ride->pickup_location }}</td>
                                <td>{{ $ride->destination_location }}</td>
                                <td>
                                    <span class="cd-badge {{ $statusClass($ride->status) }}">
                                        <i class="fas fa-circle" style="font-size: 0.6rem;"></i> {{ $ride->status }}
                                    </span>
                                </td>
                                <td>{{ $ride->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('rides.show', $ride) }}" class="cd-action">
                                        View <i class="fas fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="cd-empty">
                                        <x-logo-mark class="mx-auto mb-3 h-10 w-10 opacity-35" />
                                        <p>No rides yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-customer-layout>
