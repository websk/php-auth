<?php

namespace WebSK\Auth\User;

use WebSK\Auth\Auth;
use WebSK\Image\ImageConstants;
use WebSK\Image\ImageManager;
use WebSK\Cache\CacheService;
use WebSK\Entity\EntityRepository;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Config\ConfWrapper;
use WebSK\Utils\Filters;

/**
 * Class UserService
 * @method User getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Auth\User
 */
class UserService extends EntityService
{
    const PASSWORD_LENGTH = 12;

    /** @var RoleService */
    protected $role_service;

    /** @var UserRoleService */
    protected $user_role_service;

    /** @var UserRepository */
    protected $repository;

    public function __construct(
        string $entity_class_name,
        EntityRepository $repository,
        CacheService $cache_service,
        RoleService $role_service,
        UserRoleService $user_role_service
    ) {
        $this->role_service = $role_service;
        $this->user_role_service = $user_role_service;

        parent::__construct($entity_class_name, $repository, $cache_service);
    }

    /**
     * @param InterfaceEntity|User $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj)
    {
        if (!$entity_obj->getEmail()) {
            throw new \Exception('Ошибка! Не указан Email.');
        }

        if (!$entity_obj->getName()) {
            throw new \Exception('Ошибка! Не указано имя на сайте.');
        }

        $exist_user_obj = null;
        $email = '';
        if ($entity_obj->getId()) {
            $exist_user_obj = $this->getById($entity_obj->getId());
            $email = $exist_user_obj->getEmail();
        }

        if ($email != $entity_obj->getEmail()) {
            $has_user_id = $this->hasUserByEmail($email);
            if ($has_user_id) {
                throw new \Exception('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже существует.');
            }
        }

        parent::beforeSave($entity_obj);
    }

    /**
     * @param int $user_id
     * @throws \Exception
     */
    public function deleteUserRolesForUserId(int $user_id)
    {
        $user_roles_ids_arr = $this->user_role_service->getIdsArrByUserId($user_id);

        foreach ($user_roles_ids_arr as $user_role_id) {
            $user_role_obj = $this->user_role_service->getById($user_role_id);
            $this->user_role_service->delete($user_role_obj);
        }
    }

    /**
     * @param int $user_id
     * @return array
     * @throws \Exception
     */
    public function getRoleIdsArrByUserId(int $user_id): array
    {
        $user_roles_ids_arr = $this->user_role_service->getIdsArrByUserId($user_id);

        $role_ids_arr = [];

        foreach ($user_roles_ids_arr as $user_role_id) {
            $user_role_obj = $this->user_role_service->getById($user_role_id);
            $role_ids_arr[] = $user_role_obj->getRoleId();
        }

        return $role_ids_arr;
    }

    /**
     * @param User $user_obj
     * @return bool
     * @throws \Exception
     */
    public function deletePhoto(User $user_obj)
    {
        if (!$user_obj->getPhotoPath()) {
            return true;
        }

        $user_obj->setPhoto('');
        $this->save($user_obj);

        $file_path = ConfWrapper::value('site_full_path') . DIRECTORY_SEPARATOR . ImageConstants::IMG_ROOT_FOLDER . DIRECTORY_SEPARATOR . $user_obj->getPhotoPath();
        if (!file_exists($file_path)) {
            return false;
        }

        $image_manager = new ImageManager();
        $image_manager->removeImageFile($user_obj->getPhotoPath());

        return true;
    }

    /**
     * @param InterfaceEntity|User $user_obj
     * @throws \Exception
     */
    public function afterDelete(InterfaceEntity $user_obj)
    {
        $this->deletePhoto($user_obj);
        $this->deleteUserRolesForUserId($user_obj->getId());

        parent::afterDelete($user_obj);
    }

    /**
     * Является ли пользователь администратором
     * @param int $user_id
     * @return bool
     * @throws \Exception
     */
    public function hasRoleAdminByUserId(int $user_id): bool
    {
        if (in_array(Role::ROLE_ADMIN, $this->getRoleIdsArrByUserId($user_id))) {
            return true;
        }

        return false;
    }

