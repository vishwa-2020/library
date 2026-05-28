<?php
include '../includes/db.php';
include '../includes/header.php';

$message = '';
$message_type = '';

// Handle return request
if (isset($_GET['return'])) {
    $issue_id = intval($_GET['return']);
    
    // Get issue details
    $stmt = $conn->prepare("SELECT * FROM issued_books WHERE id = ? AND status = 'issued'");
    $stmt->bind_param("i", $issue_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $issue = $result->fetch_assoc();
        $book_id = $issue['book_id'];
        
        // Update issue status
        $stmt = $conn->prepare("UPDATE issued_books SET status = 'returned' WHERE id = ?");
        $stmt->bind_param("i", $issue_id);
        
        if ($stmt->execute()) {
            // Increase available quantity
            $stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $stmt->close();
            
            $message = 'Book returned successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error returning book: ' . $conn->error;
            $message_type = 'danger';
        }
        $stmt->close();
    } else {
        $message = 'Invalid issue record or book already returned.';
        $message_type = 'danger';
    }
    $stmt->close();
}

// Get issued books
$issued_books = [];
$stmt = $conn->prepare("SELECT ib.*, b.title, b.author, s.name as student_name, s.email as student_email 
                        FROM issued_books ib 
                        JOIN books b ON ib.book_id = b.id 
                        JOIN students s ON ib.student_id = s.id 
                        WHERE ib.status = 'issued' 
                        ORDER BY ib.issue_date DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $issued_books[] = $row;
}
$stmt->close();
?>

<div class="page-header">
    <h2><i class="bi bi-bookmark-check"></i> Return Book</h2>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($issued_books)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No books are currently issued.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Issue ID</th>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Student Name</th>
                            <th>Student Email</th>
                            <th>Issue Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($issued_books as $issue): ?>
                            <tr>
                                <td><?php echo $issue['id']; ?></td>
                                <td><?php echo htmlspecialchars($issue['title']); ?></td>
                                <td><?php echo htmlspecialchars($issue['author']); ?></td>
                                <td><?php echo htmlspecialchars($issue['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($issue['student_email']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($issue['issue_date'])); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($issue['return_date'])); ?></td>
                                <td>
                                    <span class="badge badge-issued">Issued</span>
                                    <?php 
                                    $today = date('Y-m-d');
                                    if ($issue['return_date'] < $today): 
                                    ?>
                                        <span class="badge badge-overdue ms-1">Overdue</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="return_book.php?return=<?php echo $issue['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to return this book?');">
                                        <i class="bi bi-check-circle"></i> Return
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include '../includes/footer.php';
?>
