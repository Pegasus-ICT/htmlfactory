<?php declare(strict_types=1);

namespace PegasusICT\HtmlFactory;

use PegasusICT\HtmlFactory\model\HtmlEntity;

/**
 * Interface HtmlEntityInterface
 *
 * @package PegasusICT\HtmlFactory
 */
interface HtmlEntityInterface {
    /**
     * @param string $field
     * @param mixed  ...$value
     */
    public function setAttribute(string $attributeName, ...$value):HtmlEntity;

    public function setContent(...$content);

    /**
     * @return string
     */
    public function render():string;

    /**
     * @param \PegasusICT\HtmlFactory\model\HtmlEntity $child
     */
    public function addChild(HtmlEntity $child):HtmlEntity;

    public function close():void;
}