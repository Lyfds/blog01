<?php
namespace controllers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use models\Blog;

class BlogController
{
    //导出Excel文件
    public function makeExcel() {
        $blog = new Blog;
        $data = $blog->getNew();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //设置第一行内容
        $sheet->setCellValue('A1','标题');
        $sheet->setCellValue('B1','内容');
        $sheet->setCellValue('C1','发表日期');
        $sheet->setCellValue('D1','浏览量');
        $sheet->setCellValue('E1','是否公开');

        //从第二行写数据
        $i = 2;
        foreach($data as $v) {
            $sheet->setCellValue('A'.$i,$v['title']);
            $sheet->setCellValue('B'.$i,$v['content']);
            $sheet->setCellValue('C'.$i,$v['created_at']);
            $sheet->setCellValue('D'.$i,$v['display']);
            $sheet->setCellValue('E'.$i,$v['is_show']==1?'公开':'私有');
            $i++;
        }
        //生成Excel文件
        date_default_timezone_set('PRC');
        $root = ROOT.'public/excel/';
        $twoLevel = date('Ymd');    
        if(!is_dir($root.$twoLevel))
           mkdir($root.$twoLevel);
        $name = date('H'.'时'.'i'.'分'.'s'.'秒');
        $writer = new Xlsx($spreadsheet);
        $writer->save($root.$twoLevel.'/'.$name.'-生成'.'.xlsx');
        header('Location:http://localhost:9999/blog/index');
    

        //下载文件路径
        $file = ROOT.'excel/'.$date.'.xlsx';
        //下载时文件名
        $fileName = '最新的20条日志-'.$date.'.xlsx';
           // 告诉浏览器这是一个二进程文件流    
        Header ( "Content-Type: application/octet-stream" ); 
        // 请求范围的度量单位  
        Header ( "Accept-Ranges: bytes" );  
        // 告诉浏览器文件尺寸    
        Header ( "Accept-Length: " . filesize ( $file ) );  
        // 开始下载，下载时的文件名
        Header ( "Content-Disposition: attachment; filename=" . $fileName );    

        // 读取服务器上的一个文件并以文件流的形式输出给浏览器
        readfile($file);
   
    }
    // 显示私有日志
    public function content()
    {
        // 1. 接收ID，并取出日志信息
        $id = $_GET['id'];
        $model = new Blog;
        $blog = $model->find($id);

        // 2. 判断这个日志是不是我的日志
        if($_SESSION['id'] != $blog['user_id'])
            die('无权访问！');

        // 3. 加载视图
        view('blogs.content', [
            'blog' => $blog,
        ]);

    }

    public function update()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $id = $_POST['id'];
        
        $blog = new Blog;
        $blog->update($title, $content, $is_show, $id);

        // 如果日志是公开的就生成静态页
        if($is_show == 1)
        {
            $blog->makeHtml($id);
        }
        else
        {
            // 如果改为私有，就要将原来的静态页删除掉
            $blog->deleteHtml($id);
        }

        message('修改成功！', 2, '/blog/index');
    }

    public function edit()
    {
        $id = $_GET['id'];
        // 根据ID取出日志的信息

        $blog = new Blog;
        $data = $blog->find( $id );

        view('blogs.edit', [
            'data' => $data,
        ]);

    }

    public function delete()
    {
        $id = $_POST['id'];

        $blog = new Blog;
        $blog->delete($id);

        // 静态页删除掉
        $blog->deleteHtml($id);

        message('删除成功',2,'/blog/index');
        
    }

    // 显示添加日志的表单
    public function create()
    {
        view('blogs.create');
    }

    public function store()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];

        $blog = new Blog;
        // 添加新日志并返回 新日志的ID
        $id = $blog->add($title,$content,$is_show);

        // 如果日志是公开的就生成静态页
        if($is_show == 1)
        {
            $blog->makeHtml($id);
        }

        // 跳转
        message('发表成功', 2, '/blog/index');
    }

    // 日志列表
    public function index()
    {
        $blog = new Blog;
        // 搜索数据
        $data = $blog->search();
        // 加载视图
        view('blogs.index', $data);
    }

    // 为所有的日志生成详情页的静态页
    public function content_to_html()
    {
        $blog = new Blog;
        $blog->content2html();
    }

    public function index2html()
    {
        $blog = new Blog;
        $blog->index2html();
    }

    public function display()
    {
        // 接收日志ID
        $id = (int)$_GET['id'];

        $blog = new Blog;

        // 把浏览量+1，并输出（如果内存中没有就查询数据库，如果内存中有直接操作内存）
        $display =  $blog->getDisplay($id);

        // 返回多个数据时必须要用 JSON

        echo json_encode([
            'display' => $display,
            'email' => isset($_SESSION['email']) ? $_SESSION['email'] : ''
        ]);
        
    }

    public function displayToDb()
    {
        $blog = new Blog;
        $blog->displayToDb();
    }
}
