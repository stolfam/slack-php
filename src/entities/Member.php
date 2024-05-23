<?php
    declare(strict_types=1);

    namespace Ataccama\Slack\Env;

    use Ataccama\Common\Env\Name;
    use Ataccama\Common\Traits\IdentifiedByString;


    /**
     * Class Member
     * @package Ataccama\Slack\Env
     * @property-read Name $name
     */
    class Member
    {
        use IdentifiedByString;


        /** @var Name */
        protected Name $name;

        /**
         * Member constructor.
         * @param string $id
         * @param Name   $name
         */
        public function __construct(string $id, Name $name)
        {
            $this->id = $id;
            $this->name = $name;
        }

        /**
         * @return Name
         */
        public function getName(): Name
        {
            return $this->name;
        }
    }