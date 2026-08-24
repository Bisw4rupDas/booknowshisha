<?php
/**
 * ShishaRent - Courier & Field Staff Mobile Driver Portal
 * Mobile-first interface for delivery dispatch, ID check, COD cash collection, and return inspections.
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ShishaRent Driver & Field Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bns-bg-dark: #080a10;
            --bns-bg-surface: #0f131f;
            --bns-bg-card: #151b2b;
            --bns-border: #1e2638;
            --bns-border-glow: #00b4d8;
            --bns-primary: #00b4d8;
            --bns-primary-glow: rgba(0, 180, 216, 0.35);
            --bns-amber: #f59e0b;
            --bns-green: #10b981;
            --bns-red: #ef4444;
            --bns-text-main: #f8fafc;
            --bns-text-muted: #94a3b8;
            --bns-radius: 12px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bns-bg-dark);
            color: var(--bns-text-main);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        /* Top Header */
        .courier-header {
            background: rgba(15, 19, 31, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--bns-border);
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .courier-brand {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #fff 40%, var(--bns-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .courier-badge {
            background: rgba(0, 180, 216, 0.15);
            border: 1px solid rgba(0, 180, 216, 0.4);
            color: var(--bns-primary);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            text-transform: uppercase;
        }
        .courier-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--bns-green);
            box-shadow: 0 0 10px var(--bns-green);
            display: inline-block;
        }

        /* Container */
        .portal-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 16px;
        }

        /* Top Stats Pill Bar */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }
        .stat-pill {
            background: var(--bns-bg-surface);
            border: 1px solid var(--bns-border);
            border-radius: var(--bns-radius);
            padding: 12px 10px;
            text-align: center;
        }
        .stat-pill .num {
            font-family: 'Outfit', sans-serif;
            font-size: 1.3rem;
            font-weight: 800;
            color: #fff;
        }
        .stat-pill .label {
            font-size: 0.72rem;
            color: var(--bns-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* Tab Switcher */
        .portal-tabs {
            display: flex;
            background: var(--bns-bg-surface);
            border: 1px solid var(--bns-border);
            border-radius: var(--bns-radius);
            padding: 4px;
            margin-bottom: 18px;
            gap: 4px;
        }
        .portal-tab {
            flex: 1;
            padding: 10px 4px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--bns-text-muted);
            background: transparent;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
        }
        .portal-tab.active {
            background: var(--bns-card);
            color: var(--bns-primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(0, 180, 216, 0.3);
        }

        /* Task Card */
        .task-card {
            background: var(--bns-bg-card);
            border: 1px solid var(--bns-border);
            border-radius: var(--bns-radius);
            padding: 16px;
            margin-bottom: 14px;
            position: relative;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .task-card:active {
            transform: scale(0.99);
        }
        .task-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        .task-id {
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
        }
        .task-type-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }
        .type-delivery {
            background: rgba(0, 180, 216, 0.15);
            color: var(--bns-primary);
            border: 1px solid rgba(0, 180, 216, 0.3);
        }
        .type-return {
            background: rgba(245, 158, 11, 0.15);
            color: var(--bns-amber);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .task-customer {
            font-size: 0.9rem;
            font-weight: 600;
            color: #e2e8f0;
            margin-bottom: 4px;
        }
        .task-address {
            font-size: 0.8rem;
            color: var(--bns-text-muted);
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .task-meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 0.78rem;
            color: #cbd5e1;
            padding: 10px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            margin-bottom: 12px;
        }
        .task-meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Action Buttons Grid */
        .task-actions {
            display: grid;
            grid-template-columns: 1fr 1fr 2fr;
            gap: 8px;
        }
        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 8px;
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid var(--bns-border);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-call {
            background: rgba(16, 185, 129, 0.15);
            color: var(--bns-green);
            border-color: rgba(16, 185, 129, 0.3);
        }
        .btn-map {
            background: rgba(0, 180, 216, 0.15);
            color: var(--bns-primary);
            border-color: rgba(0, 180, 216, 0.3);
        }
        .btn-primary-action {
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            color: #fff;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 180, 216, 0.3);
        }
        .btn-return-action {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            border: none;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        /* Inspection & Delivery Modal */
        .portal-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            z-index: 100;
            padding: 20px 16px;
            overflow-y: auto;
        }
        .portal-modal.open {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: var(--bns-bg-surface);
            border: 1px solid var(--bns-border-glow);
            box-shadow: 0 0 40px var(--bns-primary-glow);
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            border-bottom: 1px solid var(--bns-border);
            padding-bottom: 12px;
        }
        .modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
        }
        .modal-close {
            background: none;
            border: none;
            color: var(--bns-text-muted);
            font-size: 1.4rem;
            cursor: pointer;
        }

        /* Checkbox Checklist */
        .check-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bns-bg-card);
            border: 1px solid var(--bns-border);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: pointer;
        }
        .check-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--bns-primary);
        }
        .check-item span {
            font-size: 0.85rem;
            color: #e2e8f0;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 14px;
        }
        .form-group label {
            display: block;
            font-size: 0.78rem;
            color: var(--bns-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            background: var(--bns-bg-card);
            border: 1px solid var(--bns-border);
            border-radius: 8px;
            padding: 12px;
            color: #fff;
            font-size: 0.9rem;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--bns-primary);
            box-shadow: 0 0 8px var(--bns-primary-glow);
        }

        /* Bottom Fixed Navigation */
        .bottom-dock {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(15, 19, 31, 0.98);
            border-top: 1px solid var(--bns-border);
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 50;
        }
        .dock-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            font-size: 0.7rem;
            color: var(--bns-text-muted);
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
        }
        .dock-btn.active {
            color: var(--bns-primary);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="courier-header">
        <div class="courier-brand">
            <span class="courier-status-dot"></span>
            SHISHARENT
            <span class="courier-badge">Field Staff</span>
        </div>
        <div style="font-size: 0.8rem; color: var(--bns-text-muted);">
            Driver: <strong style="color: #fff;" id="driverName">Debashis (Kolkata East Zone)</strong>
        </div>
    </header>

    <main class="portal-container">
        <!-- Top Stats -->
        <div class="stats-bar">
            <div class="stat-pill">
                <div class="num" id="statDeliveries">4</div>
                <div class="label">Deliveries</div>
            </div>
            <div class="stat-pill">
                <div class="num" id="statReturns">2</div>
                <div class="label">Pickups</div>
            </div>
            <div class="stat-pill">
                <div class="num" style="color: var(--bns-amber);" id="statCod">₹3,298</div>
                <div class="label">COD Cash</div>
            </div>
        </div>

        <!-- Tab Filters -->
        <div class="portal-tabs">
            <button class="portal-tab active" onclick="switchPortalTab('deliveries')">Deliveries (4)</button>
            <button class="portal-tab" onclick="switchPortalTab('returns')">Pickups (2)</button>
            <button class="portal-tab" onclick="switchPortalTab('cod')">COD Cash Log</button>
        </div>

        <!-- Deliveries List -->
        <div id="tabDeliveriesSection">
            <!-- Card 1 -->
            <div class="task-card">
                <div class="task-card-header">
                    <div>
                        <div class="task-id">#ORD-DEL-1042</div>
                        <div class="task-customer">Aarav Patel (+91 99035 56825)</div>
                    </div>
                    <span class="task-type-badge type-delivery">Out for Delivery</span>
                </div>
                <div class="task-address">📍 42, Salt Lake Sector V, Kolkata, PIN 700091 (Near Webel More)</div>
                <div class="task-meta-row">
                    <div class="task-meta-item">📦 Solo 24H (KM Gold)</div>
                    <div class="task-meta-item">🕒 Slot: 18:00 - 20:00</div>
                    <div class="task-meta-item" style="color: var(--bns-amber);">💵 COD: ₹1,649</div>
                </div>
                <div class="task-actions">
                    <a href="tel:+919903556825" class="btn-action btn-call">📞 Call</a>
                    <a href="https://maps.google.com/?q=Salt+Lake+Sector+V+Kolkata" target="_blank" class="btn-action btn-map">🗺️ Map</a>
                    <button class="btn-action btn-primary-action" onclick="openHandoverModal('ORD-DEL-1042', 'Aarav Patel', 1649, 'KM-GLD-001')">
                        Handover & Collect
                    </button>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="task-card">
                <div class="task-card-header">
                    <div>
                        <div class="task-id">#ORD-DEL-1043</div>
                        <div class="task-customer">Pooja Malhotra (+91 98111 22334)</div>
                    </div>
                    <span class="task-type-badge type-delivery">Scheduled</span>
                </div>
                <div class="task-address">📍 Flat 402, New Town Action Area II, Kolkata, PIN 700156</div>
                <div class="task-meta-row">
                    <div class="task-meta-item">📦 Duo Weekend (Oduman N2)</div>
                    <div class="task-meta-item">🕒 Slot: 20:00 - 22:00</div>
                    <div class="task-meta-item" style="color: var(--bns-green);">✅ Paid via UPI</div>
                </div>
                <div class="task-actions">
                    <a href="tel:+919811122334" class="btn-action btn-call">📞 Call</a>
                    <a href="https://maps.google.com/?q=New+Town+Action+Area+II+Kolkata" target="_blank" class="btn-action btn-map">🗺️ Map</a>
                    <button class="btn-action btn-primary-action" onclick="openHandoverModal('ORD-DEL-1043', 'Pooja Malhotra', 0, 'ODU-N2-004')">
                        Handover Unit
                    </button>
                </div>
            </div>
        </div>

        <!-- Pickups List -->
        <div id="tabReturnsSection" style="display: none;">
            <!-- Return Card 1 -->
            <div class="task-card">
                <div class="task-card-header">
                    <div>
                        <div class="task-id">#RNT-RET-9081</div>
                        <div class="task-customer">Rohan Mehra (+91 99887 76655)</div>
                    </div>
                    <span class="task-type-badge type-return">Return Ready</span>
                </div>
                <div class="task-address">📍 House 18, Block C, Ballygunge Circular Rd, Kolkata, PIN 700019</div>
                <div class="task-meta-row">
                    <div class="task-meta-item">📦 Amy Deluxe Stainless</div>
                    <div class="task-meta-item">🔒 Deposit Held: ₹1,500</div>
                </div>
                <div class="task-actions">
                    <a href="tel:+919988776655" class="btn-action btn-call">📞 Call</a>
                    <a href="https://maps.google.com/?q=Ballygunge+Circular+Road+Kolkata" target="_blank" class="btn-action btn-map">🗺️ Map</a>
                    <button class="btn-action btn-return-action" onclick="openInspectionModal('RNT-RET-9081', 'Rohan Mehra', 1500)">
                        Inspect & Retrieve
                    </button>
                </div>
            </div>
        </div>

        <!-- COD Cash Section -->
        <div id="tabCodSection" style="display: none;">
            <div class="task-card">
                <div class="task-card-header">
                    <div class="task-id">Courier Cash In Hand</div>
                    <span class="task-type-badge" style="background: rgba(16,185,129,0.15); color: var(--bns-green);">Bag #BAG-2026-V8</span>
                </div>
                <div style="font-size: 1.8rem; font-family: 'Outfit'; font-weight: 800; color: #fff; margin: 10px 0;">
                    ₹3,298.00
                </div>
                <div style="font-size: 0.8rem; color: var(--bns-text-muted); margin-bottom: 12px;">
                    Total cash collected today across 2 COD deliveries. Hand over to warehouse manager at end of shift for 1-click reconciliation.
                </div>
                <button class="btn-action btn-primary-action" style="width: 100%;" onclick="alert('Cash bag #BAG-2026-V8 submitted for warehouse reconciliation.')">
                    Submit Cash Bag for Reconciliation
                </button>
            </div>
        </div>
    </main>

    <!-- Handover Modal -->
    <div class="portal-modal" id="handoverModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Delivery Handover & Age Check</div>
                <button class="modal-close" onclick="closeModal('handoverModal')">✕</button>
            </div>
            <div id="handoverBody">
                <div class="form-group">
                    <label>Assigned Unit Serial / Barcode</label>
                    <input type="text" id="handoverSerial" class="form-control" readonly>
                </div>
                <div class="check-item">
                    <input type="checkbox" id="checkIdAge" checked>
                    <span><strong>Legal 21+ Age Verified:</strong> Customer presented valid government photo ID matching order name.</span>
                </div>
                <div class="check-item">
                    <input type="checkbox" id="checkSanitized" checked>
                    <span><strong>Hygiene Seal Verified:</strong> Sealed silicone hoses and sanitized mouthpieces inspected with customer.</span>
                </div>
                <div class="form-group" id="codPaymentGroup">
                    <label>Cash Collected (₹)</label>
                    <input type="number" id="handoverCodAmount" class="form-control">
                </div>
                <button class="btn-action btn-primary-action" style="width: 100%; padding: 14px; font-size: 0.95rem; margin-top: 10px;" onclick="confirmHandover()">
                    Confirm Delivery & Activate Rental
                </button>
            </div>
        </div>
    </div>

    <!-- Return Inspection Modal -->
    <div class="portal-modal" id="inspectionModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">5-Point Return Inspection</div>
                <button class="modal-close" onclick="closeModal('inspectionModal')">✕</button>
            </div>
            <div>
                <div class="check-item">
                    <input type="checkbox" id="inspBase" checked>
                    <span>1. Glass Base / Acrylic Body intact without cracks or chips.</span>
                </div>
                <div class="check-item">
                    <input type="checkbox" id="inspStem" checked>
                    <span>2. Stainless Steel Stem / Downstem clean & airtight.</span>
                </div>
                <div class="check-item">
                    <input type="checkbox" id="inspBowl" checked>
                    <span>3. Silicone/Clay Phunnel Bowl present and unburnt.</span>
                </div>
                <div class="check-item">
                    <input type="checkbox" id="inspHmd" checked>
                    <span>4. Heat Management Device (HMD) & tongs present.</span>
                </div>
                <div class="check-item">
                    <input type="checkbox" id="inspHose" checked>
                    <span>5. Washable Silicone Hose returned in good order.</span>
                </div>
                <div class="form-group">
                    <label>Damage / Condition Observation Notes</label>
                    <input type="text" id="inspNotes" class="form-control" placeholder="All components returned clean in original padded flight case.">
                </div>
                <button class="btn-action btn-return-action" style="width: 100%; padding: 14px; font-size: 0.95rem; margin-top: 10px;" onclick="confirmReturnInspection()">
                    Pass Inspection & Release Deposit (₹1,500)
                </button>
            </div>
        </div>
    </div>

    <!-- Bottom Dock -->
    <nav class="bottom-dock">
        <button class="dock-btn active" onclick="switchPortalTab('deliveries')">
            <span style="font-size: 1.2rem;">📦</span>
            Deliveries
        </button>
        <button class="dock-btn" onclick="switchPortalTab('returns')">
            <span style="font-size: 1.2rem;">🔄</span>
            Pickups
        </button>
        <button class="dock-btn" onclick="switchPortalTab('cod')">
            <span style="font-size: 1.2rem;">💰</span>
            Cash
        </button>
    </nav>

    <script>
        function switchPortalTab(tab) {
            document.getElementById('tabDeliveriesSection').style.display = tab === 'deliveries' ? 'block' : 'none';
            document.getElementById('tabReturnsSection').style.display = tab === 'returns' ? 'block' : 'none';
            document.getElementById('tabCodSection').style.display = tab === 'cod' ? 'block' : 'none';

            const tabs = document.querySelectorAll('.portal-tab');
            tabs.forEach((t, index) => {
                if ((tab === 'deliveries' && index === 0) ||
                    (tab === 'returns' && index === 1) ||
                    (tab === 'cod' && index === 2)) {
                    t.classList.add('active');
                } else {
                    t.classList.remove('active');
                }
            });
        }

        function openHandoverModal(orderId, customerName, codAmount, serial) {
            document.getElementById('handoverSerial').value = serial;
            document.getElementById('handoverCodAmount').value = codAmount;
            if (codAmount === 0) {
                document.getElementById('codPaymentGroup').style.display = 'none';
            } else {
                document.getElementById('codPaymentGroup').style.display = 'block';
            }
            document.getElementById('handoverModal').classList.add('open');
        }

        function openInspectionModal(rentalId, customerName, deposit) {
            document.getElementById('inspectionModal').classList.add('open');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('open');
        }

        function confirmHandover() {
            if (!document.getElementById('checkIdAge').checked) {
                alert('Legal 21+ Age verification check is mandatory.');
                return;
            }
            alert('Delivery confirmed! Rental activated and customer notification dispatched.');
            closeModal('handoverModal');
        }

        function confirmReturnInspection() {
            alert('Return inspection passed! Unit marked available and security deposit released.');
            closeModal('inspectionModal');
        }
    </script>
</body>
</html>
