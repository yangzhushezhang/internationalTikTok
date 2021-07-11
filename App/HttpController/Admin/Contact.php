<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\ConfigModel;
use App\HttpController\Model\ContactModel;
use App\HttpController\Model\Dy_url;
use App\Log\LogHandel;
use Cassandra\Varint;
use EasySwoole\Bridge\Exception;
use EasySwoole\HttpClient\Exception\InvalidUrl;

class Contact extends Base
{
    /**
     * @return bool
     * @throws \Throwable
     * 获取信息
     */
    function get_contact()
    {


        try {
            $limit = $this->request()->getQueryParam('limit');
            $page = $this->request()->getQueryParam('page');
            $model = ContactModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created_at', 'DESC');;
            // 列表数据
            $list = $model->all();
            $result = $model->lastQueryResult();
            $total = $result->getTotalCount();
            $return_data = [
                'code' => 0,
                'msg' => '',
                'count' => $total,
                'data' => $list
            ];
            $this->response()->write(json_encode($return_data, true));
            return true;
        } catch (\Exception $exception) {
            var_dump($exception);
            return false;


        }
    }

    /**
     * @return bool
     * @throws \Throwable
     *
     * 删除
     */
    function contact_del()
    {

        try {

            $id = $this->request()->getQueryParam('id');


            //删除
            $res = ContactModel::create()->destroy(['id' => $id]);

            if (!$res) {

                $this->response()->write(json_encode(['code' => 0, 'msg' => ''], true));
                return false;
            }

            $this->response()->write(json_encode(['code' => 1, 'msg' => ''], true));

        } catch (\Exception $exception) {

            var_dump("异常!");

            return false;

        }


    }

    /**
     * @return bool
     * @throws \Throwable
     *  添加!
     */
    function add_chat()
    {

        try {

            $contact = $this->request()->getQueryParam('contact');


            if ($contact == "") {
                $this->response()->write(json_encode(['code' => 0, 'msg' => 'contact 不可以為空'], true));
                return false;
            }
            //删除
            $res = ContactModel::create()->get(['contact' => $contact]);
            if ($res) {
                $this->response()->write(json_encode(['code' => 0, 'msg' => '不可以重複添加'], true));
                return false;
            }


            $res = ContactModel::create()->data(['contact' => $contact, 'created_at' => time()])->save();


            if (!$res) {

                $this->response()->write(json_encode(['code' => 0, 'msg' => '添加失敗'], true));
                return false;
            }

            $this->response()->write(json_encode(['code' => 1, 'msg' => '添加成功'], true));

        } catch (\Exception $exception) {

            var_dump("异常!");

            return false;

        }


    }

    /**
     * @return bool
     * @throws \Throwable
     */
    function get_one()
    {


        try {
            $data = ContactModel::create()->column('contact');


            $data_array = [];
            foreach ($data as $v) {
                array_push($data_array, $v);
            }


            $re = $data_array[rand(0, count($data_array) - 1)];


            $this->response()->write(json_encode(['code' => 1, 'msg' => $re], true));
            return true;
        } catch (\Exception $exception) {
            var_dump($exception);
            return false;


        }


    }


    /***
     * python 上传抖音链接
     */
    function PyAddDyUrl()
    {

        $id = $this->request()->getRequestParam('id');
        if ($id == "") {
            $this->writeJson(-101, '', "id 不可以为空");
            return false;
        }
        try {
            $one = Dy_url::create()->get(['Dy_id' => $id]);
            if ($one) {
                $this->writeJson(0, '', '不要重复添加!');
                return false;
            }
            $insert_data = [
                'Dy_url' => "py添加!!",
                'Dy_id' => $id,
                'status' => 0,
                'created_at' => time(),
                'updated_at' => time()
            ];
            $res = Dy_url::create()->data($insert_data)->save();
            if (!$res) {
                $this->writeJson(0, '', '添加抖音链接地址失败');
                return false;

            }
            $this->writeJson(1, '', '添加抖音链接成功');
            return true;

        } catch (\Throwable $e) {
            $this->writeJson(-1, '', $e->getMessage());
            return false;
        }

    }


    /**
     * @return bool
     * 添加   国际抖音的链接
     */
    function add_Dy_url()
    {
        $log = new LogHandel();
        try {
            $action = $this->request()->getRequestParam('action');
            if ($action == "file") {
                var_dump("批量上传抖音链接.....进来了!!");
                $this->data['file'] = $this->request()->getUploadedFile('file');
                $file = $this->data['file'];
                $filePath = $file->getTempName();
                $content = file_get_contents($filePath);
                //$content = mb_convert_encoding($content, 'utf-8', 'gbk');
                $data_array = explode(PHP_EOL, $content);
                foreach ($data_array as $value) {
                    $id = $this->get_url_id($value);
                    if (!$id) {
                        continue;
                    }
                    #判断下这个 链接是否已经添加了
                    $one = Dy_url::create()->get(['Dy_id' => $id]);
                    if ($one) {
                        continue;
                    }
                    $insert_data = [
                        'Dy_url' => $value,
                        'Dy_id' => $id,
                        'status' => 0,
                        'created_at' => time(),
                        'updated_at' => time()
                    ];
                    $res = Dy_url::create()->data($insert_data)->save();
                    if (!$res) {
                        continue;
                    }
                }
                $this->writeJson(1, '', '添加抖音链接成功');
                return true;
            }

            $Dy_url = $this->request()->getRequestParam('url');
            if ($Dy_url == "") {
                $this->writeJson(0, '', '非法参数,抖音链接不可以为空');
                return false;
            }
            $id = $this->get_url_id($Dy_url);
            if (!$id) {
                $this->writeJson(0, '', '添加抖音链接地址失败');
                return false;
            }
            #判断下这个 链接是否已经添加了
            $one = Dy_url::create()->get(['Dy_id' => $id]);
            if ($one) {
                $this->writeJson(0, '', '不要重复添加!');
                return false;
            }

            $insert_data = [
                'Dy_url' => $Dy_url,
                'Dy_id' => $id,
                'status' => 0,
                'created_at' => time(),
                'updated_at' => time()
            ];
            $res = Dy_url::create()->data($insert_data)->save();
            if (!$res) {
                $this->writeJson(0, '', '添加抖音链接地址失败');
                return false;

            }
            $this->writeJson(1, '', '添加抖音链接成功');
            return true;
        } catch (\Throwable $e) {
            var_dump($e->getMessage());
            $this->writeJson(-1, '', '添加抖音地址异常:' . $e->getMessage());
            $log->log('添加抖音地址失败,异常:' . $e->getMessage());
            return false;
        }
    }


