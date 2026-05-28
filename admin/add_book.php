<?php
include '../includes/db.php';
include '../includes/header.php';

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = trim($_POST['category']);
    $isbn = trim($_POST['isbn']);
    $quantity = intval($_POST['quantity']);
    $added_date = date('Y-m-d');
    
    // Validate inputs
    if (empty($title) || empty($author) || empty($category) || empty($isbn) || $quantity <= 0) {
        $message = 'Please fill in all fields with valid values.';
        $message_type = 'danger';
    } else {
        // Check if ISBN already exists
        $stmt = $conn->prepare("SELECT id FROM books WHERE isbn = ?");
        $stmt->bind_param("s", $isbn);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = 'A book with this ISBN already exists.';
            $message_type = 'danger';
        } else {
            // Insert new book
            $stmt = $conn->prepare("INSERT INTO books (title, author, category, isbn, quantity, available_quantity, added_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssii", $title, $author, $category, $isbn, $quantity, $quantity, $added_date);
            
            if ($stmt->execute()) {
                $message = 'Book added successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error adding book: ' . $conn->error;
                $message_type = 'danger';
            }
            $stmt->close();
        }
        $stmt->close();
    }
}
?>

<div class="page-header">
    <h2><i class="bi bi-plus-circle"></i> Add New Book</h2>
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
                    <label for="title" class="form-label">Book Title *</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="author" class="form-label">Author *</label>
                    <input type="text" class="form-control" id="author" name="author" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">Category *</label>
                    <input type="text" class="form-control" id="category" name="category" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="isbn" class="form-label">ISBN *</label>
                    <input type="text" class="form-control" id="isbn" name="isbn" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="quantity" class="form-label">Quantity *</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Book
                </button>
                <a href="manage_books.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Books
                </a>
            </div>
        </form>
    </div>
</div>

<?php
include '../includes/footer.php';
?>
