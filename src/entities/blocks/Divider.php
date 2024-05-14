<?php
    declare(strict_types=1);

    namespace Ataccama\Slack\Blocks;

    use Ataccama\Slack\Env\SlackMessageBlock;


    /**
     * Class Divider
     * @package Ataccama\Slack\Blocks
     */
    class Divider extends SlackMessageBlock
    {
        public function toArray(): array
        {
            return [
                "type" => self::TYPE_DIVIDER,
            ];
        }
    }