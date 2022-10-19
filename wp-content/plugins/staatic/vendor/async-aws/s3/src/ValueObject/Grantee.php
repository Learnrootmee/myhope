<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\S3\Enum\Type;
final class Grantee
{
    private $displayName;
    private $emailAddress;
    private $id;
    private $type;
    private $uri;
    public function __construct(array $input)
    {
        $this->displayName = $input['DisplayName'] ?? null;
        $this->emailAddress = $input['EmailAddress'] ?? null;
        $this->id = $input['ID'] ?? null;
        $this->type = $input['Type'] ?? null;
        $this->uri = $input['URI'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    /**
     * @return string|null
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }
    /**
     * @return string|null
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }
    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }
    public function getType() : string
    {
        return $this->type;
    }
    /**
     * @return string|null
     */
    public function getUri()
    {
        return $this->uri;
    }
    /**
     * @return void
     */
    public function requestBody(DOMElement $node, DOMDocument $document)
    {
        if (null !== ($v = $this->displayName)) {
            $node->appendChild($document->createElement('DisplayName', $v));
        }
        if (null !== ($v = $this->emailAddress)) {
            $node->appendChild($document->createElement('EmailAddress', $v));
        }
        if (null !== ($v = $this->id)) {
            $node->appendChild($document->createElement('ID', $v));
        }
        if (null === ($v = $this->type)) {
            throw new InvalidArgument(\sprintf('Missing parameter "Type" for "%s". The value cannot be null.', __CLASS__));
        }
        if (!Type::exists($v)) {
            throw new InvalidArgument(\sprintf('Invalid parameter "xsi:type" for "%s". The value "%s" is not a valid "Type".', __CLASS__, $v));
        }
        $node->setAttribute('xsi:type', $v);
        if (null !== ($v = $this->uri)) {
            $node->appendChild($document->createElement('URI', $v));
        }
    }
}
