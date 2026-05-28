<?php
include '../includes/db.php';
include '../includes/header.php';

// Get statistics
// Total books
$total_books = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM books");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_books = $row['count'];
}
$stmt->close();

// Total students
$total_students = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM students");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_students = $row['count'];
}
$stmt->close();

// Issued books count
$issued_books = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM issued_books WHERE status = 'issued'");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $issued_books = $row['count'];
}
$stmt->close();

// Returned books count
$returned_books = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM issued_books WHERE status = 'returned'");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $returned_books = $row['count'];
}
$stmt->close();

// Available books
$available_books = 0;
$stmt = $conn->prepare("SELECT SUM(available_quantity) as total FROM books");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $available_books = $row['total'] ? $row['total'] : 0;
}
$stmt->close();
?>

<div class="page-header">
    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
    <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card stat-card books h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Total Books</h6>
                        <h3 class="card-title mb-0"><?php echo $total_books; ?></h3>
                    </div>
                    <div class="stat-icon text-primary">
                        <i class="bi bi-book"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card stat-card students h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Total Students</h6>
                        <h3 class="card-title mb-0"><?php echo $total_students; ?></h3>
                    </div>
                    <div class="stat-icon text-success">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card stat-card issued h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Issued Books</h6>
                        <h3 class="card-title mb-0"><?php echo $issued_books; ?></h3>
                    </div>
                    <div class="stat-icon text-warning">
                        <i class="bi bi-bookmark-plus"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card stat-card returned h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Returned Books</h6>
                        <h3 class="card-title mb-0"><?php echo $returned_books; ?></h3>
                    </div>
                    <div class="stat-icon text-danger">
                        <i class="bi bi-bookmark-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card stat-card available h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Available Books</h6>
                        <h3 class="card-title mb-0"><?php echo $available_books; ?></h3>
                    </div>
                    <div class="stat-icon text-info">
                        <i class="bi bi-book-half"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="add_book.php" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Add Book
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="add_student.php" class="btn btn-success w-100">
                            <i class="bi bi-person-plus"></i> Add Student
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="issue_book.php" class="btn btn-warning w-100">
                            <i class="bi bi-bookmark-plus"></i> Issue Book
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="return_book.php" class="btn btn-danger w-100">
                            <i class="bi bi-bookmark-check"></i> Return Book
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>
