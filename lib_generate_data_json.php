<?php
class lib_generate_data_json{
    private $CI;
    private $_PerPage = 10;
    public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->helper(array('url'));
    }
    private function current_full_url(){
          $url = $this->CI->config->site_url($this->CI->uri->uri_string());
          return $_SERVER['QUERY_STRING'] ? $url.'?'.$_SERVER['QUERY_STRING'] : $url;
    }

    public function SetPerPage($number){
        $this->_PerPage = (gettype($number)!=="integer")?int($number):$number;
    }

    private function GetPerPage(){
        return $this->_PerPage;
    }

     public function GenerateTablesDataToTypeJson($tb)
     {
         if(empty($tb)) die('tables empty !');
         $ArrayMainDataInsert = array();
         $ArrayExplodeFullPath = array();
         $FullPathURL = $this->current_full_url();
         //echo $FullPathURL;
         $page = $this->CI->input->get('page');
         $perPage = $this->GetPerPage();
         $Start = 0;
         $NextURL = "";
         $PrvURL = "";
         if(isset($page) && $page!=""){
             $Start = ($page-1)*$perPage;
         }else{
             $Start = 0;
         }
         $ResultRows = $this->CI->db->query("SELECT COUNT(*) AS RowsNumber FROM $tb")->row();
         $numRows = $ResultRows->RowsNumber;
         $list = $this->CI->db->limit($perPage,$Start)->get($tb)->result();
         $ArrayAllRowsData = array();
         foreach($list as $row){
               $RowsItem = array();
            foreach($row as $key =>$value){
               $RowsItem[$key]=$value;
            }
            $ArrayAllRowsData[] = $RowsItem;
         }
         $ArrayExplodeFullPath = explode('?',$FullPathURL);
         $TotalPage = ceil($numRows/$perPage);
         $CurrentURL = $FullPathURL;
         $PrvURL = $ArrayExplodeFullPath[0].'?page='.($page-1);
         $NextURL = $ArrayExplodeFullPath[0].'?page='.($page+1);
         $ArrayMainDataInsert=array(
             'total_page'=>$TotalPage,
             'total_rows'=>$numRows,
             'page'=> $page,
             'current_url'=> $FullPathURL,
             'url_non_param'=> $ArrayExplodeFullPath[0],
             'prv_url'=> ($page > 1)?$PrvURL:null,
             'next_url'=> $NextURL,
             'data'=> $ArrayAllRowsData,

         );
         echo json_encode($ArrayMainDataInsert);

     }

     public function GenerateQueryDBToTypeJson($sql){
        if(empty($sql)) die('Query Empty !');
         $ArrayMainDataInsert = array();
         $ArrayExplodeFullPath = array();
         $FullPathURL = $this->current_full_url();
         $page = $this->CI->input->get('page');
         $perPage = $this->GetPerPage();
         $Start = 0;
         $NextURL = "";
         $PrvURL = "";
         if(isset($page)){
             $Start = ($page-1)*$perPage;
         }else{
             $Start = 0;
         }
        $sqtToSquery="";
        $TotalRows = count($this->CI->db->query($sql)->result());
        $sqtToSquery.= $sql." LIMIT $Start,$perPage";
        $list = $this->CI->db->query($sqtToSquery)->result();
         $ArrayAllRowsData = array();
         foreach($list as $row){
               $RowsItem = array();
            foreach($row as $key =>$value){
               $RowsItem[$key]=$value;
            }
            $ArrayAllRowsData[] = $RowsItem;
         }
         $ArrayExplodeFullPath = explode('?',$FullPathURL);
         $TotalPage = ceil($TotalRows/$perPage);
         $CurrentURL = $FullPathURL;
         $PrvURL = $ArrayExplodeFullPath[0].'?page='.($page-1);
         $NextURL = $ArrayExplodeFullPath[0].'?page='.($page+1);
         $ArrayMainDataInsert = array(
             'total_page'=>$TotalPage,
             'total_rows'=>$TotalRows,
             'page'=> $page,
             'current_url'=>$FullPathURL,
             'url_non_param'=>$ArrayExplodeFullPath,
             'prv_url'=>$PrvURL,
             'next_url'=> $NextURL,
             'data'=>$ArrayAllRowsData,
         );
         echo json_encode($ArrayMainDataInsert);
     }
}