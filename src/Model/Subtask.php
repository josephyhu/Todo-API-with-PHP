<?php
namespace App\Model;
use App\Exception\ApiException;

class Subtask
{
    protected $database;
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }
    public function getSubtasksByTaskId($task_id)
    {
        if (empty($task_id)) {
            throw new ApiException(ApiException::SUBTASK_INFO_REQUIRED);
        }
        $sql = 'SELECT * FROM subtasks WHERE task_id = ?';
        $statement = $this->database->prepare($sql);
        $statement->bindParam(1, $task_id);
        $statement->execute();
        $subtasks = $statement->fetchAll();
        if (empty($subtasks)) {
            throw new ApiException(ApiException::SUBTASK_NOT_FOUND, 404);
        }
        return $subtasks;
    }
    public function getSubtask($subtask_id)
    {
        if (empty($subtask_id)) {
            throw new ApiException(ApiException::SUBTASK_INFO_REQUIRED);
        }
        $sql = 'SELECT * FROM subtasks WHERE id = ?';
        $statement = $this->database->prepare($sql);
        $statement->bindParam(1, $subtask_id);
        $statement->execute();
        $subtask = $statement->fetch();
        if (empty($subtask)) {
            throw new ApiException(ApiException::SUBTASK_NOT_FOUND, 404);
        }
        return $subtask;
    }
    public function createSubtask($data)
    {
        if (empty($data['name']) || !isset($data['subtask_status']) || empty($data['task_id'])) {
            throw new ApiException(ApiException::SUBTASK_INFO_REQUIRED);
        }
        $sql = 'INSERT INTO subtasks (name, status, task_id) VALUES (?, ?, ?)';
        $statement = $this->database->prepare($sql);
        $statement->bindParam(1, $data['name']);
        $statement->bindParam(2, $data['subtask_status']);
        $statement->bindParam(3, $data['task_id']);
        $statement->execute();
        if ($statement->rowCount() < 1) {
            throw new ApiException(ApiException::SUBTASK_CREATION_FAILED);
        }
        return $this->getSubtask($this->database->lastInsertId());
    }
    public function updateSubtask($data)
    {
        if (empty($data['name']) || !isset($data['subtask_status']) || empty($data['subtask_id'])) {
            throw new ApiException(ApiException::SUBTASK_INFO_REQUIRED);
        }
        $sql = 'UPDATE subtasks set name = ?, status = ? WHERE id = ?';
        $statement = $this->database->prepare($sql);
        $statement->bindParam(1, $data['name']);
        $statement->bindParam(2, $data['subtask_status']);
        $statement->bindParam(3, $data['subtask_id']);
        $statement->execute();
        if ($statement->rowCount() < 1) {
            throw new ApiException(ApiException::SUBTASK_UPDATE_FAILED);
        }
        return $this->getSubtask($data['subtask_id']);
    }
    public function deleteSubtask($subtask_id)
    {
        if (empty($subtask_id)) {
            throw new ApiException(ApiException::SUBTASK_INFO_REQUIRED);
        }
        $this->getSubtask($subtask_id);
        $sql = 'DELETE FROM subtasks WHERE id = ?';
        $statement = $this->database->prepare($sql);
        $statement->bindParam(1, $subtask_id);
        $statement->execute();
        if ($statement->rowCount() < 1) {
            throw new ApiException(ApiException::SUBTASK_DELETE_FAILED);
        }
        return ['message' => 'The subtask was deleted.'];
    }
}
