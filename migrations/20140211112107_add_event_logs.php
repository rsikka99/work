<?php

use Phinx\Migration\AbstractMigration;

class AddEventLogs extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS `event_log_types` (
        `id`             VARCHAR(255)   NOT NULL,
        `name`           VARCHAR(255)   NOT NULL,
        `description`    VARCHAR(255)   NULL,
        PRIMARY KEY (`id`))
    ');

        $this->execute('CREATE TABLE IF NOT EXISTS `event_logs` (
        `id`                   INT(11)      NOT NULL AUTO_INCREMENT,
        `eventLogTypeId`       VARCHAR(255) NOT NULL,
        `timestamp`            DATETIME     NOT NULL,
        `message`              VARCHAR(255) NULL,
        `ipAddress`            VARCHAR(255) NOT NULL,
        INDEX `event_logs_ibfk_1_idx` (`eventLogTypeId` ASC),
        CONSTRAINT `event_logs_ibfk_1`
        FOREIGN KEY (`eventLogTypeId`)
        REFERENCES `event_log_types` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            PRIMARY KEY(`id`))
            ');

        $this->execute('CREATE TABLE IF NOT EXISTS `user_event_logs` (
        `userId`         INT(11)      NOT NULL,
        `eventLogId`     INT(11)      NOT NULL,
        INDEX `user_event_logs_ibfk_1_idx` (`userId` ASC),
        INDEX `user_event_logs_ibfk_2_idx` (`eventLogId` ASC),
        CONSTRAINT `user_event_logs_ibfk_1`
        FOREIGN KEY (`userId`)
        REFERENCES `users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
        CONSTRAINT `user_event_logs_ibfk_2`
        FOREIGN KEY (`eventLogId`)
        REFERENCES `event_logs` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE)
            ');

        $this->execute("INSERT INTO event_log_types (`id`, `name`, `description`) VALUES
        ('login', 'Login Success', 'User logged into the system'),
        ('login_fail', 'Login Failure', 'User failed to login to the system'),
        ('logout', 'Logged Out', 'User logged out of the system'),
        ('change_password', 'Password - User Changed', 'User changed their password'),
        ('forgot_password_send', 'Forgot Password - Email Sent', 'User sent a forgot password request'),
        ('forgot_password_changed', 'Forgot Password - Password Changed', 'User changed their password through a forgot password request')
        ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("DROP TABLE `user_event_logs`");
        $this->execute("DROP TABLE `event_logs`");
        $this->execute("DROP TABLE `event_log_types`");

    }
}