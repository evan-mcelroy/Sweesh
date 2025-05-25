<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if (isset($_POST['id'], $_POST['type'])) {
    $postId = (int)$_POST['id'];
    $voteType = $_POST['type'] === 'up' ? 'up' : 'down';

    $stmt = $conn->prepare("SELECT id, vote_type FROM votes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $postId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($vote = $result->fetch_assoc()) {
        if ($vote['vote_type'] === $voteType) {
            $delete = $conn->prepare("DELETE FROM votes WHERE id = ?");
            $delete->bind_param("i", $vote['id']);
            $delete->execute();
        } else {
            $update = $conn->prepare("UPDATE votes SET vote_type = ? WHERE id = ?");
            $update->bind_param("si", $voteType, $vote['id']);
            $update->execute();
        }
    } else {
        $insert = $conn->prepare("INSERT INTO votes (user_id, post_id, vote_type) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $_SESSION['user_id'], $postId, $voteType);
        $insert->execute();
    }

    // Get new score
    $scoreStmt = $conn->prepare("
        SELECT 
            (SELECT COUNT(*) FROM votes WHERE post_id = ? AND vote_type = 'up') - 
            (SELECT COUNT(*) FROM votes WHERE post_id = ? AND vote_type = 'down') AS score
    ");
    $scoreStmt->bind_param("ii", $postId, $postId);
    $scoreStmt->execute();
    $scoreResult = $scoreStmt->get_result();
    $scoreRow = $scoreResult->fetch_assoc();

    echo json_encode([
        'success' => true,
        'score' => $scoreRow['score']
    ]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}
?>
