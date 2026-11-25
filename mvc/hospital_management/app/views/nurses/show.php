
<?php
function nurseShowEscape($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Details</title>

    <style>
        body { font-family: Arial; background: #f4f6f9; padding: 25px; }
        .container { max-width: 750px; margin: auto; background: white; padding: 25px; border-radius: 10px; }
        h2 { color: #263b5e; }
        .detail { padding: 12px; border-bottom: 1px solid #eee; }
        .detail strong { display: inline-block; width: 180px; }
        a { display: inline-block; margin-top: 20px; padding: 10px 16px; background: #263b5e; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>

<body>

<div class="container">

    <h2>Nurse Details</h2>

    <div class="detail">
        <strong>Nurse Number:</strong>
        <?= nurseShowEscape($nurse['nurse_number']) ?>
    </div>

    <div class="detail">
        <strong>Name:</strong>
        <?= nurseShowEscape($nurse['first_name'] . ' ' . $nurse['last_name']) ?>
    </div>

    <div class="detail">
        <strong>Username:</strong>
        <?= nurseShowEscape($nurse['username']) ?>
    </div>

    <div class="detail">
        <strong>Department:</strong>
        <?= nurseShowEscape($nurse['department_name']) ?>
    </div>

    <div class="detail">
        <strong>Gender:</strong>
        <?= nurseShowEscape($nurse['gender']) ?>
    </div>

    <div class="detail">
        <strong>Qualification:</strong>
        <?= nurseShowEscape($nurse['qualification']) ?>
    </div>

    <div class="detail">
        <strong>Phone:</strong>
        <?= nurseShowEscape($nurse['phone']) ?>
    </div>

    <div class="detail">
        <strong>Email:</strong>
        <?= nurseShowEscape($nurse['email']) ?>
    </div>

    <div class="detail">
        <strong>Experience:</strong>
        <?= nurseShowEscape($nurse['experience_years']) ?> years
    </div>

    <div class="detail">
        <strong>Shift:</strong>
        <?= nurseShowEscape($nurse['shift']) ?>
    </div>

    <div class="detail">
        <strong>Address:</strong>
        <?= nl2br(nurseShowEscape($nurse['address'])) ?>
    </div>

    <div class="detail">
        <strong>Status:</strong>
        <?= (int)$nurse['status'] === 1 ? 'Active' : 'Inactive' ?>
    </div>

    <a href="/nurses">Back to Nurse List</a>

    <a href="/nurses/edit?id=<?= (int)$nurse['id'] ?>">Edit Nurse</a>

</div>

</body>
</html>
