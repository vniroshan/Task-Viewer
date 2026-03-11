<?php
// main page
declare(strict_types=1);

require_once __DIR__ . '/config.php';

$filters = ['all', 'completed', 'pending'];
$filter = in_array($_GET['filter'] ?? '', $filters, true) ? $_GET['filter'] : 'all';

// Get tasks
try {
    $db = get_db();

    $counts = $db->query(
        'SELECT COUNT(*) AS total, SUM(completed) AS done FROM tasks'
    )->fetch();

    $total_tasks     = (int) $counts['total'];
    $completed_tasks = (int) $counts['done'];
    $pending_tasks   = $total_tasks - $completed_tasks;

    $query = match ($filter) {
        'completed' => 'SELECT * FROM tasks WHERE completed = 1 ORDER BY created_at DESC',
        'pending'   => 'SELECT * FROM tasks WHERE completed = 0 ORDER BY created_at DESC',
        default     => 'SELECT * FROM tasks ORDER BY created_at DESC',
    };

    $tasks = $db->query($query)->fetchAll();

    $db_error = null;

} catch (PDOException $e) {
    $tasks           = [];
    $total_tasks     = 0;
    $completed_tasks = 0;
    $pending_tasks   = 0;
    $db_error        = 'Could not connect to the database.';
}

function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Viewer - Niroshan</title>

    <!-- Bootstrap 5 -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light min-vh-100">

<!-- Header -->
<header class="bg-dark py-3 border-bottom border-danger border-3">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <div>
                <div class="fw-bold text-white fs-5">
                    <span class="text-danger">Task</span> Viewer
                </div>
                <div class="text-white-50 small">A simple task management application</div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="container">
    <?php if ($db_error): ?>
        <div class="alert alert-danger mt-4" role="alert">
            <strong>Database error:</strong> <?= e($db_error) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mt-4 mb-4 border-0">
        <!-- Add Task -->
        <form class="d-flex gap-2 p-3 bg-dark rounded-top" action="actions.php" method="POST"
              data-bs-theme="dark">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="filter" value="<?= e($filter) ?>">
            <input
                type="text"
                name="title"
                class="form-control"
                placeholder="Add a new task and press Enter or click Add…"
                maxlength="255"
                required
                autocomplete="off"
            >
            <button type="submit" class="btn btn-danger fw-semibold text-nowrap px-4">Add</button>
        </form>

        <!-- Filter Tabs -->
        <div class="p-3 border-bottom bg-white">
            <div class="btn-group" role="group" aria-label="Filter tasks">
                <?php foreach (['all' => 'All', 'pending' => 'Pending', 'completed' => 'Completed'] as $key => $label): ?>
                    <a
                        href="?filter=<?= $key ?>"
                        class="btn btn-sm <?= $filter === $key ? 'btn-dark' : 'btn-outline-secondary' ?>"
                    ><?= $label ?>
                        <?php if ($key === 'all'): ?>
                            (<?= $total_tasks ?>)
                        <?php elseif ($key === 'completed'): ?>
                            (<?= $completed_tasks ?>)
                        <?php else: ?>
                            (<?= $pending_tasks ?>)
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Task List -->
        <?php if (empty($tasks)): ?>
            <div class="text-center text-secondary py-5">
                <p class="mb-0 small">No tasks here. <?= $filter === 'all' ? 'Add one above!' : 'Try a different filter.' ?></p>
            </div>
        <?php else: ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($tasks as $task): ?>
                    <li class="list-group-item d-flex align-items-center gap-3 py-3 px-4">
                        <!-- Toggle completed -->
                        <form class="m-0" action="actions.php" method="POST">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id"     value="<?= (int) $task['id'] ?>">
                            <input type="hidden" name="filter" value="<?= e($filter) ?>">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                style="width:20px;height:20px;"
                                <?= $task['completed'] ? 'checked' : '' ?>
                                onchange="this.form.submit()"
                                title="Mark as <?= $task['completed'] ? 'pending' : 'completed' ?>"
                            >
                        </form>

                        <span class="flex-grow-1 small fw-medium <?= $task['completed'] ? 'text-decoration-line-through text-secondary' : '' ?>">
                            <?= e($task['title']) ?>
                        </span>

                        <?php if ($task['completed']): ?>
                            <span class="badge bg-success-subtle text-success fw-semibold">Done</span>
                        <?php else: ?>
                            <span class="badge bg-warning-subtle text-warning fw-semibold">Pending</span>
                        <?php endif; ?>

                        <!-- Delete -->
                        <form class="m-0" action="actions.php" method="POST">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id"     value="<?= (int) $task['id'] ?>">
                            <input type="hidden" name="filter" value="<?= e($filter) ?>">
                            <button
                                type="submit"
                                class="btn btn-sm btn-outline-danger"
                                title="Delete task"
                                onclick="return confirm('Delete this task?')"
                            >X</button>
                        </form>

                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</main>

<!--banana-->

<footer class="text-center text-secondary small pb-4">
   Developed by Niroshan
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>
</html>
