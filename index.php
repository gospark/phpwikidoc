<?php
/**
 *
 * Class WIKI
 * @author Xiaowu@sql.hk
 */
define( '__CPATH__' ,dirname(__FILE__) );
define( '__STATIC__' ,'/');
define( '__PROJECT_PATH__' , __CPATH__.'/project');
class WIKI
{
    public static $title           = '接口文档';
    public static $typeMaps = [
                'string'  => '字符串',
                'int'     => '整型',
                'float'   => '浮点型',
                'boolean' => '布尔型',
                'date'    => '日期',
                'array'   => '数组',
                'fixed'   => '固定值',
                'enum'    => '枚举类型',
                'object'  => '对象',
            ];
    /**
     * 显示接口列表
     * @param $arr
     * @param $project
     * @return string
     */

    public static function ShowList( $arr , $project )
    {
        $s = "";
        if( $arr )
        {
            foreach( $arr as $k=>$v )
            {
                $s .= "<li>";
                $s .= "<p>".$k." ".$v['name']."</p>";
                if( $v['data'] )
                {
                    $s .= "<ul>";
                    foreach( $v['data'] as $kk=>$vv )
                    {
                        $s .= "<li><a href=\"./index.php?project=".$project."&category=".$k."&node=".$kk."\">".$kk." ".$vv['name']."</a></li>";
                    }
                    $s .= "</li>";
                }
                $s .= "</li>";
            }
            return $s ;
        }
    }

    /**
     * 显示项目列表
     * @param $project_list
     * @return string
     */

    public static function ShowProject( $project_list )
    {
        $e = '<ul>';
        if( $project_list )
        {
            foreach( $project_list as $v )
            {
                $e .= '<li><a href="index.php?project='.$v.'">'.$v.'</a></li>';
            }
        }
        $e .= '</ul>';
        return  $e ;
    }


    public static function Showparam( $param )
    {
        $s = '';
        if( $param )
        {
            $s ='<h3>接口参数</h3>
                <table class="ui red celled striped table">
                    <thead>
                        <tr>
                           <th>参数名字</th>
                           <th>类型</th>
                           <th>是否必须</th>
                           <th>默认值</th>
                           <th>其他</th>
                           <th>说明</th>
                        </tr>
                    </thead>
                    <tbody>';


            foreach ( $param as $k => $v)
            {

                $s .= '<tr>
                            <td>'.$v['field'].'</td>
                       <td>'.$v['type'].'</td>
                       <td>'.($v['required'] ? '<font color="red">必须</font>' : '否').'</td>
                       <td>'.$v['default'].'</td>
                       <td>'.$v['other'].'</td>
                       <td>'.$v['remark'].'</td>
                   </tr>';
            }

            $s .= '</tbody></table>';
        }
        return $s ;
    }

    /**
     * 显示返回的字段
     * @param $callback
     * @return string
     */

    public static function ShowCallbackField( $callback )
    {
        $s = '';
        if( $callback )
        {
            $s = '<h3>返回字段</h3>
              <table class="ui green celled striped table" >
                <thead>
                    <tr>
                        <th>返回字段</th>
                        <th>类型</th>
                        <th>说明</th>
                    </tr>
                </thead>
                <tbody>
             ';
            foreach( $callback as $k=>$v )
            {

                $s .= '<tr>
                    <td>'.$v['field'].'</td>
                    <td>'.$v['type'].'</td>
                    <td>'.$v['remark'].'</td>
                    </tr>';

            }
            $s .= '</tbody></table>';
        }
        return $s;


    }

    /**
     * 显示返回
     * @param $path
     * @param $category
     * @param $node
     * @param $datatype
     * @return string
     */
    public static function ShowCallback( $path , $category , $node , $datatype  )
    {
        if( file_exists( $path.'/'.$category.'/'.$node.'.txt')  )
        {
            $s = '<h3>返回结果('.$datatype.')</h3><pre style="border-radius: 4px;"><code class="'.$datatype.'" >';
            $s .="\n";
            $s .= file_get_contents( $path.'/'.$category.'/'.$node.'.txt' )."\n";
            $s .= '</code></pre><script src="'.__STATIC__.'static/highlight.js"></script><script>hljs.initHighlightingOnLoad();</script>';
            return $s ;
        }
    }

