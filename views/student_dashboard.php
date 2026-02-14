<?php
session_start();
require_once "../config/Database.php";
$conn = Database::connect();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";

// --- 1. UPDATE LOGIC (For All Fields except Phone) ---
if (isset($_POST['update_profile'])) {
    $name    = $_POST['name'];
    $class   = $_POST['class'];
    $address = $_POST['address'];
    $gender  = $_POST['gender'];

    // Phone ko yahan se hata diya hai kyunki wo OTP se update hoga
    $upd = $conn->prepare("UPDATE user SET name=:n, class=:c, address=:a, gender=:g WHERE id=:uid");
    $upd->execute([':n'=>$name, ':c'=>$class, ':a'=>$address, ':g'=>$gender, ':uid'=>$user_id]);
    $success_msg = "Profile Updated Successfully! ✅";
}

// --- 2. PHOTO UPLOAD LOGIC ---
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $targetDir = "../assets/uploads/";
    $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetDir . $fileName)) {
        $conn->prepare("UPDATE user SET profile_pic = ? WHERE id = ?")->execute([$fileName, $user_id]);
        header("Location: student_dashboard.php");
        exit();
    }
}

// --- 3. FETCH ALL DATA ---
$stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$editMode = isset($_GET['action']) && $_GET['action'] == 'edit';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ScholarTrack | Student Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { --primary: #ff85a2; --bg: #fff5f7; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; display: flex; }
        .sidebar { width: 240px; background: white; height: 100vh; padding: 20px; border-right: 1px solid #eee; position: fixed; }
        .sidebar h2 { color: var(--primary); margin-bottom: 30px; }
        .sidebar a { display: block; padding: 12px; color: #555; text-decoration: none; border-radius: 8px; margin-bottom: 5px; }
        .sidebar a.active { background: var(--bg); color: var(--primary); font-weight: bold; }
        .main { margin-left: 240px; flex: 1; padding: 40px; display: flex; justify-content: center; }
        .profile-card { background: white; width: 100%; max-width: 800px; border-radius: 24px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); position: relative; }
        .img-area { position: relative; width: 140px; height: 140px; margin: 0 auto 20px; }
        .profile-img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary); padding: 3px; background: white; }
        .cam-btn { position: absolute; bottom: 5px; right: 5px; background: var(--primary); color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid white; transition: 0.3s; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px; }
        .info-box { background: #fcfcfc; padding: 15px 20px; border-radius: 15px; border: 1px solid #f1f1f1; display: flex; align-items: center; gap: 15px; }
        .info-box i { color: var(--primary); font-size: 1.2rem; width: 25px; }
        .info-box label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-box strong { display: block; color: #333; font-size: 15px; }
        .btn { padding: 12px 25px; border-radius: 10px; border: none; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; transition: 0.3s; }
        .btn-main { background: var(--primary); color: white; box-shadow: 0 5px 15px rgba(255,133,162,0.3); }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; box-sizing: border-box;}
        
        /* New Style for Phone Update button */
        .phone-update-btn { background: #f0f0f0; border: 1px solid #ddd; padding: 5px 10px; border-radius: 5px; font-size: 12px; cursor: pointer; margin-top: 5px; color: var(--primary); font-weight: bold;}
    </style>
</head>
<body>

<div class="sidebar">
    <h2>ScholarTrack</h2>
    <a href="#" class="active"><i class="fas fa-user-circle"></i> My Profile</a>
    <a href="logout.php" style="margin-top:50px; color:#ff4757;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
    <div class="profile-card">
        <?php if($success_msg): ?>
            <div style="background:#d4edda; color:#155724; padding:10px; border-radius:8px; margin-bottom:20px; text-align:center;"><?= $success_msg ?></div>
        <?php endif; ?>

        <div style="text-align:center;">
            <div class="img-area">
                <?php $pic = !empty($student['profile_pic']) ? $student['profile_pic'] : 'default.png'; ?>
                <img src="../assets/uploads/<?= $pic ?>" class="profile-img">
                <label for="photo-up" class="cam-btn"><i class="fas fa-camera"></i></label>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="file" name="photo" id="photo-up" style="display:none;" onchange="this.form.submit()">
                </form>
            </div>
            <h2 style="margin:0;"><?= htmlspecialchars($student['name']) ?></h2>
            <p style="color:#888; margin:5px 0;">Student ID: #<?= $student['id'] ?></p>
        </div>

        <?php if(!$editMode): ?>
            <div class="info-grid">
                <div class="info-box"><i class="fas fa-envelope"></i><div><label>Email</label><strong><?= $student['email'] ?></strong></div></div>
                <div class="info-box"><i class="fas fa-phone"></i><div><label>Phone</label><strong><?= $student['phone'] ?? '---' ?></strong></div></div>
                <div class="info-box"><i class="fas fa-graduation-cap"></i><div><label>Class</label><strong><?= $student['class'] ?? '---' ?></strong></div></div>
                <div class="info-box"><i class="fas fa-venus-mars"></i><div><label>Gender</label><strong><?= $student['gender'] ?? '---' ?></strong></div></div>
                <div class="info-box" style="grid-column: span 2;"><i class="fas fa-map-marker-alt"></i><div><label>Address</label><strong><?= $student['address'] ?? '---' ?></strong></div></div>
            </div>
            <div style="text-align:center; margin-top:30px;">
                <a href="?action=edit" class="btn btn-main">Edit Profile Information</a>
            </div>

        <?php else: ?>
            <form method="POST">
                <div class="info-grid">
                    <div><label>Full Name</label><input type="text" name="name" value="<?= $student['name'] ?>"></div>
                    
                    <div>
                        <label>Phone Number (OTP Required to change)</label>
                        <input type="text" id="display_phone" value="<?= $student['phone'] ?>" readonly style="background: #f9f9f9; cursor: not-allowed;">
                        <button type="button" class="phone-update-btn" onclick="openPhoneModal()">Change Phone via OTP</button>
                    </div>

                    <div><label>Class</label><input type="text" name="class" value="<?= $student['class'] ?>"></div>
                    <div>
                        <label>Gender</label>
                        <select name="gender">
                            <option value="Male" <?= ($student['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($student['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= ($student['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    <div style="grid-column: span 2;"><label>Address</label><input type="text" name="address" value="<?= $student['address'] ?>"></div>
                </div>
                <div style="text-align:center; margin-top:30px;">
                    <button type="submit" name="update_profile" class="btn btn-main">Save All Changes</button>
                    <a href="student_dashboard.php" style="margin-left:15px; color:#666;">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
// Profile Update Logic (SweetAlert ke saath)
const profileForm = document.querySelector('form[method="POST"]');

if(profileForm) {
    profileForm.addEventListener('submit', async (e) => {
        // Sirf tab AJAX chalayein jab hum Edit Mode mein hon aur Save button dabayein
        if(e.submitter && e.submitter.name === 'update_profile') {
            e.preventDefault();
            
            const formData = new FormData(profileForm);
            
            try {
                // Aapki bani hui API file ko call karna
                const response = await fetch('../api/update_profile_api.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.message,
                        confirmButtonColor: '#ff85a2'
                    }).then(() => {
                        window.location.href = 'student_dashboard.php'; // Data refresh ke liye
                    });
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                console.error("Error:", error);
                Swal.fire('Error', 'Server connection fail ho gaya!', 'error');
            }
        }
    });
}
</script>

</body>
</html>



