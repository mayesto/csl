<?php

namespace Mayesto\CSL;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class CheckResult implements \JsonSerializable
{
    /**
     * @var \Mayesto\CSL\OutputMessage
     */
    private $messages = [];

    /**
     * @return \Generator|\Mayesto\CSL\OutputMessage
     */
    public function getMessage(): \Generator
    {
        foreach ($this->messages as $message) {
            yield $message;
        }
    }

    /**
     * @param \Mayesto\CSL\OutputMessage $message
     *
     * @return \Mayesto\CSL\CheckResult
     */
    public function addMessage(OutputMessage $message): CheckResult
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @since 5.4.0
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "messages" => $this->messages
        ];
    }
}
