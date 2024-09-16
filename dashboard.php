<?php
session_start();
include '../includes/functions.php';
include '../includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    if (isset($_POST['add_task'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $category = trim($_POST['category']);
        $deadline = $_POST['deadline'];
        if (!empty($title) && !empty($description)) {
            addTask($userId, $title, $description, $category, $deadline);
        }
    } elseif (isset($_POST['delete_task'])) {
        $taskId = $_POST['task_id'];
        deleteTask($taskId);
    } elseif (isset($_POST['edit_task'])) {
        $taskId = $_POST['task_id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $category = trim($_POST['category']);
        $deadline = $_POST['deadline'];
        updateTask($taskId, $title, $description, $category, $deadline);
    }

    // Redirect to avoid resubmission
    header('Location: dashboard.php');
    exit();
}

// Handle displaying the edit form
$taskToEdit = null;
if (isset($_GET['edit_task_id'])) {
    $taskId = $_GET['edit_task_id'];
    $taskToEdit = getTaskById($taskId); // Retrieve the task details
}

// Handle filtering tasks by category
$categoryFilter = isset($_GET['category_filter']) ? $_GET['category_filter'] : '';

if ($categoryFilter) {
    $tasks = getTasksByCategory($userId, $categoryFilter);
} else {
    $tasks = getTasks($userId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Dashboard</h1>
    
    <!-- Logout Button -->
    <form method="POST" action="logout.php" style="display:inline;">
        <button type="submit">Logout</button>
    </form>

    <!-- Task Management Form -->
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <select name="category" required>
            <option value="General">General</option>
            <option value="Work">Work</option>
            <option value="Personal">Personal</option>
            <option value="Urgent">Urgent</option>
        </select>
        <input type="date" name="deadline">
        <button type="submit" name="add_task">Add Task</button>
    </form>

    <!-- Filter Tasks by Category -->
    <form method="GET">
        <label for="category_filter">Filter by Category:</label>
        <select name="category_filter" id="category_filter">
            <option value="">All</option>
            <option value="General" <?php echo $categoryFilter === 'General' ? 'selected' : ''; ?>>General</option>
            <option value="Work" <?php echo $categoryFilter === 'Work' ? 'selected' : ''; ?>>Work</option>
            <option value="Personal" <?php echo $categoryFilter === 'Personal' ? 'selected' : ''; ?>>Personal</option>
            <option value="Urgent" <?php echo $categoryFilter === 'Urgent' ? 'selected' : ''; ?>>Urgent</option>
        </select>
        <button type="submit">Filter</button>
    </form>

    <!-- Display Tasks -->
    <?php foreach ($tasks as $task): ?>
        <div>
            <h3><?php echo htmlspecialchars($task['title']); ?></h3>
            <p><?php echo htmlspecialchars($task['description']); ?></p>
            <p>Category: <?php echo htmlspecialchars($task['category']); ?></p>
            <p>Deadline: <?php echo htmlspecialchars($task['deadline']); ?></p>
            <!-- Edit Form -->
            <form method="GET" action="dashboard.php" style="display:inline;">
                <input type="hidden" name="edit_task_id" value="<?php echo $task['id']; ?>">
                <button type="submit">Edit</button>
            </form>
            <!-- Delete Form -->
            <form method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                <button type="submit" name="delete_task">Delete</button>
            </form>
        </div>
    <?php endforeach; ?>

    <!-- Edit Task Form -->
    <?php if ($taskToEdit): ?>
        <h2>Edit Task</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
            <input type="hidden" name="task_id" value="<?php echo $taskToEdit['id']; ?>">
            <input type="text" name="title" value="<?php echo htmlspecialchars($taskToEdit['title']); ?>" required>
            <textarea name="description" required><?php echo htmlspecialchars($taskToEdit['description']); ?></textarea>
            <select name="category" required>
                <option value="General" <?php echo $taskToEdit['category'] === 'General' ? 'selected' : ''; ?>>General</option>
                <option value="Work" <?php echo $taskToEdit['category'] === 'Work' ? 'selected' : ''; ?>>Work</option>
                <option value="Personal" <?php echo $taskToEdit['category'] === 'Personal' ? 'selected' : ''; ?>>Personal</option>
                <option value="Urgent" <?php echo $taskToEdit['category'] === 'Urgent' ? 'selected' : ''; ?>>Urgent</option>
            </select>
            <input type="date" name="deadline" value="<?php echo htmlspecialchars($taskToEdit['deadline']); ?>">
            <button type="submit" name="edit_task">Update Task</button>
        </form>
    <?php endif; ?>

</body>
</html>
