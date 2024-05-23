<?php
    declare(strict_types=1);

    namespace Ataccama\Slack\Env;

    use Ataccama\Common\Interfaces\IdentifiableByString;
    use Ataccama\Common\Traits\IdentifiedByString;


    /**
     * Class Channel
     * @package Ataccama\Slack\Env
     * @property-read string $purpose
     * @property-read string $name
     */
    class Channel implements IdentifiableByString
    {
        use IdentifiedByString;


        /** @var string|null */
        protected ?string $purpose;

        /** @var string|null */
        protected ?string $name;

        /**
         * Channel constructor.
         * @param string      $id
         * @param string|null $name
         * @param string|null $purpose
         */
        public function __construct(string $id, ?string $name = null, ?string $purpose = null)
        {
            $this->id = $id;
            $this->purpose = $purpose;
            $this->name = $name;
        }

        /**
         * @return string|null
         */
        public function getPurpose(): ?string
        {
            return $this->purpose;
        }

        /**
         * @return string|null
         */
        public function getName(): ?string
        {
            return $this->name;
        }
    }