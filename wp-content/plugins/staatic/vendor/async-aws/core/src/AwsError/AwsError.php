<?php

namespace Staatic\Vendor\AsyncAws\Core\AwsError;

final class AwsError
{
    private $code;
    private $message;
    private $type;
    private $detail;
    /**
     * @param string|null $code
     * @param string|null $message
     * @param string|null $type
     * @param string|null $detail
     */
    public function __construct($code, $message, $type, $detail)
    {
        $this->code = $code;
        $this->message = $message;
        $this->type = $type;
        $this->detail = $detail;
    }
    /**
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }
    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @return string|null
     */
    public function getDetail()
    {
        return $this->detail;
    }
}
