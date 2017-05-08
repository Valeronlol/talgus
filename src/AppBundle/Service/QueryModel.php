<?php

namespace AppBundle\Service;

use Doctrine\DBAL\Connection;

class QueryModel
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $dbalConnection)  {
        $this->connection = $dbalConnection;
    }

    public function getUserId($userName)
    {
        $sql = "SELECT id FROM fos_user WHERE username = :username";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("username", $userName);
        $stmt->execute();
        $res = $stmt->fetch();
        return intval($res['id']);
    }

    public function changePasswordById($userId, $newPassword)
    {
        $sql = "UPDATE fos_user SET password = :password WHERE id = :userid";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":password", $newPassword, \PDO::PARAM_STR);
        $stmt->bindValue(":userid", $userId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

}