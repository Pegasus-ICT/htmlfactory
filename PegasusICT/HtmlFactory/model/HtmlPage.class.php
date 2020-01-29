<?php

namespace PegasusICT\HtmlFactory;

use PegasusICT\HtmlFactory\model\HtmlEntity;

/**
 * Class HtmlPage
 *
 * @package PegasusICT\HtmlFactory
 */
class HtmlPage extends HtmlEntity implements HtmlEntityInterface {
    /**
     * HtmlPage constructor.
     *
     * @param string $entity
     * @param array  $attributes
     * @param array  $Children
     *
     * @throws \Exception
     */
    public function __construct(string $entity, array $attributes = [], array $Children = [] ) {
        parent::__construct($entity, $attributes, $Children);
    }

    public function setContent(...$content) {
        // TODO: Implement setContent() method.
    }

    public function close(): void {
        // TODO: Implement close() method.
    }
}