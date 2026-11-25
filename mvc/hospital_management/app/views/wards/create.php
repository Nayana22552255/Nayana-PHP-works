
<?php
$csrfToken = $csrfToken ?? '';
?>

<style>
    * {
        box-sizing: border-box;
    }

    .ward-create-container {
        max-width: 950px;
        margin: 35px auto;
        padding: 35px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .ward-create-container h2 {
        color: #263b5e;
        font-size: 28px;
        margin: 0 0 25px;
        border-bottom: 3px solid #2563eb;
        padding-bottom: 15px;
    }

    .ward-form-group {
        margin-bottom: 20px;
    }

    .ward-form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #263b5e;
    }

    .ward-form-group input,
    .ward-form-group select,
    .ward-form-group textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        font-size: 15px;
        font-family: Arial, sans-serif;
        outline: none;
        background: #ffffff;
        transition: 0.3s;
    }

    .ward-form-group input:focus,
    .ward-form-group select:focus,
    .ward-form-group textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .ward-form-group textarea {
        resize: vertical;
        min-height: 110px;
    }

    .ward-form-actions {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-top: 25px;
    }

    .ward-save-btn {
        background: #2563eb;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 7px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    .ward-save-btn:hover {
        background: #1d4ed8;
    }

    .ward-cancel-btn {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
    }

    .ward-cancel-btn:hover {
        text-decoration: underline;
    }

    @media (max-width: 600px) {
        .ward-create-container {
            margin: 15px;
            padding: 20px;
        }

        .ward-create-container h2 {
            font-size: 23px;
        }

        .ward-form-actions {
            flex-wrap: wrap;
        }
    }
</style>

<div class="ward-create-container">

    <h2>Add New Ward</h2>

    <form method="POST" action="/wards/store">

        <!-- CSRF Protection -->
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
        >

        <!-- Ward Name -->
        <div class="ward-form-group">

            <label for="ward_name">Ward Name *</label>

            <input
                type="text"
                id="ward_name"
                name="ward_name"
                placeholder="Enter ward name"
                required
            >

        </div>

        <!-- Ward Code -->
        <div class="ward-form-group">

            <label for="ward_code">Ward Code *</label>

            <input
                type="text"
                id="ward_code"
                name="ward_code"
                placeholder="Example: WRD001"
                required
            >

        </div>

        <!-- Ward Type -->
        <div class="ward-form-group">

            <label for="ward_type">Ward Type *</label>

            <select
                id="ward_type"
                name="ward_type"
                required
            >

                <option value="">Select Ward Type</option>
                <option value="General">General</option>
                <option value="ICU">ICU</option>
                <option value="Emergency">Emergency</option>
                <option value="Maternity">Maternity</option>
                <option value="Pediatric">Pediatric</option>
                <option value="Private">Private</option>
                <option value="Other">Other</option>

            </select>

        </div>

        <!-- Description -->
        <div class="ward-form-group">

            <label for="description">Description</label>

            <textarea
                id="description"
                name="description"
                rows="4"
                placeholder="Enter ward description..."
            ></textarea>

        </div>

        <!-- Buttons -->
        <div class="ward-form-actions">

            <button
                type="submit"
                class="ward-save-btn"
            >
                Save Ward
            </button>

            <a
                href="/wards"
                class="ward-cancel-btn"
            >
                Cancel
            </a>

        </div>

    </form>

</div>
