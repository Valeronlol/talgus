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

    /**
     * find user by phone number
     * @param $phone
     * @return mixed
     */
    public function getAbonByPhone($phone)
    {
        $sql = "select s.subs_id, p.name, p.surname, p.msisdn, p.imsi, p.adress,
                    case when s.state = 1
                        then 'active'
                        when s.state = 0
                        then 'inactive'
                        when s.state = '2'
                        then 'deactivated'
                            else 'error'
                            end as State
                            from subscriber s join personification p on
                s.msisdn = p.msisdn
                where s.msisdn = :phone";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("phone", $phone);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * find user by sim card number
     * @param $phone
     * @return mixed
     */
    public function getAbonBySim($sim)
    {
        $sql = "select s.subs_id, p.name, p.surname, p.msisdn, p.imsi, p.adress,
                    case when s.state = 1
                        then 'active'
                        when s.state = 0
                        then 'inactive'
                        when s.state = '2'
                        then 'deactivated'
                            else 'error'
                            end as State
                            from subscriber s join personification p on
                s.msisdn = p.msisdn
                where s.imsi = :sim";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("sim", $sim);
        $stmt->execute();
        return $stmt->fetch();
    }

}