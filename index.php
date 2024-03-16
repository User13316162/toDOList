<?php
include 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$error = '';

if ($action == 'add') {
    $task = $_POST['task'];
    $sql = "INSERT INTO tasks (task) VALUES ('$task')";
    if (!$conn->query($sql)) {
        $error = 'Error adding task: ' . $conn->error;
    }
}

if ($action == 'delete') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM tasks WHERE id = $id";
        if (!$conn->query($sql)) {
            $error = 'Error deleting task: ' . $conn->error;
        }
    } else {
        $error = 'Error deleting task: Task ID not set';
    }
}


if ($action == 'complete') {
    $id = $_POST['id'];
    $sql = "UPDATE tasks SET completed = 1 WHERE id = $id";
    if (!$conn->query($sql)) {
        $error = 'Error completing task: ' . $conn->error;
    }
}

if ($action == 'edit') {
    $id = $_POST['id'];
    $task = $_POST['task'];
    $sql = "UPDATE tasks SET task = '$task' WHERE id = $id";
    if (!$conn->query($sql)) {
        $error = 'Error editing task: ' . $conn->error;
    }
}

if ($action == 'get_task') {
    $sql = "SELECT * FROM tasks WHERE id = $id";
    $result = $conn->query($sql);
    if (!$result) {
        $error = 'Error fetching task: ' . $conn->error;
    } else {
        $task = $result->fetch_assoc();
    }
}

if ($action != 'get_task') {
    $sql = "SELECT * FROM tasks";
    $result = $conn->query($sql);
    if (!$result) {
        $error = 'Error fetching tasks: ' . $conn->error;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>TODO List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-8 bg-gray-100">
<?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <div class="max-w-md mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <?php if ($action == 'get_task'): ?>
            <form action="index.php?action=edit" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="task">
                        Edit task
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="task" type="text" value="<?php echo $task['task']; ?>">
                    <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Edit Task
                    </button>
                </div>
            </form>
        <?php else: ?>
            <form action="index.php?action=add" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="task">
                        Add a task
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="task" type="text" placeholder="Add a task">
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Add Task
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <ul class="mt-8">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="flex items-center justify-between py-2 border-b">
                    <span class="<?php echo $row['completed'] ? 'line-through text-gray-500' : ''; ?>"><?php echo $row['task']; ?></span>
                    <div class="flex">
                        <?php if (!$row['completed']): ?>
                            <form action="index.php?action=complete" method="post" class="mr-2">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline" type="submit">
                                    Complete
                                </button>
                            </form>
                        <?php endif; ?>
                        <form action="index.php?action=get_task&id=<?php echo $row['id']; ?>" method="post" class="mr-2">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline" type="submit">
                                Edit
                            </button>
                        </form>
                        <form action="index.php?action=delete" method="post">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline" type="submit">
                                Delete
                            </button>
                        </form>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