    /**
     * @param $url
     * @return bool|mixed
     * @throws InvalidUrl
     * 获取 国际版抖音的 id
     */
    function get_url_id($url)
    {

        $log = new LogHandel();
        try {


            if (!$url || $url == "" || empty($url)) {
                return false;
            }

            $url = trim($url);
            #new 一个对象
            $client = new \EasySwoole\HttpClient\HttpClient($url);
            #禁止重定向
            $client->enableFollowLocation(0);
            #正则 id
            $pattern = "/\d{19}/";
            $response = $client->get();
            if (!$response) {
                var_dump($response);
                $log->log('获取抖音的url id 失败 $response 返回为false');
                return false;
            }


            $data = $response->getBody();
            if (!$data) {
                var_dump($data);
                $log->log('获取抖音的url id 失败 $data 返回为false');
                return false;
            }
            preg_match($pattern, $data, $match);

            if (!$match[0]) {
                $log->log('获取抖音的url id 失败');
                return false;
            }
            return $match[0];
        } catch (Exception $exception) {
            $log->log('获取抖音的url id 异常' . $exception);
            var_dump($exception);
            return false;
        }


    }


    /**
     * @return bool
     * @throws \Throwable
     * #  获取抖音的 链接
     */
    function get_list_for_dy_url()
    {
        $log = new LogHandel();
        try {
            $page = $this->request()->getRequestParam('page');         // 当前页码
            $limit = $this->request()->getRequestParam('limit');        // 每页多少条数据
            $status = $this->request()->getQueryParam('status');
            $nickname = $this->request()->getQueryParam("nickname");

            if ($status == 2) {
                $model = Dy_url::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('use_time', 'ASC');
            } else {
                $model = Dy_url::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('updated_at', 'DESC');
            }


            // 列表数据
            if (isset($nickname)) {
                $model = $model->where(['nickname' => $nickname]);
            }
            $list = $model->all(['status' => $status]);
            $result = $model->lastQueryResult();
            // 总条数
            $total = $result->getTotalCount();
            $return_data = [
                "code" => 0,
                "msg" => '',
                'count' => $total,
                'data' => $list
            ];
            $this->response()->write(json_encode($return_data));
            return true;

        } catch (\EasySwoole\ORM\Exception\Exception $e) {
            $log->log('获取抖音链接失败,异常原因:' . $e);
            return false;
        }

    }

    /**
     * @return bool
     * 获取 抖音的单独链接 id
     */
    function get_Dy_link_id()
    {
        $log = new LogHandel();
        try {
            $nickname = $this->request()->getQueryParam('nickname');#获取别称
            if ($nickname == "") {
                $this->writeJson(0, '', 'nickname 不可以为空');
                return false;
            }


            # 判断 这个 nickname 有没有正在被 使用的链接
            # 0 没有使用 1正在使用 2使用过了
            $one = Dy_url::create()->where(['nickname' => $nickname])->where(['status' => 1])->all();
            if ($one) {
                #把 链接全部改成 已经 使用完毕
                foreach ($one as $two) {
                    $update = [
                        'status' => 2,
                        'use_time' => time() - $two['updated_at']
                    ];
                    Dy_url::create()->where(['Dy_id' => $two['Dy_id']])->update($update);
                }
            }

            $res = Dy_url::create()->get(['status' => 0]);
            if (!$res) {
                $this->writeJson(0, '', '链接已经用完了,请添加链接!');
                return false;
            }

            $res = $res->toArray();
            if ($res['Dy_id'] == "") {
                $this->writeJson(0, '', '获取的链接失败');
                return false;
            }

            #获取链接成功 更新数据库
            Dy_url::create()->where(['id' => $res['id']])->update(['updated_at' => time(), 'status' => 1, 'nickname' => $nickname]);


            $this->writeJson(1, '', $res['Dy_id']);
            return true;
        } catch (\Throwable $e) {
            $log->log("异常,获取抖音链接id 异常抛出:" . $e);
            return false;
        }
    }


    // 更新连接的 comments 评论数量
    function set_comments()
    {

        $log = new LogHandel();
        try {

            $comments = $this->request()->getQueryParam('comments');
            $Dy_id = $this->request()->getQueryParam('Dy_id');
            if ($comments == "" || $Dy_id == "") {
                $this->writeJson(0, '', '必要参数 不可以为空');
                return false;
            }

            $res = Dy_url::create()->get(['Dy_id' => $Dy_id]);
            if (!$res) {
                $this->writeJson(0, '', '链接 id 不存在');
                return false;
            }


            $res = Dy_url::create()->where(['Dy_id' => $Dy_id])->update(['comments' => $comments]);
            if (!$res) {
                $this->writeJson(0, '', '评论数量上传失败!');
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $log->log("set_comments 异常抛出:" . $e);
            return false;
        }


    }


}