<?php

    require __DIR__ . "/../bootstrap.php";

    use Tester\Assert;


    // set up before tests
    const TEST_TOKEN = "xoxb-xxxxxxxxx-xxxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxx";
    const TEST_CHANNEL = "XXXXXXXXXX";


    $slack = new \Ataccama\Output\Slack\Slack([
        "token"  => TEST_TOKEN,
        "enable" => true
    ]);

    $message = new \Ataccama\Slack\Env\SlackMessage();
    $message->addBlock(new \Ataccama\Slack\Blocks\Section("Some section"));
    $message->addBlock(new \Ataccama\Slack\Blocks\Context(["Some context"]));
    $message->addBlock(new \Ataccama\Slack\Blocks\Divider());
    $message->addBlock(new \Ataccama\Slack\Blocks\Fields(["Field 1", "Field 2", "Field 3"]));
    $message->addBlock(new \Ataccama\Slack\Blocks\Image("A goat",
        "https://upload.wikimedia.org/wikipedia/commons/b/b2/Hausziege_04.jpg", "image of goat"));

    $response = $slack->sendMessage($message, new \Ataccama\Slack\Env\Channel(TEST_CHANNEL, "Sandbox"));

    // comment when you set valid credentials
    Assert::same(false, $response);
    Assert::same("invalid_auth", $slack->lastError);

    // uncomment when you set valid credentials
//    Assert::same(true, $response);