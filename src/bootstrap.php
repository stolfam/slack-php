<?php
    /**
     * Created by PhpStorm.
     * User: miroslav
     * Date: 23/04/2019
     * Time: 10:24
     */

    // for tests run: vendor/bin/tester tests/

    require __DIR__ . "/exceptions/SlackException.php";

    require __DIR__ . "/entities/SlackMessageBlock.php";
    require __DIR__ . "/entities/blocks/Context.php";
    require __DIR__ . "/entities/blocks/Divider.php";
    require __DIR__ . "/entities/blocks/Fields.php";
    require __DIR__ . "/entities/blocks/Image.php";
    require __DIR__ . "/entities/blocks/Section.php";
    require __DIR__ . "/entities/SlackMessage.php";
    require __DIR__ . "/entities/Channel.php";
    require __DIR__ . "/entities/ChannelArray.php";

    require __DIR__ . "/core/Slack.php";