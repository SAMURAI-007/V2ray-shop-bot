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

    public function updateWallet($user_id, $balance, $updated_at, $last)
    {
        $stmt = $this->db->prepare("UPDATE wallet SET updated_at = :updated_at , balance=:balance , last_depo = :last_depo WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':balance', $balance);
        $stmt->bindParam(':updated_at', $updated_at);
        $stmt->bindParam(':last_depo', $last);
        $stmt->execute();
    }

    public function createSub($user_id, $username,$g,$e)
    {

        $curl = curl_init();

        $time = time();
        // Dynamic data
        $data = [
            "data_limit" => $g,
            "data_limit_reset_strategy" => "no_reset",
            "expire" => $e,
            "inbounds" => [
                "vless" => [
                    "VLESS TCP REALITY",
                    "VLESS GRPC REALITY"
                ]
            ],
            "next_plan" => [
                "add_remaining_traffic" => false,
                "data_limit" => 0,
                "expire" => 0,
                "fire_on_either" => true
            ],
            "note" => "",
            "on_hold_expire_duration" => 0,
            "on_hold_timeout" => "2023-11-03T20:30:00",
            "proxies" => [
                "vless" => new stdClass() // Empty object
            ],
            "status" => "active",
            "username" => "$username-$time"
        ];

        // Convert the PHP array to a JSON string
        $postFields = json_encode($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://nn.nayathemitherkabnafhnkahf.ir/api/user',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields, // Use the JSON-encoded data
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJOYXlhVlBOIiwiYWNjZXNzIjoic3VkbyIsImlhdCI6MTc0Mzg1MDI0MywiZXhwIjoxNzQzOTM2NjQzfQ.EHN_HHqUOr2o11JAVc4FLL5_C8M5UGGlUCmxPih5I4k'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);




        $stmt = $this->db->prepare("INSERT INTO subs (user_id,sub_url,exp_date,data_limit) VALUES (:user_id,:sub_url,:exp_date,:data_limit)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':sub_url', $response->subscription_url);
        $stmt->bindParam(':exp_date', $response->expire);
        $stmt->bindParam(':data_limit', $response->data_limit);
        $stmt->execute();
    }

    public function getSub($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM subs WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
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
