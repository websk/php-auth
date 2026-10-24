<?php

namespace WebSK\Auth\User;

use PHPMailer\PHPMailer\PHPMailer;
use WebSK\Auth\Auth;
use WebSK\FileManager\FileManager;
use WebSK\Cache\CacheService;
use WebSK\Entity\EntityService;
use WebSK\Entity\EntityRepository;
use WebSK\Entity\InterfaceEntity;
use WebSK\Config\ConfWrapper;
use WebSK\Utils\Sanitize;

/**
 * Class UserService
 * @method User|null getById(?int $entity_id, bool $exception_if_not_loaded = true)
 * @package WebSK\Auth\User
 */
class UserService extends EntityService
{
    public const int PASSWORD_LENGTH = 12;
    public const string PHOTO_STORAGE = 'files';
    public const string PHOTO_DIR = 'user';
    public const string PHOTO_DIR_INSIDE_STORAGE = 'images' . DIRECTORY_SEPARATOR . UserService::PHOTO_DIR;
    public const string PHOTO_URL_RELATIVE_TO_SITE_ROOT = DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . UserService::PHOTO_DIR_INSIDE_STORAGE;

    protected RoleService $role_service;

    protected UserRoleService $user_role_service;

    /** @var UserRepository */
    protected EntityRepository $repository;

