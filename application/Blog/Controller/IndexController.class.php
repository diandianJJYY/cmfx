<?php

namespace Blog\Controller;
use Common\Controller\HomebaseController; 

class IndexController extends HomebaseController {


    // 前台文章列表
    public function index() {
        $term_id=I('get.id',1,'intval');

        if($term_id){
            $term=sp_get_term($term_id);
            $term[$term['term_id']]=$term;
        }else{
            $terms=sp_get_terms();
            $term = [];
            $term_ids = [];
            foreach($terms as $value){
                $term_ids[]=$value['term_id'];
                $term[$value['term_id']] = $value;
            }
            $term_id = implode(',',$term_ids);
        }



        if(empty($term)){
            header('HTTP/1.1 404 Not Found');
            header('Status:404 Not Found');
            if(sp_template_file_exists(MODULE_NAME."/404")){
                $this->display(":404");
            }
            return;
        }

        $tplname= isset($term["list_tpl"])?$term["list_tpl"]:'index';
        $tplname=sp_get_apphome_tpl($tplname, "index");
        $this->assign('term',$term);
        $this->assign('cat_id', $term_id);
        $this->display(":$tplname");
    }

    // 文章分类列表接口,返回文章分类列表,用于后台导航编辑添加
    public function nav_index(){
        $navcatname="文章分类";
        $term_obj= M("Terms");

        $where=array();
        $where['status'] = array('eq',1);
        $terms=$term_obj->field('term_id,name,parent')->where($where)->order('term_id')->select();
        $datas=$terms;
        $navrule = array(
            "id"=>'term_id',
            "action" => "Portal/List/index",
            "param" => array(
                "id" => "term_id"
            ),
            "label" => "name",
            "parentid"=>'parent'
        );
        return sp_get_nav4admin($navcatname,$datas,$navrule) ;
    }

}


