<?php
namespace app\model;
use think\Model;
use niklaslu\UnLimitTree;
class AuthRule extends Model {
    
    protected $autoWriteTimestamp = true;
    
    public function getRules($type = 'pc' , $component = 0,$status = null){
        
        if ($status != null){
            $map['status'] = 1;
        }
        
        $map['type'] = $type;
        $map['component_id'] = $component;
        $data = $this->where($map)->order('sort','asc')->select();
        if ($data){
            
            $data = UnLimitTree::unlimitedForLevel($data);
        }else{
            $data = [];
        }
        
        return $data;
    }
    
    public function getRulesGroupByType(){
        
        $map['component_id'] = 0;
        $map['status'] = 1;
        
        $data = $this->where($map)->order('sort','asc')->select();
        
        $datas = [];
        foreach ($data as $vo){
            $datas[$vo['type']][] = $vo->data;
        }
        
        foreach ($datas as $k=>$vo){
            $datas[$k] = UnLimitTree::unlimitedForLevel($vo);
        }
        return $datas;
    }
    
    public function getRuleSelect($type = 'pc' , $id = 0, $component = 0){
       
        if ($id){
            $map['id'] = ['<>' , $id];
        }
        
        $map['type'] = $type;
        $map['component_id'] = $component;
        $data = $this->where($map)->order('sort','asc')->select();
        if ($data){
            
            $data = UnLimitTree::unlimitedForLevel($data);
            
        }else{
            $data = [];
        }
        
        return $data;
    }
}