<?php
// Function to add a task
function addTask($userId, $title, $description, $category, $deadline) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, category, deadline) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $title, $description, $category, $deadline]);
}

// Function to delete a task
function deleteTask($taskId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
}

// Function to update a task
function updateTask($taskId, $title, $description, $category, $deadline) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, category = ?, deadline = ? WHERE id = ?");
    $stmt->execute([$title, $description, $category, $deadline, $taskId]);
}

// Function to get all tasks for a user
function getTasks($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Function to get tasks by category
function getTasksByCategory($userId, $category) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? AND category = ?");
    $stmt->execute([$userId, $category]);
    return $stmt->fetchAll();
}

// Function to get a task by ID
function getTaskById($taskId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
    return $stmt->fetch();
}


?>
