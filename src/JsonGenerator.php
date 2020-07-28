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
     * @param  String  $name
     * @return JsonGeneratorNode
     * @throws \Exception
     * @author Blues
     */
    public function findNode(string $name): JsonGeneratorNode
    {
        if (!isset($this->nodeList[$name])) {
            throw new \Exception($name.' Node not exist');
        }
        return $this->nodeList[$name];
    }

    /**
     * @param  JsonGeneratorNode  $node
     * @param  JsonGeneratorNode  $relatedModelNode
     * @param  ModelRelation  $relation
     * @author Blues
     *
     */
    public function link(JsonGeneratorNode $node, JsonGeneratorNode $relatedModelNode, ModelRelation $relation): void
    {
        $node->setRelation($relatedModelNode, $relation);
        //todo 把各个模型关联上
    }

    /**
     * @param  string  $type
     * @param  string  $name
     * @return array
     * @author Blues
     *
     */
    public function export($type = 'json', $name = "graph.json")
    {
        $json = $this->getJson();
        return file_put_contents($name, json_encode($json,JSON_UNESCAPED_UNICODE));
    }

    public function getJson()
    {
        return [
            'version' => 1.0,
            'type' => 'laravel-er',
            'data' => $this->parse2Json($this->nodeList)
        ];
    }


    /**
     * @param $nodes
     * @return array
     * @author Blues
     *
     */
    protected function parse2Json($nodes)
    {
        $return = [];
        /**
         * @var $node JsonGeneratorNode
         */
        foreach ($nodes as $node) {
            $nodeJson                 = [
                'nodeName' => $node->getName(),
                'schema' => [
                    [
                        'name' => 'id',
                        'comment' => 'test',
                        'type' => 'varchar(255)'
                    ],
                ],
                'annotation' => [
                    'author' => 'Blues',
                    'comment' => '测试',
                ],
                'relations' => $node->getRelations()
            ];
            $return[$node->getName()] = $nodeJson;
        }
        return $return;
    }


}
