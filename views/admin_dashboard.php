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

// Stats (Keeping these in PHP for quick load)
$totalStudents = $db->query("SELECT COUNT(*) FROM student")->fetchColumn();
$totalUsers = $db->query("SELECT COUNT(*) FROM user")->fetchColumn();
$totalAdmins = $db->query("SELECT COUNT(*) FROM user WHERE role='admin'")->fetchColumn();

// Add Logic
if(isset($_POST['add_student'])){
    $studentCtrl->add($_POST);
    $_SESSION['success'] = "Student added successfully";
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Load data on page load
document.addEventListener('DOMContentLoaded', loadStudents);

// --- 0. FETCH STUDENTS (Webservice Consumption) ---
function loadStudents() {
    fetch('../api/get_students_api.php')
    .then(res => res.json())
    .then(data => {
        let tbody = document.getElementById('studentTableBody');
        tbody.innerHTML = ''; 
        data.forEach(s => {
            tbody.innerHTML += `
                <tr class="student-row" id="row-${s.id}">
                    <td><span class="badge rounded-pill bg-light text-dark">#${s.id}</span></td>
                    <td><input id="name-${s.id}" value="${s.name}" class="form-control form-control-sm border-0 bg-transparent student-name" disabled></td>
                    <td><input id="email-${s.id}" value="${s.email}" class="form-control form-control-sm border-0 bg-transparent" disabled></td>
                    <td><input id="class-${s.id}" value="${s.class}" class="form-control form-control-sm border-0 bg-transparent" disabled></td>
                    <td><input id="phone-${s.id}" value="${s.phone}" class="form-control form-control-sm border-0 bg-transparent" disabled></td>
                    <td>
                        <input type="hidden" id="user-${s.id}" value="${s.user_id}">
                        <button type="button" class="btn btn-edit btn-sm edit-btn" onclick="enableEdit(${s.id}, this)"><i class="fas fa-edit"></i></button>
                        <button type="button" class="btn btn-update btn-sm update-btn d-none" onclick="updateStudent(${s.id}, this)"><i class="fas fa-check"></i></button>
                        <button type="button" class="btn btn-delete btn-sm" onclick="deleteStudent(${s.id})"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>`;
        });
    })
    .catch(err => console.error("Error loading students:", err));
}

// --- 1. DELETE STUDENT (Webservice) ---
function deleteStudent(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This student's record will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff85a2',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../api/delete_student_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire('Deleted!', 'Student removed successfully.', 'success');
                    let row = document.getElementById('row-' + id);
                    row.style.opacity = "0";
                    setTimeout(() => row.remove(), 500);
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            });
        }
    });
}

// --- 2. UPDATE STUDENT (Webservice) ---
function updateStudent(id, btn) {
    let formData = new FormData();
    formData.append('id', id);
    formData.append('user_id', document.getElementById('user-' + id).value);
    formData.append('name', document.getElementById('name-' + id).value);
    formData.append('email', document.getElementById('email-' + id).value);
    formData.append('class', document.getElementById('class-' + id).value);
    formData.append('phone', document.getElementById('phone-' + id).value);
    formData.append('action', 'update_student');

    fetch('../api/update_student_api.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire('Success!', 'Updated successfully.', 'success');
            disableEdit(id, btn);
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    });
}

function enableEdit(id, btn){
    let row = document.getElementById('row-' + id);
    row.querySelectorAll("input").forEach(i => {
        if(i.type !== "hidden") {
            i.disabled = false;
            i.classList.remove("border-0", "bg-transparent");
            i.classList.add("border", "p-1");
            i.style.background = "#fff";
        }
    });
    btn.classList.add("d-none");
    row.querySelector(".update-btn").classList.remove("d-none");
}

function disableEdit(id, btn){
    let row = document.getElementById('row-' + id);
    row.querySelectorAll("input").forEach(i => {
        if(i.type !== "hidden") {
            i.disabled = true;
            i.classList.add("border-0", "bg-transparent");
            i.classList.remove("border", "p-1");
            i.style.background = "transparent";
        }
    });
    row.querySelector(".update-btn").classList.add("d-none");
    row.querySelector(".edit-btn").classList.remove("d-none");
}

function toggleAddForm(){ document.getElementById("addStudentForm").classList.toggle("d-none"); }

// Search Logic
document.getElementById('studentSearch').addEventListener('input', function() {
    let query = this.value.toLowerCase();
    document.querySelectorAll('.student-row').forEach(row => {
        let name = row.querySelector('.student-name').value.toLowerCase();
        row.style.display = name.includes(query) ? "" : "none";
    });
});
</script>
</body>
</html>