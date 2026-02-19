<?php
session_start();
require_once "../config/Database.php";
$conn = Database::connect();

// Security: Check karein ki user login hai aur student hai
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .btn { padding: 12px 25px; border-radius: 10px; border: none; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; transition: 0.3s; text-align: center; }
        .btn-main { background: var(--primary); color: white; box-shadow: 0 5px 15px rgba(255,133,162,0.3); }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; box-sizing: border-box;}
        .phone-update-btn { background: #f0f0f0; border: 1px solid #ddd; padding: 5px 10px; border-radius: 5px; font-size: 12px; cursor: pointer; margin-top: 5px; color: var(--primary); font-weight: bold;}
        
        /* Modal Overlay */
        #phoneModal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:9999; }
        .modal-body { background:white; padding:30px; border-radius:20px; width:350px; position:relative; box-shadow: 0 15px 50px rgba(0,0,0,0.2); }
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
                        <label>Phone Number (OTP Required)</label>
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

<div id="phoneModal">
    <div class="modal-body">
        <button onclick="closePhoneModal()" style="position:absolute;top:15px;right:15px;border:none;background:none;font-size:20px;cursor:pointer;">&times;</button>
        <div id="modal-content-area">
            <h3>Update Phone</h3>
            <p style="font-size:13px; color:#666; margin-bottom:15px;">Enter your new 10-digit number:</p>
            <input type="text" id="new_phone_val" placeholder="9876543210" style="margin-bottom:15px;">
            <button type="button" class="btn btn-main" style="width:100%;" onclick="sendOTP()">Send OTP</button>
        </div>
    </div>
</div>

<script>
// 1. OPEN MODAL
function openPhoneModal() {
    document.getElementById("phoneModal").style.display = "flex";
}

// 2. CLOSE MODAL
function closePhoneModal() {
    document.getElementById("phoneModal").style.display = "none";
}

// 3. STEP 1: SEND OTP (WEBSERVICE)
async function sendOTP() {
    const phone = document.getElementById('new_phone_val').value.trim();

    if (!/^[0-9]{10}$/.test(phone)) {
        Swal.fire('Error', 'Please enter a valid 10-digit phone number!', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'send_otp');
    formData.append('phone', phone);

    try {
        const response = await fetch('../api/phone_handler.php', { method: 'POST', body: formData });
        const res = await response.json();

        if (res.status === "success") {
            document.getElementById('modal-content-area').innerHTML = `
                <h3>Verify OTP</h3>
                <p style="font-size:13px;">OTP sent to: <b>${phone}</b></p>
                <input type="text" id="otp_val" placeholder="Enter 6-Digit OTP" style="margin-bottom:15px;">
                <button type="button" class="btn btn-main" style="width:100%;" onclick="verifyOTP()">Verify & Update</button>
                <p style="font-size:11px; color:#ff85a2; margin-top:10px; background:#fff0f3; padding:5px; border-radius:5px; text-align:center;">Demo OTP: ${res.mock_otp}</p>
            `;
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'API connect nahi ho payi!', 'error');
    }
}

// 4. STEP 2: VERIFY OTP (WEBSERVICE)
async function verifyOTP() {
    const otp = document.getElementById('otp_val').value.trim();

    const formData = new FormData();
    formData.append('action', 'verify_otp');
    formData.append('otp', otp);

    try {
        const response = await fetch('../api/phone_handler.php', { method: 'POST', body: formData });
        const res = await response.json();

        if (res.status === "success") {
            Swal.fire({ icon: 'success', title: 'Done!', text: res.message, confirmButtonColor: '#ff85a2' })
            .then(() => { location.href = 'student_dashboard.php'; });
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Verification fail ho gayi!', 'error');
    }
}
</script>

</body>
</html>