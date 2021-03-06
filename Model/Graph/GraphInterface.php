<?php
/**
 * Created by PhpStorm.
 * User: pandi
 * Date: 25.07.16
 * Time: 10:43
 */

namespace Dopamedia\StateMachine\Model\Graph;

interface GraphInterface
{
    const DEFAULT_GROUP = 'default';

    /**
     * @param string $name
     * @param array $attributes
     * @param string $group
     *
     * @return $this
     */
    public function addNode($name, $attributes = [], $group = self::DEFAULT_GROUP);

    /**
     * @param string $fromNode
     * @param string $toNode
     * @param array $attributes
     *
     * @return $this
     */
    public function addEdge($fromNode, $toNode, $attributes = []);

    /**
     * @param string $name
     * @param array $attributes
     *
     * @return $this
     */
    public function addCluster($name, $attributes = []);

    /**
     * @param string $type
     * @param string|null $fileName
     *
     * @return string
     */
    public function render($type, $fileName = null);
}