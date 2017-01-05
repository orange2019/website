<?php
namespace app\model;
use think\Model;
class Models extends Model {
    
    protected $autoWriteTimestamp = true;
    
    public function getListDisplayFields($id){
        
        $data = $this->get($id);
        $fields = $data->fields;
        $displayFields = $data->list_display;
        
        $fields = parse_config($fields);
        $displays = explode(',', $displayFields);
        
        $result = [];
        foreach ($fields as $v){
            if (in_array($v[0], $displays)){
                $result[$v[0]] = $v;
            }
        }
        
        return $result;
        
       
    }
    
    public function getFieldsArr($id){
        
        $data = $this->get($id);
        $fields = $data->fields;
        $fields = parse_config($fields);
        return $fields;
        
    }
}