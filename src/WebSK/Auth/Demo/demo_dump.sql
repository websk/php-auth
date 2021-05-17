# password '12345' with config 'salt' => 'webskskif'
INSERT INTO `users` (`id`, `email`, `passw`, `name`, `first_name`, `last_name`, `photo`, `birthday`, `phone`, `city`, `address`, `company`, `comment`, `confirm`, `confirm_code`, `provider`, `provider_uid`, `profile_url`, `created_at_ts`)
VALUES (1, 'demo@websk.ru', '4fb143e2df8c137525040ac54901e31c', 'Администратор', '', '', '', '', '', '', '', '', '', 1, '', '', NULL, NULL, 0);

INSERT INTO `users_roles` (`id`, `user_id`, `role_id`) VALUES (1, 1, 1);