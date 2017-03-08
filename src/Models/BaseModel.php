<?php

namespace APILIB\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {

  protected $queryData=['perPage'=>15, 'order'=>'asc', 'sort'=>'id', 'q'=>''];

  public function setQueryData($params){

    $this->queryData=$this->getQueryData($params);
  }

  public function scopepage($query){

    return $this->getPageScope($query, $this->queryData);
  }

  public function scopetotal($query, $sums){


    $queryData=$this->queryData;
    $queryData['page']=1;
    $pageQuery=$this->getPageScope($query, $this->getQueryData($queryData));

    return $this->getTotals($pageQuery, $this->getQueryData($queryData), $sums);
  }

  public function totals($sums){

    //return $this->getTotals($this, $this->queryData, $sums);

    $queryData=$this->queryData;
    $queryData['page']=1;
    $pageQuery=$this->getPageScope($this, $this->getQueryData($queryData));

    return $this->getTotals($pageQuery, $this->getQueryData($queryData), $sums);
  }

  private function getQueryData($params, $defs=['perPage'=>15, 'order'=>'asc', 'sort'=>'id']){

    $data['page']=array_key_exists('page', $params)?$params['page']:1;
    $data['perPage']=array_key_exists('perPage', $params)?$params['perPage']:$defs['perPage'];
    $data['offset']=self::getOffset($data);
    $data['sort']=array_key_exists('sort', $params)?trim($params['sort']):$defs['sort'];
    $data['order']=array_key_exists('order', $params)?trim($params['order']):$defs['order'];
    $data['q']=array_key_exists('q', $params)?$params['q']:'';

    return $data;

  }

  private function getOffset($data){

    return ($data['perPage']*$data['page'])-$data['perPage'];
  }

  private function getQuery($params){

    $data=$this->getQueryData($params);

    return ($data['perPage']*$data['page'])-$data['perPage'];
  }

  public function getTotals($query, $queryData, $sums){


    $data=$this->getQueryData($queryData);

    $totals['rows']=$query->count();
    $totals['pages']=ceil($query->count()/$data['perPage']?:15);

    foreach ($sums as $field) {
      $totals['sums'][$field]=$query->sum($field);
    }

    return $totals;

  }

  private function getPageScope($query, $queryData){

    $query= $query->take($queryData['perPage'])
    ->offset($queryData['offset'])
    ->orderBy($queryData['sort'], $queryData['order']);

    $query=self::addQuerys($query, $queryData);

    return $query;

  }

  private function addNestedQuery($field, $o, $prefsuf, $s, $value, $query){

    $column=substr($field,0,strpos($field, '.'));
    $column_value=substr($field,strpos($field, '.')+1);

    //We now filter the main table over the nested one
    $query=$query->whereHas($column, function($q) use ($column, $column_value, $value, $o, $prefsuf, $s)
    {

      if(strpos($column_value, '.')==false){

        if($s===':isNULL'){
          $query= $query->whereNull($field);
          break;
        }

        $q->where($column_value,$o,$prefsuf.str_replace($s,'', $value).$prefsuf);
      }else{

        $this->addNestedQuery($column_value, $o, $prefsuf, $s, $value, $q);
      }

    });

    return $query;

  }


  private function addQuerys($query, $data){

    foreach (explode(';',$data['q']) as $q) {

      if(strpos($q, ':')!==false){

        $field=substr($q,0,strpos($q, ':'));
        $value=substr($q,strpos($q, ':'));

        foreach (self::getOperators() as $s => $o) {
          if(strpos($value, $s)!==false){
            $prefsuf=strpos($s, '?')?'%':'';




            if(strpos($field, '.')==false){

              if($s===':isNULL'){
                $query= $query->whereNull($field);
                break;
              }

              //If field has no dot notation we filter just the main table
              $query= $query->where($field,$o,$prefsuf.str_replace($s,'',$value).$prefsuf);

            }else{

              //If field has dot notation we
              //filter with a recursive function over the nested tables
              $query=$this->addNestedQuery($field, $o, $prefsuf, $s, $value, $query);

            }

            break;
          }
        }
      }

    }

    return $query;

  }

  private function getOperators(){

    return [
      ':='=>'=',
      ':!='=>'!=',
      ':<'=>'<',
      ':>'=>'>',
      ':<='=>'<=',
      ':>='=>'>=',
      ':<>'=>'<>',
      ':?'=>'like',
      ':!?'=>'not like',
      ':isNULL'=>'is null',
    ];
  }



}
