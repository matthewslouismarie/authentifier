<?php

namespace LM\Common\Model;

use LM\Common\Type\TypeCheckerTrait;
use Serializable;

class ArrayObject implements Serializable
{
    use TypeCheckerTrait;

    private $items;

    private $currentItemIndex;

    public function __construct(array $items, string $type)
    {
        $this->items = [];
        foreach ($items as $key => $item) {
            $this->checkType($item, $type);
            if ($this->isStringType($type)) {
                $this->items[$key] = $item;
            } elseif ($this->isIntegerType($type)) {
                $this->items[$key] = $item;
            } elseif ($this->isClassOrInterfaceName($type)) {
                $this->items[$key] = $item; 
            }
        }
        $this->currentItemIndex = 0;
    }

    /**
     * @todo Rename to append.
     */
    public function add($value, string $type): self
    {
        $this->checkType($value, $type);
        $items = $this->items;
        $items[] = $value;

        return new self($items, $type);
    }

    /**
     * @todo Rename to add.
     */
    public function addWithkey($key, $value, string $type): self
    {
        $this->checkType($value, $type);
        $items = $this->items;
        $items[$key] = $value;

        return new self($items, $type);
    }

    public function hasNextItem(): bool
    {
        return $this->currentItemIndex + 1 < count($this->items);
    }

    public function get($key, string $type)
    {
        $item = $this->items[$key];
        $this->checkType($item, $type);

        return $item;
    }

    public function getCurrentItem(string $class)
    {
        $currentItem = $this->items[$this->currentItemIndex];
        $this->checkType($currentItem, $class);

        return $currentItem;
    }

    /**
     * @todo Mutable object!
     */
    public function setToNextItem(): void
    {
        $this->currentItemIndex++;
    }

    public function getSize(): int
    {
        return count($this->items);
    }

    public function toArray(string $type): array
    {
        foreach ($this->items as $item) {
            $this->checkType($item, $type);
        }

        return $this->items;
    }

    public function serialize(): string
    {
        return serialize([
            $this->currentItemIndex,
            $this->items])
        ;
    }

    public function unserialize($serialized): void
    {
        list(
            $this->currentItemIndex,
            $this->items) = unserialize($serialized);
    }
}