    /**
     * @param $node_data
     * @param $path
     * @param $category
     * @param $node
     * @return string
     */
    public static function ShowBase( $node_data ,$path  ,$category , $node  )
    {
        self::$title = $node_data['name'];
        $s = '
    <h2 class="ui header">
        接口：<a href="javascript:;">'.$node_data['name'].'</a>
    </h2>
    <br/>
    <span class="ui teal tag label">
        '.$node_data['summary'].'
    </span>
    <div class="ui raised segment">
        <span class="ui red ribbon label">接口说明</span>
        <div class="ui message">
            <p> 撰写者:'.$node_data['author'].'</p>
            <p>
                最后更新时间:'.date('Y-m-d H:i:s', (filectime($path . '/' . $category . '/' . $node . '.json') + (60 * 60 * 8))).'</p>
        </div>
        <table class="ui green celled striped table">
            <thead>
            <tr>
                <th>名称</th>
                <th>值</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>接口地址</td>
                    <td>'.$node_data['request']['url'].'</td>
                </tr>
                <tr>
                    <td>提交方式</td>
                    <td>'.$node_data['request']['method'].'</td>
                </tr>
                <tr>
                    <td>返回格式</td>
                    <td>'.$node_data['request']['datatype'].'</td>
                </tr>
            </tbody>
        </table>
    </div>';

        $s .= self::Showparam( $node_data['param'] );
        $s .= self::ShowCallbackField( $node_data['callback'] );
        $s = str_replace(["\n","\r"],'' , $s );
        $s .= self::ShowCallback( $path , $category , $node , $node_data['request']['datatype'] );
        return $s ;

    }



    /**
     * 获取栏目
     * @param $path
     * @return array
     */

    public static function GetCategoryList( $path )
    {
        $arr = [];
        $handle  = opendir($path);
        while( $file = readdir( $handle )){

            if( preg_match('/\.json$/i',$file) )
            {
                $newpath = $path.'/'.$file;
                $newname = rtrim($file,'.json');
                $arr[$newname] = json_decode( trim( file_get_contents( $newpath ) ) ,true );
            }
        }
        closedir($handle);
        if( $arr )
        {
            ksort( $arr );
        }
        return $arr ;
    }

    /**
     * 获取接口数据
     * @param $path
     * @param $dirname
     * @return array
     */
    public static function GetInterFace( $path ,$dirname )
    {
        $newdata = [];
        $handle  = opendir($path.'/'.$dirname);
        while( $file = readdir( $handle ))
        {
            if( preg_match('/\.json$/i',$file ) )
            {
                $newpath            = $path.'/'.$dirname.'/'.$file;
                $data               = json_decode( trim(file_get_contents( $newpath )) , true );
                $newFile            = rtrim( $file , '.json');
                $newdata[$newFile]  = $data ;
            }
        }
        closedir($handle);
        if( $newdata )
        {
            ksort( $newdata );
        }
        return $newdata ;
    }

    public static function GetProjecdtList( $project_path )
    {
        $project_list = [];
        $handle       = opendir( $project_path );
        while( $file = readdir( $handle )){

            if( !preg_match('/^\./i',$file) )
            {
                if( is_dir( $project_path.'/'.$file  ) )
                {
                    $project_list[] =  $file ;
                }
            }
        }
        closedir($handle);
        return $project_list ;
    }

    public static function  Run()
    {

        $project      =  isset( $_GET['project'] )  ? $_GET['project']  : '';
        $category     =  isset( $_GET['category'] ) ? $_GET['category']  : '';
        $node         =  isset( $_GET['node'] ) ? $_GET['node']  : '';

        $project_list  = self::GetProjecdtList(  __PROJECT_PATH__ );
        if( $project_list )
        {
            if( $project )
            {
                if( in_array( $project , $project_list ) )
                {
                    $path       = __PROJECT_PATH__.'/'.$project ;
                    $arr        =  self::GetCategoryList( $path );
                    if( $arr )
                    {
                        foreach( $arr as $k=>$v )
                        {
                            $newdata = self::GetInterFace( $path , $v['dirname'] );
                            if( $newdata )
                            {
                                $arr[$k]['data'] =  $newdata ;
                            }
                        }
                    }

                    $html =   '<!DOCTYPE html><HTML lang="zh_CN"><HEAD><META charset="utf-8"><link rel="stylesheet" href="'.__STATIC__.'static/public.css"><link rel="stylesheet" href="'.__STATIC__.'static/xcode.css"><title><!#{{title}}#></title></HEAD><BODY><div id="manul"><div id="manul_bg"></div><div id="manul_box"><ul id="t">'.self::ShowList( $arr , $project ).' </ul></div> </div>';

                    if( $category && $node )
                    {
                        if( array_key_exists( $category  ,$arr ) )
                        {
                            if ( array_key_exists( $node, $arr[$category]['data'] ) )
                            {
                                $node_data = $arr[$category]['data'][$node];
                                $html .= '<div class="ui text container" style="max-width: none !important; margin-top: 30px; margin-bottom:30px;padding-left: 160px;"><div class="ui floating message">'.self::ShowBase( $node_data , $path , $category ,$node  ).'</div></div>';
                            }
                        }
                    }
                    $html  .= '</BODY></HTML>';
                    echo str_replace( '<!#{{title}}#>',self::$title , $html );
                    exit;
                }
            }
        }
        echo self::ShowProject( $project_list );
    }

}

WIKI::Run();



