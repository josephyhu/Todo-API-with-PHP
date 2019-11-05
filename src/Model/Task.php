<?php
namespace App\Model;
use App\Exception\ApiException;

class Task
{
    protected $database;
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }
    public function getTasks()
    {
        $sql = 'SELECT * FROM tasks ORDER BY id';
        $statement = $this->database->prepare($sql);
        $statement->execute();
        $tasks = $statement->fetchAll();
        if (empty($tasks)) {
            throw new ApiException(ApiException::TASK_NOT_FOUND, 404);
        }
        return $tasks;
    }
    public function getTask($task_id)
    {
        $sql = 'SELECT * FROM tasks WHERE id = ?';
        $statement = $this->database->prepare($sql);
        $statement->bindParam(1, $task_id);
        $statement->execute();
        $task = $statement->fetch();
        if (empty($task)) {
            throw new ApiException(ApiException::TASK_NOT_FOUND, 404);
        }
        return $task;
    }
    public function createTask($data)
    {
        if (empty($data['task']) || empty($data['status'])) {
            throw new ApiException(ApiException::TASK_INFO_REQUIRED);
        }
        $sql = 'INSERT INTO tasks (task, status) VALUES (?, ?)';
        $statement = $this->database->prepare($sql);
        $statement->bindParam(1, $data['task']);
        $statement->bindParam(2, $data['status']);
        $statement->execute();
        if ($statement->rowCount() < 1) {
            throw new ApiException(ApiException::TASK_CREATION_FAILED);
        }
        return $this->getTask($this->database->lastInsertId());
    }
    public function updateTask($data)
    {
        if (empty($data['task']) || empty($data['status']) || empty($data['task_id'])) {
            throw new ApiException(ApiException::TASK_INFO_REQUIRED);
        }
        $sql = 'UPDATE tasks SET task = ?, status = ? WHERE id = ?';
        $statement = $this->database->prepare($sql);
        $statement->bindParam(1, $data['task']);
        $statement->bindParam(2, $data['status']);
        $statement->bindParam(3, $data['task_id']);
        $statement->execute();
        if ($statement->rowCount() < 1) {
            throw new ApiException(ApiException::TASK_UPDATE_FAILED);
        }
        return $this->getTask($data['task_id']);
    }
    public function deleteTask($task_id)
    {
        $this->getTask($task_id);
        $sql = 'DELETE FROM tasks WHERE id = ?';
        $statement = $this->database->prepare($sql);
        $statement->bindParam(1, $task_id);
        $statement->execute();
        if ($statement->rowCount() < 1) {
            throw new ApiException(ApiException::TASK_DELETE_FAILED);
        }
        return ['message' => 'The task was deleted.'];
    }
}
