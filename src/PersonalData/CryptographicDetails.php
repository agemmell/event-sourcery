<?php namespace EventSourcery\EventSourcery\PersonalData;

use EventSourcery\EventSourcery\Serialization\SerializableValue;

/**
 * CryptographicDetails is the value object that contains the
 * necessary details to encrypt and decrypt personal data for
 * a single person.
 */
class CryptographicDetails implements SerializableValue {

    /**
     * a key => value array of details
     * @var array
     */
    private $details;
    /**
     * the name of the encryption type used
     * @var string
     */
    private $encryption;

    public function __construct(string $encryption, array $details) {
        $this->encryption = $encryption;
        $this->details    = $details;
    }

    /**
     * get the type of encryption that these details were created for
     *
     * @return string
     */
    public function encryption(): string {
        return $this->encryption;
    }

    /**
     * get the value for a key from the cryptographic details
     *
     * @param $name
     * @return string
     * @throws CryptographicDetailsDoNotContainKey
     */
    public function key($name): string {
        if ( ! isset($this->details[$name])) {
            throw new CryptographicDetailsDoNotContainKey($name);
        }
        return $this->details[$name];
    }

    /**
     * serialize() returns a string form of the value for persistence
     * which will be deserialized when needed
     *
     * @return string
     */
    public function serialize(): string {
        return json_encode($this->details + ['encryption' => $this->encryption]);
    }

    /**
     * deserialize() returns a value object from a string received
     * from persistence
     *
     * @param string $string
     * @return mixed
     * @throws CannotDeserializeCryptographicDetails
     */
    public static function deserialize(string $string): CryptographicDetails {
        $data = (array) json_decode($string);

        if ( ! isset($data['encryption'])) {
            throw new CannotDeserializeCryptographicDetails('Encryption type could not be identified from serialized form.');
        }

        $encryption = $data['encryption'];
        unset($data['encryption']);

        return new static($encryption, $data);
    }
}