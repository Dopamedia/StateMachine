<?php
/**
 * Created by PhpStorm.
 * User: pandi
 * Date: 25.07.16
 * Time: 11:10
 */

namespace Dopamedia\StateMachine\Model\Graph\Adapter;

use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use Dopamedia\StateMachine\Helper\Generator\StringGenerator;

class PhpDocumentorGraphAdapter implements GraphAdapterInterface
{
    /**
     * @var Graph
     */
    private $graph;

    /**
     * @var StringGenerator
     */
    protected $stringGenerator;

    /**
     * @param StringGenerator $stringGenerator
     */
    public function __construct(
        \Dopamedia\StateMachine\Helper\Generator\StringGenerator $stringGenerator
    )
    {
        $this->stringGenerator = $stringGenerator;
    }


    /**
     * @return \phpDocumentor\GraphViz\Graph
     */
    private function createPhpDocumentorGraph()
    {
        return new Graph();
    }

    /**
     * @inheritDoc
     */
    public function create($name, array $attributes = [], $directed = true, $strict = true)
    {
        $this->graph = $this->createPhpDocumentorGraph();
        $this->graph->setName($name);
        $type = $this->getType($directed);
        $this->graph->setType($type);
        $this->graph->setStrict($strict);
        $this->addAttributesTo($attributes, $this->graph);
        return $this;
    }

    /**
     * @param bool $directed
     *
     * @return string
     */
    private function getType($directed)
    {
        return $directed ? self::DIRECTED_GRAPH : self::GRAPH;
    }

    /**
     * @param string $name
     * @param array $attributes
     * @param string $group
     *
     * @return $this
     */
    public function addNode($name, $attributes = [], $group = self::DEFAULT_GROUP)
    {
        $node = new Node($name);
        $this->addAttributesTo($attributes, $node);

        if ($group !== self::DEFAULT_GROUP) {
            $graph = $this->getGraphByName($group);
            $graph->setNode($node);
        } else {
            $this->graph->setNode($node);
        }

        return $this;
    }

    /**
     * @param string $fromNode
     * @param string $toNode
     * @param array $attributes
     *
     * @return $this
     */
    public function addEdge($fromNode, $toNode, $attributes = [])
    {
        $edge = new Edge($this->graph->findNode($fromNode), $this->graph->findNode($toNode));
        $this->addAttributesTo($attributes, $edge);

        $this->graph->link($edge);

        return $this;
    }

    /**
     * @param string $name
     * @param array $attributes
     *
     * @return $this
     */
    public function addCluster($name, $attributes = [])
    {
        $graph = $this->getGraphByName($name);

        $this->addAttributesTo($attributes, $graph);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return \phpDocumentor\GraphViz\Graph
     */
    private function getGraphByName($name)
    {
        $name = 'cluster_' . $name;

        if (!$this->graph->hasGraph($name)) {
            $graph = $this->graph->create($name);
            $this->graph->addGraph($graph);
        }

        return $this->graph->getGraph($name);
    }

    /**
     * @param string $type
     * @param string|null $fileName
     *
     * @throws \phpDocumentor\GraphViz\Exception
     *
     * @return string
     */
    public function render($type, $fileName = null)
    {
        if ($fileName === null) {
            $fileName = sys_get_temp_dir() . '/' . $this->stringGenerator->generateRandomString(16);
        }
        $this->graph->export($type, $fileName);

        return file_get_contents($fileName);
    }

    /**
     * @param array $attributes
     * @param \phpDocumentor\GraphViz\Edge|\phpDocumentor\GraphViz\Node|\phpDocumentor\GraphViz\Graph $element
     *
     * @return void
     */
    private function addAttributesTo($attributes, $element)
    {
        foreach ($attributes as $attribute => $value) {
            $setter = 'set' . ucfirst($attribute);
            if (strip_tags($value) !== $value) {
                $value = '<' . $value . '>';
            }
            $element->$setter($value);
        }
    }
}