    /**
     * Есть ли у пользователя роль, по обозначению роли
     * @param int $user_id
     * @param string $designation
     * @return bool
     * @throws \Exception
     */
    public function hasRoleByUserIdAndDesignation(int $user_id, string $designation): bool
    {
        $roles_ids_arr = $this->getRoleIdsArrByUserId($user_id);

        foreach ($roles_ids_arr as $role_id) {
            if (!$role_id) {
                continue;
            }

            $role_obj = $this->role_service->getById($role_id);

            if (trim($role_obj->getDesignation()) == trim($designation)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $user_obj
     * @return string
     */
    public function getImageHtml(User $user_obj): string
    {
        if (!$user_obj->getPhoto()) {
            return '';
        }

        return '<img src="/files/images/user/'. $user_obj->getPhoto() .'" alt="' . $user_obj->getName() .'" title="' . $user_obj->getName() .'" style="max-width: 75px;">';
    }

    /**
     * Смена и отправка пароля пользователю
     * @param $user_id
     * @return string
     */
    public function createAndSendPasswordToUser(int $user_id): string
    {
        $new_password = $this->generatePassword(self::PASSWORD_LENGTH);

        $user_obj = $this->getById($user_id);
        $user_obj->setPassw(Auth::getHash($new_password));
        $this->save($user_obj);

        if ($user_obj->getEmail()) {
            $site_email = ConfWrapper::value('site_email');
            $site_domain = ConfWrapper::value('site_domain');
            $site_name = ConfWrapper::value('site_name');

            $mail_message = "<p>Добрый день, " . $user_obj->getName() . "</p>";
            $mail_message .= "<p>Вы воспользовались формой восстановления пароля на сайте " . $site_name . "</p>";
            $mail_message .= "<p>Ваш новый пароль: " . $new_password . "<br>";
            $mail_message .= "Ваш email для входа: " . $user_obj->getEmail() . "</p>";
            $mail_message .= "<p>Рекомендуем сменить пароль после входа на сайт.</p>";
            $mail_message .= '<p>' . $site_domain . "</p>";

            $subject = "Смена пароля на сайте " . ConfWrapper::value('site_name');

            $mail = new \PHPMailer;
            $mail->CharSet = "utf-8";
            $mail->setFrom($site_email, $site_name);
            $mail->addAddress($user_obj->getEmail());
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $mail_message;
            $mail->AltBody = Filters::checkPlain($mail_message);
            $mail->send();
        }

        return $new_password;
    }

    /**
     * ID пользователя по его email
     * @param $email
     * @return int|null
     */
    public function getUserIdByEmail(string $email): ?int
    {
        $user_id = $this->repository->findUserIdByEmail($email);

        return $user_id ?? null;
    }

    /**
     * @param $email
     * @return bool
     */
    public function hasUserByEmail($email): bool
    {
        $has_user_id = $this->getUserIdByEmail($email);
        if ($has_user_id) {
            return true;
        }

        return false;
    }

    /**
     * ID пользователя по коду подтверждения регистрации на сайте
     * @param string $confirm_code
     * @return int|null
     */
    public function getUserIdByConfirmCode(string $confirm_code): ?int
    {
        $user_id = $this->repository->findUserIdByConfirmCode($confirm_code);

        return $user_id ?? null;
    }

    /**
     * Генератор пароля
     * @param $number
     * @return string
     */
    public function generatePassword($number): string
    {
        $arr = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];

        $pass = '';
        for ($i = 0; $i < $number; $i++) {
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }

        return $pass;
    }

    /**
     * Генератор кода подтверждения регистрации на сайте
     * @return string
     */
    public function generateConfirmCode(): string
    {
        $confirm_code = time() . uniqid();

        $confirm_code = Auth::getHash($confirm_code);

        return $confirm_code;
    }

    /**
     * @param string $provider_name
     * @param string $provider_uid
     * @return int|null
     */
    public function getUserIdIfExistByProvider(string $provider_name, string $provider_uid): ?int
    {
        $user_id = $this->repository->findUserIdIfExistByProvider($provider_name, $provider_uid);

        return $user_id ?? null;
    }

    /**
     * @param string $email
     * @param string $password
     * @return int|null
     */
    public function getUserIdByEmailAndPassword(string $email, string $password): ?int
    {
        $salt_password = Auth::getHash($password);

        $user_id = $this->repository->findUserIdByEmailAndPassword($email, $salt_password);

        return $user_id ?? null;
    }
}
