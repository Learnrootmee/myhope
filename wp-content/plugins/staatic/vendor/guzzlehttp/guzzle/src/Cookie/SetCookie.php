<?php

namespace Staatic\Vendor\GuzzleHttp\Cookie;

use InvalidArgumentException;
class SetCookie
{
    private static $defaults = ['Name' => null, 'Value' => null, 'Domain' => null, 'Path' => '/', 'Max-Age' => null, 'Expires' => null, 'Secure' => \false, 'Discard' => \false, 'HttpOnly' => \false];
    private $data;
    /**
     * @param string $cookie
     */
    public static function fromString($cookie) : self
    {
        $data = self::$defaults;
        $pieces = \array_filter(\array_map('trim', \explode(';', $cookie)));
        if (!isset($pieces[0]) || \strpos($pieces[0], '=') === \false) {
            return new self($data);
        }
        foreach ($pieces as $part) {
            $cookieParts = \explode('=', $part, 2);
            $key = \trim($cookieParts[0]);
            $value = isset($cookieParts[1]) ? \trim($cookieParts[1], " \n\r\t\x00\v") : \true;
            if (!isset($data['Name'])) {
                $data['Name'] = $key;
                $data['Value'] = $value;
            } else {
                foreach (\array_keys(self::$defaults) as $search) {
                    if (!\strcasecmp($search, $key)) {
                        $data[$search] = $value;
                        continue 2;
                    }
                }
                $data[$key] = $value;
            }
        }
        return new self($data);
    }
    public function __construct(array $data = [])
    {
        $replaced = \array_replace(self::$defaults, $data);
        if ($replaced === null) {
            throw new InvalidArgumentException('Unable to replace the default values for the Cookie.');
        }
        $this->data = $replaced;
        if (!$this->getExpires() && $this->getMaxAge()) {
            $this->setExpires(\time() + $this->getMaxAge());
        } elseif (null !== ($expires = $this->getExpires()) && !\is_numeric($expires)) {
            $this->setExpires($expires);
        }
    }
    public function __toString()
    {
        $str = $this->data['Name'] . '=' . ($this->data['Value'] ?? '') . '; ';
        foreach ($this->data as $k => $v) {
            if ($k !== 'Name' && $k !== 'Value' && $v !== null && $v !== \false) {
                if ($k === 'Expires') {
                    $str .= 'Expires=' . \gmdate('D, d M Y H:i:s \\G\\M\\T', $v) . '; ';
                } else {
                    $str .= ($v === \true ? $k : "{$k}={$v}") . '; ';
                }
            }
        }
        return \rtrim($str, '; ');
    }
    public function toArray() : array
    {
        return $this->data;
    }
    public function getName()
    {
        return $this->data['Name'];
    }
    /**
     * @return void
     */
    public function setName($name)
    {
        if (!\is_string($name)) {
            trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a string to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }
        $this->data['Name'] = (string) $name;
    }
    public function getValue()
    {
        return $this->data['Value'];
    }
    /**
     * @return void
     */
    public function setValue($value)
    {
        if (!\is_string($value)) {
            trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a string to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }
        $this->data['Value'] = (string) $value;
    }
    public function getDomain()
    {
        return $this->data['Domain'];
    }
    /**
     * @return void
     */
    public function setDomain($domain)
    {
        if (!\is_string($domain) && null !== $domain) {
            trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a string or null to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }
        $this->data['Domain'] = null === $domain ? null : (string) $domain;
    }
    public function getPath()
    {
        return $this->data['Path'];
    }
    /**
     * @return void
     */
    public function setPath($path)
    {
        if (!\is_string($path)) {
            trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a string to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }
        $this->data['Path'] = (string) $path;
    }
    public function getMaxAge()
    {
        return null === $this->data['Max-Age'] ? null : (int) $this->data['Max-Age'];
    }
    /**
     * @return void
     */
    public function setMaxAge($maxAge)
    {
        if (!\is_int($maxAge) && null !== $maxAge) {
            trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing an int or null to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }
        $this->data['Max-Age'] = $maxAge === null ? null : (int) $maxAge;
    }
    public function getExpires()
    {
        return $this->data['Expires'];
    }
    /**
     * @return void
     */
    public function setExpires($timestamp)
    {
        if (!\is_int($timestamp) && !\is_string($timestamp) && null !== $timestamp) {
            trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing an int, string or null to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }
        $this->data['Expires'] = null === $timestamp ? null : (\is_numeric($timestamp) ? (int) $timestamp : \strtotime((string) $timestamp));
    }
    public function getSecure()
    {
        return $this->data['Secure'];
    }
    /**
     * @return void
     */
    public function setSecure($secure)
    {
        if (!\is_bool($secure)) {
            trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a bool to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }
        $this->data['Secure'] = (bool) $secure;
    }
    public function getDiscard()
    {
        return $this->data['Discard'];
    }
    /**
     * @return void
     */
    public function setDiscard($discard)
    {
        if (!\is_bool($discard)) {
            trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a bool to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }
        $this->data['Discard'] = (bool) $discard;
    }
    public function getHttpOnly()
    {
        return $this->data['HttpOnly'];
    }
    /**
     * @return void
     */
    public function setHttpOnly($httpOnly)
    {
        if (!\is_bool($httpOnly)) {
            trigger_deprecation('guzzlehttp/guzzle', '7.4', 'Not passing a bool to %s::%s() is deprecated and will cause an error in 8.0.', __CLASS__, __FUNCTION__);
        }
        $this->data['HttpOnly'] = (bool) $httpOnly;
    }
    /**
     * @param string $requestPath
     */
    public function matchesPath($requestPath) : bool
    {
        $cookiePath = $this->getPath();
        if ($cookiePath === '/' || $cookiePath == $requestPath) {
            return \true;
        }
        if (0 !== \strpos($requestPath, $cookiePath)) {
            return \false;
        }
        if (\substr($cookiePath, -1, 1) === '/') {
            return \true;
        }
        return \substr($requestPath, \strlen($cookiePath), 1) === '/';
    }
    /**
     * @param string $domain
     */
    public function matchesDomain($domain) : bool
    {
        $cookieDomain = $this->getDomain();
        if (null === $cookieDomain) {
            return \true;
        }
        $cookieDomain = \ltrim(\strtolower($cookieDomain), '.');
        $domain = \strtolower($domain);
        if ('' === $cookieDomain || $domain === $cookieDomain) {
            return \true;
        }
        if (\filter_var($domain, \FILTER_VALIDATE_IP)) {
            return \false;
        }
        return (bool) \preg_match('/\\.' . \preg_quote($cookieDomain, '/') . '$/', $domain);
    }
    public function isExpired() : bool
    {
        return $this->getExpires() !== null && \time() > $this->getExpires();
    }
    public function validate()
    {
        $name = $this->getName();
        if ($name === '') {
            return 'The cookie name must not be empty';
        }
        if (\preg_match('/[\\x00-\\x20\\x22\\x28-\\x29\\x2c\\x2f\\x3a-\\x40\\x5c\\x7b\\x7d\\x7f]/', $name)) {
            return 'Cookie name must not contain invalid characters: ASCII ' . 'Control characters (0-31;127), space, tab and the ' . 'following characters: ()<>@,;:\\"/?={}';
        }
        $value = $this->getValue();
        if ($value === null) {
            return 'The cookie value must not be empty';
        }
        $domain = $this->getDomain();
        if ($domain === null || $domain === '') {
            return 'The cookie domain must not be empty';
        }
        return \true;
    }
}
