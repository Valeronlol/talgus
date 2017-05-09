<?php

namespace AppBundle\Service;

use Doctrine\DBAL\Connection;

class QueryModel
{
    private $connection;
    private $userManager;

    public function __construct(Connection $dbalConnection, $user_manager)  {
        $this->connection = $dbalConnection;
        $this->userManager = $user_manager;
    }

    /**
     * Get user id by username
     * @param $userName
     * @return int
     */
    public function getUserId($userName)
    {
        $sql = "SELECT id FROM fos_user WHERE username = :username";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("username", $userName);
        $stmt->execute();
        $res = $stmt->fetch();
        return intval($res['id']);
    }

    /**
     * Change password of another user instead current
     * @param $userId
     * @param $newPassword
     * @return bool
     */
    public function changePasswordById($userId, $newPassword)
    {
        $sql = "UPDATE fos_user SET password = :password WHERE id = :userid";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(":password", $newPassword, \PDO::PARAM_STR);
        $stmt->bindValue(":userid", $userId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function blockUser($login, $isblocked)
    {
        $user = $this->userManager->findUserByUsername($login);

        if(!$user){
            return null;
        }

        $user->setEnabled($isblocked);
        $this->userManager->updateUser($user);

        return true;
    }

}