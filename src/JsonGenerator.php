<?php


namespace BeyondCode\ErdGenerator;


class JsonGenerator
{
    protected $nodeList = [];

    /**
     * add Model to here
     * @param  JsonGeneratorNode  $node
     * @author Blues
     *
     */
    public function setNode(JsonGeneratorNode $node)
    {
        $this->nodeList[$node->getName()] = $node;
    }

    /**
     * find exist Node.
     * @return JsonGeneratorNode
     * @throws \Exception
     * @author Blues
     *
     */
    public function findNode($name): JsonGeneratorNode
    {
        if (!isset($this->nodeList[$name])) {
            throw new \Exception($name.' Node not exist');
        }
        return $this->nodeList[$name];
    }

    public function link()
    {
    }

    public function export($type = 'json', $name = "graph.json")
    {
    }


}