    public function __construct(
        string $entity_class_name,
        UserRepository $repository,
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
    public function beforeSave(InterfaceEntity $entity_obj): void
    {
        if (!$entity_obj instanceof User) {
            throw new \InvalidArgumentException('UserService can only save User entities.');
        }
        if (!$entity_obj->getEmail()) {
            throw new \Exception('Ошибка! Не указан Email.');
        }

        if (!$entity_obj->getName()) {
            throw new \Exception('Ошибка! Не указано имя на сайте.');
        }

        $email = '';
        if ($entity_obj->getId()) {
            $exist_user_obj = $this->getById($entity_obj->getId());
            if ($exist_user_obj === null) {
                throw new \UnexpectedValueException('Existing user was not found.');
            }
            $email = $exist_user_obj->getEmail();
        }

        if ($email != $entity_obj->getEmail()) {
            $has_user_id = $this->hasUserByEmail($entity_obj->getEmail());
            if ($has_user_id) {
                throw new \Exception('Ошибка! Пользователь с таким адресом электронной почты ' . $entity_obj->getEmail() . ' уже существует.');
            }
        }

        parent::beforeSave($entity_obj);
    }

    /**
     * @param int $user_id
     * @throws \Exception
     */
    public function deleteUserRolesForUserId(int $user_id): void
    {
        $user_roles_ids_arr = $this->user_role_service->getIdsArrByUserId($user_id);

        foreach ($user_roles_ids_arr as $user_role_id) {
            $user_role_obj = $this->user_role_service->getById($user_role_id);
            if ($user_role_obj === null) {
                continue;
            }
            $this->user_role_service->delete($user_role_obj);
        }
    }

    /** @return list<int> */
    public function getRoleIdsArrByUserId(int $user_id): array
    {
        $user_roles_ids_arr = $this->user_role_service->getIdsArrByUserId($user_id);

        $role_ids_arr = [];

        foreach ($user_roles_ids_arr as $user_role_id) {
            $user_role_obj = $this->user_role_service->getById($user_role_id);
            if ($user_role_obj === null) {
                continue;
            }
            $role_ids_arr[] = $user_role_obj->getRoleId();
        }

        return $role_ids_arr;
    }

    /**
     * @param User $user_obj
     * @return bool
     * @throws \Exception
     */
    public function deletePhoto(User $user_obj): bool
    {
        if (!$user_obj->getPhoto()) {
            return true;
        }

        $photo = $user_obj->getPhoto();
        $user_obj->setPhoto('');
        $this->save($user_obj);

        $file_manager = new FileManager(self::PHOTO_STORAGE);

        $file_path = self::PHOTO_DIR_INSIDE_STORAGE . DIRECTORY_SEPARATOR . $photo;
        if ($file_manager->getStorage()->fileExists($file_path)) {
            try {
                $file_manager->deleteFileIfExist($file_path);
            } catch (\Throwable $exception) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param InterfaceEntity|User $entity_obj
     * @throws \Exception
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        if (!$entity_obj instanceof User) {
            throw new \InvalidArgumentException('UserService can only delete User entities.');
        }
        $this->deletePhoto($entity_obj);
        $user_id = $entity_obj->getId();
        if ($user_id !== null) {
            $this->deleteUserRolesForUserId($user_id);
        }

        parent::afterDelete($entity_obj);
    }

    /**
     * Является ли пользователь администратором
     * @param int $user_id
     * @return bool
     * @throws \Exception
     */
    public function hasRoleAdminByUserId(int $user_id): bool
    {
        if (in_array(Role::ADMIN_ROLE_ID, $this->getRoleIdsArrByUserId($user_id))) {
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
            if ($role_obj === null) {
                continue;
            }

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
        $photo = $user_obj->getPhoto();
        if ($photo === '') {
            return '';
        }

        $photo_url = self::PHOTO_URL_RELATIVE_TO_SITE_ROOT . '/' . rawurlencode(basename($photo));

        return '<img src="' . Sanitize::sanitizeAttrValue($photo_url)
            . '" alt="' . Sanitize::sanitizeAttrValue($user_obj->getName())
            . '" width="75" loading="lazy">';
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
        if ($user_obj === null) {
            throw new \UnexpectedValueException('User was not found.');
        }
        $user_obj->setPassw(Auth::hashPassword($new_password));
        $this->save($user_obj);

        if ($user_obj->getEmail()) {
            $site_email = self::configString('site_email');
            $site_domain = self::configString('site_domain');
            $site_name = self::configString('site_name');

            $mail_message = '<p>Добрый день, ' . Sanitize::sanitizeTagContent($user_obj->getName()) . '</p>';
            $mail_message .= '<p>Вы воспользовались формой восстановления пароля на сайте ' . Sanitize::sanitizeTagContent($site_name) . '</p>';
            $mail_message .= '<p>Ваш новый пароль: ' . Sanitize::sanitizeTagContent($new_password) . '<br>';
            $mail_message .= 'Ваш email для входа: ' . Sanitize::sanitizeTagContent($user_obj->getEmail()) . '</p>';
            $mail_message .= "<p>Рекомендуем сменить пароль после входа на сайт.</p>";
            $mail_message .= '<p>' . Sanitize::sanitizeTagContent($site_domain) . '</p>';

            $subject = "Смена пароля на сайте " . $site_name;

            $mail = new PHPMailer;
            $mail->CharSet = "utf-8";
            $mail->setFrom($site_email, $site_name);
            $mail->addAddress($user_obj->getEmail());
            $mail->Subject = $subject;
            $mail->Body = $mail_message;
            $mail->AltBody = Sanitize::sanitizeTagContent($mail_message);
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
    public function hasUserByEmail(string $email): bool
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
    public function generatePassword(int $number): string
    {
        $arr = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];

        $pass = '';
        for ($i = 0; $i < $number; $i++) {
            $index = random_int(0, count($arr) - 1);
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
        return bin2hex(random_bytes(32));
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
        $user_id = $this->getUserIdByEmail($email);
        if ($user_id === null) {
            return null;
        }

        $user = $this->getById($user_id);
        if ($user === null) {
            return null;
        }
        if (!Auth::verifyPassword($password, $user->getPassw())) {
            return null;
        }

        if (Auth::passwordNeedsRehash($user->getPassw())) {
            $user->setPassw(Auth::hashPassword($password));
            $this->save($user);
        }

        return $user_id;
    }

    private static function configString(string $key): string
    {
        $value = ConfWrapper::value($key);
        if (!is_string($value)) {
            throw new \UnexpectedValueException('Configuration value "' . $key . '" must be a string.');
        }

        return $value;
    }
}
