<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Patient Registration</title>
        <link rel="stylesheet" href="<?= htmlspecialchars(APP_BASE_URL) ?>/assets/css/style.css">
    </head>

    <body>
        <?php require __DIR__ . '/../partials/navigation.php'; ?>
        <h2>Patient Registration</h2>
            <form action="<?= htmlspecialchars(APP_BASE_URL) ?>/patients/store" method="POST">
            <label>Patient Name</label>
            <input type="text" name="patient_name" required>
            <br><br>

            <label>Gender</label>
           <select name="gender" required>
                    <option value= "">Select gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="other">Other</option>
           </select>
           <br><br>

           <label>Date of Birth</label>
           <input type="date" name="date_of_birth" >
           <br><br>

           <label>Phone</label>
           <input type="text" name="phone">
           <br><br>

           <label>Email</label>
           <input type="email" name="email">
           <br><br>

           <label>Address</label>
           <textarea name="address"></textarea>
           <br><br>

           <button type="submit">Register Patient</button>

        </form>
    </body>
</html>