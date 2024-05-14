<?php

    require __DIR__ . "/../bootstrap.php";

    use Tester\Assert;


    $channelArray = new \Ataccama\Slack\Env\ChannelArray();

    $channelArray->add(new \Ataccama\Slack\Env\Channel("CABCDEFG", "#TestChannel"));
    $channelArray->add(new \Ataccama\Slack\Env\Channel("CXOXOXO", "Foo"));

    Assert::count(2, $channelArray);

    Assert::same("CABCDEFG",$channelArray->find("Test")->id);

    Assert::same("Foo",$channelArray->get("CXOXOXO")->name);