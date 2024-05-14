<?php
    declare(strict_types=1);

    namespace Ataccama\Slack\Env;

    use Ataccama\Common\Env\IArray;


    /**
     * Class SlackMessageBlock
     * @package Ataccama\Slack\Env
     */
    abstract class SlackMessageBlock implements IArray
    {
        /** @var string */
        const TYPE_SECTION = "section";

        /** @var string */
        const TYPE_CONTEXT = "context";

        /** @var string */
        const TYPE_DIVIDER = "divider";

        /** @var string */
        const TYPE_IMAGE = "image";
    }