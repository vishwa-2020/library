<?php
include '../includes/db.php';
include '../includes/header.php';

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

// Get returned books
$returned_books = [];
$stmt = $conn->prepare("SELECT ib.*, b.title, b.author, s.name as student_name, s.email as student_email 
                        FROM issued_books ib 
                        JOIN books b ON ib.book_id = b.id 
                        JOIN students s ON ib.student_id = s.id 
                        WHERE ib.status = 'returned' 
                        ORDER BY ib.return_date DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $returned_books[] = $row;
}
$stmt->close();

// Get overdue books
$today = date('Y-m-d');
$overdue_books = [];
$stmt = $conn->prepare("SELECT ib.*, b.title, b.author, s.name as student_name, s.email as student_email 
                        FROM issued_books ib 
                        JOIN books b ON ib.book_id = b.id 
                        JOIN students s ON ib.student_id = s.id 
                        WHERE ib.status = 'issued' AND ib.return_date < ? 
                        ORDER BY ib.return_date ASC");
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $overdue_books[] = $row;
}
$stmt->close();
?>

<div class="page-header">
    <h2><i class="bi bi-file-earmark-bar-graph"></i> Reports</h2>
</div>

<!-- Report Tabs -->
<ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="issued-tab" data-bs-toggle="tab" data-bs-target="#issued" type="button" role="tab">
            <i class="bi bi-bookmark-plus"></i> Issued Books (<?php echo count($issued_books); ?>)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab">
            <i class="bi bi-bookmark-check"></i> Returned Books (<?php echo count($returned_books); ?>)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="overdue-tab" data-bs-toggle="tab" data-bs-target="#overdue" type="button" role="tab">
            <i class="bi bi-exclamation-triangle"></i> Overdue Books (<?php echo count($overdue_books); ?>)
        </button>
    </li>
</ul>

<div class="tab-content" id="reportTabsContent">
    <!-- Issued Books Tab -->
    <div class="tab-pane fade show active" id="issued" role="tabpanel">
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
                                            <?php if ($issue['return_date'] < $today): ?>
                                                <span class="badge badge-overdue ms-1">Overdue</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Returned Books Tab -->
    <div class="tab-pane fade" id="returned" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <?php if (empty($returned_books)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No books have been returned yet.
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($returned_books as $issue): ?>
                                    <tr>
                                        <td><?php echo $issue['id']; ?></td>
                                        <td><?php echo htmlspecialchars($issue['title']); ?></td>
                                        <td><?php echo htmlspecialchars($issue['author']); ?></td>
                                        <td><?php echo htmlspecialchars($issue['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($issue['student_email']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($issue['issue_date'])); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($issue['return_date'])); ?></td>
                                        <td>
                                            <span class="badge badge-returned">Returned</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Overdue Books Tab -->
    <div class="tab-pane fade" id="overdue" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <?php if (empty($overdue_books)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> No overdue books! Great job!
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> <?php echo count($overdue_books); ?> book(s) are overdue.
                    </div>
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
                                    <th>Due Date</th>
                                    <th>Days Overdue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overdue_books as $issue): ?>
                                    <?php 
                                    $due_date = new DateTime($issue['return_date']);
                                    $today_date = new DateTime($today);
                                    $interval = $today_date->diff($due_date);
                                    $days_overdue = $interval->days;
                                    ?>
                                    <tr>
                                        <td><?php echo $issue['id']; ?></td>
                                        <td><?php echo htmlspecialchars($issue['title']); ?></td>
                                        <td><?php echo htmlspecialchars($issue['author']); ?></td>
                                        <td><?php echo htmlspecialchars($issue['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($issue['student_email']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($issue['issue_date'])); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($issue['return_date'])); ?></td>
                                        <td>
                                            <span class="badge badge-overdue"><?php echo $days_overdue; ?> days</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>
