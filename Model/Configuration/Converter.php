<?php
/**
 * Created by PhpStorm.
 * User: pandi
 * Date: 06.07.16
 * Time: 22:46
 */

namespace Dopamedia\StateMachine\Model\Configuration;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * @inheritDoc
     */
    public function convert($document)
    {
        $result = [];
        $rootElement = $this->getRootElement($document);
        foreach ($this->getChildrenByName($rootElement, 'process') as $processNode) {
            $processName = $processNode->attributes->getNamedItem('name')->nodeValue;
            $processObjectClass = $processNode->attributes->getNamedItem('object')->nodeValue;
            $result['processes'][$processName] = [
                'objectClass' => $processObjectClass,
                'states' => $this->gatherStates($processNode),
                'transitions' => $this->gatherTransitions($processNode),
                'events' => $this->gatherEvents($processNode)
            ];
        }
        return $result;
    }

    /**
     * @param \DOMElement $parentNode
     * @return array
     */
    protected function gatherStates(\DOMElement $parentNode)
    {
        $states = [];
        foreach ($this->getChildrenByName($parentNode, 'states') as $statesNode) {
            foreach ($this->getAllChildElements($statesNode) as $stateNode) {
                $stateName = $stateNode->attributes->getNamedItem('name')->nodeValue;
                $states[$stateName] = [];
            }
        }
        return $states;
    }

    /**
     * @param \DOMElement $parentNode
     * @return array
     */
    protected function gatherTransitions(\DOMElement $parentNode)
    {
        $transitions = [];
        foreach ($this->getChildrenByName($parentNode, 'transitions') as $transitionsNode) {
            foreach ($this->getAllChildElements($transitionsNode) as $transitionNode) {
                $transition = [
                    'source' => $this->getFirstChildByName($transitionNode, 'source')->nodeValue,
                    'target' => $this->getFirstChildByName($transitionNode, 'target')->nodeValue,
                    'event' => $this->getFirstChildByName($transitionNode, 'event')->nodeValue
                ];

                if ($condition = $this->getAttributeString($transitionNode, 'condition')) {
                    $transition['condition'] = $condition;
                }

                if ($happyCase = $this->getAttributeBoolean($transitionNode, 'happy')) {
                    $transition['happy'] = $happyCase;
                }

                $transitions[] = $transition;
            }
        }
        return $transitions;
    }

    /**
     * @param \DOMElement $parentNode
     * @return array
     */
    protected function gatherEvents(\DOMElement $parentNode)
    {
        $events = [];
        foreach ($this->getChildrenByName($parentNode, 'events') as $eventsNode) {
            foreach ($this->getAllChildElements($eventsNode) as $eventNode) {
                $eventName = $eventNode->attributes->getNamedItem('name')->nodeValue;
                $event = [];

                if (!is_null($command = $this->getAttributeString($eventNode, 'command'))) {
                    $event['command'] = $command;
                }

                if (!is_null($manual = $this->getAttributeString($eventNode, 'manual'))) {
                    $event['manual'] = $manual;
                }

                if (!is_null($onEnter = $this->getAttributeString($eventNode, 'onEnter'))) {
                    $event['onEnter'] = $onEnter;
                }

                if (!is_null($timeOut = $this->getAttributeString($eventNode, 'timeOut'))) {
                    $event['timeOut'] = $timeOut;
                }

                $events[$eventName] = $event;
            }
        }
        return $events;
    }

    /**
     * @param \DOMElement $parent
     * @param string $name
     * @return \DOMElement
     */
    public function getFirstChildByName(\DOMElement $parent, $name)
    {
        $children = $this->getChildrenByName($parent, $name);
        return reset($children);
    }

    /**
     * @param \DOMElement $parent
     * @param string $name
     * @return \DomElement[]
     */
    public function getChildrenByName(\DOMElement $parent, $name)
    {
        return array_filter($this->getAllChildElements($parent), function(\DOMElement $child) use ($name) {
            return $child->nodeName === $name;
        });
    }

    /**
     * @param \DOMDocument $document
     * @return \DOMElement
     */
    private function getRootElement(\DOMDocument $document)
    {
        return $this->getAllChildElements($document)[0];
    }

    /**
     * @param \DOMNode $parent
     * @return \DOMElement[]
     */
    private function getAllChildElements(\DOMNode $parent)
    {
        return array_filter(iterator_to_array($parent->childNodes), function(\DOMNode $child) {
            return $child->nodeType === \XML_ELEMENT_NODE;
        });
    }

    /**
     * @param \DOMElement $node
     * @param string $attributeName
     * @return string|null
     */
    protected function getAttributeString(\DOMElement $node, $attributeName)
    {
        return ($node->getAttribute($attributeName) !== '') ? $node->getAttribute($attributeName) : null;
    }

    /**
     * @param \DOMElement $node
     * @param $attributeName
     * @param null $default
     * @return bool|null
     */
    protected function getAttributeBoolean(\DOMElement $node, $attributeName, $default = null)
    {
        if ($node->getAttribute($attributeName) !== '') {
            return ($node->getAttribute($attributeName) === 'true') ? true : false;
        }
        return $default;
    }
}