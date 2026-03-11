<?php
// for all actions (add, toggle, delete)
declare(strict_types=1);
require_once __DIR__ . '/config.php';
$action = $_POST['action'] ?? '';
$filter = $_POST['filter'] ?? 'all';

try {
    $db = get_db();
    switch ($action) {

        //Add a new task
        case 'add':
            $title = trim($_POST['title'] ?? '');
            if ($title !== '') {
                $stmt = $db->prepare(
                    'INSERT INTO tasks (title, completed) VALUES (:title, 0)'
                );
                $stmt->execute([':title' => $title]);
            }
            break;

        //Toggle completed status
        case 'toggle':
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $db->prepare(
                    'UPDATE tasks SET completed = 1 - completed WHERE id = :id'
                );
                $stmt->execute([':id' => $id]);
            }
            break;

        //Delete a task
        case 'delete':
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $db->prepare('DELETE FROM tasks WHERE id = :id');
                $stmt->execute([':id' => $id]);
            }
            break;
    }
} catch (PDOException $e) {
    error_log('DB error: ' . $e->getMessage());
}
// banana
// validate resubmit
$redirect = 'index.php';
if (in_array($filter, ['all', 'completed', 'pending'], true)) {
    $redirect .= '?filter=' . urlencode($filter);
}

header('Location: ' . $redirect);
exit;
