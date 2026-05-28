<?php
include '../includes/db.php';
include '../includes/header.php';

$message = '';
$message_type = '';

// Get students and books for dropdowns
$students = [];
$books = [];

$stmt = $conn->prepare("SELECT * FROM students ORDER BY name ASC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM books WHERE available_quantity > 0 ORDER BY title ASC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = intval($_POST['student_id']);
    $book_id = intval($_POST['book_id']);
    $issue_date = $_POST['issue_date'];
    $return_date = $_POST['return_date'];
    
    // Validate inputs
    if (empty($student_id) || empty($book_id) || empty($issue_date) || empty($return_date)) {
        $message = 'Please fill in all fields.';
        $message_type = 'danger';
    } elseif ($return_date <= $issue_date) {
        $message = 'Return date must be after issue date.';
        $message_type = 'danger';
    } else {
        // Check if book is available
        $stmt = $conn->prepare("SELECT available_quantity FROM books WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        $stmt->close();
        
        if ($book['available_quantity'] <= 0) {
            $message = 'This book is not available for issue.';
            $message_type = 'danger';
        } else {
            // Insert issue record
            $stmt = $conn->prepare("INSERT INTO issued_books (book_id, student_id, issue_date, return_date, status) VALUES (?, ?, ?, ?, 'issued')");
            $stmt->bind_param("iiss", $book_id, $student_id, $issue_date, $return_date);
            
            if ($stmt->execute()) {
                // Decrease available quantity
                $stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE id = ?");
                $stmt->bind_param("i", $book_id);
                $stmt->execute();
                $stmt->close();
                
                $message = 'Book issued successfully!';
                $message_type = 'success';
                
                // Refresh books list
                $books = [];
                $stmt = $conn->prepare("SELECT * FROM books WHERE available_quantity > 0 ORDER BY title ASC");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $books[] = $row;
                }
                $stmt->close();
            } else {
                $message = 'Error issuing book: ' . $conn->error;
                $message_type = 'danger';
            }
            $stmt->close();
        }
    }
}
?>

<div class="page-header">
    <h2><i class="bi bi-bookmark-plus"></i> Issue Book</h2>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="student_id" class="form-label">Select Student *</label>
                    <select class="form-select" id="student_id" name="student_id" required>
                        <option value="">-- Select Student --</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>">
                                <?php echo htmlspecialchars($student['name'] . ' - ' . $student['email']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="book_id" class="form-label">Select Book *</label>
                    <select class="form-select" id="book_id" name="book_id" required>
                        <option value="">-- Select Book --</option>
                        <?php foreach ($books as $book): ?>
                            <option value="<?php echo $book['id']; ?>">
                                <?php echo htmlspecialchars($book['title'] . ' by ' . $book['author'] . ' (Available: ' . $book['available_quantity'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="issue_date" class="form-label">Issue Date *</label>
                    <input type="date" class="form-control" id="issue_date" name="issue_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="return_date" class="form-label">Return Date *</label>
                    <input type="date" class="form-control" id="return_date" name="return_date" required>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-bookmark-plus"></i> Issue Book
                </button>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </form>
    </div>
</div>

<?php
include '../includes/footer.php';
?>
