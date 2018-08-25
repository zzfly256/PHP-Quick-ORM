<?php
namespace Controller;
use System\Collection;
use System\Controller;
use System\Database;
use Model\Demo;

class apiController extends Controller {

    public function testGet($id){
        $data = Demo::find($id);
        return $this->response($data);
    }

    // ORM 查询类方法演示

    public function findGet($id){
        // 通过 id 查询数据表
        $resultObject = Demo::find($id);
        return $this->response($resultObject);
    }

    public function whereGet(){
        // 条件检索数据表(条件数组)
        $conditionArray = ["author" => "Rytia"];
        $resultObjectArray = Demo::where($conditionArray)->get();
        return $this->response($resultObjectArray);
    }

    public function whereRawGet(){
        // 条件检索数据表(SQL语句)
        $conditionStatement = 'author LIKE "Rytia"';
        $resultObjectArray = Demo::whereRaw($conditionStatement)->get();
        return $this->response($resultObjectArray);
    }

    public function allGet(){
        // 显示全部数据
        $resultObjectArray = Demo::all();
        return $this->response($resultObjectArray);
    }

    public function rawGet(){
        // 执行SQL语句
        $sqlStatement = 'SELECT * FROM {table} WHERE author LIKE "Rytia"';
        $resultObjectArray = Demo::raw($sqlStatement)->get();
        dd($resultObjectArray);
        return $this->response($resultObjectArray);
    }

    public function searchGet(){
        // 数据表字段搜索
        $resultObjectArray = Demo::search("title", "%嗯%");
        return $this->response($resultObjectArray);
    }

    public function orWhereGet(){
        // 多重条件检索数据表(条件数组)
        // 支持 where、whereRaw、orWhere、orWhereRaw
        $test = Demo::where(['title' => '还是标题'])->orWhere(['title' => '测试标题'])->get();
        return $this->response($test);
    }

    public function deleteGet($id){
        // 删除条目
        $result = Demo::find($id)->delete();
        return $this->response($result);
    }

    public function paginateGet(){
        // 条目分页演示

        // 直接调用：相当于 Database 层分页，效率高
        $test = Demo::paginate(5);

        // Collection 层分页：先把全部数据取出再通过 Collection 分页，效率低
        $test = Demo::all()->paginate(5);

        // Database 层分页：在 SQL 语句里添加 LIMIT，效率高
        $test = Demo::where()->paginate(5);
        $test = Database::table('demo')->where()->setModel(Demo::class)->paginate(5);
        $test = Database::model(Demo::class)->where()->paginate(5);

        // 直接返回（前端无法判断分页，不推荐）
        // return $this->response($test);

        // 采用分页专用对象返回（带有 page 字段供前端使用，推荐）
        return $this->response()->pageEncode($test);
    }

    public function databaseWhereGet(){
        // 数据库层封装演示1
        $result = Database::table('demo')
            ->setModel(Demo::class)
            ->select('title')
            ->where(["author" => "Rytia"])
            ->orWhereRaw('content LIKE "%测试%"')
            ->paginate(5);

        return $this->response($result);

    }

    public function databaseOnGet(){
        // 数据库层封装演示2
        $result = Database::table('wiki')
            ->select('DISTINCT zhong.name,wiki.coordinate')
            ->join('zhong')
            ->on('wiki.zhong=zhong.id')
            ->orderBy("coordinate", "DESC")
            ->fetchAll();
        return $this->response($result);

    }

    public function responseDemoGet($id){
        // 响应演示

        $data = Demo::find($id);

        // 直接返回 JSON
        $this->response()->json($data, '200');

        // 返回请求标准 JSON 对象
        $this->response()->dataEncode($data, '200', '0', '');

        // 返回请求标准 JSON 对象
        $this->response($data, '200', '0', '');

        // 标准 JSON 对象示例
        //    {
        //      "errcode": "0",
        //      "errmsg": "",
        //      "data": {
        //          "id": "2",
        //          "title": "还是标题",
        //          "content": "这是无敌的测试",
        //          "author": "Rytia",
        //          "created_at": "0000-00-00 00:00:00",
        //          "updated_at": "0000-00-00 00:00:00"
        //      }
        //    }

    }
}
