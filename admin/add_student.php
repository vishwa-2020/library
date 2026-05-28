<?php
include '../includes/db.php';
include '../includes/header.php';

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($phone)) {
        $message = 'Please fill in all fields.';
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $message_type = 'danger';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = 'A student with this email already exists.';
            $message_type = 'danger';
        } else {
            // Insert new student
            $stmt = $conn->prepare("INSERT INTO students (name, email, phone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $phone);
            
            if ($stmt->execute()) {
                $message = 'Student added successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error adding student: ' . $conn->error;
                $message_type = 'danger';
            }
            $stmt->close();
        }
        $stmt->close();
    }
}
?>

<div class="page-header">
    <h2><i class="bi bi-person-plus"></i> Add New Student</h2>
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
                    <label for="name" class="form-label">Student Name *</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number *</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Add Student
                </button>
                <a href="manage_students.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Students
                </a>
            </div>
        </form>
    </div>
</div>

<?php
include '../includes/footer.php';
?>
