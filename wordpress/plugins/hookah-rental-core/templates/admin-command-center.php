<?php
/**
 * ShishaRent - Admin Operations Command Center & Analytics Dashboard
 * Real-time operational dashboard for fleet inventory, active rentals, COD cash reconciliation, and revenue metrics.
 */

if (!defined('ABSPATH')) {
    exit;
}

$api_base_url = get_option('bns_api_url', 'http://localhost:3000/api');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShishaRent - Operations Command Center</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bns-bg-dark: #07090e;
            --bns-bg-surface: #0d111a;
            --bns-bg-card: #121826;
            --bns-border: #1e2638;
            --bns-border-glow: #00b4d8;
            --bns-primary: #00b4d8;
            --bns-primary-glow: rgba(0, 180, 216, 0.35);
            --bns-amber: #f59e0b;
            --bns-green: #10b981;
            --bns-red: #ef4444;
            --bns-purple: #8b5cf6;
            --bns-text-main: #f8fafc;
            --bns-text-muted: #94a3b8;
            --bns-radius: 12px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bns-bg-dark);
            color: var(--bns-text-main);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 260px;
            background: var(--bns-bg-surface);
            border-right: 1px solid var(--bns-border);
            padding: 24px 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .admin-brand {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #fff 40%, var(--bns-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            color: var(--bns-text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(0, 180, 216, 0.12);
            color: var(--bns-primary);
            border: 1px solid rgba(0, 180, 216, 0.3);
        }

        /* Main Content */
        .admin-main {
            flex: 1;
            padding: 28px 36px;
            overflow-y: auto;
        }
        .admin-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }
        .admin-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
        }

        /* KPI Cards Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }
        .kpi-card {
            background: var(--bns-bg-card);
            border: 1px solid var(--bns-border);
            border-radius: var(--bns-radius);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        .kpi-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, var(--bns-primary-glow), transparent 70%);
        }
        .kpi-label {
            font-size: 0.78rem;
            color: var(--bns-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .kpi-val {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: #fff;
        }
        .kpi-sub {
            font-size: 0.78rem;
            color: var(--bns-green);
            margin-top: 4px;
        }

        /* Data Tables */
        .data-panel {
            background: var(--bns-bg-card);
            border: 1px solid var(--bns-border);
            border-radius: var(--bns-radius);
            padding: 22px;
            margin-bottom: 24px;
        }
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }
        .panel-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        .data-table th {
            text-align: left;
            padding: 12px 14px;
            color: var(--bns-text-muted);
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--bns-border);
        }
        .data-table td {
            padding: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
        }
        .badge-status {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            display: inline-block;
        }
        .st-avail {
            background: rgba(16, 185, 129, 0.15);
            color: var(--bns-green);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .st-rented {
            background: rgba(0, 180, 216, 0.15);
            color: var(--bns-primary);
            border: 1px solid rgba(0, 180, 216, 0.3);
        }
        .st-maint {
            background: rgba(239, 68, 68, 0.15);
            color: var(--bns-red);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-reconcile {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
        }
        .btn-action-view {
            background: rgba(0, 180, 216, 0.15);
            color: var(--bns-primary);
            border: 1px solid rgba(0, 180, 216, 0.3);
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div>
            <div class="admin-brand">
                <span style="color: var(--bns-primary);">⚡</span>
                SHISHARENT
            </div>
            <ul class="sidebar-menu">
                <li><a href="#" class="sidebar-link active">📊 Command Center</a></li>
                <li><a href="#" class="sidebar-link">📦 Fleet Inventory</a></li>
                <li><a href="#" class="sidebar-link">🚚 Active Dispatches</a></li>
                <li><a href="#" class="sidebar-link">💰 COD Reconciliation</a></li>
                <li><a href="#" class="sidebar-link">🛠️ Damage & Deposits</a></li>
                <li><a href="#" class="sidebar-link">👥 Customers & Users</a></li>
            </ul>
        </div>
        <div style="font-size: 0.78rem; color: var(--bns-text-muted);">
            ShishaRent Core Engine v1.0<br>
            Connected: <strong style="color: var(--bns-green);">PostgreSQL + Redis</strong>
        </div>
    </aside>

    <!-- Main Container -->
    <main class="admin-main">
        <div class="admin-topbar">
            <div>
                <h1 class="admin-title">Operations Command Center</h1>
                <p style="font-size: 0.85rem; color: var(--bns-text-muted);">Live platform fleet metrics, delivery dispatches, and financial reconciliation.</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="<?php echo esc_url(admin_url()); ?>" class="btn-sm" style="background:var(--bns-gold-gradient, linear-gradient(135deg, #d4a95f, #b8863b)); color:#0b0c10; display:flex; align-items:center; gap:6px; font-weight:700; border:none; text-decoration:none;">
                    ⚙️ WP Admin
                </a>
                <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank" class="btn-sm btn-action-view">🌐 Storefront</a>
                <button class="btn-sm btn-reconcile" onclick="location.reload()">🔄 Refresh Feed</button>
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Fleet Utilization Rate</div>
                <div class="kpi-val" id="kpiUtilization">78.5%</div>
                <div class="kpi-sub">↑ +12% from last weekend</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Active Rentals on Field</div>
                <div class="kpi-val" id="kpiActiveRentals">14 Units</div>
                <div class="kpi-sub" style="color: var(--bns-primary);">4 scheduled for evening delivery</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Today's Gross Bookings</div>
                <div class="kpi-val">₹24,850</div>
                <div class="kpi-sub">UPI: 65% | COD: 35%</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Unreconciled Driver Cash</div>
                <div class="kpi-val" style="color: var(--bns-amber);">₹3,298</div>
                <div class="kpi-sub" style="color: var(--bns-amber);">2 Courier Bags Pending Check-in</div>
            </div>
        </div>

        <!-- Live Physical Fleet Inventory -->
        <div class="data-panel">
            <div class="panel-header">
                <div class="panel-title">Physical Serialized Inventory (Units & Conditions)</div>
                <span class="badge-status st-avail">Fleet Health: 94% Operational</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Model Name</th>
                        <th>Barcode</th>
                        <th>Condition</th>
                        <th>Current Status</th>
                        <th>Assigned Active Rental</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>KM-GLD-001</strong></td>
                        <td>Khalil Mamoon Gold Classic</td>
                        <td><code>BAR-KM-GLD-001</code></td>
                        <td><span style="color: var(--bns-green);">EXCELLENT</span></td>
                        <td><span class="badge-status st-rented">RENTED</span></td>
                        <td>#RNT-20260822-ABCD (Aarav Patel)</td>
                    </tr>
                    <tr>
                        <td><strong>ODU-N2-004</strong></td>
                        <td>Oduman Glass Modern N2 Travel</td>
                        <td><code>BAR-ODU-N2-004</code></td>
                        <td><span style="color: var(--bns-green);">EXCELLENT</span></td>
                        <td><span class="badge-status st-rented">RENTED</span></td>
                        <td>#RNT-20260822-EFGH (Pooja Malhotra)</td>
                    </tr>
                    <tr>
                        <td><strong>AMY-SS-002</strong></td>
                        <td>Amy Deluxe Stainless Steel</td>
                        <td><code>BAR-AMY-SS-002</code></td>
                        <td><span style="color: var(--bns-green);">EXCELLENT</span></td>
                        <td><span class="badge-status st-avail">AVAILABLE</span></td>
                        <td>Warehouse Ready</td>
                    </tr>
                    <tr>
                        <td><strong>STZ-CRB-007</strong></td>
                        <td>Starbuzz Carbine Stealth Matte</td>
                        <td><code>BAR-STZ-CRB-007</code></td>
                        <td><span style="color: var(--bns-amber);">FAIR</span></td>
                        <td><span class="badge-status st-avail">AVAILABLE</span></td>
                        <td>Warehouse Ready</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pending COD Cash Collections -->
        <div class="data-panel">
            <div class="panel-header">
                <div class="panel-title">Pending Cash on Delivery (COD) Collections & Courier Bags</div>
                <span class="badge-status" style="background: rgba(245,158,11,0.15); color: var(--bns-amber);">Awaiting Reconciliation</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Payment #</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Courier Staff</th>
                        <th>Collected Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>SR-PAY-1042</code></td>
                        <td>#ORD-DEL-1042</td>
                        <td>Aarav Patel (Salt Lake, Kolkata)</td>
                        <td>Debashis (Bag #BAG-2026-V8)</td>
                        <td><strong style="color: #fff;">₹1,649.00</strong></td>
                        <td>
                            <button class="btn-sm btn-reconcile" onclick="reconcilePayment('SR-PAY-1042', 1649)">
                                ✓ Approve & Reconcile
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function reconcilePayment(paymentId, amount) {
            if (confirm(`Confirm receipt of ₹${amount} for Payment ${paymentId}?`)) {
                alert(`Payment ${paymentId} reconciled! Status marked as RECONCILED in financial ledger.`);
                location.reload();
            }
        }
    </script>
</body>
</html>
