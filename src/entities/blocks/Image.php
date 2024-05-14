<?php
    declare(strict_types=1);

    namespace Ataccama\Slack\Blocks;

    use Ataccama\Slack\Env\SlackMessageBlock;


    /**
     * Class Image
     * @package Ataccama\Slack\Blocks
     */
    class Image extends SlackMessageBlock
    {
        /** @var string */
        private string $title;
        /** @var string */
        private string $imageUrl;
        /** @var string */
        private string $alternativeText;

        /**
         * Image constructor.
         * @param string $title
         * @param string $imageUrl
         * @param string $alternativeText
         */
        public function __construct(string $title, string $imageUrl, string $alternativeText)
        {
            $this->title = $title;
            $this->imageUrl = $imageUrl;
            $this->alternativeText = $alternativeText;
        }

        public function toArray(): array
        {
            return [
                "type"      => self::TYPE_IMAGE,
                "title"     => [
                    "type"  => "plain_text",
                    "text"  => $this->title,
                    "emoji" => true
                ],
                "image_url" => $this->imageUrl,
                "alt_text"  => $this->alternativeText
            ];
        }
    }