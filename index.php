<?php
$project      =  isset( $_GET['project'] )  ? $_GET['project']  : '';
$category     =  isset( $_GET['category'] ) ? $_GET['category']  : '';
$node         =  isset( $_GET['node'] ) ? $_GET['node']  : '';

$typeMaps = array(
    'string'  => '字符串',
    'int'     => '整型',
    'float'   => '浮点型',
    'boolean' => '布尔型',
    'date'    => '日期',
    'array'   => '数组',
    'fixed'   => '固定值',
    'enum'    => '枚举类型',
    'object'  => '对象',
);


$cpath         =  dirname( __FILE__ );
$project_path = $cpath.'/project';

$handle       = opendir( $project_path );

$project_list = [];
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

if( $project_list )
{
    if( $project )
    {
        if( !in_array( $project , $project_list ) )
        {
            exit;
        }
        // 获取栏目列表
        $arr  = [] ;
        $path       = $project_path.'/'.$project ;
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
            foreach( $arr as $k=>$v )
            {
                $newdata = [];
                $handle  = opendir($path.'/'.$v['dirname']);
                while( $file = readdir( $handle ))
                {
                    if( preg_match('/\.json$/i',$file ) )
                    {
                        $newpath = $path.'/'.$v['dirname'].'/'.$file;
                        $data    = json_decode( trim(file_get_contents( $newpath )) , true );
                        $newFile = rtrim( $file , '.json');
                        $newdata[$newFile] = $data ;
                    }
                }
                closedir($handle);
                if( $newdata )
                {
                    ksort( $newdata );
                    $arr[$k]['data'] =  $newdata ;
                }
            }
        }
######################################################
?>
<!DOCTYPE html>
<HTML lang="zh_CN">
<HEAD>
    <META charset="utf-8">
    <link rel="stylesheet" href="./static/public.css">
    <link rel="stylesheet" href="./static/xcode.css">
    <link rel="stylesheet" href="./static/common.css">
    <script type="text/javascript" src="./static/highlight.js"></script>
    <script>
        hljs.initHighlightingOnLoad();
    </script>
    <title>接口文档</title>
</HEAD>
<BODY>
<div id="manul">
    <div id="manul_bg"></div>
    <div id="manul_box">
        <ul id="t">
            <?php
            if( $arr )
            {
                $s = "";
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
                echo $s ;
            }
            ?>

        </ul>
    </div>
</div>

<?php
    if( $category && $node )
    {
        if( array_key_exists( $category  ,$arr ) ) {
            if (array_key_exists($node, $arr[$category]['data'])) {
                $node_data = $arr[$category]['data'][$node];
                ?>
                <div class="ui text container" style="max-width: none !important; margin-top: 30px; margin-bottom:30px;padding-left: 160px;">
                <div class="ui floating message">
                <h2 class='ui header'>接口：<a href="javascript:;"><?php echo $node_data['name'] ?></a></h2>
                <br/>
                <span class='ui teal tag label'>
             <?php echo $node_data['summary'] ?>
        </span>
                <div class="ui raised segment">
                    <span class="ui red ribbon label">接口说明</span>
                    <div class="ui message">
                        <p> 撰写者:<?php echo $node_data['author'] ?> </p>
                        <p>
                            最后更新时间:<?php echo date('Y-m-d H:i:s', (filectime($path . '/' . $category . '/' . $node . '.json') + (60 * 60 * 8))) ?></p>
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
                            <td><?php echo $node_data['request']['url'] ?></td>
                        </tr>
                        <tr>
                            <td>提交方式</td>
                            <td><?php echo $node_data['request']['method'] ?></td>
                        </tr>
                        <tr>
                            <td>返回格式</td>
                            <td><?php echo $node_data['request']['datatype'] ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
<?php
   if ($node_data['param'])
   {
       ?>
       <h3>接口参数</h3>
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
           <tbody>
           <?php

               foreach ($node_data['param'] as $k => $v) {
                   ?>
                   <tr>
                       <td><?php echo $v['field'] ?></td>
                       <td><?php echo $v['type'] ?></td>
                       <td><?php echo($v['required'] ? '<font color="red">必须</font>' : '否') ?></td>
                       <td><?php echo $v['default'] ?></td>
                       <td><?php echo $v['other'] ?></td>
                       <td><?php echo $v['remark'] ?></td>
                   </tr>
                   <?php
               }

           ?>
           </tbody>
       </table>
<?php
   }
?>

<?php
if ($node_data['callback'])
{
    ?>

<h3>返回字段</h3>
<table class="ui green celled striped table" >
<thead>
<tr>
    <th>返回字段</th>
    <th>类型</th>
    <th>说明</th>
</tr>
</thead>
<tbody>
<?php
                    foreach($node_data['callback'] as $k=>$v )
                    {
                    ?>
                    <tr>
                        <td><?php echo $v['field'] ?></td>
                        <td><?php echo $v['type'] ?></td>
                        <td><?php echo $v['remark'] ?></td>
                    </tr>
                    <?php
                }

?>
            </tbody>
            </table>
<?php
        }
?>

<?php
if( file_exists( $path.'/'.$category.'/'.$node.'.txt')  )
{
?>
    <h3>返回结果(<?php echo  $node_data['request']['datatype'] ?>)</h3>
        <pre style="border-radius: 4px;">
            <code class="<?php echo  $node_data['request']['datatype'] ?>" >
   <?php  echo  file_get_contents( $path.'/'.$category.'/'.$node.'.txt' )   ; ?>
            </code>
        </pre>
<?php
    }
?>

                    </div>

                </div>

<?php
            }
        }
    }
?>
</BODY>
</HTML>



<?php
#######################################################
        exit;
    }

    $e = '<ul>';
    if( $project_list )
    {
        foreach( $project_list as $v )
        {
            $e .= '<li><a href="index.php?project='.$v.'">'.$v.'</a></li>';
        }
    }
    $e .= '</ul>';
    echo $e ;
}



