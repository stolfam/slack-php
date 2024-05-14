<?php
    declare(strict_types=1);

    namespace Ataccama\Slack\Blocks;

    use Ataccama\Slack\Env\SlackMessageBlock;


    /**
     * Class Section
     * @package Ataccama\Slack\Blocks
     *
     * This class represents default text.
     * You can choose if it has to be mark-downed or plain-text like.
     */
    class Section extends SlackMessageBlock
    {
        const TYPE_PLAIN_TEXT = "plain_text";
        const TYPE_MARK_DOWN = "mrkdwn";

        /** @var string */
        private string $text;

        /** @var string */
        private string $type;

        /**
         * Section constructor.
         * @param string $text
         * @param string $type
         */
        public function __construct(string $text, string $type = self::TYPE_MARK_DOWN)
        {
            $this->text = $text;
            $this->type = $type;
        }

        public function toArray(): array
        {
            return [
                "type" => self::TYPE_SECTION,
                "text" => [
                    "type" => $this->type,
                    "text" => $this->text,
                ]
            ];
        }
    }