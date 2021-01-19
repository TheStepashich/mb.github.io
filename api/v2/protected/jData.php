<?php
class jData {
    private int|null $lastMod = null;
    private array|null $data = null;
    private string $selectBy;
    private int $AI = 0;
    public function __construct(
        private String $file,
        private array $structute = [
            "name","value"
        ]
    ){
        if(!file_exists($file))
            file_put_contents($file,"{\"structure\":${structute},\"AI\":0,\"data\":[]");
        $file_content = (object) json_decode(file_get_contents($file));
        $this->data = (array) $file_content->data;
        $this->AI = (int) $file_content->AI;
        if($structute != $file_content->structure)
            return false;
        return true;
    }
    public function select(int|string|null $selectBy = null, bool|int|string|null $select = null): object|array|bool {
        if(is_null($selectBy)) return $this->data;
        if(is_int($selectBy)){
            $this->lastMod = $selectBy;
            if(isset($this->data[$selectBy])) return (object) $this->data[$selectBy];
        }
        if(is_bool(array_search($selectBy,$this->structute))||is_null($select)) return false;
        $this->selectBy = $selectBy;
        $map = array_map(function($item){
            $select = $this->selectBy;
            return $item->$select;
        },$this->data);
        $keys = array_keys($map,$select);
        if(sizeof($keys)==1){
            $this->lastMod = $keys[0];
            return $this->data[$keys[0]];
        }
        $result = [];
        foreach($keys as $key){
            array_push($result,$this->data[$key]);
        }
        return $result;
    }
    public function insert(array $arguments): int|bool {
        if(sizeof($arguments)!=sizeof($this->structute)) return false;
        $result = (object) array_combine($this->structute,$arguments);
        $result->id = (int) $this->AI++;
        array_push($this->data,$result);
        self::saveFile();
        return $this->AI;
    }
    public function update(int|array $id, array|null $arguments = null): int|bool {
        if(is_null($this->lastMod)) return false;
        if(!is_int($id)) {
            if (!is_null($this->lastMod)) $id = $this->lastMod;
        } else $arguments = $id;
        if(sizeof($arguments)!=sizeof($this->structute)) return false;
        $map = array_map(function($item){return(int)$item["id"];},$this->data);
        $index = array_search($id,$map);
        if(is_bool($index)) return false;
        $this->data[$index] = (object) array_merge($this->data[$index],array_combine($this->structute,$arguments));
        return $index;
    }
    public function delete(int|null $id = null): bool {
        if(is_null($id)&&is_null($this->lastMod)) return false;
        if(is_null($id)) $id = $this->lastMod;
        $map = array_map(function($item){return(int)$item->id;},$this->data);
        $index = array_search($id,$map);
        if(is_bool($index)) return false;
        unset($this->data[$index]);
        self::saveFile();
        return true;
    }
    private function saveFile(): void {
        file_put_contents($this->file,json_encode(["structure"=>$this->structute,"AI"=>$this->AI,"data"=>$this->data]));
    }
}