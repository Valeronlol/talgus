<?php

namespace AppBundle\Service;

use Doctrine\DBAL\Connection;
use FOS\UserBundle\Doctrine\UserManager;

class QueryModel
{
    private $connection, $userManager;

    public function __construct(Connection $dbalConnection, UserManager $user_manager)
    {
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

    /**
     * returns userdata by id
     * @param $id
     * @return mixed
     */
    public function getUserStatisticsAbonentById($id)
    {
        $sql = "select t.priceplan_name as 'Тарифный план',
                s.balance / 100000 as 'Баланс', 
                s.billing_language as'Язык обслуживания', 
                s.msisdn as'Номер абонента', 
                s.startdate as 'Дата активации'
                from priceplan t 
                join subscriber s on	s.priceplan = t.priceplan_id                                        
                where s.subs_id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * returns userdata by id
     * @param $id
     * @return mixed
     */
    public function getUserStatisticsBaseServicesById($id)
    {
        $sql = "select s.service_desc as 'Тип сервиса',
                case 
                when ss.provisioned_state = 0 then 'Отключен' 
                when ss.provisioned_state = 1 then 'Активен'
                else 'Ошибка'
                end as 'Статус'
                from subscriber_services ss 
                inner join services s on ss.service_id = s.service_id 
                inner join subscriber su on ss.subs_id = su.subs_id
                where su.subs_id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * returns userdata by id
     * @param $id
     * @return mixed
     */
    public function getUserStatisticsAdditionalServiceById($id)
    {
        $sql = "select au.auxservice_name as 'Тип сервиса',
                case 
                when sa.provisioned = 1 then 'Активный'
                when sa.provisioned = 2 then 'Пауза'
                else 'Ошибка'
                end as 'Статус', 
                case
                when sa.nextrecurringchargedate is null then 'отсутствует'
                else sa.nextrecurringchargedate
                end as 'Дата снятия следующей АП'
                from subscriber_aux_services sa
                inner join auxiliary_services au on au.auxservice_id = sa.auxservice_id
                inner join subscriber su on sa.subscriber_id = su.subs_id
                where su.subs_id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * returns userdata by id
     * @param $id
     * @return mixed
     */
    public function updateBaseServiceStatus($userid, $service, $action)
    {
        $sql = "update subscriber_services ss
                inner join services se
                on ss.service_id = se.service_id
                set provisioned_state = ?
                where ss.subs_id = ?
                and se.service_desc = ?";

        return $this->connection->executeUpdate($sql, [$action, $userid, $service ]);
    }

    public function getTransactionsData ()
    {
        $sql = "select t.msisdn as 'Номер абонента', 
                ad.name as 'Тип транзакции', 
                t.agent as 'Агент', 
                t.amount as 'Сумма',
                t.balance as 'Баланс абонента',
                t.payment_agency as 'Терминал',
                t.receivedate as 'Время регистрации транзакции',
                t.transdate as 'Время обработки транзакции' from transaction t
                inner join adjustment_type ad
                on t.adjustment_type = ad.code
                where t.subs_id = 1
                and t.receivedate < CURDATE()
                and t.receivedate >= DATE_SUB(CURDATE(),Interval 2 MONTH)
                group by t.receivedate";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

}
