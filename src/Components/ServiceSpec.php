<?php

namespace ROrier\Container\Components;

use ArrayAccess;
use Exception;
use ROrier\Config\Components\Bag;

class ServiceSpec implements ArrayAccess
{
    private string $name;

    private Bag $data;

    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->data = new Bag($data);
    }

    public function toArray()
    {
        return $this->data->toArray();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getArguments(): array
    {
        return $this->data['arguments'] ?: [];
    }

    public function isShared()
    {
        return ($this->data['shared'] !== false);
    }

    public function isFixed()
    {
        return ($this->data['fixed'] === true);
    }

    public function isAbstract()
    {
        return ($this->data['abstract'] === true);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasTag(string $name): bool
    {
        if ($this->data['tags']) {
            foreach($this->data['tags'] as $tag) {
                if ($tag['name'] === $name) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getTagDefinitions($name): array
    {
        $validTags = array();

        if ($this->data['tags']) {
            foreach($this->data['tags'] as $tag) {
                if ($tag['name'] === $name) {
                    $validTags[] = array(
                        'service' => $this->getName(),
                        'tag' => $name,
                        'options' => $tag['options'] ?? []
                    );
                }
            }
        }

        return $validTags;
    }

    // ###################################################################
    // ###       sous-fonctions d'accès par tableau
    // ###################################################################

    /**
     * @param mixed $var
     * @param mixed $value
     * @throws Exception
     */
    public function offsetSet($var, $value)
    {
        $this->data[$var] = $value;
    }

    /**
     * @param mixed $var
     * @return bool
     */
    public function offsetExists($var)
    {
        return isset($this->data[$var]);
    }

    /**
     * @param mixed $var
     * @throws Exception
     */
    public function offsetUnset($var)
    {
        unset($this->data[$var]);
    }

    /**
     * @param mixed $var
     * @return array|bool|mixed|null
     */
    public function offsetGet($var)
    {
        return $this->data[$var];
    }
}