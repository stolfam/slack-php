<?php
    declare(strict_types=1);

    namespace Ataccama\Slack\Env;

    use Ataccama\Common\Env\BaseArray;
    use Nette\Utils\Strings;


    /**
     * Class Channels
     * @package Ataccama\Slack\Env
     */
    class ChannelArray extends BaseArray
    {
        /**
         * @param Channel $channel
         * @return ChannelArray
         */
        public function add($channel): ChannelArray
        {
            $this->items[$channel->id] = $channel;

            return $this;
        }

        /**
         * @return Channel|null
         */
        public function current(): ?Channel
        {
            return parent::current();
        }

        /**
         * Returns first occur.
         *
         * @param string $name
         * @return Channel|null
         */
        public function find(string $name): ?Channel
        {
            foreach ($this as $channel) {
                if (Strings::contains($channel->name, $name)) {
                    return $channel;
                }
            }

            return null;
        }

        /**
         * @param string $channelId
         * @return Channel
         */
        public function get($channelId): Channel
        {
            if (isset($this->items[$channelId])) {
                return parent::get($channelId);
            }
            throw new \OutOfBoundsException("Channel with ID $channelId is not in the array.");
        }
    }