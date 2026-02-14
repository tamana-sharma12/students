<?php
require_once '../controllers/StudentController.php';
$studentCtrl = new StudentController();

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

// Security: Check if admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: admin_login.php");
    exit;
}

$db = $studentCtrl->db;

// --- PAGINATION LOGIC ---
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalRecords = $db->query("SELECT COUNT(*) FROM student")->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch students
$stmt = $db->prepare("
    SELECT s.*, u.name, u.email 
    FROM student s 
    JOIN user u ON s.user_id = u.id 
    ORDER BY s.id DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats
$totalStudents = $totalRecords;
$totalUsers = $db->query("SELECT COUNT(*) FROM user")->fetchColumn();
$totalAdmins = $db->query("SELECT COUNT(*) FROM user WHERE role='admin'")->fetchColumn();

// Add/Update/Delete Logic
if($_POST){
    if(isset($_POST['add_student'])){
        $studentCtrl->add($_POST);
        $_SESSION['success'] = "Student added successfully";
        header("Location: admin_dashboard.php");
        exit;
    }
    if(isset($_POST['update_student'])){
        $db->prepare("UPDATE user SET name=?, email=? WHERE id=?")->execute([$_POST['name'], $_POST['email'], $_POST['user_id']]);
        $db->prepare("UPDATE student SET class=?, phone=? WHERE id=?")->execute([$_POST['class'], $_POST['phone'], $_POST['id']]);
        $_SESSION['success'] = "Student updated successfully";
        header("Location: admin_dashboard.php");
        exit;
    }
}

if(isset($_GET['delete'])){
    $studentCtrl->delete($_GET['delete']);
    $_SESSION['success'] = "Student deleted successfully";
    header("Location: admin_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | SMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #fff0f5; font-family: 'Poppins', sans-serif; }
        .dashboard { background: #fff; border-radius: 20px; padding: 30px; margin-top: 30px; box-shadow: 0 10px 30px rgba(255, 133, 162, 0.2); border: 1px solid #ffdae3; margin-bottom: 50px; }
        .stat-card { border: none; border-radius: 15px; color: white; padding: 25px; transition: 0.3s; text-align: center; }
        .stat-card:hover { transform: translateY(-5px); }
        .table thead { background: #ff85a2; color: #fff; }
        .btn-edit { background: #ffdae3; color: #ff5e84; border: none; }
        .btn-update { background: #ff85a2; color: #fff; border: none; }
        .btn-delete { background: #ffebee; color: #ff4d4d; border: none; }
        .add-card { background: #fff9fa; border: 2px dashed #ff85a2; border-radius: 15px; }
        #suggestionBox { border: 1px solid #ffdae3; border-radius: 10px; overflow: hidden; background: white; z-index: 1000; }
        .list-group-item-action:hover { background-color: #fff0f5; color: #ff85a2; }
    </style>
</head>
<body>

<div class="container">
    <div class="dashboard">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h3><i class="fas fa-user-shield"></i> Admin Panel</h3>
            <a href="logout.php" class="btn btn-danger btn-sm px-4">Logout</a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="stat-card" style="background: #ff85a2;"><h6>Students</h6><h2><?= $totalStudents ?></h2></div></div>
            <div class="col-md-4"><div class="stat-card" style="background: #ff5e84;"><h6>Total Users</h6><h2><?= $totalUsers ?></h2></div></div>
            <div class="col-md-4"><div class="stat-card" style="background: #ffb6c1;"><h6>Admins</h6><h2><?= $totalAdmins ?></h2></div></div>
        </div>

        <div class="mb-4 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-color: #ffdae3;">
                    <i class="fas fa-search" style="color: #ff85a2;"></i>
                </span>
                <input type="text" id="studentSearch" class="form-control border-start-0" 
                       placeholder="Search student name..." style="border-color: #ffdae3; box-shadow: none;" autocomplete="off">
            </div>
            <div id="suggestionBox" class="list-group position-absolute w-100 shadow d-none"></div>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success border-0"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 style="color: #ff5e84;"><i class="fas fa-list"></i> Student Directory</h5>
            <button class="btn btn-success btn-sm px-4 py-2" onclick="toggleAddForm()">
                <i class="fas fa-plus"></i> Add New Student
            </button>
        </div>

        <div id="addStudentForm" class="add-card p-4 mb-4 d-none">
            <h5 style="color: #ff5e84;">Fill Student Details</h5>
            <form method="POST" class="row g-3 mt-1">
                <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Full Name" required></div>
                <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                <div class="col-md-2"><input type="text" name="class" class="form-control" placeholder="Class" required></div>
                <div class="col-md-2"><input type="text" name="phone" class="form-control" placeholder="Phone" required></div>
                <div class="col-md-2"><button name="add_student" class="btn btn-update w-100">Save</button></div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Class</th><th>Phone</th><th>Action</th></tr>
                </thead>
                <tbody id="studentTableBody">
                <?php foreach($students as $s): ?>
                    <tr class="student-row">
                        <form method="POST">
                            <td><span class="badge rounded-pill bg-light text-dark">#<?= $s['id']; ?></span></td>
                            <td><input name="name" value="<?= htmlspecialchars($s['name']); ?>" class="form-control form-control-sm border-0 bg-transparent student-name" disabled></td>
                            <td><input name="email" value="<?= htmlspecialchars($s['email']); ?>" class="form-control form-control-sm border-0 bg-transparent" disabled></td>
                            <td><input name="class" value="<?= htmlspecialchars($s['class']); ?>" class="form-control form-control-sm border-0 bg-transparent" disabled></td>
                            <td><input name="phone" value="<?= htmlspecialchars($s['phone']); ?>" class="form-control form-control-sm border-0 bg-transparent" disabled></td>
                            <td>
                                <input type="hidden" name="id" value="<?= $s['id']; ?>">
                                <input type="hidden" name="user_id" value="<?= $s['user_id']; ?>">
                                <button type="button" class="btn btn-edit btn-sm" onclick="enableEdit(this)"><i class="fas fa-edit"></i></button>
                                <button type="submit" name="update_student" class="btn btn-update btn-sm d-none"><i class="fas fa-check"></i></button>
                                <a href="?delete=<?= $s['id']; ?>" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// --- Updated Search API Link ---
document.getElementById('studentSearch').addEventListener('input', function() {
    let query = this.value;
    let suggestionBox = document.getElementById('suggestionBox');

    if (query.length > 0) {
        // Correct path to your API file
        fetch('../api/search_api.php?term=' + query)
        .then(response => response.json())
        .then(data => {
            suggestionBox.innerHTML = "";
            if (data.length > 0) {
                suggestionBox.classList.remove('d-none');
                data.forEach(item => {
                    let a = document.createElement('a');
                    a.href = "#";
                    a.className = "list-group-item list-group-item-action border-0";
                    a.innerHTML = `<strong>${item.name}</strong> <small class="text-muted ms-2">${item.email}</small>`;
                    a.onclick = function(e) {
                        e.preventDefault();
                        document.getElementById('studentSearch').value = item.name;
                        suggestionBox.classList.add('d-none');
                        filterTable(item.name);
                    };
                    suggestionBox.appendChild(a);
                });
            } else {
                suggestionBox.classList.add('d-none');
            }
        });
    } else {
        suggestionBox.classList.add('d-none');
        resetTable();
    }
});

function filterTable(name) {
    let rows = document.querySelectorAll('.student-row');
    rows.forEach(row => {
        let studentName = row.querySelector('.student-name').value.toLowerCase();
        row.style.display = studentName.includes(name.toLowerCase()) ? "" : "none";
    });
}

function resetTable() {
    document.querySelectorAll('.student-row').forEach(row => row.style.display = "");
}

function toggleAddForm(){ document.getElementById("addStudentForm").classList.toggle("d-none"); }

function enableEdit(btn){
    let row = btn.closest("tr");
    row.querySelectorAll("input").forEach(i => {
        if(i.type !== "hidden") {
            i.disabled = false;
            i.classList.remove("border-0", "bg-transparent");
            i.classList.add("border");
        }
    });
    btn.classList.add("d-none");
    row.querySelector("[name='update_student']").classList.remove("d-none");
}
</script>
</body>
</html>