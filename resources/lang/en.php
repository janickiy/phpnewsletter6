<?php

return [
    'str' => [
        'import' => 'Импорт',
        'export' => 'Экспорт',
        'delete_all' => 'Удалить всех',
        'delete_all_subscribers' => 'Удалить всех подписчиков',
        'import_subscribers' => 'Импорт подписчиков',
        'export_subscribers' => 'Экспорт подписчиков',
        'remove_all_subscribers' => 'Удалить всех подписчиков',
        'want_to_delete_all_subscribers' => 'Вы действительно хотите удалить всех подписчиков?',
        'add_subscriber' => 'Добавить  подписчика',
        'check_uncheck_all' => 'Отметить все/Снять отметку у всех',
        'name' => 'Имя',
        'value' => 'Значение',
        'subscribers_number' => 'Количество подписчиков',
        'status' => 'Статус',
        'added' => 'Добавлен',
        'action' => 'Действие',
        'activate' => 'Активировать',
        'deactivate' => 'Деактивировать',
        'remove' => 'Удалить',
        'apply' => 'Применить',
        'no' => 'нет',
        'add_category' => 'Добавить категорию',
        'interface_settings' => 'Настройки интерфейса',
        'mailing_options' => 'Параметры рассылки',
        'additional_headers' => 'Дополнительные заголовки',
        'add_smtp_server' => 'Добавить SMTP сервер',
        'smtp_server' => 'SMTP сервер',
        'login' => 'Логин',
        'port' => 'Порт',
        'connection_timeout' => 'Таймаут соединения',
        'connection' => 'Подключение',
        'authentication_method' => 'Метод аутентификации',
        'add_template' => 'Добавить шаблон',
        'template' => 'Шаблон',
        'category' => 'Категория',
        'importance' => 'Важность',
        'date' => 'Дата',
        'add_user' => 'Добавить пользователя',
        'description' => 'Описание',
        'role' => 'Роль',
        'admin' => 'Админ',
        'moderator' => 'Модератор',
        'edit' => 'Редактор',
        'footer_copyright' => '<a href="http:://janicky.com">PHP Newsletter</a>, Яницкий Александр</span>',
        'roll_up' => 'Свернуть',
        'expand_full_screen' => 'Развернуть на весь экран',
        'signout' => 'Выйти',
    ],
    'msg' => [
        'are_you_sure' => 'Are you sure?',
        'will_not_be_able_to_ecover_information' => 'Вы не сможете восстановить эту информацию!',
        'yes_remove' => 'Да, удалить!',
        'done' => 'Сделано!',
        'data_successfully_deleted' => 'Данные успешно удалены!',
        'error_eleting' => 'Ошибка при удалении!',
        'try_again' => 'Попробуйте еще раз',
    ],
    'form' => [
        'required_fields' => 'обязательные поля',
        'attach_files' => 'Присоединить файлы',
        'browse' => 'Обзор',
        'select_files' => 'Выберите файлы',
        'maximum_size' => 'Максимальный размер',
        'charset' => 'Кодировка',
        'select' => 'Выберите',
        'subscribers_category' => 'Категория подписчиков',
        'select_category' => 'Выберите категорию',
        'format' => 'Формат',
        'text' => 'Текст',
        'compress' => 'Упаковать',
        'name' => 'Имя',
        'value' => 'Значение',
        'login' => 'Логин',
        'password' => 'Пароль',
        'port' => 'Порт',
        'connection_timeout' => 'Таймаут соединения',
        'secure_connection' => 'Подключаться через безопасное соединение',
        'authentication_method' => 'Метод аутентификации',
        'low_secrecy' => 'низкая секретность',
        'medium_secrecy' => 'средняя секретность',
        'high_secrecy' => 'высокая секретность',
        'remove' => 'Удалить',
        'add' => 'Добавить',
        'show_admin_email_in_sent_emails' => 'Показывать e-mail администратора в отправленных письмах',
        'admin_email_name' => 'Имя к E-mail администратора (from)',
        'return_path' => 'Обратный адрес (Return-path)',
        'list_owner' => 'Email адрес организатора рассылки (List-Owner)',
        'organization' => 'Организация',
        'subject_text_confirm' => 'Тема подтверждения рассылки',
        'text_confirmation' => 'Текст подтверждения рассылки',
        'require_subscription_confirmation' => 'Требовать подтверждение подписки',
        'unsublink_text' => 'Текст ссылки отписки от рассылки',
        'show_unsubscribe_link' => 'Показывать форму отписки от  рассылки',
        'request_reply' => '>Запрашивать уведомления о прочтении писем',
        'new_subscriber_notify' => 'Уведомлять о новом подписчике',
        'limit_number' => 'Отправлять не более писем за раз',
        'sleep' => 'Задержка между отправки писем (сек.)',
        'days_for_remove_subscriber' => 'Удалять подписчиков которые не подтвердили подписку в течении дней',
        'random_send' => 'Рандомизация рассылки',
        'rendom_replacement_subject' => 'Рандомная замена кириллицы в загаловке письма на латиницу (обход спам фильтра)',
        'random_replacement_body' => 'Рандомная замена кириллицы в теле  письма на латиницу (обход спам фильтра)',
        'content_type' => 'Формат исходящих писем',
        'how_to_send' => 'Способ отправки',
        'sendmail_path' => 'Путь к Sendmail',
        'add_dkim' => 'Добавить подпись DKIM в заголовок  письма',
        'dkim_domain' => 'Подписанный домен',
        'dkim_selector' => 'Селектор',
        'dkim_private' => 'Файл секретного ключа(dkim private)',
        'dkim_passphras' => 'Ключевое слово',
        'dkim_identity' => 'Подписанная личность (E-mail)',
        'smtp_server' => 'SMTP сервер',
        'template' => 'Шаблон',
        'prior' => 'Важность',
        'normal' => 'Нормальная',
        'low' => 'Низкая',
        'high' => 'Высокая',
        'send' => 'Отправить',
        'back' => 'Назад',
        'description' => 'Описание',
        'role' => 'Роль',
        'password_again' => 'Павтор пароля',
    ],
    'note' => [
        'personalization' => '<strong>Персонализация:</strong> %NAME% - имя подписчика, %EMAIL% - E-mail адрес, %UNSUB% - ссылка для удаления рассылки, %SERVER_NAME% - адрес сайта, %REFERRAL:http://my_website.com/% - ссылка для статистика переходов по ссылкам'
    ],
    'menu' => [
        'templates' => 'Шаблоны',
        'subscribers' => 'Подписчики',
        'subscribers_category' => 'Категория подписчиков',
        'logs' => 'Логи',
        'mailing_log' => 'Журнал рассылки',
        'referrens_log' => 'Статистика переходов по ссылкам',
        'settings' => 'Настройки',
        'users' => 'Пользователи',
    ],
];