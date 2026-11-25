
<style>
    .edit-ward-container {
        max-width: 750px;
        margin: 30px auto;
        padding: 20px;
    }

    .edit-ward-card {
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        border: 1px solid #e5eaf1;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
    }

    .edit-ward-header {
        margin-bottom: 25px;
        padding-bottom: 18px;
        border-bottom: 1px solid #e5eaf1;
    }

    .edit-ward-header h2 {
        color: #1e293b;
        font-size: 25px;
        margin: 0 0 8px;
    }

    .edit-ward-header p {
        color: #64748b;
        font-size: 14px;
        margin: 0;
    }

    .edit-ward-form .form-group {
        margin-bottom: 20px;
    }

    .edit-ward-form label {
        display: block;
        color: #334155;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .edit-ward-form input,
    .edit-ward-form select,
    .edit-ward-form textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #ffffff;
        color: #1e293b;
        font-size: 14px;
        outline: none;
        transition: 0.2s ease;
    }

    .edit-ward-form input:focus,
    .edit-ward-form select:focus,
    .edit-ward-form textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .edit-ward-form textarea {
        resize: vertical;
        min-height: 110px;
        font-family: inherit;
    }

    .edit-ward-form input:hover,
    .edit-ward-form select:hover,
    .edit-ward-form textarea:hover {
        border-color: #94a3b8;
    }

    .edit-ward-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid #e5eaf1;
        flex-wrap: wrap;
    }

    .edit-ward-btn {
        display: inline-block;
        padding: 12px 22px;
        border: none;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .update-ward-btn {
        background: #2563eb;
        color: #ffffff;
    }

    .update-ward-btn:hover {
        background: #1d4ed8;
    }

    .cancel-ward-btn {
        background: #e2e8f0;
        color: #334155;
    }

    .cancel-ward-btn:hover {
        background: #cbd5e1;
    }

    @media (max-width: 600px) {
        .edit-ward-container {
            padding: 10px;
        }

        .edit-ward-card {
            padding: 20px;
        }

        .edit-ward-header h2 {
            font-size: 21px;
        }

        .edit-ward-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .edit-ward-btn {
            text-align: center;
        }
    }
</style>

<div class="edit-ward-container">

    <div class="edit-ward-card">

        <div class="edit-ward-header">
            <h2>Edit Ward</h2>
            <p>Update the ward information below.</p>
        </div>

        <form class="edit-ward-form"
              method="POST"
              action="/wards/update">

            <!-- CSRF Token -->
            <input type="hidden"
                   name="csrf_token"
                   value="<?= htmlspecialchars($csrfToken) ?>">

            <!-- Ward ID -->
            <input type="hidden"
                   name="id"
                   value="<?= (int)$ward['id'] ?>">

            <!-- Ward Name -->
            <div class="form-group">
                <label for="ward_name">Ward Name</label>

                <input type="text"
                       id="ward_name"
                       name="ward_name"
                       value="<?= htmlspecialchars($ward['ward_name'] ?? '') ?>"
                       placeholder="Enter ward name"
                       required>
            </div>

            <!-- Ward Code -->
            <div class="form-group">
                <label for="ward_code">Ward Code</label>

                <input type="text"
                       id="ward_code"
                       name="ward_code"
                       value="<?= htmlspecialchars($ward['ward_code'] ?? '') ?>"
                       placeholder="Enter ward code"
                       required>
            </div>

            <!-- Ward Type -->
            <div class="form-group">
                <label for="ward_type">Ward Type</label>

                <?php
                $types = [
                    'General',
                    'ICU',
                    'Emergency',
                    'Maternity',
                    'Pediatric',
                    'Private',
                    'Other'
                ];
                ?>

                <select name="ward_type"
                        id="ward_type"
                        required>

                    <?php foreach ($types as $type): ?>

                        <option value="<?= htmlspecialchars($type) ?>"
                            <?= ($ward['ward_type'] ?? '') === $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type) ?>
                        </option>

                    <?php endforeach; ?>

                </select>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Description</label>

                <textarea name="description"
                          id="description"
                          rows="4"
                          placeholder="Enter ward description"><?= htmlspecialchars($ward['description'] ?? '') ?></textarea>
            </div>

            <!-- Buttons -->
            <div class="edit-ward-actions">

                <button type="submit"
                        class="edit-ward-btn update-ward-btn">
                    Update Ward
                </button>

                <a href="/wards"
                   class="edit-ward-btn cancel-ward-btn">
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>
