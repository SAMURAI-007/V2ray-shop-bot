<?php

class DB
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getDB()
    {
        return $this->db;
    }

    public function getUser($chat_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE chat_id = :chat_id");
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function createUser($username, $chat_id)
    {
        $ref_id = intval($chat_id * 2 / 100);

        $stmt = $this->db->prepare("SELECT * FROM users WHERE chat_id = :chat_id");
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->execute();

        $stmt = $this->db->prepare("INSERT INTO users (username,chat_id,ref_id) VALUES (:username,:chat_id,:ref_id)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->bindParam(':ref_id', $ref_id);
        $stmt->execute();
    }

    public function updateUser($username, $chat_id)
    {
        $stmt = $this->db->prepare("UPDATE users SET chat_id = :chat_id, username = :username WHERE chat_id = :chat_id");
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
    }

    public function createWallet($user_id, $created_at)
    {
        $stmt = $this->db->prepare("INSERT INTO wallet (user_id,created_at) VALUES (:user_id,:created_at)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->execute();
    }

    public function getWallet($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM wallet WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function deleteUser($chat_id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE chat_id = :chat_id");
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->execute();
    }

    public function deleteWallet()
    {
        $stmt = $this->db->prepare("DELETE FROM wallet");
        $stmt->execute();
    }
}
