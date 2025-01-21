<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Include the database connection
include "db_connect.php";

// Get the submission ID from the URL
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $submissionId = $_GET["id"];

    // Fetch submission details
    $query = "SELECT rr.id, u.username, rr.email, rr.status, rs.relationship, rs.comments, 
                     rs.file_path, rs.submitted_at 
              FROM recommendation_requests rr
              LEFT JOIN recommendation_submissions rs ON rr.id = rs.request_id
              JOIN users u ON rr.user_id = u.id
              WHERE rr.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $submissionId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $submission = $result->fetch_assoc();
    } else {
        echo "Submission not found.";
        exit();
    }
} else {
    echo "Invalid submission ID.";
    exit();
}
?>

<?php include "header.php";
// Include the header
?>

<div class="container">
    <h2 class="mt-4">Submission Details</h2>
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <table class="table table-bordered">
        <tr>
            <th>Submission ID</th>
            <td><?= htmlspecialchars($submission["id"]) ?></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><?= htmlspecialchars($submission["username"]) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= htmlspecialchars($submission["email"]) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= htmlspecialchars($submission["status"]) ?></td>
        </tr>
        <tr>
            <th>Relationship</th>
            <td><?= htmlspecialchars(
                $submission["relationship"] ?: "N/A"
            ) ?></td>
        </tr>
        <tr>
            <th>Comments</th>
            <td><?= nl2br(
                htmlspecialchars($submission["comments"] ?: "N/A")
            ) ?></td>
        </tr>
        <tr>
            <th>Submitted At</th>
            <td><?= htmlspecialchars(
                $submission["submitted_at"] ?: "N/A"
            ) ?></td>
        </tr>
        <tr>
            <th>File</th>
            <td>
                <?php if ($submission["file_path"]): ?>
                    <!-- Update the link to use the download.php script -->
                    <a href="download.php?file=<?= urlencode(
                        basename($submission["file_path"])
                    ) ?>">Download File</a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<?php include "footer.php"; // Include the footer ?>
