<?php
    declare(strict_types=1);

    namespace Ataccama\Slack\Env;

    use Ataccama\Common\Env\Message;


    /**
     * Class SlackMessage
     * @package Ataccama\Slack\Env
     */
    final class SlackMessage extends Message
    {
        /** @var bool */
        protected bool $as_user = true;

        /** @var string */
        protected string $icon_emoji = ":robot_face:", $username = "Bot";

        /** @var SlackMessageBlock[] */
        protected array $blocks = [];

        /**
         * SlackMessage constructor.
         * @param string $text
         */
        public function __construct(string $text = "")
        {
            parent::__construct($text);
        }

        /**
         * @param SlackMessageBlock $block
         * @return SlackMessage
         */
        public function addBlock(SlackMessageBlock $block): SlackMessage
        {
            $this->blocks[] = $block;

            return $this;
        }

        /**
         * @param Channel $channel
         * @return array
         */
        public function createMessage(Channel $channel): array
        {
            $message = [
                "channel"    => $channel->id,
                "as_user"    => $this->as_user,
                "username"   => $this->username,
                "icon_emoji" => $this->icon_emoji,
                "text"       => $this->text
            ];

            foreach ($this->blocks as $block) {
                $message["blocks"][] = $block->toArray();
            }

            return $message;
        }
    }