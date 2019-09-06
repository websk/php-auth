<?php

namespace WebSK\Auth;

use WebSK\Auth\User\User;
use WebSK\Auth\User\UserRole;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Config\ConfWrapper;
use WebSK\Image\ImageManager;
use WebSK\Slim\Container;
use WebSK\Utils\Messages;
use WebSK\Utils\Url;

class HybridAuth
{
    /**
     * @return bool
     */
    public static function useSocialLogin()
    {
        $social_config = ConfWrapper::value('auth.hybrid');
        if (!empty($social_config)) {
            return true;
        }

        return false;
    }

    /**
     * URL авторизации на сайте через внешнего провайдера социальной сети
     * @param $provider
     * @return string
     */
    public static function getSocialLoginUrl($provider)
    {
        return '/user/social_login/' . $provider;
    }

    /**
     * @param $provider_name
     * @param $destination
     * @return \Hybrid_Provider_Adapter|null
     */
    public static function socialLogin($provider_name, $destination)
    {
        $config = ConfWrapper::value('auth.hybrid');

        $params = array();

        $message = "Неизвестная ошибка авторизации";

        if (!array_key_exists($provider_name, $config['providers'])) {
            Messages::setError($message);
            return null;
        }

        $filtered_destination = filter_var($destination, FILTER_VALIDATE_URL);
        if ($filtered_destination) {
            $params['hauth_return_to'] = Url::getUriNoQueryString() . '?destination='
                . $filtered_destination . '&Provider=' . $provider_name;
            //$params['hauth_return_to'] = $filtered_destination;
        }

        //hybridauth use exception for control
        try {
            $hybrid_auth = new \Hybrid_Auth($config);

            $provider = $hybrid_auth->authenticate($provider_name, $params);
            //if user is not logged in hybrid will initialize login process and redirect with die(),
            //so next line will be run only if there is logged in user or any error occurred
            return $provider;
        } catch (\Exception $e) {
            switch ($e->getCode()) {
                case 0:
                    $message = "Unspecified error.";
                    break;
                case 1:
                    $message = "Hybriauth configuration error.";
                    break;
                case 2:
                    $message = "Provider not properly configured.";
                    break;
                case 3:
                    $message = "Unknown or disabled provider.";
                    break;
                case 4:
                    $message = "Missing provider application credentials.";
                    break;
                case 5:
                    $message = "Authentication failed. The user has canceled the authentication or the provider refused the connection.";
                    break;
                case 6:
                    $message = "Authentication failed. The user has canceled the authentication or the provider refused the connection.";
                    break;

                default:
                    $message = "Unspecified error!";
            }
            Messages::setError($message);
        }

        return null;
    }

    /**
     * @param $user_profile \Hybrid_User_Profile
     * @param $provider
     * @return bool
     */
    public static function registerUserByHybridAuthProfile($user_profile, $provider)
    {
        $user_obj = new User();

        $user_obj->setProvider($provider);
        $user_obj->setProviderUid($user_profile->identifier);
        $user_obj->setProfileUrl($user_profile->profileURL);
        $user_obj->setName($user_profile->displayName);
        $user_obj->setFirstName($user_profile->firstName);
        $user_obj->setLastName($user_profile->lastName);

        // twitter и vkontakte не дают адрес почты
        if ($user_profile->email) {
            $user_obj->setEmail($user_profile->email);
        }

        /*
        if (!empty($user_profile->email)) {
            $user_obj->email_verified = ($user_profile->emailVerified === $user_profile->email);
        }
        */

        if (!empty($user_profile->photoURL)) {
            // save remote image to local
            $photo = self::saveRemoteUserProfileImage($user_profile->photoURL);
            $user_obj->setPhoto($photo);
        }

        $container = Container::self();

        $user_service = UserServiceProvider::getUserService($container);
        $user_service->save($user_obj);

        if (!$user_obj->getId()) {
            return false;
        }

        // Roles
        $role_id = ConfWrapper::value('user.default_role_id', 0);

        $user_role_obj = new UserRole();
        $user_role_obj->setUserId($user_obj->getId());
        $user_role_obj->setRoleId($role_id);

        $user_role_service = UserServiceProvider::getUserRoleService($container);
        $user_role_service->save($user_role_obj);

        return $user_obj->getId();
    }

    /**
     * @param string $image_path
     * @return string
     */
    protected static function saveRemoteUserProfileImage($image_path)
    {
        $image_manager = new ImageManager();
        $image_name = $image_manager->storeRemoteImageFile($image_path, 'user');

        return $image_name;
    }
}
