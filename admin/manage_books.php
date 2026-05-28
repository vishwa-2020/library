<?php
include '../includes/db.php';
include '../includes/header.php';

// Handle delete request
if (isset($_GET['delete'])) {
    $book_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_books.php");
    exit();
}

// Handle search
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Get books
$books = [];
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ? OR isbn LIKE ? ORDER BY added_date DESC");
    $search_param = "%$search%";
    $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
} else {
    $stmt = $conn->prepare("SELECT * FROM books ORDER BY added_date DESC");
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
$stmt->close();
?>

<div class="page-header">
    <h2><i class="bi bi-book"></i> Manage Books</h2>
</div>

<!-- Search and Add Button -->
<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="" class="search-box">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
    <div class="col-md-6 text-end">
        <a href="add_book.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Book
        </a>
    </div>
</div>

<!-- Books Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>ISBN</th>
                        <th>Total</th>
                        <th>Available</th>
                        <th>Added Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No books found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?php echo $book['id']; ?></td>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><?php echo htmlspecialchars($book['category']); ?></td>
                                <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                                <td><?php echo $book['quantity']; ?></td>
                                <td><?php echo $book['available_quantity']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($book['added_date'])); ?></td>
                                <td>
                                    <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary action-btn">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="manage_books.php?delete=<?php echo $book['id']; ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure you want to delete this book?');">
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
