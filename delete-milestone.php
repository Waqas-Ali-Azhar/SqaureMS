<?php
require_once('./');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $milestoneId = isset($_POST['id']) ? intval($_POST['id']) : 0;
  $order         = (int)($_POST['order'] ?? 0);

  if ($milestoneId > 0) {
    $query = "DELETE FROM milestones WHERE id = $milestoneId";
    $taskdel = "DELETE FROM tasks WHERE milestone_id = $milestoneId";
    $result = mysqli_query($conn, $query);
    $result2 = mysqli_query($conn, $taskdel);

    if ($result) {
      echo json_encode(['success' => true]);
    } else {
      echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
  } else {
    echo json_encode(['success' => false, 'error' => 'Invalid milestone ID']);
  }
} else {
  echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
