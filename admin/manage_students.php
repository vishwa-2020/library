<?php
include '../includes/db.php';
include '../includes/header.php';

// Handle delete request
if (isset($_GET['delete'])) {
    $student_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_students.php");
    exit();
}

// Handle search
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Get students
$students = [];
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY created_at DESC");
    $search_param = "%$search%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
} else {
    $stmt = $conn->prepare("SELECT * FROM students ORDER BY created_at DESC");
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();
?>

<div class="page-header">
    <h2><i class="bi bi-people"></i> Manage Students</h2>
</div>

<!-- Search and Add Button -->
<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="" class="search-box">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search students..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
    <div class="col-md-6 text-end">
        <a href="add_student.php" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Add New Student
        </a>
    </div>
</div>

<!-- Students Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No students found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo $student['id']; ?></td>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($student['created_at'])); ?></td>
                                <td>
                                    <a href="manage_students.php?delete=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this student?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>
