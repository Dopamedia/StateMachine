<?php
/**
 * Created by PhpStorm.
 * User: pandi
 * Date: 16.07.16
 * Time: 14:46
 */

namespace Dopamedia\StateMachine\Model;

class Configuration extends \Magento\Framework\Config\Data implements ConfigurationInterface
{
    /**
     * @param \Magento\Framework\Config\ReaderInterface $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId = 'dopamedia_state_machine'
    )
    {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->get('processes');
    }

    /**
     * @inheritDoc
     */
    public function getProcess($processName)
    {
        return $this->get('processes/' . $processName);
    }

    /**
     * @inheritdoc
     */
    public function getStates($processName)
    {
        return $this->get('processes/' . $processName . '/states');
    }

    /**
     * @inheritdoc
     */
    public function getTransitions($processName)
    {
        return $this->get('processes/' . $processName . '/transitions');
    }

    /**
     * @inheritDoc
     */
    public function getEvents($processName)
    {
        return $this->get('processes/' . $processName . '/events');
    }

    /**
     * @inheritDoc
     */
    public function getEventCommand($processName, $eventName)
    {
        return $this->get('processes/' .$processName . '/events/' . $eventName . '/command');
    }

    /**
     * @inheritDoc
     */
    public function getEventManual($processName, $eventName)
    {
        return $this->get('processes/' .$processName . '/events/' . $eventName . '/manual');
    }

    /**
     * @inheritDoc
     */
    public function getEventOnEnter($processName, $eventName)
    {
        return $this->get('processes/' .$processName . '/events/' . $eventName . '/onEnter');
    }

    /**
     * @inheritDoc
     */
    public function getEventTimeout($processName, $eventName)
    {
        return $this->get('processes/' .$processName . '/events/' . $eventName . '/timeout');
    }
}