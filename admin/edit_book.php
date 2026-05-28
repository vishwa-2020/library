<?php
include '../includes/db.php';
include '../includes/header.php';

$message = '';
$message_type = '';

// Get book ID
if (!isset($_GET['id'])) {
    header("Location: manage_books.php");
    exit();
}

$book_id = intval($_GET['id']);

// Get book details
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: manage_books.php");
    exit();
}

$book = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = trim($_POST['category']);
    $isbn = trim($_POST['isbn']);
    $quantity = intval($_POST['quantity']);
    
    // Calculate new available quantity
    $issued_count = $book['quantity'] - $book['available_quantity'];
    $new_available = max(0, $quantity - $issued_count);
    
    // Validate inputs
    if (empty($title) || empty($author) || empty($category) || empty($isbn) || $quantity <= 0) {
        $message = 'Please fill in all fields with valid values.';
        $message_type = 'danger';
    } else {
        // Check if ISBN already exists (excluding current book)
        $stmt = $conn->prepare("SELECT id FROM books WHERE isbn = ? AND id != ?");
        $stmt->bind_param("si", $isbn, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = 'A book with this ISBN already exists.';
            $message_type = 'danger';
        } else {
            // Update book
            $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, category = ?, isbn = ?, quantity = ?, available_quantity = ? WHERE id = ?");
            $stmt->bind_param("ssssiii", $title, $author, $category, $isbn, $quantity, $new_available, $book_id);
            
            if ($stmt->execute()) {
                $message = 'Book updated successfully!';
                $message_type = 'success';
                
                // Refresh book data
                $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
                $stmt->bind_param("i", $book_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $book = $result->fetch_assoc();
                $stmt->close();
            } else {
                $message = 'Error updating book: ' . $conn->error;
                $message_type = 'danger';
            }
            $stmt->close();
        }
        $stmt->close();
    }
}
?>

<div class="page-header">
    <h2><i class="bi bi-pencil"></i> Edit Book</h2>
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
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="author" class="form-label">Author *</label>
                    <input type="text" class="form-control" id="author" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">Category *</label>
                    <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($book['category']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="isbn" class="form-label">ISBN *</label>
                    <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="quantity" class="form-label">Quantity *</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="<?php echo $book['quantity'] - $book['available_quantity']; ?>" value="<?php echo $book['quantity']; ?>" required>
                    <small class="text-muted">Minimum: <?php echo $book['quantity'] - $book['available_quantity']; ?> (<?php echo $book['available_quantity']; ?> currently available)</small>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Book
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
