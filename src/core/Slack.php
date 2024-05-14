<?php
    /**
     * Created by PhpStorm.
     * User: miroslav
     * Date: 19/02/2019
     * Time: 16:47
     */
    declare(strict_types=1);

    namespace Ataccama\Output\Slack;

    use Ataccama\Common\Env\Email;
    use Ataccama\Common\Env\Name;
    use Ataccama\Common\Env\Person;
    use Ataccama\Common\Utils\Cache\DataStorage;
    use Ataccama\Common\Utils\Cache\Key;
    use Ataccama\Output\Slack\Exception\SlackException;
    use Ataccama\Slack\Env\Channel;
    use Ataccama\Slack\Env\ChannelArray;
    use Ataccama\Slack\Env\Member;
    use Ataccama\Slack\Env\MemberList;
    use Ataccama\Slack\Env\SlackMessage;
    use Curl\Curl;
    use Exception;
    use Tracy\Debugger;


    /**
     * Class Slack
     * @package Ataccama\Outputs
     */
    class Slack
    {
        /** @var string */
        private string $token;

        /** @var bool */
        private bool $enable = false;

        /**
         * @var string[]
         */
        private array $blacklist = [];

        /** @var string|null */
        public ?string $lastError;

        /** @var SlackMessage|null */
        public ?SlackMessage $lastMessage;

        /** @var DataStorage|null */
        private static ?DataStorage $cache;

        /**
         * Slack constructor.
         * @param array $parameters
         * @throws Exception
         */
        public function __construct(array $parameters)
        {
            $requiredParameters = ['token', 'enable'];
            foreach ($requiredParameters as $parameter) {
                if (isset($parameters[$parameter])) {
                    $this->$parameter = $parameters[$parameter];
                } else {
                    throw new SlackException("Required parameter $parameter is missing. Define it in config/services/slack.neon");
                }
            }

            if (isset($parameters["blacklist"])) {
                $this->blacklist = $parameters["blacklist"];
            }
        }

        /**
         * @param DataStorage $dataStorage
         */
        public static function setCache(DataStorage $dataStorage): void
        {
            self::$cache = $dataStorage;
        }

        /**
         * @return bool
         */
        public function isEnabled(): bool
        {
            return $this->enable;
        }

        /**
         * @param Email $email
         * @return Person
         * @throws SlackException
         */
        public function getUserByEmail(Email $email): Person
        {
            $curl = new Curl();
            $curl->setHeader("Authorization", "Bearer $this->token");
            $curl->setHeader("Content-Type", "application/json; charset=utf-8");
            $curl->get("https://slack.com/api/users.lookupByEmail", ['email' => $email->definition]);

            if ($curl->error) {
                Debugger::log('Slack lookup error: ' . $curl->errorCode . ': ' . $curl->errorMessage . '');
                throw new SlackException('Slack lookup error: ' . $curl->errorCode . ': ' . $curl->errorMessage . '');
            }

            if (!$curl->response->ok) {
                throw new SlackException("User ($email->definition) not found. Slack error code: [" .
                    $curl->response->error . "]");
            }

            return new Person($curl->response->user->id, new Name($curl->response->user->real_name), $email);
        }

        /**
         * @param string $userId
         * @return Person
         * @throws SlackException
         */
        public function getUserByUserID(string $userId): Person
        {
            $curl = new Curl();
            $curl->setHeader("Authorization", "Bearer $this->token");
            $curl->setHeader("Content-Type", "application/json; charset=utf-8");
            $curl->get("https://slack.com/api/users.info", ['user' => $userId]);

            if ($curl->error) {
                Debugger::log('Slack lookup error: ' . $curl->errorCode . ': ' . $curl->errorMessage . '');
                throw new SlackException('Slack lookup error: ' . $curl->errorCode . ': ' . $curl->errorMessage . '');
            }

            if (!$curl->response->ok) {
                throw new SlackException("User ($userId) not found. Slack error code: [" . $curl->response->error .
                    "]");
            }

            return new Person($curl->response->user->id, new Name($curl->response->user->real_name),
                new Email($curl->response->user->profile->email));
        }

        /**
         * @return ChannelArray
         * @throws SlackException
         */
        public function getChannels(): ChannelArray
        {
            $cacheKey = new Key(sha1($this->token) . "_channels");

            if (isset(self::$cache)) {
                try {
                    $channels = self::$cache->get($cacheKey);
                    if (!is_null($channels)) {
                        return $channels;
                    }
                } catch (\Throwable $t) {
                    // do nothing
                }
            }

            $curl = new Curl();
            $curl->setHeader("Authorization", "Bearer $this->token");
            $curl->setHeader("Content-Type", "application/json; charset=utf-8");

            $channels = new ChannelArray();
            $cursor = null;
            do {
                $args = [
                    'exclude_archived' => true,
                    "limit"            => 1000
                ];
                if (!empty($cursor)) {
                    $args["cursor"] = $cursor;
                }
                $curl->get("https://slack.com/api/conversations.list", $args);

                if ($curl->error) {
                    Debugger::log('Slack channels.list error: ' . $curl->errorCode . ': ' . $curl->errorMessage . '');
                    throw new SlackException('Slack channels.list error: ' . $curl->errorCode . ': ' .
                        $curl->errorMessage . '');
                }

                if (!$curl->response->ok) {
                    throw new SlackException("Slack conversations.list endpoint error code: [" .
                        $curl->response->error . "]");
                }

                $curlData = $curl->response->channels;

                if (isset($curl->response->response_metadata->next_cursor)) {
                    $cursor = $curl->response->response_metadata->next_cursor;
                }

                // remove blacklisted channels
                foreach ($curlData as $k => $v) {
                    if (in_array($v->name, $this->blacklist)) {
                        unset($curlData[$k]);
                    }
                }

                foreach ($curlData as $channel) {
                    $channels->add(new Channel($channel->id, $channel->name, $channel->purpose->value));
                }
            } while (!empty($cursor));

            if (isset(self::$cache)) {
                try {
                    self::$cache->add($cacheKey, $channels, '24 hours');
                } catch (\Throwable $t) {
                    // do nothing
                }
            }

            return $channels;
        }

        /**
         * @param SlackMessage $message
         * @param Channel      $channel
         * @return bool
         * @throws SlackException
         */
        public function sendMessage(SlackMessage $message, Channel $channel): bool
        {
            $_to = str_replace(["#", "@"], "", $channel->name);
            if (in_array($_to, $this->blacklist)) {
                throw new SlackException("$channel->name is on blacklist.");
            }

            if (!$this->enable) {
                throw new SlackException("Slack is disabled.");
            }

            $curl = new Curl();
            $curl->setHeader("Authorization", "Bearer $this->token");
            $curl->setHeader("Content-Type", "application/json; charset=utf-8");
            $curl->post("https://slack.com/api/chat.postMessage", $message->createMessage($channel));

            if (isset($curl->response->ok)) {
                if (isset($curl->response->error)) {
                    $this->lastError = $curl->response->error;
                }

                return $curl->response->ok;
            }

            throw new SlackException("Unknown response ($curl->errorCode): " . $curl->errorMessage . " | JSON data: " .
                json_decode($curl->response));
        }

        /**
         * @return MemberList
         * @throws SlackException
         */
        public function listMembers(): MemberList
        {
            $cacheKey = new Key(sha1($this->token) . "_members");

            if (isset(self::$cache)) {
                try {
                    $members = self::$cache->get($cacheKey);
                    if (!is_null($members)) {
                        return $members;
                    }
                } catch (\Throwable $t) {
                    // do nothing
                }
            }

            $curl = new Curl();
            $curl->setHeader("Authorization", "Bearer $this->token");
            $curl->get("https://slack.com/api/users.list");

            if ($curl->error) {
                Debugger::log('Slack channels.list error: ' . $curl->errorCode . ': ' . $curl->errorMessage . '');
                throw new SlackException('Slack users.list error: ' . $curl->errorCode . ': ' . $curl->errorMessage .
                    '');
            }

            if (!$curl->response->ok) {
                throw new SlackException("Slack conversations.list endpoint error code: [" . $curl->response->error .
                    "]");
            }

            $members = new MemberList();

            foreach ($curl->response->members as $member) {
                if (!$member->deleted and !$member->is_bot and !$member->is_app_user) {
                    $members->add(new Member($member->id, new Name($member->profile->real_name)));
                }
            }

            if (isset(self::$cache)) {
                try {
                    self::$cache->add($cacheKey, $members, '24 hours');
                } catch (\Throwable $t) {
                    // do nothing
                }
            }

            return $members;
        }
    }