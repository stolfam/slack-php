<?php
    declare(strict_types=1);

    namespace Ataccama\Slack\Blocks;

    use Ataccama\Slack\Env\SlackMessageBlock;


    /**
     * Class Context
     * @package Ataccama\Slack\Blocks
     */
    class Context extends SlackMessageBlock
    {
        /** @var string[] */
        private array $texts = [];

        /**
         * Context constructor.
         * @param string[] $texts
         */
        public function __construct(array $texts)
        {
            $this->texts = $texts;
        }

        public function toArray(): array
        {
            $elements = [];
            foreach ($this->texts as $text) {
                $elements[] = [
                    "type" => "mrkdwn",
                    "text" => $text
                ];
            }

            return [
                "type"     => self::TYPE_CONTEXT,
                "elements" => $elements
            ];
        }
    }