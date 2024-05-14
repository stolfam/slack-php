<?php
    declare(strict_types=1);

    namespace Ataccama\Slack\Blocks;

    use Ataccama\Slack\Env\SlackMessageBlock;


    /**
     * Class Fields
     * @package Ataccama\Slack\Blocks
     */
    class Fields extends SlackMessageBlock
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
                    "type"  => "plain_text",
                    "text"  => $text,
                    "emoji" => true
                ];
            }

            return [
                "type"   => self::TYPE_SECTION,
                "fields" => $elements
            ];
        }

    }