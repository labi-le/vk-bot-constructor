<?php

declare(strict_types=1);

namespace Astaroth\Commands;


use Astaroth\DataFetcher\Events\MessageEvent;
use Astaroth\DataFetcher\Events\MessageNew;
use Astaroth\Foundation\Utils;
use Astaroth\Support\Facades\Create;
use Astaroth\Support\Facades\Request;
use Astaroth\VkUtils\Builders\Message;
use Astaroth\VkUtils\Contracts\IBuilder;

/**
 * Class BaseCommands
 * @package Astaroth\Commands
 */
abstract class BaseCommands
{

    /**
     * @throws \Throwable
     */
    protected function message(int $peer_id, string $text = null, array $attachment = [], string $access_token = null): array
    {
        $message = (new Message())
            ->setPeerId($peer_id)
            ->setMessage($text)
            ->setAttachment(...$attachment);

        if ($access_token) {
            return Create::changeToken($access_token)->create($message);
        }

        return Create::new($message);
    }

    /**
     * @param MessageNew|MessageEvent $data
     * @param array $event
     * @return array
     * @throws \Throwable
     * @see https://vk.com/dev/messages.sendMessageEventAnswer
     */
    protected function sendMessageEventAnswer(MessageNew|MessageEvent $data, array $event): array
    {
        return Request::call("messages.sendMessageEventAnswer",
            [
                "event_id" => $data->getEventId(),
                "user_id" => $data->getUserId(),
                "peer_id" => $data->getPeerId(),
                "event_data" => json_encode($event)
            ]
        );
    }

    /**
     * @param IBuilder $message
     * @param int|null $conversation_message_id
     * @param int|null $message_id
     * @return array
     * @throws \Throwable
     * @see https://vk.com/dev/messages.edit
     */
    protected function messagesEdit(IBuilder $message, int $conversation_message_id = null, int $message_id = null): array
    {
        $params = $message->getParams();
        $params["peer_id"] = $params["peer_ids"];
        $params["conversation_message_id"] = $conversation_message_id;
        $params["message_id"] = $message_id;

        return Request::call("messages.edit", $params);
    }

    /**
     * @param int $chat_id
     * @param int $id
     * @return array
     * @throws \Throwable
     * @see https://vk.com/dev/messages.removeChatUser
     */
    protected function kick(int $chat_id, int $id): array
    {
        return Request::call("messages.removeChatUser", ["chat_id" => $chat_id, "member_id" => $id]);
    }

    /**
     * @param int[] $user_ids
     * @param string[] $fields
     * @param string $name_case
     * @return array
     * @throws \Throwable
     * @see https://vk.com/dev/users.get
     */
    protected function usersGet(array $user_ids, array $fields = [], string $name_case = "nom"): array
    {
        return Request::call("users.get",
            [
                "user_ids" => implode(",", $user_ids),
                "fields" => implode(",", $fields),
                "name_case" => $name_case
            ]
        );
    }

    /**
     * @param int[] $group_ids
     * @param string[] $fields
     * @return array
     * @throws \Throwable
     * @see https://vk.com/dev/groups.getById
     */
    protected function groupsGetById(array $group_ids, array $fields = []): array
    {
        return Request::call("groups.getById",
            [
                "group_ids" => implode(",", $group_ids),
                "fields" => implode(",", $fields)
            ]
        );
    }

    /**
     * @param string $url
     * @param bool $private
     * @return array
     * @throws \Throwable
     * @see https://vk.com/dev/utils.getShortLink
     */
    protected function utilsGetShortLink(string $url, bool $private = false): array
    {
        return Request::call("utils.getShortLink", ["url" => $url, "private" => $private]);
    }

    /**
     * @throws \Throwable
     */
    protected function logToMessage(int $id, string $error_level, \Exception|string $e): void
    {
        Utils::logToMessage($id, $error_level, $e);
    }
}