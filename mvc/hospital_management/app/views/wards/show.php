
<?php
$wardId = $ward['id'] ?? 0;

$totalRooms = $ward['total_rooms'] ?? 0;
$totalBeds = $ward['total_beds'] ?? 0;
$availableBeds = $ward['available_beds'] ?? 0;
$occupiedBeds = $ward['occupied_beds'] ?? 0;

$status = $ward['status'] ?? 'Active';
?>

<style>
    .ward-details-container {
        max-width: 1000px;
        margin: 30px auto;
        padding: 20px;
    }

    .ward-details-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        border: 1px solid #e5eaf1;
    }

    .ward-details-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e5eaf1;
    }

    .ward-details-header h2 {
        color: #1e293b;
        font-size: 25px;
        margin: 0;
    }

    .ward-details-header p {
        color: #64748b;
        margin: 6px 0 0;
        font-size: 14px;
    }

    .ward-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .ward-info-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 18px;
    }

    .ward-info-item label {
        display: block;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .ward-info-item p {
        color: #1e293b;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        overflow-wrap: anywhere;
    }

    .ward-description {
        grid-column: 1 / -1;
    }

    .ward-count-section {
        margin-top: 25px;
    }

    .ward-count-section h3 {
        color: #1e293b;
        font-size: 19px;
        margin-bottom: 18px;
    }

    .ward-count-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
    }

    .ward-count-card {
        padding: 20px 15px;
        border-radius: 10px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
    }

    .ward-count-card span {
        display: block;
        color: #475569;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .ward-count-card h3 {
        font-size: 27px;
        color: #1d4ed8;
        margin: 0;
    }

    .ward-count-card.available {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .ward-count-card.available h3 {
        color: #15803d;
    }

    .ward-count-card.occupied {
        background: #fff7ed;
        border-color: #fed7aa;
    }

    .ward-count-card.occupied h3 {
        color: #c2410c;
    }

    .ward-status {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        background: #dcfce7;
        color: #166534;
    }

    .ward-status.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .ward-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e5eaf1;
    }

    .ward-btn {
        display: inline-block;
        text-decoration: none;
        padding: 11px 22px;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 600;
        transition: 0.2s ease;
    }

    .ward-edit-btn {
        background: #2563eb;
        color: #ffffff;
    }

    .ward-edit-btn:hover {
        background: #1d4ed8;
    }

    .ward-back-btn {
        background: #e2e8f0;
        color: #334155;
    }

    .ward-back-btn:hover {
        background: #cbd5e1;
    }

    @media (max-width: 700px) {
        .ward-count-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 500px) {
        .ward-details-container {
            padding: 10px;
        }

        .ward-details-card {
            padding: 20px;
        }

        .ward-info-grid {
            grid-template-columns: 1fr;
        }

        .ward-description {
            grid-column: auto;
        }

        .ward-count-grid {
            grid-template-columns: 1fr 1fr;
        }

        .ward-details-header h2 {
            font-size: 21px;
        }
    }
</style>

<div class="ward-details-container">

    <div class="ward-details-card">

        <div class="ward-details-header">
            <div>
                <h2>Ward Details</h2>
                <p>View the information and capacity of this ward.</p>
            </div>
        </div>

        <!-- Ward Information -->
        <div class="ward-info-grid">

            <div class="ward-info-item">
                <label>Ward Name</label>
                <p>
                    <?= htmlspecialchars($ward['ward_name'] ?? '') ?>
                </p>
            </div>

            <div class="ward-info-item">
                <label>Ward Code</label>
                <p>
                    <?= htmlspecialchars($ward['ward_code'] ?? '') ?>
                </p>
            </div>

            <div class="ward-info-item">
                <label>Ward Type</label>
                <p>
                    <?= htmlspecialchars($ward['ward_type'] ?? '') ?>
                </p>
            </div>

            <div class="ward-info-item">
                <label>Status</label>
                <p>
                    <span class="ward-status <?= strtolower($status) === 'active' ? '' : 'inactive' ?>">
                        <?= htmlspecialchars($status) ?>
                    </span>
                </p>
            </div>

            <div class="ward-info-item ward-description">
                <label>Description</label>
                <p>
                    <?= htmlspecialchars($ward['description'] ?? 'No description available') ?>
                </p>
            </div>

        </div>

        <!-- Ward Capacity -->
        <div class="ward-count-section">

            <h3>Ward Capacity Overview</h3>

            <div class="ward-count-grid">

                <div class="ward-count-card">
                    <span>Total Rooms</span>
                    <h3><?= (int)$totalRooms ?></h3>
                </div>

                <div class="ward-count-card">
                    <span>Total Beds</span>
                    <h3><?= (int)$totalBeds ?></h3>
                </div>

                <div class="ward-count-card available">
                    <span>Available Beds</span>
                    <h3><?= (int)$availableBeds ?></h3>
                </div>

                <div class="ward-count-card occupied">
                    <span>Occupied Beds</span>
                    <h3><?= (int)$occupiedBeds ?></h3>
                </div>

            </div>

        </div>

        <!-- Action Buttons -->
        <div class="ward-actions">

            <a href="/wards/edit?id=<?= (int)$wardId ?>"
               class="ward-btn ward-edit-btn">
                Edit Ward
            </a>

            <a href="/wards"
               class="ward-btn ward-back-btn">
                Back to Ward List
            </a>

        </div>

    </div>

</div>
