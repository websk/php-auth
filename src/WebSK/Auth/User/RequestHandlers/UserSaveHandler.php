<?php

namespace WebSK\Auth\User\RequestHandlers;

use Slim\Http\StatusCode;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\Auth;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserServiceProvider;

/**
 * Class UserSaveHandler
 * @package WebSK\Auth\User\RequestHandlers
 */
class UserSaveHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int|null $user_id
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, ?int $user_id = null)
    {
        $user_service = UserServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id, false);
        if (!$user_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        if (($user_id != Auth::getCurrentUserId()) && !Auth::currentUserIsAdmin()) {
            return $response->withStatus(StatusCode::HTTP_FORBIDDEN);
        }

        $destination = $request->getParam('destination', $this->pathFor(UserRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_id]));

        $name = $request->getParam('name', '');
        $first_name = $request->getParam('first_name', '');
        $last_name = $request->getParam('last_name', '');
        $confirm = $request->getParam('confirm', false);
        $birthday = $request->getParam('birthday', '');
        $email = $request->getParam('email', '');
        $phone = $request->getParam('phone', '');
        $city = $request->getParam('city', '');
        $address = $request->getParam('address', '');
        $comment = $request->getParam('comment', '');

        if (empty($email)) {
            Messages::setError('Ошибка! Не указан Email.');
            return $response->withRedirect($destination);
        }

        if (empty($name)) {
            Messages::setError('Ошибка! Не указаны Фамилия Имя Отчество.');
            return $response->withRedirect($destination);
        }

        if ($email != $user_obj->getEmail()) {
            $has_user_id = $user_service->hasUserByEmail($email);
            if ($has_user_id) {
                Messages::setError('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже существует.');
                return $response->withRedirect($destination);
            }
        }

        if (Auth::currentUserIsAdmin()) {
            $user_obj->setConfirm($confirm);
        }

        $user_obj->setName($name);
        $user_obj->setFirstName($first_name);
        $user_obj->setLastName($last_name);
        $user_obj->setBirthday($birthday);
        $user_obj->setPhone($phone);
        $user_obj->setEmail($email);
        $user_obj->setCity($city);
        $user_obj->setAddress($address);
        $user_obj->setComment($comment);

        $user_service->save($user_obj);

        $user_id = $user_obj->getId();

        Messages::setMessage('Информация о пользователе была успешно сохранена');

        $destination = str_replace('/create', '/' . $user_id, $destination);

        return $response->withRedirect($destination);
    }
}
