<?php

namespace WebSK\Auth\User;

use WebSK\Entity\Entity;

/**
 * Class User
 * @package WebSK\Auth\User
 */
class User extends Entity
{
    const string DB_TABLE_NAME = 'users';

    const string _EMAIL = 'email';
    protected string $email;

    const string _PASSW = 'passw';
    protected string $passw;

    const string _NAME = 'name';
    protected string $name;

    const string _FIRST_NAME = 'first_name';
    protected ?string $first_name = null;

    const string _LAST_NAME = 'last_name';
    protected ?string $last_name = null;

    const string _PHOTO = 'photo';
    protected string $photo = '';

    const string _BIRTHDAY = 'birthday';
    protected ?string $birthday = null;

    const string _PHONE = 'phone';
    protected ?string $phone = null;

    const string _CITY = 'city';
    protected ?string $city = null;

    const string _ADDRESS = 'address';
    protected ?string $address = null;

    const string _COMPANY = 'company';
    protected ?string $company = null;

    const string _COMMENT = 'comment';
    protected string $comment = '';

    const string _CONFIRM = 'confirm';
    protected bool $confirm = false;

    const string _CONFIRM_CODE = 'confirm_code';
    protected string $confirm_code = '';

    const string _PROVIDER = 'provider';
    protected string $provider = '';

    const string _PROVIDER_UID = 'provider_uid';
    protected string $provider_uid = '';

    const string _PROFILE_URL = 'profile_url';
    protected string $profile_url = '';

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassw(): string
    {
        return $this->passw;
    }

    /**
     * @param string $passw
     */
    public function setPassw(string $passw): void
    {
        $this->passw = $passw;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    /**
     * @param ?string $first_name
     */
    public function setFirstName(?string $first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return ?string
     */
    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    /**
     * @param ?string $last_name
     */
    public function setLastName(?string $last_name): void
    {
        $this->last_name = $last_name;
    }

    /**
     * @return string
     */
    public function getPhoto(): string
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto(string $photo): void
    {
        $this->photo = $photo;
    }

    /**
     * @return ?string
     */
    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    /**
     * @param ?string $birthday
     */
    public function setBirthday(?string $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return ?string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param ?string $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return ?string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param ?string $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return ?string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param ?string $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return ?string
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param ?string $company
     */
    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @return string
     */
    public function getProviderUid(): string
    {
        return $this->provider_uid;
    }

    /**
     * @param string $provider_uid
     */
    public function setProviderUid(string $provider_uid): void
    {
        $this->provider_uid = $provider_uid;
    }

    /**
     * @return string
     */
    public function getProfileUrl(): string
    {
        return $this->profile_url;
    }

    /**
     * @param string $profile_url
     */
    public function setProfileUrl(string $profile_url): void
    {
        $this->profile_url = $profile_url;
    }

    /**
     * Регистрация пользователя подтверждена
     * @return bool
     */
    public function isConfirm(): bool
    {
        return $this->confirm;
    }

    /**
     * @param bool $confirm
     */
    public function setConfirm(bool $confirm): void
    {
        $this->confirm = $confirm;
    }

    /**
     * @return string
     */
    public function getConfirmCode(): string
    {
        return $this->confirm_code;
    }

    /**
     * @param string $confirm_code
     */
    public function setConfirmCode(string $confirm_code): void
    {
        $this->confirm_code = $confirm_code;
    }
}
